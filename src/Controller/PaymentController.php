<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Repository\PaymentRepository;
use App\Service\PaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/payment')]
class PaymentController extends AbstractController
{
    public function __construct(private PaymentService $paymentService) {}

    #[Route('/', name: 'app_payment_index', methods: ['GET'])]
    public function index(PaymentRepository $repository): JsonResponse
    {
        $payments = $repository->findAll();
        return $this->json($payments, Response::HTTP_OK, [], ['groups' => ['payments_detail', 'invoices_list']]);
    }

    #[Route('/collection', name: 'app_payment_collection', methods: ['GET'])]
    public function getCollection(Request $request, PaymentRepository $paymentRepository): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? max((int)$requestData['itemsPerPage'], 1) : 10;
        $page = isset($requestData['page']) ? max((int)$requestData['page'], 1) : 1;

        $paymentsData = $paymentRepository->getAllPaymentsByFilter($requestData, $itemsPerPage, $page);

        return $this->json(
            $paymentsData,
            JsonResponse::HTTP_OK,
            [],
            ['groups' => ['payments_detail', 'invoices_list']]
        );
    }

    #[Route('/create', name: 'app_payment_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $payment = $this->paymentService->createPayment($requestData);
        return $this->json($payment, JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_payment_show', methods: ['GET'])]
    public function show(Payment $payment): JsonResponse
    {
        return $this->json($payment, Response::HTTP_OK, [], ['groups' => ['payments_detail', 'invoices_list']]);
    }

    #[Route('/{id}/edit', name: 'app_payment_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, Payment $payment): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $updatedPayment = $this->paymentService->updatePayment($payment, $requestData);
        return $this->json($updatedPayment, JsonResponse::HTTP_OK);
    }

    #[Route('/{id}/delete', name: 'app_payment_delete', methods: ['DELETE'])]
    public function delete(Payment $payment): JsonResponse
    {
        $this->paymentService->deletePayment($payment);
        return $this->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
