<?php

namespace App\Controller;

use App\Entity\Part;
use App\Entity\PriceHistoryPart;
use App\Form\PriceHistoryPartType;
use App\Repository\PriceHistoryPartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/price-history-part')]
class PriceHistoryPartController extends AbstractController
{
    #[Route('/', name: 'app_price_history_part_index', methods: ['GET'])]
    public function index(PriceHistoryPartRepository $priceHistoryPartRepository): Response
    {
        $parts = $priceHistoryPartRepository->findAll();
        return $this->json($parts, 200, [], ['groups' => ['price_history_part_list', 'part_detail']]);
    }

    #[Route('/create', name: 'app_price_history_part_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['part_id']) || !is_numeric($data['part_id'])) {
            return $this->json(['error' => 'Invalid or missing part_id'], Response::HTTP_BAD_REQUEST);
        }

        $part = $entityManager->getRepository(Part::class)->find($data['part_id']);
        if (!$part) {
            return $this->json(['error' => 'Part not found'], Response::HTTP_NOT_FOUND);
        }

        $priceHistoryPart = new PriceHistoryPart();
        $priceHistoryPart->setPart($part);
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

    #[Route('/part/{partId}', name: 'app_price_history_part_by_part', methods: ['GET'])]
    public function findByPartId(int $partId, EntityManagerInterface $entityManager): Response
    {
        $priceHistoryParts = $entityManager->getRepository(PriceHistoryPart::class)->findBy(['part' => $partId]);

        if (!$priceHistoryParts) {
            return $this->json(['error' => 'No price history found for the given part_id'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($priceHistoryParts);
    }

    #[Route('/{id}/edit', name: 'app_price_history_part_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, PriceHistoryPart $priceHistoryPart, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['effectiveDate'])) {
            try {
                $priceHistoryPart->setEffectiveDate(new \DateTime($data['effectiveDate']));
            } catch (\Exception $e) {
                return $this->json(['error' => 'Invalid effectiveDate format'], Response::HTTP_BAD_REQUEST);
            }
        }

        if (isset($data['price'])) {
            if (!is_numeric($data['price'])) {
                return $this->json(['error' => 'Invalid price'], Response::HTTP_BAD_REQUEST);
            }
            $priceHistoryPart->setPrice($data['price']);
        }

        if (isset($data['part_id'])) {
            $part = $entityManager->getRepository(Part::class)->find($data['part_id']);
            if (!$part) {
                return $this->json(['error' => 'Part not found'], Response::HTTP_NOT_FOUND);
            }
            $priceHistoryPart->setPart($part);
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
