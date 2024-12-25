<?php

namespace App\Service;

use App\Entity\PriceHistoryService;
use App\Entity\Service;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class PriceHistoryServiceService
{
    public function __construct(private EntityManagerInterface $entityManager, private RequestCheckerService $requestCheckerService) {}

    public function createPriceHistoryService(array $data): PriceHistoryService
    {
        $this->requestCheckerService->check($data, ['service_id', 'effectiveDate', 'price']);
        $service = $this->findService($data['service_id']);
        $priceHistory = $this->fillPriceHistoryServiceData(new PriceHistoryService(), $data, $service);
        $this->entityManager->persist($priceHistory);
        $this->entityManager->flush();
        return $priceHistory;
    }

    public function updatePriceHistoryService(PriceHistoryService $priceHistoryService, array $data): PriceHistoryService
    {
        $service = isset($data['service_id']) ? $this->findService($data['service_id']) : $priceHistoryService->getService();
        $this->fillPriceHistoryServiceData($priceHistoryService, $data, $service);
        $this->entityManager->flush();
        return $priceHistoryService;
    }

    public function deletePriceHistoryService(PriceHistoryService $priceHistoryService): void
    {
        $this->entityManager->remove($priceHistoryService);
        $this->entityManager->flush();
    }

    private function fillPriceHistoryServiceData(PriceHistoryService $priceHistoryService, array $data, Service $service): PriceHistoryService
    {
        $priceHistoryService->setService($service);
        $priceHistoryService->setEffectiveDate(new \DateTime($data['effectiveDate']));
        $priceHistoryService->setPrice($data['price']);
        return $priceHistoryService;
    }

    private function findService(int $serviceId): Service
    {
        $service = $this->entityManager->getRepository(Service::class)->find($serviceId);
        if (!$service) {
            throw new BadRequestException('Service not found');
        }
        return $service;
    }
}
