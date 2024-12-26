<?php

namespace App\Repository;

use App\Entity\Invoice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Invoice>
 *
 * @method Invoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Invoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Invoice[]    findAll()
 * @method Invoice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invoice::class);
    }

    public function getAllInvoicesByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $queryBuilder = $this->createQueryBuilder('i')
            ->leftJoin('i.repair', 'r')
            ->addSelect('r');

        if (isset($data['id'])) {
            $queryBuilder->andWhere('i.id = :id')
                ->setParameter('id', $data['id']);
        }

        if (isset($data['repair'])) {
            $queryBuilder->andWhere('r.id = :repair')
                ->setParameter('repair', $data['repair']);
        }

        if (isset($data['dateIssued'])) {
            if (is_array($data['dateIssued'])) {
                if (isset($data['dateIssued']['from'])) {
                    $queryBuilder->andWhere('i.dateIssued >= :dateFrom')
                        ->setParameter('dateFrom', new \DateTime($data['dateIssued']['from']));
                }
                if (isset($data['dateIssued']['to'])) {
                    $queryBuilder->andWhere('i.dateIssued <= :dateTo')
                        ->setParameter('dateTo', new \DateTime($data['dateIssued']['to']));
                }
            } else {
                $queryBuilder->andWhere('i.dateIssued = :dateIssued')
                    ->setParameter('dateIssued', new \DateTime($data['dateIssued']));
            }
        }

        if (isset($data['totalAmount'])) {
            if (is_array($data['totalAmount'])) {
                if (isset($data['totalAmount']['min'])) {
                    $queryBuilder->andWhere('i.totalAmount >= :amountMin')
                        ->setParameter('amountMin', $data['totalAmount']['min']);
                }
                if (isset($data['totalAmount']['max'])) {
                    $queryBuilder->andWhere('i.totalAmount <= :amountMax')
                        ->setParameter('amountMax', $data['totalAmount']['max']);
                }
            } else {
                $queryBuilder->andWhere('i.totalAmount = :totalAmount')
                    ->setParameter('totalAmount', $data['totalAmount']);
            }
        }

        if (isset($data['isPaid'])) {
            $queryBuilder->andWhere('i.isPaid = :isPaid')
                ->setParameter('isPaid', filter_var($data['isPaid'], FILTER_VALIDATE_BOOLEAN));
        }

        if (isset($data['sort'])) {
            $sortParams = explode(',', $data['sort']);
            if (count($sortParams) === 2) {
                [$sortField, $sortOrder] = $sortParams;
                $allowedSortFields = ['id', 'dateIssued', 'totalAmount', 'isPaid'];
                $allowedSortOrder = ['asc', 'desc'];

                if (in_array($sortField, $allowedSortFields) && in_array(strtolower($sortOrder), $allowedSortOrder)) {
                    $queryBuilder->orderBy('i.' . $sortField, strtoupper($sortOrder));
                }
            }
        } else {
            $queryBuilder->orderBy('i.id', 'ASC');
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
