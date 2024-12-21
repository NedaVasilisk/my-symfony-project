<?php

namespace App\Controller;

use App\Entity\RepairPart;
use App\Form\RepairPartType;
use App\Repository\RepairPartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/repair/part')]
class RepairPartController extends AbstractController
{
    #[Route('/', name: 'app_repair_part_index', methods: ['GET'])]
    public function index(RepairPartRepository $repairPartRepository): Response
    {
        $repairParts = $repairPartRepository->findAll();
        return $this->json($repairParts);
    }

    #[Route('/create', name: 'app_repair_part_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $repairPart = new RepairPart();
        $repairPart->setQuantity($data['quantity'] ?? 1);
        $repairPart->setPriceAtTime($data['priceAtTime'] ?? 0.0);

        $entityManager->persist($repairPart);
        $entityManager->flush();

        return $this->json($repairPart, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_repair_part_show', methods: ['GET'])]
    public function show(RepairPart $repairPart): Response
    {
        return $this->json($repairPart);
    }

    #[Route('/{id}/edit', name: 'app_repair_part_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, RepairPart $repairPart, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['quantity'])) $repairPart->setQuantity($data['quantity']);
        if (isset($data['priceAtTime'])) $repairPart->setPriceAtTime($data['priceAtTime']);

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
