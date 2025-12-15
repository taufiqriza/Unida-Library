<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskChecklist extends Model
{
    protected $fillable = [
        'task_id',
        'content',
        'is_completed',
        'completed_by',
        'completed_at',
        'order',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function completedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function toggle(): void
    {
        $this->is_completed = !$this->is_completed;
        $this->completed_by = $this->is_completed ? auth()->id() : null;
        $this->completed_at = $this->is_completed ? now() : null;
        $this->save();
    }
}
