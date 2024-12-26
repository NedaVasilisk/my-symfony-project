<?php

namespace App\Repository;

use App\Entity\Vehicle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Vehicle>
 *
 * @method Vehicle|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vehicle|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vehicle[]    findAll()
 * @method Vehicle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VehicleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vehicle::class);
    }

    /**
     * Повертає список Vehicle з фільтрацією та пагінацією.
     *
     * @param array $filters Масив фільтрів (наприклад, make, model, year).
     * @param int $itemsPerPage Кількість елементів на сторінку.
     * @param int $page Номер сторінки.
     * @return array Дані для пагінації та список об'єктів.
     */
    public function getAllVehiclesByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $queryBuilder = $this->createQueryBuilder('v');

        if (isset($data['id'])) {
            $queryBuilder->andWhere('v.id = :id')
                ->setParameter('id', $data['id']);
        }

        if (isset($data['customer'])) {
            $queryBuilder->andWhere('v.customer = :customer')
                ->setParameter('customer', $data['customer']);
        }

        if (isset($data['vin'])) {
            $queryBuilder->andWhere('v.vin LIKE :vin')
                ->setParameter('vin', '%' . $data['vin'] . '%');
        }

        if (isset($data['licensePlate'])) {
            $queryBuilder->andWhere('v.licensePlate LIKE :licensePlate')
                ->setParameter('licensePlate', '%' . $data['licensePlate'] . '%');
        }

        if (isset($data['make'])) {
            $queryBuilder->andWhere('v.make LIKE :make')
                ->setParameter('make', '%' . $data['make'] . '%');
        }

        if (isset($data['model'])) {
            $queryBuilder->andWhere('v.model LIKE :model')
                ->setParameter('model', '%' . $data['model'] . '%');
        }

        if (isset($data['year'])) {
            if (is_array($data['year'])) {
                // Діапазон років
                if (isset($data['year']['min'])) {
                    $queryBuilder->andWhere('v.year >= :yearMin')
                        ->setParameter('yearMin', $data['year']['min']);
                }
                if (isset($data['year']['max'])) {
                    $queryBuilder->andWhere('v.year <= :yearMax')
                        ->setParameter('yearMax', $data['year']['max']);
                }
            } else {
                // Точний збіг
                $queryBuilder->andWhere('v.year = :year')
                    ->setParameter('year', $data['year']);
            }
        }

        if (isset($data['engineType'])) {
            $queryBuilder->andWhere('v.engineType = :engineType')
                ->setParameter('engineType', $data['engineType']);
        }

        if (isset($data['batteryCapacity'])) {
            if (is_array($data['batteryCapacity'])) {
                if (isset($data['batteryCapacity']['min'])) {
                    $queryBuilder->andWhere('v.batteryCapacity >= :batteryMin')
                        ->setParameter('batteryMin', $data['batteryCapacity']['min']);
                }
                if (isset($data['batteryCapacity']['max'])) {
                    $queryBuilder->andWhere('v.batteryCapacity <= :batteryMax')
                        ->setParameter('batteryMax', $data['batteryCapacity']['max']);
                }
            } else {
                // Точний збіг
                $queryBuilder->andWhere('v.batteryCapacity = :batteryCapacity')
                    ->setParameter('batteryCapacity', $data['batteryCapacity']);
            }
        }

        if (isset($data['lastIotUpdate'])) {
            if (is_array($data['lastIotUpdate'])) {
                if (isset($data['lastIotUpdate']['from'])) {
                    $queryBuilder->andWhere('v.lastIotUpdate >= :lastIotUpdateFrom')
                        ->setParameter('lastIotUpdateFrom', new \DateTime($data['lastIotUpdate']['from']));
                }
                if (isset($data['lastIotUpdate']['to'])) {
                    $queryBuilder->andWhere('v.lastIotUpdate <= :lastIotUpdateTo')
                        ->setParameter('lastIotUpdateTo', new \DateTime($data['lastIotUpdate']['to']));
                }
            } else {
                // Точний збіг або лише дата "from"
                $queryBuilder->andWhere('v.lastIotUpdate = :lastIotUpdate')
                    ->setParameter('lastIotUpdate', new \DateTime($data['lastIotUpdate']));
            }
        }

        if (isset($data['sort'])) {
            $sortParams = explode(',', $data['sort']);
            if (count($sortParams) === 2) {
                [$sortField, $sortOrder] = $sortParams;
                $allowedSortFields = ['id', 'make', 'model', 'year', 'engineType', 'batteryCapacity', 'lastIotUpdate'];
                $allowedSortOrder = ['asc', 'desc'];

                if (in_array($sortField, $allowedSortFields) && in_array(strtolower($sortOrder), $allowedSortOrder)) {
                    $queryBuilder->orderBy('v.' . $sortField, strtoupper($sortOrder));
                }
            }
        } else {
            $queryBuilder->orderBy('v.id', 'ASC');
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
