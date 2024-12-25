<?php

namespace App\Service;

class PartValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['partNumber'])) {
            $errors[] = 'Part number is required.';
        }

        if (isset($data['currentPrice']) && (!is_numeric($data['currentPrice']) || $data['currentPrice'] < 0)) {
            $errors[] = 'Invalid current price.';
        }

        if (isset($data['quantityInStock']) && (!is_numeric($data['quantityInStock']) || $data['quantityInStock'] < 0)) {
            $errors[] = 'Invalid quantity in stock.';
        }

        return $errors;
    }
}
