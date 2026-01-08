<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SystemUpdate extends Model
{
    protected $fillable = [
        'title',
        'description', 
        'type',
        'icon',
        'color',
        'target_roles',
        'is_active',
        'is_dismissible',
        'priority',
        'published_at'
    ];

    protected $casts = [
        'target_roles' => 'array',
        'is_active' => 'boolean',
        'is_dismissible' => 'boolean',
        'published_at' => 'datetime'
    ];

    public function dismissedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_dismissed_updates');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('published_at', '<=', now());
    }

    public function scopeForRole($query, $role)
    {
        return $query->where(function($q) use ($role) {
            $q->whereNull('target_roles')
              ->orWhereJsonContains('target_roles', $role);
        });
    }

    public function scopeNotDismissedBy($query, $userId)
    {
        return $query->whereDoesntHave('dismissedByUsers', function($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }
}
