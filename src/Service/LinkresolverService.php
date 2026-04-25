<?php

namespace App\Service;

use App\Security\KeycloakUser;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Ermittelt die passende Link-Liste für einen Benutzer anhand seiner E-Mail-Adresse.
 * Die erste Gruppe, deren email_pattern (PHP-Regex) passt, wird zurückgegeben.
 */
final class LinkResolverService
{
    /**
     * @param list<array{email_pattern: string, links: list<array<string, string>>}> $linkGroups
     */
    public function __construct(
        private readonly array $linkGroups,
    ) {
    }

    /**
     * @return list<array<string, string>>
     */
    public function resolveForUser(?UserInterface $user): array
    {
        if (!$user instanceof KeycloakUser) {
            return [];
        }

        $email = $user->getEmail();

        foreach ($this->linkGroups as $group) {
            $pattern = $group['email_pattern'] ?? '';
            if ('' === $pattern) {
                continue;
            }

            if (@preg_match($pattern, $email)) {
                return $group['links'] ?? [];
            }
        }

        return [];
    }
}