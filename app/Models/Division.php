<?php

namespace App\Models;

use App\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Division extends Model
{
    use BelongsToBranch;

    protected $fillable = [
        'branch_id', 'name', 'code', 'description', 'color', 'head_id', 'is_active'
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }
    public function head(): BelongsTo { return $this->belongsTo(User::class, 'head_id'); }
    public function users(): BelongsToMany { return $this->belongsToMany(User::class)->withPivot('role'); }
    public function projects(): HasMany { return $this->hasMany(Project::class); }
    public function tasks(): HasMany { return $this->hasMany(Task::class); }
    public function taskTemplates(): HasMany { return $this->hasMany(TaskTemplate::class); }
}
