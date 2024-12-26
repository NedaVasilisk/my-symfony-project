<?php

namespace App\Repository;

use App\Entity\Repair;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Repair>
 *
 * @method Repair|null find($id, $lockMode = null, $lockVersion = null)
 * @method Repair|null findOneBy(array $criteria, array $orderBy = null)
 * @method Repair[]    findAll()
 * @method Repair[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RepairRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Repair::class);
    }

    public function getAllRepairsByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $queryBuilder = $this->createQueryBuilder('r')
            ->leftJoin('r.vehicle', 'v')
            ->addSelect('v');

        if (isset($data['id'])) {
            $queryBuilder->andWhere('r.id = :id')
                ->setParameter('id', $data['id']);
        }

        if (isset($data['vehicle'])) {
            $queryBuilder->andWhere('v.id = :vehicle')
                ->setParameter('vehicle', $data['vehicle']);
        }

        if (isset($data['status'])) {
            $queryBuilder->andWhere('r.status = :status')
                ->setParameter('status', $data['status']);
        }

        if (isset($data['dateIn'])) {
            if (is_array($data['dateIn'])) {
                if (isset($data['dateIn']['from'])) {
                    $queryBuilder->andWhere('r.dateIn >= :dateInFrom')
                        ->setParameter('dateInFrom', new \DateTime($data['dateIn']['from']));
                }
                if (isset($data['dateIn']['to'])) {
                    $queryBuilder->andWhere('r.dateIn <= :dateInTo')
                        ->setParameter('dateInTo', new \DateTime($data['dateIn']['to']));
                }
            } else {
                $queryBuilder->andWhere('r.dateIn = :dateIn')
                    ->setParameter('dateIn', new \DateTime($data['dateIn']));
            }
        }

        if (isset($data['dateOut'])) {
            if (is_array($data['dateOut'])) {
                if (isset($data['dateOut']['from'])) {
                    $queryBuilder->andWhere('r.dateOut >= :dateOutFrom')
                        ->setParameter('dateOutFrom', new \DateTime($data['dateOut']['from']));
                }
                if (isset($data['dateOut']['to'])) {
                    $queryBuilder->andWhere('r.dateOut <= :dateOutTo')
                        ->setParameter('dateOutTo', new \DateTime($data['dateOut']['to']));
                }
            } else {
                $queryBuilder->andWhere('r.dateOut = :dateOut')
                    ->setParameter('dateOut', new \DateTime($data['dateOut']));
            }
        }

        if (isset($data['totalCost'])) {
            if (is_array($data['totalCost'])) {
                if (isset($data['totalCost']['min'])) {
                    $queryBuilder->andWhere('r.totalCost >= :costMin')
                        ->setParameter('costMin', $data['totalCost']['min']);
                }
                if (isset($data['totalCost']['max'])) {
                    $queryBuilder->andWhere('r.totalCost <= :costMax')
                        ->setParameter('costMax', $data['totalCost']['max']);
                }
            } else {
                $queryBuilder->andWhere('r.totalCost = :totalCost')
                    ->setParameter('totalCost', $data['totalCost']);
            }
        }

        if (isset($data['sort'])) {
            $sortParams = explode(',', $data['sort']);
            if (count($sortParams) === 2) {
                [$sortField, $sortOrder] = $sortParams;
                $allowedSortFields = ['id', 'dateIn', 'dateOut', 'status', 'totalCost'];
                $allowedSortOrder = ['asc', 'desc'];

                if (in_array($sortField, $allowedSortFields) && in_array(strtolower($sortOrder), $allowedSortOrder)) {
                    $queryBuilder->orderBy('r.' . $sortField, strtoupper($sortOrder));
                }
            }
        } else {
            $queryBuilder->orderBy('r.id', 'ASC');
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
