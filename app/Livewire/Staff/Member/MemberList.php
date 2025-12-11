<?php

namespace App\Livewire\Staff\Member;

use App\Models\Member;
use App\Models\MemberType;
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
    
    protected $queryString = [
        'search' => ['except' => ''],
        'filterType' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterExpired' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function extendMembership($memberId)
    {
        $member = Member::with('memberType')->find($memberId);
        
        if (!$member) {
            session()->flash('error', 'Anggota tidak ditemukan');
            return;
        }

        // Only allow extending own branch members
        if ($member->branch_id !== auth()->user()->branch_id) {
            session()->flash('error', 'Tidak dapat memperpanjang anggota cabang lain');
            return;
        }

        $period = $member->memberType->membership_period ?? 365;
        $member->update([
            'register_date' => now(),
            'expire_date' => now()->addDays($period),
        ]);

        session()->flash('success', "Keanggotaan {$member->name} berhasil diperpanjang");
    }

    public function toggleActive($memberId)
    {
        $member = Member::find($memberId);
        
        if ($member && $member->branch_id === auth()->user()->branch_id) {
            $member->update(['is_active' => !$member->is_active]);
            session()->flash('success', 'Status anggota berhasil diubah');
        }
    }

    public function render()
    {
        $user = auth()->user();
        $userBranchId = $user->branch_id;

        // Stats scoped to user's branch
        $stats = [
            'total' => Member::where('branch_id', $userBranchId)->count(),
            'active' => Member::where('branch_id', $userBranchId)->where('is_active', true)->where('expire_date', '>=', now())->count(),
            'expired' => Member::where('branch_id', $userBranchId)->where('expire_date', '<', now())->count(),
            'new_this_month' => Member::where('branch_id', $userBranchId)->whereMonth('created_at', now()->month)->count(),
        ];

        $members = Member::query()
            ->with(['memberType', 'faculty', 'department'])
            ->withCount(['loans' => fn($q) => $q->where('is_returned', false)])
            ->where('branch_id', $userBranchId)
            ->when($this->search, function (Builder $query) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('member_id', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
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
        ])->extends('staff.layouts.app')->section('content');
    }
}
