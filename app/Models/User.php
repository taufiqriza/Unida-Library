<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    const ROLE_SUPER_ADMIN = 'super_admin';
    const ROLE_ADMIN = 'admin';
    const ROLE_LIBRARIAN = 'librarian';
    const ROLE_STAFF = 'staff';

    public static function getRoles(): array
    {
        return [
            self::ROLE_SUPER_ADMIN => 'Super Admin',
            self::ROLE_ADMIN => 'Admin Cabang',
            self::ROLE_LIBRARIAN => 'Pustakawan',
            self::ROLE_STAFF => 'Staff',
        ];
    }

    protected $fillable = ['name', 'email', 'password', 'branch_id', 'role', 'is_active', 'is_online', 'last_seen_at', 'photo', 'status', 'approved_by', 'approved_at', 'rejection_reason'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_online' => 'boolean',
            'last_seen_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN]);
    }

    public function isLibrarian(): bool
    {
        return in_array($this->role, [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN, self::ROLE_LIBRARIAN]);
    }

    public function canManageBranch(): bool
    {
        return $this->isAdmin();
    }

    public function canManageUsers(): bool
    {
        return $this->isAdmin();
    }

    public function canAccessReports(): bool
    {
        return $this->isLibrarian();
    }

    // Get current working branch (for super admin who can switch)
    public function getCurrentBranchId(): ?int
    {
        if ($this->isSuperAdmin()) {
            return session('current_branch_id');
        }
        return $this->branch_id;
    }

    public function getRoleLabel(): string
    {
        return self::getRoles()[$this->role] ?? $this->role;
    }

    /**
     * Check if user is really online (last seen within 5 minutes)
     */
    public function isReallyOnline(): bool
    {
        if (!$this->last_seen_at) {
            return false;
        }
        
        return $this->last_seen_at->diffInMinutes(now()) < 5;
    }

    /**
     * Get online status text
     */
    public function getOnlineStatusText(): string
    {
        if ($this->isReallyOnline()) {
            return 'Online';
        }
        
        if ($this->last_seen_at) {
            return $this->last_seen_at->diffForHumans();
        }
        
        return 'Tidak aktif';
    }

    /**
     * Get the user's avatar URL
     * Returns real photo if available, otherwise generates initials-based avatar
     */
    public function getAvatarUrl(int $size = 100): string
    {
        // Check if user has a real photo
        if ($this->photo) {
            // Check if it's a full URL (e.g., from Google OAuth)
            if (str_starts_with($this->photo, 'http://') || str_starts_with($this->photo, 'https://')) {
                return $this->photo;
            }
            // Local storage photo
            return asset('storage/' . $this->photo);
        }

        // Generate initials-based avatar
        return $this->getInitialsAvatarUrl($size);
    }

    /**
     * Get initials from name
     */
    public function getInitials(): string
    {
        $words = explode(' ', $this->name);
        $initials = '';
        
        foreach ($words as $index => $word) {
            if ($index < 2 && !empty($word)) { // Max 2 initials
                $initials .= strtoupper(mb_substr($word, 0, 1));
            }
        }
        
        return $initials ?: '?';
    }

    /**
     * Generate a consistent color based on name (for avatar background)
     */
    public function getAvatarColor(): string
    {
        $colors = [
            '3b82f6', // blue
            '22c55e', // green
            'f59e0b', // amber
            'ef4444', // red
            '8b5cf6', // violet
            'ec4899', // pink
            '14b8a6', // teal
            'f97316', // orange
            '6366f1', // indigo
            '06b6d4', // cyan
        ];
        
        $hash = crc32($this->name);
        return $colors[abs($hash) % count($colors)];
    }

    /**
     * Get initials-based avatar URL (using ui-avatars.com)
     */
    public function getInitialsAvatarUrl(int $size = 100): string
    {
        $initials = $this->getInitials();
        $color = $this->getAvatarColor();
        
        return "https://ui-avatars.com/api/?name=" . urlencode($initials) 
            . "&size={$size}&background={$color}&color=ffffff&bold=true&format=svg";
    }
}
