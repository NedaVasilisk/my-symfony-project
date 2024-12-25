<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class CustomerValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['firstName'])) {
            $errors[] = 'First name is required.';
        }

        if (empty($data['lastName'])) {
            $errors[] = 'Last name is required.';
        }

        if (isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format.';
        }

        if (empty($data['phone'])) {
            $errors[] = 'Phone number is required.';
        }

        return $errors;
    }

    public function validateUser(?int $userId, EntityManagerInterface $entityManager): ?User
    {
        if ($userId === null) {
            return null;
        }

        $user = $entityManager->getRepository(User::class)->find($userId);

        return $user ?: null;
    }
}
