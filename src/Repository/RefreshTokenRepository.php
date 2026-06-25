<?php

namespace App\Repository;

use App\Entity\RefreshToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;

/**
 * @extends ServiceEntityRepository<RefreshToken>
 */
class RefreshTokenRepository extends ServiceEntityRepository implements RefreshTokenRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RefreshToken::class);
    }

    public function getNewRefreshToken(): ?RefreshTokenEntityInterface
    {
        $refreshToken = new RefreshToken();
        $refreshToken->setRevoked(false);

        return $refreshToken;
    }

    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity): void
    {
        $this->getEntityManager()->persist($refreshTokenEntity);
        $this->getEntityManager()->flush();
    }

    public function revokeRefreshToken(string $tokenId): void
    {
        $token = $this->findOneBy(['identifier' => $tokenId]);
        if ($token) {
            $token->setRevoked(true);
            $this->getEntityManager()->flush();
        }
    }

    public function isRefreshTokenRevoked(string $tokenId): bool
    {
        $token = $this->findOneBy(['identifier' => $tokenId]);

        return $token === null || $token->isRevoked();
    }

    //    /**
    //     * @return RefreshToken[] Returns an array of RefreshToken objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?RefreshToken
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
