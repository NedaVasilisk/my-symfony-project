<?php

namespace App\Service;

use App\Entity\RepairAssignment;
use App\Entity\Repair;
use App\Entity\Employee;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class RepairAssignmentService
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private RequestCheckerService $requestCheckerService) {}

    public function createRepairAssignment(array $data): RepairAssignment
    {
        if (empty($data['repair_id'])) {
            throw new BadRequestException('Repair ID is required');
        }

        $repair = $this->entityManager->getRepository(Repair::class)->find($data['repair_id']);
        if (!$repair) {
            throw new BadRequestException('Repair not found');
        }

        $employee = null;
        if (!empty($data['employeeId'])) {
            $employee = $this->entityManager->getRepository(Employee::class)->find($data['employeeId']);
            if (!$employee) {
                throw new BadRequestException('Employee not found');
            }
        }

        $repairAssignment = new RepairAssignment();
        $repairAssignment->setRepair($repair);
        $repairAssignment->setEmployee($employee);

        $this->entityManager->persist($repairAssignment);
        $this->entityManager->flush();

        return $repairAssignment;
    }

    public function updateRepairAssignment(RepairAssignment $repairAssignment, array $data): RepairAssignment
    {
        if (isset($data['employeeId'])) {
            $employee = $this->entityManager->getRepository(Employee::class)->find($data['employeeId']);
            if (!$employee) {
                throw new BadRequestException('Employee not found');
            }
            $repairAssignment->setEmployee($employee);
        }
        $this->entityManager->flush();

        return $repairAssignment;
    }

    public function deleteRepairAssignment(RepairAssignment $repairAssignment): void
    {
        $this->entityManager->remove($repairAssignment);
        $this->entityManager->flush();
    }

    private function fillRepairAssignmentData(RepairAssignment $repairAssignment, Repair $repair, ?Employee $employee): RepairAssignment
    {
        $repairAssignment->setRepair($repair);
        $repairAssignment->setEmployee($employee);
        return $repairAssignment;
    }

    private function findRepair(int $repairId): Repair
    {
        $repair = $this->entityManager->getRepository(Repair::class)->find($repairId);
        if (!$repair) {
            throw new BadRequestException('Repair not found');
        }
        return $repair;
    }

    private function findEmployee(int $employeeId): Employee
    {
        $employee = $this->entityManager->getRepository(Employee::class)->find($employeeId);
        if (!$employee) {
            throw new BadRequestException('Employee not found');
        }
        return $employee;
    }
}
