<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Repository\InvoiceRepository;
use App\Repository\PaymentRepository;
use App\Service\PaymentService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/payments')]
class PaymentController extends AbstractController
{
    public function __construct(private readonly PaymentService $paymentService) {}

    #[Route('/', name: 'app_payment_index', methods: ['GET'])]
    public function index(Request $request, PaymentRepository $paymentRepository): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? max((int)$requestData['itemsPerPage'], 1) : 10;
        $page = isset($requestData['page']) ? max((int)$requestData['page'], 1) : 1;

        $paymentsData = $paymentRepository->getAllPaymentsByFilter($requestData, $itemsPerPage, $page);

        return $this->json($paymentsData, Response::HTTP_OK, [], ['groups' => ['payments_detail', 'invoices_list']]);
    }

    #[Route('/', name: 'app_payment_create', methods: ['POST'])]
    public function create(Request $request, InvoiceRepository $invoiceRepository): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        if (!isset($requestData['invoice_id'])) {
            return $this->json(['error' => 'Missing required field: invoice_id'], Response::HTTP_BAD_REQUEST);
        }

        $invoice = $invoiceRepository->find($requestData['invoice_id']);
        if (!$invoice) {
            throw new NotFoundHttpException('Invoice not found');
        }

        $requestData['invoice'] = $invoice;

        try {
            $this->paymentService->createPayment($requestData);
            return $this->json(['message' => 'Successfully created'], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'app_payment_show', methods: ['GET'])]
    public function show(Payment $payment): JsonResponse
    {
        return $this->json($payment, Response::HTTP_OK, [], ['groups' => ['payments_detail', 'invoices_list']]);
    }

    #[Route('/{id}', name: 'app_payment_edit', methods: ['PUT', 'PATCH'])]
    public function edit(
        Payment $payment,
        Request $request,
        InvoiceRepository $invoiceRepository
    ): JsonResponse {
        $requestData = json_decode($request->getContent(), true);

        if (isset($requestData['invoice_id'])) {
            $invoice = $invoiceRepository->find($requestData['invoice_id']);
            if (!$invoice) {
                throw new NotFoundHttpException('Invoice not found');
            }
            $requestData['invoice'] = $invoice;
        }

        try {
            $this->paymentService->updatePayment($payment, $requestData);
            return $this->json(['message' => 'Successfully updated'], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'app_payment_delete', methods: ['DELETE'])]
    public function delete(Payment $payment): JsonResponse
    {
        try {
            $this->paymentService->deletePayment($payment);
            return $this->json(['message' => 'Successfully deleted'], Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
