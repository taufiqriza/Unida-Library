<?php

namespace App\Livewire\Staff\Attendance;

use App\Models\Attendance;
use App\Models\AttendanceLocation;
use App\Models\ActivityLog;
use Livewire\Component;
use Carbon\Carbon;
use Livewire\Attributes\Computed;

class QuickAttendance extends Component
{
    public $mode = 'location'; // 'location' or 'qr'
    public $selectedLocationId = null;
    public $currentLat = null;
    public $currentLng = null;
    public $scannedQrCode = null;

    public function checkIn()
    {
        $user = auth()->user();
        
        // Validate location selected
        if (!$this->selectedLocationId) {
            $this->dispatch('notify', type: 'error', message: 'Pilih lokasi terlebih dahulu');
            return;
        }

        // Check if already checked in today
        if (Attendance::checkInToday($user->id)) {
            $this->dispatch('notify', type: 'error', message: 'Anda sudah check-in hari ini');
            return;
        }

        // Get location
        $location = AttendanceLocation::find($this->selectedLocationId);
        if (!$location) {
            $this->dispatch('notify', type: 'error', message: 'Lokasi tidak ditemukan');
            return;
        }

        // Validate GPS
        if (!$this->currentLat || !$this->currentLng) {
            $this->dispatch('notify', type: 'error', message: 'GPS tidak tersedia');
            return;
        }

        // Calculate distance
        $distance = $this->calculateDistance(
            $this->currentLat, $this->currentLng,
            $location->latitude, $location->longitude
        );

        // Check radius
        if ($distance > $location->radius_meters) {
            $distanceFormatted = $this->formatDistance($distance);
            $this->dispatch('notify', type: 'error', message: "Anda di luar radius! Jarak: {$distanceFormatted} (max: {$location->radius_meters}m)");
            return;
        }

        // Calculate late minutes
        $now = Carbon::now();
        $scheduleStart = Carbon::parse($location->schedule_start);
        $todaySchedule = $now->copy()->setTimeFrom($scheduleStart);
        $lateMinutes = max(0, (int) $now->diffInMinutes($todaySchedule, false) * -1);

        // Create attendance
        Attendance::create([
            'user_id' => $user->id,
            'branch_id' => $user->branch_id,
            'location_id' => $location->id,
            'type' => 'check_in',
            'scanned_at' => $now,
            'latitude' => $this->currentLat,
            'longitude' => $this->currentLng,
            'distance_meters' => min((int) round($distance), 2147483647),
            'is_within_radius' => true,
            'is_verified' => true,
            'late_minutes' => $lateMinutes,
        ]);

        // Log activity
        ActivityLog::log(
            'create',
            'attendance',
            "Check-in di {$location->name} (jarak: " . round($distance) . "m)",
            null,
            ['location' => $location->name, 'distance' => round($distance), 'late_minutes' => $lateMinutes]
        );

        $this->dispatch('notify', type: 'success', message: '✅ Check-in berhasil! Jarak: ' . round($distance) . 'm');
        $this->dispatch('attendance-updated');
    }

    public function checkOut()
    {
        $user = auth()->user();
        
        // Check if checked in today (with location eager loaded)
        $checkIn = Attendance::checkInToday($user->id);
        if (!$checkIn) {
            $this->dispatch('notify', type: 'error', message: 'Anda belum check-in hari ini');
            return;
        }

        // Check if already checked out
        if (Attendance::checkOutToday($user->id)) {
            $this->dispatch('notify', type: 'error', message: 'Anda sudah check-out hari ini');
            return;
        }

        // Validate GPS
        if (!$this->currentLat || !$this->currentLng) {
            $this->dispatch('notify', type: 'error', message: 'GPS tidak tersedia. Aktifkan lokasi di browser.');
            return;
        }

        // Get check-in location
        $location = $checkIn->location;
        
        // If location not found (deleted), block checkout
        if (!$location) {
            $this->dispatch('notify', type: 'error', message: 'Lokasi check-in tidak ditemukan. Hubungi admin.');
            return;
        }

        // Calculate distance to check-in location
        $distance = $this->calculateDistance(
            $this->currentLat, $this->currentLng,
            $location->latitude, $location->longitude
        );

        // Validate radius - must be within check-in location radius
        if ($distance > $location->radius_meters) {
            $distanceFormatted = $this->formatDistance($distance);
            $this->dispatch('notify', type: 'error', message: "Anda di luar area {$location->name}! Jarak: {$distanceFormatted} (max: {$location->radius_meters}m). Silakan mendekat ke lokasi.");
            return;
        }

        // Create checkout
        Attendance::create([
            'user_id' => $user->id,
            'branch_id' => $user->branch_id,
            'location_id' => $checkIn->location_id,
            'type' => 'check_out',
            'scanned_at' => now(),
            'latitude' => $this->currentLat,
            'longitude' => $this->currentLng,
            'distance_meters' => min((int) round($distance), 2147483647),
            'is_within_radius' => true,
            'is_verified' => true,
        ]);

        // Log activity
        ActivityLog::log(
            'create',
            'attendance',
            "Check-out di {$location->name} (jarak: " . round($distance) . "m)",
            null,
            ['location' => $location->name, 'distance' => round($distance)]
        );

        $this->dispatch('notify', type: 'success', message: '✅ Check-out berhasil! Jarak: ' . round($distance) . 'm');
        $this->dispatch('attendance-updated');
    }

    public function handleQrScan($code)
    {
        // Parse QR data - support both plain text and JSON format
        $qrCode = $code;
        if (str_starts_with($code, '{')) {
            $data = json_decode($code, true);
            if (isset($data['code'])) {
                $qrCode = $data['code'];
            }
        }

        // Find location by QR code
        $location = AttendanceLocation::where('qr_code', $qrCode)
            ->active()
            ->first(['id', 'name']);
        
        if ($location) {
            $this->selectedLocationId = $location->id;
            $this->scannedQrCode = $qrCode;
            $this->dispatch('qr-detected', name: $location->name);
        } else {
            $this->dispatch('notify', type: 'error', message: 'QR Code tidak valid atau lokasi tidak aktif');
        }
    }

    public function updateGps($lat, $lng)
    {
        $this->currentLat = (float) $lat;
        $this->currentLng = (float) $lng;
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     */
    protected function calculateDistance($lat1, $lng1, $lat2, $lng2): float
    {
        $earthRadius = 6371000; // meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    /**
     * Format distance for display
     */
    protected function formatDistance(float $distance): string
    {
        return $distance >= 1000 
            ? number_format($distance / 1000, 2) . ' km' 
            : round($distance) . ' m';
    }

    #[Computed(cache: true)]
    public function locations()
    {
        $user = auth()->user();
        return AttendanceLocation::query()
            ->active()
            ->when($user->role !== 'super_admin', function($q) use ($user) {
                $q->where(fn($sub) => 
                    $sub->where('branch_id', $user->branch_id)
                        ->orWhereNull('branch_id')
                );
            })
            ->orderBy('name')
            ->get(['id', 'name', 'qr_code']);
    }

    #[Computed]
    public function todayStatus()
    {
        $userId = auth()->id();
        return [
            'checkIn' => Attendance::checkInToday($userId),
            'checkOut' => Attendance::checkOutToday($userId),
        ];
    }

    public function render()
    {
        return view('livewire.staff.attendance.quick-attendance', [
            'locations' => $this->locations,
            'todayStatus' => $this->todayStatus,
        ]);
    }
}
