<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class NotificationService
{
    public function __construct(private EntityManagerInterface $entityManager, private RequestCheckerService $requestCheckerService) {}

    public function createNotification(array $data): Notification
    {
        $this->requestCheckerService->check($data, ['user_id', 'message', 'sentAt']);
        $user = $this->findUser($data['user_id']);
        $notification = $this->fillNotificationData(new Notification(), $data, $user);
        $this->entityManager->persist($notification);
        $this->entityManager->flush();
        return $notification;
    }

    public function updateNotification(Notification $notification, array $data): Notification
    {
        $user = isset($data['user_id']) ? $this->findUser($data['user_id']) : $notification->getUser();
        $this->fillNotificationData($notification, $data, $user);
        $this->entityManager->flush();
        return $notification;
    }

    public function deleteNotification(Notification $notification): void
    {
        $this->entityManager->remove($notification);
        $this->entityManager->flush();
    }

    private function fillNotificationData(Notification $notification, array $data, User $user): Notification
    {
        $notification->setUser($user);
        $notification->setMessage($data['message']);
        $notification->setSentAt(new \DateTime($data['sentAt']));
        $notification->setIsRead($data['isRead'] ?? $notification->isRead());
        return $notification;
    }

    private function findUser(int $userId): User
    {
        $user = $this->entityManager->getRepository(User::class)->find($userId);
        if (!$user) {
            throw new BadRequestException('User not found');
        }
        return $user;
    }
}
