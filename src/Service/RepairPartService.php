<?php

namespace App\Service;

use App\Entity\RepairPart;
use App\Entity\Repair;
use App\Entity\Part;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class RepairPartService
{
    public function __construct(private EntityManagerInterface $entityManager, private RequestCheckerService $requestCheckerService) {}

    public function createRepairPart(array $data): RepairPart
    {
        $this->requestCheckerService->check($data, ['part_id', 'repair_id', 'quantity', 'priceAtTime']);
        $part = $this->findPart($data['part_id']);
        $repair = $this->findRepair($data['repair_id']);
        $repairPart = $this->fillRepairPartData(new RepairPart(), $data, $part, $repair);
        $this->entityManager->persist($repairPart);
        $this->entityManager->flush();
        return $repairPart;
    }

    public function updateRepairPart(RepairPart $repairPart, array $data): RepairPart
    {
        $part = isset($data['part_id']) ? $this->findPart($data['part_id']) : $repairPart->getPart();
        $repair = $repairPart->getRepair();
        $this->fillRepairPartData($repairPart, $data, $part, $repair);
        $this->entityManager->flush();
        return $repairPart;
    }

    public function deleteRepairPart(RepairPart $repairPart): void
    {
        $this->entityManager->remove($repairPart);
        $this->entityManager->flush();
    }

    private function fillRepairPartData(RepairPart $repairPart, array $data, Part $part, Repair $repair): RepairPart
    {
        $repairPart->setPart($part);
        $repairPart->setRepair($repair);
        $repairPart->setQuantity($data['quantity']);
        $repairPart->setPriceAtTime($data['priceAtTime']);
        return $repairPart;
    }

    private function findPart(int $partId): Part
    {
        $part = $this->entityManager->getRepository(Part::class)->find($partId);
        if (!$part) {
            throw new BadRequestException('Part not found');
        }
        return $part;
    }

    private function findRepair(int $repairId): Repair
    {
        $repair = $this->entityManager->getRepository(Repair::class)->find($repairId);
        if (!$repair) {
            throw new BadRequestException('Repair not found');
        }
        return $repair;
    }
}
