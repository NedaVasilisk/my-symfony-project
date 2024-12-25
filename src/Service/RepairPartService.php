<?php

namespace App\Service;

use App\Entity\RepairPart;
use App\Entity\Repair;
use App\Entity\Part;

class RepairPartService
{
    public function createOrUpdateRepairPart(
        array       $data,
        Repair      $repair,
        Part        $part,
        ?RepairPart $repairPart = null
    ): RepairPart
    {
        if (!$repairPart) {
            $repairPart = new RepairPart();
            $repairPart->setRepair($repair);
            $repairPart->setPart($part);
        }

        $repairPart->setQuantity($data['quantity'] ?? $repairPart->getQuantity());
        $repairPart->setPriceAtTime($data['priceAtTime'] ?? $repairPart->getPriceAtTime());

        return $repairPart;
    }
}
