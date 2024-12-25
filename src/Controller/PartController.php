<?php

namespace App\Controller;

use App\Entity\Part;
use App\Form\PartType;
use App\Repository\PartRepository;
use App\Service\PartService;
use App\Service\PartValidator;
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
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        PartValidator $validator,
        PartService $service
    ): Response {
        $data = json_decode($request->getContent(), true);

        $errors = $validator->validate($data);
        if (!empty($errors)) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $part = $service->createOrUpdatePart($data);
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
    public function edit(
        Request $request,
        Part $part,
        EntityManagerInterface $entityManager,
        PartValidator $validator,
        PartService $service
    ): Response {
        $data = json_decode($request->getContent(), true);

        $errors = $validator->validate($data);
        if (!empty($errors)) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $part = $service->createOrUpdatePart($data, $part);
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
