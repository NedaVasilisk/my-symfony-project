<?php

namespace App\Repository;

use App\Entity\RepairPart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RepairPart>
 *
 * @method RepairPart|null find($id, $lockMode = null, $lockVersion = null)
 * @method RepairPart|null findOneBy(array $criteria, array $orderBy = null)
 * @method RepairPart[]    findAll()
 * @method RepairPart[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RepairPartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RepairPart::class);
    }

//    /**
//     * @return RepairPart[] Returns an array of RepairPart objects
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

//    public function findOneBySomeField($value): ?RepairPart
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
