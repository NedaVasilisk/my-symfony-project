<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function getAllUsers(): array
    {
        return $this->entityManager->getRepository(User::class)->findAll();
    }

    public function createUser(array $data): User
    {
        $user = new User();
        $this->fillUserData($user, $data);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function updateUser(User $user, array $data): User
    {
        $this->fillUserData($user, $data);
        $this->entityManager->flush();

        return $user;
    }

    public function deleteUser(User $user): void
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    private function fillUserData(User $user, array $data): void
    {
        $user->setUsername($data['username'] ?? $user->getUsername());
        $user->setPassword($data['passwordHash'] ?? $user->getPassword());
        $user->setFirstName($data['firstName'] ?? $user->getFirstName());
        $user->setLastName($data['lastName'] ?? $user->getLastName());
        $user->setEmail($data['email'] ?? $user->getEmail());
        $user->setIsActive($data['isActive'] ?? $user->isActive());

        if (isset($data['created_at'])) {
            try {
                $createdAt = new \DateTime($data['created_at']);
                $user->setCreatedAt($createdAt);
            } catch (\Exception $e) {
                throw new \InvalidArgumentException('Invalid date format for createdAt: ' . $data['created_at']);
            }
        }
    }
}
