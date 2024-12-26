<?php

namespace App\Controller;

use App\Entity\Vehicle;
use App\Form\VehicleType;
use App\Repository\VehicleRepository;
use App\Service\VehicleService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/vehicle')]
class VehicleController extends AbstractController
{
    public function __construct(private VehicleService $vehicleService) {}

    #[Route('/', name: 'app_vehicle_index', methods: ['GET'])]
    #[IsGranted("Адміністратор")]
    public function index(VehicleRepository $vehicleRepository): JsonResponse
    {
        $vehicles = $vehicleRepository->findAll();
        return $this->json($vehicles, Response::HTTP_OK, [], ['groups' => ['vehicle_detail', 'customer_list']]);
    }

    #[Route('/collection', name: 'app_vehicle_collection', methods: ['GET'])]
    #[IsGranted("ROLE_USER")]
    public function getCollection(Request $request, VehicleRepository $vehicleRepository): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? (int)$requestData['itemsPerPage'] : 10;
        $page = isset($requestData['page']) ? (int)$requestData['page'] : 1;

        $vehiclesData = $vehicleRepository->getAllVehiclesByFilter($requestData, $itemsPerPage, $page);

        return $this->json($vehiclesData, Response::HTTP_CREATED, [], ['groups' => ['vehicle_detail', 'customer_list']]);
    }

    #[Route('/create', name: 'app_vehicle_create', methods: ['POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $vehicle = $this->vehicleService->createVehicle($requestData);
        return $this->json($vehicle, Response::HTTP_CREATED, [], ['groups' => ['vehicle_detail', 'customer_list']]);
    }

    #[Route('/{id}', name: 'app_vehicle_show', methods: ['GET'])]
    #[IsGranted("ROLE_USER")]
    public function show(Vehicle $vehicle): JsonResponse
    {
        return $this->json($vehicle, Response::HTTP_OK, [], ['groups' => ['vehicle_detail', 'customer_list']]);
    }

    #[Route('/{id}/edit', name: 'app_vehicle_edit', methods: ['PUT', 'PATCH'])]
    #[IsGranted("ROLE_ADMIN")]
    public function edit(Request $request, Vehicle $vehicle): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $updatedVehicle = $this->vehicleService->updateVehicle($vehicle, $requestData);
        return $this->json($updatedVehicle, Response::HTTP_OK, [], ['groups' => ['vehicle_detail', 'customer_list']]);
    }

    #[Route('/{id}/delete', name: 'app_vehicle_delete', methods: ['DELETE'])]
    #[IsGranted("ROLE_ADMIN")]
    public function delete(Vehicle $vehicle): JsonResponse
    {
        $this->vehicleService->deleteVehicle($vehicle);
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

}
