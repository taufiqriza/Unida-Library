<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Setting;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirect()
    {
        if (!Setting::get('google_enabled')) {
            return redirect()->route('login')->with('error', 'Google login tidak aktif.');
        }
        
        // Store intent in session
        if (request('link_staff')) {
            session(['google_intent' => 'link_staff', 'link_user_id' => auth()->id()]);
        }
        
        // Use prompt=select_account to force account picker
        return Socialite::driver('google')
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    public function callback()
    {
        if (!Setting::get('google_enabled')) {
            return redirect()->route('login')->with('error', 'Google login tidak aktif.');
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            Log::error('Google OAuth error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Gagal login dengan Google.');
        }

        // Check domain whitelist
        if (!$this->isAllowedDomain($googleUser->getEmail())) {
            return redirect()->route('login')->with('error', 'Domain email tidak diizinkan.');
        }

        // Handle staff linking
        if (session('google_intent') === 'link_staff') {
            return $this->handleStaffLinking($googleUser);
        }

        return $this->handleNormalLogin($googleUser);
    }

    protected function handleNormalLogin($googleUser)
    {
        // Find linked accounts
        $staffAccount = SocialAccount::where('provider', 'google')
            ->where('provider_id', $googleUser->getId())
            ->whereNotNull('user_id')
            ->with('user')
            ->first();

        $memberAccount = SocialAccount::where('provider', 'google')
            ->where('provider_id', $googleUser->getId())
            ->whereNotNull('member_id')
            ->with('member')
            ->first();

        // Also check by email match
        $userByEmail = User::where('email', $googleUser->getEmail())->first();
        $memberByEmail = Member::where('email', $googleUser->getEmail())->first();

        // Auto-link if found by email but not yet linked
        if ($userByEmail && !$staffAccount) {
            $staffAccount = SocialAccount::updateOrCreate(
                ['provider' => 'google', 'provider_id' => $googleUser->getId(), 'user_id' => $userByEmail->id],
                ['provider_email' => $googleUser->getEmail(), 'provider_avatar' => $googleUser->getAvatar()]
            );
        }

        if ($memberByEmail && !$memberAccount) {
            $memberAccount = SocialAccount::updateOrCreate(
                ['provider' => 'google', 'provider_id' => $googleUser->getId(), 'member_id' => $memberByEmail->id],
                ['provider_email' => $googleUser->getEmail(), 'provider_avatar' => $googleUser->getAvatar()]
            );
        }

        $hasStaff = ($staffAccount && $staffAccount->user) || $userByEmail;
        $hasMember = ($memberAccount && $memberAccount->member) || $memberByEmail;

        // Both roles - show chooser
        if ($hasStaff && $hasMember) {
            session([
                'google_user_id' => $googleUser->getId(),
                'google_staff_id' => $staffAccount?->user_id ?? $userByEmail?->id,
                'google_member_id' => $memberAccount?->member_id ?? $memberByEmail?->id,
            ]);
            return redirect()->route('auth.choose-role');
        }

        // Staff only
        if ($hasStaff) {
            $user = $staffAccount?->user ?? $userByEmail;
            Auth::login($user);
            return redirect()->route('staff.dashboard');
        }

        // Member only
        if ($hasMember) {
            $member = $memberAccount?->member ?? $memberByEmail;
            Auth::guard('member')->login($member);
            return $this->redirectAfterLogin($member);
        }

        // No account - create new member
        $member = Member::create([
            'member_id' => $this->generateUniqueMemberId(),
            'name' => $googleUser->getName(),
            'email' => $googleUser->getEmail(),
            'member_type_id' => 1,
            'is_active' => true,
            'profile_completed' => false,
            'register_date' => now(),
            'expire_date' => now()->addYear(),
        ]);

        SocialAccount::create([
            'member_id' => $member->id,
            'provider' => 'google',
            'provider_id' => $googleUser->getId(),
            'provider_email' => $googleUser->getEmail(),
            'provider_avatar' => $googleUser->getAvatar(),
        ]);

        Auth::guard('member')->login($member);
        return redirect()->route('member.complete-profile');
    }

    public function chooseRole()
    {
        if (!session('google_user_id')) {
            return redirect()->route('login');
        }
        
        $staffId = session('google_staff_id');
        $memberId = session('google_member_id');
        
        $staff = $staffId ? User::find($staffId) : null;
        $member = $memberId ? Member::find($memberId) : null;

        return view('auth.choose-role', compact('staff', 'member'));
    }

    public function selectRole($role)
    {
        if (!session('google_user_id')) {
            return redirect()->route('login');
        }

        $staffId = session('google_staff_id');
        $memberId = session('google_member_id');

        session()->forget(['google_user_id', 'google_staff_id', 'google_member_id']);

        if ($role === 'staff' && $staffId) {
            $user = User::find($staffId);
            if ($user) {
                Auth::login($user);
                return redirect()->route('staff.dashboard');
            }
        }

        if ($role === 'member' && $memberId) {
            $member = Member::find($memberId);
            if ($member) {
                Auth::guard('member')->login($member);
                return $this->redirectAfterLogin($member);
            }
        }

        return redirect()->route('login')->with('error', 'Gagal login.');
    }

    public function switchPortal($role)
    {
        $email = null;
        
        // Get email from current session
        if (Auth::check()) {
            $email = Auth::user()->email;
            Auth::logout();
        } elseif (Auth::guard('member')->check()) {
            $email = Auth::guard('member')->user()->email;
            Auth::guard('member')->logout();
        }

        if (!$email) {
            return redirect()->route('login');
        }

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        if ($role === 'member') {
            $member = Member::where('email', $email)->first();
            if ($member) {
                Auth::guard('member')->login($member);
                return redirect()->route('member.dashboard');
            }
        }

        if ($role === 'staff') {
            $user = User::where('email', $email)->first();
            if ($user) {
                Auth::login($user);
                return redirect()->route('staff.dashboard');
            }
        }

        return redirect()->route('login');
    }

    protected function handleStaffLinking($googleUser)
    {
        $userId = session('link_user_id');
        session()->forget(['google_intent', 'link_user_id']);

        if (!$userId) {
            return redirect()->route('staff.profile')->with('error', 'Sesi tidak valid.');
        }

        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('staff.profile')->with('error', 'User tidak ditemukan.');
        }

        // Check if Google account already linked to another staff user
        $existingStaff = SocialAccount::where('provider', 'google')
            ->where('provider_id', $googleUser->getId())
            ->whereNotNull('user_id')
            ->where('user_id', '!=', $user->id)
            ->first();

        if ($existingStaff) {
            return redirect()->route('staff.profile')->with('error', 'Akun Google sudah terhubung ke akun staf lain.');
        }

        // Link Google account to staff user
        SocialAccount::updateOrCreate(
            ['provider' => 'google', 'provider_id' => $googleUser->getId(), 'user_id' => $user->id],
            ['provider_email' => $googleUser->getEmail(), 'provider_avatar' => $googleUser->getAvatar()]
        );

        // Also check if there's a matching Member by email and link
        $member = Member::where('email', $googleUser->getEmail())->first();
        if (!$member) {
            // Check if staff email matches any imported student by NIM pattern in email
            // e.g., 432022111002@student.unida.gontor.ac.id
            $emailParts = explode('@', $googleUser->getEmail());
            $nimOrUsername = $emailParts[0] ?? '';
            $member = Member::where('member_id', $nimOrUsername)->first();
        }

        if ($member) {
            // Link same Google to Member as well
            SocialAccount::updateOrCreate(
                ['provider' => 'google', 'provider_id' => $googleUser->getId(), 'member_id' => $member->id],
                ['provider_email' => $googleUser->getEmail(), 'provider_avatar' => $googleUser->getAvatar()]
            );
            
            // Update member email and mark profile complete if staff
            $member->update([
                'email' => $googleUser->getEmail(),
                'profile_completed' => true,
            ]);
        }

        return redirect()->route('staff.profile')->with('success', 'Akun Google berhasil dihubungkan!');
    }

    protected function isAllowedDomain(string $email): bool
    {
        $allowedDomains = Setting::get('google_allowed_domains');
        
        if (empty($allowedDomains)) {
            return true;
        }

        $domains = array_filter(array_map('trim', explode("\n", $allowedDomains)));
        
        if (empty($domains)) {
            return true;
        }

        $emailDomain = substr(strrchr($email, '@'), 1);

        foreach ($domains as $domain) {
            $domain = ltrim($domain, '@');
            if ($emailDomain === $domain) {
                return true;
            }
        }

        return false;
    }

    protected function redirectAfterLogin(Member $member)
    {
        if (!$member->profile_completed) {
            return redirect()->route('member.complete-profile');
        }
        return redirect()->route('member.dashboard');
    }

    protected function generateUniqueMemberId(): string
    {
        do {
            $id = 'M' . date('Ymd') . strtoupper(\Illuminate\Support\Str::random(4));
        } while (Member::where('member_id', $id)->exists());
        
        return $id;
    }
}
