<?php

namespace App\Livewire\Staff\Task;

use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\StaffSchedule;
use App\Models\PersonalNote;
use App\Models\Branch;
use Livewire\Component;
use Carbon\Carbon;

class TaskTimeline extends Component
{
    public $filterBranch = '';
    public $timelineZoom = 'week'; // day, week, month
    public $showCompleted = false;
    
    // For task detail modal
    public $selectedTask = null;
    public $showTaskModal = false;
    
    // For note preview modal
    public $selectedNote = null;
    public $showNoteModal = false;

    public function mount()
    {
        $user = auth()->user();
        if (!in_array($user->role, ['super_admin'])) {
            $this->filterBranch = $user->branch_id;
        }
    }

    public function openTaskModal($taskId)
    {
        $this->selectedTask = Task::with(['assignee', 'reporter', 'status', 'comments.user', 'checklists', 'activities.user'])->find($taskId);
        $this->showTaskModal = true;
    }

    public function closeTaskModal()
    {
        $this->showTaskModal = false;
        $this->selectedTask = null;
    }
    
    public function openNoteModal($noteId)
    {
        $this->selectedNote = PersonalNote::with('user')->find($noteId);
        $this->showNoteModal = true;
    }

    public function closeNoteModal()
    {
        $this->showNoteModal = false;
        $this->selectedNote = null;
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
        
        // Get ALL active tasks (not just those with dates)
        $tasksQuery = Task::with(['assignee', 'status', 'reporter', 'branch']);
        
        // Filter by completion status
        if (!$this->showCompleted) {
            $tasksQuery->whereHas('status', fn($q) => $q->where('is_done', false));
        }
        
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
        
        $allTasks = $tasksQuery->orderBy('due_date')->get();
        
        // Separate tasks with dates (for Gantt) and without dates (for backlog)
        $tasksWithDates = $allTasks->filter(fn($t) => $t->due_date || $t->start_date);
        $tasksWithoutDates = $allTasks->filter(fn($t) => !$t->due_date && !$t->start_date);
        
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
        
        // Get personal notes (for timeline integration)
        $notesQuery = PersonalNote::with('user')
            ->where(function($q) use ($user) {
                // User's own notes or public notes
                $q->where('user_id', $user->id)
                  ->orWhere('is_public', true);
            })
            ->latest()
            ->limit(10);
        
        $recentNotes = $notesQuery->get();
        
        // Get pinned notes
        $pinnedNotes = PersonalNote::where('user_id', $user->id)
            ->where('is_pinned', true)
            ->orderByDesc('pinned_at')
            ->limit(5)
            ->get();
        
        // Stats
        $stats = [
            'total_tasks' => $allTasks->count(),
            'my_tasks' => $allTasks->where('assigned_to', $user->id)->count(),
            'overdue' => $allTasks->filter(fn($t) => $t->isOverdue())->count(),
            'due_soon' => $allTasks->filter(fn($t) => $t->due_date && $t->due_date->isBetween(now(), now()->addDays(3)) && !$t->status?->is_done)->count(),
            'no_date' => $tasksWithoutDates->count(),
            'schedules_today' => $schedules->filter(fn($s) => Carbon::parse($s->schedule_date)->isToday())->count(),
            'my_notes' => PersonalNote::where('user_id', $user->id)->count(),
        ];
        
        return view('livewire.staff.task.timeline', [
            'tasks' => $tasksWithDates,
            'tasksWithoutDates' => $tasksWithoutDates,
            'allTasks' => $allTasks,
            'schedules' => $schedules,
            'branches' => Branch::where('is_active', true)->get(),
            'timelineStart' => $timelineStart,
            'timelineEnd' => $timelineEnd,
            'timelineDays' => $timelineDays,
            'stats' => $stats,
            'recentNotes' => $recentNotes,
            'pinnedNotes' => $pinnedNotes,
            'isSuperAdmin' => $isSuperAdmin,
        ])->extends('staff.layouts.app')->section('content');
    }
}
