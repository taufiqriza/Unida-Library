<?php

namespace App\Livewire\Opac\Auth;

use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Login extends Component
{
    public string $identifier = '';
    public string $password = '';
    public bool $remember = false;
    
    // Portal selection
    public bool $showPortalChoice = false;
    public ?array $availablePortals = null;

    protected $rules = [
        'identifier' => 'required',
        'password' => 'required',
    ];

    protected $messages = [
        'identifier.required' => 'Email atau No. Anggota wajib diisi',
        'password.required' => 'Password wajib diisi',
    ];

    public function login()
    {
        \Log::info('LOGIN CALLED', ['identifier' => $this->identifier]);
        
        $this->validate();

        $staff = null;
        $member = null;

        // Check if email format
        if (str_contains($this->identifier, '@')) {
            // Check staff account
            $staff = User::where('email', $this->identifier)
                ->whereIn('role', ['super_admin', 'admin', 'librarian', 'staff', 'pustakawan'])
                ->first();
            
            if ($staff && !Hash::check($this->password, $staff->password)) {
                $staff = null;
            }
            
            // Check member account
            $member = Member::where('email', $this->identifier)->first();
            if ($member && !Hash::check($this->password, $member->password)) {
                $member = null;
            }
        } else {
            // Member ID login - only check member
            $member = Member::where('member_id', $this->identifier)->first();
            if ($member && !Hash::check($this->password, $member->password)) {
                $member = null;
            }
        }

        // No valid account found
        if (!$staff && !$member) {
            Log::channel('daily')->warning('Login failed', [
                'identifier' => $this->identifier,
                'ip' => request()->ip(),
            ]);
            $this->addError('identifier', 'Email/No. Anggota atau password salah');
            return;
        }

        // Both accounts exist - show portal choice
        if ($staff && $member) {
            // Validate staff status first
            if ($staff->status === 'pending' || $staff->status === 'rejected' || !$staff->is_active) {
                $staff = null;
            }
            // Validate member status
            if ($member->email_verified !== 'verified') {
                // If only member needs verification, redirect
                if (!$staff) {
                    session(['pending_member_id' => $member->id]);
                    return redirect()->route('opac.verify-email');
                }
                $member = null;
            }
            
            // Still both valid? Show choice
            if ($staff && $member) {
                $this->availablePortals = [
                    'staff' => ['name' => $staff->name, 'role' => $staff->role],
                    'member' => ['name' => $member->name, 'member_id' => $member->member_id],
                ];
                $this->showPortalChoice = true;
                return;
            }
        }

        // Only staff account
        if ($staff) {
            return $this->loginAsStaff($staff);
        }

        // Only member account
        if ($member) {
            return $this->loginAsMember($member);
        }
    }

    public function selectPortal(string $portal)
    {
        if ($portal === 'staff') {
            $staff = User::where('email', $this->identifier)->first();
            return $this->loginAsStaff($staff);
        } else {
            $member = Member::where('email', $this->identifier)->first();
            return $this->loginAsMember($member);
        }
    }

    protected function loginAsStaff(User $user)
    {
        if ($user->status === 'pending') {
            $this->addError('identifier', 'Akun Anda masih menunggu persetujuan admin.');
            return;
        }
        if ($user->status === 'rejected') {
            $this->addError('identifier', 'Pendaftaran Anda ditolak. Silakan hubungi admin.');
            return;
        }
        if (!$user->is_active) {
            $this->addError('identifier', 'Akun Anda tidak aktif.');
            return;
        }

        Log::channel('daily')->info('Staff login attempt', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => request()->ip(),
        ]);
        
        Auth::guard('web')->login($user, $this->remember);
        
        Log::channel('daily')->info('After Auth::login', [
            'auth_check' => Auth::guard('web')->check(),
            'auth_id' => Auth::guard('web')->id(),
            'session_id' => session()->getId(),
        ]);
        
        session()->regenerate();
        
        Log::channel('daily')->info('After session regenerate', [
            'auth_check' => Auth::guard('web')->check(),
            'new_session_id' => session()->getId(),
        ]);
        
        return redirect()->route('staff.dashboard');
    }

    protected function loginAsMember(Member $member)
    {
        if ($member->email_verified !== 'verified') {
            session(['pending_member_id' => $member->id]);
            return redirect()->route('opac.verify-email');
        }

        Log::channel('daily')->info('Member login success', [
            'member_id' => $member->member_id,
            'ip' => request()->ip(),
        ]);
        
        Auth::guard('member')->login($member, $this->remember);
        session()->regenerate();
        return redirect()->route('opac.member.dashboard');
    }

    public function cancelPortalChoice()
    {
        $this->showPortalChoice = false;
        $this->availablePortals = null;
    }

    public function render()
    {
        return view('livewire.opac.auth.login')
            ->layout('components.opac.layout', ['title' => 'Login']);
    }
}
