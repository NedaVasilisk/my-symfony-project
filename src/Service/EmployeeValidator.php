<?php

namespace App\Service;

class EmployeeValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['userId'])) {
            $errors[] = 'User ID is required.';
        }
        if (empty($data['firstName'])) {
            $errors[] = 'First name is required.';
        }
        if (empty($data['lastName'])) {
            $errors[] = 'Last name is required.';
        }
        if (isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format.';
        }

        return $errors;
    }
}
