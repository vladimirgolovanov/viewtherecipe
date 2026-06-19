<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ClientEntity;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

class ClientRepository implements ClientRepositoryInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    public function getClientEntity(string $clientIdentifier): ?ClientEntityInterface
    {
        $user = $this->userRepository->loadUserByIdentifier($clientIdentifier);

        return $user !== null ? new ClientEntity($clientIdentifier) : null;
    }

    public function validateClient(string $clientIdentifier, ?string $clientSecret, ?string $grantType): bool
    {
        return $this->userRepository->loadUserByIdentifier($clientIdentifier) !== null;
    }
}
