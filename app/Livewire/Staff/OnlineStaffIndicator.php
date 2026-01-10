<?php

namespace App\Livewire\Staff;

use App\Models\User;
use Livewire\Component;

class OnlineStaffIndicator extends Component
{
    public function getOnlineStaffProperty()
    {
        return User::where('is_online', true)
            ->orWhere('last_seen_at', '>=', now()->subMinutes(5))
            ->orderByDesc('last_seen_at')
            ->get(['id', 'name', 'role', 'photo', 'last_seen_at', 'is_online']);
    }

    public function render()
    {
        return view('livewire.staff.online-staff-indicator');
    }
}
