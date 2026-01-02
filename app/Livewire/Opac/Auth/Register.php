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
    
    // Claim fields
    public string $claimNim = '';
    public string $claimNiy = '';
    
    // Detection
    public ?Member $detectedMember = null;
    public ?Employee $detectedEmployee = null;
    public ?string $detectedType = null;
    public bool $claimVerified = false;

    protected function rules()
    {
        return [
            'name' => 'required|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|max:20',
            'password' => 'required|min:8|confirmed',
        ];
    }

    public function mount()
    {
        // If coming from Google OAuth with email, auto-detect
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
            
            // Try to find by NIM in email
            $nim = explode('@', $this->email)[0];
            if (preg_match('/^\d{9,15}$/', $nim)) {
                $this->detectedMember = Member::where('member_id', $nim)
                    ->orWhere('nim_nidn', $nim)
                    ->first();
            }
            return;
        }
        
        // Dosen/Tendik - email kampus non-student
        if (str_contains($domain, 'unida.gontor')) {
            $this->detectedType = 'dosen_tendik';
            
            // Try to find employee by email
            $this->detectedEmployee = Employee::where('email', $this->email)->first();
            if ($this->detectedEmployee) {
                $this->userType = $this->detectedEmployee->type;
            }
            return;
        }
        
        // External - need to choose
        $this->detectedType = 'external';
    }

    public function selectUserType(string $type)
    {
        $this->userType = $type;
        $this->step = 2;
        $this->claimVerified = false;
        $this->detectedMember = null;
        $this->detectedEmployee = null;
    }

    public function verifyClaim()
    {
        $this->claimVerified = false;
        
        if ($this->userType === 'mahasiswa') {
            $nim = trim($this->claimNim);
            if (empty($nim)) {
                $this->addError('claimNim', 'NIM wajib diisi');
                return;
            }
            
            $this->detectedMember = Member::where('member_id', $nim)
                ->orWhere('nim_nidn', $nim)
                ->first();
                
            if (!$this->detectedMember) {
                $this->addError('claimNim', 'NIM tidak ditemukan di database SIAKAD');
                return;
            }
            
            // Check if already has email
            if ($this->detectedMember->email && $this->detectedMember->email !== $this->email) {
                $this->addError('claimNim', 'NIM ini sudah terdaftar dengan email lain');
                return;
            }
            
            $this->claimVerified = true;
            $this->name = $this->detectedMember->name;
            
        } elseif (in_array($this->userType, ['dosen', 'tendik'])) {
            $niy = trim($this->claimNiy);
            if (empty($niy)) {
                $this->addError('claimNiy', 'NIY wajib diisi');
                return;
            }
            
            $this->detectedEmployee = Employee::where('niy', $niy)
                ->where('type', $this->userType)
                ->first();
                
            if (!$this->detectedEmployee) {
                $this->addError('claimNiy', 'NIY tidak ditemukan di database SDM');
                return;
            }
            
            $this->claimVerified = true;
            $this->name = $this->detectedEmployee->full_name ?? $this->detectedEmployee->name;
        }
    }

    public function goToStep(int $step)
    {
        $this->step = $step;
    }

    public function register(OtpService $otpService)
    {
        // Validate based on user type
        if ($this->userType === 'umum') {
            $this->validate([
                'name' => 'required|max:255',
                'email' => 'required|email',
                'phone' => 'nullable|max:20',
                'institution' => 'required|max:255',
                'password' => 'required|min:8|confirmed',
            ]);
        } else {
            $this->validate();
        }

        // Check email exists
        if (Member::where('email', $this->email)->exists()) {
            $this->addError('email', 'Email sudah terdaftar');
            return;
        }

        $registrationType = $otpService->detectRegistrationType($this->email);
        $isTrusted = $registrationType === 'internal';

        // Handle claim for civitas
        if ($this->userType === 'mahasiswa' && $this->detectedMember) {
            return $this->linkToSiakadMember($isTrusted, $otpService);
        }
        
        if (in_array($this->userType, ['dosen', 'tendik']) && $this->detectedEmployee) {
            return $this->createFromEmployee($isTrusted, $otpService);
        }

        // Create new member (umum or unverified civitas)
        $memberTypeId = $this->getMemberTypeId();
        
        $member = Member::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => Hash::make($this->password),
            'member_id' => $this->generateUniqueMemberId(),
            'member_type_id' => $memberTypeId,
            'register_date' => now(),
            'expire_date' => now()->addYear(),
            'is_active' => true,
            'registration_type' => $this->userType === 'umum' ? 'public' : 'internal',
            'institution' => $this->institution,
            'institution_city' => $this->institution_city,
            'email_verified' => $isTrusted ? 'verified' : 'pending',
            'email_verified_at' => $isTrusted ? now() : null,
        ]);

        Log::info('New member registered', [
            'member_id' => $member->member_id,
            'email' => $member->email,
            'type' => $this->userType,
        ]);

        if ($isTrusted) {
            Auth::guard('member')->login($member);
            return redirect()->route('opac.member.dashboard');
        }

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

        Log::info('SIAKAD member linked', [
            'member_id' => $this->detectedMember->member_id,
            'email' => $this->email,
        ]);

        if ($isTrusted) {
            Auth::guard('member')->login($this->detectedMember);
            return redirect()->route('opac.member.dashboard')
                ->with('success', 'Akun berhasil dihubungkan dengan data SIAKAD!');
        }

        $otpService->sendOtp($this->email, $this->detectedMember->name);
        session(['pending_member_id' => $this->detectedMember->id]);
        return redirect()->route('opac.verify-email');
    }

    protected function createFromEmployee(bool $isTrusted, OtpService $otpService)
    {
        $memberTypeId = $this->userType === 'dosen' ? 2 : 3; // Dosen=2, Tendik=3
        
        $member = Member::create([
            'name' => $this->detectedEmployee->full_name ?? $this->detectedEmployee->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => Hash::make($this->password),
            'member_id' => $this->detectedEmployee->niy ?? $this->generateUniqueMemberId(),
            'nim_nidn' => $this->detectedEmployee->nidn,
            'member_type_id' => $memberTypeId,
            'gender' => $this->detectedEmployee->gender === 'L' ? 'M' : ($this->detectedEmployee->gender === 'P' ? 'F' : null),
            'register_date' => now(),
            'expire_date' => now()->addYears(5),
            'is_active' => true,
            'registration_type' => 'internal',
            'profile_completed' => true,
            'email_verified' => $isTrusted ? 'verified' : 'pending',
            'email_verified_at' => $isTrusted ? now() : null,
        ]);

        Log::info('Employee registered as member', [
            'member_id' => $member->member_id,
            'employee_niy' => $this->detectedEmployee->niy,
            'type' => $this->userType,
        ]);

        if ($isTrusted) {
            Auth::guard('member')->login($member);
            return redirect()->route('opac.member.dashboard')
                ->with('success', 'Akun berhasil dibuat dari data SDM!');
        }

        $otpService->sendOtp($this->email, $member->name);
        session(['pending_member_id' => $member->id]);
        return redirect()->route('opac.verify-email');
    }

    protected function getMemberTypeId(): int
    {
        return match($this->userType) {
            'mahasiswa' => 1,
            'dosen' => 2,
            'tendik' => MemberType::where('name', 'like', '%tendik%')->first()?->id ?? 3,
            default => MemberType::where('name', 'like', '%umum%')->first()?->id ?? 4,
        };
    }

    protected function generateUniqueMemberId(): string
    {
        $prefix = match($this->userType) {
            'dosen' => 'D',
            'tendik' => 'T',
            'umum' => 'U',
            default => 'M',
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
