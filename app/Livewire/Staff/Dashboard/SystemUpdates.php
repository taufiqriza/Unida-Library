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
        
        logger('SystemUpdates mount: updates count = ' . $this->updates->count());
        logger('SystemUpdates mount: modal_dismissed session = ' . (session()->has('modal_dismissed') ? 'true' : 'false'));
        
        // Show modal only if not dismissed this session
        if ($this->updates->isNotEmpty() && !session()->has('modal_dismissed')) {
            $this->showSplashModal = true;
            logger('SystemUpdates: SHOWING splash modal');
        } else {
            logger('SystemUpdates: NOT showing modal - updates empty or dismissed');
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

    public function dismissAll()
    {
        // Mark as dismissed for this session
        session()->put('modal_dismissed', true);
        $this->showSplashModal = false;
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
