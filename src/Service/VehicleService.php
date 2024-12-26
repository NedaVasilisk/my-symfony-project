<?php

namespace App\Service;

use App\Entity\Customer;
use App\Entity\Vehicle;
use App\Repository\CustomerRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\Validator\Constraints as Assert;

class VehicleService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CustomerRepository $customerRepository
    ) {}

    public function createVehicle(Vehicle $vehicle): Vehicle
    {
        $this->validateCustomer($vehicle);
        $this->entityManager->persist($vehicle);
        $this->entityManager->flush();

        return $vehicle;
    }

    public function updateVehicle(Vehicle $vehicle): Vehicle
    {
        $this->validateCustomer($vehicle);
        $this->entityManager->flush();

        return $vehicle;
    }

    public function deleteVehicle(Vehicle $vehicle): void
    {
        $this->entityManager->remove($vehicle);
        $this->entityManager->flush();
    }

    private function validateCustomer(Vehicle $vehicle): void
    {
        $customer = $vehicle->getCustomer();
        if (!$customer || !$this->customerRepository->find($customer->getId())) {
            throw new BadRequestException('Invalid or missing customer.');
        }
    }
}

