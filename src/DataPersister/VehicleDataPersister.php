<?php


namespace App\DataPersister;

use ApiPlatform\Symfony\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Vehicle;
use App\Service\VehicleService;
use Doctrine\ORM\EntityManagerInterface;

class VehicleDataPersister implements ContextAwareDataPersisterInterface
{
    private EntityManagerInterface $entityManager;
    private VehicleService $vehicleService;

    public function __construct(EntityManagerInterface $entityManager, VehicleService $vehicleService)
    {
        $this->entityManager = $entityManager;
        $this->vehicleService = $vehicleService;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof Vehicle;
    }

    public function persist($data, array $context = [])
    {
        if ($context['collection_operation_name'] ?? null === 'post') {
            $vehicle = $this->vehicleService->createVehicle($data);
        } else {
            $vehicle = $this->vehicleService->updateVehicle($data, $data);
        }

        return $vehicle;
    }

    public function remove($data, array $context = [])
    {
        $this->vehicleService->deleteVehicle($data);
    }
}
