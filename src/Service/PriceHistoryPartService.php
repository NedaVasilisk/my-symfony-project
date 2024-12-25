<?php

namespace App\Service;

use App\Entity\PriceHistoryPart;
use App\Entity\Part;

class PriceHistoryPartService
{
    public function createOrUpdatePriceHistoryPart(
        array             $data,
        Part              $part,
        ?PriceHistoryPart $priceHistoryPart = null
    ): PriceHistoryPart
    {
        if (!$priceHistoryPart) {
            $priceHistoryPart = new PriceHistoryPart();
        }

        $priceHistoryPart->setPart($part);
        $priceHistoryPart->setPrice($data['price'] ?? $priceHistoryPart->getPrice());
        $priceHistoryPart->setEffectiveDate(
            isset($data['effectiveDate']) ? new \DateTime($data['effectiveDate']) : $priceHistoryPart->getEffectiveDate()
        );

        return $priceHistoryPart;
    }
}
