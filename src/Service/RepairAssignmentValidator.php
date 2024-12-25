<?php

namespace App\Service;

class RepairAssignmentValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['repairId'])) {
            $errors[] = 'Repair ID is required.';
        }

        if (empty($data['employeeId'])) {
            $errors[] = 'Employee ID is required.';
        }

        return $errors;
    }
}
