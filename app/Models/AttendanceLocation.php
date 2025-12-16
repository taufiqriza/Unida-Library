<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class AttendanceLocation extends Model
{
    protected $fillable = [
        'branch_id',
        'name',
        'address',
        'latitude',
        'longitude',
        'radius_meters',
        'qr_code',
        'is_active',
        'work_start_time',
        'work_end_time',
        'late_tolerance_minutes',
        'created_by',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'radius_meters' => 'integer',
        'is_active' => 'boolean',
        'work_start_time' => 'datetime:H:i',
        'work_end_time' => 'datetime:H:i',
        'late_tolerance_minutes' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->qr_code)) {
                $model->qr_code = 'ATT-' . strtoupper(Str::random(12));
            }
        });
    }

    // Relationships
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'location_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    // Helpers
    public function calculateDistance(float $lat, float $lng): float
    {
        $earthRadius = 6371000; // meters
        
        $latFrom = deg2rad($this->latitude);
        $lngFrom = deg2rad($this->longitude);
        $latTo = deg2rad($lat);
        $lngTo = deg2rad($lng);
        
        $latDelta = $latTo - $latFrom;
        $lngDelta = $lngTo - $lngFrom;
        
        $a = sin($latDelta / 2) ** 2 + cos($latFrom) * cos($latTo) * sin($lngDelta / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return round($earthRadius * $c);
    }

    public function isWithinRadius(float $lat, float $lng): bool
    {
        return $this->calculateDistance($lat, $lng) <= $this->radius_meters;
    }

    public function getQrDataAttribute(): string
    {
        return json_encode([
            'type' => 'attendance',
            'code' => $this->qr_code,
            'location' => $this->name,
        ]);
    }

    public function getTodayAttendanceCount(): int
    {
        return $this->attendances()
            ->whereDate('date', today())
            ->where('type', 'check_in')
            ->count();
    }
}
