<?php

namespace App\Repository;

use App\Entity\PriceHistoryService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<PriceHistoryService>
 *
 * @method PriceHistoryService|null find($id, $lockMode = null, $lockVersion = null)
 * @method PriceHistoryService|null findOneBy(array $criteria, array $orderBy = null)
 * @method PriceHistoryService[]    findAll()
 * @method PriceHistoryService[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PriceHistoryServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PriceHistoryService::class);
    }

    public function getAllPriceHistoryServicesByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $queryBuilder = $this->createQueryBuilder('phs')
            ->leftJoin('phs.service', 's')
            ->addSelect('s');

        if (isset($data['id'])) {
            $queryBuilder->andWhere('phs.id = :id')
                ->setParameter('id', $data['id']);
        }

        if (isset($data['service'])) {
            $queryBuilder->andWhere('s.id = :service')
                ->setParameter('service', $data['service']);
        }

        if (isset($data['effectiveDate'])) {
            if (is_array($data['effectiveDate'])) {
                if (isset($data['effectiveDate']['from'])) {
                    $queryBuilder->andWhere('phs.effectiveDate >= :dateFrom')
                        ->setParameter('dateFrom', new \DateTime($data['effectiveDate']['from']));
                }
                if (isset($data['effectiveDate']['to'])) {
                    $queryBuilder->andWhere('phs.effectiveDate <= :dateTo')
                        ->setParameter('dateTo', new \DateTime($data['effectiveDate']['to']));
                }
            } else {
                $queryBuilder->andWhere('phs.effectiveDate = :effectiveDate')
                    ->setParameter('effectiveDate', new \DateTime($data['effectiveDate']));
            }
        }

        if (isset($data['price'])) {
            if (is_array($data['price'])) {
                if (isset($data['price']['min'])) {
                    $queryBuilder->andWhere('phs.price >= :priceMin')
                        ->setParameter('priceMin', $data['price']['min']);
                }
                if (isset($data['price']['max'])) {
                    $queryBuilder->andWhere('phs.price <= :priceMax')
                        ->setParameter('priceMax', $data['price']['max']);
                }
            } else {
                $queryBuilder->andWhere('phs.price = :price')
                    ->setParameter('price', $data['price']);
            }
        }

        if (isset($data['sort'])) {
            $sortParams = explode(',', $data['sort']);
            if (count($sortParams) === 2) {
                [$sortField, $sortOrder] = $sortParams;
                $allowedSortFields = ['id', 'effectiveDate', 'price'];
                $allowedSortOrder = ['asc', 'desc'];

                if (in_array($sortField, $allowedSortFields) && in_array(strtolower($sortOrder), $allowedSortOrder)) {
                    $queryBuilder->orderBy('phs.' . $sortField, strtoupper($sortOrder));
                }
            }
        } else {
            $queryBuilder->orderBy('phs.id', 'ASC');
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
