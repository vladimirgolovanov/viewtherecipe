<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\OAuth2Client;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

class ClientRepository implements ClientRepositoryInterface
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function getClientEntity(string $clientIdentifier): ?ClientEntityInterface
    {
        return $this->em->getRepository(OAuth2Client::class)->find($clientIdentifier);
    }

    public function validateClient(string $clientIdentifier, ?string $clientSecret, ?string $grantType): bool
    {
        $client = $this->em->getRepository(OAuth2Client::class)->find($clientIdentifier);

        if (!$client instanceof OAuth2Client) {
            return false;
        }

        if ('client_credentials' !== $grantType) {
            return false;
        }

        return null !== $clientSecret && $client->validateSecret($clientSecret);
    }
}
