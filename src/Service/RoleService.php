<?php

namespace App\Service;

use App\Entity\Role;

class RoleService
{
    public function createOrUpdateRole(array $data, ?Role $role = null): Role
    {
        if (!$role) {
            $role = new Role();
        }

        $role->setRoleName($data['roleName'] ?? $role->getRoleName());
        $role->setDescription($data['description'] ?? $role->getDescription());

        return $role;
    }
}

