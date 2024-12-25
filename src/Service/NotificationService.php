<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\User;

class NotificationService
{
    public function createOrUpdateNotification(
        array         $data,
        User          $user,
        ?Notification $notification = null
    ): Notification
    {
        if (!$notification) {
            $notification = new Notification();
        }

        $notification->setUser($user);
        $notification->setMessage($data['message']);
        $notification->setSentAt(
            isset($data['sentAt']) ? new \DateTime($data['sentAt']) : $notification->getSentAt()
        );
        $notification->setIsRead($data['isRead'] ?? false);

        return $notification;
    }
}
