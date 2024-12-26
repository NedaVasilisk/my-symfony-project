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

#[Route('api/appointment')]
class AppointmentController extends AbstractController
{
    public function __construct(private AppointmentService $appointmentService) {}

    #[Route('/', name: 'app_appointment_index', methods: ['GET'])]
    public function index(AppointmentRepository $repository): JsonResponse
    {
        $appointments = $repository->findAll();
        return $this->json($appointments, Response::HTTP_OK, [], ['groups' => ['appointment_detail', 'customer_list', 'vehicle_list']]);
    }

    #[Route('/collection', name: 'app_appointment_collection', methods: ['GET'])]
    public function getCollection(Request $request, AppointmentRepository $appointmentRepository): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? max((int)$requestData['itemsPerPage'], 1) : 10;
        $page = isset($requestData['page']) ? max((int)$requestData['page'], 1) : 1;

        $appointmentsData = $appointmentRepository->getAllAppointmentsByFilter($requestData, $itemsPerPage, $page);

        return $this->json(
            $appointmentsData,
            JsonResponse::HTTP_OK,
            [],
            ['groups' => ['appointment_detail', 'customer_list', 'vehicle_list']]
        );
    }

    #[Route('/create', name: 'app_appointment_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $appointment = $this->appointmentService->createAppointment($requestData);
        return $this->json($appointment, JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_appointment_show', methods: ['GET'])]
    public function show(Appointment $appointment): JsonResponse
    {
        return $this->json($appointment, Response::HTTP_OK, [], ['groups' => ['appointment_detail', 'customer_list', 'vehicle_list']]);
    }

    #[Route('/{id}/edit', name: 'app_appointment_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, Appointment $appointment): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $updatedAppointment = $this->appointmentService->updateAppointment($appointment, $requestData);
        return $this->json($updatedAppointment, JsonResponse::HTTP_OK);
    }

    #[Route('/{id}/delete', name: 'app_appointment_delete', methods: ['DELETE'])]
    public function delete(Appointment $appointment): JsonResponse
    {
        $this->appointmentService->deleteAppointment($appointment);
        return $this->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
