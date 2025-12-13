<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class StaffRegisterController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'branch_id' => 'required|exists:branches,id',
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'email.unique' => 'Email sudah terdaftar',
            'branch_id.required' => 'Pilih cabang perpustakaan',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'branch_id' => $validated['branch_id'],
            'role' => 'staff',
            'is_active' => false,
            'status' => 'pending',
        ]);

        return redirect()->route('login')->with('success', 'Pendaftaran berhasil! Akun Anda akan aktif setelah disetujui oleh admin perpustakaan.');
    }
}
