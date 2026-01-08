<?php

namespace App\Livewire\Staff\Member;

use App\Models\Employee;
use App\Models\Member;
use App\Models\MemberType;
use App\Models\Branch;
use App\Models\ActivityLog;
use Livewire\Component;
use Livewire\WithPagination;

class MemberList extends Component
{
    use WithPagination;

    public $search = '';
    public $activeTab = 'all';
    public $filterStatus = '';
    public $filterExpired = '';
    public $filterBranchId = '';
    public $showDetailModal = false;
    public $selectedMember = null;
    public $showDeleteModal = false;
    public $memberToDelete = null;
    
    // SDM filters
    public $sdmType = '';
    public $sdmFaculty = '';
    
    protected $queryString = [
        'search' => ['except' => ''],
        'activeTab' => ['except' => 'all'],
        'filterStatus' => ['except' => ''],
        'filterExpired' => ['except' => ''],
        'filterBranchId' => ['except' => ''],
        'sdmType' => ['except' => ''],
        'sdmFaculty' => ['except' => ''],
    ];

    // Employee modal
    public $showEmployeeModal = false;
    public $selectedEmployee = null;

    public function updatingSearch() { $this->resetPage(); }
    public function updatingActiveTab() { $this->resetPage(); }
    public function setTab($tab) { $this->activeTab = $tab; $this->resetPage(); }

    public function showDetail($memberId)
    {
        $this->selectedMember = Member::with(['memberType', 'branch', 'faculty', 'department'])->find($memberId);
        $this->showDetailModal = true;
    }

    public function closeDetail() { 
        $this->showDetailModal = false; 
        $this->selectedMember = null; 
        $this->dispatch('close-modal');
    }

    public function showEmployeeDetail($employeeId)
    {
        $this->selectedEmployee = Employee::find($employeeId);
        $this->showEmployeeModal = true;
    }

    public function closeEmployeeDetail() { 
        $this->showEmployeeModal = false; 
        $this->selectedEmployee = null; 
        $this->dispatch('close-modal');
    }

    public function extendMembership($memberId)
    {
        $member = Member::with('memberType')->find($memberId);
        $user = auth()->user();
        
        if (!$member || ($user->role !== 'super_admin' && $member->branch_id !== $user->branch_id)) {
            $this->dispatch('notify', type: 'error', message: 'Tidak dapat memperpanjang');
            return;
        }

        $period = $member->memberType->membership_period ?? 365;
        $member->update(['register_date' => now(), 'expire_date' => now()->addDays($period)]);
        ActivityLog::log('update', 'member', "Perpanjang keanggotaan: {$member->name}", $member);
        $this->dispatch('notify', type: 'success', message: "Keanggotaan {$member->name} diperpanjang");
    }

    public function toggleActive($memberId)
    {
        $member = Member::find($memberId);
        $user = auth()->user();
        
        if (!$member || ($user->role !== 'super_admin' && $member->branch_id !== $user->branch_id)) {
            $this->dispatch('notify', type: 'error', message: 'Tidak dapat mengubah status');
            return;
        }
        
        $member->update(['is_active' => !$member->is_active]);
        ActivityLog::log('update', 'member', ($member->is_active ? 'Aktifkan' : 'Nonaktifkan') . " anggota: {$member->name}", $member);
        $this->dispatch('notify', type: 'success', message: 'Status anggota berhasil diubah');
    }

    protected function getTypeIdByTab($tab)
    {
        return match($tab) {
            'mahasiswa' => MemberType::where('name', 'Mahasiswa')->value('id'),
            'santri' => MemberType::where('name', 'Santri')->value('id'),
            'umum' => MemberType::where('name', 'Umum')->value('id'),
            default => null
        };
    }

    public function confirmDelete($memberId)
    {
        $this->memberToDelete = Member::find($memberId);
        $this->showDeleteModal = true;
    }

    public function deleteMember()
    {
        if (!$this->memberToDelete) {
            return;
        }

        try {
            // Check if member has any related data that should prevent deletion
            $memberName = $this->memberToDelete->name;
            
            // Delete the member
            $this->memberToDelete->delete();
            
            // Log the activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'delete',
                'model' => 'Member',
                'model_id' => $this->memberToDelete->id,
                'description' => "Menghapus member: {$memberName}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            session()->flash('success', "Member {$memberName} berhasil dihapus.");
            
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus member: ' . $e->getMessage());
        }

        $this->showDeleteModal = false;
        $this->memberToDelete = null;
        $this->resetPage();
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->memberToDelete = null;
    }

