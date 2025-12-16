<?php

namespace App\Livewire\Staff\Attendance;

use App\Models\Attendance;
use App\Models\AttendanceLocation;
use App\Models\Branch;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class AttendancePortal extends Component
{
    use WithPagination;

    // Tab Navigation
    public string $activeTab = 'absen';
    
    // Absen Tab
    public string $scanMode = 'select'; // qr or select
    public ?int $selectedLocationId = null;
    public ?float $currentLat = null;
    public ?float $currentLng = null;
    public ?float $gpsAccuracy = null;
    public bool $gpsLoading = false;
    public ?string $scannedQrCode = null;
    
    // Riwayat Tab
    public ?string $filterDateStart = null;
    public ?string $filterDateEnd = null;
    public ?int $filterUserId = null;
    public ?int $filterLocationId = null;
    public string $filterStatus = 'all'; // all, on_time, late
    
    // Lokasi Tab (Admin)
    public bool $showLocationModal = false;
    public bool $editingLocation = false;
    public ?int $editLocationId = null;
    public array $locationForm = [
        'name' => '',
        'address' => '',
        'latitude' => '',
        'longitude' => '',
        'radius_meters' => 100,
        'work_start_time' => '08:00',
        'work_end_time' => '17:00',
        'late_tolerance_minutes' => 15,
        'is_active' => true,
    ];
    
    // Branch filter (for super admin)
    public ?int $selectedBranchId = null;
    
    protected $queryString = ['activeTab'];

    public function mount()
    {
        $user = auth()->user();
        
        // Set default branch for non-super-admin
        if (!in_array($user->role, ['super_admin'])) {
            $this->selectedBranchId = $user->branch_id;
        }
        
        // Set default date range for history (last 7 days)
        $this->filterDateStart = now()->subDays(7)->format('Y-m-d');
        $this->filterDateEnd = now()->format('Y-m-d');
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function setScanMode($mode)
    {
        $this->scanMode = $mode;
        $this->scannedQrCode = null;
    }

    // ===== GPS & Location =====
    public function updateGps($lat, $lng, $accuracy)
    {
        $this->currentLat = $lat;
        $this->currentLng = $lng;
        $this->gpsAccuracy = $accuracy;
        $this->gpsLoading = false;
    }

    public function handleQrScan($code)
    {
        $this->scannedQrCode = $code;
        
        // Try to decode QR
        $data = json_decode($code, true);
        if ($data && isset($data['code'])) {
            $location = AttendanceLocation::where('qr_code', $data['code'])->active()->first();
            if ($location) {
                $this->selectedLocationId = $location->id;
                $this->dispatch('notify', type: 'success', message: "Lokasi ditemukan: {$location->name}");
            } else {
                $this->dispatch('notify', type: 'error', message: 'QR Code tidak valid atau lokasi tidak aktif');
            }
        } else {
            // Try direct code match
            $location = AttendanceLocation::where('qr_code', $code)->active()->first();
            if ($location) {
                $this->selectedLocationId = $location->id;
                $this->dispatch('notify', type: 'success', message: "Lokasi ditemukan: {$location->name}");
            } else {
                $this->dispatch('notify', type: 'error', message: 'QR Code tidak valid');
            }
        }
    }

    // ===== Check In / Check Out =====
    public function checkIn()
    {
        $user = auth()->user();
        
        // Validate location selected
        if (!$this->selectedLocationId) {
            $this->dispatch('notify', type: 'error', message: 'Pilih lokasi atau scan QR terlebih dahulu');
            return;
        }
        
        // Validate GPS
        if (!$this->currentLat || !$this->currentLng) {
            $this->dispatch('notify', type: 'error', message: 'Lokasi GPS belum tersedia. Izinkan akses lokasi.');
            return;
        }
        
        // Check if already checked in today
        $existing = Attendance::checkInToday($user->id);
        if ($existing) {
            $this->dispatch('notify', type: 'error', message: 'Anda sudah check-in hari ini');
            return;
        }
        
        // Get location and verify GPS
        $location = AttendanceLocation::find($this->selectedLocationId);
        if (!$location) {
            $this->dispatch('notify', type: 'error', message: 'Lokasi tidak ditemukan');
            return;
        }
        
        $distance = $location->calculateDistance($this->currentLat, $this->currentLng);
        $isVerified = $location->isWithinRadius($this->currentLat, $this->currentLng);
        
        // Calculate late status
        $now = Carbon::now();
        $workStart = Carbon::parse($location->work_start_time);
        $lateThreshold = $workStart->copy()->addMinutes($location->late_tolerance_minutes);
        $isLate = $now->format('H:i:s') > $lateThreshold->format('H:i:s');
        $lateMinutes = $isLate ? $now->diffInMinutes($workStart) : 0;
        
        // Create attendance
        Attendance::create([
            'user_id' => $user->id,
            'location_id' => $location->id,
            'branch_id' => $location->branch_id ?? $user->branch_id,
            'type' => 'check_in',
            'scanned_at' => now(),
            'scheduled_time' => $location->work_start_time,
            'latitude' => $this->currentLat,
            'longitude' => $this->currentLng,
            'distance_meters' => $distance,
            'verification_method' => $this->scanMode === 'qr' ? 'qr_scan' : 'location_select',
            'is_late' => $isLate,
            'late_minutes' => $lateMinutes,
            'is_verified' => $isVerified,
            'device_info' => [
                'user_agent' => request()->userAgent(),
                'ip' => request()->ip(),
                'gps_accuracy' => $this->gpsAccuracy,
            ],
        ]);
        
        $message = $isLate 
            ? "Check-in berhasil (Terlambat {$lateMinutes} menit)" 
            : "Check-in berhasil!";
        
        if (!$isVerified) {
            $message .= " ⚠️ Di luar radius ({$distance}m dari lokasi)";
        }
        
        $this->dispatch('notify', type: $isLate ? 'warning' : 'success', message: $message);
        $this->selectedLocationId = null;
    }

    public function checkOut()
    {
        $user = auth()->user();
        
        // Validate GPS
        if (!$this->currentLat || !$this->currentLng) {
            $this->dispatch('notify', type: 'error', message: 'Lokasi GPS belum tersedia');
            return;
        }
        
        // Check if checked in today
        $checkIn = Attendance::checkInToday($user->id);
        if (!$checkIn) {
            $this->dispatch('notify', type: 'error', message: 'Anda belum check-in hari ini');
            return;
        }
        
        // Check if already checked out
        $existing = Attendance::checkOutToday($user->id);
        if ($existing) {
            $this->dispatch('notify', type: 'error', message: 'Anda sudah check-out hari ini');
            return;
        }
        
        $location = $checkIn->location;
        $distance = $location ? $location->calculateDistance($this->currentLat, $this->currentLng) : null;
        $isVerified = $location ? $location->isWithinRadius($this->currentLat, $this->currentLng) : true;
        
        // Create checkout
        Attendance::create([
            'user_id' => $user->id,
            'location_id' => $checkIn->location_id,
            'branch_id' => $checkIn->branch_id,
            'type' => 'check_out',
            'scanned_at' => now(),
            'scheduled_time' => $location?->work_end_time,
            'latitude' => $this->currentLat,
            'longitude' => $this->currentLng,
            'distance_meters' => $distance,
            'verification_method' => $this->selectedLocationId ? 
                ($this->scanMode === 'qr' ? 'qr_scan' : 'location_select') : 'manual',
            'is_verified' => $isVerified,
            'device_info' => [
                'user_agent' => request()->userAgent(),
                'ip' => request()->ip(),
                'gps_accuracy' => $this->gpsAccuracy,
            ],
        ]);
        
        $this->dispatch('notify', type: 'success', message: 'Check-out berhasil!');
    }

    // ===== Location CRUD (Admin) =====
    public function openLocationModal($id = null)
    {
        if ($id) {
            $this->editingLocation = true;
            $this->editLocationId = $id;
            $location = AttendanceLocation::find($id);
            $this->locationForm = [
                'name' => $location->name,
                'address' => $location->address ?? '',
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'radius_meters' => $location->radius_meters,
                'work_start_time' => Carbon::parse($location->work_start_time)->format('H:i'),
                'work_end_time' => Carbon::parse($location->work_end_time)->format('H:i'),
                'late_tolerance_minutes' => $location->late_tolerance_minutes,
                'is_active' => $location->is_active,
            ];
        } else {
            $this->editingLocation = false;
            $this->editLocationId = null;
            $this->locationForm = [
                'name' => '',
                'address' => '',
                'latitude' => '',
                'longitude' => '',
                'radius_meters' => 100,
                'work_start_time' => '08:00',
                'work_end_time' => '17:00',
                'late_tolerance_minutes' => 15,
                'is_active' => true,
            ];
        }
        $this->showLocationModal = true;
    }

    public function closeLocationModal()
    {
        $this->showLocationModal = false;
        $this->editingLocation = false;
        $this->editLocationId = null;
    }

    public function setLocationFromMap($lat, $lng)
    {
        $this->locationForm['latitude'] = $lat;
        $this->locationForm['longitude'] = $lng;
    }

    public function saveLocation()
    {
        $this->validate([
            'locationForm.name' => 'required|string|max:255',
            'locationForm.latitude' => 'required|numeric',
            'locationForm.longitude' => 'required|numeric',
            'locationForm.radius_meters' => 'required|integer|min:10|max:5000',
            'locationForm.work_start_time' => 'required',
            'locationForm.work_end_time' => 'required',
        ]);

        $user = auth()->user();
        $branchId = $user->role === 'super_admin' 
            ? ($this->selectedBranchId ?? null) 
            : $user->branch_id;

        $data = [
            'branch_id' => $branchId,
            'name' => $this->locationForm['name'],
            'address' => $this->locationForm['address'],
            'latitude' => $this->locationForm['latitude'],
            'longitude' => $this->locationForm['longitude'],
            'radius_meters' => $this->locationForm['radius_meters'],
            'work_start_time' => $this->locationForm['work_start_time'],
            'work_end_time' => $this->locationForm['work_end_time'],
            'late_tolerance_minutes' => $this->locationForm['late_tolerance_minutes'],
            'is_active' => $this->locationForm['is_active'],
        ];

        if ($this->editingLocation && $this->editLocationId) {
            AttendanceLocation::find($this->editLocationId)->update($data);
            $this->dispatch('notify', type: 'success', message: 'Lokasi berhasil diperbarui');
        } else {
            $data['created_by'] = $user->id;
            AttendanceLocation::create($data);
            $this->dispatch('notify', type: 'success', message: 'Lokasi berhasil ditambahkan');
        }

        $this->closeLocationModal();
    }

    public function deleteLocation($id)
    {
        $location = AttendanceLocation::find($id);
        if ($location) {
            $location->delete();
            $this->dispatch('notify', type: 'success', message: 'Lokasi berhasil dihapus');
        }
    }

    public function regenerateQr($id)
    {
        $location = AttendanceLocation::find($id);
        if ($location) {
            $location->update(['qr_code' => 'ATT-' . strtoupper(Str::random(12))]);
            $this->dispatch('notify', type: 'success', message: 'QR Code berhasil di-generate ulang');
        }
    }

    // ===== Computed Properties =====
    public function getTodayStatusProperty()
    {
        $userId = auth()->id();
        return [
            'check_in' => Attendance::checkInToday($userId),
            'check_out' => Attendance::checkOutToday($userId),
        ];
    }

    public function getWeekSummaryProperty()
    {
        $userId = auth()->id();
        $start = now()->startOfWeek();
        $end = now()->endOfWeek();
        
        $attendances = Attendance::where('user_id', $userId)
            ->whereBetween('date', [$start, $end])
            ->where('type', 'check_in')
            ->get()
            ->keyBy(fn($a) => $a->date->format('Y-m-d'));
        
        $days = [];
        for ($d = $start->copy(); $d <= $end; $d->addDay()) {
            $key = $d->format('Y-m-d');
            $days[$key] = [
                'date' => $d->copy(),
                'attended' => isset($attendances[$key]),
                'is_late' => isset($attendances[$key]) && $attendances[$key]->is_late,
                'is_today' => $d->isToday(),
                'is_future' => $d->isFuture(),
            ];
        }
        
        return $days;
    }

    public function getLocationsProperty()
    {
        $user = auth()->user();
        $query = AttendanceLocation::active();
        
        if ($user->role === 'super_admin') {
            if ($this->selectedBranchId) {
                $query->where('branch_id', $this->selectedBranchId);
            }
        } else {
            $query->where('branch_id', $user->branch_id);
        }
        
        return $query->orderBy('name')->get();
    }

    public function getAllLocationsProperty()
    {
        $user = auth()->user();
        $query = AttendanceLocation::with('branch');
        
        if ($user->role === 'super_admin') {
            if ($this->selectedBranchId) {
                $query->where('branch_id', $this->selectedBranchId);
            }
        } else {
            $query->where('branch_id', $user->branch_id);
        }
        
        return $query->orderBy('name')->get();
    }

    public function getHistoryProperty()
    {
        $user = auth()->user();
        $query = Attendance::with(['user', 'location', 'branch'])
            ->orderByDesc('scanned_at');
        
        // Filter by role
        if (in_array($user->role, ['staff', 'librarian', 'pustakawan'])) {
            $query->where('user_id', $user->id);
        } elseif ($user->role === 'admin') {
            $query->where('branch_id', $user->branch_id);
        } elseif ($this->selectedBranchId) {
            $query->where('branch_id', $this->selectedBranchId);
        }
        
        // Date filter
        if ($this->filterDateStart) {
            $query->whereDate('date', '>=', $this->filterDateStart);
        }
        if ($this->filterDateEnd) {
            $query->whereDate('date', '<=', $this->filterDateEnd);
        }
        
        // User filter (admin/super admin only)
        if ($this->filterUserId && in_array($user->role, ['super_admin', 'admin'])) {
            $query->where('user_id', $this->filterUserId);
        }
        
        // Location filter
        if ($this->filterLocationId) {
            $query->where('location_id', $this->filterLocationId);
        }
        
        // Status filter
        if ($this->filterStatus === 'late') {
            $query->where('is_late', true);
        } elseif ($this->filterStatus === 'on_time') {
            $query->where('is_late', false);
        }
        
        return $query->paginate(20);
    }

    public function getMapDataProperty()
    {
        $locations = $this->allLocations;
        $today = today();
        
        return $locations->map(function ($location) use ($today) {
            $todayAttendances = Attendance::where('location_id', $location->id)
                ->whereDate('date', $today)
                ->where('type', 'check_in')
                ->with('user:id,name')
                ->get();
            
            return [
                'id' => $location->id,
                'name' => $location->name,
                'address' => $location->address,
                'lat' => (float) $location->latitude,
                'lng' => (float) $location->longitude,
                'radius' => $location->radius_meters,
                'is_active' => $location->is_active,
                'branch' => $location->branch?->name ?? 'Pusat',
                'today_count' => $todayAttendances->count(),
                'staff' => $todayAttendances->map(fn($a) => [
                    'name' => $a->user->name,
                    'time' => Carbon::parse($a->scanned_at)->format('H:i'),
                    'is_late' => $a->is_late,
                ])->toArray(),
            ];
        })->toArray();
    }

    public function getStatsProperty()
    {
        $user = auth()->user();
        $today = today();
        
        $query = Attendance::whereDate('date', $today)->where('type', 'check_in');
        
        if ($user->role === 'admin') {
            $query->where('branch_id', $user->branch_id);
        } elseif ($this->selectedBranchId) {
            $query->where('branch_id', $this->selectedBranchId);
        }
        
        $todayAttendances = $query->get();
        
        return [
            'total_present' => $todayAttendances->count(),
            'total_late' => $todayAttendances->where('is_late', true)->count(),
            'total_on_time' => $todayAttendances->where('is_late', false)->count(),
        ];
    }

    public function getBranchesProperty()
    {
        return Branch::orderBy('name')->get(['id', 'name', 'code']);
    }

    public function getUsersProperty()
    {
        $user = auth()->user();
        $query = User::where('is_active', true);
        
        if ($user->role === 'admin') {
            $query->where('branch_id', $user->branch_id);
        } elseif ($this->selectedBranchId) {
            $query->where('branch_id', $this->selectedBranchId);
        }
        
        return $query->orderBy('name')->get(['id', 'name']);
    }

    public function getIsSuperAdminProperty()
    {
        return auth()->user()->role === 'super_admin';
    }

    public function getIsAdminProperty()
    {
        return in_array(auth()->user()->role, ['super_admin', 'admin']);
    }

    public function render()
    {
        return view('livewire.staff.attendance.attendance-portal')
            ->extends('staff.layouts.app')
            ->section('content');
    }
}
