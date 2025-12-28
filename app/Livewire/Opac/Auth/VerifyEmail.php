<?php

namespace App\Livewire\Opac\Auth;

use App\Models\Member;
use App\Services\OtpService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class VerifyEmail extends Component
{
    public ?Member $member = null;
    public string $otp = '';
    public array $resendInfo = [];

    public function mount()
    {
        $memberId = session('pending_member_id');
        if (!$memberId) {
            return redirect()->route('login');
        }

        $this->member = Member::find($memberId);
        if (!$this->member || $this->member->email_verified === 'verified') {
            session()->forget('pending_member_id');
            return redirect()->route('login');
        }

        $otpService = app(OtpService::class);
        $this->resendInfo = $otpService->canResendOtp($this->member->email);
    }

    public function verify(OtpService $otpService)
    {
        $this->validate([
            'otp' => 'required|digits:6',
        ], [
            'otp.required' => 'Kode OTP wajib diisi',
            'otp.digits' => 'Kode OTP harus 6 digit',
        ]);

        $result = $otpService->verifyOtp($this->member->email, $this->otp);

        if (!$result['success']) {
            $this->addError('otp', $result['message']);
            return;
        }

        $this->member->update([
            'email_verified' => 'verified',
            'email_verified_at' => now(),
        ]);

        session()->forget('pending_member_id');
        Auth::guard('member')->login($this->member);

        return redirect()->route('opac.member.dashboard')
            ->with('success', 'Email berhasil diverifikasi!');
    }

    public function resendOtp(OtpService $otpService)
    {
        $this->resendInfo = $otpService->canResendOtp($this->member->email);
        
        if (!$this->resendInfo['can_resend']) {
            $this->dispatch('notify', type: 'warning', message: 'Tunggu sebentar sebelum mengirim ulang');
            return;
        }

        $otpService->sendOtp($this->member->email, $this->member->name);
        $this->resendInfo = $otpService->canResendOtp($this->member->email);
        
        $this->dispatch('notify', type: 'success', message: 'Kode verifikasi telah dikirim ulang');
        $this->dispatch('otp-resent');
    }

    public function render()
    {
        return view('livewire.opac.auth.verify-email')
            ->layout('components.opac.layout', ['title' => 'Verifikasi Email']);
    }
}
