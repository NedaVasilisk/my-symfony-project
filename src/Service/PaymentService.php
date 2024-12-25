<?php

namespace App\Service;

use App\Entity\Payment;
use App\Entity\Invoice;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class PaymentService
{
    public function __construct(private EntityManagerInterface $entityManager, private RequestCheckerService $requestCheckerService) {}

    public function createPayment(array $data): Payment
    {
        $this->requestCheckerService->check($data, ['invoice_id', 'paymentDate', 'amount', 'paymentMethod']);
        $invoice = $this->findInvoice($data['invoice_id']);
        $payment = $this->fillPaymentData(new Payment(), $data, $invoice);
        $this->entityManager->persist($payment);
        $this->entityManager->flush();
        return $payment;
    }

    public function updatePayment(Payment $payment, array $data): Payment
    {
        $invoice = isset($data['invoice_id']) ? $this->findInvoice($data['invoice_id']) : $payment->getInvoice();
        $this->fillPaymentData($payment, $data, $invoice);
        $this->entityManager->flush();
        return $payment;
    }

    public function deletePayment(Payment $payment): void
    {
        $this->entityManager->remove($payment);
        $this->entityManager->flush();
    }

    private function fillPaymentData(Payment $payment, array $data, Invoice $invoice): Payment
    {
        $payment->setInvoice($invoice);
        $payment->setPaymentDate(new \DateTime($data['paymentDate']));
        $payment->setAmount($data['amount']);
        $payment->setPaymentMethod($data['paymentMethod']);
        return $payment;
    }

    private function findInvoice(int $invoiceId): Invoice
    {
        $invoice = $this->entityManager->getRepository(Invoice::class)->find($invoiceId);
        if (!$invoice) {
            throw new BadRequestException('Invoice not found');
        }
        return $invoice;
    }
}
