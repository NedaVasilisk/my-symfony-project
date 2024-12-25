<?php

namespace App\Service;

use App\Entity\PriceHistoryService;
use App\Entity\Service;

class PriceHistoryServiceService
{
    public function createOrUpdatePriceHistoryService(
        array                $data,
        Service              $service,
        ?PriceHistoryService $priceHistoryService = null
    ): PriceHistoryService
    {
        if (!$priceHistoryService) {
            $priceHistoryService = new PriceHistoryService();
        }

        $priceHistoryService->setService($service);
        $priceHistoryService->setPrice($data['price'] ?? $priceHistoryService->getPrice());
        $priceHistoryService->setEffectiveDate(
            isset($data['effectiveDate']) ? new \DateTime($data['effectiveDate']) : $priceHistoryService->getEffectiveDate()
        );

        return $priceHistoryService;
    }
}
