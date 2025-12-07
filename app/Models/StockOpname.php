<?php

namespace App\Models;

use App\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockOpname extends Model
{
    use BelongsToBranch;

    protected $fillable = [
        'branch_id', 'user_id', 'code', 'name', 'start_date', 'end_date',
        'status', 'total_items', 'found_items', 'missing_items', 'damaged_items', 'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockOpnameItem::class);
    }

    public function generateCode(): string
    {
        $prefix = 'SO-' . now()->format('Ymd');
        $last = static::withoutGlobalScopes()->where('code', 'like', $prefix . '%')->max('code');
        $number = $last ? (int) substr($last, -4) + 1 : 1;
        return $prefix . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    public function updateCounts(): void
    {
        $this->update([
            'found_items' => $this->items()->where('status', 'found')->count(),
            'missing_items' => $this->items()->where('status', 'missing')->count(),
            'damaged_items' => $this->items()->where('status', 'damaged')->count(),
        ]);
    }
}
