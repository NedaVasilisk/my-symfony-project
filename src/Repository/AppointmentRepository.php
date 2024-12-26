<?php

namespace App\Repository;

use App\Entity\Appointment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Appointment>
 *
 * @method Appointment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Appointment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Appointment[]    findAll()
 * @method Appointment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppointmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Appointment::class);
    }

    public function getAllAppointmentsByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->leftJoin('a.customer', 'c')
            ->addSelect('c')
            ->leftJoin('a.vehicle', 'v')
            ->addSelect('v');

        if (isset($data['id'])) {
            $queryBuilder->andWhere('a.id = :id')
                ->setParameter('id', $data['id']);
        }

        if (isset($data['customer'])) {
            $queryBuilder->andWhere('c.id = :customer')
                ->setParameter('customer', $data['customer']);
        }

        if (isset($data['vehicle'])) {
            $queryBuilder->andWhere('v.id = :vehicle')
                ->setParameter('vehicle', $data['vehicle']);
        }

        if (isset($data['scheduledDate'])) {
            if (is_array($data['scheduledDate'])) {
                if (isset($data['scheduledDate']['from'])) {
                    $queryBuilder->andWhere('a.scheduledDate >= :dateFrom')
                        ->setParameter('dateFrom', new \DateTime($data['scheduledDate']['from']));
                }
                if (isset($data['scheduledDate']['to'])) {
                    $queryBuilder->andWhere('a.scheduledDate <= :dateTo')
                        ->setParameter('dateTo', new \DateTime($data['scheduledDate']['to']));
                }
            } else {
                $queryBuilder->andWhere('a.scheduledDate = :scheduledDate')
                    ->setParameter('scheduledDate', new \DateTime($data['scheduledDate']));
            }
        }

        if (isset($data['status'])) {
            $queryBuilder->andWhere('a.status = :status')
                ->setParameter('status', $data['status']);
        }

        if (isset($data['sort'])) {
            $sortParams = explode(',', $data['sort']);
            if (count($sortParams) === 2) {
                [$sortField, $sortOrder] = $sortParams;
                $allowedSortFields = ['id', 'scheduledDate', 'status'];
                $allowedSortOrder = ['asc', 'desc'];

                if (in_array($sortField, $allowedSortFields) && in_array(strtolower($sortOrder), $allowedSortOrder)) {
                    $queryBuilder->orderBy('a.' . $sortField, strtoupper($sortOrder));
                }
            }
        } else {
            $queryBuilder->orderBy('a.id', 'ASC');
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
