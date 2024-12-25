<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\Repair;
use App\Form\InvoiceType;
use App\Repository\InvoiceRepository;
use App\Repository\RepairRepository;
use App\Service\InvoiceService;
use App\Service\InvoiceValidator;
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
        InvoiceValidator $validator,
        InvoiceService $service
    ): Response {
        $data = json_decode($request->getContent(), true);

        $errors = $validator->validate($data);
        if (!empty($errors)) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $repair = $entityManager->getRepository(Repair::class)->find($data['repairId']);
        if (!$repair) {
            return $this->json(['error' => 'Repair not found.'], Response::HTTP_NOT_FOUND);
        }

        $invoice = $service->createOrUpdateInvoice($data, $repair);
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
        InvoiceValidator $validator,
        InvoiceService $service
    ): Response {
        $data = json_decode($request->getContent(), true);

        $errors = $validator->validate($data);
        if (!empty($errors)) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        if (isset($data['repair_id'])) {
            $repair = $entityManager->getRepository(Repair::class)->find($data['repairId']);
            if (!$repair) {
                return $this->json(['error' => 'Repair not found.'], Response::HTTP_NOT_FOUND);
            }
            $invoice->setRepair($repair);
        }

        $invoice = $service->createOrUpdateInvoice($data, $invoice->getRepair(), $invoice);
        $entityManager->flush();

        return $this->json($invoice, Response::HTTP_OK);
    }

    #[Route('/{id}/delete', name: 'app_invoice_delete', methods: ['DELETE'])]
    public function delete(Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($invoice);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
