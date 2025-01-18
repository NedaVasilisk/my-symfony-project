<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Exception;

#[Route('api/notifications')]
class NotificationController extends AbstractController
{
    public function __construct(private readonly NotificationService $notificationService) {}

    #[Route('/', name: 'app_notification_index', methods: ['GET'])]
    public function index(Request $request, NotificationRepository $notificationRepository): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? max((int)$requestData['itemsPerPage'], 1) : 10;
        $page = isset($requestData['page']) ? max((int)$requestData['page'], 1) : 1;

        $notificationsData = $notificationRepository->getAllNotificationsByFilter($requestData, $itemsPerPage, $page);

        return $this->json($notificationsData, Response::HTTP_OK, [], ['groups' => ['notifications_detail', 'user_list']]);
    }

    #[Route('/', name: 'app_notification_create', methods: ['POST'])]
    public function create(Request $request, UserRepository $userRepository): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        if (!isset($requestData['user_id'])) {
            return $this->json(['error' => 'Missing required field: userId'], Response::HTTP_BAD_REQUEST);
        }

        $user = $userRepository->find($requestData['user_id']);
        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        $requestData['user'] = $user;

        try {
            return $this->json(['message' => 'Successfully created'], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'app_notification_show', methods: ['GET'])]
    public function show(Notification $notification): JsonResponse
    {
        return $this->json($notification, Response::HTTP_OK, [], ['groups' => ['notifications_detail', 'user_list']]);
    }

    #[Route('/{id}', name: 'app_notification_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, UserRepository $userRepository): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        if (isset($requestData['user_id'])) {
            $user = $userRepository->find($requestData['user_id']);
            if (!$user) {
                throw new NotFoundHttpException('User not found');
            }
            $requestData['user'] = $user;
        }

        try {
            return $this->json(['message' => 'Successfully updated'], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'app_notification_delete', methods: ['DELETE'])]
    public function delete(Notification $notification): JsonResponse
    {
        try {
            $this->notificationService->deleteNotification($notification);
            return $this->json(['message' => 'Successfully deleted'], Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
