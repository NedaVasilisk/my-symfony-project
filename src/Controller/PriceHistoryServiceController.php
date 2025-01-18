<?php

namespace App\Controller;

use App\Entity\PriceHistoryService;
use App\Repository\PriceHistoryServiceRepository;
use App\Repository\ServiceRepository;
use App\Service\PriceHistoryServiceService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/services/{serviceId}/price-history')]
class PriceHistoryServiceController extends AbstractController
{
    public function __construct(private readonly PriceHistoryServiceService $priceHistoryServiceService) {}

    #[Route('/', name: 'app_price_history_service_index', methods: ['GET'])]
    public function index(
        int $serviceId,
        Request $request,
        PriceHistoryServiceRepository $repository,
        ServiceRepository $serviceRepository
    ): JsonResponse {
        $service = $serviceRepository->find($serviceId);
        if (!$service) {
            throw new NotFoundHttpException('Service not found');
        }

        $requestData = $request->query->all();
        $requestData['service'] = $serviceId;

        $itemsPerPage = isset($requestData['itemsPerPage']) ? max((int)$requestData['itemsPerPage'], 1) : 10;
        $page = isset($requestData['page']) ? max((int)$requestData['page'], 1) : 1;

        $priceHistoryServices = $repository->getAllPriceHistoryServicesByFilter($requestData, $itemsPerPage, $page);

        return $this->json($priceHistoryServices, Response::HTTP_OK, [], ['groups' => ['price_history_service_list', 'service_list']]);
    }

    #[Route('/', name: 'app_price_history_service_create', methods: ['POST'])]
    public function create(
        int $serviceId,
        Request $request,
        ServiceRepository $serviceRepository
    ): JsonResponse {
        $service = $serviceRepository->find($serviceId);
        if (!$service) {
            throw new NotFoundHttpException('Service not found');
        }

        $requestData = json_decode($request->getContent(), true);

        if (!$requestData) {
            return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        $requestData['service_id'] = $serviceId;

        try {
            $this->priceHistoryServiceService->createPriceHistoryService($requestData);
            return $this->json(
                ['message' => 'Successfully created'],
                Response::HTTP_CREATED,
                [],
                ['groups' => ['price_history_service_list']]
            );
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'app_price_history_service_show', methods: ['GET'])]
    public function show(
        int $serviceId,
        PriceHistoryService $priceHistoryService,
        ServiceRepository $serviceRepository
    ): JsonResponse {
        $service = $serviceRepository->find($serviceId);
        if (!$service) {
            throw new NotFoundHttpException('Service not found');
        }

        if ($priceHistoryService->getService()->getId() !== $service->getId()) {
            throw new NotFoundHttpException('Price history entry not found for this service');
        }

        return $this->json($priceHistoryService, Response::HTTP_OK, [], ['groups' => ['price_history_service_list', 'service_list']]);
    }

    #[Route('/{id}', name: 'app_price_history_service_edit', methods: ['PUT', 'PATCH'])]
    public function edit(
        int $serviceId,
        PriceHistoryService $priceHistoryService,
        Request $request,
        ServiceRepository $serviceRepository
    ): JsonResponse {
        $service = $serviceRepository->find($serviceId);
        if (!$service) {
            throw new NotFoundHttpException('Service not found');
        }

        if ($priceHistoryService->getService()->getId() !== $service->getId()) {
            throw new NotFoundHttpException('Price history entry not found for this service');
        }

        $requestData = json_decode($request->getContent(), true);

        try {
            $this->priceHistoryServiceService->updatePriceHistoryService($priceHistoryService, $requestData);
            return $this->json(['message' => 'Successfully updated'], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'app_price_history_service_delete', methods: ['DELETE'])]
    public function delete(
        int $serviceId,
        PriceHistoryService $priceHistoryService,
        ServiceRepository $serviceRepository
    ): JsonResponse {
        $service = $serviceRepository->find($serviceId);
        if (!$service) {
            throw new NotFoundHttpException('Service not found');
        }

        if ($priceHistoryService->getService()->getId() !== $service->getId()) {
            throw new NotFoundHttpException('Price history entry not found for this service');
        }

        $this->priceHistoryServiceService->deletePriceHistoryService($priceHistoryService);

        return $this->json(['message' => 'Successfully deleted'], Response::HTTP_OK);
    }
}
