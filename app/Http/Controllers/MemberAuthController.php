<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
                Auth::guard('member')->login($member);
                return redirect()->route('opac.member.dashboard');
            }

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
                'password' => 'required|min:6|confirmed',
            ]);

            $member = Member::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'member_id' => 'M' . date('Ymd') . rand(1000, 9999),
                'register_date' => now(),
                'expire_date' => now()->addYear(),
                'is_active' => true,
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
        $fines = $member->fines()->where('status', 'unpaid')->get();

        return view('opac.member-dashboard', compact('member', 'loans', 'history', 'fines'));
    }
}
