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
        $this->validate();

        // Auto-detect: Email with @ = check staff first, then member
        if (str_contains($this->identifier, '@')) {
            // Try staff login first
            $user = User::where('email', $this->identifier)->first();
            if ($user && Hash::check($this->password, $user->password)) {
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
                
                if (in_array($user->role, ['super_admin', 'admin', 'librarian', 'staff'])) {
                    Log::channel('daily')->info('Staff login success', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'ip' => request()->ip(),
                    ]);
                    Auth::guard('web')->login($user, $this->remember);
                    session()->regenerate();
                    return redirect()->route('staff.dashboard');
                }
            }
        }

        // Try member login
        $member = Member::where('member_id', $this->identifier)
            ->orWhere('email', $this->identifier)
            ->first();

        if ($member && Hash::check($this->password, $member->password)) {
            // Check email verification
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

        Log::channel('daily')->warning('Login failed', [
            'identifier' => $this->identifier,
            'ip' => request()->ip(),
        ]);

        $this->addError('identifier', 'Email/No. Anggota atau password salah');
    }

    public function render()
    {
        return view('livewire.opac.auth.login')
            ->layout('components.opac.layout', ['title' => 'Login']);
    }
}
