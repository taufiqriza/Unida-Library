<?php

namespace App\Livewire\Staff\Task;

use App\Models\Task;
use App\Models\StaffSchedule;
use App\Models\Branch;
use Livewire\Component;
use Carbon\Carbon;

class TaskTimeline extends Component
{
    public $filterBranch = '';
    public $timelineZoom = 'week'; // day, week, month
    
    // For task detail modal
    public $selectedTask = null;
    public $showTaskModal = false;

    public function mount()
    {
        $user = auth()->user();
        if (!in_array($user->role, ['super_admin'])) {
            $this->filterBranch = $user->branch_id;
        }
    }

    public function openTaskModal($taskId)
    {
        $this->selectedTask = Task::with(['assignee', 'creator', 'status', 'comments.user', 'checklists', 'activities.user'])->find($taskId);
        $this->showTaskModal = true;
    }

    public function closeTaskModal()
    {
        $this->showTaskModal = false;
        $this->selectedTask = null;
    }

    public function render()
    {
        $user = auth()->user();
        $isSuperAdmin = $user->role === 'super_admin';
        
        // Timeline date range
        $timelineDays = match($this->timelineZoom) {
            'day' => 7,
            'week' => 14,
            'month' => 30,
            default => 14
        };
        
        $timelineStart = now()->startOfDay();
        $timelineEnd = $timelineStart->copy()->addDays($timelineDays - 1);
        
        // Get tasks with dates
        $tasksQuery = Task::with(['assignee', 'status'])
            ->where(function($q) {
                $q->whereNotNull('due_date')->orWhereNotNull('start_date');
            })
            ->whereHas('status', fn($q) => $q->where('is_completed', false));
        
        // Apply branch filter
        if ($this->filterBranch) {
            $tasksQuery->where('branch_id', $this->filterBranch);
        } elseif (!$isSuperAdmin) {
            $tasksQuery->where(function($q) use ($user) {
                $q->where('branch_id', $user->branch_id)
                  ->orWhere('assigned_to', $user->id)
                  ->orWhere('created_by', $user->id);
            });
        }
        
        $tasks = $tasksQuery->orderBy('due_date')->get();
        
        // Get schedules
        $schedulesQuery = StaffSchedule::with(['user', 'branch'])
            ->whereBetween('schedule_date', [$timelineStart, $timelineEnd])
            ->whereIn('status', ['scheduled', 'ongoing']);
        
        if ($this->filterBranch) {
            $schedulesQuery->where('branch_id', $this->filterBranch);
        } elseif (!$isSuperAdmin) {
            $schedulesQuery->where(function($q) use ($user) {
                $q->where('branch_id', $user->branch_id)
                  ->orWhere('user_id', $user->id);
            });
        }
        
        $schedules = $schedulesQuery->orderBy('schedule_date')->get();
        
        return view('livewire.staff.task.timeline', [
            'tasks' => $tasks,
            'schedules' => $schedules,
            'branches' => Branch::where('is_active', true)->get(),
            'timelineStart' => $timelineStart,
            'timelineEnd' => $timelineEnd,
            'timelineDays' => $timelineDays,
        ]);
    }
}
