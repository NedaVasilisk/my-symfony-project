<?php

namespace App\Repository;

use App\Entity\RepairAssignment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<RepairAssignment>
 *
 * @method RepairAssignment|null find($id, $lockMode = null, $lockVersion = null)
 * @method RepairAssignment|null findOneBy(array $criteria, array $orderBy = null)
 * @method RepairAssignment[]    findAll()
 * @method RepairAssignment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RepairAssignmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RepairAssignment::class);
    }

    public function getAllRepairAssignmentsByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $queryBuilder = $this->createQueryBuilder('ra')
            ->leftJoin('ra.repair', 'r')
            ->addSelect('r')
            ->leftJoin('ra.employee', 'e')
            ->addSelect('e');

        if (isset($data['repair_id'])) {
            $queryBuilder->andWhere('r.id = :repairId')
                ->setParameter('repairId', $data['repair_id']);
        }

        if (isset($data['employee'])) {
            $queryBuilder->andWhere('e.id = :employee')
                ->setParameter('employee', $data['employee']);
        }

        $queryBuilder->orderBy('ra.id', 'ASC');

        $paginator = new Paginator($queryBuilder);
        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $itemsPerPage);

        $queryBuilder->setFirstResult($itemsPerPage * ($page - 1))
            ->setMaxResults($itemsPerPage);

        return [
            'data' => $paginator->getQuery()->getResult(),
            'pagination' => [
                'currentPage' => $page,
                'itemsPerPage' => $itemsPerPage,
                'totalPages' => $pagesCount,
                'totalItems' => $totalItems
            ]
        ];
    }
}
