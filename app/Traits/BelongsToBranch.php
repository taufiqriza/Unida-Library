<?php

namespace App\Traits;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToBranch
{
    public static function bootBelongsToBranch(): void
    {
        // Auto-filter by branch for non-super-admin (only for admin users, not members)
        static::addGlobalScope('branch', function (Builder $builder) {
            $user = auth('web')->user();
            
            // Only apply branch filter for admin users (not member login)
            if ($user instanceof User) {
                if (!$user->isSuperAdmin()) {
                    $builder->where('branch_id', $user->branch_id);
                } elseif (session()->has('current_branch_id')) {
                    $builder->where('branch_id', session('current_branch_id'));
                }
            }
        });

        // Auto-set branch_id on create (only for admin context)
        static::creating(function ($model) {
            if (empty($model->branch_id)) {
                $user = auth('web')->user();
                
                if ($user instanceof User) {
                    $model->branch_id = $user->getCurrentBranchId() 
                        ?? Branch::where('is_main', true)->first()?->id 
                        ?? 1;
                } else {
                    // For member registration or other contexts
                    $model->branch_id = Branch::where('is_main', true)->first()?->id ?? 1;
                }
            }
        });
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
