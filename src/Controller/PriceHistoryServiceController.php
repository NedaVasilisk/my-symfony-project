<?php

namespace App\Controller;

use App\Entity\PriceHistoryService;
use App\Entity\Service;
use App\Form\PriceHistoryServiceType;
use App\Repository\PriceHistoryServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/price-history-service')]
class PriceHistoryServiceController extends AbstractController
{
    #[Route('/', name: 'app_price_history_service_index', methods: ['GET'])]
    public function index(PriceHistoryServiceRepository $priceHistoryServiceRepository): Response
    {
        $services = $priceHistoryServiceRepository->findAll();
        return $this->json($services, 200, [], ['groups' => ['price_history_service_list', 'service_detail']]);
    }

    #[Route('/create', name: 'app_price_history_service_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['service_id']) || !is_numeric($data['service_id'])) {
            return $this->json(['error' => 'Invalid or missing service ID'], Response::HTTP_BAD_REQUEST);
        }

        $service = $entityManager->getRepository(Service::class)->find($data['service_id']);
        if (!$service) {
            return $this->json(['error' => 'Service not found'], Response::HTTP_NOT_FOUND);
        }

        $priceHistoryService = new PriceHistoryService();
        $priceHistoryService->setService($service);
        $priceHistoryService->setEffectiveDate(new \DateTime($data['effectiveDate'] ?? 'now'));
        $priceHistoryService->setPrice($data['price'] ?? 0.0);

        $entityManager->persist($priceHistoryService);
        $entityManager->flush();

        return $this->json($priceHistoryService, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_price_history_service_show', methods: ['GET'])]
    public function show(PriceHistoryService $priceHistoryService): Response
    {
        return $this->json($priceHistoryService);
    }

    #[Route('/service/{id}', name: 'app_price_history_service_by_service', methods: ['GET'])]
    public function findByServiceId(int $id, EntityManagerInterface $entityManager): Response
    {
        $service = $entityManager->getRepository(Service::class)->find($id);
        if (!$service) {
            return $this->json(['error' => 'Service not found'], Response::HTTP_NOT_FOUND);
        }

        $priceHistoryServices = $entityManager->getRepository(PriceHistoryService::class)->findBy(['service' => $service]);

        return $this->json($priceHistoryServices);
    }

    #[Route('/{id}/edit', name: 'app_price_history_service_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, PriceHistoryService $priceHistoryService, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['service_id'])) {
            if (!is_numeric($data['service_id'])) {
                return $this->json(['error' => 'Invalid service ID'], Response::HTTP_BAD_REQUEST);
            }

            $service = $entityManager->getRepository(Service::class)->find($data['service_id']);
            if (!$service) {
                return $this->json(['error' => 'Service not found'], Response::HTTP_NOT_FOUND);
            }

            $priceHistoryService->setService($service);
        }

        if (isset($data['effectiveDate'])) {
            try {
                $priceHistoryService->setEffectiveDate(new \DateTime($data['effectiveDate']));
            } catch (\Exception $e) {
                return $this->json(['error' => 'Invalid date format for effectiveDate'], Response::HTTP_BAD_REQUEST);
            }
        }

        if (isset($data['price'])) {
            if (!is_numeric($data['price'])) {
                return $this->json(['error' => 'Invalid price'], Response::HTTP_BAD_REQUEST);
            }
            $priceHistoryService->setPrice((float)$data['price']);
        }

        $entityManager->flush();

        return $this->json($priceHistoryService);
    }

    #[Route('/{id}/delete', name: 'app_price_history_service_delete', methods: ['DELETE'])]
    public function delete(PriceHistoryService $priceHistoryService, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($priceHistoryService);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
