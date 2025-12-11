<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureMemberProfileCompleted
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $member = Auth::guard('member')->user();

        // Jika user login sebagai member
        if ($member) {
            // Jika profil belum lengkap
            if (!$member->profile_completed) {
                // List route yang dikecualikan (Whitelist)
                // 1. Halaman form complete profile
                // 2. Action submit complete profile
                // 3. Logout (PENTING: User harus bisa logout kalau stuck)
                $exemptRoutes = [
                    'member.complete-profile',
                    'member.complete-profile.store',
                    'opac.logout',
                ];

                if (!in_array($request->route()->getName(), $exemptRoutes)) {
                    return redirect()->route('member.complete-profile');
                }
            }
        }

        return $next($request);
    }
}
