<?php

namespace App\Service;

use App\Entity\Invoice;
use App\Entity\Repair;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class InvoiceService
{
    public function __construct(private EntityManagerInterface $entityManager, private RequestCheckerService $requestCheckerService) {}

    public function createInvoice(array $data): Invoice
    {
        $this->requestCheckerService->check($data, ['repair_id', 'dateIssued', 'totalAmount']);
        $repair = $this->findRepair($data['repair_id']);
        $invoice = $this->fillInvoiceData(new Invoice(), $data, $repair);
        $this->entityManager->persist($invoice);
        $this->entityManager->flush();
        return $invoice;
    }

    public function updateInvoice(Invoice $invoice, array $data): Invoice
    {
        $repair = isset($data['repair_id']) ? $this->findRepair($data['repair_id']) : $invoice->getRepair();
        $this->fillInvoiceData($invoice, $data, $repair);
        $this->entityManager->flush();
        return $invoice;
    }

    public function deleteInvoice(Invoice $invoice): void
    {
        $this->entityManager->remove($invoice);
        $this->entityManager->flush();
    }

    private function fillInvoiceData(Invoice $invoice, array $data, Repair $repair): Invoice
    {
        $invoice->setRepair($repair);
        $invoice->setDateIssued(new \DateTime($data['dateIssued']));
        $invoice->setTotalAmount($data['totalAmount']);
        $invoice->setIsPaid($data['isPaid'] ?? $invoice->isPaid());
        return $invoice;
    }

    private function findRepair(int $repairId): Repair
    {
        $repair = $this->entityManager->getRepository(Repair::class)->find($repairId);
        if (!$repair) {
            throw new BadRequestException('Repair not found');
        }
        return $repair;
    }
}
