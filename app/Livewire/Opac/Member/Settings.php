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
            // Allow NIM that exists in SIAKAD (will be merged)
            $rules['nim'] = 'required|string|max:30';
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
            // Check if NIM exists in another member record
            $existingMember = \App\Models\Member::where('member_id', $this->nim)
                ->where('id', '!=', $this->member->id)
                ->first();
            
            if ($existingMember) {
                // If has SIAKAD data (pddikti_id), merge
                if ($existingMember->pddikti_id) {
                    // Merge: transfer current member's data to SIAKAD member
                    $existingMember->update([
                        'email' => $this->member->email,
                        'phone' => $this->phone,
                        'gender' => $this->gender,
                        'photo' => $this->photo 
                            ? $this->photo->store('members', 'public') 
                            : ($this->member->photo ?? $existingMember->photo),
                        'profile_completed' => true,
                    ]);
                    
                    // Transfer social accounts
                    \App\Models\SocialAccount::where('member_id', $this->member->id)
                        ->update(['member_id' => $existingMember->id]);
                    
                    // Delete old member record
                    $oldMemberId = $this->member->id;
                    
                    // Re-login with merged account
                    Auth::guard('member')->login($existingMember);
                    
                    // Delete after re-login
                    \App\Models\Member::find($oldMemberId)?->delete();
                    
                    $this->dispatch('notify', type: 'success', message: 'NIM berhasil diperbarui dan data SIAKAD telah ditautkan.');
                    
                    return redirect()->route('member.settings');
                } else {
                    // NIM used by non-SIAKAD member, reject
                    $this->addError('nim', 'NIM sudah digunakan oleh anggota lain.');
                    return;
                }
            }
            
            // NIM not used, just update
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
