<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\User;
use App\Form\CustomerType;
use App\Repository\CustomerRepository;
use App\Repository\UserRepository;
use App\Service\CustomerService;
use App\Service\CustomerValidator;
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
        return $this->json($customers, 200, [], ['groups' => ['customer_detail', 'user_list']]);
    }

    #[Route('/create', name: 'app_customer_new', methods: ['POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        CustomerValidator $validator,
        CustomerService $service
    ): Response {
        $data = json_decode($request->getContent(), true);

        $errors = $validator->validate($data);
        if (!empty($errors)) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $user = $validator->validateUser($data['userId'] ?? null, $entityManager);
        if ($user === null) {
            return $this->json(['error' => 'User not found.'], Response::HTTP_NOT_FOUND);
        }

        $customer = $service->createOrUpdateCustomer($data, $user);
        $entityManager->persist($customer);
        $entityManager->flush();

        return $this->json($customer, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_customer_show', methods: ['GET'])]
    public function show(Customer $customer): Response
    {
        return $this->json($customer, 200, [], ['groups' => ['customer_detail', 'user_list']]);
    }

    #[Route('/{id}/edit', name: 'app_customer_edit', methods: ['PUT', 'PATCH'])]
    public function edit(
        Request $request,
        Customer $customer,
        EntityManagerInterface $entityManager,
        CustomerValidator $validator,
        CustomerService $service
    ): Response {
        $data = json_decode($request->getContent(), true);

        $errors = $validator->validate($data);
        if (!empty($errors)) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        if (isset($data['user_id'])) {
            $user = $validator->validateUser($data['userId'], $entityManager);
            if ($user === null) {
                return $this->json(['error' => 'User not found.'], Response::HTTP_NOT_FOUND);
            }
            $customer->setUser($user);
        }

        $customer = $service->createOrUpdateCustomer($data, $customer->getUser(), $customer);
        $entityManager->flush();

        return $this->json($customer, Response::HTTP_OK);
    }

    #[Route('/{id}/delete', name: 'app_customer_delete', methods: ['DELETE'])]
    public function delete(Customer $customer, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($customer);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
