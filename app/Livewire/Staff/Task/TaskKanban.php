<?php

namespace App\Livewire\Staff\Task;

use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Livewire\Component;

class TaskKanban extends Component
{
    public $filterAssignee = '';
    public $filterPriority = '';
    public $filterType = '';
    
    // For task quick create
    public $newTaskTitle = '';
    public $newTaskStatusId = null;
    
    // For task detail modal
    public $selectedTask = null;
    public $showTaskModal = false;

    protected $listeners = ['taskUpdated' => '$refresh'];

    public function moveTask($taskId, $newStatusId)
    {
        $task = Task::find($taskId);
        $user = auth()->user();
        
        if ($task && ($task->assigned_to === $user->id || $task->reported_by === $user->id || $user->role === 'admin')) {
            $oldStatusId = $task->status_id;
            $task->update(['status_id' => $newStatusId]);
            
            // Also mark completed_at if moved to done status
            $newStatus = TaskStatus::find($newStatusId);
            if ($newStatus && $newStatus->is_done && !$task->completed_at) {
                $task->update(['completed_at' => now()]);
            }
        }
    }

    public function createQuickTask($statusId)
    {
        if (empty($this->newTaskTitle)) return;

        $user = auth()->user();
        
        Task::create([
            'title' => $this->newTaskTitle,
            'status_id' => $statusId,
            'branch_id' => $user->branch_id,
            'assigned_to' => $user->id,
            'reported_by' => $user->id,
            'priority' => 'medium',
            'type' => 'task',
        ]);

        $this->newTaskTitle = '';
        $this->newTaskStatusId = null;
    }

    public function openTaskModal($taskId)
    {
        $this->selectedTask = Task::with(['status', 'assignee', 'reporter', 'project', 'comments.user'])
            ->find($taskId);
        $this->showTaskModal = true;
    }

    public function closeTaskModal()
    {
        $this->selectedTask = null;
        $this->showTaskModal = false;
    }

    public function updateTaskPriority($taskId, $priority)
    {
        $task = Task::find($taskId);
        if ($task) {
            $task->update(['priority' => $priority]);
        }
    }

    public function assignTaskToMe($taskId)
    {
        $task = Task::find($taskId);
        if ($task) {
            $task->update(['assigned_to' => auth()->id()]);
        }
        $this->closeTaskModal();
    }

    public function render()
    {
        $user = auth()->user();
        
        // Get statuses (default ones without project_id)
        $statuses = TaskStatus::whereNull('project_id')
            ->orderBy('order')
            ->get();

        // Build task query
        $taskQuery = Task::with(['status', 'assignee', 'reporter', 'project'])
            ->whereIn('status_id', $statuses->pluck('id'))
            ->where(function($q) use ($user) {
                // Show tasks assigned to user, reported by user, or user is admin
                $q->where('assigned_to', $user->id)
                  ->orWhere('reported_by', $user->id)
                  ->orWhere('branch_id', $user->branch_id);
            });

        // Apply filters
        if ($this->filterAssignee) {
            $taskQuery->where('assigned_to', $this->filterAssignee);
        }
        if ($this->filterPriority) {
            $taskQuery->where('priority', $this->filterPriority);
        }
        if ($this->filterType) {
            $taskQuery->where('type', $this->filterType);
        }

        $tasks = $taskQuery->get();

        // Group tasks by status
        $tasksByStatus = $statuses->mapWithKeys(function ($status) use ($tasks) {
            return [$status->id => $tasks->where('status_id', $status->id)->values()];
        });

        // Stats
        $stats = [
            'total' => $tasks->count(),
            'my_tasks' => $tasks->where('assigned_to', $user->id)->count(),
            'overdue' => $tasks->filter(fn($t) => $t->isOverdue())->count(),
            'due_soon' => $tasks->filter(fn($t) => $t->due_date && $t->due_date->isBetween(now(), now()->addDays(3)) && !$t->status?->is_done)->count(),
        ];

        // Get users for filter
        $users = User::whereIn('role', ['admin', 'pustakawan'])->orderBy('name')->get();

        return view('livewire.staff.task.kanban', [
            'statuses' => $statuses,
            'tasksByStatus' => $tasksByStatus,
            'stats' => $stats,
            'users' => $users,
        ])->extends('staff.layouts.app')->section('content');
    }
}
