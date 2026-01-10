<?php

namespace App\Livewire\Staff;

use App\Models\User;
use Livewire\Component;

class OnlineStaffIndicator extends Component
{
    public function getOnlineStaffProperty()
    {
        $user = auth()->user();
        
        $query = User::query()
            ->whereIn('role', ['super_admin', 'admin', 'librarian', 'staff']);
        
        // Admin cabang hanya lihat staff di cabangnya
        if ($user->role === 'admin' && $user->branch_id) {
            $query->where('branch_id', $user->branch_id);
        }
        
        return $query->orderByDesc('is_online')
            ->orderByDesc('last_seen_at')
            ->get(['id', 'name', 'role', 'photo', 'last_seen_at', 'is_online']);
    }

    public function getOnlineCountProperty()
    {
        return $this->onlineStaff->where('is_online', true)->count();
    }

    public function render()
    {
        return view('livewire.staff.online-staff-indicator');
    }
}
