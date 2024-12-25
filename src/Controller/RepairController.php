<?php

namespace App\Controller;

use App\Entity\Repair;
use App\Entity\Vehicle;
use App\Form\RepairType;
use App\Repository\RepairRepository;
use App\Service\RepairService;
use App\Service\RepairValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/repair')]
class RepairController extends AbstractController
{
    #[Route('/', name: 'app_repair_index', methods: ['GET'])]
    public function index(RepairRepository $repairRepository): Response
    {
        $repairs = $repairRepository->findAll();
        return $this->json($repairs, 200, [], ['groups' => ['repair_detail', 'vehicle_list']]);
    }

    #[Route('/create', name: 'app_repair_new', methods: ['POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        RepairValidator $repairValidator,
        RepairService $repairService
    ): Response {
        $data = json_decode($request->getContent(), true);

        $errors = $repairValidator->validate($data);
        if (!empty($errors)) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $vehicle = $entityManager->getRepository(Vehicle::class)->find($data['vehicle_id']);
        if (!$vehicle) {
            return $this->json(['error' => 'Vehicle not found.'], Response::HTTP_NOT_FOUND);
        }

        $repair = $repairService->createOrUpdateRepair($data, $vehicle);
        $entityManager->persist($repair);
        $entityManager->flush();

        return $this->json($repair, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_repair_show', methods: ['GET'])]
    public function show(Repair $repair): Response
    {
        return $this->json($repair, 200, [], ['groups' => ['repair_detail', 'vehicle_list']]);
    }

    #[Route('/{id}/edit', name: 'app_repair_edit', methods: ['PUT', 'PATCH'])]
    public function edit(
        Request $request,
        Repair $repair,
        EntityManagerInterface $entityManager,
        RepairValidator $repairValidator,
        RepairService $repairService
    ): Response {
        $data = json_decode($request->getContent(), true);

        $errors = $repairValidator->validate($data);
        if (!empty($errors)) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $vehicle = $repair->getVehicle();
        $repair = $repairService->createOrUpdateRepair($data, $vehicle, $repair);
        $entityManager->flush();

        return $this->json($repair);
    }

    #[Route('/{id}', name: 'app_repair_delete', methods: ['DELETE'])]
    public function delete(Repair $repair, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($repair);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
