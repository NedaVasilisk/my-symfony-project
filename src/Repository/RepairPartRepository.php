<?php

namespace App\Repository;

use App\Entity\RepairPart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

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

    public function getAllRepairPartsByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $queryBuilder = $this->createQueryBuilder('rp');

        if (isset($data['repair_id'])) {
            $queryBuilder->andWhere('rp.repair = :repairId')
                ->setParameter('repairId', $data['repair_id']);
        }

        if (isset($data['quantity'])) {
            if (is_array($data['quantity'])) {
                if (isset($data['quantity']['min'])) {
                    $queryBuilder->andWhere('rp.quantity >= :qtyMin')
                        ->setParameter('qtyMin', $data['quantity']['min']);
                }
                if (isset($data['quantity']['max'])) {
                    $queryBuilder->andWhere('rp.quantity <= :qtyMax')
                        ->setParameter('qtyMax', $data['quantity']['max']);
                }
            } else {
                $queryBuilder->andWhere('rp.quantity = :quantity')
                    ->setParameter('quantity', $data['quantity']);
            }
        }

        if (isset($data['priceAtTime'])) {
            if (is_array($data['priceAtTime'])) {
                if (isset($data['priceAtTime']['min'])) {
                    $queryBuilder->andWhere('rp.priceAtTime >= :priceMin')
                        ->setParameter('priceMin', $data['priceAtTime']['min']);
                }
                if (isset($data['priceAtTime']['max'])) {
                    $queryBuilder->andWhere('rp.priceAtTime <= :priceMax')
                        ->setParameter('priceMax', $data['priceAtTime']['max']);
                }
            } else {
                $queryBuilder->andWhere('rp.priceAtTime = :priceAtTime')
                    ->setParameter('priceAtTime', $data['priceAtTime']);
            }
        }

        $queryBuilder->orderBy('rp.id', 'ASC');

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
