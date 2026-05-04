<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    /**
     * Send notification to single user
     */
    public static function send(
        $userId,
        $title,
        $message,
        $type = 'info',
        $showFrom = null,
        $showUntil = null
    ) {
        return Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'show_from' => $showFrom ?? now(),
            'show_until' => $showUntil ?? now()->addHours(2),
        ]);
    }

    /**
     * Send notification to multiple users
     */
    public static function sendToMany($userIds, $title, $message, $type = 'info')
    {
        foreach ($userIds as $id) {
            self::send($id, $title, $message, $type);
        }
    }

    /**
     * Get active notifications
     */
    public static function getActive($userId)
    {
        return Notification::where('user_id', $userId)
            ->where(function ($q) {
                $q->whereNull('show_from')
                  ->orWhere('show_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('show_until')
                  ->orWhere('show_until', '>=', now());
            })
            ->latest()
            ->get();
    }

    /**
     * Mark as read
     */
    public static function markAsRead($id, $userId)
    {
        return Notification::where('id', $id)
            ->where('user_id', $userId)
            ->update(['is_read' => true]);
    }
}
