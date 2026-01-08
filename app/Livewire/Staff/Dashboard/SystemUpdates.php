<?php

namespace App\Livewire\Staff\Dashboard;

use App\Models\SystemUpdate;
use Livewire\Component;

class SystemUpdates extends Component
{
    public $showSplashModal = false;
    public $updates = [];

    public function mount()
    {
        $this->loadUpdates();
        
        // Always show modal on mount (first load after login)
        if ($this->updates->isNotEmpty()) {
            $this->showSplashModal = true;
            logger('SystemUpdates: showing splash modal on mount');
        }
    }

    public function loadUpdates()
    {
        // Always show all active updates regardless of dismissal status
        $this->updates = SystemUpdate::active()
            ->forRole(auth()->user()->role)
            ->orderByDesc('priority')
            ->orderByDesc('published_at')
            ->get();
    }

    public function closeSplash()
    {
        $this->showSplashModal = false;
    }

    public function render()
    {
        return view('livewire.staff.dashboard.system-updates');
    }
}
