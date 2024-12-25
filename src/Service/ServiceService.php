<?php

namespace App\Service;

use App\Entity\Service;

class ServiceService
{
    public function createOrUpdateService(array $data, ?Service $service = null): Service
    {
        if (!$service) {
            $service = new Service();
        }

        $service->setName($data['name'] ?? $service->getName());
        $service->setDescription($data['description'] ?? $service->getDescription());
        $service->setCurrentPrice($data['currentPrice'] ?? $service->getCurrentPrice());

        return $service;
    }
}
