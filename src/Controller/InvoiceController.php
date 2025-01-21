<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Repository\InvoiceRepository;
use App\Service\InvoiceService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/invoices')]
class InvoiceController extends AbstractController
{
    public function __construct(private readonly InvoiceService $invoiceService) {}

    #[Route('/', name: 'app_invoice_index', methods: ['GET'])]
    public function index(Request $request, InvoiceRepository $invoiceRepository): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? max((int)$requestData['itemsPerPage'], 1) : 10;
        $page = isset($requestData['page']) ? max((int)$requestData['page'], 1) : 1;

        $invoicesData = $invoiceRepository->getAllInvoicesByFilter($requestData, $itemsPerPage, $page);

        return $this->json($invoicesData, Response::HTTP_OK, [], ['groups' => ['invoices_detail', 'repair_list']]);
    }

    #[Route('/', name: 'app_invoice_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        try {
            $this->invoiceService->createInvoice($requestData);
            return $this->json(['message' => 'Successfully created'], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'app_invoice_show', methods: ['GET'])]
    public function show(Invoice $invoice): JsonResponse
    {
        return $this->json($invoice, Response::HTTP_OK, [], ['groups' => ['invoices_detail', 'repair_list']]);
    }

    #[Route('/{id}', name: 'app_invoice_edit', methods: ['PUT', 'PATCH'])]
    public function edit(int $id, Request $request, InvoiceRepository $invoiceRepository): JsonResponse
    {
        $invoice = $invoiceRepository->find($id);
        if (!$invoice) {
            throw new NotFoundHttpException('Invoice not found');
        }

        $requestData = json_decode($request->getContent(), true);

        try {
            $this->invoiceService->updateInvoice($invoice, $requestData);
            return $this->json(['message' => 'Successfully updated'], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'app_invoice_delete', methods: ['DELETE'])]
    public function delete(int $id, InvoiceRepository $invoiceRepository): JsonResponse
    {
        $invoice = $invoiceRepository->find($id);
        if (!$invoice) {
            throw new NotFoundHttpException('Invoice not found');
        }

        try {
            $this->invoiceService->deleteInvoice($invoice);
            return $this->json(['message' => 'Successfully deleted'], Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
