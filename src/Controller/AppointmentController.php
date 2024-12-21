<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Form\AppointmentType;
use App\Repository\AppointmentRepository;
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
        return $this->json($appointments);
    }

    #[Route('/create', name: 'app_appointment_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $appointment = new Appointment();
        $appointment->setScheduledDate(new \DateTime($data['scheduledDate'] ?? 'now'))
            ->setStatus($data['status'] ?? 'Pending');

        $entityManager->persist($appointment);
        $entityManager->flush();

        return $this->json($appointment, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_appointment_show', methods: ['GET'])]
    public function show(Appointment $appointment): Response
    {
        return $this->json($appointment);
    }

    #[Route('/{id}/edit', name: 'app_appointment_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, Appointment $appointment, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

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
