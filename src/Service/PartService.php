<?php

namespace App\Service;

use App\Entity\Part;

class PartService
{
    public function createOrUpdatePart(array $data, ?Part $part = null): Part
    {
        if (!$part) {
            $part = new Part();
        }

        $part->setName($data['name'] ?? $part->getName());
        $part->setManufacturer($data['manufacturer'] ?? $part->getManufacturer());
        $part->setPartNumber($data['partNumber']);
        $part->setCurrentPrice($data['currentPrice'] ?? $part->getCurrentPrice());
        $part->setQuantityInStock($data['quantityInStock'] ?? $part->getQuantityInStock());

        return $part;
    }
}
