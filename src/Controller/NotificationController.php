<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Form\NotificationType;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/notification')]
class NotificationController extends AbstractController
{
    #[Route('/', name: 'app_notification_index', methods: ['GET'])]
    public function index(NotificationRepository $notificationRepository): Response
    {
        $notifications = $notificationRepository->findAll();
        return $this->json($notifications);
    }

    #[Route('/new', name: 'app_notification_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $notification = new Notification();
        $notification->setMessage($data['message'] ?? '')
            ->setSentAt(new \DateTime($data['sentAt'] ?? 'now'))
            ->setIsRead($data['isRead'] ?? false);

        $entityManager->persist($notification);
        $entityManager->flush();

        return $this->json($notification, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_notification_show', methods: ['GET'])]
    public function show(Notification $notification): Response
    {
        return $this->json($notification);
    }
    #[Route('/{id}/edit', name: 'app_notification_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, Notification $notification, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['message'])) $notification->setMessage($data['message']);
        if (isset($data['sentAt'])) $notification->setSentAt(new \DateTime($data['sentAt']));
        if (isset($data['isRead'])) $notification->setIsRead($data['isRead']);

        $entityManager->flush();

        return $this->json($notification);
    }

    #[Route('/{id}/delete', name: 'app_notification_delete', methods: ['DELETE'])]
    public function delete(Notification $notification, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($notification);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
