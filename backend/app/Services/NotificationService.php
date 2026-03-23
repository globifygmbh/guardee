<?php

namespace App\Services;

use App\Models\NotificationModel;

class NotificationService
{
    protected NotificationModel $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
    }

    /**
     * Create a new notification for a user.
     */
    public function createNotification(int $userId, string $type, string $title, string $message, ?string $link = null): int
    {
        return $this->notificationModel->insert([
            'user_id' => $userId,
            'type'    => $type,
            'title'   => $title,
            'message' => $message,
            'link'    => $link,
            'is_read' => 0,
        ]);
    }

    /**
     * Send an email using CI4's email service.
     */
    public function sendEmail(string $to, string $subject, string $body): bool
    {
        $email = service('email');

        $email->setTo($to);
        $email->setSubject($subject);
        $email->setMessage($body);

        return $email->send(false);
    }
}
