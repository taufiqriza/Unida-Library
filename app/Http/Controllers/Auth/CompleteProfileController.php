<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompleteProfileController extends Controller
{
    public function show()
    {
        $member = Auth::guard('member')->user();
        if ($member->profile_completed) {
            return redirect()->route('member.dashboard');
        }
        return view('auth.complete-profile', compact('member'));
    }

    public function update(Request $request)
    {
        $member = Auth::guard('member')->user();

        $validated = $request->validate([
            'identity_number' => 'required|string|max:50',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'gender' => 'required|in:L,P',
            'birth_date' => 'required|date',
        ]);

        $member->update([
            ...$validated,
            'profile_completed' => true,
        ]);

        return redirect()->route('member.dashboard')
            ->with('success', 'Profil berhasil dilengkapi.');
    }
}
