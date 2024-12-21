<?php

namespace App\Controller;

use App\Entity\PriceHistoryPart;
use App\Form\PriceHistoryPartType;
use App\Repository\PriceHistoryPartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/price/history/part')]
class PriceHistoryPartController extends AbstractController
{
    #[Route('/', name: 'app_price_history_part_index', methods: ['GET'])]
    public function index(PriceHistoryPartRepository $priceHistoryPartRepository): Response
    {
        $parts = $priceHistoryPartRepository->findAll();
        return $this->json($parts);
    }

    #[Route('/create', name: 'app_price_history_part_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $priceHistoryPart = new PriceHistoryPart();
        $priceHistoryPart->setEffectiveDate(new \DateTime($data['effectiveDate'] ?? 'now'));
        $priceHistoryPart->setPrice($data['price'] ?? 0.0);

        $entityManager->persist($priceHistoryPart);
        $entityManager->flush();

        return $this->json($priceHistoryPart, Response::HTTP_CREATED);
    }

    #[Route('/{part}', name: 'app_price_history_part_show', methods: ['GET'])]
    public function show(PriceHistoryPart $priceHistoryPart): Response
    {
        return $this->json($priceHistoryPart);
    }

    #[Route('/{part}/edit', name: 'app_price_history_part_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, PriceHistoryPart $priceHistoryPart, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['effectiveDate'])) {
            $priceHistoryPart->setEffectiveDate(new \DateTime($data['effectiveDate']));
        }
        if (isset($data['price'])) {
            $priceHistoryPart->setPrice($data['price']);
        }

        $entityManager->flush();

        return $this->json($priceHistoryPart);
    }

    #[Route('/{part}/delete', name: 'app_price_history_part_delete', methods: ['DELETE'])]
    public function delete(PriceHistoryPart $priceHistoryPart, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($priceHistoryPart);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
