<?php

namespace App\Controller;

use App\Security\KeycloakAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AuthController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['GET'])]
    public function login(Request $request, KeycloakAuthenticator $authenticator): Response
    {
        return $authenticator->start($request);
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(
        Request $request,
        TokenStorageInterface $tokenStorage,
        #[Autowire('%env(KEYCLOAK_PUBLIC_URL)%')] string $keycloakPublicUrl,
        #[Autowire('%env(KEYCLOAK_REALM)%')] string $realm,
        #[Autowire('%env(KEYCLOAK_CLIENT_ID)%')] string $clientId,
        #[Autowire('%env(APP_URL)%')] string $appUrl,
    ): Response {
        $idToken = $request->hasSession() ? (string) $request->getSession()->get(KeycloakAuthenticator::SESSION_ID_TOKEN_KEY, '') : '';

        if ($request->hasSession()) {
            $request->getSession()->invalidate();
        }

        $tokenStorage->setToken(null);

        $query = [
            'client_id' => $clientId,
            'post_logout_redirect_uri' => rtrim($appUrl, '/').'/login',
        ];

        if ('' !== $idToken) {
            $query['id_token_hint'] = $idToken;
        }

        return new RedirectResponse(sprintf(
            '%s/realms/%s/protocol/openid-connect/logout?%s',
            rtrim($keycloakPublicUrl, '/'),
            $realm,
            http_build_query($query),
        ));
    }
}
