<?php

namespace App\Repository;

use App\Entity\Service;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Service>
 *
 * @method Service|null find($id, $lockMode = null, $lockVersion = null)
 * @method Service|null findOneBy(array $criteria, array $orderBy = null)
 * @method Service[]    findAll()
 * @method Service[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Service::class);
    }

    /**
     * @param array $data Фільтри
     * @param int $itemsPerPage Кількість елементів на сторінку
     * @param int $page Номер сторінки
     * @return array
     */
    public function getAllServicesByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $queryBuilder = $this->createQueryBuilder('s');

        if (isset($data['id'])) {
            $queryBuilder->andWhere('s.id = :id')
                ->setParameter('id', $data['id']);
        }

        if (isset($data['name'])) {
            $queryBuilder->andWhere('s.name LIKE :name')
                ->setParameter('name', '%' . $data['name'] . '%');
        }

        if (isset($data['description'])) {
            $queryBuilder->andWhere('s.description LIKE :description')
                ->setParameter('description', '%' . $data['description'] . '%');
        }

        if (isset($data['currentPrice'])) {
            if (is_array($data['currentPrice'])) {
                // Діапазон цін
                if (isset($data['currentPrice']['min'])) {
                    $queryBuilder->andWhere('s.currentPrice >= :priceMin')
                        ->setParameter('priceMin', $data['currentPrice']['min']);
                }
                if (isset($data['currentPrice']['max'])) {
                    $queryBuilder->andWhere('s.currentPrice <= :priceMax')
                        ->setParameter('priceMax', $data['currentPrice']['max']);
                }
            } else {
                $queryBuilder->andWhere('s.currentPrice = :currentPrice')
                    ->setParameter('currentPrice', $data['currentPrice']);
            }
        }

        if (isset($data['sort'])) {
            $sortParams = explode(',', $data['sort']);
            if (count($sortParams) === 2) {
                [$sortField, $sortOrder] = $sortParams;
                $allowedSortFields = ['id', 'name', 'currentPrice'];
                $allowedSortOrder = ['asc', 'desc'];

                if (in_array($sortField, $allowedSortFields) && in_array(strtolower($sortOrder), $allowedSortOrder)) {
                    $queryBuilder->orderBy('s.' . $sortField, strtoupper($sortOrder));
                }
            }
        } else {
            $queryBuilder->orderBy('s.id', 'ASC');
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
