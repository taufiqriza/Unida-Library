<?php

namespace App\Livewire\Opac\Auth;

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
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $institution = '';
    public string $institution_city = '';
    public string $password = '';
    public string $password_confirmation = '';
    
    // SIAKAD member detection
    public ?Member $detectedMember = null;
    public bool $showConfirmation = false;

    protected function rules()
    {
        return [
            'name' => 'required|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|max:20',
            'institution' => 'nullable|max:255',
            'institution_city' => 'nullable|max:100',
            'password' => 'required|min:8|confirmed',
        ];
    }

    protected $messages = [
        'email.unique' => 'Email sudah terdaftar',
        'password.min' => 'Password minimal 8 karakter',
        'password.confirmed' => 'Konfirmasi password tidak cocok',
    ];

    public function updatedEmail()
    {
        $this->detectSiakadMember();
    }

    public function updatedName()
    {
        // Only check by name if email doesn't match
        if (!$this->detectedMember) {
            $this->detectSiakadMember();
        }
    }

    protected function detectSiakadMember()
    {
        $this->detectedMember = null;
        $this->showConfirmation = false;

        // 1. Check if email already registered
        $existingByEmail = Member::where('email', $this->email)->first();
        if ($existingByEmail) {
            return; // Will be handled by validation
        }

        // 2. Extract NIM from campus email (e.g., 432022413017@student.unida.gontor.ac.id)
        $nim = $this->extractNimFromEmail($this->email);
        if ($nim) {
            $member = Member::where('member_id', $nim)
                ->orWhere('nim_nidn', $nim)
                ->first();
            if ($member) {
                $this->detectedMember = $member;
                $this->showConfirmation = true;
                return;
            }
        }

        // 3. Check by exact name match (for SIAKAD members)
        if (strlen($this->name) >= 5) {
            $normalizedName = $this->normalizeName($this->name);
            $member = Member::whereRaw('UPPER(REPLACE(REPLACE(name, \".\", \"\"), \"  \", \" \")) = ?', [$normalizedName])
                ->where('registration_type', 'internal')
                ->first();
            if ($member) {
                $this->detectedMember = $member;
                $this->showConfirmation = true;
                return;
            }
            
            // 4. Fuzzy match - cari nama yang sangat mirip
            $member = Member::where('registration_type', 'internal')
                ->whereRaw('SOUNDEX(name) = SOUNDEX(?)', [$this->name])
                ->whereRaw('LENGTH(name) BETWEEN ? AND ?', [strlen($this->name) - 3, strlen($this->name) + 3])
                ->first();
            if ($member) {
                $this->detectedMember = $member;
                $this->showConfirmation = true;
            }
        }
    }
    
    protected function normalizeName(string $name): string
    {
        return strtoupper(preg_replace('/\s+/', ' ', str_replace('.', '', trim($name))));
    }

    protected function extractNimFromEmail(string $email): ?string
    {
        $campusDomains = [
            'student.unida.gontor.ac.id', 'student.cs.unida.gontor.ac.id', 
            'student.iqt.unida.gontor.ac.id', 'student.ilkom.unida.gontor.ac.id',
            'student.hi.unida.gontor.ac.id', 'student.hes.unida.gontor.ac.id',
            'student.gizi.unida.gontor.ac.id', 'student.fk.unida.gontor.ac.id',
            'student.farmasi.unida.gontor.ac.id', 'student.ei.unida.gontor.ac.id',
            'student.agro.unida.gontor.ac.id', 'student.afi.unida.gontor.ac.id',
            'student.k3.unida.gontor.ac.id', 'student.mgt.unida.gontor.ac.id',
            'student.pai.unida.gontor.ac.id', 'student.pba.unida.gontor.ac.id',
            'student.pm.unida.gontor.ac.id', 'student.saa.unida.gontor.ac.id',
            'student.tbi.unida.gontor.ac.id', 'student.tip.unida.gontor.ac.id',
            'mhs.unida.gontor.ac.id', 'stu.unida.gontor.ac.id',
        ];
        
        $domain = strtolower(substr(strrchr($email, '@'), 1));
        if (in_array($domain, $campusDomains)) {
            $nim = explode('@', $email)[0];
            // Validate NIM format (numeric, 9-15 digits)
            if (preg_match('/^\d{9,15}$/', $nim)) {
                return $nim;
            }
        }
        return null;
    }

    public function confirmLinkAccount()
    {
        if (!$this->detectedMember) {
            return;
        }

        $this->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $otpService = app(OtpService::class);
        $registrationType = $otpService->detectRegistrationType($this->email);
        $isTrusted = $registrationType === 'internal';

        // Update existing SIAKAD member
        $this->detectedMember->update([
            'email' => $this->email,
            'phone' => $this->phone ?: $this->detectedMember->phone,
            'password' => Hash::make($this->password),
            'profile_completed' => true,
            'email_verified' => $isTrusted ? 'verified' : 'pending',
            'email_verified_at' => $isTrusted ? now() : null,
        ]);

        Log::channel('daily')->info('SIAKAD member linked via registration', [
            'member_id' => $this->detectedMember->member_id,
            'email' => $this->email,
            'ip' => request()->ip(),
        ]);

        if ($isTrusted) {
            Auth::guard('member')->login($this->detectedMember);
            return redirect()->route('opac.member.dashboard')
                ->with('success', 'Akun berhasil dihubungkan dengan data SIAKAD!');
        }

        // Send OTP for verification
        $otpService->sendOtp($this->email, $this->detectedMember->name);
        session(['pending_member_id' => $this->detectedMember->id]);
        
        return redirect()->route('opac.verify-email');
    }

    public function cancelLinkAccount()
    {
        $this->detectedMember = null;
        $this->showConfirmation = false;
    }

    public function register(OtpService $otpService)
    {
        $this->validate();

        // Check if email already exists
        if (Member::where('email', $this->email)->exists()) {
            $this->addError('email', 'Email sudah terdaftar');
            return;
        }

        // Re-check SIAKAD detection
        $this->detectSiakadMember();
        if ($this->showConfirmation) {
            return; // Show confirmation dialog
        }
        
        // Final check: prevent duplicate by similar name for internal registration
        $registrationType = $otpService->detectRegistrationType($this->email);
        if ($registrationType === 'internal') {
            $normalizedName = $this->normalizeName($this->name);
            $existingByName = Member::whereRaw('UPPER(REPLACE(REPLACE(name, ".", ""), "  ", " ")) = ?', [$normalizedName])
                ->where('registration_type', 'internal')
                ->first();
            if ($existingByName) {
                $this->detectedMember = $existingByName;
                $this->showConfirmation = true;
                return;
            }
        }

        $isTrusted = $registrationType === 'internal';

        // Auto-detect institution for external (.ac.id)
        $institution = $this->institution;
        if ($registrationType === 'external' && !$institution) {
            $institution = $otpService->extractInstitution($this->email);
        }

        $member = Member::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => Hash::make($this->password),
            'member_id' => $this->generateUniqueMemberId(),
            'member_type_id' => MemberType::first()?->id ?? 1,
            'register_date' => now(),
            'expire_date' => now()->addYear(),
            'is_active' => true,
            'registration_type' => $registrationType,
            'institution' => $institution,
            'institution_city' => $this->institution_city,
            'email_verified' => $isTrusted ? 'verified' : 'pending',
            'email_verified_at' => $isTrusted ? now() : null,
        ]);

        Log::channel('daily')->info('New member registered', [
            'member_id' => $member->member_id,
            'email' => $member->email,
            'type' => $registrationType,
            'ip' => request()->ip(),
        ]);

        // Trusted domain: auto login
        if ($isTrusted) {
            Auth::guard('member')->login($member);
            return redirect()->route('opac.member.dashboard');
        }

        // Non-trusted: send OTP and redirect to verification
        $otpService->sendOtp($this->email, $this->name);
        session(['pending_member_id' => $member->id]);
        
        return redirect()->route('opac.verify-email');
    }

    protected function generateUniqueMemberId(): string
    {
        do {
            $id = 'M' . date('Ymd') . strtoupper(Str::random(4));
        } while (Member::where('member_id', $id)->exists());
        
        return $id;
    }

    public function render()
    {
        return view('livewire.opac.auth.register')
            ->layout('components.opac.layout', ['title' => 'Daftar Anggota']);
    }
}
