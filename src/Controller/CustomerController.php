<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use App\Service\CustomerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Exception;

#[Route('api/customers')]
class CustomerController extends AbstractController
{
    public function __construct(private readonly CustomerService $customerService) {}

    #[Route('/', name: 'app_customer_index', methods: ['GET'])]
    public function index(Request $request, CustomerRepository $customerRepository): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? max((int)$requestData['itemsPerPage'], 1) : 10;
        $page = isset($requestData['page']) ? max((int)$requestData['page'], 1) : 1;

        $customersData = $customerRepository->getAllCustomersByFilter($requestData, $itemsPerPage, $page);

        return $this->json($customersData, Response::HTTP_OK, [], ['groups' => ['customer_detail', 'user_list']]);
    }

    #[Route('/', name: 'app_customer_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        try {
            $this->customerService->createCustomer($requestData);
            return $this->json(['message' => 'Successfully created'], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'app_customer_show', methods: ['GET'])]
    public function show(Customer $customer): JsonResponse
    {
        return $this->json($customer, Response::HTTP_OK, [], ['groups' => ['customer_detail', 'user_list']]);
    }

    #[Route('/{id}', name: 'app_customer_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, Customer $customer): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        try {
            $this->customerService->updateCustomer($customer, $requestData);
            return $this->json(['message' => 'Successfully updated'], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'app_customer_delete', methods: ['DELETE'])]
    public function delete(Customer $customer): JsonResponse
    {
        try {
            $this->customerService->deleteCustomer($customer);
            return $this->json(['message' => 'Successfully deleted'], Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}



