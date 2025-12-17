<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'branch_id',
        'action',
        'module',
        'description',
        'loggable_type',
        'loggable_id',
        'properties',
        'metadata',
        'level',
    ];

    protected $casts = [
        'properties' => 'array',
        'metadata' => 'array',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function loggable(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeByModule($query, $module)
    {
        return $query->where('module', $module);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Helper methods
    public static function log(
        string $action,
        string $module,
        string $description,
        ?Model $loggable = null,
        array $properties = [],
        string $level = 'info'
    ): self {
        $user = auth()->user();
        
        return self::create([
            'user_id' => $user?->id,
            'branch_id' => $user?->branch_id,
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'loggable_type' => $loggable ? get_class($loggable) : null,
            'loggable_id' => $loggable?->id,
            'properties' => $properties,
            'metadata' => [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ],
            'level' => $level,
        ]);
    }

    // Action constants
    public const ACTION_CREATE = 'create';
    public const ACTION_UPDATE = 'update';
    public const ACTION_DELETE = 'delete';
    public const ACTION_LOGIN = 'login';
    public const ACTION_LOGOUT = 'logout';
    public const ACTION_VIEW = 'view';
    public const ACTION_EXPORT = 'export';
    public const ACTION_IMPORT = 'import';
    public const ACTION_APPROVE = 'approve';
    public const ACTION_REJECT = 'reject';

    // Module constants
    public const MODULE_AUTH = 'auth';
    public const MODULE_USER = 'user';
    public const MODULE_BIBLIO = 'biblio';
    public const MODULE_MEMBER = 'member';
    public const MODULE_CIRCULATION = 'circulation';
    public const MODULE_ATTENDANCE = 'attendance';
    public const MODULE_ELIBRARY = 'elibrary';
    public const MODULE_SETTINGS = 'settings';

    // Helper for icon & color
    public function getActionIcon(): string
    {
        return match($this->action) {
            'create' => 'fa-plus',
            'update' => 'fa-pen',
            'delete' => 'fa-trash',
            'login' => 'fa-sign-in-alt',
            'logout' => 'fa-sign-out-alt',
            'view' => 'fa-eye',
            'export' => 'fa-download',
            'import' => 'fa-upload',
            'approve' => 'fa-check',
            'reject' => 'fa-times',
            default => 'fa-circle',
        };
    }

    public function getActionColor(): string
    {
        return match($this->action) {
            'create' => 'emerald',
            'update' => 'blue',
            'delete' => 'red',
            'login' => 'violet',
            'logout' => 'gray',
            'approve' => 'green',
            'reject' => 'rose',
            default => 'slate',
        };
    }

    public function getLevelColor(): string
    {
        return match($this->level) {
            'info' => 'blue',
            'warning' => 'amber',
            'error' => 'red',
            'critical' => 'rose',
            default => 'gray',
        };
    }

    public function getModuleIcon(): string
    {
        return match($this->module) {
            'auth' => 'fa-key',
            'user' => 'fa-users',
            'biblio' => 'fa-book',
            'member' => 'fa-id-card',
            'circulation' => 'fa-exchange-alt',
            'attendance' => 'fa-fingerprint',
            'elibrary' => 'fa-cloud',
            'settings' => 'fa-cog',
            default => 'fa-folder',
        };
    }
}
