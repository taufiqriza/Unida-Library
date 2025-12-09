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

            $member = Member::where('member_id', $request->identifier)
                ->orWhere('email', $request->identifier)
                ->first();

            if ($member && Hash::check($request->password, $member->password)) {
                Log::channel('daily')->info('Member login success', [
                    'member_id' => $member->member_id,
                    'ip' => $request->ip(),
                ]);
                
                Auth::guard('member')->login($member);
                return redirect()->route('opac.member.dashboard');
            }

            Log::channel('daily')->warning('Member login failed', [
                'identifier' => $request->identifier,
                'ip' => $request->ip(),
            ]);

            return back()->withErrors(['identifier' => 'No. Anggota/Email atau password salah']);
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
