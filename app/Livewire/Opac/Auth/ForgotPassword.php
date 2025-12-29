<?php

namespace App\Livewire\Opac\Auth;

use App\Models\Member;
use App\Models\PasswordReset;
use App\Services\OtpService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class ForgotPassword extends Component
{
    public string $step = 'email'; // email, otp, reset
    public string $email = '';
    public string $otp = '';
    public string $password = '';
    public string $password_confirmation = '';
    
    public int $countdown = 0;
    public ?string $resetToken = null;

    protected OtpService $otpService;

    public function boot(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function sendOtp()
    {
        $this->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
        ]);

        $member = Member::where('email', $this->email)->first();
        
        if (!$member) {
            $this->addError('email', 'Email tidak terdaftar di sistem kami');
            return;
        }

        // Check resend cooldown
        $canResend = $this->otpService->canResendOtp($this->email);
        if (!$canResend['can_resend']) {
            $this->countdown = $canResend['wait_seconds'];
            $this->addError('email', "Tunggu {$this->countdown} detik sebelum mengirim ulang");
            return;
        }

        // Generate and send OTP
        $otp = $this->otpService->generateOtp($this->email);
        
        try {
            Mail::send('emails.password-reset', [
                'name' => $member->name,
                'code' => $otp,
            ], function ($message) {
                $message->to($this->email)
                    ->subject('🔑 Reset Password - UNIDA Library');
            });

            $this->step = 'otp';
            $this->countdown = 60;
            $this->dispatch('notify', type: 'success', message: 'Kode OTP telah dikirim ke email Anda');
        } catch (\Exception $e) {
            $this->addError('email', 'Gagal mengirim email. Silakan coba lagi.');
            \Log::error('Failed to send password reset email: ' . $e->getMessage());
        }
    }

    public function verifyOtp()
    {
        $this->validate([
            'otp' => 'required|digits:6',
        ], [
            'otp.required' => 'Kode OTP wajib diisi',
            'otp.digits' => 'Kode OTP harus 6 digit',
        ]);

        $result = $this->otpService->verifyOtp($this->email, $this->otp);

        if (!$result['success']) {
            $this->addError('otp', $result['message']);
            return;
        }

        // Generate reset token
        $this->resetToken = bin2hex(random_bytes(32));
        
        PasswordReset::updateOrCreate(
            ['email' => $this->email],
            ['token' => Hash::make($this->resetToken), 'created_at' => now()]
        );

        $this->step = 'reset';
        $this->dispatch('notify', type: 'success', message: 'Verifikasi berhasil. Silakan buat password baru.');
    }

    public function resetPassword()
    {
        $this->validate([
            'password' => 'required|min:8|confirmed',
        ], [
            'password.required' => 'Password baru wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        // Verify token
        $reset = PasswordReset::where('email', $this->email)->first();
        
        if (!$reset || !Hash::check($this->resetToken, $reset->token)) {
            $this->addError('password', 'Sesi reset password tidak valid. Silakan ulangi dari awal.');
            $this->step = 'email';
            return;
        }

        // Check token expiry (30 minutes)
        if ($reset->created_at->addMinutes(30)->isPast()) {
            $reset->delete();
            $this->addError('password', 'Sesi reset password sudah kadaluarsa. Silakan ulangi dari awal.');
            $this->step = 'email';
            return;
        }

        // Update password
        $member = Member::where('email', $this->email)->first();
        $member->update(['password' => Hash::make($this->password)]);

        // Cleanup
        $reset->delete();

        session()->flash('success', 'Password berhasil diubah. Silakan login dengan password baru.');
        return redirect()->route('opac.login');
    }

    public function resendOtp()
    {
        $this->otp = '';
        $this->resetValidation();
        $this->sendOtp();
    }

    public function backToEmail()
    {
        $this->step = 'email';
        $this->otp = '';
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.opac.auth.forgot-password')
            ->layout('components.opac.layout', ['title' => 'Lupa Password']);
    }
}
