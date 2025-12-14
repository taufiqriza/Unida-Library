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

    protected function rules()
    {
        return [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:members,email',
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

    public function register(OtpService $otpService)
    {
        $this->validate();

        $registrationType = $otpService->detectRegistrationType($this->email);
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
