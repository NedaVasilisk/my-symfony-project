<?php

namespace App\Service;

use App\Entity\Payment;
use App\Entity\Invoice;

class PaymentService
{
    public function createOrUpdatePayment(
        array    $data,
        Invoice  $invoice,
        ?Payment $payment = null
    ): Payment
    {
        if (!$payment) {
            $payment = new Payment();
        }

        $payment->setInvoice($invoice);
        $payment->setAmount($data['amount'] ?? $payment->getAmount());
        $payment->setPaymentDate(
            isset($data['paymentDate']) ? new \DateTime($data['paymentDate']) : $payment->getPaymentDate()
        );
        $payment->setPaymentMethod($data['paymentMethod'] ?? $payment->getPaymentMethod());

        return $payment;
    }
}
