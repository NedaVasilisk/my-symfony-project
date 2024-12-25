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
        private RequestCheckerService $requestCheckerService,
        private CustomerRepository $customerRepository
    ) {}

    public function createVehicle(array $data): Vehicle
    {
        $this->requestCheckerService->check($data, ['customer_id', 'vin', 'licensePlate', 'make', 'model', 'year']);
        $customer = $this->findCustomer($data['customer_id']);
        $vehicle = $this->createOrUpdateVehicle(new Vehicle(), $data, $customer);
        $this->entityManager->persist($vehicle);
        $this->entityManager->flush();
        return $vehicle;
    }

    public function updateVehicle(Vehicle $vehicle, array $data): Vehicle
    {
        $customer = isset($data['customer_id']) ? $this->findCustomer($data['customer_id']) : $vehicle->getCustomer();
        $this->createOrUpdateVehicle($vehicle, $data, $customer);
        $this->entityManager->flush();
        return $vehicle;
    }

    public function deleteVehicle(Vehicle $vehicle): void
    {
        $this->entityManager->remove($vehicle);
        $this->entityManager->flush();
    }

    private function createOrUpdateVehicle(Vehicle $vehicle, array $data, Customer $customer): Vehicle
    {
        $vehicle->setCustomer($customer);
        $vehicle->setVin($data['vin'] ?? $vehicle->getVin());
        $vehicle->setLicensePlate($data['licensePlate'] ?? $vehicle->getLicensePlate());
        $vehicle->setMake($data['make'] ?? $vehicle->getMake());
        $vehicle->setModel($data['model'] ?? $vehicle->getModel());
        $vehicle->setYear($data['year'] ?? $vehicle->getYear());
        $vehicle->setEngineType($data['engineType'] ?? $vehicle->getEngineType());
        $vehicle->setBatteryCapacity($data['batteryCapacity'] ?? $vehicle->getBatteryCapacity());
        $vehicle->setLastIotUpdate(isset($data['lastIotUpdate']) ? new \DateTime($data['lastIotUpdate']) : $vehicle->getLastIotUpdate());
        return $vehicle;
    }

    private function findCustomer(int $customerId): Customer
    {
        $customer = $this->customerRepository->find($customerId);
        if (!$customer) {
            throw new BadRequestException('Customer not found');
        }
        return $customer;
    }
}

