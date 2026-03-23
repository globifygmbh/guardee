<?php

namespace App\Services;

use App\Models\ActivityLogModel;

class ActivityLogService
{
    protected ActivityLogModel $activityLogModel;

    public function __construct()
    {
        $this->activityLogModel = new ActivityLogModel();
    }

    /**
     * Log an activity.
     */
    public function log(int $userId, string $action, ?string $entityType = null, ?int $entityId = null, ?array $metadata = null): int
    {
        return $this->activityLogModel->insert([
            'user_id'     => $userId,
            'action'      => $action,
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'metadata'    => $metadata ? json_encode($metadata) : null,
        ]);
    }
}
