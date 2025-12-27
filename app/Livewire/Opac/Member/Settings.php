<?php

namespace App\Livewire\Opac\Member;

use App\Models\Branch;
use App\Models\Faculty;
use App\Models\MemberType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Settings extends Component
{
    use WithFileUploads;

    public $member;
    public string $name = '';
    public string $phone = '';
    public string $gender = '';
    public string $nim = '';
    public bool $canEditNim = false;
    public $photo;
    
    public $branches;
    public $faculties;
    public $memberTypes;

    public function mount()
    {
        $this->member = Auth::guard('member')->user();
        $this->name = $this->member->name ?? '';
        $this->phone = $this->member->phone ?? '';
        $this->gender = $this->member->gender ?? '';
        $this->nim = $this->member->member_id ?? '';
        
        // Allow NIM edit if it's auto-generated (starts with M2025, M2024, etc)
        $this->canEditNim = preg_match('/^M20\d{2}/', $this->nim);
        
        $this->branches = Branch::where('is_active', true)->orderBy('name')->get();
        $this->faculties = Faculty::orderBy('name')->get();
        $this->memberTypes = MemberType::orderBy('name')->distinct()->get(['id', 'name']);
    }

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:150',
            'phone' => 'required|string|max:20',
            'gender' => 'required|in:M,F',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
        
        if ($this->canEditNim) {
            $rules['nim'] = 'required|string|max:30|unique:members,member_id,' . $this->member->id;
        }
        
        return $rules;
    }

    protected $messages = [
        'name.required' => 'Nama Lengkap wajib diisi',
        'phone.required' => 'Nomor Telepon/WA wajib diisi',
        'photo.image' => 'File harus berupa gambar',
        'photo.max' => 'Ukuran foto maksimal 2MB',
        'nim.required' => 'NIM wajib diisi',
        'nim.unique' => 'NIM sudah terdaftar',
    ];

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'phone' => $this->phone,
            'gender' => $this->gender,
        ];
        
        if ($this->canEditNim && $this->nim) {
            $data['member_id'] = $this->nim;
            $data['nim_nidn'] = $this->nim;
        }

        if ($this->photo) {
            if ($this->member->photo && Storage::disk('public')->exists($this->member->photo)) {
                Storage::disk('public')->delete($this->member->photo);
            }
            $data['photo'] = $this->photo->store('members', 'public');
        }

        $this->member->update($data);
        
        // Refresh canEditNim after save
        $this->canEditNim = preg_match('/^M20\d{2}/', $this->nim);

        $this->dispatch('notify', type: 'success', message: 'Profil berhasil diperbarui.');
    }

    public function render()
    {
        return view('livewire.opac.member.settings')
            ->layout('components.opac.layout', ['title' => 'Pengaturan Profil']);
    }
}
