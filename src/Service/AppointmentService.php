<?php

namespace App\Service;

use App\Entity\Appointment;
use App\Entity\Customer;
use App\Entity\Vehicle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class AppointmentService
{
    public function __construct(private EntityManagerInterface $entityManager, private RequestCheckerService $requestCheckerService) {}

    public function createAppointment(array $data): Appointment
    {
        $this->requestCheckerService->check($data, ['customer_id', 'vehicle_id', 'scheduledDate', 'status']);
        $customer = $this->findCustomer($data['customer_id']);
        $vehicle = $this->findVehicle($data['vehicle_id']);
        $appointment = $this->fillAppointmentData(new Appointment(), $data, $customer, $vehicle);
        $this->entityManager->persist($appointment);
        $this->entityManager->flush();
        return $appointment;
    }

    public function updateAppointment(Appointment $appointment, array $data): Appointment
    {
        $customer = isset($data['customer_id']) ? $this->findCustomer($data['customer_id']) : $appointment->getCustomer();
        $vehicle = isset($data['vehicle_id']) ? $this->findVehicle($data['vehicle_id']) : $appointment->getVehicle();
        $this->fillAppointmentData($appointment, $data, $customer, $vehicle);
        $this->entityManager->flush();
        return $appointment;
    }

    public function deleteAppointment(Appointment $appointment): void
    {
        $this->entityManager->remove($appointment);
        $this->entityManager->flush();
    }

    private function fillAppointmentData(Appointment $appointment, array $data, Customer $customer, Vehicle $vehicle): Appointment
    {
        $appointment->setCustomer($customer);
        $appointment->setVehicle($vehicle);
        $appointment->setScheduledDate(new \DateTime($data['scheduledDate']));
        $appointment->setStatus($data['status']);
        return $appointment;
    }

    private function findCustomer(int $customerId): Customer
    {
        $customer = $this->entityManager->getRepository(Customer::class)->find($customerId);
        if (!$customer) {
            throw new BadRequestException('Customer not found');
        }
        return $customer;
    }

    private function findVehicle(int $vehicleId): Vehicle
    {
        $vehicle = $this->entityManager->getRepository(Vehicle::class)->find($vehicleId);
        if (!$vehicle) {
            throw new BadRequestException('Vehicle not found');
        }
        return $vehicle;
    }
}
