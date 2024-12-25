<?php

namespace App\Service;

class PriceHistoryPartValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['part_id'])) {
            $errors[] = 'Part ID is required.';
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
