<?php

namespace App\Service;

use App\Entity\Service;
use Doctrine\ORM\EntityManagerInterface;

class ServiceService
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function createService(array $data): Service
    {
        $service = new Service();
        $this->fillServiceData($service, $data);
        $this->entityManager->persist($service);
        $this->entityManager->flush();

        return $service;
    }

    public function updateService(Service $service, array $data): Service
    {
        $this->fillServiceData($service, $data);
        $this->entityManager->flush();

        return $service;
    }

    public function deleteService(Service $service): void
    {
        $this->entityManager->remove($service);
        $this->entityManager->flush();
    }

    private function fillServiceData(Service $service, array $data): void
    {
        $service->setName($data['name'] ?? $service->getName());
        $service->setDescription($data['description'] ?? $service->getDescription());
        $service->setCurrentPrice($data['currentPrice'] ?? $service->getCurrentPrice());
    }
}
