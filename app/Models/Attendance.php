<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'location_id',
        'branch_id',
        'date',
        'type',
        'scanned_at',
        'scheduled_time',
        'actual_time',
        'latitude',
        'longitude',
        'distance_meters',
        'verification_method',
        'is_late',
        'late_minutes',
        'is_verified',
        'notes',
        'device_info',
    ];

    protected $casts = [
        'date' => 'date',
        'scanned_at' => 'datetime',
        'scheduled_time' => 'datetime:H:i',
        'actual_time' => 'datetime:H:i',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'distance_meters' => 'integer',
        'is_late' => 'boolean',
        'late_minutes' => 'integer',
        'is_verified' => 'boolean',
        'device_info' => 'array',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(AttendanceLocation::class, 'location_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    // Scopes
    public function scopeCheckIn($query)
    {
        return $query->where('type', 'check_in');
    }

    public function scopeCheckOut($query)
    {
        return $query->where('type', 'check_out');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    // Mutators
    public function setScannedAtAttribute($value)
    {
        $this->attributes['scanned_at'] = $value;
        $this->attributes['actual_time'] = Carbon::parse($value)->format('H:i:s');
        $this->attributes['date'] = Carbon::parse($value)->format('Y-m-d');
    }

    // Helpers
    public static function checkInToday($userId): ?self
    {
        return static::where('user_id', $userId)
            ->whereDate('date', today())
            ->where('type', 'check_in')
            ->first();
    }

    public static function checkOutToday($userId): ?self
    {
        return static::where('user_id', $userId)
            ->whereDate('date', today())
            ->where('type', 'check_out')
            ->first();
    }

    public function getDurationAttribute(): ?string
    {
        if ($this->type !== 'check_in') {
            return null;
        }

        $checkOut = static::where('user_id', $this->user_id)
            ->whereDate('date', $this->date)
            ->where('type', 'check_out')
            ->first();

        if (!$checkOut) {
            // Calculate from check-in to now
            $diff = Carbon::parse($this->scanned_at)->diff(now());
            return $diff->format('%hj %im');
        }

        $diff = Carbon::parse($this->scanned_at)->diff($checkOut->scanned_at);
        return $diff->format('%hj %im');
    }

    public function getStatusColorAttribute(): string
    {
        if ($this->is_late) {
            return 'amber';
        }
        return 'emerald';
    }

    public function getVerificationIconAttribute(): string
    {
        return match($this->verification_method) {
            'qr_scan' => 'fa-qrcode',
            'location_select' => 'fa-map-marker-alt',
            'manual' => 'fa-hand-pointer',
            default => 'fa-check',
        };
    }
}
