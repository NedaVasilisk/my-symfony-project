<?php

namespace App\Controller;

use App\Entity\RepairPart;
use App\Repository\RepairPartRepository;
use App\Service\RepairPartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/repair-parts')]
class RepairPartController extends AbstractController
{
    public function __construct(private RepairPartService $repairPartService) {}

    #[Route('/', name: 'app_repair_part_index', methods: ['GET'])]
    public function index(RepairPartRepository $repairPartRepository): JsonResponse
    {
        $repairParts = $repairPartRepository->findAll();
        return $this->json($repairParts, JsonResponse::HTTP_OK, [], ['groups' => ['repair_part_detail', 'repair_list', 'part_list']]);
    }

    #[Route('/collection', name: 'app_repair_part_collection', methods: ['GET'])]
    public function getCollection(Request $request, RepairPartRepository $repairPartRepository): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? max((int)$requestData['itemsPerPage'], 1) : 10;
        $page = isset($requestData['page']) ? max((int)$requestData['page'], 1) : 1;

        $repairPartsData = $repairPartRepository->getAllRepairPartsByFilter($requestData, $itemsPerPage, $page);

        return $this->json(
            $repairPartsData,
            JsonResponse::HTTP_OK,
            [],
            ['groups' => ['repair_part_detail', 'part_list', 'repair_list']]
        );
    }

    #[Route('/create', name: 'app_repair_part_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $repairPart = $this->repairPartService->createRepairPart($requestData);
        return $this->json($repairPart, JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_repair_part_show', methods: ['GET'])]
    public function show(RepairPart $repairPart): JsonResponse
    {
        return $this->json($repairPart, JsonResponse::HTTP_OK, [], ['groups' => ['repair_part_detail', 'repair_list', 'part_list']]);
    }

    #[Route('/{id}/edit', name: 'app_repair_part_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, RepairPart $repairPart): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $updatedRepairPart = $this->repairPartService->updateRepairPart($repairPart, $requestData);
        return $this->json($updatedRepairPart, JsonResponse::HTTP_OK);
    }

    #[Route('/{id}/delete', name: 'app_repair_part_delete', methods: ['DELETE'])]
    public function delete(RepairPart $repairPart): JsonResponse
    {
        $this->repairPartService->deleteRepairPart($repairPart);
        return $this->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
