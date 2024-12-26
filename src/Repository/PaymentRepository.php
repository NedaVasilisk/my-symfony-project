<?php

namespace App\Repository;

use App\Entity\Payment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Payment>
 *
 * @method Payment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Payment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Payment[]    findAll()
 * @method Payment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    public function getAllPaymentsByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->leftJoin('p.invoice', 'i')
            ->addSelect('i');

        if (isset($data['id'])) {
            $queryBuilder->andWhere('p.id = :id')
                ->setParameter('id', $data['id']);
        }

        if (isset($data['invoice'])) {
            $queryBuilder->andWhere('i.id = :invoice')
                ->setParameter('invoice', $data['invoice']);
        }

        if (isset($data['paymentDate'])) {
            if (is_array($data['paymentDate'])) {
                if (isset($data['paymentDate']['from'])) {
                    $queryBuilder->andWhere('p.paymentDate >= :dateFrom')
                        ->setParameter('dateFrom', new \DateTime($data['paymentDate']['from']));
                }
                if (isset($data['paymentDate']['to'])) {
                    $queryBuilder->andWhere('p.paymentDate <= :dateTo')
                        ->setParameter('dateTo', new \DateTime($data['paymentDate']['to']));
                }
            } else {
                $queryBuilder->andWhere('p.paymentDate = :paymentDate')
                    ->setParameter('paymentDate', new \DateTime($data['paymentDate']));
            }
        }

        if (isset($data['amount'])) {
            if (is_array($data['amount'])) {
                if (isset($data['amount']['min'])) {
                    $queryBuilder->andWhere('p.amount >= :amountMin')
                        ->setParameter('amountMin', $data['amount']['min']);
                }
                if (isset($data['amount']['max'])) {
                    $queryBuilder->andWhere('p.amount <= :amountMax')
                        ->setParameter('amountMax', $data['amount']['max']);
                }
            } else {
                $queryBuilder->andWhere('p.amount = :amount')
                    ->setParameter('amount', $data['amount']);
            }
        }

        if (isset($data['paymentMethod'])) {
            $queryBuilder->andWhere('p.paymentMethod = :paymentMethod')
                ->setParameter('paymentMethod', $data['paymentMethod']);
        }

        if (isset($data['sort'])) {
            $sortParams = explode(',', $data['sort']);
            if (count($sortParams) === 2) {
                [$sortField, $sortOrder] = $sortParams;
                $allowedSortFields = ['id', 'paymentDate', 'amount', 'paymentMethod'];
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
