<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskStatus extends Model
{
    protected $fillable = ['project_id', 'name', 'slug', 'color', 'order', 'is_default', 'is_done'];

    protected $casts = ['is_default' => 'boolean', 'is_done' => 'boolean'];

    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function tasks(): HasMany { return $this->hasMany(Task::class, 'status_id'); }

    public static function getDefaultStatuses(): array
    {
        return [
            ['name' => 'Backlog', 'slug' => 'backlog', 'color' => '#6b7280', 'order' => 1, 'is_default' => true],
            ['name' => 'To Do', 'slug' => 'todo', 'color' => '#3b82f6', 'order' => 2],
            ['name' => 'In Progress', 'slug' => 'in_progress', 'color' => '#f59e0b', 'order' => 3],
            ['name' => 'Review', 'slug' => 'review', 'color' => '#8b5cf6', 'order' => 4],
            ['name' => 'Done', 'slug' => 'done', 'color' => '#10b981', 'order' => 5, 'is_done' => true],
        ];
    }
}
