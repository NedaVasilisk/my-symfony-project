<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\Payment;
use App\Form\PaymentType;
use App\Repository\InvoiceRepository;
use App\Repository\PaymentRepository;
use App\Service\PaymentService;
use App\Service\PaymentValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/payment')]
class PaymentController extends AbstractController
{
    #[Route('/', name: 'app_payment_index', methods: ['GET'])]
    public function index(PaymentRepository $paymentRepository): Response
    {
        $payments = $paymentRepository->findAll();
        return $this->json($payments, 200, [], ['groups' => ['payments_detail', 'invoices_list']]);
    }

    #[Route('/create', name: 'app_payment_new', methods: ['POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        PaymentValidator $validator,
        PaymentService $service
    ): Response {
        $data = json_decode($request->getContent(), true);

        $errors = $validator->validate($data);
        if (!empty($errors)) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $invoice = $entityManager->getRepository(Invoice::class)->find($data['invoiceId']);
        if (!$invoice) {
            return $this->json(['error' => 'Invoice not found.'], Response::HTTP_NOT_FOUND);
        }

        $payment = $service->createOrUpdatePayment($data, $invoice);
        $entityManager->persist($payment);
        $entityManager->flush();

        return $this->json($payment, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_payment_show', methods: ['GET'])]
    public function show(Payment $payment): Response
    {
        return $this->json($payment, 200, [], ['groups' => ['payments_detail', 'invoices_list']]);
    }

    #[Route('/{id}/edit', name: 'app_payment_edit', methods: ['PUT', 'PATCH'])]
    public function edit(
        Request $request,
        Payment $payment,
        EntityManagerInterface $entityManager,
        PaymentValidator $validator,
        PaymentService $service
    ): Response {
        $data = json_decode($request->getContent(), true);

        $errors = $validator->validate($data);
        if (!empty($errors)) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        if (isset($data['invoice_id'])) {
            $invoice = $entityManager->getRepository(Invoice::class)->find($data['invoiceId']);
            if (!$invoice) {
                return $this->json(['error' => 'Invoice not found.'], Response::HTTP_NOT_FOUND);
            }
            $payment->setInvoice($invoice);
        }

        $payment = $service->createOrUpdatePayment($data, $payment->getInvoice(), $payment);
        $entityManager->flush();

        return $this->json($payment, Response::HTTP_OK);
    }

    #[Route('/{id}/delete', name: 'app_payment_delete', methods: ['DELETE'])]
    public function delete(Payment $payment, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($payment);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
