<?php

namespace App\Controller;

use App\Entity\Part;
use App\Repository\PartRepository;
use App\Service\PartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Exception;

#[Route('api/parts')]
class PartController extends AbstractController
{
    public function __construct(private readonly PartService $partService) {}

    #[Route('/', name: 'app_part_index', methods: ['GET'])]
    public function index(Request $request, PartRepository $partRepository): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? max((int)$requestData['itemsPerPage'], 1) : 10;
        $page = isset($requestData['page']) ? max((int)$requestData['page'], 1) : 1;

        $partsData = $partRepository->getAllPartsByFilter($requestData, $itemsPerPage, $page);

        return $this->json($partsData, Response::HTTP_OK, [], ['groups' => ['part_detail']]);
    }

    #[Route('/', name: 'app_part_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        if (!$requestData) {
            return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->partService->createPart($requestData);
            return $this->json(['message' => 'Successfully created'], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'app_part_show', methods: ['GET'])]
    public function show(Part $part): JsonResponse
    {
        return $this->json($part, Response::HTTP_OK, [], ['groups' => ['part_detail']]);
    }

    #[Route('/{id}', name: 'app_part_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, Part $part): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        if (!$requestData) {
            return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->partService->updatePart($part, $requestData);
            return $this->json(['message' => 'Successfully updated'], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'app_part_delete', methods: ['DELETE'])]
    public function delete(Part $part): JsonResponse
    {
        try {
            $this->partService->deletePart($part);
            return $this->json(['message' => 'Successfully deleted'], Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}

