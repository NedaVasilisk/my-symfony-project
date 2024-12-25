<?php

namespace App\Service;

class UserValidator
{
    public function validate(array $data, bool $isCreate = true): array
    {
        $errors = [];

        if ($isCreate && empty($data['username'])) {
            $errors[] = 'Username is required';
        }

        if (isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }

        if ($isCreate && empty($data['passwordHash'])) {
            $errors[] = 'Password is required';
        }

        return $errors;
    }
}
