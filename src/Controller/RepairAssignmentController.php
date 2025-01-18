<?php

namespace App\Controller;

use App\Entity\RepairAssignment;
use App\Repository\RepairAssignmentRepository;
use App\Service\RepairAssignmentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/repairs/{repairId}/assignments')]
class RepairAssignmentController extends AbstractController
{
    public function __construct(private readonly RepairAssignmentService $repairAssignmentService) {}

    #[Route('/', name: 'app_repair_assignment_index', methods: ['GET'])]
    public function index(int $repairId, Request $request, RepairAssignmentRepository $repairAssignmentRepository): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? max((int)$requestData['itemsPerPage'], 1) : 10;
        $page = isset($requestData['page']) ? max((int)$requestData['page'], 1) : 1;

        $requestData['repair_id'] = $repairId;

        $repairAssignmentsData = $repairAssignmentRepository->getAllRepairAssignmentsByFilter($requestData, $itemsPerPage, $page);

        return $this->json($repairAssignmentsData, Response::HTTP_OK, [], ['groups' => ['repair_assignment_detail', 'repair_list', 'employee_list']]);
    }

    #[Route('/', name: 'app_repair_assignment_create', methods: ['POST'])]
    public function create(int $repairId, Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $requestData['repair_id'] = $repairId;

        $this->repairAssignmentService->createRepairAssignment($requestData);

        return $this->json(['message' => 'Successfully created'], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_repair_assignment_show', methods: ['GET'])]
    public function show(int $repairId, RepairAssignment $repairAssignment): JsonResponse
    {
        if ($repairAssignment->getRepair()->getId() !== $repairId) {
            throw new NotFoundHttpException('Repair assignment not found for this repair');
        }

        return $this->json($repairAssignment, Response::HTTP_OK, [], ['groups' => ['repair_assignment_detail', 'repair_list', 'employee_list']]);
    }

    #[Route('/{id}', name: 'app_repair_assignment_edit', methods: ['PUT', 'PATCH'])]
    public function edit(int $repairId, Request $request, RepairAssignment $repairAssignment): JsonResponse
    {
        if ($repairAssignment->getRepair()->getId() !== $repairId) {
            throw new NotFoundHttpException('Repair assignment not found for this repair');
        }

        $requestData = json_decode($request->getContent(), true);

        $updatedRepairAssignment = $this->repairAssignmentService->updateRepairAssignment($repairAssignment, $requestData);

        return $this->json(['message' => 'Successfully updated'], Response::HTTP_OK, [], ['groups' => ['repair_assignment_detail']]);
    }

    #[Route('/{id}', name: 'app_repair_assignment_delete', methods: ['DELETE'])]
    public function delete(int $repairId, RepairAssignment $repairAssignment): JsonResponse
    {
        if ($repairAssignment->getRepair()->getId() !== $repairId) {
            throw new NotFoundHttpException('Repair assignment not found for this repair');
        }

        $this->repairAssignmentService->deleteRepairAssignment($repairAssignment);

        return $this->json(['message' => 'Successfully deleted'], Response::HTTP_NO_CONTENT);
    }
}

