<?php

namespace App\Livewire\Staff\Task;

use App\Models\Task;
use App\Models\TaskComment;
use App\Models\TaskChecklist;
use App\Models\TaskStatus;
use App\Models\User;
use App\Models\Branch;
use Livewire\Component;

class TaskKanban extends Component
{
    public $filterAssignee = '';
    public $filterPriority = '';
    public $filterType = '';
    public $filterBranch = '';
    public $searchQuery = '';
    
    // For task quick create
    public $newTaskTitle = '';
    public $newTaskStatusId = null;
    
    // For task detail modal
    public $selectedTask = null;
    public $showTaskModal = false;
    
    // Inline edit fields
    public $editTitle = '';
    public $editDescription = '';
    public $editPriority = '';
    public $editType = '';
    public $editAssignedTo = '';
    public $editDueDate = '';
    public $editingField = null;
    
    // Comments
    public $newComment = '';
    
    // Checklist
    public $newChecklistItem = '';
    
    // Confirmation modal
    public $showDeleteConfirm = false;
    public $taskToDelete = null;

    protected $listeners = ['taskUpdated' => '$refresh'];

    public function mount()
    {
        $user = auth()->user();
        // Default to user's branch unless super_admin
        if (!in_array($user->role, ['super_admin'])) {
            $this->filterBranch = $user->branch_id;
        }
    }

