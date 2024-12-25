<?php

namespace App\Service;

use App\Entity\Customer;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class CustomerService
{
    public function __construct(private EntityManagerInterface $entityManager, private RequestCheckerService $requestCheckerService) {}

    public function createCustomer(array $data): Customer
    {
        $this->requestCheckerService->check($data, ['firstName', 'lastName', 'phone', 'email']);
        $user = isset($data['user_id']) ? $this->findUser($data['user_id']) : null;
        $customer = $this->fillCustomerData(new Customer(), $data, $user);
        $this->entityManager->persist($customer);
        $this->entityManager->flush();
        return $customer;
    }

    public function updateCustomer(Customer $customer, array $data): Customer
    {
        $user = isset($data['user_id']) ? $this->findUser($data['user_id']) : $customer->getUser();
        $this->fillCustomerData($customer, $data, $user);
        $this->entityManager->flush();
        return $customer;
    }

    public function deleteCustomer(Customer $customer): void
    {
        $this->entityManager->remove($customer);
        $this->entityManager->flush();
    }

    private function fillCustomerData(Customer $customer, array $data, ?User $user): Customer
    {
        $customer->setUser($user);
        $customer->setFirstName($data['firstName']);
        $customer->setLastName($data['lastName']);
        $customer->setPhone($data['phone']);
        $customer->setEmail($data['email']);
        $customer->setAddress($data['address'] ?? $customer->getAddress());
        return $customer;
    }

    private function findUser(int $userId): User
    {
        $user = $this->entityManager->getRepository(User::class)->find($userId);
        if (!$user) {
            throw new BadRequestException('User not found');
        }
        return $user;
    }
}
