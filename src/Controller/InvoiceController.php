<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Repository\InvoiceRepository;
use App\Service\InvoiceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/invoice')]
class InvoiceController extends AbstractController
{
    public function __construct(private InvoiceService $invoiceService) {}

    #[Route('/', name: 'app_invoice_index', methods: ['GET'])]
    public function index(InvoiceRepository $repository): JsonResponse
    {
        $invoices = $repository->findAll();
        return $this->json($invoices, Response::HTTP_OK, [], ['groups' => ['invoices_detail', 'repair_list']]);
    }

    #[Route('/collection', name: 'app_invoice_collection', methods: ['GET'])]
    public function getCollection(Request $request, InvoiceRepository $invoiceRepository): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? max((int)$requestData['itemsPerPage'], 1) : 10;
        $page = isset($requestData['page']) ? max((int)$requestData['page'], 1) : 1;

        $invoicesData = $invoiceRepository->getAllInvoicesByFilter($requestData, $itemsPerPage, $page);

        return $this->json(
            $invoicesData,
            JsonResponse::HTTP_OK,
            [],
            ['groups' => ['invoices_detail', 'repair_list']]
        );
    }

    #[Route('/create', name: 'app_invoice_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $invoice = $this->invoiceService->createInvoice($requestData);
        return $this->json($invoice, JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_invoice_show', methods: ['GET'])]
    public function show(Invoice $invoice): JsonResponse
    {
        return $this->json($invoice, Response::HTTP_OK, [], ['groups' => ['invoices_detail', 'repair_list']]);
    }

    #[Route('/{id}/edit', name: 'app_invoice_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, Invoice $invoice): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $updatedInvoice = $this->invoiceService->updateInvoice($invoice, $requestData);
        return $this->json($updatedInvoice, JsonResponse::HTTP_OK);
    }

    #[Route('/{id}/delete', name: 'app_invoice_delete', methods: ['DELETE'])]
    public function delete(Invoice $invoice): JsonResponse
    {
        $this->invoiceService->deleteInvoice($invoice);
        return $this->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
