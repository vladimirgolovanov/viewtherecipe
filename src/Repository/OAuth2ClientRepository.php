<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\OAuth2Client;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

/**
 * @extends ServiceEntityRepository<OAuth2Client>
 */
class OAuth2ClientRepository extends ServiceEntityRepository implements ClientRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OAuth2Client::class);
    }

    public function getClientEntity(string $clientIdentifier): ?OAuth2Client
    {
        return $this->findOneBy(['identifier' => $clientIdentifier]);
    }

    public function validateClient(string $clientIdentifier, ?string $clientSecret, ?string $grantType): bool
    {
        $client = $this->findOneBy(['identifier' => $clientIdentifier]);

        if (null === $client) {
            return false;
        }

        if ($client->isConfidential() && !$client->validateSecret((string) $clientSecret)) {
            return false;
        }

        return true;
    }

    public function findByUser(User $user): ?OAuth2Client
    {
        return $this->findOneBy(['user' => $user]);
    }
}
