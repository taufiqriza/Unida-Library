<?php

namespace App\Models;

use App\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use BelongsToBranch;

    protected $fillable = [
        'branch_id', 'division_id', 'name', 'code', 'description',
        'status', 'start_date', 'end_date', 'created_by'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }
    public function division(): BelongsTo { return $this->belongsTo(Division::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function tasks(): HasMany { return $this->hasMany(Task::class); }
    public function statuses(): HasMany { return $this->hasMany(TaskStatus::class)->orderBy('order'); }

    public function getTaskCountAttribute(): int { return $this->tasks()->count(); }
    public function getCompletedTaskCountAttribute(): int { return $this->tasks()->whereHas('status', fn($q) => $q->where('is_done', true))->count(); }
}
