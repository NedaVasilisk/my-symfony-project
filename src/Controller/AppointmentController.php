<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Repository\AppointmentRepository;
use App\Service\AppointmentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Exception;

#[Route('api/appointments')]
class AppointmentController extends AbstractController
{
    public function __construct(private readonly AppointmentService $appointmentService) {}

    #[Route('/', name: 'app_appointment_index', methods: ['GET'])]
    public function index(Request $request, AppointmentRepository $appointmentRepository): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? max((int)$requestData['itemsPerPage'], 1) : 10;
        $page = isset($requestData['page']) ? max((int)$requestData['page'], 1) : 1;

        $appointmentsData = $appointmentRepository->getAllAppointmentsByFilter($requestData, $itemsPerPage, $page);

        return $this->json($appointmentsData, Response::HTTP_OK, [], ['groups' => ['appointment_detail', 'customer_list', 'vehicle_list']]);
    }

    #[Route('/', name: 'app_appointment_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        try {
            $this->appointmentService->createAppointment($requestData);
            return $this->json(['message' => 'Successfully created'], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'app_appointment_show', methods: ['GET'])]
    public function show(Appointment $appointment): JsonResponse
    {
        return $this->json($appointment, Response::HTTP_OK, [], ['groups' => ['appointment_detail', 'customer_list', 'vehicle_list']]);
    }

    #[Route('/{id}', name: 'app_appointment_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, Appointment $appointment): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        try {
            $this->appointmentService->updateAppointment($appointment, $requestData);
            return $this->json(['message' => 'Successfully updated'], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'app_appointment_delete', methods: ['DELETE'])]
    public function delete(Appointment $appointment): JsonResponse
    {
        try {
            $this->appointmentService->deleteAppointment($appointment);
            return $this->json(['message' => 'Successfully deleted'], Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}

