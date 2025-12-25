<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureStaffAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        
        if (!$user || !in_array($user->role, ['super_admin', 'admin', 'librarian', 'staff'])) {
            abort(403, 'Akses ditolak. Anda bukan staff perpustakaan.');
        }

        if ($user->status !== 'approved' || !$user->is_active) {
            auth()->logout();
            return redirect()->route('login')->withErrors(['email' => 'Akun Anda belum disetujui atau tidak aktif.']);
        }

        return $next($request);
    }
}
