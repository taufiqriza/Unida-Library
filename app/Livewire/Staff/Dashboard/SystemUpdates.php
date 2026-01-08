<?php

namespace App\Livewire\Staff\Dashboard;

use App\Models\SystemUpdate;
use Livewire\Component;

class SystemUpdates extends Component
{
    public $showSplashModal = false;
    public $updates = [];
    public $memberLinkingInfo = null;

    public function mount()
    {
        $this->loadUpdates();
        $this->checkMemberLinking();
        
        // Show splash modal if there are new updates
        if ($this->updates->isNotEmpty()) {
            $this->showSplashModal = true;
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

    public function checkMemberLinking()
    {
        $user = auth()->user();
        
        // Check if staff has linked member account
        $linkedMember = \App\Models\Member::where('email', $user->email)->first();
        
        if (!$linkedMember && in_array($user->role, ['staff', 'librarian'])) {
            $this->memberLinkingInfo = [
                'title' => 'Hubungkan Akun Member Anda',
                'description' => 'Sebagai staff yang juga mahasiswa/dosen, hubungkan akun member Anda untuk mengakses fasilitas perpustakaan dengan satu akun.',
                'benefits' => [
                    'Akses Member Portal dengan data akademik',
                    'Peminjaman buku dan e-resources',
                    'Riwayat aktivitas perpustakaan',
                    'Notifikasi dan reminder otomatis'
                ]
            ];
        }
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
