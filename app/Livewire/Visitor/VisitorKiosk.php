<?php

namespace App\Livewire\Visitor;

use App\Models\Branch;
use App\Models\Member;
use App\Models\Visit;
use Livewire\Component;

class VisitorKiosk extends Component
{
    public ?Branch $branch = null;
    public string $mode = 'idle'; // idle, member, guest, success
    public string $nim = '';
    public string $guestName = '';
    public string $guestInstitution = '';
    public string $purpose = 'baca';
    public ?Member $foundMember = null;
    public string $message = '';
    public string $errorMessage = '';

    public function mount(string $code)
    {
        $this->branch = Branch::where('code', $code)->where('is_active', true)->first();
        if (!$this->branch) abort(404, 'Cabang tidak ditemukan');
    }

    public function searchMember()
    {
        $this->errorMessage = '';
        $this->foundMember = null;

        if (strlen($this->nim) < 3) {
            $this->errorMessage = 'Masukkan minimal 3 karakter';
            return;
        }

        $member = Member::where('member_id', $this->nim)->first();

        if (!$member) {
            $this->errorMessage = 'NIM tidak terdaftar';
            return;
        }

        if ($member->expire_date && $member->expire_date < now()) {
            $this->errorMessage = 'Keanggotaan sudah habis masa berlaku';
            return;
        }

        $this->foundMember = $member;
        $this->mode = 'member';
    }

    public function confirmMemberVisit()
    {
        if (!$this->foundMember) return;

        // Check duplicate (within 30 minutes)
        $recent = Visit::where('member_id', $this->foundMember->id)
            ->where('branch_id', $this->branch->id)
            ->where('visited_at', '>=', now()->subMinutes(30))
            ->first();

        if ($recent) {
            $recent->update(['visited_at' => now(), 'purpose' => $this->purpose]);
        } else {
            Visit::create([
                'branch_id' => $this->branch->id,
                'member_id' => $this->foundMember->id,
                'visitor_type' => 'member',
                'purpose' => $this->purpose,
                'visited_at' => now(),
            ]);
        }

        $this->message = "Selamat datang, {$this->foundMember->name}!";
        $this->mode = 'success';
    }

    public function submitGuest()
    {
        $this->validate([
            'guestName' => 'required|min:3|max:100',
            'guestInstitution' => 'required|min:2|max:100',
        ], [
            'guestName.required' => 'Nama wajib diisi',
            'guestName.min' => 'Nama minimal 3 karakter',
            'guestInstitution.required' => 'Institusi wajib diisi',
        ]);

        Visit::create([
            'branch_id' => $this->branch->id,
            'guest_name' => $this->guestName,
            'guest_institution' => $this->guestInstitution,
            'visitor_type' => 'guest',
            'purpose' => $this->purpose,
            'visited_at' => now(),
        ]);

        $this->message = "Selamat datang, {$this->guestName}!";
        $this->mode = 'success';
    }

    public function switchToGuest()
    {
        $this->mode = 'guest';
        $this->errorMessage = '';
    }

    public function reset_form()
    {
        $this->mode = 'idle';
        $this->nim = '';
        $this->guestName = '';
        $this->guestInstitution = '';
        $this->purpose = 'baca';
        $this->foundMember = null;
        $this->message = '';
        $this->errorMessage = '';
    }

    public function render()
    {
        $todayCount = Visit::where('branch_id', $this->branch->id)
            ->whereDate('visited_at', today())->count();

        $todayStats = [
            'member' => Visit::where('branch_id', $this->branch->id)
                ->whereDate('visited_at', today())->where('visitor_type', 'member')->count(),
            'guest' => Visit::where('branch_id', $this->branch->id)
                ->whereDate('visited_at', today())->where('visitor_type', 'guest')->count(),
        ];

        foreach (['baca', 'pinjam', 'belajar', 'penelitian', 'lainnya'] as $purpose) {
            $todayStats[$purpose] = Visit::where('branch_id', $this->branch->id)
                ->whereDate('visited_at', today())->where('purpose', $purpose)->count();
        }

        return view('livewire.visitor.visitor-kiosk', [
            'todayCount' => $todayCount,
            'todayStats' => $todayStats,
        ])->layout('components.visitor-layout', ['branch' => $this->branch]);
    }
}
