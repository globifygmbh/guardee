<?php

namespace App\Controllers\Api;

use App\Models\NotificationModel;

class NotificationController extends BaseApiController
{
    protected NotificationModel $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
    }

    /**
     * GET /api/notifications
     * Get user notifications.
     */
    public function index()
    {
        $notifications = $this->notificationModel
            ->where('user_id', $this->currentUserId())
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return $this->jsonSuccess($notifications);
    }

    /**
     * PUT /api/notifications/$id/read
     * Mark a notification as read.
     */
    public function read(int $id)
    {
        $notification = $this->notificationModel->find($id);

        if (!$notification) {
            return $this->jsonError('Notification not found.', 404);
        }

        if ((int) $notification['user_id'] !== $this->currentUserId()) {
            return $this->jsonError('You are not authorized to update this notification.', 403);
        }

        $this->notificationModel->update($id, [
            'is_read' => 1,
            'read_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->jsonSuccess($this->notificationModel->find($id));
    }

    /**
     * GET /api/notifications/unread-count
     * Get count of unread notifications.
     */
    public function unreadCount()
    {
        $count = $this->notificationModel
            ->where('user_id', $this->currentUserId())
            ->where('is_read', 0)
            ->countAllResults();

        return $this->jsonSuccess(['count' => $count]);
    }
}
