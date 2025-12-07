<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'member_id' => 'required|string',
            'password' => 'required|string',
        ]);

        $member = Member::where('member_id', $request->member_id)
            ->orWhere('email', $request->member_id)
            ->first();

        if (!$member || !Hash::check($request->password, $member->password)) {
            throw ValidationException::withMessages([
                'member_id' => ['ID atau password salah.'],
            ]);
        }

        if (!$member->is_active) {
            throw ValidationException::withMessages([
                'member_id' => ['Akun tidak aktif.'],
            ]);
        }

        $token = $member->createToken('opac')->plainTextToken;

        return response()->json([
            'token' => $token,
            'member' => [
                'id' => $member->id,
                'member_id' => $member->member_id,
                'name' => $member->name,
                'email' => $member->email,
                'photo' => $member->photo ? asset('storage/' . $member->photo) : null,
                'member_type' => $member->memberType?->name,
                'expire_date' => $member->expire_date?->format('Y-m-d'),
                'is_expired' => $member->isExpired(),
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    public function me(Request $request)
    {
        $member = $request->user();
        $member->load('memberType');

        return response()->json([
            'id' => $member->id,
            'member_id' => $member->member_id,
            'name' => $member->name,
            'email' => $member->email,
            'phone' => $member->phone,
            'address' => $member->address,
            'photo' => $member->photo ? asset('storage/' . $member->photo) : null,
            'member_type' => $member->memberType?->name,
            'register_date' => $member->register_date?->format('Y-m-d'),
            'expire_date' => $member->expire_date?->format('Y-m-d'),
            'is_expired' => $member->isExpired(),
            'loan_limit' => $member->memberType?->loan_limit ?? 3,
            'active_loans' => $member->loans()->where('is_returned', false)->count(),
        ]);
    }
}
