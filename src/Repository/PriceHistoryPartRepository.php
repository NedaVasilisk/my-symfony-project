<?php

namespace App\Repository;

use App\Entity\PriceHistoryPart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

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

    public function getAllPriceHistoryPartsByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $queryBuilder = $this->createQueryBuilder('php')
            ->leftJoin('php.part', 'p')
            ->addSelect('p');

        if (isset($data['id'])) {
            $queryBuilder->andWhere('php.id = :id')
                ->setParameter('id', $data['id']);
        }

        if (isset($data['part'])) {
            $queryBuilder->andWhere('p.id = :part')
                ->setParameter('part', $data['part']);
        }

        if (isset($data['effectiveDate'])) {
            if (is_array($data['effectiveDate'])) {
                if (isset($data['effectiveDate']['from'])) {
                    $queryBuilder->andWhere('php.effectiveDate >= :dateFrom')
                        ->setParameter('dateFrom', new \DateTime($data['effectiveDate']['from']));
                }
                if (isset($data['effectiveDate']['to'])) {
                    $queryBuilder->andWhere('php.effectiveDate <= :dateTo')
                        ->setParameter('dateTo', new \DateTime($data['effectiveDate']['to']));
                }
            } else {
                $queryBuilder->andWhere('php.effectiveDate = :effectiveDate')
                    ->setParameter('effectiveDate', new \DateTime($data['effectiveDate']));
            }
        }

        if (isset($data['price'])) {
            if (is_array($data['price'])) {
                if (isset($data['price']['min'])) {
                    $queryBuilder->andWhere('php.price >= :priceMin')
                        ->setParameter('priceMin', $data['price']['min']);
                }
                if (isset($data['price']['max'])) {
                    $queryBuilder->andWhere('php.price <= :priceMax')
                        ->setParameter('priceMax', $data['price']['max']);
                }
            } else {
                $queryBuilder->andWhere('php.price = :price')
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
                    $queryBuilder->orderBy('php.' . $sortField, strtoupper($sortOrder));
                }
            }
        } else {
            $queryBuilder->orderBy('php.id', 'ASC');
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
