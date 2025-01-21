<?php

namespace App\Controller;

use App\Entity\PriceHistoryPart;
use App\Repository\PartRepository;
use App\Repository\PriceHistoryPartRepository;
use App\Service\PriceHistoryPartService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/parts/{partId}/price-history')]
class PriceHistoryPartController extends AbstractController
{
    public function __construct(private readonly PriceHistoryPartService $priceHistoryPartService) {}

    #[Route('/', name: 'app_price_history_part_index', methods: ['GET'])]
    public function index(
        int $partId,
        Request $request,
        PriceHistoryPartRepository $repository,
        PartRepository $partRepository
    ): JsonResponse {
        $part = $partRepository->find($partId);
        if (!$part) {
            throw new NotFoundHttpException('Part not found');
        }

        $requestData = $request->query->all();
        $requestData['part'] = $partId;

        $itemsPerPage = isset($requestData['itemsPerPage']) ? max((int)$requestData['itemsPerPage'], 1) : 10;
        $page = isset($requestData['page']) ? max((int)$requestData['page'], 1) : 1;

        $priceHistoryParts = $repository->getAllPriceHistoryPartsByFilter($requestData, $itemsPerPage, $page);

        return $this->json($priceHistoryParts, Response::HTTP_OK, [], ['groups' => ['price_history_part_list', 'part_list']]);
    }

    #[Route('/', name: 'app_price_history_part_create', methods: ['POST'])]
    public function create(
        int $partId,
        Request $request,
        PartRepository $partRepository
    ): JsonResponse {
        $part = $partRepository->find($partId);
        if (!$part) {
            throw new NotFoundHttpException('Part not found');
        }

        $requestData = json_decode($request->getContent(), true);
        if (!$requestData) {
            return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        $requestData['part_id'] = $partId;

        try {
            $this->priceHistoryPartService->createPriceHistoryPart($requestData);
            return $this->json(['message' => 'Successfully created'], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'app_price_history_part_show', methods: ['GET'])]
    public function show(
        int $partId,
        PriceHistoryPart $priceHistoryPart,
        PartRepository $partRepository
    ): JsonResponse {
        $part = $partRepository->find($partId);
        if (!$part) {
            throw new NotFoundHttpException('Part not found');
        }

        if ($priceHistoryPart->getPart()->getId() !== $part->getId()) {
            throw new NotFoundHttpException('Price history entry not found for this part');
        }

        return $this->json($priceHistoryPart, Response::HTTP_OK, [], ['groups' => ['price_history_part_list', 'part_list']]);
    }

    #[Route('/{id}', name: 'app_price_history_part_edit', methods: ['PUT', 'PATCH'])]
    public function edit(
        int $partId,
        PriceHistoryPart $priceHistoryPart,
        Request $request,
        PartRepository $partRepository
    ): JsonResponse {
        $part = $partRepository->find($partId);
        if (!$part) {
            throw new NotFoundHttpException('Part not found');
        }

        if ($priceHistoryPart->getPart()->getId() !== $part->getId()) {
            throw new NotFoundHttpException('Price history entry not found for this part');
        }

        $requestData = json_decode($request->getContent(), true);

        try {
            $this->priceHistoryPartService->updatePriceHistoryPart($priceHistoryPart, $requestData);
            return $this->json(['message' => 'Successfully updated'], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'app_price_history_part_delete', methods: ['DELETE'])]
    public function delete(
        int $partId,
        PriceHistoryPart $priceHistoryPart,
        PartRepository $partRepository
    ): JsonResponse {
        $part = $partRepository->find($partId);
        if (!$part) {
            throw new NotFoundHttpException('Part not found');
        }

        if ($priceHistoryPart->getPart()->getId() !== $part->getId()) {
            throw new NotFoundHttpException('Price history entry not found for this part');
        }

        $this->priceHistoryPartService->deletePriceHistoryPart($priceHistoryPart);

        return $this->json(['message' => 'Successfully deleted'], Response::HTTP_OK);
    }
}

