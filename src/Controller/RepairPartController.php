<?php

namespace App\Controller;

use App\Entity\Part;
use App\Entity\Repair;
use App\Entity\RepairPart;
use App\Form\RepairPartType;
use App\Repository\RepairPartRepository;
use App\Service\RepairPartService;
use App\Service\RepairPartValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/repair-part')]
class RepairPartController extends AbstractController
{
    #[Route('/', name: 'app_repair_part_index', methods: ['GET'])]
    public function index(RepairPartRepository $repairPartRepository): Response
    {
        $repairParts = $repairPartRepository->findAll();
        return $this->json($repairParts, 200, [], ['groups' => ['repair_part_detail', 'repair_list', 'part_list']]);
    }

    #[Route('/create', name: 'app_repair_part_new', methods: ['POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        RepairPartValidator $validator,
        RepairPartService $service
    ): Response {
        $data = json_decode($request->getContent(), true);

        $errors = $validator->validate($data);
        if (!empty($errors)) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $repair = $entityManager->getRepository(Repair::class)->find($data['repair_id']);
        if (!$repair) {
            return $this->json(['error' => 'Repair not found.'], Response::HTTP_NOT_FOUND);
        }

        $part = $entityManager->getRepository(Part::class)->find($data['part_id']);
        if (!$part) {
            return $this->json(['error' => 'Part not found.'], Response::HTTP_NOT_FOUND);
        }

        $repairPart = $service->createOrUpdateRepairPart($data, $repair, $part);
        $entityManager->persist($repairPart);
        $entityManager->flush();

        return $this->json($repairPart, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_repair_part_show', methods: ['GET'])]
    public function show(RepairPart $repairPart): Response
    {
        return $this->json($repairPart, 200, [], ['groups' => ['repair_part_detail', 'repair_list', 'part_list']]);
    }

    #[Route('/{id}/edit', name: 'app_repair_part_edit', methods: ['PUT', 'PATCH'])]
    public function edit(
        Request $request,
        RepairPart $repairPart,
        EntityManagerInterface $entityManager,
        RepairPartValidator $validator,
        RepairPartService $service
    ): Response {
        $data = json_decode($request->getContent(), true);

        $errors = $validator->validate($data);
        if (!empty($errors)) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $repair = $repairPart->getRepair();
        $part = $repairPart->getPart();

        if (isset($data['repair_id'])) {
            $repair = $entityManager->getRepository(Repair::class)->find($data['repair_id']);
            if (!$repair) {
                return $this->json(['error' => 'Repair not found.'], Response::HTTP_NOT_FOUND);
            }
        }

        if (isset($data['part_id'])) {
            $part = $entityManager->getRepository(Part::class)->find($data['part_id']);
            if (!$part) {
                return $this->json(['error' => 'Part not found.'], Response::HTTP_NOT_FOUND);
            }
        }

        $repairPart = $service->createOrUpdateRepairPart($data, $repair, $part, $repairPart);
        $entityManager->flush();

        return $this->json($repairPart);
    }

    #[Route('/{id}/delete', name: 'app_repair_part_delete', methods: ['DELETE'])]
    public function delete(RepairPart $repairPart, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($repairPart);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
