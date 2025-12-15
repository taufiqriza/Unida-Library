<?php

namespace App\Observers;

use App\Models\Task;
use App\Services\NotificationService;

class TaskObserver
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        // Notify assignee if task is assigned to someone else
        if ($task->assigned_to && $task->assigned_to !== auth()->id()) {
            $this->notificationService->send(
                $task->assignee,
                'task',
                'Tugas Baru Ditugaskan',
                "Anda ditugaskan untuk: {$task->title}",
                [
                    'priority' => $task->priority === 'urgent' ? 'urgent' : 'normal',
                    'action_url' => route('staff.task.index'),
                    'action_label' => 'Lihat Tugas',
                    'icon' => 'fa-clipboard-list',
                    'data' => ['task_id' => $task->id],
                ]
            );
        }
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        // Notify new assignee when task is reassigned
        if ($task->wasChanged('assigned_to') && $task->assigned_to) {
            // Don't notify if assigning to self
            if ($task->assigned_to !== auth()->id()) {
                $this->notificationService->send(
                    $task->assignee,
                    'task',
                    'Tugas Ditugaskan kepada Anda',
                    "Anda sekarang ditugaskan untuk: {$task->title}",
                    [
                        'priority' => 'normal',
                        'action_url' => route('staff.task.index'),
                        'action_label' => 'Lihat Tugas',
                        'icon' => 'fa-user-check',
                        'data' => ['task_id' => $task->id],
                    ]
                );
            }
        }

        // Notify reporter when status changed to done
        if ($task->wasChanged('status_id') && $task->status?->is_done) {
            if ($task->reported_by && $task->reported_by !== auth()->id()) {
                $this->notificationService->send(
                    $task->reporter,
                    'task',
                    'Tugas Selesai',
                    "Tugas \"{$task->title}\" telah diselesaikan oleh {$task->assignee?->name}",
                    [
                        'priority' => 'low',
                        'action_url' => route('staff.task.index'),
                        'action_label' => 'Lihat Tugas',
                        'icon' => 'fa-check-circle',
                        'data' => ['task_id' => $task->id],
                    ]
                );
            }
        }
    }
}
