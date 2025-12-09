<?php

namespace App\Models;

use App\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskTemplate extends Model
{
    use BelongsToBranch;

    protected $fillable = [
        'branch_id', 'project_id', 'division_id', 'title', 'description', 'priority',
        'default_assignee', 'frequency', 'schedule_time', 'schedule_days', 'schedule_day',
        'due_days', 'is_active', 'last_generated_at'
    ];

    protected $casts = [
        'schedule_days' => 'array',
        'is_active' => 'boolean',
        'last_generated_at' => 'datetime',
    ];

    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }
    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function division(): BelongsTo { return $this->belongsTo(Division::class); }
    public function assignee(): BelongsTo { return $this->belongsTo(User::class, 'default_assignee'); }

    public function shouldGenerateToday(): bool
    {
        if (!$this->is_active) return false;
        
        $today = now();
        
        return match($this->frequency) {
            'daily' => true,
            'weekly' => in_array($today->dayOfWeek, $this->schedule_days ?? [1]),
            'monthly' => $today->day === ($this->schedule_day ?? 1),
            'quarterly' => $today->day === ($this->schedule_day ?? 1) && in_array($today->month, [1, 4, 7, 10]),
            'yearly' => $today->day === ($this->schedule_day ?? 1) && $today->month === 1,
            default => false,
        };
    }

    public function generateTask(): ?Task
    {
        $defaultStatus = TaskStatus::where('project_id', $this->project_id)
            ->where('is_default', true)->first()
            ?? TaskStatus::whereNull('project_id')->where('is_default', true)->first();

        $task = Task::create([
            'branch_id' => $this->branch_id,
            'project_id' => $this->project_id,
            'division_id' => $this->division_id,
            'title' => $this->title . ' - ' . now()->format('d M Y'),
            'description' => $this->description,
            'priority' => $this->priority,
            'status_id' => $defaultStatus?->id,
            'assigned_to' => $this->default_assignee,
            'reported_by' => null,
            'due_date' => now()->addDays($this->due_days),
        ]);

        $this->update(['last_generated_at' => now()]);
        return $task;
    }
}
