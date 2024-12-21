<?php

namespace App\Controller;

use App\Entity\Part;
use App\Form\PartType;
use App\Repository\PartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/part')]
class PartController extends AbstractController
{
    #[Route('/', name: 'app_part_index', methods: ['GET'])]
    public function index(PartRepository $partRepository): Response
    {
        $parts = $partRepository->findAll();
        return $this->json($parts);
    }

    #[Route('/create', name: 'app_part_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $part = new Part();
        $part->setName($data['name'] ?? null)
            ->setManufacturer($data['manufacturer'] ?? null)
            ->setPartNumber($data['partNumber'] ?? null)
            ->setCurrentPrice($data['currentPrice'] ?? 0)
            ->setQuantityInStock($data['quantityInStock'] ?? 0);

        $entityManager->persist($part);
        $entityManager->flush();

        return $this->json($part, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_part_show', methods: ['GET'])]
    public function show(Part $part): Response
    {
        return $this->json($part);
    }

    #[Route('/{id}/edit', name: 'app_part_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, Part $part, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) $part->setName($data['name']);
        if (isset($data['manufacturer'])) $part->setManufacturer($data['manufacturer']);
        if (isset($data['partNumber'])) $part->setPartNumber($data['partNumber']);
        if (isset($data['currentPrice'])) $part->setCurrentPrice($data['currentPrice']);
        if (isset($data['quantityInStock'])) $part->setQuantityInStock($data['quantityInStock']);

        $entityManager->flush();

        return $this->json($part);
    }

    #[Route('/{id}/delete', name: 'app_part_delete', methods: ['DELETE'])]
    public function delete(Part $part, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($part);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
