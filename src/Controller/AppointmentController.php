<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Form\AppointmentType;
use App\Repository\AppointmentRepository;
use App\Repository\CustomerRepository;
use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/appointment')]
class AppointmentController extends AbstractController
{
    #[Route('/', name: 'app_appointment_index', methods: ['GET'])]
    public function index(AppointmentRepository $appointmentRepository): Response
    {
        $appointments = $appointmentRepository->findAll();
        return $this->json($appointments, 200, [], ['groups' => ['appointment_detail', 'customer_list', 'vehicle_list']]);
    }

    #[Route('/create', name: 'app_appointment_new', methods: ['POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        CustomerRepository $customerRepository,
        VehicleRepository $vehicleRepository
    ): Response {
        $data = json_decode($request->getContent(), true);

        if (empty($data['customerId'])) {
            return $this->json(['error' => 'Missing customerId'], Response::HTTP_BAD_REQUEST);
        }
        $customer = $customerRepository->find($data['customerId']);
        if (!$customer) {
            return $this->json(['error' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }

        if (empty($data['vehicleId'])) {
            return $this->json(['error' => 'Missing vehicleId'], Response::HTTP_BAD_REQUEST);
        }
        $vehicle = $vehicleRepository->find($data['vehicleId']);
        if (!$vehicle) {
            return $this->json(['error' => 'Vehicle not found'], Response::HTTP_NOT_FOUND);
        }

        $appointment = new Appointment();
        $appointment
            ->setCustomer($customer)
            ->setVehicle($vehicle)
            ->setScheduledDate(new \DateTime($data['scheduledDate'] ?? 'now'))
            ->setStatus($data['status'] ?? 'Pending');

        $entityManager->persist($appointment);
        $entityManager->flush();

        return $this->json($appointment, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_appointment_show', methods: ['GET'])]
    public function show(Appointment $appointment): Response
    {
        return $this->json($appointment, 200, [], ['groups' => ['appointment_detail', 'customer_list', 'vehicle_list']]);
    }

    #[Route('/{id}/edit', name: 'app_appointment_edit', methods: ['PUT', 'PATCH'])]
    public function edit(
        Request $request,
        Appointment $appointment,
        EntityManagerInterface $entityManager,
        CustomerRepository $customerRepository,
        VehicleRepository $vehicleRepository
    ): Response {
        $data = json_decode($request->getContent(), true);

        if (isset($data['customerId'])) {
            $customer = $customerRepository->find($data['customerId']);
            if (!$customer) {
                return $this->json(['error' => 'Customer not found'], Response::HTTP_NOT_FOUND);
            }
            $appointment->setCustomer($customer);
        }

        if (isset($data['vehicleId'])) {
            $vehicle = $vehicleRepository->find($data['vehicleId']);
            if (!$vehicle) {
                return $this->json(['error' => 'Vehicle not found'], Response::HTTP_NOT_FOUND);
            }
            $appointment->setVehicle($vehicle);
        }

        if (isset($data['scheduledDate'])) {
            $appointment->setScheduledDate(new \DateTime($data['scheduledDate']));
        }
        if (isset($data['status'])) {
            $appointment->setStatus($data['status']);
        }

        $entityManager->flush();
        return $this->json($appointment);
    }

    #[Route('/{id}/delete', name: 'app_appointment_delete', methods: ['DELETE'])]
    public function delete(Appointment $appointment, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($appointment);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
