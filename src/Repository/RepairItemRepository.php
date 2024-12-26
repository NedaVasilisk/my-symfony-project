<?php

namespace App\Repository;

use App\Entity\RepairItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<RepairItem>
 *
 * @method RepairItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method RepairItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method RepairItem[]    findAll()
 * @method RepairItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RepairItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RepairItem::class);
    }

    public function getAllRepairItemsByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $queryBuilder = $this->createQueryBuilder('ri')
            ->leftJoin('ri.repair', 'r')
            ->addSelect('r')
            ->leftJoin('ri.service', 's')
            ->addSelect('s');

        if (isset($data['id'])) {
            $queryBuilder->andWhere('ri.id = :id')
                ->setParameter('id', $data['id']);
        }

        if (isset($data['repair'])) {
            $queryBuilder->andWhere('r.id = :repair')
                ->setParameter('repair', $data['repair']);
        }

        if (isset($data['service'])) {
            $queryBuilder->andWhere('s.id = :service')
                ->setParameter('service', $data['service']);
        }

        if (isset($data['quantity'])) {
            if (is_array($data['quantity'])) {
                if (isset($data['quantity']['min'])) {
                    $queryBuilder->andWhere('ri.quantity >= :qtyMin')
                        ->setParameter('qtyMin', $data['quantity']['min']);
                }
                if (isset($data['quantity']['max'])) {
                    $queryBuilder->andWhere('ri.quantity <= :qtyMax')
                        ->setParameter('qtyMax', $data['quantity']['max']);
                }
            } else {
                $queryBuilder->andWhere('ri.quantity = :quantity')
                    ->setParameter('quantity', $data['quantity']);
            }
        }

        if (isset($data['priceAtTime'])) {
            if (is_array($data['priceAtTime'])) {
                if (isset($data['priceAtTime']['min'])) {
                    $queryBuilder->andWhere('ri.priceAtTime >= :priceMin')
                        ->setParameter('priceMin', $data['priceAtTime']['min']);
                }
                if (isset($data['priceAtTime']['max'])) {
                    $queryBuilder->andWhere('ri.priceAtTime <= :priceMax')
                        ->setParameter('priceMax', $data['priceAtTime']['max']);
                }
            } else {
                $queryBuilder->andWhere('ri.priceAtTime = :priceAtTime')
                    ->setParameter('priceAtTime', $data['priceAtTime']);
            }
        }

        if (isset($data['sort'])) {
            $sortParams = explode(',', $data['sort']);
            if (count($sortParams) === 2) {
                [$sortField, $sortOrder] = $sortParams;
                $allowedSortFields = ['id', 'quantity', 'priceAtTime'];
                $allowedSortOrder = ['asc', 'desc'];

                if (in_array($sortField, $allowedSortFields) && in_array(strtolower($sortOrder), $allowedSortOrder)) {
                    $queryBuilder->orderBy('ri.' . $sortField, strtoupper($sortOrder));
                }
            }
        } else {
            $queryBuilder->orderBy('ri.id', 'ASC');
        }

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
