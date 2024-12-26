<?php

namespace App\Controller;

use App\Entity\Part;
use App\Repository\PartRepository;
use App\Service\PartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/part')]
class PartController extends AbstractController
{
    public function __construct(private PartService $partService) {}

    #[Route('/', name: 'app_part_index', methods: ['GET'])]
    public function index(PartRepository $partRepository): JsonResponse
    {
        $parts = $partRepository->findAll();
        return $this->json($parts, JsonResponse::HTTP_OK);
    }

    #[Route('/collection', name: 'app_part_collection', methods: ['GET'])]
    public function getCollection(Request $request, PartRepository $partRepository): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? max((int)$requestData['itemsPerPage'], 1) : 10;
        $page = isset($requestData['page']) ? max((int)$requestData['page'], 1) : 1;

        $partsData = $partRepository->getAllPartsByFilter($requestData, $itemsPerPage, $page);

        return $this->json(
            $partsData,
            JsonResponse::HTTP_OK,
            [],
            ['groups' => ['part_detail']]
        );
    }

    #[Route('/create', name: 'app_part_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $part = $this->partService->createPart($requestData);
        return $this->json($part, JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_part_show', methods: ['GET'])]
    public function show(Part $part): JsonResponse
    {
        return $this->json($part, JsonResponse::HTTP_OK);
    }

    #[Route('/{id}/edit', name: 'app_part_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, Part $part): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $updatedPart = $this->partService->updatePart($part, $requestData);
        return $this->json($updatedPart, JsonResponse::HTTP_OK);
    }

    #[Route('/{id}/delete', name: 'app_part_delete', methods: ['DELETE'])]
    public function delete(Part $part): JsonResponse
    {
        $this->partService->deletePart($part);
        return $this->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
