<?php

namespace App\Models;

use App\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use BelongsToBranch;

    protected $fillable = [
        'branch_id', 'project_id', 'division_id', 'parent_id', 'title', 'description',
        'status_id', 'priority', 'type', 'tags', 'assigned_to', 'reported_by',
        'due_date', 'start_date', 'completed_at', 'estimated_hours', 'actual_hours'
    ];

    protected $casts = [
        'tags' => 'array',
        'due_date' => 'date',
        'start_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }
    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function division(): BelongsTo { return $this->belongsTo(Division::class); }
    public function status(): BelongsTo { return $this->belongsTo(TaskStatus::class, 'status_id'); }
    public function assignee(): BelongsTo { return $this->belongsTo(User::class, 'assigned_to'); }
    public function reporter(): BelongsTo { return $this->belongsTo(User::class, 'reported_by'); }
    public function parent(): BelongsTo { return $this->belongsTo(Task::class, 'parent_id'); }
    public function subtasks(): HasMany { return $this->hasMany(Task::class, 'parent_id'); }
    public function comments(): HasMany { return $this->hasMany(TaskComment::class)->whereNull('parent_id')->latest(); }
    public function attachments(): HasMany { return $this->hasMany(TaskAttachment::class); }
    public function activities(): HasMany { return $this->hasMany(TaskActivity::class)->latest(); }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && !$this->status?->is_done;
    }

    public function logActivity(string $action, ?string $field = null, $oldValue = null, $newValue = null): void
    {
        $this->activities()->create([
            'user_id' => auth()->id(),
            'action' => $action,
            'field' => $field,
            'old_value' => is_array($oldValue) ? json_encode($oldValue) : $oldValue,
            'new_value' => is_array($newValue) ? json_encode($newValue) : $newValue,
        ]);
    }

    public static function boot()
    {
        parent::boot();
        
        static::created(function ($task) {
            $task->logActivity('created');
        });

        static::updating(function ($task) {
            if ($task->isDirty('status_id')) {
                $oldStatus = TaskStatus::find($task->getOriginal('status_id'));
                $newStatus = TaskStatus::find($task->status_id);
                $task->logActivity('status_changed', 'status', $oldStatus?->name, $newStatus?->name);
                
                if ($newStatus?->is_done && !$task->completed_at) {
                    $task->completed_at = now();
                }
            }
            if ($task->isDirty('assigned_to')) {
                $oldUser = User::find($task->getOriginal('assigned_to'));
                $newUser = User::find($task->assigned_to);
                $task->logActivity('assigned', 'assigned_to', $oldUser?->name, $newUser?->name);
            }
        });
    }

    public function scopeOverdue($query)
    {
        return $query->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->whereHas('status', fn($q) => $q->where('is_done', false));
    }

    public function scopeDueSoon($query, int $days = 3)
    {
        return $query->whereNotNull('due_date')
            ->whereBetween('due_date', [now(), now()->addDays($days)])
            ->whereHas('status', fn($q) => $q->where('is_done', false));
    }
}
