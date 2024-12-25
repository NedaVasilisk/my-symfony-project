<?php

namespace App\Service;

class InvoiceValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['repairId'])) {
            $errors[] = 'Repair ID is required.';
        }
        if (isset($data['totalAmount']) && (!is_numeric($data['totalAmount']) || $data['totalAmount'] < 0)) {
            $errors[] = 'Total amount must be a positive number.';
        }
        if (isset($data['isPaid']) && !is_bool($data['isPaid'])) {
            $errors[] = 'isPaid must be a boolean value.';
        }

        return $errors;
    }
}
