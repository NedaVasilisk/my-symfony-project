<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param array $data
     * @param int $itemsPerPage
     * @param int $page
     * @return array
     */
    public function getAllUsersByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $queryBuilder = $this->createQueryBuilder('u');

        if (isset($data['id'])) {
            $queryBuilder->andWhere('u.id = :id')
                ->setParameter('id', $data['id']);
        }

        if (isset($data['username'])) {
            $queryBuilder->andWhere('u.username LIKE :username')
                ->setParameter('username', '%' . $data['username'] . '%');
        }

        if (isset($data['firstName'])) {
            $queryBuilder->andWhere('u.firstName LIKE :firstName')
                ->setParameter('firstName', '%' . $data['firstName'] . '%');
        }

        if (isset($data['lastName'])) {
            $queryBuilder->andWhere('u.lastName LIKE :lastName')
                ->setParameter('lastName', '%' . $data['lastName'] . '%');
        }

        if (isset($data['email'])) {
            $queryBuilder->andWhere('u.email LIKE :email')
                ->setParameter('email', '%' . $data['email'] . '%');
        }

        if (isset($data['role'])) {
            $queryBuilder->andWhere('u.role = :role')
                ->setParameter('role', $data['role']);
        }

        if (isset($data['isActive'])) {
            $queryBuilder->andWhere('u.isActive = :isActive')
                ->setParameter('isActive', filter_var($data['isActive'], FILTER_VALIDATE_BOOLEAN));
        }

        if (isset($data['createdAt'])) {
            if (is_array($data['createdAt'])) {
                if (isset($data['createdAt']['from'])) {
                    $queryBuilder->andWhere('u.createdAt >= :createdAtFrom')
                        ->setParameter('createdAtFrom', new \DateTime($data['createdAt']['from']));
                }
                if (isset($data['createdAt']['to'])) {
                    $queryBuilder->andWhere('u.createdAt <= :createdAtTo')
                        ->setParameter('createdAtTo', new \DateTime($data['createdAt']['to']));
                }
            } else {
                $queryBuilder->andWhere('u.createdAt = :createdAt')
                    ->setParameter('createdAt', new \DateTime($data['createdAt']));
            }
        }

        if (isset($data['sort'])) {
            $sortParams = explode(',', $data['sort']);
            if (count($sortParams) === 2) {
                [$sortField, $sortOrder] = $sortParams;
                $allowedSortFields = ['id', 'username', 'firstName', 'lastName', 'email', 'createdAt'];
                $allowedSortOrder = ['asc', 'desc'];

                if (in_array($sortField, $allowedSortFields) && in_array(strtolower($sortOrder), $allowedSortOrder)) {
                    $queryBuilder->orderBy('u.' . $sortField, strtoupper($sortOrder));
                }
            }
        } else {
            $queryBuilder->orderBy('u.id', 'ASC');
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
