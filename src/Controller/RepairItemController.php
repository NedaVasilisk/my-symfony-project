<?php

namespace App\Controller;

use App\Entity\RepairItem;
use App\Form\RepairItemType;
use App\Repository\RepairItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/repair-item')]
class RepairItemController extends AbstractController
{
    #[Route('/', name: 'app_repair_item_index', methods: ['GET'])]
    public function index(RepairItemRepository $repairItemRepository): Response
    {
        $repairItems = $repairItemRepository->findAll();
        return $this->json($repairItems, 200, [], ['groups' => ['repair_item_detail', 'repair_list', 'service_list']]);
    }

    #[Route('/create', name: 'app_repair_item_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $repairItem = new RepairItem();
        $repairItem->setQuantity($data['quantity'] ?? 1);
        $repairItem->setPriceAtTime($data['priceAtTime'] ?? 0.0);

        $entityManager->persist($repairItem);
        $entityManager->flush();

        return $this->json($repairItem, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_repair_item_show', methods: ['GET'])]
    public function show(RepairItem $repairItem): Response
    {
        return $this->json($repairItem, 200, [], ['groups' => ['repair_item_detail', 'repair_list', 'service_list']]);
    }

    #[Route('/{id}/edit', name: 'app_repair_item_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, RepairItem $repairItem, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['quantity'])) $repairItem->setQuantity($data['quantity']);
        if (isset($data['priceAtTime'])) $repairItem->setPriceAtTime($data['priceAtTime']);

        $entityManager->flush();

        return $this->json($repairItem);
    }

    #[Route('/{id}/delete', name: 'app_repair_item_delete', methods: ['DELETE'])]
    public function delete(RepairItem $repairItem, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($repairItem);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

}
