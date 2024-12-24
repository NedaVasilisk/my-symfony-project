<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Entity\Repair;
use App\Entity\RepairAssignment;
use App\Form\RepairAssignmentType;
use App\Repository\RepairAssignmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/repair-assignment')]
class RepairAssignmentController extends AbstractController
{
    #[Route('/', name: 'app_repair_assignment_index', methods: ['GET'])]
    public function index(RepairAssignmentRepository $repairAssignmentRepository): Response
    {
        $repairAssignments = $repairAssignmentRepository->findAll();
        return $this->json($repairAssignments, 200, [], ['groups' => ['repair_assignment_detail', 'repair_list', 'employee_list']]);
    }

    #[Route('/create', name: 'app_repair_assignment_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $repairAssignment = new RepairAssignment();

        $repair = $entityManager->getRepository(Repair::class)->find($data['repairId']);
        $employee = $entityManager->getRepository(Employee::class)->find($data['employeeId']);

        if (!$repair || !$employee) {
            return $this->json(['error' => 'Invalid repair or employee'], Response::HTTP_BAD_REQUEST);
        }

        $repairAssignment->setRepair($repair);
        $repairAssignment->setEmployee($employee);

        $entityManager->persist($repairAssignment);
        $entityManager->flush();

        return $this->json($repairAssignment, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_repair_assignment_show', methods: ['GET'])]
    public function show(RepairAssignment $repairAssignment): Response
    {
        return $this->json($repairAssignment, 200, [], ['groups' => ['repair_assignment_detail', 'repair_list', 'employee_list']]);
    }

    #[Route('/{id}/edit', name: 'app_repair_assignment_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, RepairAssignment $repairAssignment, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['repairId'])) {
            $repair = $entityManager->getRepository(Repair::class)->find($data['repairId']);
            if ($repair) $repairAssignment->setRepair($repair);
        }
        if (isset($data['employeeId'])) {
            $employee = $entityManager->getRepository(Employee::class)->find($data['employeeId']);
            if ($employee) $repairAssignment->setEmployee($employee);
        }

        $entityManager->flush();

        return $this->json($repairAssignment);
    }

    #[Route('/{id}/delete', name: 'app_repair_assignment_delete', methods: ['DELETE'])]
    public function delete(RepairAssignment $repairAssignment, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($repairAssignment);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
