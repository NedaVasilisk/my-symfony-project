<?php

namespace App\Controller;

use App\Entity\PriceHistoryPart;
use App\Repository\PriceHistoryPartRepository;
use App\Service\PriceHistoryPartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/price-history-part')]
class PriceHistoryPartController extends AbstractController
{
    public function __construct(private PriceHistoryPartService $priceHistoryPartService) {}

    #[Route('/', name: 'app_price_history_part_index', methods: ['GET'])]
    public function index(PriceHistoryPartRepository $repository): JsonResponse
    {
        $priceHistory = $repository->findAll();
        return $this->json($priceHistory, JsonResponse::HTTP_OK);
    }

    #[Route('/collection', name: 'app_price_history_part_collection', methods: ['GET'])]
    public function getCollection(Request $request, PriceHistoryPartRepository $priceHistoryPartRepository): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? max((int)$requestData['itemsPerPage'], 1) : 10;
        $page = isset($requestData['page']) ? max((int)$requestData['page'], 1) : 1;

        $priceHistoryPartsData = $priceHistoryPartRepository->getAllPriceHistoryPartsByFilter($requestData, $itemsPerPage, $page);

        return $this->json(
            $priceHistoryPartsData,
            JsonResponse::HTTP_OK,
            [],
            ['groups' => ['price_history_part_list', 'part_list']]
        );
    }

    #[Route('/create', name: 'app_price_history_part_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $priceHistoryPart = $this->priceHistoryPartService->createPriceHistoryPart($requestData);
        return $this->json($priceHistoryPart, JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_price_history_part_show', methods: ['GET'])]
    public function show(PriceHistoryPart $priceHistoryPart): JsonResponse
    {
        return $this->json($priceHistoryPart, JsonResponse::HTTP_OK);
    }

    #[Route('/{id}/edit', name: 'app_price_history_part_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, PriceHistoryPart $priceHistoryPart): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $updatedPriceHistoryPart = $this->priceHistoryPartService->updatePriceHistoryPart($priceHistoryPart, $requestData);
        return $this->json($updatedPriceHistoryPart, JsonResponse::HTTP_OK);
    }

    #[Route('/{id}/delete', name: 'app_price_history_part_delete', methods: ['DELETE'])]
    public function delete(PriceHistoryPart $priceHistoryPart): JsonResponse
    {
        $this->priceHistoryPartService->deletePriceHistoryPart($priceHistoryPart);
        return $this->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
