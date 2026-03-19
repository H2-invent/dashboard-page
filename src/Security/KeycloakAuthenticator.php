<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class KeycloakAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
{
    private const SESSION_STATE_KEY = 'keycloak_oauth_state';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly string $serverUrl,
        private readonly string $publicUrl,
        private readonly string $realm,
        private readonly string $clientId,
        private readonly string $clientSecret,
        private readonly string $redirectUri,
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return 'app_auth_callback' === $request->attributes->get('_route');
    }

    public function authenticate(Request $request): SelfValidatingPassport
    {
        $session = $request->getSession();
        $state = (string) $request->query->get('state');
        $code = (string) $request->query->get('code');

        if ('' === $code || !$session instanceof SessionInterface) {
            throw new CustomUserMessageAuthenticationException('Der Autorisierungscode fehlt.');
        }

        if ($state === '' || $state !== $session->get(self::SESSION_STATE_KEY)) {
            throw new CustomUserMessageAuthenticationException('Der OAuth-State ist ungültig.');
        }

        $session->remove(self::SESSION_STATE_KEY);
        $tokenPayload = $this->exchangeCodeForTokenPayload($code);
        $profile = $this->fetchUserProfile($tokenPayload);

        $identifier = $profile['preferred_username'] ?? $profile['email'] ?? $profile['sub'] ?? null;
        if (!\is_string($identifier) || '' === $identifier) {
            throw new CustomUserMessageAuthenticationException('Keycloak hat keinen Benutzer-Identifier geliefert.');
        }

        $displayName = $profile['name'] ?? $profile['given_name'] ?? $identifier;
        $email = $profile['email'] ?? sprintf('%s@local.invalid', $identifier);
        $roles = ['ROLE_USER'];

        if (isset($profile['realm_access']['roles']) && \is_array($profile['realm_access']['roles'])) {
            foreach ($profile['realm_access']['roles'] as $role) {
                if (\is_string($role) && '' !== $role) {
                    $roles[] = 'ROLE_'.strtoupper(str_replace(['-', ' '], '_', $role));
                }
            }
        }

        return new SelfValidatingPassport(new UserBadge($identifier, static fn () => new KeycloakUser(
            $identifier,
            (string) $displayName,
            (string) $email,
            $roles,
        )));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse($this->urlGenerator->generate('app_landing'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new Response(sprintf('Keycloak-Authentifizierung fehlgeschlagen: %s', $exception->getMessage()), Response::HTTP_UNAUTHORIZED);
    }

    public function start(Request $request, ?AuthenticationException $authException = null): Response
    {
        return new RedirectResponse($this->buildAuthorizationUrl($request->getSession()));
    }

    public function buildAuthorizationUrl(SessionInterface $session): string
    {
        $state = bin2hex(random_bytes(16));
        $session->set(self::SESSION_STATE_KEY, $state);

        $query = http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'scope' => 'openid profile email',
            'state' => $state,
        ]);

        return sprintf('%s/realms/%s/protocol/openid-connect/auth?%s', rtrim($this->publicUrl, '/'), $this->realm, $query);
    }

    /**
     * @return array<string, mixed>
     */
    private function exchangeCodeForTokenPayload(string $code): array
    {
        try {
            $response = $this->httpClient->request('POST', sprintf('%s/realms/%s/protocol/openid-connect/token', rtrim($this->serverUrl, '/'), $this->realm), [
                'body' => [
                    'grant_type' => 'authorization_code',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'code' => $code,
                    'redirect_uri' => $this->redirectUri,
                ],
            ]);
        } catch (TransportExceptionInterface $exception) {
            throw new CustomUserMessageAuthenticationException('Keycloak ist nicht erreichbar.', [], 0, $exception);
        }

        $payload = $this->decodeJsonResponse($response, 'Die Token-Antwort von Keycloak ist leer oder ungültig.');
        $accessToken = $payload['access_token'] ?? null;

        if (!\is_string($accessToken) || '' === $accessToken) {
            throw new CustomUserMessageAuthenticationException('Kein Access Token von Keycloak erhalten.');
        }

        return $payload;
    }

    /**
     * @param array<string, mixed> $tokenPayload
     *
     * @return array<string, mixed>
     */
    private function fetchUserProfile(array $tokenPayload): array
    {
        $fallbackProfile = $this->extractProfileFromTokenPayload($tokenPayload);
        $accessToken = (string) $tokenPayload['access_token'];

        try {
            $response = $this->httpClient->request('GET', sprintf('%s/realms/%s/protocol/openid-connect/userinfo', rtrim($this->serverUrl, '/'), $this->realm), [
                'headers' => [
                    'Authorization' => sprintf('Bearer %s', $accessToken),
                    'Accept' => 'application/json',
                ],
            ]);
        } catch (TransportExceptionInterface $exception) {
            if ([] !== $fallbackProfile) {
                return $fallbackProfile;
            }

            throw new CustomUserMessageAuthenticationException('Das Keycloak-Benutzerprofil konnte nicht geladen werden.', [], 0, $exception);
        }

        $content = trim($response->getContent(false));
        $statusCode = $response->getStatusCode();

        if ('' === $content) {
            if ([] !== $fallbackProfile) {
                return $fallbackProfile;
            }

            throw new CustomUserMessageAuthenticationException('Keycloak hat ein leeres Benutzerprofil zurückgegeben.');
        }

        $payload = json_decode($content, true);
        if (!\is_array($payload)) {
            if ([] !== $fallbackProfile) {
                return $fallbackProfile;
            }

            throw new CustomUserMessageAuthenticationException('Keycloak hat kein gültiges JSON-Benutzerprofil geliefert.');
        }

        if ($statusCode >= 400 && [] === $fallbackProfile) {
            $message = $payload['error_description'] ?? $payload['error'] ?? 'Keycloak hat das Benutzerprofil abgelehnt.';
            throw new CustomUserMessageAuthenticationException((string) $message);
        }

        return array_replace($fallbackProfile, $payload);
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeJsonResponse(ResponseInterface $response, string $errorMessage): array
    {
        $content = trim($response->getContent(false));

        if ('' === $content) {
            throw new CustomUserMessageAuthenticationException($errorMessage);
        }

        $payload = json_decode($content, true);
        if (!\is_array($payload)) {
            throw new CustomUserMessageAuthenticationException($errorMessage);
        }

        if ($response->getStatusCode() >= 400) {
            $message = $payload['error_description'] ?? $payload['error'] ?? $errorMessage;
            throw new CustomUserMessageAuthenticationException((string) $message);
        }

        return $payload;
    }

    /**
     * @param array<string, mixed> $tokenPayload
     *
     * @return array<string, mixed>
     */
    private function extractProfileFromTokenPayload(array $tokenPayload): array
    {
        $profile = [];

        foreach (['id_token', 'access_token'] as $tokenKey) {
            $token = $tokenPayload[$tokenKey] ?? null;
            if (!\is_string($token) || '' === $token) {
                continue;
            }

            $claims = $this->decodeJwtPayload($token);
            if ([] !== $claims) {
                $profile = array_replace($profile, $claims);
            }
        }

        return $profile;
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeJwtPayload(string $jwt): array
    {
        $parts = explode('.', $jwt);
        if (3 !== count($parts)) {
            return [];
        }

        $payload = $parts[1];
        $remainder = strlen($payload) % 4;
        if (0 !== $remainder) {
            $payload .= str_repeat('=', 4 - $remainder);
        }

        $decoded = base64_decode(strtr($payload, '-_', '+/'), true);
        if (false === $decoded || '' === $decoded) {
            return [];
        }

        $claims = json_decode($decoded, true);

        return \is_array($claims) ? $claims : [];
    }
}
