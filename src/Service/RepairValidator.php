<?php

namespace App\Service;

class RepairValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['vehicle_id'])) {
            $errors[] = 'Vehicle ID is required.';
        }

        if (isset($data['totalCost']) && (!is_numeric($data['totalCost']) || $data['totalCost'] < 0)) {
            $errors[] = 'Invalid total cost.';
        }

        if (isset($data['dateOut']) && strtotime($data['dateOut']) < strtotime($data['dateIn'] ?? 'now')) {
            $errors[] = 'DateOut must be later than DateIn.';
        }

        return $errors;
    }
}
