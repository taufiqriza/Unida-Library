<?php

namespace App\Services;

use App\Models\StaffNotification;
use App\Models\NotificationPreference;
use App\Models\User;
use App\Models\Member;
use Illuminate\Support\Str;

class NotificationService
{
    /**
     * Send a notification to a user/member
     */
    public function send(
        $notifiable,
        string $category,
        string $title,
        string $body,
        array $options = []
    ): StaffNotification {
        $notification = StaffNotification::create([
            'id' => Str::uuid(),
            'type' => $options['type'] ?? 'App\\Notifications\\GeneralNotification',
            'notifiable_type' => get_class($notifiable),
            'notifiable_id' => $notifiable->id,
            'category' => $category,
            'priority' => $options['priority'] ?? 'normal',
            'title' => $title,
            'body' => $body,
            'action_url' => $options['action_url'] ?? null,
            'action_label' => $options['action_label'] ?? null,
            'icon' => $options['icon'] ?? null,
            'color' => $options['color'] ?? null,
            'image_url' => $options['image_url'] ?? null,
            'data' => $options['data'] ?? null,
            'channels_sent' => ['database'],
        ]);

        // TODO: Send to other channels (email, whatsapp, push)
        // based on user preferences
        
        return $notification;
    }

    /**
     * Send notification to multiple users
     */
    public function sendToMany(
        iterable $notifiables,
        string $category,
        string $title,
        string $body,
        array $options = []
    ): int {
        $count = 0;
        foreach ($notifiables as $notifiable) {
            $this->send($notifiable, $category, $title, $body, $options);
            $count++;
        }
        return $count;
    }

    /**
     * Send to all staff members
     */
    public function sendToAllStaff(
        string $category,
        string $title,
        string $body,
        array $options = []
    ): int {
        $staff = User::whereIn('role', ['super_admin', 'admin', 'pustakawan'])->get();
        return $this->sendToMany($staff, $category, $title, $body, $options);
    }

    /**
     * Send to staff in specific branch
     */
    public function sendToBranch(
        int $branchId,
        string $category,
        string $title,
        string $body,
        array $options = []
    ): int {
        $staff = User::where('branch_id', $branchId)
            ->whereIn('role', ['admin', 'pustakawan'])
            ->get();
        return $this->sendToMany($staff, $category, $title, $body, $options);
    }

    /**
     * Get unread count for a notifiable
     */
    public function getUnreadCount($notifiable): int
    {
        return StaffNotification::forUser($notifiable->id)
            ->unread()
            ->count();
    }

    /**
     * Get notifications for a notifiable
     */
    public function getNotifications($notifiable, int $limit = 20, ?string $category = null)
    {
        $query = StaffNotification::forUser($notifiable->id)
            ->recent();
        
        if ($category) {
            $query->category($category);
        }
        
        return $query->take($limit)->get();
    }

    /**
     * Get grouped notifications (today, yesterday, this week, older)
     */
    public function getGroupedNotifications($notifiable, int $limit = 50): array
    {
        $notifications = $this->getNotifications($notifiable, $limit);
        
        $grouped = [
            'today' => [],
            'yesterday' => [],
            'this_week' => [],
            'older' => [],
        ];

        foreach ($notifications as $notification) {
            if ($notification->created_at->isToday()) {
                $grouped['today'][] = $notification;
            } elseif ($notification->created_at->isYesterday()) {
                $grouped['yesterday'][] = $notification;
            } elseif ($notification->created_at->isCurrentWeek()) {
                $grouped['this_week'][] = $notification;
            } else {
                $grouped['older'][] = $notification;
            }
        }

        return $grouped;
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(string $notificationId): void
    {
        $notification = StaffNotification::find($notificationId);
        $notification?->markAsRead();
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead($notifiable): int
    {
        return StaffNotification::forUser($notifiable->id)
            ->unread()
            ->update(['read_at' => now()]);
    }

    /**
     * Delete old notifications
     */
    public function deleteOld(int $daysOld = 90): int
    {
        return StaffNotification::where('created_at', '<', now()->subDays($daysOld))
            ->delete();
    }

    // ============================================
    // Quick notification helpers
    // ============================================

    public function notifyTaskAssigned(User $user, $task): StaffNotification
    {
        return $this->send($user, 'task', 
            'Tugas Baru Ditugaskan',
            "Anda ditugaskan untuk: {$task->title}",
            [
                'priority' => 'normal',
                'action_url' => route('staff.task.index'),
                'action_label' => 'Lihat Tugas',
                'icon' => 'fa-clipboard-list',
                'data' => ['task_id' => $task->id],
            ]
        );
    }

    public function notifyTaskDue(User $user, $task): StaffNotification
    {
        return $this->send($user, 'task',
            'Tugas Akan Jatuh Tempo',
            "Tugas \"{$task->title}\" jatuh tempo besok!",
            [
                'priority' => 'high',
                'action_url' => route('staff.task.index'),
                'action_label' => 'Lihat Tugas',
                'icon' => 'fa-clock',
                'data' => ['task_id' => $task->id],
            ]
        );
    }

    public function notifyTaskComment(User $user, $task, $commenter): StaffNotification
    {
        return $this->send($user, 'task',
            'Komentar Baru di Tugas',
            "{$commenter->name} mengomentari tugas \"{$task->title}\"",
            [
                'priority' => 'normal',
                'action_url' => route('staff.task.index'),
                'action_label' => 'Lihat Komentar',
                'icon' => 'fa-comment',
                'data' => ['task_id' => $task->id],
            ]
        );
    }

    public function notifyLoanDue($member, $loan): StaffNotification
    {
        return $this->send($member, 'loan',
            'Buku Akan Jatuh Tempo',
            "Buku \"{$loan->item->book->title}\" jatuh tempo pada {$loan->due_date->format('d M Y')}",
            [
                'priority' => 'high',
                'action_url' => null,
                'icon' => 'fa-book',
                'data' => ['loan_id' => $loan->id],
            ]
        );
    }

    public function notifyAnnouncement($notifiable, string $title, string $message): StaffNotification
    {
        return $this->send($notifiable, 'announcement', $title, $message, [
            'priority' => 'normal',
            'icon' => 'fa-bullhorn',
        ]);
    }

    public function notifySystem($notifiable, string $title, string $message): StaffNotification
    {
        return $this->send($notifiable, 'system', $title, $message, [
            'priority' => 'low',
            'icon' => 'fa-info-circle',
        ]);
    }
}
