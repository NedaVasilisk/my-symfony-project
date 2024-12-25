<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Repository\EmployeeRepository;
use App\Service\EmployeeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/employee')]
class EmployeeController extends AbstractController
{
    public function __construct(private EmployeeService $employeeService) {}

    #[Route('/', name: 'app_employee_index', methods: ['GET'])]
    public function index(EmployeeRepository $repository): JsonResponse
    {
        $employees = $repository->findAll();
        return $this->json($employees, Response::HTTP_OK, [], ['groups' => ['employee_detail', 'user_list']]);
    }

    #[Route('/create', name: 'app_employee_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $employee = $this->employeeService->createEmployee($requestData);
        return $this->json($employee, JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_employee_show', methods: ['GET'])]
    public function show(Employee $employee): JsonResponse
    {
        return $this->json($employee, Response::HTTP_OK, [], ['groups' => ['employee_detail', 'user_list']]);
    }

    #[Route('/{id}/edit', name: 'app_employee_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, Employee $employee): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $updatedEmployee = $this->employeeService->updateEmployee($employee, $requestData);
        return $this->json($updatedEmployee, JsonResponse::HTTP_OK);
    }

    #[Route('/{id}/delete', name: 'app_employee_delete', methods: ['DELETE'])]
    public function delete(Employee $employee): JsonResponse
    {
        $this->employeeService->deleteEmployee($employee);
        return $this->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
