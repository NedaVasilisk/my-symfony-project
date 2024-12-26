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

#[Route('/api/service')]
class ServiceController extends AbstractController
{
    public function __construct(private ServiceService $serviceService) {}

    #[Route('/', name: 'app_service_index', methods: ['GET'])]
    public function index(ServiceRepository $serviceRepository): JsonResponse
    {
        $services = $serviceRepository->findAll();
        return $this->json($services, Response::HTTP_OK);
    }

    #[Route('/collection', name: 'app_service_collection', methods: ['GET'])]
    public function getCollection(Request $request, ServiceRepository $serviceRepository): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? max((int)$requestData['itemsPerPage'], 1) : 10;
        $page = isset($requestData['page']) ? max((int)$requestData['page'], 1) : 1;

        $servicesData = $serviceRepository->getAllServicesByFilter($requestData, $itemsPerPage, $page);

        return $this->json($servicesData, JsonResponse::HTTP_OK, [], ['groups' => ['service_detail']]);
    }

    #[Route('/create', name: 'app_service_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $service = $this->serviceService->createService($requestData);
        return $this->json($service, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_service_show', methods: ['GET'])]
    public function show(Service $service): JsonResponse
    {
        return $this->json($service, Response::HTTP_OK);
    }

    #[Route('/{id}/edit', name: 'app_service_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, Service $service): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $updatedService = $this->serviceService->updateService($service, $requestData);
        return $this->json($updatedService, Response::HTTP_OK);
    }

    #[Route('/{id}/delete', name: 'app_service_delete', methods: ['DELETE'])]
    public function delete(Service $service): JsonResponse
    {
        $this->serviceService->deleteService($service);
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
