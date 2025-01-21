<?php

namespace App\Controller;

use App\Entity\Repair;
use App\Repository\RepairRepository;
use App\Service\RepairService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('api/repairs')]
class RepairController extends AbstractController
{
    public function __construct(private readonly RepairService $repairService) {}

    #[Route('/', name: 'app_repair_index', methods: ['GET'])]
    public function index(Request $request, RepairRepository $repairRepository): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? max((int)$requestData['itemsPerPage'], 1) : 10;
        $page = isset($requestData['page']) ? max((int)$requestData['page'], 1) : 1;

        $repairsData = $repairRepository->getAllRepairsByFilter($requestData, $itemsPerPage, $page);

        return $this->json($repairsData, Response::HTTP_OK, [], ['groups' => ['repair_detail', 'vehicle_list']]);
    }

    #[Route('/', name: 'app_repair_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        try {
            $this->repairService->createRepair($requestData);
            return $this->json(['message' => 'Successfully created'], Response::HTTP_CREATED, [], ['groups' => ['repair_detail']]);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'app_repair_show', methods: ['GET'])]
    public function show(Repair $repair): JsonResponse
    {
        return $this->json($repair, Response::HTTP_OK, [], ['groups' => ['repair_detail', 'vehicle_list']]);
    }

    #[Route('/{id}', name: 'app_repair_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, Repair $repair): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        try {
            $this->repairService->updateRepair($repair, $requestData);
            return $this->json(['message' => 'Successfully updated'], Response::HTTP_OK, [], ['groups' => ['repair_detail']]);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'app_repair_delete', methods: ['DELETE'])]
    public function delete(Repair $repair): JsonResponse
    {
        try {
            $this->repairService->deleteRepair($repair);
            return $this->json(['message' => 'Successfully deleted'], Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
