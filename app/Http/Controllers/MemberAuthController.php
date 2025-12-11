<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class MemberAuthController extends Controller
{
    public function login(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'identifier' => 'required',
                'password' => 'required',
            ]);

            $identifier = $request->identifier;
            $password = $request->password;

            // Auto-detect: Email with @ = check staff first, then member
            if (str_contains($identifier, '@')) {
                // Try staff login first
                $user = \App\Models\User::where('email', $identifier)->first();
                if ($user && Hash::check($password, $user->password)) {
                    if (in_array($user->role, ['super_admin', 'admin', 'librarian'])) {
                        Log::channel('daily')->info('Staff login success', [
                            'user_id' => $user->id,
                            'email' => $user->email,
                            'ip' => $request->ip(),
                        ]);
                        Auth::guard('web')->login($user);
                        return redirect()->route('staff.dashboard');
                    }
                }
            }

            // Try member login
            $member = Member::where('member_id', $identifier)
                ->orWhere('email', $identifier)
                ->first();

            if ($member && Hash::check($password, $member->password)) {
                Log::channel('daily')->info('Member login success', [
                    'member_id' => $member->member_id,
                    'ip' => $request->ip(),
                ]);
                
                Auth::guard('member')->login($member);
                return redirect()->route('opac.member.dashboard');
            }

            Log::channel('daily')->warning('Login failed', [
                'identifier' => $identifier,
                'ip' => $request->ip(),
            ]);

            return back()->withErrors(['identifier' => 'Email/No. Anggota atau password salah']);
        }

        return view('opac.login');
    }

    public function register(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|max:255',
                'email' => 'required|email|unique:members,email',
                'phone' => 'nullable|max:20',
                'password' => [
                    'required',
                    'confirmed',
                    Password::min(8)->letters()->numbers(),
                ],
            ], [
                'password.min' => 'Password minimal 8 karakter',
                'password.letters' => 'Password harus mengandung huruf',
                'password.numbers' => 'Password harus mengandung angka',
            ]);

            $member = Member::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'member_id' => $this->generateUniqueMemberId(),
                'register_date' => now(),
                'expire_date' => now()->addYear(),
                'is_active' => true,
            ]);

            Log::channel('daily')->info('New member registered', [
                'member_id' => $member->member_id,
                'email' => $member->email,
                'ip' => $request->ip(),
            ]);

            Auth::guard('member')->login($member);
            return redirect()->route('opac.member.dashboard');
        }

        return view('opac.register');
    }

    public function logout()
    {
        Auth::guard('member')->logout();
        return redirect()->route('opac.home');
    }

    public function dashboard()
    {
        $member = Auth::guard('member')->user();
        $loans = $member->loans()->with('item.book')->where('is_returned', false)->get();
        $history = $member->loans()->with('item.book')->where('is_returned', true)->latest()->take(10)->get();
        $fines = $member->fines()->where('is_paid', false)->get();
        
        $submissions = $member->thesisSubmissions()->with('department')->latest()->get();
        
        $clearanceLetters = \App\Models\ClearanceLetter::where('member_id', $member->id)
            ->with('thesisSubmission')
            ->latest()
            ->get();

        return view('opac.member-dashboard', compact('member', 'loans', 'history', 'fines', 'submissions', 'clearanceLetters'));
    }

    protected function generateUniqueMemberId(): string
    {
        do {
            $id = 'M' . date('Ymd') . strtoupper(Str::random(4));
        } while (Member::where('member_id', $id)->exists());
        
        return $id;
    }
}
