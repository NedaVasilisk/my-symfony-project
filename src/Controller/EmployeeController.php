<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Entity\User;
use App\Form\EmployeeType;
use App\Repository\EmployeeRepository;
use App\Repository\UserRepository;
use App\Service\EmployeeService;
use App\Service\EmployeeValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/employee')]
class EmployeeController extends AbstractController
{
    #[Route('/', name: 'app_employee_index', methods: ['GET'])]
    public function index(EmployeeRepository $employeeRepository): Response
    {
        $employees = $employeeRepository->findAll();
        return $this->json($employees, 200, [], ['groups' => ['employee_detail', 'user_list']]);
    }

    #[Route('/create', name: 'app_employee_new', methods: ['POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        EmployeeValidator $validator,
        EmployeeService $service
    ): Response {
        $data = json_decode($request->getContent(), true);

        $errors = $validator->validate($data);
        if (!empty($errors)) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $user = $entityManager->getRepository(User::class)->find($data['userId']);
        if (!$user) {
            return $this->json(['error' => 'User not found.'], Response::HTTP_NOT_FOUND);
        }

        $employee = $service->createOrUpdateEmployee($data, $user);
        $entityManager->persist($employee);
        $entityManager->flush();

        return $this->json($employee, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_employee_show', methods: ['GET'])]
    public function show(Employee $employee): Response
    {
        return $this->json($employee, 200, [], ['groups' => ['employee_detail', 'user_list']]);
    }

    #[Route('/{id}/edit', name: 'app_employee_edit', methods: ['PUT', 'PATCH'])]
    public function edit(
        Request $request,
        Employee $employee,
        EntityManagerInterface $entityManager,
        EmployeeValidator $validator,
        EmployeeService $service
    ): Response {
        $data = json_decode($request->getContent(), true);

        $errors = $validator->validate($data);
        if (!empty($errors)) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        if (isset($data['user_id'])) {
            $user = $entityManager->getRepository(User::class)->find($data['userId']);
            if (!$user) {
                return $this->json(['error' => 'User not found.'], Response::HTTP_NOT_FOUND);
            }
            $employee->setUser($user);
        }

        $employee = $service->createOrUpdateEmployee($data, $employee->getUser(), $employee);
        $entityManager->flush();

        return $this->json($employee, Response::HTTP_OK);
    }

    #[Route('/{id}/delete', name: 'app_employee_delete', methods: ['DELETE'])]
    public function delete(Employee $employee, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($employee);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
