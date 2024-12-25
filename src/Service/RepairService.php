<?php

namespace App\Service;

use App\Entity\Repair;
use App\Entity\Vehicle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class RepairService
{
    public function __construct(private EntityManagerInterface $entityManager, private RequestCheckerService $requestCheckerService) {}

    public function createRepair(array $data): Repair
    {
        $this->requestCheckerService->check($data, ['vehicle_id', 'dateIn', 'status', 'totalCost']);
        $vehicle = $this->findVehicle($data['vehicle_id']);
        $repair = $this->fillRepairData(new Repair(), $data, $vehicle);
        $this->entityManager->persist($repair);
        $this->entityManager->flush();
        return $repair;
    }

    public function updateRepair(Repair $repair, array $data): Repair
    {
        $this->fillRepairData($repair, $data, $repair->getVehicle());
        $this->entityManager->flush();
        return $repair;
    }

    public function deleteRepair(Repair $repair): void
    {
        $this->entityManager->remove($repair);
        $this->entityManager->flush();
    }

    private function fillRepairData(Repair $repair, array $data, Vehicle $vehicle): Repair
    {
        $repair->setVehicle($vehicle);
        $repair->setDateIn(new \DateTime($data['dateIn'] ?? $repair->getDateIn()->format('Y-m-d')));
        $repair->setDateOut(isset($data['dateOut']) ? new \DateTime($data['dateOut']) : $repair->getDateOut());
        $repair->setStatus($data['status'] ?? $repair->getStatus());
        $repair->setTotalCost($data['totalCost'] ?? $repair->getTotalCost());
        return $repair;
    }

    private function findVehicle(int $vehicleId): Vehicle
    {
        $vehicle = $this->entityManager->getRepository(Vehicle::class)->find($vehicleId);
        if (!$vehicle) {
            throw new BadRequestException('Vehicle not found');
        }
        return $vehicle;
    }
}
