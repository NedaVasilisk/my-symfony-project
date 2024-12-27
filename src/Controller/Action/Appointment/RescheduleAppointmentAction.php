<?php

namespace App\Controller\Action\Appointment;

use App\Entity\Appointment;
use App\Service\AppointmentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class RescheduleAppointmentAction extends AbstractController
{

    #[Route(
        path: '/api/appointment/{id}/reschedule',
        name: 'app_appointment_reschedule',
        methods: ['PATCH', 'POST']
    )]
    public function __invoke(
        Appointment        $appointment,
        Request            $request,
        AppointmentService $appointmentService
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['scheduledDate'])) {
            throw new BadRequestException('Параметр "scheduledDate" обязателен для переноса записи.');
        }

        $data['status'] = 'Rescheduled';

        $updatedAppointment = $appointmentService->updateAppointment($appointment, $data);

        return $this->json(
            $updatedAppointment,
            Response::HTTP_OK,
            [],
            ['groups' => ['appointment_detail', 'customer_list', 'vehicle_list']]
        );
    }
}
