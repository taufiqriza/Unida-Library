<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Setting;
use App\Models\SocialAccount;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirect()
    {
        if (!Setting::get('google_oauth_enabled')) {
            return redirect()->route('login')->with('error', 'Google login tidak aktif.');
        }
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        if (!Setting::get('google_oauth_enabled')) {
            return redirect()->route('login')->with('error', 'Google login tidak aktif.');
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Gagal login dengan Google.');
        }

        // Check domain whitelist
        $allowedDomains = Setting::get('google_allowed_domains');
        if ($allowedDomains) {
            $domains = array_filter(array_map('trim', explode("\n", $allowedDomains)));
            $emailDomain = substr(strrchr($googleUser->getEmail(), '@'), 1);
            if (!empty($domains) && !in_array($emailDomain, $domains)) {
                return redirect()->route('login')->with('error', 'Domain email tidak diizinkan.');
            }
        }

        // Find or create social account
        $socialAccount = SocialAccount::where('provider', 'google')
            ->where('provider_id', $googleUser->getId())
            ->first();

        if ($socialAccount) {
            Auth::guard('member')->login($socialAccount->member);
            return $this->redirectAfterLogin($socialAccount->member);
        }

        // Check if member exists with same email
        $member = Member::where('email', $googleUser->getEmail())->first();

        if ($member) {
            $member->socialAccounts()->create([
                'provider' => 'google',
                'provider_id' => $googleUser->getId(),
                'provider_email' => $googleUser->getEmail(),
                'provider_avatar' => $googleUser->getAvatar(),
            ]);
            Auth::guard('member')->login($member);
            return $this->redirectAfterLogin($member);
        }

        // Create new member with incomplete profile
        $member = Member::create([
            'name' => $googleUser->getName(),
            'email' => $googleUser->getEmail(),
            'is_active' => true,
            'profile_completed' => false,
            'register_date' => now(),
        ]);

        $member->socialAccounts()->create([
            'provider' => 'google',
            'provider_id' => $googleUser->getId(),
            'provider_email' => $googleUser->getEmail(),
            'provider_avatar' => $googleUser->getAvatar(),
        ]);

        Auth::guard('member')->login($member);
        return redirect()->route('member.complete-profile');
    }

    protected function redirectAfterLogin(Member $member)
    {
        if (!$member->profile_completed) {
            return redirect()->route('member.complete-profile');
        }
        return redirect()->route('member.dashboard');
    }
}
