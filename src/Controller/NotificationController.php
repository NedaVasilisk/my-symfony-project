<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/notification')]
class NotificationController extends AbstractController
{
    public function __construct(private NotificationService $notificationService) {}

    #[Route('/', name: 'app_notification_index', methods: ['GET'])]
    public function index(NotificationRepository $repository): JsonResponse
    {
        $notifications = $repository->findAll();
        return $this->json($notifications, Response::HTTP_OK, [], ['groups' => ['notifications_detail', 'user_list']]);
    }

    #[Route('/collection', name: 'app_notification_collection', methods: ['GET'])]
    public function getCollection(Request $request, NotificationRepository $notificationRepository): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? max((int)$requestData['itemsPerPage'], 1) : 10;
        $page = isset($requestData['page']) ? max((int)$requestData['page'], 1) : 1;

        $notificationsData = $notificationRepository->getAllNotificationsByFilter($requestData, $itemsPerPage, $page);

        return $this->json(
            $notificationsData,
            JsonResponse::HTTP_OK,
            [],
            ['groups' => ['notifications_detail', 'user_list']]
        );
    }

    #[Route('/create', name: 'app_notification_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $notification = $this->notificationService->createNotification($requestData);
        return $this->json($notification, JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_notification_show', methods: ['GET'])]
    public function show(Notification $notification): JsonResponse
    {
        return $this->json($notification, Response::HTTP_OK, [], ['groups' => ['notifications_detail', 'user_list']]);
    }

    #[Route('/{id}/edit', name: 'app_notification_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, Notification $notification): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $updatedNotification = $this->notificationService->updateNotification($notification, $requestData);
        return $this->json($updatedNotification, JsonResponse::HTTP_OK);
    }

    #[Route('/{id}/delete', name: 'app_notification_delete', methods: ['DELETE'])]
    public function delete(Notification $notification): JsonResponse
    {
        $this->notificationService->deleteNotification($notification);
        return $this->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
