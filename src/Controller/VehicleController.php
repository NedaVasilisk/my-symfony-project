<?php

namespace App\Controller;

use App\Entity\Customer;
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
        return $this->json($vehicles, 200, [], ['groups' => ['vehicle_detail', 'customer_list']]);
    }

    #[Route('/create', name: 'app_vehicle_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $customerId = $data['customer_id'] ?? null;

        if (!$customerId) {
            return $this->json(['error' => 'Customer ID is required'], Response::HTTP_BAD_REQUEST);
        }

        $customer = $entityManager->getRepository(Customer::class)->find($customerId);

        if (!$customer) {
            return $this->json(['error' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }


        $vehicle = new Vehicle();
        $vehicle->setCustomer($customer);
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

        if (!$data) {
            return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        if (isset($data['customer_id'])) {
            $customerId = $data['customer_id'];
            if (!is_numeric($customerId)) {
                return $this->json(['error' => 'Invalid customer ID'], Response::HTTP_BAD_REQUEST);
            }

            $customer = $entityManager->getRepository(Customer::class)->find($customerId);

            if (!$customer) {
                return $this->json(['error' => 'Customer not found'], Response::HTTP_NOT_FOUND);
            }

            if (!$this->isGranted('EDIT', $customer)) {
                return $this->json(['error' => 'Access denied to customer'], Response::HTTP_FORBIDDEN);
            }

            $vehicle->setCustomer($customer);
        }

        if (isset($data['vin'])) $vehicle->setVin($data['vin']);
        if (isset($data['licensePlate'])) $vehicle->setLicensePlate($data['licensePlate']);
        if (isset($data['make'])) $vehicle->setMake($data['make']);
        if (isset($data['model'])) $vehicle->setModel($data['model']);
        if (isset($data['year'])) {
            if (!is_numeric($data['year']) || $data['year'] < 1886 || $data['year'] > (int)date('Y')) {
                return $this->json(['error' => 'Invalid year'], Response::HTTP_BAD_REQUEST);
            }
            $vehicle->setYear((int)$data['year']);
        }
        if (isset($data['engineType'])) $vehicle->setEngineType($data['engineType']);
        if (isset($data['batteryCapacity'])) {
            if (!is_numeric($data['batteryCapacity'])) {
                return $this->json(['error' => 'Invalid batteryCapacity'], Response::HTTP_BAD_REQUEST);
            }
            $vehicle->setBatteryCapacity($data['batteryCapacity']);
        }
        if (isset($data['lastIotUpdate'])) {
            try {
                $vehicle->setLastIotUpdate(new \DateTime($data['lastIotUpdate']));
            } catch (\Exception $e) {
                return $this->json(['error' => 'Invalid date format for lastIotUpdate'], Response::HTTP_BAD_REQUEST);
            }
        }

        // Збереження змін
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
