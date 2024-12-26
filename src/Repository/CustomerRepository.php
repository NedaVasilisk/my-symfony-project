<?php

namespace App\Repository;

use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Customer>
 *
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    public function getAllCustomersByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->leftJoin('c.user', 'u')
            ->addSelect('u');

        if (isset($data['id'])) {
            $queryBuilder->andWhere('c.id = :id')
                ->setParameter('id', $data['id']);
        }

        if (isset($data['user'])) {
            $queryBuilder->andWhere('u.id = :user')
                ->setParameter('user', $data['user']);
        }

        if (isset($data['firstName'])) {
            $queryBuilder->andWhere('c.firstName LIKE :firstName')
                ->setParameter('firstName', '%' . $data['firstName'] . '%');
        }

        if (isset($data['lastName'])) {
            $queryBuilder->andWhere('c.lastName LIKE :lastName')
                ->setParameter('lastName', '%' . $data['lastName'] . '%');
        }

        if (isset($data['phone'])) {
            $queryBuilder->andWhere('c.phone LIKE :phone')
                ->setParameter('phone', '%' . $data['phone'] . '%');
        }

        if (isset($data['email'])) {
            $queryBuilder->andWhere('c.email LIKE :email')
                ->setParameter('email', '%' . $data['email'] . '%');
        }

        if (isset($data['address'])) {
            $queryBuilder->andWhere('c.address LIKE :address')
                ->setParameter('address', '%' . $data['address'] . '%');
        }

        if (isset($data['sort'])) {
            $sortParams = explode(',', $data['sort']);
            if (count($sortParams) === 2) {
                [$sortField, $sortOrder] = $sortParams;
                $allowedSortFields = ['id', 'firstName', 'lastName', 'position'];
                $allowedSortOrder = ['asc', 'desc'];

                if (in_array($sortField, $allowedSortFields) && in_array(strtolower($sortOrder), $allowedSortOrder)) {
                    $queryBuilder->orderBy('c.' . $sortField, strtoupper($sortOrder));
                }
            }
        } else {
            $queryBuilder->orderBy('c.id', 'ASC');
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