    public function moveTask($taskId, $newStatusId)
    {
        $task = Task::find($taskId);
        $user = auth()->user();
        
        // Check permissions: assigned, reporter, admin, or super_admin
        $canEdit = $task && (
            $task->assigned_to === $user->id || 
            $task->reported_by === $user->id || 
            in_array($user->role, ['admin', 'super_admin'])
        );
        
        if ($canEdit) {
            $task->update(['status_id' => $newStatusId]);
            
            if ($this->selectedTask && $this->selectedTask->id == $taskId) {
                $this->refreshSelectedTask();
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
            'branch_id' => $this->filterBranch ?: $user->branch_id,
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
        $this->selectedTask = Task::with(['status', 'assignee', 'reporter', 'project', 'branch', 'comments.user', 'activities.user', 'subtasks', 'checklists'])
            ->find($taskId);
        
        if ($this->selectedTask) {
            $this->editTitle = $this->selectedTask->title;
            $this->editDescription = $this->selectedTask->description;
            $this->editPriority = $this->selectedTask->priority;
            $this->editType = $this->selectedTask->type;
            $this->editAssignedTo = $this->selectedTask->assigned_to;
            $this->editDueDate = $this->selectedTask->due_date?->format('Y-m-d');
        }
        
        $this->showTaskModal = true;
        $this->editingField = null;
    }

    public function closeTaskModal()
    {
        $this->selectedTask = null;
        $this->showTaskModal = false;
        $this->editingField = null;
        $this->newComment = '';
        $this->newChecklistItem = '';
    }
    
    public function refreshSelectedTask()
    {
        if ($this->selectedTask) {
            $this->selectedTask = Task::with(['status', 'assignee', 'reporter', 'project', 'branch', 'comments.user', 'activities.user', 'subtasks', 'checklists'])
                ->find($this->selectedTask->id);
        }
    }

    public function startEdit($field)
    {
        $this->editingField = $field;
    }

    public function cancelEdit()
    {
        $this->editingField = null;
        if ($this->selectedTask) {
            $this->editTitle = $this->selectedTask->title;
            $this->editDescription = $this->selectedTask->description;
            $this->editPriority = $this->selectedTask->priority;
            $this->editType = $this->selectedTask->type;
            $this->editAssignedTo = $this->selectedTask->assigned_to;
            $this->editDueDate = $this->selectedTask->due_date?->format('Y-m-d');
        }
    }

    public function saveField($field)
    {
        if (!$this->selectedTask) return;
        
        $task = Task::find($this->selectedTask->id);
        if (!$task) return;

        switch ($field) {
            case 'title':
                if (!empty($this->editTitle)) {
                    $task->update(['title' => $this->editTitle]);
                }
                break;
            case 'description':
                $task->update(['description' => $this->editDescription]);
                break;
            case 'priority':
                $task->update(['priority' => $this->editPriority]);
                break;
            case 'type':
                $task->update(['type' => $this->editType]);
                break;
            case 'assigned_to':
                $task->update(['assigned_to' => $this->editAssignedTo ?: null]);
                break;
            case 'due_date':
                $task->update(['due_date' => $this->editDueDate ?: null]);
                break;
        }

        $this->editingField = null;
        $this->refreshSelectedTask();
    }

    public function updateTaskPriority($taskId, $priority)
    {
        $task = Task::find($taskId);
        if ($task) {
            $task->update(['priority' => $priority]);
            $this->refreshSelectedTask();
        }
    }

    public function assignTaskToMe($taskId)
    {
        $task = Task::find($taskId);
        if ($task) {
            $task->update(['assigned_to' => auth()->id()]);
            $this->refreshSelectedTask();
        }
    }
    
    // Comments - using 'content' field as per migration
    public function addComment()
    {
        if (empty(trim($this->newComment)) || !$this->selectedTask) return;
        
        TaskComment::create([
            'task_id' => $this->selectedTask->id,
            'user_id' => auth()->id(),
            'content' => trim($this->newComment),
        ]);
        
        $this->newComment = '';
        $this->refreshSelectedTask();
    }
    
    public function deleteComment($commentId)
    {
        $comment = TaskComment::find($commentId);
        if ($comment && $comment->user_id === auth()->id()) {
            $comment->delete();
            $this->refreshSelectedTask();
        }
    }
    
    // Checklist Methods
    public function addChecklistItem()
    {
        if (empty(trim($this->newChecklistItem)) || !$this->selectedTask) return;
        
        $maxOrder = $this->selectedTask->checklists()->max('order') ?? 0;
        
        TaskChecklist::create([
            'task_id' => $this->selectedTask->id,
            'content' => trim($this->newChecklistItem),
            'order' => $maxOrder + 1,
        ]);
        
        $this->newChecklistItem = '';
        $this->refreshSelectedTask();
    }
    
    public function toggleChecklistItem($checklistId)
    {
        $item = TaskChecklist::find($checklistId);
        if ($item && $item->task_id === $this->selectedTask?->id) {
            $item->toggle();
            $this->refreshSelectedTask();
        }
    }
    
    public function deleteChecklistItem($checklistId)
    {
        $item = TaskChecklist::find($checklistId);
        if ($item && $item->task_id === $this->selectedTask?->id) {
            $item->delete();
            $this->refreshSelectedTask();
        }
    }
    
    // Delete Task
    public function confirmDelete($taskId)
    {
        $this->taskToDelete = $taskId;
        $this->showDeleteConfirm = true;
    }
    
    public function cancelDelete()
    {
        $this->taskToDelete = null;
        $this->showDeleteConfirm = false;
    }
    
    public function deleteTask()
    {
        if (!$this->taskToDelete) return;
        
        $task = Task::find($this->taskToDelete);
        $user = auth()->user();
        
        if ($task && ($task->reported_by === $user->id || in_array($user->role, ['admin', 'super_admin']))) {
            $task->comments()->delete();
            $task->activities()->delete();
            $task->delete();
            
            $this->taskToDelete = null;
            $this->showDeleteConfirm = false;
            $this->closeTaskModal();
        }
    }
    
    // Duplicate Task
    public function duplicateTask($taskId)
    {
        $task = Task::find($taskId);
        if (!$task) return;
        
        $newTask = $task->replicate();
        $newTask->title = '[Copy] ' . $task->title;
        $newTask->completed_at = null;
        $newTask->reported_by = auth()->id();
        $newTask->save();
        
        session()->flash('success', 'Task berhasil diduplikasi');
    }

    public function render()
    {
        $user = auth()->user();
        $isSuperAdmin = $user->role === 'super_admin';
        
        // Get statuses (default ones without project_id)
        $statuses = TaskStatus::whereNull('project_id')
            ->orderBy('order')
            ->get();

        // Build task query
        $taskQuery = Task::with(['status', 'assignee', 'reporter', 'project', 'branch'])
            ->whereIn('status_id', $statuses->pluck('id'));
        
        // Branch filtering
        if ($this->filterBranch) {
            $taskQuery->where('branch_id', $this->filterBranch);
        } elseif (!$isSuperAdmin) {
            // Non-super_admin can only see their branch + tasks assigned to them
            $taskQuery->where(function($q) use ($user) {
                $q->where('branch_id', $user->branch_id)
                  ->orWhere('assigned_to', $user->id)
                  ->orWhere('reported_by', $user->id);
            });
        }

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
        if ($this->searchQuery) {
            $taskQuery->where('title', 'like', '%' . $this->searchQuery . '%');
        }

        $tasks = $taskQuery->latest()->get();

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
            'completed_today' => $tasks->filter(fn($t) => $t->completed_at && $t->completed_at->isToday())->count(),
        ];

        // Get users for filter (same branch or all for super_admin)
        $usersQuery = User::whereIn('role', ['admin', 'super_admin', 'pustakawan'])->orderBy('name');
        if (!$isSuperAdmin && $this->filterBranch) {
            $usersQuery->where('branch_id', $this->filterBranch);
        }
        $users = $usersQuery->get();
        
        // Get branches for filter (only for super_admin)
        $branches = $isSuperAdmin ? Branch::orderBy('name')->get() : collect();

        return view('livewire.staff.task.kanban', [
            'statuses' => $statuses,
            'tasksByStatus' => $tasksByStatus,
            'stats' => $stats,
            'users' => $users,
            'branches' => $branches,
            'isSuperAdmin' => $isSuperAdmin,
        ])->extends('staff.layouts.app')->section('content');
    }
}
