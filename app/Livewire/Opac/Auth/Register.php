<?php

namespace App\Livewire\Opac\Auth;

use App\Models\Employee;
use App\Models\Member;
use App\Models\MemberType;
use App\Services\OtpService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;

class Register extends Component
{
    // Step wizard
    public int $step = 1;
    public string $userType = ''; // mahasiswa, dosen, tendik, umum
    
    // Form fields
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $institution = '';
    public string $institution_city = '';
    public string $password = '';
    public string $password_confirmation = '';
    
    // Search & claim
    public string $searchQuery = '';
    public array $searchResults = [];
    public ?int $selectedId = null;
    
    // Detection
    public ?Member $detectedMember = null;
    public ?Employee $detectedEmployee = null;
    public ?string $detectedType = null;
    public bool $claimVerified = false;

    public function mount()
    {
        if (session('google_email')) {
            $this->email = session('google_email');
            $this->name = session('google_name', '');
            $this->detectFromEmail();
        }
    }

    public function updatedEmail()
    {
        $this->detectFromEmail();
    }

    public function updatedSearchQuery()
    {
        $this->searchResults = [];
        $this->selectedId = null;
        
        if (strlen($this->searchQuery) < 3) return;
        
        if ($this->userType === 'mahasiswa') {
            $this->searchMahasiswa();
        } elseif (in_array($this->userType, ['dosen', 'tendik'])) {
            $this->searchEmployee();
        }
    }

    protected function searchMahasiswa()
    {
        $q = $this->searchQuery;
        
        $results = Member::where('registration_type', 'internal')
            ->where(function($query) use ($q) {
                $query->where('member_id', 'like', "%{$q}%")
                      ->orWhere('nim_nidn', 'like', "%{$q}%")
                      ->orWhere('name', 'like', "%{$q}%");
            })
            ->whereNull('email') // Hanya yang belum punya email (belum terdaftar)
            ->limit(10)
            ->get(['id', 'member_id', 'name', 'nim_nidn'])
            ->map(fn($m) => [
                'id' => $m->id,
                'nim' => $m->member_id,
                'name' => $m->name,
                'display' => $m->member_id . ' - ' . $m->name,
            ])
            ->toArray();
            
        $this->searchResults = $results;
    }

    protected function searchEmployee()
    {
        $q = $this->searchQuery;
        
        $results = Employee::where('type', $this->userType)
            ->where('is_active', true)
            ->where(function($query) use ($q) {
                $query->where('niy', 'like', "%{$q}%")
                      ->orWhere('nidn', 'like', "%{$q}%")
                      ->orWhere('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
            })
            ->limit(10)
            ->get(['id', 'niy', 'nidn', 'name', 'full_name', 'faculty', 'prodi', 'satker'])
            ->map(fn($e) => [
                'id' => $e->id,
                'niy' => $e->niy,
                'nidn' => $e->nidn,
                'name' => $e->full_name ?? $e->name,
                'unit' => $e->faculty ?? $e->satker,
                'prodi' => $e->prodi,
                'display' => ($e->niy ?? '-') . ' - ' . ($e->full_name ?? $e->name),
            ])
            ->toArray();
            
        $this->searchResults = $results;
    }

    protected function detectFromEmail()
    {
        $this->detectedType = null;
        $this->detectedMember = null;
        $this->detectedEmployee = null;
        
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) return;

        $domain = strtolower(substr(strrchr($this->email, '@'), 1));
        
        // Mahasiswa - email kampus student
        if (str_contains($domain, 'student.') || str_starts_with($domain, 'stu.') || str_starts_with($domain, 'mhs.')) {
            $this->detectedType = 'mahasiswa';
            $this->userType = 'mahasiswa';
            
            $nim = explode('@', $this->email)[0];
            if (preg_match('/^\d{9,15}$/', $nim)) {
                $this->detectedMember = Member::where('member_id', $nim)
                    ->orWhere('nim_nidn', $nim)
                    ->first();
                if ($this->detectedMember) {
                    $this->claimVerified = true;
                    $this->name = $this->detectedMember->name;
                }
            }
            return;
        }
        
        // Dosen/Tendik - email kampus non-student
        if (str_contains($domain, 'unida.gontor')) {
            $this->detectedType = 'dosen_tendik';
            
            $this->detectedEmployee = Employee::where('email', $this->email)->first();
            if ($this->detectedEmployee) {
                $this->userType = $this->detectedEmployee->type;
                $this->claimVerified = true;
                $this->name = $this->detectedEmployee->full_name ?? $this->detectedEmployee->name;
            }
            return;
        }
        
        $this->detectedType = 'external';
    }

    public function selectUserType(string $type)
    {
        $this->userType = $type;
        $this->searchQuery = '';
        $this->searchResults = [];
        $this->selectedId = null;
        $this->claimVerified = false;
        
        // Umum langsung ke step 3
        $this->step = ($type === 'umum') ? 3 : 2;
    }

    public function selectResult(int $id)
    {
        $this->selectedId = $id;
        
        if ($this->userType === 'mahasiswa') {
            $this->detectedMember = Member::find($id);
            if ($this->detectedMember) {
                $this->claimVerified = true;
                $this->name = $this->detectedMember->name;
                $this->searchResults = [];
            }
        } else {
            $this->detectedEmployee = Employee::find($id);
            if ($this->detectedEmployee) {
                $this->claimVerified = true;
                $this->name = $this->detectedEmployee->full_name ?? $this->detectedEmployee->name;
                $this->searchResults = [];
            }
        }
    }

