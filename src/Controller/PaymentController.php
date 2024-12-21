<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Form\PaymentType;
use App\Repository\PaymentRepository;
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
        return $this->json($payments);
    }

    #[Route('/create', name: 'app_payment_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $payment = new Payment();
        $payment->setPaymentDate(new \DateTime($data['paymentDate'] ?? 'now'));
        $payment->setAmount($data['amount'] ?? 0.0);
        $payment->setPaymentMethod($data['paymentMethod'] ?? 'Unknown');

        $entityManager->persist($payment);
        $entityManager->flush();

        return $this->json($payment, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_payment_show', methods: ['GET'])]
    public function show(Payment $payment): Response
    {
        return $this->json($payment);
    }

    #[Route('/{id}/edit', name: 'app_payment_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, Payment $payment, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['paymentDate'])) {
            $payment->setPaymentDate(new \DateTime($data['paymentDate']));
        }
        if (isset($data['amount'])) {
            $payment->setAmount($data['amount']);
        }
        if (isset($data['paymentMethod'])) {
            $payment->setPaymentMethod($data['paymentMethod']);
        }

        $entityManager->flush();

        return $this->json($payment);
    }

    #[Route('/{id}/delete', name: 'app_payment_delete', methods: ['DELETE'])]
    public function delete(Payment $payment, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($payment);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
