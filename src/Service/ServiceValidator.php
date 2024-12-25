<?php

namespace App\Service;

class ServiceValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['name'])) {
            $errors[] = 'Service name is required';
        }

        if (isset($data['currentPrice']) && (!is_numeric($data['currentPrice']) || $data['currentPrice'] < 0)) {
            $errors[] = 'Invalid currentPrice';
        }

        return $errors;
    }
}
