<?php

namespace App\Controller;

use App\Entity\Repair;
use App\Entity\RepairItem;
use App\Entity\Service;
use App\Form\RepairItemType;
use App\Repository\RepairItemRepository;
use App\Service\RepairItemService;
use App\Service\RepairItemValidator;
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
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
        RepairItemValidator $validator,
        RepairItemService $service
    ): Response {
        $data = json_decode($request->getContent(), true);

        $errors = $validator->validate($data);
        if (!empty($errors)) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $repair = $entityManager->getRepository(Repair::class)->find($data['repair_id']);
        if (!$repair) {
            return $this->json(['error' => 'Repair not found.'], Response::HTTP_NOT_FOUND);
        }

        $serviceEntity = $entityManager->getRepository(Service::class)->find($data['service_id']);
        if (!$serviceEntity) {
            return $this->json(['error' => 'Service not found.'], Response::HTTP_NOT_FOUND);
        }

        $repairItem = $service->createOrUpdateRepairItem($data, $repair, $serviceEntity);
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
    public function edit(
        Request $request,
        RepairItem $repairItem,
        EntityManagerInterface $entityManager,
        RepairItemValidator $validator,
        RepairItemService $service
    ): Response {
        $data = json_decode($request->getContent(), true);

        $errors = $validator->validate($data);
        if (!empty($errors)) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $repair = $repairItem->getRepair();
        $serviceEntity = $repairItem->getService();

        if (isset($data['repair_id'])) {
            $repair = $entityManager->getRepository(Repair::class)->find($data['repair_id']);
            if (!$repair) {
                return $this->json(['error' => 'Repair not found.'], Response::HTTP_NOT_FOUND);
            }
        }

        if (isset($data['service_id'])) {
            $serviceEntity = $entityManager->getRepository(Service::class)->find($data['service_id']);
            if (!$serviceEntity) {
                return $this->json(['error' => 'Service not found.'], Response::HTTP_NOT_FOUND);
            }
        }

        $repairItem = $service->createOrUpdateRepairItem($data, $repair, $serviceEntity, $repairItem);
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
