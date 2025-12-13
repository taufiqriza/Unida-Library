<?php

namespace App\Livewire\Staff\Profile;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use Livewire\WithFileUploads;

class StaffProfile extends Component
{
    use WithFileUploads;

    public $name;
    public $email;
    public $photo;
    
    public $current_password = '';
    public $password = '';
    public $password_confirmation = '';
    public $showPasswordForm = false;

    public function mount()
    {
        $this->name = auth()->user()->name;
        $this->email = auth()->user()->email;
    }

    public function updatedPhoto()
    {
        $this->validate(['photo' => 'image|max:2048']);
    }

    public function updatePhoto()
    {
        $this->validate(['photo' => 'required|image|max:2048']);

        $user = auth()->user();
        
        // Delete old photo
        if ($user->photo && Storage::disk('public')->exists($user->photo)) {
            Storage::disk('public')->delete($user->photo);
        }

        $path = $this->photo->store('staff-photos', 'public');
        $user->update(['photo' => $path]);
        
        $this->photo = null;
        $this->dispatch('notify', type: 'success', message: 'Foto profil berhasil diperbarui');
    }

    public function removePhoto()
    {
        $user = auth()->user();
        
        if ($user->photo && Storage::disk('public')->exists($user->photo)) {
            Storage::disk('public')->delete($user->photo);
        }
        
        $user->update(['photo' => null]);
        $this->dispatch('notify', type: 'success', message: 'Foto profil berhasil dihapus');
    }

    public function updateProfile()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
        ]);
        auth()->user()->update(['name' => $this->name, 'email' => $this->email]);
        $this->dispatch('notify', type: 'success', message: 'Profil berhasil diperbarui');
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = auth()->user();

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Password lama tidak sesuai');
            return;
        }

        $user->update(['password' => $this->password]);
        $this->reset(['current_password', 'password', 'password_confirmation']);
        $this->showPasswordForm = false;
        $this->dispatch('notify', type: 'success', message: 'Password berhasil diubah');
    }

    public function unlinkGoogle()
    {
        auth()->user()->socialAccounts()->where('provider', 'google')->delete();
        $this->dispatch('notify', type: 'success', message: 'Akun Google berhasil diputuskan');
    }

    public function render()
    {
        $user = auth()->user();
        
        return view('livewire.staff.profile.staff-profile', [
            'user' => $user,
            'branch' => $user->branch,
            'googleAccount' => $user->socialAccounts()->where('provider', 'google')->first(),
        ])->extends('staff.layouts.app')->section('content');
    }
}
