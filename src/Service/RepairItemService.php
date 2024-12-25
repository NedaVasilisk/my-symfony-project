<?php

namespace App\Service;

use App\Entity\Repair;
use App\Entity\RepairItem;
use App\Entity\Service;
use Doctrine\ORM\EntityManagerInterface;

class RepairItemService
{
    public function createOrUpdateRepairItem(
        array $data,
        Repair $repair,
        Service $service,
        RepairItem $repairItem = null
    ): RepairItem {
        if (!$repairItem) {
            $repairItem = new RepairItem();
        }

        $repairItem->setRepair($repair);
        $repairItem->setService($service);
        $repairItem->setQuantity($data['quantity']);
        $repairItem->setPriceAtTime($data['price_at_time']);

        return $repairItem;
    }
}

