<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class KeycloakUserProvider implements UserProviderInterface
{
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        return new KeycloakUser(
            $identifier,
            $identifier,
            sprintf('%s@local.invalid', $identifier),
        );
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof KeycloakUser) {
            throw new UnsupportedUserException(sprintf('Unsupported user class "%s".', $user::class));
        }

        return new KeycloakUser(
            $user->getUserIdentifier(),
            $user->getDisplayName(),
            $user->getEmail(),
            $user->getRoles(),
        );
    }

    public function supportsClass(string $class): bool
    {
        return KeycloakUser::class === $class || is_subclass_of($class, KeycloakUser::class);
    }
}
