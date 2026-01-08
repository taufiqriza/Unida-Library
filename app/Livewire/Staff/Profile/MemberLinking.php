<?php

namespace App\Livewire\Staff\Profile;

use App\Models\Member;
use App\Models\User;
use App\Models\Employee;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class MemberLinking extends Component
{
    public $user;
    public $linkedMember;
    public $searchName = '';
    public $searchResults = [];
    public $isSearching = false;
    public $employeeResults = [];
    public $selectedEmployee = null;
    public $selectedPddiktiId = null;
    public $selectedPddikti = null;

    public function mount()
    {
        $this->user = auth()->user();
        $this->checkExistingLink();
    }

    public function checkExistingLink()
    {
        // Check if staff already has linked member account
        $this->linkedMember = Member::where('email', $this->user->email)->first();
    }

    public function testMethod()
    {
        logger('MemberLinking: testMethod called - SUCCESS');
        $this->searchResults = collect([
            (object)['id' => 1, 'name' => 'Test Member 1'],
            (object)['id' => 2, 'name' => 'Test Member 2']
        ]);
    }

    public function searchPddikti()
    {
        logger('MemberLinking: searchPddikti called');
        
        $this->isSearching = true;
        $this->searchResults = [];
        $this->employeeResults = [];
        
        try {
            $search = trim($this->searchName);
            
            if (strlen($search) < 2) {
                $this->isSearching = false;
                return;
            }

            // Simple member search only
            $mahasiswa = Member::where('name', 'like', "%{$search}%")
                ->limit(5)->get();
            
            $this->searchResults = $mahasiswa;
            logger('MemberLinking: Found ' . $mahasiswa->count() . ' results');
            
        } catch (\Exception $e) {
            logger('MemberLinking: Error - ' . $e->getMessage());
            $this->searchResults = [];
        }
        
        $this->isSearching = false;
    }

    public function linkMember($memberId, $type = 'member')
    {
        try {
            DB::beginTransaction();

            if ($type === 'member') {
                // Link existing member
                $member = Member::find($memberId);
                
                if (!$member) {
                    throw new \Exception('Data member tidak ditemukan');
                }

                if ($member->email) {
                    throw new \Exception('Member ini sudah terhubung dengan akun lain');
                }

                // Link member with staff email
                $member->update([
                    'email' => $this->user->email,
                    'password' => $this->user->password,
                    'email_verified' => 'verified',
                    'email_verified_at' => now(),
                    'profile_completed' => true,
                ]);

                $this->linkedMember = $member;

            } else if ($type === 'employee') {
                // Create member from employee data
                $employee = \App\Models\Employee::find($memberId);
                
                if (!$employee) {
                    throw new \Exception('Data employee tidak ditemukan');
                }

                // Check if member with this email already exists
                $existingMember = Member::where('email', $this->user->email)->first();

                if ($existingMember) {
                    throw new \Exception('Email sudah terhubung dengan member lain');
                }

                // Create new member from employee data
                $member = Member::create([
                    'name' => $employee->full_name ?? $employee->name,
                    'email' => $this->user->email,
                    'password' => $this->user->password,
                    'member_id' => $employee->niy ?? $employee->nidn ?? 'EMP' . $employee->id,
                    'nim_nidn' => $employee->nidn ?? $employee->niy,
                    'member_type_id' => 2, // Assuming 2 is for staff/lecturer
                    'registration_type' => 'internal',
                    'register_date' => now(),
                    'expire_date' => now()->addYears(5),
                    'is_active' => true,
                    'profile_completed' => true,
                    'email_verified' => 'verified',
                    'email_verified_at' => now(),
                    'branch_id' => $this->user->branch_id ?? 1,
                ]);

                $this->linkedMember = $member;
            }

            DB::commit();

            $this->reset(['searchName', 'searchResults']);

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
