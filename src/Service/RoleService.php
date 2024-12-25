<?php

namespace App\Service;

use App\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;

class RoleService
{
    public function __construct(private EntityManagerInterface $entityManager, private RequestCheckerService $requestCheckerService) {}

    public function createRole(array $data): Role
    {
        $this->requestCheckerService->check($data, ['roleName', 'description']);
        $role = $this->fillRoleData(new Role(), $data);
        $this->entityManager->persist($role);
        $this->entityManager->flush();
        return $role;
    }

    public function updateRole(Role $role, array $data): Role
    {
        $this->fillRoleData($role, $data);
        $this->entityManager->flush();
        return $role;
    }

    public function deleteRole(Role $role): void
    {
        $this->entityManager->remove($role);
        $this->entityManager->flush();
    }

    private function fillRoleData(Role $role, array $data): Role
    {
        $role->setRoleName($data['roleName'] ?? $role->getRoleName());
        $role->setDescription($data['description'] ?? $role->getDescription());
        return $role;
    }
}

