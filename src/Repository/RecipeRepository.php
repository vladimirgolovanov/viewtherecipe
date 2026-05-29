<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function findAllForOwner(int $ownerId): array
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.owner = :owner')
            ->setParameter('owner', $ownerId);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param int[] $excludeIds
     */
    public function findRandomForOwner(int $ownerId, array $excludeIds = []): ?Recipe
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.owner = :owner')
            ->setParameter('owner', $ownerId);

        if ($excludeIds) {
            $qb->andWhere('r.id NOT IN (:excludeIds)')
               ->setParameter('excludeIds', $excludeIds);
        }

        $total = (int) (clone $qb)
            ->select('COUNT(r.id)')
            ->getQuery()
            ->getSingleScalarResult();

        if ($total === 0) {
            return null;
        }

        return $qb
            ->setFirstResult(random_int(0, $total - 1))
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    //    /**
    //     * @return Recipe[] Returns an array of Recipe objects
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

    //    public function findOneBySomeField($value): ?Recipe
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
