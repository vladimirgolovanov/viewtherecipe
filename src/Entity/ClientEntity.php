<?php

declare(strict_types=1);

namespace App\Entity;

use League\OAuth2\Server\Entities\ClientEntityInterface;

class ClientEntity implements ClientEntityInterface
{
    public function __construct(
        private readonly string $identifier,
    ) {
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getName(): string
    {
        return $this->identifier;
    }

    public function getRedirectUri(): array
    {
        return [];
    }

    public function isConfidential(): bool
    {
        return true;
    }
}
