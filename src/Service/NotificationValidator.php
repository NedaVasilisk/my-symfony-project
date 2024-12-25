<?php

namespace App\Service;

class NotificationValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['userId'])) {
            $errors[] = 'User ID is required.';
        }

        if (empty($data['message'])) {
            $errors[] = 'Notification message is required.';
        }

        return $errors;
    }
}
