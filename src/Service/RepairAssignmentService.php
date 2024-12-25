<?php

namespace App\Service;

use App\Entity\RepairAssignment;
use App\Entity\Repair;
use App\Entity\Employee;

class RepairAssignmentService
{
    public function createOrUpdateRepairAssignment(
        Repair            $repair,
        Employee          $employee,
        ?RepairAssignment $repairAssignment = null
    ): RepairAssignment
    {
        if (!$repairAssignment) {
            $repairAssignment = new RepairAssignment();
            $repairAssignment->setRepair($repair);
            $repairAssignment->setEmployee($employee);
        }

        return $repairAssignment;
    }
}
