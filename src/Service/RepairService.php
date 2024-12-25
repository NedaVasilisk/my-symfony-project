<?php

namespace App\Service;

use App\Entity\Repair;
use App\Entity\Vehicle;

class RepairService
{
    public function createOrUpdateRepair(array $data, Vehicle $vehicle, ?Repair $repair = null): Repair
    {
        if (!$repair) {
            $repair = new Repair();
            $repair->setVehicle($vehicle);
        }

        $repair->setDateIn(new \DateTime($data['dateIn'] ?? 'now'));
        $repair->setDateOut(isset($data['dateOut']) ? new \DateTime($data['dateOut']) : $repair->getDateOut());
        $repair->setStatus($data['status'] ?? $repair->getStatus());
        $repair->setTotalCost($data['totalCost'] ?? $repair->getTotalCost());

        return $repair;
    }
}
