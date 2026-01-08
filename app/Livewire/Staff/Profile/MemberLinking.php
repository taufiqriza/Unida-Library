<?php

namespace App\Livewire\Staff\Profile;

use App\Models\Member;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class MemberLinking extends Component
{
    public $user;
    public $linkedMember;
    public $searchQuery = '';
    public $searchResults = [];
    public $isSearching = false;
    public $showLinkingSection = false;

    public function mount()
    {
        $this->user = auth()->user();
        $this->checkExistingLink();
    }

    public function checkExistingLink()
    {
        // Check if staff already has linked member account
        $this->linkedMember = Member::withoutGlobalScope('branch')
            ->where('email', $this->user->email)
            ->first();
    }

    public function toggleLinkingSection()
    {
        $this->showLinkingSection = !$this->showLinkingSection;
        if (!$this->showLinkingSection) {
            $this->reset(['searchQuery', 'searchResults', 'isSearching']);
        }
    }

    public function searchMembers()
    {
        if (strlen($this->searchQuery) < 3) {
            $this->searchResults = [];
            return;
        }

        $this->isSearching = true;

        // Search in members table (SIAKAD data)
        $members = Member::withoutGlobalScope('branch')
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->searchQuery . '%')
                      ->orWhere('member_id', 'like', '%' . $this->searchQuery . '%')
                      ->orWhere('nim_nidn', 'like', '%' . $this->searchQuery . '%')
                      ->orWhere('email', 'like', '%' . $this->searchQuery . '%');
            })
            ->whereNull('email') // Only show members without email (not linked yet)
            ->with(['faculty', 'department', 'memberType'])
            ->limit(10)
            ->get();

        $this->searchResults = $members->map(function ($member) {
            return [
                'id' => $member->id,
                'name' => $member->name,
                'member_id' => $member->member_id,
                'nim_nidn' => $member->nim_nidn,
                'faculty' => $member->faculty->name ?? '-',
                'department' => $member->department->name ?? '-',
                'member_type' => $member->memberType->name ?? '-',
                'registration_type' => $member->registration_type,
            ];
        })->toArray();

        $this->isSearching = false;
    }

    public function linkMember($memberId)
    {
        try {
            DB::beginTransaction();

            $member = Member::withoutGlobalScope('branch')->find($memberId);
            
            if (!$member) {
                throw new \Exception('Data member tidak ditemukan');
            }

            if ($member->email) {
                throw new \Exception('Member ini sudah terhubung dengan akun lain');
            }

            // Link member with staff email
            $member->update([
                'email' => $this->user->email,
                'password' => $this->user->password, // Use same password
                'email_verified' => 'verified',
                'email_verified_at' => now(),
                'profile_completed' => true,
            ]);

            DB::commit();

            $this->linkedMember = $member;
            $this->showLinkingSection = false;
            $this->reset(['searchQuery', 'searchResults']);

            session()->flash('success', 'Berhasil menghubungkan akun dengan data member!');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', $e->getMessage());
        }
    }

    public function unlinkMember()
    {
        if (!$this->linkedMember) return;

        try {
            DB::beginTransaction();

            // Remove email link but keep member data
            $this->linkedMember->update([
                'email' => null,
                'password' => null,
                'email_verified' => null,
                'email_verified_at' => null,
            ]);

            DB::commit();

            $this->linkedMember = null;
            session()->flash('success', 'Berhasil memutus hubungan dengan data member');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal memutus hubungan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.staff.profile.member-linking');
    }
}
