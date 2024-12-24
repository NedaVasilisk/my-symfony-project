<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Form\NotificationType;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
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
        return $this->json($notifications, 200, [], ['groups' => ['notifications_detail', 'user_list']]);
    }

    #[Route('/create', name: 'app_notification_new', methods: ['POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository
    ): Response {
        $data = json_decode($request->getContent(), true);

        if (empty($data['userId'])) {
            return $this->json(['error' => 'Missing userId'], Response::HTTP_BAD_REQUEST);
        }
        $user = $userRepository->find($data['userId']);
        if (!$user) {
            return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $notification = new Notification();
        $notification
            ->setUser($user)
            ->setMessage($data['message'] ?? '')
            ->setSentAt(new \DateTime($data['sentAt'] ?? 'now'))
            ->setIsRead($data['isRead'] ?? false);

        $entityManager->persist($notification);
        $entityManager->flush();

        return $this->json($notification, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_notification_show', methods: ['GET'])]
    public function show(Notification $notification): Response
    {
        return $this->json($notification, 200, [], ['groups' => ['notifications_detail', 'user_list']]);
    }
    #[Route('/{id}/edit', name: 'app_notification_edit', methods: ['PUT', 'PATCH'])]
    public function edit(
        Request $request,
        Notification $notification,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository
    ): Response {
        $data = json_decode($request->getContent(), true);

        if (isset($data['userId'])) {
            $user = $userRepository->find($data['userId']);
            if (!$user) {
                return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
            }
            $notification->setUser($user);
        }

        if (isset($data['message'])) {
            $notification->setMessage($data['message']);
        }
        if (isset($data['sentAt'])) {
            $notification->setSentAt(new \DateTime($data['sentAt']));
        }
        if (isset($data['isRead'])) {
            $notification->setIsRead($data['isRead']);
        }

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
