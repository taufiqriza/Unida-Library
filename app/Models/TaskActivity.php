<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskActivity extends Model
{
    protected $fillable = ['task_id', 'user_id', 'action', 'field', 'old_value', 'new_value'];

    public function task(): BelongsTo { return $this->belongsTo(Task::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public function getDescriptionAttribute(): string
    {
        $user = $this->user?->name ?? 'System';
        return match($this->action) {
            'created' => "{$user} membuat task ini",
            'status_changed' => "{$user} mengubah status dari {$this->old_value} ke {$this->new_value}",
            'assigned' => "{$user} mengassign ke {$this->new_value}",
            'commented' => "{$user} menambahkan komentar",
            default => "{$user} melakukan {$this->action}",
        };
    }
}
