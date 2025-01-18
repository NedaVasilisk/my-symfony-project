<?php

namespace App\Controller;

use App\Entity\Service;
use App\Repository\ServiceRepository;
use App\Service\ServiceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Exception;

#[Route('api/services')]
class ServiceController extends AbstractController
{
    public function __construct(private readonly ServiceService $serviceService) {}

    #[Route('/', name: 'app_service_index', methods: ['GET'])]
    public function index(Request $request, ServiceRepository $serviceRepository): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? max((int)$requestData['itemsPerPage'], 1) : 10;
        $page = isset($requestData['page']) ? max((int)$requestData['page'], 1) : 1;

        $servicesData = $serviceRepository->getAllServicesByFilter($requestData, $itemsPerPage, $page);

        return $this->json($servicesData, Response::HTTP_OK, [], ['groups' => ['service_detail']]);
    }

    #[Route('/', name: 'app_service_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        try {
            $service = $this->serviceService->createService($requestData);
            return $this->json(['message' => 'Successfully created'], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'app_service_show', methods: ['GET'])]
    public function show(Service $service): JsonResponse
    {
        return $this->json($service, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'app_service_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, Service $service): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        try {
            $updatedService = $this->serviceService->updateService($service, $requestData);
            return $this->json(['message' => 'Successfully updated'], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'app_service_delete', methods: ['DELETE'])]
    public function delete(Service $service): JsonResponse
    {
        try {
            $this->serviceService->deleteService($service);
            return $this->json(['message' => 'Successfully deleted'], Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
