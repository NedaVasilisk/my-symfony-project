<?php

namespace App\Controller;

use App\Entity\PriceHistoryService;
use App\Repository\PriceHistoryServiceRepository;
use App\Service\PriceHistoryServiceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/price-history-service')]
class PriceHistoryServiceController extends AbstractController
{
    public function __construct(private PriceHistoryServiceService $priceHistoryServiceService) {}

    #[Route('/', name: 'app_price_history_service_index', methods: ['GET'])]
    public function index(PriceHistoryServiceRepository $repository): JsonResponse
    {
        $priceHistory = $repository->findAll();
        return $this->json($priceHistory, JsonResponse::HTTP_OK);
    }

    #[Route('/collection', name: 'app_price_history_service_collection', methods: ['GET'])]
    public function getCollection(Request $request, PriceHistoryServiceRepository $priceHistoryServiceRepository): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? max((int)$requestData['itemsPerPage'], 1) : 10;
        $page = isset($requestData['page']) ? max((int)$requestData['page'], 1) : 1;

        $priceHistoryServicesData = $priceHistoryServiceRepository->getAllPriceHistoryServicesByFilter($requestData, $itemsPerPage, $page);

        return $this->json(
            $priceHistoryServicesData,
            JsonResponse::HTTP_OK,
            [],
            ['groups' => ['price_history_service_list', 'service_list']]
        );
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
