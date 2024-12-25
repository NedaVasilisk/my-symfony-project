<?php

namespace App\Controller;

use App\Entity\Repair;
use App\Repository\RepairRepository;
use App\Service\RepairService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/repair')]
class RepairController extends AbstractController
{
    public function __construct(private RepairService $repairService) {}

    #[Route('/', name: 'app_repair_index', methods: ['GET'])]
    public function index(RepairRepository $repairRepository): JsonResponse
    {
        $repairs = $repairRepository->findAll();
        return $this->json($repairs, Response::HTTP_OK, [], ['groups' => ['repair_detail', 'vehicle_list']]);
    }

    #[Route('/create', name: 'app_repair_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $repair = $this->repairService->createRepair($requestData);
        return $this->json($repair, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_repair_show', methods: ['GET'])]
    public function show(Repair $repair): JsonResponse
    {
        return $this->json($repair, Response::HTTP_OK, [], ['groups' => ['repair_detail', 'vehicle_list']]);
    }

    #[Route('/{id}/edit', name: 'app_repair_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, Repair $repair): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $updatedRepair = $this->repairService->updateRepair($repair, $requestData);
        return $this->json($updatedRepair, Response::HTTP_OK);
    }

    #[Route('/{id}/delete', name: 'app_repair_delete', methods: ['DELETE'])]
    public function delete(Repair $repair): JsonResponse
    {
        $this->repairService->deleteRepair($repair);
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
