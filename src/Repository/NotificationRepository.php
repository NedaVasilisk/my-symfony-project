<?php

namespace App\Repository;

use App\Entity\Notification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Notification>
 *
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    public function getAllNotificationsByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $queryBuilder = $this->createQueryBuilder('n')
            ->leftJoin('n.user', 'u')
            ->addSelect('u');

        if (isset($data['id'])) {
            $queryBuilder->andWhere('n.id = :id')
                ->setParameter('id', $data['id']);
        }

        if (isset($data['user'])) {
            $queryBuilder->andWhere('u.id = :user')
                ->setParameter('user', $data['user']);
        }

        if (isset($data['message'])) {
            $queryBuilder->andWhere('n.message LIKE :message')
                ->setParameter('message', '%' . $data['message'] . '%');
        }

        if (isset($data['sentAt'])) {
            if (is_array($data['sentAt'])) {
                if (isset($data['sentAt']['from'])) {
                    $queryBuilder->andWhere('n.sentAt >= :sentAtFrom')
                        ->setParameter('sentAtFrom', new \DateTime($data['sentAt']['from']));
                }
                if (isset($data['sentAt']['to'])) {
                    $queryBuilder->andWhere('n.sentAt <= :sentAtTo')
                        ->setParameter('sentAtTo', new \DateTime($data['sentAt']['to']));
                }
            } else {
                $queryBuilder->andWhere('n.sentAt = :sentAt')
                    ->setParameter('sentAt', new \DateTime($data['sentAt']));
            }
        }

        if (isset($data['isRead'])) {
            $queryBuilder->andWhere('n.isRead = :isRead')
                ->setParameter('isRead', filter_var($data['isRead'], FILTER_VALIDATE_BOOLEAN));
        }

        if (isset($data['sort'])) {
            $sortParams = explode(',', $data['sort']);
            if (count($sortParams) === 2) {
                [$sortField, $sortOrder] = $sortParams;
                $allowedSortFields = ['id', 'sentAt', 'isRead'];
                $allowedSortOrder = ['asc', 'desc'];

                if (in_array($sortField, $allowedSortFields) && in_array(strtolower($sortOrder), $allowedSortOrder)) {
                    $queryBuilder->orderBy('n.' . $sortField, strtoupper($sortOrder));
                }
            }
        } else {
            $queryBuilder->orderBy('n.id', 'ASC');
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
