<?php

namespace App\Service;

use App\Entity\User;

class UserService
{
    public function createOrUpdateUser(array $data, ?User $user = null): User
    {
        if (!$user) {
            $user = new User();
        }

        $user->setUsername($data['username'] ?? $user->getUsername());
        $user->setPasswordHash($data['passwordHash'] ?? $user->getPasswordHash());
        $user->setFirstName($data['firstName'] ?? $user->getFirstName());
        $user->setLastName($data['lastName'] ?? $user->getLastName());
        $user->setEmail($data['email'] ?? $user->getEmail());
        $user->setIsActive($data['isActive'] ?? $user->isActive());

        return $user;
    }
}
