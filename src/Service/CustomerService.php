<?php

namespace App\Service;

use App\Entity\Customer;
use App\Entity\User;

class CustomerService
{
    public function createOrUpdateCustomer(array $data, ?User $user = null, ?Customer $customer = null): Customer
    {
        if (!$customer) {
            $customer = new Customer();
        }

        if ($user !== null) {
            $customer->setUser($user);
        }

        $customer->setFirstName($data['firstName'] ?? '');
        $customer->setLastName($data['lastName'] ?? '');
        $customer->setPhone($data['phone'] ?? '');
        $customer->setEmail($data['email'] ?? null);
        $customer->setAddress($data['address'] ?? null);

        return $customer;
    }
}
