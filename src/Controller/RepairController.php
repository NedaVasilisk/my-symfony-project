<?php

namespace App\Controller;

use App\Entity\Repair;
use App\Form\RepairType;
use App\Repository\RepairRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/repair')]
class RepairController extends AbstractController
{
    #[Route('/', name: 'app_repair_index', methods: ['GET'])]
    public function index(RepairRepository $repairRepository): Response
    {
        $repairs = $repairRepository->findAll();
        return $this->json($repairs);
    }

    #[Route('/create', name: 'app_repair_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $repair = new Repair();
        $repair->setDateIn(new \DateTime($data['dateIn'] ?? 'now'));
        $repair->setDateOut(isset($data['dateOut']) ? new \DateTime($data['dateOut']) : null);
        $repair->setStatus($data['status'] ?? 'Pending');
        $repair->setTotalCost($data['totalCost'] ?? 0.0);

        $entityManager->persist($repair);
        $entityManager->flush();

        return $this->json($repair, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_repair_show', methods: ['GET'])]
    public function show(Repair $repair): Response
    {
        return $this->json($repair);
    }

    #[Route('/{id}/edit', name: 'app_repair_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, Repair $repair, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['dateIn'])) $repair->setDateIn(new \DateTime($data['dateIn']));
        if (isset($data['dateOut'])) $repair->setDateOut(new \DateTime($data['dateOut']));
        if (isset($data['status'])) $repair->setStatus($data['status']);
        if (isset($data['totalCost'])) $repair->setTotalCost($data['totalCost']);

        $entityManager->flush();

        return $this->json($repair);
    }

    #[Route('/{id}', name: 'app_repair_delete', methods: ['DELETE'])]
    public function delete(Repair $repair, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($repair);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
