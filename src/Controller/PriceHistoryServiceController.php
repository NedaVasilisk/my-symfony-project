<?php

namespace App\Controller;

use App\Entity\PriceHistoryService;
use App\Form\PriceHistoryServiceType;
use App\Repository\PriceHistoryServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/price/history/service')]
class PriceHistoryServiceController extends AbstractController
{
    #[Route('/', name: 'app_price_history_service_index', methods: ['GET'])]
    public function index(PriceHistoryServiceRepository $priceHistoryServiceRepository): Response
    {
        $services = $priceHistoryServiceRepository->findAll();
        return $this->json($services);
    }

    #[Route('/create', name: 'app_price_history_service_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $priceHistoryService = new PriceHistoryService();
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

    #[Route('/{id}/edit', name: 'app_price_history_service_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, PriceHistoryService $priceHistoryService, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['effectiveDate'])) {
            $priceHistoryService->setEffectiveDate(new \DateTime($data['effectiveDate']));
        }
        if (isset($data['price'])) {
            $priceHistoryService->setPrice($data['price']);
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