    public function clearSelection()
    {
        $this->selectedId = null;
        $this->detectedMember = null;
        $this->detectedEmployee = null;
        $this->claimVerified = false;
        $this->searchQuery = '';
        $this->name = '';
    }

    public function goToStep(int $step)
    {
        $this->step = $step;
    }

    public function register(OtpService $otpService)
    {
        // Validate
        $rules = [
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ];
        
        if ($this->userType === 'umum') {
            $rules['name'] = 'required|max:255';
            $rules['institution'] = 'required|max:255';
        } elseif (!$this->claimVerified) {
            $rules['name'] = 'required|max:255';
        }
        
        $this->validate($rules);

        // Check email exists
        if (Member::where('email', $this->email)->exists()) {
            $this->addError('email', 'Email sudah terdaftar. Silakan login.');
            return;
        }

        $registrationType = $otpService->detectRegistrationType($this->email);
        $isTrusted = $registrationType === 'internal';

        // Handle verified civitas
        if ($this->userType === 'mahasiswa' && $this->detectedMember) {
            return $this->linkToSiakadMember($isTrusted, $otpService);
        }
        
        if (in_array($this->userType, ['dosen', 'tendik']) && $this->detectedEmployee) {
            return $this->createFromEmployee($isTrusted, $otpService);
        }

        // Create new member (umum)
        $member = Member::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => Hash::make($this->password),
            'member_id' => $this->generateUniqueMemberId(),
            'member_type_id' => MemberType::where('name', 'like', '%umum%')->first()?->id ?? 4,
            'register_date' => now(),
            'expire_date' => now()->addYear(),
            'is_active' => true,
            'registration_type' => 'public',
            'institution' => $this->institution,
            'institution_city' => $this->institution_city,
            'email_verified' => 'pending',
        ]);

        Log::info('New public member registered', ['member_id' => $member->member_id, 'email' => $member->email]);

        $otpService->sendOtp($this->email, $this->name);
        session(['pending_member_id' => $member->id]);
        return redirect()->route('opac.verify-email');
    }

    protected function linkToSiakadMember(bool $isTrusted, OtpService $otpService)
    {
        $this->detectedMember->update([
            'email' => $this->email,
            'phone' => $this->phone ?: $this->detectedMember->phone,
            'password' => Hash::make($this->password),
            'profile_completed' => true,
            'email_verified' => $isTrusted ? 'verified' : 'pending',
            'email_verified_at' => $isTrusted ? now() : null,
        ]);

        Log::info('SIAKAD member linked', ['member_id' => $this->detectedMember->member_id, 'email' => $this->email]);

        if ($isTrusted) {
            Auth::guard('member')->login($this->detectedMember);
            return redirect()->route('opac.member.dashboard')->with('success', 'Akun berhasil dihubungkan dengan data SIAKAD!');
        }

        $otpService->sendOtp($this->email, $this->detectedMember->name);
        session(['pending_member_id' => $this->detectedMember->id]);
        return redirect()->route('opac.verify-email');
    }

    protected function createFromEmployee(bool $isTrusted, OtpService $otpService)
    {
        // Check if already registered as member
        $existingMember = Member::where('member_id', $this->detectedEmployee->niy)->first();
        if ($existingMember) {
            if ($existingMember->email) {
                $this->addError('email', 'Data ini sudah terdaftar dengan email lain.');
                return;
            }
            // Update existing
            $existingMember->update([
                'email' => $this->email,
                'phone' => $this->phone,
                'password' => Hash::make($this->password),
                'profile_completed' => true,
                'email_verified' => $isTrusted ? 'verified' : 'pending',
                'email_verified_at' => $isTrusted ? now() : null,
            ]);
            $member = $existingMember;
        } else {
            $member = Member::create([
                'name' => $this->detectedEmployee->full_name ?? $this->detectedEmployee->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'password' => Hash::make($this->password),
                'member_id' => $this->detectedEmployee->niy ?? $this->generateUniqueMemberId(),
                'nim_nidn' => $this->detectedEmployee->nidn,
                'member_type_id' => $this->userType === 'dosen' ? 2 : 3,
                'gender' => $this->detectedEmployee->gender === 'L' ? 'M' : ($this->detectedEmployee->gender === 'P' ? 'F' : null),
                'register_date' => now(),
                'expire_date' => now()->addYears(5),
                'is_active' => true,
                'registration_type' => 'internal',
                'profile_completed' => true,
                'email_verified' => $isTrusted ? 'verified' : 'pending',
                'email_verified_at' => $isTrusted ? now() : null,
            ]);
        }

        Log::info('Employee registered as member', ['member_id' => $member->member_id, 'type' => $this->userType]);

        if ($isTrusted) {
            Auth::guard('member')->login($member);
            return redirect()->route('opac.member.dashboard')->with('success', 'Akun berhasil dibuat!');
        }

        $otpService->sendOtp($this->email, $member->name);
        session(['pending_member_id' => $member->id]);
        return redirect()->route('opac.verify-email');
    }

    protected function generateUniqueMemberId(): string
    {
        $prefix = match($this->userType) {
            'dosen' => 'D',
            'tendik' => 'T',
            default => 'U',
        };
        
        do {
            $id = $prefix . date('Ymd') . strtoupper(Str::random(4));
        } while (Member::where('member_id', $id)->exists());
        
        return $id;
    }

    public function render()
    {
        return view('livewire.opac.auth.register')
            ->layout('components.opac.layout', ['title' => 'Daftar Anggota']);
    }
}
