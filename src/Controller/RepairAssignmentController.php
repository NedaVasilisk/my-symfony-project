<?php

namespace App\Controller;

use App\Entity\RepairAssignment;
use App\Repository\RepairAssignmentRepository;
use App\Service\RepairAssignmentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/repair-assignment')]
class RepairAssignmentController extends AbstractController
{
    public function __construct(private RepairAssignmentService $repairAssignmentService) {}

    #[Route('/', name: 'app_repair_assignment_index', methods: ['GET'])]
    public function index(RepairAssignmentRepository $repairAssignmentRepository): JsonResponse
    {
        $repairAssignments = $repairAssignmentRepository->findAll();
        return $this->json($repairAssignments, JsonResponse::HTTP_OK, [], ['groups' => ['repair_assignment_detail', 'repair_list', 'employee_list']]);
    }

    #[Route('/collection', name: 'app_repair_assignment_collection', methods: ['GET'])]
    public function getCollection(Request $request, RepairAssignmentRepository $repairAssignmentRepository): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? max((int)$requestData['itemsPerPage'], 1) : 10;
        $page = isset($requestData['page']) ? max((int)$requestData['page'], 1) : 1;

        $repairAssignmentsData = $repairAssignmentRepository->getAllRepairAssignmentsByFilter($requestData, $itemsPerPage, $page);

        return $this->json(
            $repairAssignmentsData,
            JsonResponse::HTTP_OK,
            [],
            ['groups' => ['repair_assignment_detail', 'repair_list', 'employee_list']]
        );
    }

    #[Route('/create', name: 'app_repair_assignment_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $repairAssignment = $this->repairAssignmentService->createRepairAssignment($requestData);
        return $this->json($repairAssignment, JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_repair_assignment_show', methods: ['GET'])]
    public function show(RepairAssignment $repairAssignment): JsonResponse
    {
        return $this->json($repairAssignment, JsonResponse::HTTP_OK, [], ['groups' => ['repair_assignment_detail', 'repair_list', 'employee_list']]);
    }

    #[Route('/{id}/edit', name: 'app_repair_assignment_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, RepairAssignment $repairAssignment): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $updatedRepairAssignment = $this->repairAssignmentService->updateRepairAssignment($repairAssignment, $requestData);
        return $this->json($updatedRepairAssignment, JsonResponse::HTTP_OK);
    }

    #[Route('/{id}/delete', name: 'app_repair_assignment_delete', methods: ['DELETE'])]
    public function delete(RepairAssignment $repairAssignment): JsonResponse
    {
        $this->repairAssignmentService->deleteRepairAssignment($repairAssignment);
        return $this->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
