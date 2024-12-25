<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\User;
use App\Form\NotificationType;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use App\Service\NotificationService;
use App\Service\NotificationValidator;
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
        NotificationValidator $validator,
        NotificationService $service
    ): Response {
        $data = json_decode($request->getContent(), true);

        $errors = $validator->validate($data);
        if (!empty($errors)) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $user = $entityManager->getRepository(User::class)->find($data['userId']);
        if (!$user) {
            return $this->json(['error' => 'User not found.'], Response::HTTP_NOT_FOUND);
        }

        $notification = $service->createOrUpdateNotification($data, $user);
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
        NotificationValidator $validator,
        NotificationService $service
    ): Response {
        $data = json_decode($request->getContent(), true);

        $errors = $validator->validate($data);
        if (!empty($errors)) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        if (isset($data['user_id'])) {
            $user = $entityManager->getRepository(User::class)->find($data['userId']);
            if (!$user) {
                return $this->json(['error' => 'User not found.'], Response::HTTP_NOT_FOUND);
            }
            $notification->setUser($user);
        }

        $notification = $service->createOrUpdateNotification($data, $notification->getUser(), $notification);
        $entityManager->flush();

        return $this->json($notification, Response::HTTP_OK);
    }

    #[Route('/{id}/delete', name: 'app_notification_delete', methods: ['DELETE'])]
    public function delete(Notification $notification, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($notification);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
