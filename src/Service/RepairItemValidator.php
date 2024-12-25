<?php

namespace App\Service;

class RepairItemValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['repair_id'])) {
            $errors[] = 'Repair ID is required.';
        }

        if (empty($data['service_id'])) {
            $errors[] = 'Service ID is required.';
        }

        if (!isset($data['quantity']) || !is_numeric($data['quantity']) || $data['quantity'] <= 0) {
            $errors[] = 'Quantity must be a positive number.';
        }

        if (!isset($data['price_at_time']) || !is_numeric($data['price_at_time']) || $data['price_at_time'] < 0) {
            $errors[] = 'Price at time must be a non-negative number.';
        }

        return $errors;
    }
}
