<?php

namespace App\Services;

use App\Models\EmailVerification;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    protected array $trustedDomains;

    public function __construct()
    {
        $this->trustedDomains = $this->loadTrustedDomains();
    }

    protected function loadTrustedDomains(): array
    {
        $file = base_path('docs/email.md');
        if (!file_exists($file)) return [];
        
        return array_filter(array_map('trim', file($file)));
    }

    public function isTrustedDomain(string $email): bool
    {
        $domain = '@' . strtolower(substr(strrchr($email, '@'), 1));
        return in_array($domain, $this->trustedDomains);
    }

    public function isAcademicDomain(string $email): bool
    {
        return str_ends_with(strtolower($email), '.ac.id');
    }

    public function detectRegistrationType(string $email): string
    {
        if ($this->isTrustedDomain($email)) return 'internal';
        if ($this->isAcademicDomain($email)) return 'external';
        return 'public';
    }

    public function extractInstitution(string $email): ?string
    {
        if (!$this->isAcademicDomain($email) || $this->isTrustedDomain($email)) return null;
        
        // Extract main domain: user@mhs.ugm.ac.id → ugm
        $domain = strtolower(substr(strrchr($email, '@'), 1));
        $parts = explode('.', $domain);
        
        // Find the institution part (before .ac.id)
        $acIndex = array_search('ac', $parts);
        if ($acIndex !== false && $acIndex > 0) {
            return strtoupper($parts[$acIndex - 1]);
        }
        return null;
    }

    public function generateOtp(string $email): string
    {
        // Delete old OTPs for this email
        EmailVerification::where('email', $email)->delete();

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        EmailVerification::create([
            'email' => $email,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(15),
        ]);

        return $otp;
    }

    public function sendOtp(string $email, string $name): bool
    {
        $otp = $this->generateOtp($email);

        Mail::send('emails.otp', ['otp' => $otp, 'name' => $name], function ($message) use ($email) {
            $message->to($email)->subject('Kode Verifikasi Email - UNIDA Library');
        });

        return true;
    }

    public function verifyOtp(string $email, string $otp): array
    {
        $verification = EmailVerification::where('email', $email)->first();

        if (!$verification) {
            return ['success' => false, 'message' => 'Kode verifikasi tidak ditemukan. Silakan minta kode baru.'];
        }

        if ($verification->isExpired()) {
            $verification->delete();
            return ['success' => false, 'message' => 'Kode verifikasi sudah kadaluarsa. Silakan minta kode baru.'];
        }

        if ($verification->isMaxAttempts()) {
            return ['success' => false, 'message' => 'Terlalu banyak percobaan. Silakan minta kode baru.'];
        }

        if ($verification->otp !== $otp) {
            $verification->increment('attempts');
            $remaining = 3 - $verification->attempts;
            return ['success' => false, 'message' => "Kode salah. Sisa percobaan: {$remaining}"];
        }

        $verification->delete();
        return ['success' => true, 'message' => 'Verifikasi berhasil'];
    }

    public function canResendOtp(string $email): array
    {
        $verification = EmailVerification::where('email', $email)->first();
        
        if (!$verification) {
            return ['can_resend' => true, 'wait_seconds' => 0];
        }

        $waitUntil = $verification->created_at->addMinutes(1);
        if (now()->lt($waitUntil)) {
            return ['can_resend' => false, 'wait_seconds' => now()->diffInSeconds($waitUntil)];
        }

        return ['can_resend' => true, 'wait_seconds' => 0];
    }
}
