<?php

namespace App\Service;

use App\Entity\Employee;
use App\Entity\User;

class EmployeeService
{
    public function createOrUpdateEmployee(array $data, User $user, ?Employee $employee = null): Employee
    {
        if (!$employee) {
            $employee = new Employee();
        }

        $employee->setUser($user);
        $employee->setFirstName($data['firstName'] ?? '');
        $employee->setLastName($data['lastName'] ?? '');
        $employee->setPosition($data['position'] ?? '');
        $employee->setPhone($data['phone'] ?? null);
        $employee->setEmail($data['email'] ?? null);
        $employee->setSpecialization($data['specialization'] ?? null);

        return $employee;
    }
}