    public function render()
    {
        $user = auth()->user();
        $isSuperAdmin = $user->role === 'super_admin';
        $userBranchId = $user->branch_id;
        
        // Branch filter only applies to members, not employees (dosen/tendik visible to all)
        $effectiveBranchId = $isSuperAdmin ? ($this->filterBranchId ?: null) : $userBranchId;

        // Get member types for tabs
        $memberTypes = MemberType::withCount(['members' => function($q) use ($effectiveBranchId) {
            $q->when($effectiveBranchId, fn($q) => $q->where('branch_id', $effectiveBranchId));
        }])->orderBy('name')->get();

        // Visibility rules:
        // - Dosen/Tendik: visible to ALL branches (data SDM pusat) EXCEPT OPPM
        // - Santri: only visible to OPPM branch or super admin
        // - Mahasiswa/Umum: filtered by branch, Mahasiswa hidden for OPPM
        $isOppmBranch = $user->branch && str_contains(strtolower($user->branch->name), 'oppm');
        $canSeeSantri = $isSuperAdmin || $isOppmBranch;
        $canSeeMahasiswa = !$isOppmBranch || $isSuperAdmin;
        $canSeeDosen = !$isOppmBranch || $isSuperAdmin;
        $canSeeTendik = !$isOppmBranch || $isSuperAdmin;

        // Stats - dosen/tendik always show full count
        $statsQuery = Member::query()->when($effectiveBranchId, fn($q) => $q->where('branch_id', $effectiveBranchId));
        $stats = [
            'total' => (clone $statsQuery)->count(),
            'active' => (clone $statsQuery)->where('is_active', true)->where('expire_date', '>=', now())->count(),
            'expired' => (clone $statsQuery)->where('expire_date', '<', now())->count(),
            'new_this_month' => (clone $statsQuery)->whereMonth('created_at', now()->month)->count(),
            'mahasiswa' => (clone $statsQuery)->whereHas('memberType', fn($q) => $q->where('name', 'Mahasiswa'))->count(),
            'santri' => (clone $statsQuery)->whereHas('memberType', fn($q) => $q->where('name', 'Santri'))->count(),
            'umum' => (clone $statsQuery)->whereHas('memberType', fn($q) => $q->where('name', 'Umum'))->count(),
            'dosen' => Employee::dosen()->active()->count(),
            'tendik' => Employee::tendik()->count(),
        ];

        // Dosen/Tendik tabs - show from employees table
        if (in_array($this->activeTab, ['dosen', 'tendik'])) {
            $employees = Employee::query()
                ->where('type', $this->activeTab)
                ->when($this->search, fn($q) => $q->where(fn($q) => 
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('niy', 'like', "%{$this->search}%")
                      ->orWhere('nidn', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%")
                ))
                ->when($this->sdmFaculty, fn($q) => $q->where('faculty', $this->sdmFaculty))
                ->when($this->filterStatus === 'active', fn($q) => $q->where('is_active', true))
                ->when($this->filterStatus === 'inactive', fn($q) => $q->where('is_active', false))
                ->orderBy('name')
                ->paginate(15);

            $sdmFaculties = Employee::where('type', $this->activeTab)
                ->whereNotNull('faculty')
                ->distinct()
                ->pluck('faculty')
                ->sort();

            return view('livewire.staff.member.member-list', [
                'members' => collect(),
                'employees' => $employees,
                'stats' => $stats,
                'memberTypes' => $memberTypes,
                'branches' => $isSuperAdmin ? Branch::orderBy('name')->get() : collect(),
                'isSuperAdmin' => $isSuperAdmin,
                'canSeeSantri' => $canSeeSantri,
                'canSeeMahasiswa' => $canSeeMahasiswa,
                'canSeeDosen' => $canSeeDosen,
                'canSeeTendik' => $canSeeTendik,
                'sdmFaculties' => $sdmFaculties,
                'showEmployees' => true,
            ])->extends('staff.layouts.app')->section('content');
        }

        // Members query
        $typeId = $this->getTypeIdByTab($this->activeTab);
        
        $members = Member::query()
            ->with(['memberType', 'faculty', 'department', 'branch'])
            ->withCount(['loans' => fn($q) => $q->where('is_returned', false)])
            ->when($effectiveBranchId, fn($q) => $q->where('branch_id', $effectiveBranchId))
            ->when($typeId, function($q) use ($typeId) {
                if (is_array($typeId)) {
                    $q->whereIn('member_type_id', $typeId);
                } else {
                    $q->where('member_type_id', $typeId);
                }
            })
            ->when($this->search, fn($q) => $q->where(fn($q) => 
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('member_id', 'like', "%{$this->search}%")
                  ->orWhere('nim_nidn', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
            ))
            ->when($this->filterStatus === 'active', fn($q) => $q->where('is_active', true))
            ->when($this->filterStatus === 'inactive', fn($q) => $q->where('is_active', false))
            ->when($this->filterExpired === 'expired', fn($q) => $q->where('expire_date', '<', now()))
            ->when($this->filterExpired === 'valid', fn($q) => $q->where('expire_date', '>=', now()))
            ->latest()
            ->paginate(15);

        return view('livewire.staff.member.member-list', [
            'members' => $members,
            'employees' => collect(),
            'stats' => $stats,
            'memberTypes' => $memberTypes,
            'branches' => $isSuperAdmin ? Branch::orderBy('name')->get() : collect(),
            'isSuperAdmin' => $isSuperAdmin,
            'canSeeSantri' => $canSeeSantri,
            'canSeeMahasiswa' => $canSeeMahasiswa,
            'canSeeDosen' => $canSeeDosen,
            'canSeeTendik' => $canSeeTendik,
            'sdmFaculties' => collect(),
            'showEmployees' => false,
        ])->extends('staff.layouts.app')->section('content');
    }
}
