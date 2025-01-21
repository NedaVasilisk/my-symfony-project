<?php

namespace App\Controller;

use App\Entity\RepairItem;
use App\Repository\RepairItemRepository;
use App\Service\RepairItemService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('api/repairs/{repairId}/items')]
class RepairItemController extends AbstractController
{
    public function __construct(private readonly RepairItemService $repairItemService) {}

    #[Route('/', name: 'app_repair_item_index', methods: ['GET'])]
    public function index(int $repairId, Request $request, RepairItemRepository $repairItemRepository): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? max((int)$requestData['itemsPerPage'], 1) : 10;
        $page = isset($requestData['page']) ? max((int)$requestData['page'], 1) : 1;

        $requestData['repair_id'] = $repairId;

        $repairItemsData = $repairItemRepository->getAllRepairItemsByFilter($requestData, $itemsPerPage, $page);

        return $this->json($repairItemsData, Response::HTTP_OK, [], ['groups' => ['repair_item_detail', 'repair_list', 'service_list']]);
    }

    #[Route('/', name: 'app_repair_item_create', methods: ['POST'])]
    public function create(int $repairId, Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $requestData['repair_id'] = $repairId;

        $this->repairItemService->createRepairItem($requestData);

        return $this->json(['message' => 'Successfully created'], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_repair_item_show', methods: ['GET'])]
    public function show(int $repairId, RepairItem $repairItem): JsonResponse
    {
        if ($repairItem->getRepair()->getId() !== $repairId) {
            throw $this->createNotFoundException('Repair item not found for this repair');
        }

        return $this->json($repairItem, Response::HTTP_OK, [], ['groups' => ['repair_item_detail', 'repair_list', 'service_list']]);
    }

    #[Route('/{id}', name: 'app_repair_item_edit', methods: ['PUT', 'PATCH'])]
    public function edit(int $repairId, Request $request, RepairItem $repairItem): JsonResponse
    {
        if ($repairItem->getRepair()->getId() !== $repairId) {
            throw $this->createNotFoundException('Repair item not found for this repair');
        }

        $requestData = json_decode($request->getContent(), true);
        $this->repairItemService->updateRepairItem($repairItem, $requestData);

        return $this->json(['message' => 'Successfully updated'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'app_repair_item_delete', methods: ['DELETE'])]
    public function delete(int $repairId, RepairItem $repairItem): JsonResponse
    {
        if ($repairItem->getRepair()->getId() !== $repairId) {
            throw $this->createNotFoundException('Repair item not found for this repair');
        }

        $this->repairItemService->deleteRepairItem($repairItem);

        return $this->json(['message' => 'Successfully deleted'], Response::HTTP_NO_CONTENT);
    }
}

