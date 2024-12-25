<?php

namespace App\Controller;

use App\Entity\PriceHistoryService;
use App\Repository\PriceHistoryServiceRepository;
use App\Service\PriceHistoryServiceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/price-history-service')]
class PriceHistoryServiceController extends AbstractController
{
    public function __construct(private PriceHistoryServiceService $priceHistoryServiceService) {}

    #[Route('/', name: 'app_price_history_service_index', methods: ['GET'])]
    public function index(PriceHistoryServiceRepository $repository): JsonResponse
    {
        $priceHistory = $repository->findAll();
        return $this->json($priceHistory, JsonResponse::HTTP_OK);
    }

    #[Route('/create', name: 'app_price_history_service_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $priceHistoryService = $this->priceHistoryServiceService->createPriceHistoryService($requestData);
        return $this->json($priceHistoryService, JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_price_history_service_show', methods: ['GET'])]
    public function show(PriceHistoryService $priceHistoryService): JsonResponse
    {
        return $this->json($priceHistoryService, JsonResponse::HTTP_OK);
    }

    #[Route('/{id}/edit', name: 'app_price_history_service_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, PriceHistoryService $priceHistoryService): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $updatedPriceHistoryService = $this->priceHistoryServiceService->updatePriceHistoryService($priceHistoryService, $requestData);
        return $this->json($updatedPriceHistoryService, JsonResponse::HTTP_OK);
    }

    #[Route('/{id}/delete', name: 'app_price_history_service_delete', methods: ['DELETE'])]
    public function delete(PriceHistoryService $priceHistoryService): JsonResponse
    {
        $this->priceHistoryServiceService->deletePriceHistoryService($priceHistoryService);
        return $this->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
