<?php

namespace App\Livewire\Staff\Control;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class StaffControl extends Component
{
    use WithPagination;

    public $activeTab = 'pending';
    public $search = '';
    public $selectedUser = null;
    public $showModal = false;
    public $rejectionReason = '';

    protected $queryString = ['activeTab', 'search'];

    public function updatingSearch() { $this->resetPage(); }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function viewUser($id)
    {
        $this->selectedUser = User::with('branch')->find($id);
        $this->showModal = true;
        $this->rejectionReason = '';
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedUser = null;
        $this->rejectionReason = '';
    }

    public function approveUser()
    {
        if (!$this->selectedUser) return;

        $this->selectedUser->update([
            'status' => 'approved',
            'is_active' => true,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $this->dispatch('notify', type: 'success', message: 'Staff berhasil disetujui dan dapat login');
        $this->closeModal();
    }

    public function rejectUser()
    {
        if (!$this->selectedUser) return;

        $this->validate(['rejectionReason' => 'required|min:10']);

        $this->selectedUser->update([
            'status' => 'rejected',
            'is_active' => false,
            'rejection_reason' => $this->rejectionReason,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $this->dispatch('notify', type: 'success', message: 'Pendaftaran staff ditolak');
        $this->closeModal();
    }

    public function getStatsProperty()
    {
        $query = User::whereIn('role', ['staff', 'librarian']);
        
        // Filter by branch for non-super admin
        if (auth()->user()->role !== 'super_admin') {
            $query->where('branch_id', auth()->user()->branch_id);
        }

        return [
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'approved' => (clone $query)->where('status', 'approved')->count(),
            'rejected' => (clone $query)->where('status', 'rejected')->count(),
        ];
    }

    public function render()
    {
        $query = User::with('branch')
            ->whereIn('role', ['staff', 'librarian'])
            ->where('status', $this->activeTab)
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%"));

        // Filter by branch for non-super admin
        if (auth()->user()->role !== 'super_admin') {
            $query->where('branch_id', auth()->user()->branch_id);
        }

        return view('livewire.staff.control.staff-control', [
            'users' => $query->latest()->paginate(10),
            'stats' => $this->stats,
        ])->extends('staff.layouts.app')->section('content');
    }
}
