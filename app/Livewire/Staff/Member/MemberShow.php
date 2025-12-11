<?php

namespace App\Livewire\Staff\Member;

use App\Models\Member;
use App\Models\Loan;
use Livewire\Component;

class MemberShow extends Component
{
    public Member $member;
    public $activeLoans = [];
    public $loanHistory = [];

    public function mount($member): void
    {
        $this->member = Member::with(['memberType', 'faculty', 'department', 'branch'])
            ->findOrFail($member);

        // Check branch access
        $user = auth()->user();
        if ($this->member->branch_id !== $user->branch_id && $user->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }

        $this->loadLoans();
    }

    protected function loadLoans(): void
    {
        $this->activeLoans = Loan::with(['item.book'])
            ->where('member_id', $this->member->id)
            ->where('is_returned', false)
            ->orderBy('due_date')
            ->get();

        $this->loanHistory = Loan::with(['item.book'])
            ->where('member_id', $this->member->id)
            ->where('is_returned', true)
            ->latest('return_date')
            ->take(10)
            ->get();
    }

    public function extendMembership()
    {
        $period = $this->member->memberType->membership_period ?? 365;
        $this->member->update([
            'register_date' => now(),
            'expire_date' => now()->addDays($period),
        ]);
        
        $this->member->refresh();
        session()->flash('success', 'Keanggotaan berhasil diperpanjang');
    }

    public function toggleActive()
    {
        $this->member->update(['is_active' => !$this->member->is_active]);
        $this->member->refresh();
        session()->flash('success', 'Status anggota berhasil diubah');
    }

    public function render()
    {
        return view('livewire.staff.member.member-show')
            ->extends('staff.layouts.app')
            ->section('content');
    }
}
