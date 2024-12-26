<?php

namespace App\Repository;

use App\Entity\Part;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Part>
 *
 * @method Part|null find($id, $lockMode = null, $lockVersion = null)
 * @method Part|null findOneBy(array $criteria, array $orderBy = null)
 * @method Part[]    findAll()
 * @method Part[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Part::class);
    }

    public function getAllPartsByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $queryBuilder = $this->createQueryBuilder('p');

        if (isset($data['id'])) {
            $queryBuilder->andWhere('p.id = :id')
                ->setParameter('id', $data['id']);
        }

        if (isset($data['name'])) {
            $queryBuilder->andWhere('p.name LIKE :name')
                ->setParameter('name', '%' . $data['name'] . '%');
        }

        if (isset($data['manufacturer'])) {
            $queryBuilder->andWhere('p.manufacturer LIKE :manufacturer')
                ->setParameter('manufacturer', '%' . $data['manufacturer'] . '%');
        }

        if (isset($data['partNumber'])) {
            $queryBuilder->andWhere('p.partNumber LIKE :partNumber')
                ->setParameter('partNumber', '%' . $data['partNumber'] . '%');
        }

        if (isset($data['currentPrice'])) {
            if (is_array($data['currentPrice'])) {
                if (isset($data['currentPrice']['min'])) {
                    $queryBuilder->andWhere('p.currentPrice >= :priceMin')
                        ->setParameter('priceMin', $data['currentPrice']['min']);
                }
                if (isset($data['currentPrice']['max'])) {
                    $queryBuilder->andWhere('p.currentPrice <= :priceMax')
                        ->setParameter('priceMax', $data['currentPrice']['max']);
                }
            } else {
                $queryBuilder->andWhere('p.currentPrice = :currentPrice')
                    ->setParameter('currentPrice', $data['currentPrice']);
            }
        }

        if (isset($data['quantityInStock'])) {
            if (is_array($data['quantityInStock'])) {
                if (isset($data['quantityInStock']['min'])) {
                    $queryBuilder->andWhere('p.quantityInStock >= :qtyMin')
                        ->setParameter('qtyMin', $data['quantityInStock']['min']);
                }
                if (isset($data['quantityInStock']['max'])) {
                    $queryBuilder->andWhere('p.quantityInStock <= :qtyMax')
                        ->setParameter('qtyMax', $data['quantityInStock']['max']);
                }
            } else {
                $queryBuilder->andWhere('p.quantityInStock = :quantityInStock')
                    ->setParameter('quantityInStock', $data['quantityInStock']);
            }
        }

        if (isset($data['sort'])) {
            $sortParams = explode(',', $data['sort']);
            if (count($sortParams) === 2) {
                [$sortField, $sortOrder] = $sortParams;
                $allowedSortFields = ['id', 'name', 'partNumber', 'currentPrice', 'quantityInStock'];
                $allowedSortOrder = ['asc', 'desc'];

                if (in_array($sortField, $allowedSortFields) && in_array(strtolower($sortOrder), $allowedSortOrder)) {
                    $queryBuilder->orderBy('p.' . $sortField, strtoupper($sortOrder));
                }
            }
        } else {
            $queryBuilder->orderBy('p.id', 'ASC');
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
