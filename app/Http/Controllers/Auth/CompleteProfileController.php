<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Member;
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
            'nim' => 'required|string|max:30|unique:members,member_id,' . $member->id,
            'identity_number' => 'required|string|max:50',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'gender' => 'required|in:L,P',
            'birth_date' => 'required|date',
        ], [
            'nim.required' => 'NIM wajib diisi',
            'nim.unique' => 'NIM sudah terdaftar',
        ]);

        $member->update([
            'member_id' => $validated['nim'],
            'identity_number' => $validated['identity_number'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'gender' => $validated['gender'],
            'birth_date' => $validated['birth_date'],
            'profile_completed' => true,
        ]);

        return redirect()->route('member.dashboard')
            ->with('success', 'Profil berhasil dilengkapi.');
    }
}
