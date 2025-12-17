<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StaffSchedule extends Model
{
    protected $fillable = [
        'branch_id',
        'user_id',
        'created_by',
        'task_id',
        'title',
        'description',
        'type',
        'location',
        'schedule_date',
        'start_time',
        'end_time',
        'shift',
        'is_recurring',
        'recurrence_pattern',
        'recurrence_days',
        'recurrence_end_date',
        'status',
        'notes',
        'swap_requested_by',
        'swap_approved_by',
        'swap_approved_at',
    ];

    protected $casts = [
        'schedule_date' => 'date',
        'recurrence_end_date' => 'date',
        'recurrence_days' => 'array',
        'is_recurring' => 'boolean',
        'swap_approved_at' => 'datetime',
    ];

    // Type labels
    public static function getTypes(): array
    {
        return [
            'piket' => ['label' => 'Piket', 'icon' => 'fa-clipboard-user', 'color' => 'blue'],
            'shift' => ['label' => 'Shift Kerja', 'icon' => 'fa-clock', 'color' => 'indigo'],
            'penempatan' => ['label' => 'Penempatan', 'icon' => 'fa-map-marker-alt', 'color' => 'emerald'],
            'tugas_rutin' => ['label' => 'Tugas Rutin', 'icon' => 'fa-repeat', 'color' => 'violet'],
            'rapat' => ['label' => 'Rapat', 'icon' => 'fa-users', 'color' => 'amber'],
            'pelatihan' => ['label' => 'Pelatihan', 'icon' => 'fa-graduation-cap', 'color' => 'cyan'],
            'cuti' => ['label' => 'Cuti/Izin', 'icon' => 'fa-calendar-xmark', 'color' => 'rose'],
            'lainnya' => ['label' => 'Lainnya', 'icon' => 'fa-ellipsis', 'color' => 'gray'],
        ];
    }

    // Location labels
    public static function getLocations(): array
    {
        return [
            'sirkulasi' => ['label' => 'Meja Sirkulasi', 'icon' => 'fa-arrows-rotate'],
            'referensi' => ['label' => 'Ruang Referensi', 'icon' => 'fa-book-bookmark'],
            'rak_koleksi' => ['label' => 'Rak Koleksi', 'icon' => 'fa-books'],
            'ruang_baca' => ['label' => 'Ruang Baca', 'icon' => 'fa-book-open-reader'],
            'multimedia' => ['label' => 'Lab Multimedia', 'icon' => 'fa-computer'],
            'administrasi' => ['label' => 'Administrasi', 'icon' => 'fa-file-invoice'],
            'gudang' => ['label' => 'Gudang', 'icon' => 'fa-warehouse'],
            'all' => ['label' => 'Seluruh Area', 'icon' => 'fa-building'],
        ];
    }

    // Shift labels
    public static function getShifts(): array
    {
        return [
            'pagi' => ['label' => 'Pagi', 'time' => '07:00 - 12:00', 'color' => 'amber'],
            'siang' => ['label' => 'Siang', 'time' => '12:00 - 17:00', 'color' => 'orange'],
            'malam' => ['label' => 'Malam', 'time' => '17:00 - 21:00', 'color' => 'indigo'],
            'full' => ['label' => 'Full Day', 'time' => '07:00 - 17:00', 'color' => 'blue'],
        ];
    }

    // Status labels
    public static function getStatuses(): array
    {
        return [
            'scheduled' => ['label' => 'Terjadwal', 'color' => 'blue'],
            'ongoing' => ['label' => 'Berlangsung', 'color' => 'emerald'],
            'completed' => ['label' => 'Selesai', 'color' => 'gray'],
            'cancelled' => ['label' => 'Dibatalkan', 'color' => 'red'],
            'swapped' => ['label' => 'Ditukar', 'color' => 'violet'],
        ];
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function swapRequester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'swap_requested_by');
    }

    public function swapApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'swap_approved_by');
    }

    public function swapRequests(): HasMany
    {
        return $this->hasMany(ScheduleSwapRequest::class, 'schedule_id');
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    // Helpers
    public function getTypeInfo(): array
    {
        return self::getTypes()[$this->type] ?? self::getTypes()['lainnya'];
    }

    public function getLocationInfo(): ?array
    {
        return $this->location ? self::getLocations()[$this->location] : null;
    }

    public function getShiftInfo(): ?array
    {
        return $this->shift ? self::getShifts()[$this->shift] : null;
    }

    public function getStatusInfo(): array
    {
        return self::getStatuses()[$this->status] ?? self::getStatuses()['scheduled'];
    }

    public function isToday(): bool
    {
        return $this->schedule_date->isToday();
    }

    public function isPast(): bool
    {
        return $this->schedule_date->isPast() && !$this->schedule_date->isToday();
    }

    public function isFuture(): bool
    {
        return $this->schedule_date->isFuture();
    }

    public function getTimeRange(): string
    {
        if ($this->shift) {
            return self::getShifts()[$this->shift]['time'] ?? '';
        }
        
        if ($this->start_time && $this->end_time) {
            return substr($this->start_time, 0, 5) . ' - ' . substr($this->end_time, 0, 5);
        }
        
        return '';
    }

    // Scopes
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('schedule_date', $date);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('schedule_date', '>=', now()->startOfDay())
                     ->whereIn('status', ['scheduled', 'ongoing'])
                     ->orderBy('schedule_date');
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('schedule_date', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('schedule_date', now()->month)
                     ->whereYear('schedule_date', now()->year);
    }
}
