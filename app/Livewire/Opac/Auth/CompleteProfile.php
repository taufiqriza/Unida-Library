<?php

namespace App\Livewire\Opac\Auth;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\MemberType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class CompleteProfile extends Component
{
    use WithFileUploads;

    public $member;
    public string $nim = '';
    public ?int $branch_id = null;
    public ?int $member_type_id = null;
    public ?int $faculty_id = null;
    public ?int $department_id = null;
    public string $phone = '';
    public string $gender = '';
    public $photo;

    public $branches;
    public $faculties;
    public $memberTypes;
    public $departments = [];

    public function mount()
    {
        $this->member = Auth::guard('member')->user();
        
        if ($this->member->profile_completed) {
            return redirect()->route('member.dashboard');
        }

        $this->nim = $this->member->member_id ?? '';
        $this->phone = $this->member->phone ?? '';
        $this->gender = $this->member->gender ?? '';
        
        $this->branches = Branch::where('is_active', true)->orderBy('name')->get();
        $this->faculties = Faculty::orderBy('name')->get();
        $this->memberTypes = MemberType::orderBy('name')->distinct()->get(['id', 'name']);
    }

    public function updatedFacultyId($value)
    {
        $this->departments = $value 
            ? Department::where('faculty_id', $value)->orderBy('name')->get()
            : [];
        $this->department_id = null;
    }

    protected function rules()
    {
        return [
            'nim' => 'required|string|max:30|unique:members,member_id,' . $this->member->id,
            'branch_id' => 'required|exists:branches,id',
            'member_type_id' => 'required|exists:member_types,id',
            'faculty_id' => 'required|exists:faculties,id',
            'department_id' => 'required|exists:departments,id',
            'phone' => 'required|string|max:20',
            'gender' => 'required|in:M,F',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }

    protected $messages = [
        'nim.required' => 'NIM wajib diisi',
        'nim.unique' => 'NIM sudah terdaftar',
        'photo.image' => 'File harus berupa gambar',
        'photo.max' => 'Ukuran foto maksimal 2MB',
    ];

    public function save()
    {
        $this->validate();

        $data = [
            'member_id' => $this->nim,
            'branch_id' => $this->branch_id,
            'member_type_id' => $this->member_type_id,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'profile_completed' => true,
        ];

        if ($this->photo) {
            $data['photo'] = $this->photo->store('members', 'public');
        }

        $this->member->update($data);

        return redirect()->route('member.dashboard')
            ->with('success', 'Profil berhasil dilengkapi.');
    }

    public function render()
    {
        return view('livewire.opac.auth.complete-profile')
            ->layout('components.opac.layout', ['title' => 'Lengkapi Profil']);
    }
}
