<?php

namespace App\Repository;

use App\Entity\AuthCode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;

class AuthCodeRepository extends ServiceEntityRepository implements AuthCodeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuthCode::class);
    }

    public function getNewAuthCode(): AuthCodeEntityInterface
    {
        $authCode = new AuthCode();
        $authCode->setRevoked(false);

        return $authCode;
    }

    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity): void
    {
        $this->getEntityManager()->persist($authCodeEntity);
        $this->getEntityManager()->flush();
    }

    public function revokeAuthCode(string $codeId): void
    {
        $authCode = $this->findOneBy(['identifier' => $codeId]);
        if ($authCode) {
            $authCode->setRevoked(true);
            $this->getEntityManager()->flush();
        }
    }

    public function isAuthCodeRevoked(string $codeId): bool
    {
        $authCode = $this->findOneBy(['identifier' => $codeId]);
        return $authCode === null || $authCode->isRevoked();
    }
}
