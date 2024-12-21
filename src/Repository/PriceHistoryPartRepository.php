<?php

namespace App\Repository;

use App\Entity\PriceHistoryPart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PriceHistoryPart>
 *
 * @method PriceHistoryPart|null find($id, $lockMode = null, $lockVersion = null)
 * @method PriceHistoryPart|null findOneBy(array $criteria, array $orderBy = null)
 * @method PriceHistoryPart[]    findAll()
 * @method PriceHistoryPart[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PriceHistoryPartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PriceHistoryPart::class);
    }

//    /**
//     * @return PriceHistoryPart[] Returns an array of PriceHistoryPart objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PriceHistoryPart
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
