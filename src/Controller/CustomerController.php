<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use App\Service\CustomerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/customer')]
class CustomerController extends AbstractController
{
    public function __construct(private CustomerService $customerService) {}

    #[Route('/', name: 'app_customer_index', methods: ['GET'])]
    public function index(CustomerRepository $repository): JsonResponse
    {
        $customers = $repository->findAll();
        return $this->json($customers, JsonResponse::HTTP_OK);
    }

    #[Route('/collection', name: 'app_customer_collection', methods: ['GET'])]
    public function getCollection(Request $request, CustomerRepository $customerRepository): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? max((int)$requestData['itemsPerPage'], 1) : 10;
        $page = isset($requestData['page']) ? max((int)$requestData['page'], 1) : 1;

        $customersData = $customerRepository->getAllCustomersByFilter($requestData, $itemsPerPage, $page);

        return $this->json(
            $customersData,
            JsonResponse::HTTP_OK,
            [],
            ['groups' => ['customer_detail', 'user_list']]
        );
    }

    #[Route('/create', name: 'app_customer_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $customer = $this->customerService->createCustomer($requestData);
        return $this->json($customer, JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_customer_show', methods: ['GET'])]
    public function show(Customer $customer): JsonResponse
    {
        return $this->json($customer, JsonResponse::HTTP_OK);
    }

    #[Route('/{id}/edit', name: 'app_customer_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, Customer $customer): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $updatedCustomer = $this->customerService->updateCustomer($customer, $requestData);
        return $this->json($updatedCustomer, JsonResponse::HTTP_OK);
    }

    #[Route('/{id}/delete', name: 'app_customer_delete', methods: ['DELETE'])]
    public function delete(Customer $customer): JsonResponse
    {
        $this->customerService->deleteCustomer($customer);
        return $this->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
