<?php

namespace App\Service;

class AppointmentValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['customerId'])) {
            $errors[] = 'Customer ID is required.';
        }
        if (empty($data['vehicleId'])) {
            $errors[] = 'Vehicle ID is required.';
        }
        if (isset($data['scheduledDate']) && !strtotime($data['scheduledDate'])) {
            $errors[] = 'Invalid scheduled date format.';
        }

        return $errors;
    }
}
