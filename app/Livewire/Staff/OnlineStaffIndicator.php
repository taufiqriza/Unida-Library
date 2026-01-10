<?php

namespace App\Livewire\Staff;

use App\Models\User;
use Livewire\Component;

class OnlineStaffIndicator extends Component
{
    public function getOnlineStaffProperty()
    {
        return User::query()
            ->whereIn('role', ['super_admin', 'admin', 'librarian', 'staff'])
            ->orderByDesc('last_seen_at')
            ->get(['id', 'name', 'role', 'photo', 'last_seen_at']);
    }

    public function getOnlineCountProperty()
    {
        return $this->onlineStaff->filter(fn($s) => $s->isReallyOnline())->count();
    }

    public function render()
    {
        return view('livewire.staff.online-staff-indicator');
    }
}
