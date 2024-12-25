<?php

namespace App\Service;

class RepairPartValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['repair_id'])) {
            $errors[] = 'Repair ID is required.';
        }

        if (empty($data['part_id'])) {
            $errors[] = 'Part ID is required.';
        }

        if (isset($data['quantity']) && (!is_numeric($data['quantity']) || $data['quantity'] <= 0)) {
            $errors[] = 'Invalid quantity.';
        }

        if (isset($data['priceAtTime']) && (!is_numeric($data['priceAtTime']) || $data['priceAtTime'] < 0)) {
            $errors[] = 'Invalid price at time.';
        }

        return $errors;
    }
}
