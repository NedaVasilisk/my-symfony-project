<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Repository\EmployeeRepository;
use App\Service\EmployeeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Exception;

#[Route('api/employees')]
class EmployeeController extends AbstractController
{
    public function __construct(private readonly EmployeeService $employeeService) {}

    #[Route('/', name: 'app_employee_index', methods: ['GET'])]
    public function index(Request $request, EmployeeRepository $employeeRepository): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? max((int)$requestData['itemsPerPage'], 1) : 10;
        $page = isset($requestData['page']) ? max((int)$requestData['page'], 1) : 1;

        $employeesData = $employeeRepository->getAllEmployeesByFilter($requestData, $itemsPerPage, $page);

        return $this->json($employeesData, Response::HTTP_OK, [], ['groups' => ['employee_detail', 'user_list']]);
    }

    #[Route('/', name: 'app_employee_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        try {
            $this->employeeService->createEmployee($requestData);
            return $this->json(['message' => 'Successfully created'], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'app_employee_show', methods: ['GET'])]
    public function show(Employee $employee): JsonResponse
    {
        return $this->json($employee, Response::HTTP_OK, [], ['groups' => ['employee_detail', 'user_list']]);
    }

    #[Route('/{id}', name: 'app_employee_edit', methods: ['PUT', 'PATCH'])]
    public function edit(int $id, Request $request, EmployeeRepository $employeeRepository): JsonResponse
    {
        $employee = $employeeRepository->find($id);
        if (!$employee) {
            throw new NotFoundHttpException('Employee not found');
        }

        $requestData = json_decode($request->getContent(), true);

        try {
            $this->employeeService->updateEmployee($employee, $requestData);
            return $this->json(['message' => 'Successfully updated'], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'app_employee_delete', methods: ['DELETE'])]
    public function delete(int $id, EmployeeRepository $employeeRepository): JsonResponse
    {
        $employee = $employeeRepository->find($id);
        if (!$employee) {
            throw new NotFoundHttpException('Employee not found');
        }

        try {
            $this->employeeService->deleteEmployee($employee);
            return $this->json(['message' => 'Successfully deleted'], Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
