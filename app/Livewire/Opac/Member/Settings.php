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
        
        $this->branches = Branch::where('is_active', true)->orderBy('name')->get();
        $this->faculties = Faculty::orderBy('name')->get();
        $this->memberTypes = MemberType::orderBy('name')->distinct()->get(['id', 'name']);
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:150',
            'phone' => 'required|string|max:20',
            'gender' => 'required|in:M,F',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }

    protected $messages = [
        'name.required' => 'Nama Lengkap wajib diisi',
        'phone.required' => 'Nomor Telepon/WA wajib diisi',
        'photo.image' => 'File harus berupa gambar',
        'photo.max' => 'Ukuran foto maksimal 2MB',
    ];

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'phone' => $this->phone,
            'gender' => $this->gender,
        ];

        if ($this->photo) {
            // Delete old photo if exists
            if ($this->member->photo && Storage::disk('public')->exists($this->member->photo)) {
                Storage::disk('public')->delete($this->member->photo);
            }
            $data['photo'] = $this->photo->store('members', 'public');
        }

        $this->member->update($data);

        $this->dispatch('notify', type: 'success', message: 'Profil berhasil diperbarui.');
    }

    public function render()
    {
        return view('livewire.opac.member.settings')
            ->layout('components.opac.layout', ['title' => 'Pengaturan Profil']);
    }
}
