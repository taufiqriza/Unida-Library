<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskComment extends Model
{
    protected $fillable = ['task_id', 'user_id', 'content', 'parent_id'];

    public function task(): BelongsTo { return $this->belongsTo(Task::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function parent(): BelongsTo { return $this->belongsTo(TaskComment::class, 'parent_id'); }
    public function replies(): HasMany { return $this->hasMany(TaskComment::class, 'parent_id'); }

    protected static function boot()
    {
        parent::boot();
        static::created(function ($comment) {
            $comment->task->logActivity('commented', null, null, substr($comment->content, 0, 100));
        });
    }
}
