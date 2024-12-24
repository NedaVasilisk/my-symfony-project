<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Form\InvoiceType;
use App\Repository\InvoiceRepository;
use App\Repository\RepairRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/invoice')]
class InvoiceController extends AbstractController
{
    #[Route('/', name: 'app_invoice_index', methods: ['GET'])]
    public function index(InvoiceRepository $invoiceRepository): Response
    {
        $invoices = $invoiceRepository->findAll();
        return $this->json($invoices, 200, [], ['groups' => ['invoices_detail', 'repair_list']]);
    }

    #[Route('/create', name: 'app_invoice_new', methods: ['POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        RepairRepository $repairRepository
    ): Response {
        $data = json_decode($request->getContent(), true);

        if (empty($data['repairId'])) {
            return $this->json(['error' => 'Missing repairId'], Response::HTTP_BAD_REQUEST);
        }
        $repair = $repairRepository->find($data['repairId']);
        if (!$repair) {
            return $this->json(['error' => 'Repair not found'], Response::HTTP_NOT_FOUND);
        }

        $invoice = new Invoice();
        $invoice
            ->setRepair($repair)
            ->setDateIssued(new \DateTime($data['dateIssued'] ?? 'now'))
            ->setTotalAmount($data['totalAmount'] ?? 0.0)
            ->setIsPaid($data['isPaid'] ?? false);

        $entityManager->persist($invoice);
        $entityManager->flush();

        return $this->json($invoice, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_invoice_show', methods: ['GET'])]
    public function show(Invoice $invoice): Response
    {
        return $this->json($invoice, 200, [], ['groups' => ['invoices_detail', 'repair_list']]);
    }

    #[Route('/{id}/edit', name: 'app_invoice_edit', methods: ['PUT', 'PATCH'])]
    public function edit(
        Request $request,
        Invoice $invoice,
        EntityManagerInterface $entityManager,
        RepairRepository $repairRepository
    ): Response {
        $data = json_decode($request->getContent(), true);

        if (isset($data['repairId'])) {
            $repair = $repairRepository->find($data['repairId']);
            if (!$repair) {
                return $this->json(['error' => 'Repair not found'], Response::HTTP_NOT_FOUND);
            }
            $invoice->setRepair($repair);
        }

        if (isset($data['dateIssued'])) {
            $invoice->setDateIssued(new \DateTime($data['dateIssued']));
        }
        if (isset($data['totalAmount'])) {
            $invoice->setTotalAmount($data['totalAmount']);
        }
        if (isset($data['isPaid'])) {
            $invoice->setIsPaid($data['isPaid']);
        }

        $entityManager->flush();
        return $this->json($invoice);
    }

    #[Route('/{id}/delete', name: 'app_invoice_delete', methods: ['DELETE'])]
    public function delete(Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($invoice);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
