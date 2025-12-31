<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Member;
use App\Models\MemberDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AuthController extends BaseController
{
    public function login(Request $request)
    {
        $request->validate([
            'member_id' => 'required|string',
            'password' => 'required|string',
            'device_name' => 'nullable|string|max:255',
            'fcm_token' => 'nullable|string|max:500',
        ]);

        $member = Member::where('member_id', $request->member_id)
            ->orWhere('email', $request->member_id)
            ->first();

        if (!$member || !Hash::check($request->password, $member->password)) {
            return $this->error('NIM/Email atau password salah', 401);
        }

        if (!$member->is_active) {
            return $this->error('Akun tidak aktif. Hubungi perpustakaan.', 401);
        }

        // Create token
        $token = $member->createToken($request->device_name ?? 'mobile-app')->plainTextToken;

        // Register FCM token if provided
        if ($request->fcm_token) {
            $this->saveFcmToken($member, $request);
        }

        return $this->success([
            'token' => $token,
            'token_type' => 'Bearer',
            'member' => $this->formatMember($member),
        ]);
    }

    public function logout(Request $request)
    {
        // Remove FCM token
        if ($request->fcm_token) {
            MemberDevice::where('fcm_token', $request->fcm_token)->delete();
        }

        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Berhasil logout');
    }

    public function me(Request $request)
    {
        $member = $request->user();
        $member->load(['memberType', 'faculty', 'department', 'branch']);

        return $this->success($this->formatMember($member, true));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $member = $request->user();
        $member->update($request->only(['phone', 'address']));

        return $this->success($this->formatMember($member), 'Profil berhasil diperbarui');
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|max:2048',
        ]);

        $member = $request->user();

        if ($member->photo) {
            Storage::disk('public')->delete($member->photo);
        }

        $path = $request->file('photo')->store('members/photos', 'public');
        $member->update(['photo' => $path]);

        return $this->success([
            'photo_url' => Storage::disk('public')->url($path),
        ], 'Foto profil berhasil diperbarui');
    }

    public function registerFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string|max:500',
            'platform' => 'required|in:android,ios',
            'device_name' => 'nullable|string|max:255',
        ]);

        $this->saveFcmToken($request->user(), $request);

        return $this->success(null, 'FCM token berhasil didaftarkan');
    }

    public function removeFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        MemberDevice::where('member_id', $request->user()->id)
            ->where('fcm_token', $request->fcm_token)
            ->delete();

        return $this->success(null, 'FCM token berhasil dihapus');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $member = Member::where('email', $request->email)->first();

        if (!$member) {
            return $this->error('Email tidak terdaftar', 404);
        }

        // TODO: Send reset password email
        // For now, just return success
        return $this->success(null, 'Link reset password telah dikirim ke email');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // TODO: Implement reset password logic
        return $this->error('Fitur belum tersedia', 501);
    }

    protected function saveFcmToken(Member $member, Request $request): void
    {
        MemberDevice::updateOrCreate(
            ['fcm_token' => $request->fcm_token],
            [
                'member_id' => $member->id,
                'device_name' => $request->device_name,
                'platform' => $request->platform ?? 'android',
                'last_active_at' => now(),
            ]
        );
    }

    protected function formatMember(Member $member, bool $withStats = false): array
    {
        $data = [
            'id' => $member->id,
            'member_id' => $member->member_id,
            'name' => $member->name,
            'email' => $member->email,
            'phone' => $member->phone,
            'photo_url' => $member->photo ? Storage::disk('public')->url($member->photo) : null,
            'gender' => $member->gender,
            'birth_date' => $member->birth_date?->format('Y-m-d'),
            'address' => $member->address,
            'member_type' => $member->memberType ? [
                'id' => $member->memberType->id,
                'name' => $member->memberType->name,
                'loan_limit' => $member->memberType->loan_limit,
                'loan_period' => $member->memberType->loan_period,
            ] : null,
            'faculty' => $member->faculty ? ['id' => $member->faculty->id, 'name' => $member->faculty->name] : null,
            'department' => $member->department ? ['id' => $member->department->id, 'name' => $member->department->name] : null,
            'branch' => $member->branch ? ['id' => $member->branch->id, 'name' => $member->branch->name] : null,
            'register_date' => $member->register_date?->format('Y-m-d'),
            'expire_date' => $member->expire_date?->format('Y-m-d'),
            'is_active' => $member->is_active,
            'is_expired' => $member->isExpired(),
        ];

        if ($withStats) {
            $data['stats'] = [
                'active_loans' => $member->loans()->where('is_returned', false)->count(),
                'total_loans' => $member->loans()->count(),
                'unpaid_fines' => $member->fines()->where('is_paid', false)->sum('amount') - $member->fines()->where('is_paid', false)->sum('paid_amount'),
            ];
        }

        return $data;
    }
}
