<?php

namespace App\Service;

use App\Entity\Part;
use Doctrine\ORM\EntityManagerInterface;

class PartService
{
    public function __construct(private EntityManagerInterface $entityManager, private RequestCheckerService $requestCheckerService) {}

    public function createPart(array $data): Part
    {
        $this->requestCheckerService->check($data, ['name', 'partNumber', 'currentPrice', 'quantityInStock']);
        $part = $this->fillPartData(new Part(), $data);
        $this->entityManager->persist($part);
        $this->entityManager->flush();
        return $part;
    }

    public function updatePart(Part $part, array $data): Part
    {
        $this->fillPartData($part, $data);
        $this->entityManager->flush();
        return $part;
    }

    public function deletePart(Part $part): void
    {
        $this->entityManager->remove($part);
        $this->entityManager->flush();
    }

    private function fillPartData(Part $part, array $data): Part
    {
        $part->setName($data['name'] ?? $part->getName());
        $part->setPartNumber($data['partNumber'] ?? $part->getPartNumber());
        $part->setCurrentPrice($data['currentPrice'] ?? $part->getCurrentPrice());
        $part->setQuantityInStock($data['quantityInStock'] ?? $part->getQuantityInStock());
        $part->setManufacturer($data['manufacturer'] ?? $part->getManufacturer());
        return $part;
    }
}
