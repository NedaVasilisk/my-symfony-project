<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Form\CustomerType;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/customer')]
class CustomerController extends AbstractController
{
    #[Route('/', name: 'app_customer_index', methods: ['GET'])]
    public function index(CustomerRepository $customerRepository): Response
    {
        $customers = $customerRepository->findAll();
        return $this->json($customers);
    }

    #[Route('/create', name: 'app_customer_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $customer = new Customer();
        $customer->setFirstName($data['firstName'] ?? '')
            ->setLastName($data['lastName'] ?? '')
            ->setPhone($data['phone'] ?? '')
            ->setEmail($data['email'] ?? '')
            ->setAddress($data['address'] ?? null);

        $entityManager->persist($customer);
        $entityManager->flush();

        return $this->json($customer, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_customer_show', methods: ['GET'])]
    public function show(Customer $customer): Response
    {
        return $this->json($customer);
    }


    #[Route('/{id}/edit', name: 'app_customer_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, Customer $customer, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['firstName'])) $customer->setFirstName($data['firstName']);
        if (isset($data['lastName'])) $customer->setLastName($data['lastName']);
        if (isset($data['phone'])) $customer->setPhone($data['phone']);
        if (isset($data['email'])) $customer->setEmail($data['email']);
        if (isset($data['address'])) $customer->setAddress($data['address']);

        $entityManager->flush();

        return $this->json($customer);
    }

    #[Route('/{id}/delete', name: 'app_customer_delete', methods: ['DELETE'])]
    public function delete(Customer $customer, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($customer);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
