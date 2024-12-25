<?php

namespace App\Service;

use App\Entity\Invoice;
use App\Entity\Repair;

class InvoiceService
{
    public function createOrUpdateInvoice(array $data, Repair $repair, ?Invoice $invoice = null): Invoice
    {
        if (!$invoice) {
            $invoice = new Invoice();
        }

        $invoice->setRepair($repair);
        $invoice->setDateIssued(isset($data['dateIssued']) ? new \DateTime($data['dateIssued']) : new \DateTime());
        $invoice->setTotalAmount($data['totalAmount'] ?? 0.0);
        $invoice->setIsPaid($data['isPaid'] ?? false);

        return $invoice;
    }
}
