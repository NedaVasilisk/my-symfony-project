<?php

namespace App\Service;

class PriceHistoryServiceValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['service_id'])) {
            $errors[] = 'Service ID is required.';
        }

        if (isset($data['price']) && (!is_numeric($data['price']) || $data['price'] < 0)) {
            $errors[] = 'Invalid price.';
        }

        if (isset($data['effectiveDate'])) {
            try {
                new \DateTime($data['effectiveDate']);
            } catch (\Exception $e) {
                $errors[] = 'Invalid effectiveDate format.';
            }
        }

        return $errors;
    }
}
