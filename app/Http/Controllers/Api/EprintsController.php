<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EprintsController extends Controller
{
    /**
     * Verify member credentials for EPrints SSO
     */
    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
        ]);

        if ($request->token !== config('services.eprints.api_token')) {
            Log::channel('security')->warning('EPrints API: Invalid token', ['ip' => $request->ip()]);
            return response()->json(['valid' => false, 'error' => 'Invalid token'], 401);
        }

        $member = Member::where('email', $request->email)->where('is_active', true)->first();

        if (!$member) {
            return response()->json(['valid' => false, 'error' => 'Member not found']);
        }

        return response()->json([
            'valid' => true,
            'user' => [
                'email' => $member->email,
                'name' => $member->name,
                'member_id' => $member->member_id,
                'type' => $member->memberType?->name ?? 'Member',
            ],
        ]);
    }

    /**
     * Verify SSO login token
     */
    public function verifyLoginToken(Request $request)
    {
        $request->validate([
            'login_token' => 'required|string',
            'api_token' => 'required|string',
        ]);

        if ($request->api_token !== config('services.eprints.api_token')) {
            return response()->json(['valid' => false, 'error' => 'Invalid API token'], 401);
        }

        $cacheKey = 'eprints_login_' . $request->login_token;
        $data = Cache::get($cacheKey);

        if (!$data) {
            return response()->json(['valid' => false, 'error' => 'Token expired or invalid']);
        }

        // Delete token after use (one-time use)
        Cache::forget($cacheKey);

        $member = Member::where('email', $data['email'])->where('is_active', true)->with(['memberType', 'faculty'])->first();

        if (!$member) {
            return response()->json(['valid' => false, 'error' => 'Member not found']);
        }

        return response()->json([
            'valid' => true,
            'user' => [
                'email' => $member->email,
                'username' => $member->email,
                'name_given' => explode(' ', $member->name)[0] ?? $member->name,
                'name_family' => implode(' ', array_slice(explode(' ', $member->name), 1)) ?: '-',
                'usertype' => $this->mapUserType($member->memberType?->code),
                'dept' => $member->faculty?->name ?? 'UNIDA',
            ],
        ]);
    }

    /**
     * Generate SSO login token for member
     */
    public function generateLoginToken(Request $request)
    {
        $member = auth('member')->user();
        
        if (!$member) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $token = Str::random(64);
        $cacheKey = 'eprints_login_' . $token;
        
        // Store token for 30 minutes
        Cache::put($cacheKey, ['email' => $member->email, 'created_at' => now()], 1800);

        $eprintsUrl = config('services.eprints.base_url', 'https://repo.unida.gontor.ac.id');
        
        return response()->json([
            'redirect_url' => $eprintsUrl . '/cgi/library_sso?token=' . $token,
        ]);
    }

    private function mapUserType(?string $code): string
    {
        return match ($code) {
            'DSN' => 'editor',
            default => 'user',
        };
    }
}
