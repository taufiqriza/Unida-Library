<?php

namespace App\Livewire\Staff\Member;

use App\Models\Member;
use App\Models\MemberType;
use App\Models\Branch;
use App\Models\ActivityLog;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;

class MemberList extends Component
{
    use WithPagination;

    public $search = '';
    public $filterType = '';
    public $filterStatus = '';
    public $filterExpired = '';
    public $filterBranchId = ''; // For super admin branch filter
    
    protected $queryString = [
        'search' => ['except' => ''],
        'filterType' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterExpired' => ['except' => ''],
        'filterBranchId' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterBranchId()
    {
        $this->resetPage();
    }

    public function extendMembership($memberId)
    {
        $member = Member::with('memberType')->find($memberId);
        $user = auth()->user();
        
        if (!$member) {
            $this->dispatch('notify', type: 'error', message: 'Anggota tidak ditemukan');
            return;
        }

        // Only allow extending own branch members (unless super admin)
        if ($user->role !== 'super_admin' && $member->branch_id !== $user->branch_id) {
            $this->dispatch('notify', type: 'error', message: 'Tidak dapat memperpanjang anggota cabang lain');
            return;
        }

        $period = $member->memberType->membership_period ?? 365;
        $member->update([
            'register_date' => now(),
            'expire_date' => now()->addDays($period),
        ]);

        // Log activity
        ActivityLog::log(
            'update',
            'member',
            "Perpanjang keanggotaan: {$member->name}",
            $member,
            ['new_expire_date' => now()->addDays($period)->format('Y-m-d')]
        );

        $this->dispatch('notify', type: 'success', message: "Keanggotaan {$member->name} berhasil diperpanjang");
    }

    public function toggleActive($memberId)
    {
        $member = Member::find($memberId);
        $user = auth()->user();
        
        if (!$member) return;
        
        // Super admin can toggle any, admin only own branch
        if ($user->role !== 'super_admin' && $member->branch_id !== $user->branch_id) {
            $this->dispatch('notify', type: 'error', message: 'Tidak dapat mengubah status anggota cabang lain');
            return;
        }
        
        $member->update(['is_active' => !$member->is_active]);
        
        // Log activity
        ActivityLog::log(
            'update',
            'member',
            ($member->is_active ? 'Aktifkan' : 'Nonaktifkan') . " anggota: {$member->name}",
            $member
        );
        
        $this->dispatch('notify', type: 'success', message: 'Status anggota berhasil diubah');
    }

    public function render()
    {
        $user = auth()->user();
        $isSuperAdmin = $user->role === 'super_admin';
        $userBranchId = $user->branch_id;
        
        // Determine which branch to filter
        $effectiveBranchId = null;
        if ($isSuperAdmin) {
            $effectiveBranchId = $this->filterBranchId ?: null; // null = all branches
        } else {
            $effectiveBranchId = $userBranchId;
        }

        // Stats scoped to effective branch
        $statsQuery = Member::query();
        if ($effectiveBranchId) {
            $statsQuery->where('branch_id', $effectiveBranchId);
        } elseif (!$isSuperAdmin) {
            $statsQuery->where('branch_id', $userBranchId);
        }
        
        $stats = [
            'total' => (clone $statsQuery)->count(),
            'active' => (clone $statsQuery)->where('is_active', true)->where('expire_date', '>=', now())->count(),
            'expired' => (clone $statsQuery)->where('expire_date', '<', now())->count(),
            'new_this_month' => (clone $statsQuery)->whereMonth('created_at', now()->month)->count(),
        ];

        $members = Member::query()
            ->with(['memberType', 'faculty', 'department', 'branch'])
            ->withCount(['loans' => fn($q) => $q->where('is_returned', false)])
            // Branch filter
            ->when($effectiveBranchId, fn($q) => $q->where('branch_id', $effectiveBranchId))
            ->when(!$isSuperAdmin && !$effectiveBranchId, fn($q) => $q->where('branch_id', $userBranchId))
            // Search
            ->when($this->search, function (Builder $query) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('member_id', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            // Other filters
            ->when($this->filterType, fn($q) => $q->where('member_type_id', $this->filterType))
            ->when($this->filterStatus === 'active', fn($q) => $q->where('is_active', true))
            ->when($this->filterStatus === 'inactive', fn($q) => $q->where('is_active', false))
            ->when($this->filterExpired === 'expired', fn($q) => $q->where('expire_date', '<', now()))
            ->when($this->filterExpired === 'valid', fn($q) => $q->where('expire_date', '>=', now()))
            ->latest()
            ->paginate(15);

        return view('livewire.staff.member.member-list', [
            'members' => $members,
            'stats' => $stats,
            'memberTypes' => MemberType::orderBy('name')->get(),
            'branches' => $isSuperAdmin ? Branch::orderBy('name')->get() : collect(),
            'isSuperAdmin' => $isSuperAdmin,
        ])->extends('staff.layouts.app')->section('content');
    }
}
