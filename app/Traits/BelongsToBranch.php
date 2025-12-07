<?php

namespace App\Traits;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToBranch
{
    public static function bootBelongsToBranch(): void
    {
        // Auto-filter by branch for non-super-admin
        static::addGlobalScope('branch', function (Builder $builder) {
            if (auth()->check() && !auth()->user()->isSuperAdmin()) {
                $builder->where('branch_id', auth()->user()->branch_id);
            } elseif (auth()->check() && session()->has('current_branch_id')) {
                $builder->where('branch_id', session('current_branch_id'));
            }
        });

        // Auto-set branch_id on create
        static::creating(function ($model) {
            if (empty($model->branch_id) && auth()->check()) {
                $model->branch_id = auth()->user()->getCurrentBranchId() 
                    ?? Branch::where('is_main', true)->first()?->id 
                    ?? 1;
            }
        });
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
