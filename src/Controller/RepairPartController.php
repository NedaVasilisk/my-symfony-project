<?php

namespace App\Controller;

use App\Entity\Part;
use App\Entity\Repair;
use App\Entity\RepairPart;
use App\Form\RepairPartType;
use App\Repository\RepairPartRepository;
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
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['repair_id']) || empty($data['part_id'])) {
            return $this->json(['error' => 'repair_id and part_id are required'], Response::HTTP_BAD_REQUEST);
        }

        $repair = $entityManager->getRepository(Repair::class)->find($data['repair_id']);
        if (!$repair) {
            return $this->json(['error' => 'Repair not found'], Response::HTTP_NOT_FOUND);
        }

        $part = $entityManager->getRepository(Part::class)->find($data['part_id']);
        if (!$part) {
            return $this->json(['error' => 'Part not found'], Response::HTTP_NOT_FOUND);
        }

        $repairPart = new RepairPart();
        $repairPart->setRepair($repair);
        $repairPart->setPart($part);
        $repairPart->setQuantity($data['quantity'] ?? 1);
        $repairPart->setPriceAtTime($data['priceAtTime'] ?? 0.0);

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
    public function edit(Request $request, RepairPart $repairPart, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['repair_id'])) {
            $repair = $entityManager->getRepository(Repair::class)->find($data['repair_id']);
            if (!$repair) {
                return $this->json(['error' => 'Repair not found'], Response::HTTP_NOT_FOUND);
            }
            $repairPart->setRepair($repair);
        }

        if (isset($data['part_id'])) {
            $part = $entityManager->getRepository(Part::class)->find($data['part_id']);
            if (!$part) {
                return $this->json(['error' => 'Part not found'], Response::HTTP_NOT_FOUND);
            }
            $repairPart->setPart($part);
        }

        if (isset($data['quantity'])) {
            if (!is_numeric($data['quantity']) || $data['quantity'] <= 0) {
                return $this->json(['error' => 'Invalid quantity'], Response::HTTP_BAD_REQUEST);
            }
            $repairPart->setQuantity((int)$data['quantity']);
        }

        if (isset($data['priceAtTime'])) {
            if (!is_numeric($data['priceAtTime']) || $data['priceAtTime'] < 0) {
                return $this->json(['error' => 'Invalid priceAtTime'], Response::HTTP_BAD_REQUEST);
            }
            $repairPart->setPriceAtTime((float)$data['priceAtTime']);
        }

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
