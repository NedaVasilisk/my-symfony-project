<?php

namespace App\Service;

use App\Entity\PriceHistoryPart;
use App\Entity\Part;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class PriceHistoryPartService
{
    public function __construct(private EntityManagerInterface $entityManager, private RequestCheckerService $requestCheckerService) {}

    public function createPriceHistoryPart(array $data): PriceHistoryPart
    {
        $this->requestCheckerService->check($data, ['part_id', 'effectiveDate', 'price']);
        $part = $this->findPart($data['part_id']);
        $priceHistoryPart = $this->fillPriceHistoryPartData(new PriceHistoryPart(), $data, $part);
        $this->entityManager->persist($priceHistoryPart);
        $this->entityManager->flush();
        return $priceHistoryPart;
    }

    public function updatePriceHistoryPart(PriceHistoryPart $priceHistoryPart, array $data): PriceHistoryPart
    {
        $part = isset($data['part_id']) ? $this->findPart($data['part_id']) : $priceHistoryPart->getPart();
        $this->fillPriceHistoryPartData($priceHistoryPart, $data, $part);
        $this->entityManager->flush();
        return $priceHistoryPart;
    }

    public function deletePriceHistoryPart(PriceHistoryPart $priceHistoryPart): void
    {
        $this->entityManager->remove($priceHistoryPart);
        $this->entityManager->flush();
    }

    private function fillPriceHistoryPartData(PriceHistoryPart $priceHistoryPart, array $data, Part $part): PriceHistoryPart
    {
        $priceHistoryPart->setPart($part);
        $priceHistoryPart->setEffectiveDate(new \DateTime($data['effectiveDate']));
        $priceHistoryPart->setPrice($data['price']);
        return $priceHistoryPart;
    }

    private function findPart(int $partId): Part
    {
        $part = $this->entityManager->getRepository(Part::class)->find($partId);
        if (!$part) {
            throw new BadRequestException('Part not found');
        }
        return $part;
    }
}
