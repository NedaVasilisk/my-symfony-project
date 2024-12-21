<?php

namespace App\Controller;

use App\Entity\Vehicle;
use App\Form\VehicleType;
use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/vehicle')]
class VehicleController extends AbstractController
{
    #[Route('/', name: 'app_vehicle_index', methods: ['GET'])]
    public function index(VehicleRepository $vehicleRepository): Response
    {
        $vehicles = $vehicleRepository->findAll();
        return $this->json($vehicles);
    }

    #[Route('/create', name: 'app_vehicle_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $vehicle = new Vehicle();
        $vehicle->setVin($data['vin'] ?? null);
        $vehicle->setLicensePlate($data['licensePlate'] ?? null);
        $vehicle->setMake($data['make'] ?? null);
        $vehicle->setModel($data['model'] ?? null);
        $vehicle->setYear($data['year'] ?? null);
        $vehicle->setEngineType($data['engineType'] ?? null);
        $vehicle->setBatteryCapacity($data['batteryCapacity'] ?? null);
        $vehicle->setLastIotUpdate(isset($data['lastIotUpdate']) ? new \DateTime($data['lastIotUpdate']) : null);

        $entityManager->persist($vehicle);
        $entityManager->flush();

        return $this->json($vehicle, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_vehicle_show', methods: ['GET'])]
    public function show(Vehicle $vehicle): Response
    {
        return $this->json($vehicle);
    }

    #[Route('/{id}/edit', name: 'app_vehicle_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, Vehicle $vehicle, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['vin'])) $vehicle->setVin($data['vin']);
        if (isset($data['licensePlate'])) $vehicle->setLicensePlate($data['licensePlate']);
        if (isset($data['make'])) $vehicle->setMake($data['make']);
        if (isset($data['model'])) $vehicle->setModel($data['model']);
        if (isset($data['year'])) $vehicle->setYear($data['year']);
        if (isset($data['engineType'])) $vehicle->setEngineType($data['engineType']);
        if (isset($data['batteryCapacity'])) $vehicle->setBatteryCapacity($data['batteryCapacity']);
        if (isset($data['lastIotUpdate'])) $vehicle->setLastIotUpdate(new \DateTime($data['lastIotUpdate']));

        $entityManager->flush();

        return $this->json($vehicle);
    }

    #[Route('/{id}/delete', name: 'app_vehicle_delete', methods: ['DELETE'])]
    public function delete(Vehicle $vehicle, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($vehicle);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
