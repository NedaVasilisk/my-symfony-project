<?php

namespace App\Controller;

use App\Entity\Vehicle;
use App\Repository\VehicleRepository;
use App\Service\VehicleService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('api/vehicles')]
class VehicleController extends AbstractController
{
    public function __construct(private readonly VehicleService $vehicleService) {}

    #[Route('/', name: 'app_vehicle_index', methods: ['GET'])]
    public function index(Request $request, VehicleRepository $vehicleRepository): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? (int)$requestData['itemsPerPage'] : 10;
        $page = isset($requestData['page']) ? (int)$requestData['page'] : 1;

        $vehiclesData = $vehicleRepository->getAllVehiclesByFilter($requestData, $itemsPerPage, $page);

        return $this->json($vehiclesData, Response::HTTP_OK, [], ['groups' => ['vehicle_detail', 'customer_list']]);
    }

    #[Route('/', name: 'app_vehicle_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        try {
            $this->vehicleService->createVehicle($requestData);
            return $this->json(['message' => 'Successfully created'], Response::HTTP_CREATED, [], ['groups' => ['vehicle_detail']]);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'app_vehicle_show', methods: ['GET'])]
    public function show(Vehicle $vehicle): JsonResponse
    {
        return $this->json($vehicle, Response::HTTP_OK, [], ['groups' => ['vehicle_detail', 'customer_list']]);
    }

    #[Route('/{id}', name: 'app_vehicle_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, Vehicle $vehicle): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        try {
            $this->vehicleService->updateVehicle($vehicle, $requestData);
            return $this->json(['message' => 'Successfully updated'], Response::HTTP_OK, [], ['groups' => ['vehicle_detail']]);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'app_vehicle_delete', methods: ['DELETE'])]
    public function delete(Vehicle $vehicle): JsonResponse
    {
        try {
            $this->vehicleService->deleteVehicle($vehicle);
            return $this->json(['message' => 'Successfully deleted'], Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
