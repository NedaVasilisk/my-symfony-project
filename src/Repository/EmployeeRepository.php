<?php

namespace App\Repository;

use App\Entity\Employee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Employee>
 *
 * @method Employee|null find($id, $lockMode = null, $lockVersion = null)
 * @method Employee|null findOneBy(array $criteria, array $orderBy = null)
 * @method Employee[]    findAll()
 * @method Employee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmployeeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employee::class);
    }

    public function getAllEmployeesByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $queryBuilder = $this->createQueryBuilder('e')
            ->leftJoin('e.user', 'u')
            ->addSelect('u');

        if (isset($data['id'])) {
            $queryBuilder->andWhere('e.id = :id')
                ->setParameter('id', $data['id']);
        }

        if (isset($data['user'])) {
            $queryBuilder->andWhere('u.id = :user')
                ->setParameter('user', $data['user']);
        }

        if (isset($data['firstName'])) {
            $queryBuilder->andWhere('e.firstName LIKE :firstName')
                ->setParameter('firstName', '%' . $data['firstName'] . '%');
        }

        if (isset($data['lastName'])) {
            $queryBuilder->andWhere('e.lastName LIKE :lastName')
                ->setParameter('lastName', '%' . $data['lastName'] . '%');
        }

        if (isset($data['position'])) {
            $queryBuilder->andWhere('e.position LIKE :position')
                ->setParameter('position', '%' . $data['position'] . '%');
        }

        if (isset($data['phone'])) {
            $queryBuilder->andWhere('e.phone LIKE :phone')
                ->setParameter('phone', '%' . $data['phone'] . '%');
        }

        if (isset($data['email'])) {
            $queryBuilder->andWhere('e.email LIKE :email')
                ->setParameter('email', '%' . $data['email'] . '%');
        }

        if (isset($data['specialization'])) {
            $queryBuilder->andWhere('e.specialization LIKE :specialization')
                ->setParameter('specialization', '%' . $data['specialization'] . '%');
        }

        if (isset($data['sort'])) {
            $sortParams = explode(',', $data['sort']);
            if (count($sortParams) === 2) {
                [$sortField, $sortOrder] = $sortParams;
                $allowedSortFields = ['id', 'firstName', 'lastName', 'position'];
                $allowedSortOrder = ['asc', 'desc'];

                if (in_array($sortField, $allowedSortFields) && in_array(strtolower($sortOrder), $allowedSortOrder)) {
                    $queryBuilder->orderBy('e.' . $sortField, strtoupper($sortOrder));
                }
            }
        } else {
            $queryBuilder->orderBy('e.id', 'ASC');
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
