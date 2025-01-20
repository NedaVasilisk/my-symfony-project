<?php

namespace App\Controller;

use App\Entity\RepairPart;
use App\Repository\RepairPartRepository;
use App\Service\RepairPartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('api/repairs/{repairId}/parts')]
class RepairPartController extends AbstractController
{
    public function __construct(private readonly RepairPartService $repairPartService) {}

    #[Route('/', name: 'app_repair_part_index', methods: ['GET'])]
    public function index(int $repairId, Request $request, RepairPartRepository $repairPartRepository): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? max((int)$requestData['itemsPerPage'], 1) : 10;
        $page = isset($requestData['page']) ? max((int)$requestData['page'], 1) : 1;

        $requestData['repair_id'] = $repairId;

        $repairPartsData = $repairPartRepository->getAllRepairPartsByFilter($requestData, $itemsPerPage, $page);

        return $this->json($repairPartsData, Response::HTTP_OK, [], ['groups' => ['repair_part_detail', 'repair_list', 'part_list']]);
    }

    #[Route('/', name: 'app_repair_part_create', methods: ['POST'])]
    public function create(int $repairId, Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $requestData['repair_id'] = $repairId;

        $this->repairPartService->createRepairPart($requestData);

        return $this->json(['message' => 'Successfully created'], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_repair_part_show', methods: ['GET'])]
    public function show(int $repairId, int $id, RepairPartRepository $repairPartRepository): JsonResponse
    {
        $repairPart = $repairPartRepository->find($id);

        if (!$repairPart || $repairPart->getRepair()->getId() !== $repairId) {
            throw $this->createNotFoundException('Repair part not found for this repair');
        }

        return $this->json($repairPart, Response::HTTP_OK, [], ['groups' => ['repair_part_detail', 'repair_list', 'part_list']]);
    }

    #[Route('/{id}', name: 'app_repair_part_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, RepairPart $repairPart, int $repairId): JsonResponse
    {
        if ($repairPart->getRepair()->getId() !== $repairId) {
            return $this->json(['error' => 'Repair part not found for this repair'], Response::HTTP_NOT_FOUND);
        }

        $requestData = json_decode($request->getContent(), true);
        $this->repairPartService->updateRepairPart($repairPart, $requestData);

        return $this->json(['message' => 'Successfully updated'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'app_repair_part_delete', methods: ['DELETE'])]
    public function delete(RepairPart $repairPart, int $repairId): JsonResponse
    {
        if ($repairPart->getRepair()->getId() !== $repairId) {
            return $this->json(['error' => 'Repair part not found for this repair'], Response::HTTP_NOT_FOUND);
        }

        $this->repairPartService->deleteRepairPart($repairPart);

        return $this->json(['message' => 'Successfully deleted'], Response::HTTP_NO_CONTENT);
    }
}
