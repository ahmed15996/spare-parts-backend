<?php

namespace App\Services;

use App\Models\CustomNotification;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class NotificationService
{


    /**
     * Get user notifications in current locale
     */
    public function getUserNotifications(int $userId, int $perPage = 10)
    {
        return CustomNotification::where('notifiable_id', $userId)
            ->where('notifiable_type', User::class)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Mark notifications as read
     */
    public function markAsRead(int $userId, array $notificationIds): int
    {
        return CustomNotification::where('notifiable_id', $userId)
            ->where('notifiable_type', User::class)
            ->whereIn('id', $notificationIds)
            ->update(['is_read' => true]);
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadCount(int $userId): int
    {
        return CustomNotification::where('notifiable_id', $userId)
            ->where('notifiable_type', User::class)
            ->where('is_read', false)
            ->count();
    }
}
