<?php

namespace App\Service;

use App\Entity\RepairAssignment;
use App\Entity\Repair;
use App\Entity\Employee;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class RepairAssignmentService
{
    public function __construct(private EntityManagerInterface $entityManager, private RequestCheckerService $requestCheckerService) {}

    public function createRepairAssignment(array $data): RepairAssignment
    {
        $this->requestCheckerService->check($data, ['repair_id']);
        $repair = $this->findRepair($data['repair_id']);
        $employee = isset($data['employee_id']) ? $this->findEmployee($data['employee_id']) : null;
        $repairAssignment = $this->fillRepairAssignmentData(new RepairAssignment(), $repair, $employee);
        $this->entityManager->persist($repairAssignment);
        $this->entityManager->flush();
        return $repairAssignment;
    }

    public function updateRepairAssignment(RepairAssignment $repairAssignment, array $data): RepairAssignment
    {
        $employee = isset($data['employee_id']) ? $this->findEmployee($data['employee_id']) : $repairAssignment->getEmployee();
        $this->fillRepairAssignmentData($repairAssignment, $repairAssignment->getRepair(), $employee);
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
