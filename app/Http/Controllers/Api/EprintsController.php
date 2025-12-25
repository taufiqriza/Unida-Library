<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class EprintsController extends Controller
{
    /**
     * Verify member credentials for EPrints SSO
     * POST /api/eprints/verify
     */
    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
        ]);

        // Verify API token
        if ($request->token !== config('services.eprints.api_token')) {
            Log::channel('security')->warning('EPrints API: Invalid token', [
                'ip' => $request->ip(),
                'email' => $request->email,
            ]);
            return response()->json(['valid' => false, 'error' => 'Invalid token'], 401);
        }

        $member = Member::where('email', $request->email)
            ->where('is_active', true)
            ->first();

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
                'faculty' => $member->faculty?->name,
                'department' => $member->department?->name,
            ],
        ]);
    }

    /**
     * Create/sync EPrints user from Laravel member
     * POST /api/eprints/sync
     */
    public function sync(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
        ]);

        if ($request->token !== config('services.eprints.api_token')) {
            return response()->json(['success' => false, 'error' => 'Invalid token'], 401);
        }

        $member = Member::where('email', $request->email)
            ->where('is_active', true)
            ->with(['memberType', 'faculty', 'department'])
            ->first();

        if (!$member) {
            return response()->json(['success' => false, 'error' => 'Member not found']);
        }

        // Return data for EPrints user creation
        return response()->json([
            'success' => true,
            'eprints_user' => [
                'username' => $member->email,
                'email' => $member->email,
                'name_given' => explode(' ', $member->name)[0] ?? $member->name,
                'name_family' => implode(' ', array_slice(explode(' ', $member->name), 1)) ?: '-',
                'usertype' => $this->mapUserType($member->memberType?->code),
                'dept' => $member->department?->name ?? $member->faculty?->name ?? 'UNIDA',
            ],
        ]);
    }

    private function mapUserType(?string $code): string
    {
        return match ($code) {
            'DSN' => 'editor',
            'MHS', 'S1', 'S2', 'S3' => 'user',
            'STF' => 'user',
            default => 'user',
        };
    }
}
