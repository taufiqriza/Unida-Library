<?php

namespace App\Filament\Pages;

use App\Models\Task;
use App\Models\TaskStatus;
use Filament\Pages\Page;
use Livewire\Attributes\On;

class TaskBoard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-view-columns';
    protected static ?string $navigationGroup = 'Task Management';
    protected static ?string $navigationLabel = 'Kanban Board';
    protected static ?string $title = 'Task Board';
    protected static ?int $navigationSort = 5;
    protected static string $view = 'filament.pages.task-board';

    public function getHeading(): string
    {
        return '📋 Task Board';
    }

    public function getSubheading(): ?string
    {
        return 'Kelola dan pantau progress task dengan drag & drop';
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $overdueCount = Task::overdue()->count();
        return $overdueCount > 0 ? 'danger' : 'primary';
    }


    public ?int $projectId = null;
    public array $statuses = [];
    public array $tasks = [];

    public function mount(): void
    {
        $this->loadBoard();
    }

    public function loadBoard(): void
    {
        $this->statuses = TaskStatus::query()
            ->when($this->projectId, fn ($q) => $q->where('project_id', $this->projectId))
            ->when(!$this->projectId, fn ($q) => $q->whereNull('project_id'))
            ->orderBy('order')
            ->get()
            ->toArray();

        $this->tasks = Task::query()
            ->when($this->projectId, fn ($q) => $q->where('project_id', $this->projectId))
            ->with(['assignee', 'status', 'project'])
            ->orderByRaw("FIELD(priority, 'urgent', 'high', 'medium', 'low')")
            ->orderBy('due_date')
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('status_id')
            ->toArray();
    }


    public function updatedProjectId(): void
    {
        $this->loadBoard();
    }

    #[On('task-moved')]
    public function moveTask(int $taskId, int $statusId): void
    {
        Task::find($taskId)?->update(['status_id' => $statusId]);
        $this->loadBoard();
    }

    public static function getNavigationBadge(): ?string
    {
        return Task::whereHas('status', fn ($q) => $q->where('is_done', false))->count() ?: null;
    }
}
