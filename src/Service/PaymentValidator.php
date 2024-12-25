<?php

namespace App\Service;

class PaymentValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['invoiceId'])) {
            $errors[] = 'Invoice ID is required.';
        }

        if (isset($data['amount']) && (!is_numeric($data['amount']) || $data['amount'] < 0)) {
            $errors[] = 'Invalid payment amount.';
        }

        return $errors;
    }
}
