<?php

namespace App\Service;

use DateTime;

class VehicleValidator
{
    public function validate(array $data, bool $isCreate = true): array
    {
        $errors = [];

        if ($isCreate) {
            if (empty($data['customer_id']) || !is_numeric($data['customer_id'])) {
                $errors[] = 'Invalid or missing customer_id';
            }
        }

        if (isset($data['year'])) {
            if (!is_numeric($data['year']) || $data['year'] < 1886 || $data['year'] > (int)date('Y')) {
                $errors[] = 'Invalid year';
            }
        }

        if (isset($data['batteryCapacity']) && !is_numeric($data['batteryCapacity'])) {
            $errors[] = 'Invalid batteryCapacity';
        }

        if (isset($data['lastIotUpdate'])) {
            try {
                new DateTime($data['lastIotUpdate']);
            } catch (\Exception $e) {
                $errors[] = 'Invalid date format for lastIotUpdate';
            }
        }

        return $errors;
    }
}
