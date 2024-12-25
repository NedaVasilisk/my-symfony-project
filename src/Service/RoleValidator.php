<?php

namespace App\Service;

class RoleValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['roleName'])) {
            $errors[] = 'Role name is required.';
        }

        if (isset($data['description']) && strlen($data['description']) > 255) {
            $errors[] = 'Description is too long (maximum 255 characters).';
        }

        return $errors;
    }
}
