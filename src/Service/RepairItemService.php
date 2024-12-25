<?php

namespace App\Service;

use App\Entity\Repair;
use App\Entity\RepairItem;
use App\Entity\Service;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class RepairItemService
{
    public function __construct(private EntityManagerInterface $entityManager, private RequestCheckerService $requestCheckerService) {}

    public function createRepairItem(array $data): RepairItem
    {
        $this->requestCheckerService->check($data, ['repair_id', 'service_id', 'quantity', 'priceAtTime']);
        $repair = $this->findRepair($data['repair_id']);
        $service = $this->findService($data['service_id']);
        $repairItem = $this->fillRepairItemData(new RepairItem(), $data, $repair, $service);
        $this->entityManager->persist($repairItem);
        $this->entityManager->flush();
        return $repairItem;
    }

    public function updateRepairItem(RepairItem $repairItem, array $data): RepairItem
    {
        $repair = $repairItem->getRepair();
        $service = isset($data['service_id']) ? $this->findService($data['service_id']) : $repairItem->getService();
        $this->fillRepairItemData($repairItem, $data, $repair, $service);
        $this->entityManager->flush();
        return $repairItem;
    }

    public function deleteRepairItem(RepairItem $repairItem): void
    {
        $this->entityManager->remove($repairItem);
        $this->entityManager->flush();
    }

    private function fillRepairItemData(RepairItem $repairItem, array $data, Repair $repair, Service $service): RepairItem
    {
        $repairItem->setRepair($repair);
        $repairItem->setService($service);
        $repairItem->setQuantity($data['quantity']);
        $repairItem->setPriceAtTime($data['priceAtTime']);
        return $repairItem;
    }

    private function findRepair(int $repairId): Repair
    {
        $repair = $this->entityManager->getRepository(Repair::class)->find($repairId);
        if (!$repair) {
            throw new BadRequestException('Repair not found');
        }
        return $repair;
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

