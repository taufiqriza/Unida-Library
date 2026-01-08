<?php

namespace App\Livewire\Staff\Member;

use App\Models\Member;
use App\Models\MemberType;
use App\Models\Branch;
use App\Models\Faculty;
use App\Models\Department;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class MemberForm extends Component
{
    use WithFileUploads;

    public ?Member $member = null;
    
    // Form fields
    public $member_id = '';
    public $name = '';
    public $gender = '';
    public $birth_date = '';
    public $address = '';
    public $city = '';
    public $phone = '';
    public $email = '';
    public $photo;
    public $existing_photo = '';
    public $branch_id = '';
    public $faculty_id = '';
    public $department_id = '';
    public $member_type_id = '';
    public $register_date = '';
    public $expire_date = '';
    public $is_active = true;
    public $notes = '';

    protected function rules()
    {
        return [
            'member_id' => 'required|max:30|unique:members,member_id,' . ($this->member?->id ?? 'NULL'),
            'name' => 'required|max:255',
            'gender' => 'nullable|in:M,F',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'city' => 'nullable|max:100',
            'phone' => 'nullable|max:30',
            'email' => 'nullable|email|max:255',
            'photo' => 'nullable|image|max:2048',
            'branch_id' => 'required|exists:branches,id',
            'faculty_id' => 'nullable|exists:faculties,id',
            'department_id' => 'nullable|exists:departments,id',
            'member_type_id' => 'required|exists:member_types,id',
            'register_date' => 'required|date',
            'expire_date' => 'required|date',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ];
    }

    public function mount($member = null): void
    {
        $user = auth()->user();
        
        // Get member from parameter or query string
        $memberId = $member ?? request()->get('member');
        
        if ($memberId) {
            $this->member = Member::findOrFail($memberId);
            
            // Check branch access
            if ($this->member->branch_id !== $user->branch_id && $user->role !== 'admin') {
                abort(403, 'Unauthorized access to this member');
            }
            
            $this->member_id = $this->member->member_id;
            $this->name = $this->member->name;
            $this->gender = $this->member->gender;
            $this->birth_date = $this->member->birth_date?->format('Y-m-d');
            $this->address = $this->member->address;
            $this->city = $this->member->city;
            $this->phone = $this->member->phone;
            $this->email = $this->member->email;
            $this->existing_photo = $this->member->photo;
            $this->branch_id = $this->member->branch_id;
            $this->faculty_id = $this->member->faculty_id;
            $this->department_id = $this->member->department_id;
            $this->member_type_id = $this->member->member_type_id;
            $this->register_date = $this->member->register_date?->format('Y-m-d');
            $this->expire_date = $this->member->expire_date?->format('Y-m-d');
            $this->is_active = $this->member->is_active;
            $this->notes = $this->member->notes;
        } else {
            // Defaults for new member
            $this->branch_id = $user->branch_id;
            $this->register_date = now()->format('Y-m-d');
            $this->expire_date = now()->addYear()->format('Y-m-d');
            $this->member_id = 'M' . date('Y') . str_pad(Member::count() + 1, 4, '0', STR_PAD_LEFT);
        }
    }

    public function updatedMemberTypeId($value)
    {
        if ($value) {
            $memberType = MemberType::find($value);
            if ($memberType && !$this->member) {
                $this->expire_date = now()->addDays($memberType->membership_period ?? 365)->format('Y-m-d');
            }
        }
    }

    public function getDepartments()
    {
        if (!$this->faculty_id) {
            return collect();
        }
        return Department::where('faculty_id', $this->faculty_id)->orderBy('name')->get();
    }

    public function save()
    {
        $validated = $this->validate();

        // Handle photo upload
        $photoPath = $this->existing_photo;
        if ($this->photo) {
            // Delete old photo if exists
            if ($this->existing_photo) {
                Storage::disk('public')->delete($this->existing_photo);
            }
            $photoPath = $this->photo->store('members', 'public');
        }

        $data = [
            'member_id' => $this->member_id,
            'name' => $this->name,
            'gender' => $this->gender ?: null,
            'birth_date' => $this->birth_date ?: null,
            'address' => $this->address,
            'city' => $this->city,
            'phone' => $this->phone,
            'email' => $this->email,
            'photo' => $photoPath,
            'branch_id' => $this->branch_id,
            'faculty_id' => $this->faculty_id ?: null,
            'department_id' => $this->department_id ?: null,
            'member_type_id' => $this->member_type_id,
            'register_date' => $this->register_date,
            'expire_date' => $this->expire_date,
            'is_active' => $this->is_active,
            'notes' => $this->notes,
        ];

        if ($this->member) {
            $this->member->update($data);
            session()->flash('success', 'Data anggota berhasil diperbarui');
        } else {
            Member::create($data);
            session()->flash('success', 'Anggota baru berhasil ditambahkan');
        }

        return redirect()->route('staff.member.index');
    }

    public function render()
    {
        return view('livewire.staff.member.member-form', [
            'branches' => Branch::orderBy('name')->get(),
            'faculties' => Faculty::orderBy('name')->get(),
            'departments' => $this->getDepartments(),
            'memberTypes' => MemberType::orderBy('name')->get(),
        ])->extends('staff.layouts.app')->section('content');
    }
}
