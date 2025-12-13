<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Services\OtpService;
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
                    if ($user->status === 'pending') {
                        return back()->withErrors(['email' => 'Akun Anda masih menunggu persetujuan admin.'])->withInput();
                    }
                    if ($user->status === 'rejected') {
                        return back()->withErrors(['email' => 'Pendaftaran Anda ditolak. Silakan hubungi admin.'])->withInput();
                    }
                    if (!$user->is_active) {
                        return back()->withErrors(['email' => 'Akun Anda tidak aktif.'])->withInput();
                    }
                    
                    if (in_array($user->role, ['super_admin', 'admin', 'librarian', 'staff'])) {
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
                // Check email verification
                if ($member->email_verified !== 'verified') {
                    // Store member ID in session for verification page
                    session(['pending_member_id' => $member->id]);
                    return redirect()->route('opac.verify-email');
                }

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

    public function register(Request $request, OtpService $otpService)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|max:255',
                'email' => 'required|email|unique:members,email',
                'phone' => 'nullable|max:20',
                'institution' => 'nullable|max:255',
                'institution_city' => 'nullable|max:100',
                'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            ], [
                'password.min' => 'Password minimal 8 karakter',
                'password.letters' => 'Password harus mengandung huruf',
                'password.numbers' => 'Password harus mengandung angka',
            ]);

            $email = $request->email;
            $registrationType = $otpService->detectRegistrationType($email);
            $isTrusted = $registrationType === 'internal';

            // Auto-detect institution for external (.ac.id)
            $institution = $request->institution;
            if ($registrationType === 'external' && !$institution) {
                $institution = $otpService->extractInstitution($email);
            }

            $member = Member::create([
                'name' => $request->name,
                'email' => $email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'member_id' => $this->generateUniqueMemberId(),
                'member_type_id' => \App\Models\MemberType::first()?->id ?? 1,
                'register_date' => now(),
                'expire_date' => now()->addYear(),
                'is_active' => true,
                'registration_type' => $registrationType,
                'institution' => $institution,
                'institution_city' => $request->institution_city,
                'email_verified' => $isTrusted ? 'verified' : 'pending',
                'email_verified_at' => $isTrusted ? now() : null,
            ]);

            Log::channel('daily')->info('New member registered', [
                'member_id' => $member->member_id,
                'email' => $member->email,
                'type' => $registrationType,
                'ip' => $request->ip(),
            ]);

            // Trusted domain: auto login
            if ($isTrusted) {
                Auth::guard('member')->login($member);
                return redirect()->route('opac.member.dashboard');
            }

            // Non-trusted: send OTP and redirect to verification
            $otpService->sendOtp($email, $request->name);
            session(['pending_member_id' => $member->id]);
            
            return redirect()->route('opac.verify-email');
        }

        return view('opac.register');
    }

    public function verifyEmail(Request $request, OtpService $otpService)
    {
        $memberId = session('pending_member_id');
        if (!$memberId) {
            return redirect()->route('login');
        }

        $member = Member::find($memberId);
        if (!$member || $member->email_verified === 'verified') {
            session()->forget('pending_member_id');
            return redirect()->route('login');
        }

        if ($request->isMethod('post')) {
            $request->validate(['otp' => 'required|digits:6']);

            $result = $otpService->verifyOtp($member->email, $request->otp);

            if (!$result['success']) {
                return back()->withErrors(['otp' => $result['message']]);
            }

            $member->update([
                'email_verified' => 'verified',
                'email_verified_at' => now(),
            ]);

            session()->forget('pending_member_id');
            Auth::guard('member')->login($member);

            return redirect()->route('opac.member.dashboard')
                ->with('success', 'Email berhasil diverifikasi!');
        }

        $resendInfo = $otpService->canResendOtp($member->email);

        return view('opac.verify-email', compact('member', 'resendInfo'));
    }

    public function resendOtp(OtpService $otpService)
    {
        $memberId = session('pending_member_id');
        if (!$memberId) {
            return response()->json(['success' => false, 'message' => 'Session expired']);
        }

        $member = Member::find($memberId);
        if (!$member) {
            return response()->json(['success' => false, 'message' => 'Member not found']);
        }

        $resendInfo = $otpService->canResendOtp($member->email);
        if (!$resendInfo['can_resend']) {
            return response()->json([
                'success' => false, 
                'message' => 'Tunggu sebentar sebelum mengirim ulang',
                'wait_seconds' => $resendInfo['wait_seconds']
            ]);
        }

        $otpService->sendOtp($member->email, $member->name);

        return response()->json(['success' => true, 'message' => 'Kode verifikasi telah dikirim ulang']);
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
