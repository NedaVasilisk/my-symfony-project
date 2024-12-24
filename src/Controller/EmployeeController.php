<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Form\EmployeeType;
use App\Repository\EmployeeRepository;
use App\Repository\UserRepository;
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
        UserRepository $userRepository
    ): Response {
        $data = json_decode($request->getContent(), true);

        if (empty($data['userId'])) {
            return $this->json(['error' => 'Missing userId'], Response::HTTP_BAD_REQUEST);
        }
        $user = $userRepository->find($data['userId']);
        if (!$user) {
            return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $employee = new Employee();
        $employee
            ->setUser($user)
            ->setFirstName($data['firstName'] ?? '')
            ->setLastName($data['lastName'] ?? '')
            ->setPosition($data['position'] ?? '')
            ->setPhone($data['phone'] ?? null)
            ->setEmail($data['email'] ?? null)
            ->setSpecialization($data['specialization'] ?? null);

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
        UserRepository $userRepository
    ): Response {
        $data = json_decode($request->getContent(), true);

        if (isset($data['userId'])) {
            $user = $userRepository->find($data['userId']);
            if (!$user) {
                return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
            }
            $employee->setUser($user);
        }

        if (isset($data['firstName'])) {
            $employee->setFirstName($data['firstName']);
        }
        if (isset($data['lastName'])) {
            $employee->setLastName($data['lastName']);
        }
        if (isset($data['position'])) {
            $employee->setPosition($data['position']);
        }
        if (isset($data['phone'])) {
            $employee->setPhone($data['phone']);
        }
        if (isset($data['email'])) {
            $employee->setEmail($data['email']);
        }
        if (isset($data['specialization'])) {
            $employee->setSpecialization($data['specialization']);
        }

        $entityManager->flush();
        return $this->json($employee);
    }

    #[Route('/{id}/delete', name: 'app_employee_delete', methods: ['DELETE'])]
    public function delete(Employee $employee, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($employee);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
