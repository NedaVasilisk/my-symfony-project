<?php

namespace App\Service;

use App\Entity\Employee;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class EmployeeService
{
    public function __construct(private EntityManagerInterface $entityManager, private RequestCheckerService $requestCheckerService) {}

    public function createEmployee(array $data): Employee
    {
        $this->requestCheckerService->check($data, ['user_id', 'firstName', 'lastName', 'position']);
        $user = $this->findUser($data['user_id']);
        $employee = $this->fillEmployeeData(new Employee(), $data, $user);
        $this->entityManager->persist($employee);
        $this->entityManager->flush();
        return $employee;
    }

    public function updateEmployee(Employee $employee, array $data): Employee
    {
        $user = isset($data['user_id']) ? $this->findUser($data['user_id']) : $employee->getUser();
        $this->fillEmployeeData($employee, $data, $user);
        $this->entityManager->flush();
        return $employee;
    }

    public function deleteEmployee(Employee $employee): void
    {
        $this->entityManager->remove($employee);
        $this->entityManager->flush();
    }

    private function fillEmployeeData(Employee $employee, array $data, User $user): Employee
    {
        $employee->setUser($user);
        $employee->setFirstName($data['firstName']);
        $employee->setLastName($data['lastName']);
        $employee->setPosition($data['position']);
        $employee->setPhone($data['phone'] ?? $employee->getPhone());
        $employee->setEmail($data['email'] ?? $employee->getEmail());
        $employee->setSpecialization($data['specialization'] ?? $employee->getSpecialization());
        return $employee;
    }

    private function findUser(int $userId): User
    {
        $user = $this->entityManager->getRepository(User::class)->find($userId);
        if (!$user) {
            throw new BadRequestException('User not found');
        }
        return $user;
    }
}
