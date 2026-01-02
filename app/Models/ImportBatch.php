<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportBatch extends Model
{
    protected $fillable = [
        'branch_id', 'user_id', 'type', 'filename', 'covers_file',
        'total_rows', 'success_count', 'warning_count', 'error_count',
        'status', 'preview_data', 'error_log', 'completed_at',
    ];

    protected $casts = [
        'preview_data' => 'array',
        'error_log' => 'array',
        'completed_at' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getValidCountAttribute(): int
    {
        return $this->total_rows - $this->warning_count - $this->error_count;
    }
}
