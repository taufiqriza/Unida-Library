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
        
        // Debug logging
        logger('SystemUpdates: mount called, updates count: ' . $this->updates->count());
        
        // Show splash modal if there are new updates
        if ($this->updates->isNotEmpty()) {
            $this->showSplashModal = true;
            logger('SystemUpdates: showing splash modal');
        } else {
            logger('SystemUpdates: no updates to show');
        }
    }

    public function loadUpdates()
    {
        $this->updates = SystemUpdate::active()
            ->forRole(auth()->user()->role)
            ->notDismissedBy(auth()->id())
            ->orderByDesc('priority')
            ->orderByDesc('published_at')
            ->get();
    }

    public function dismissUpdate($updateId)
    {
        $update = SystemUpdate::find($updateId);
        if ($update && $update->is_dismissible) {
            $update->dismissedByUsers()->attach(auth()->id());
            $this->loadUpdates();
        }
    }

    public function dismissAll()
    {
        $dismissibleUpdates = $this->updates->where('is_dismissible', true);
        foreach ($dismissibleUpdates as $update) {
            $update->dismissedByUsers()->syncWithoutDetaching([auth()->id()]);
        }
        $this->loadUpdates();
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
