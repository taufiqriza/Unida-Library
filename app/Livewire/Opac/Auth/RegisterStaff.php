<?php

namespace App\Livewire\Opac\Auth;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class RegisterStaff extends Component
{
    public string $name = '';
    public string $email = '';
    public ?int $branch_id = null;
    public string $password = '';
    public string $password_confirmation = '';

    public function register()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'branch_id' => 'required|exists:branches,id',
            'password' => 'required|min:8|confirmed',
        ], [
            'email.unique' => 'Email sudah terdaftar',
            'branch_id.required' => 'Pilih cabang perpustakaan',
        ]);

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'branch_id' => $this->branch_id,
            'role' => 'staff',
            'is_active' => false,
            'status' => 'pending',
        ]);

        return redirect()->route('login')->with('success', 'Pendaftaran berhasil! Akun Anda akan aktif setelah disetujui oleh admin perpustakaan.');
    }

    public function render()
    {
        return view('livewire.opac.auth.register-staff', [
            'branches' => Branch::where('is_active', true)->orderBy('name')->get(),
        ])->layout('components.opac.layout', ['title' => 'Daftar Staff']);
    }
}
