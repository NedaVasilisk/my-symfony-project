<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\Customer;
use App\Entity\Vehicle;
use App\Form\AppointmentType;
use App\Repository\AppointmentRepository;
use App\Repository\CustomerRepository;
use App\Repository\VehicleRepository;
use App\Service\AppointmentService;
use App\Service\AppointmentValidator;
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
        AppointmentValidator $validator,
        AppointmentService $service
    ): Response {
        $data = json_decode($request->getContent(), true);

        $errors = $validator->validate($data);
        if (!empty($errors)) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $customer = $entityManager->getRepository(Customer::class)->find($data['customerId']);
        if (!$customer) {
            return $this->json(['error' => 'Customer not found.'], Response::HTTP_NOT_FOUND);
        }

        $vehicle = $entityManager->getRepository(Vehicle::class)->find($data['vehicleId']);
        if (!$vehicle) {
            return $this->json(['error' => 'Vehicle not found.'], Response::HTTP_NOT_FOUND);
        }

        $appointment = $service->createOrUpdateAppointment($data, $customer, $vehicle);
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
        AppointmentValidator $validator,
        AppointmentService $service
    ): Response {
        $data = json_decode($request->getContent(), true);

        $errors = $validator->validate($data);
        if (!empty($errors)) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        if (isset($data['customer_id'])) {
            $customer = $entityManager->getRepository(Customer::class)->find($data['customerId']);
            if (!$customer) {
                return $this->json(['error' => 'Customer not found.'], Response::HTTP_NOT_FOUND);
            }
            $appointment->setCustomer($customer);
        }

        if (isset($data['vehicle_id'])) {
            $vehicle = $entityManager->getRepository(Vehicle::class)->find($data['vehicleId']);
            if (!$vehicle) {
                return $this->json(['error' => 'Vehicle not found.'], Response::HTTP_NOT_FOUND);
            }
            $appointment->setVehicle($vehicle);
        }

        $appointment = $service->createOrUpdateAppointment($data, $appointment->getCustomer(), $appointment->getVehicle(), $appointment);
        $entityManager->flush();

        return $this->json($appointment, Response::HTTP_OK);
    }

    #[Route('/{id}/delete', name: 'app_appointment_delete', methods: ['DELETE'])]
    public function delete(Appointment $appointment, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($appointment);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
