<?php

namespace App\Livewire\Staff\Attendance;

use App\Models\Attendance;
use App\Models\AttendanceLocation;
use Livewire\Component;
use Carbon\Carbon;

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
            $distanceKm = number_format($distance / 1000, 2);
            $this->dispatch('notify', type: 'error', message: "Anda di luar radius! Jarak: {$distanceKm}km (max: {$location->radius_meters}m)");
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
            'attendance_location_id' => $location->id,
            'type' => 'check_in',
            'scanned_at' => $now,
            'latitude' => $this->currentLat,
            'longitude' => $this->currentLng,
            'distance_meters' => min((int) round($distance), 2147483647),
            'is_within_radius' => true,
            'is_verified' => true,
            'late_minutes' => $lateMinutes,
        ]);

        $this->dispatch('notify', type: 'success', message: '✅ Check-in berhasil! Jarak: ' . round($distance) . 'm');
        $this->dispatch('attendance-updated');
    }

    public function checkOut()
    {
        $user = auth()->user();
        
        // Check if checked in today
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
            $this->dispatch('notify', type: 'error', message: 'GPS tidak tersedia');
            return;
        }

        $location = $checkIn->location;
        $distance = 0;
        $isWithinRadius = true;

        if ($location) {
            $distance = $this->calculateDistance(
                $this->currentLat, $this->currentLng,
                $location->latitude, $location->longitude
            );
            $isWithinRadius = $distance <= $location->radius_meters;
        }

        // Create checkout
        Attendance::create([
            'user_id' => $user->id,
            'branch_id' => $user->branch_id,
            'attendance_location_id' => $checkIn->attendance_location_id,
            'type' => 'check_out',
            'scanned_at' => now(),
            'latitude' => $this->currentLat,
            'longitude' => $this->currentLng,
            'distance_meters' => min((int) round($distance), 2147483647),
            'is_within_radius' => $isWithinRadius,
            'is_verified' => true,
        ]);

        $this->dispatch('notify', type: 'success', message: '✅ Check-out berhasil!');
        $this->dispatch('attendance-updated');
    }

    public function handleQrScan($code)
    {
        // Parse QR data
        $qrCode = $code;
        try {
            $data = json_decode($code, true);
            if (isset($data['code'])) {
                $qrCode = $data['code'];
            }
        } catch (\Exception $e) {}

        // Find location
        $location = AttendanceLocation::where('qr_code', $qrCode)->first();
        
        if ($location) {
            $this->selectedLocationId = $location->id;
            $this->scannedQrCode = $qrCode;
            $this->dispatch('qr-detected', name: $location->name);
        } else {
            $this->dispatch('notify', type: 'error', message: 'QR Code tidak valid');
        }
    }

    public function updateGps($lat, $lng)
    {
        $this->currentLat = (float) $lat;
        $this->currentLng = (float) $lng;
    }

    protected function calculateDistance($lat1, $lng1, $lat2, $lng2): float
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    public function getLocationsProperty()
    {
        $user = auth()->user();
        return AttendanceLocation::active()
            ->where(function($q) use ($user) {
                if ($user->role === 'super_admin') {
                    // See all
                } else {
                    $q->where('branch_id', $user->branch_id)
                      ->orWhereNull('branch_id');
                }
            })
            ->orderBy('name')
            ->get(['id', 'name', 'qr_code']);
    }

    public function getTodayStatusProperty()
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
