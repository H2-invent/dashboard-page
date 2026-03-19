<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

final class KeycloakUser implements UserInterface
{
    /**
     * @param list<string> $roles
     */
    public function __construct(
        private readonly string $identifier,
        private readonly string $displayName,
        private readonly string $email,
        private readonly array $roles = ['ROLE_USER'],
    ) {
    }

    public function getUserIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return list<string>
     */
    public function getRoles(): array
    {
        return array_values(array_unique([...$this->roles, 'ROLE_USER']));
    }

    public function eraseCredentials(): void
    {
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
