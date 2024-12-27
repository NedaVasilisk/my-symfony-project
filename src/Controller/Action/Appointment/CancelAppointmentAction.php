<?php

namespace App\Controller\Action\Appointment;

use App\Entity\Appointment;
use App\Service\AppointmentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

#[AsController]
class CancelAppointmentAction extends AbstractController
{
    #[Route(
        path: '/api/appointment/{id}/cancel',
        name: 'app_appointment_cancel',
        methods: ['PATCH', 'POST']
    )]
    public function __invoke(
        Appointment        $appointment,
        Request            $request,
        AppointmentService $appointmentService
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?: [];

        $data['status'] = 'Cancelled';


        $updatedAppointment = $appointmentService->updateAppointment($appointment, $data);

        return $this->json(
            $updatedAppointment,
            Response::HTTP_OK,
            [],
            ['groups' => ['appointment_detail', 'customer_list', 'vehicle_list']]
        );
    }
}
