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
        
        // Only show modal on first login (not on refresh)
        if ($this->updates->isNotEmpty() && !session()->has('updates_shown_this_session')) {
            $this->showSplashModal = true;
            session()->put('updates_shown_this_session', true);
            logger('SystemUpdates: showing splash modal for first login');
        } else {
            logger('SystemUpdates: modal already shown this session or no updates');
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
