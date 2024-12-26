<?php

namespace App\Controller;

use App\Entity\RepairItem;
use App\Repository\RepairItemRepository;
use App\Service\RepairItemService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/repair-item')]
class RepairItemController extends AbstractController
{
    public function __construct(private RepairItemService $repairItemService) {}

    #[Route('/', name: 'app_repair_item_index', methods: ['GET'])]
    public function index(RepairItemRepository $repairItemRepository): JsonResponse
    {
        $repairItems = $repairItemRepository->findAll();
        return $this->json($repairItems, JsonResponse::HTTP_OK, [], ['groups' => ['repair_item_detail', 'repair_list', 'service_list']]);
    }

    #[Route('/collection', name: 'app_repair_item_collection', methods: ['GET'])]
    public function getCollection(Request $request, RepairItemRepository $repairItemRepository): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? max((int)$requestData['itemsPerPage'], 1) : 10;
        $page = isset($requestData['page']) ? max((int)$requestData['page'], 1) : 1;

        $repairItemsData = $repairItemRepository->getAllRepairItemsByFilter($requestData, $itemsPerPage, $page);

        return $this->json(
            $repairItemsData,
            JsonResponse::HTTP_OK,
            [],
            ['groups' => ['repair_item_detail', 'repair_list', 'service_list']]
        );
    }

    #[Route('/create', name: 'app_repair_item_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $repairItem = $this->repairItemService->createRepairItem($requestData);
        return $this->json($repairItem, JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_repair_item_show', methods: ['GET'])]
    public function show(RepairItem $repairItem): JsonResponse
    {
        return $this->json($repairItem, JsonResponse::HTTP_OK, [], ['groups' => ['repair_item_detail', 'repair_list', 'service_list']]);
    }

    #[Route('/{id}/edit', name: 'app_repair_item_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, RepairItem $repairItem): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $updatedRepairItem = $this->repairItemService->updateRepairItem($repairItem, $requestData);
        return $this->json($updatedRepairItem, JsonResponse::HTTP_OK);
    }

    #[Route('/{id}/delete', name: 'app_repair_item_delete', methods: ['DELETE'])]
    public function delete(RepairItem $repairItem): JsonResponse
    {
        $this->repairItemService->deleteRepairItem($repairItem);
        return $this->json(null, JsonResponse::HTTP_NO_CONTENT);
    }

}
