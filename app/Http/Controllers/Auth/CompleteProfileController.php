<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Faculty;
use App\Models\MemberType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompleteProfileController extends Controller
{
    public function show()
    {
        $member = Auth::guard('member')->user();
        if ($member->profile_completed) {
            return redirect()->route('member.dashboard');
        }
        
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        $faculties = Faculty::orderBy('name')->get();
        $memberTypes = MemberType::orderBy('name')->distinct()->get(['id', 'name']);
        
        return view('auth.complete-profile', compact('member', 'branches', 'faculties', 'memberTypes'));
    }

    public function update(Request $request)
    {
        $member = Auth::guard('member')->user();

        $validated = $request->validate([
            'nim' => 'required|string|max:30|unique:members,member_id,' . $member->id,
            'branch_id' => 'required|exists:branches,id',
            'member_type_id' => 'required|exists:member_types,id',
            'faculty_id' => 'required|exists:faculties,id',
            'department_id' => 'required|exists:departments,id',
            'phone' => 'required|string|max:20',
            'gender' => 'required|in:M,F',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'nim.required' => 'NIM wajib diisi',
            'nim.unique' => 'NIM sudah terdaftar',
            'photo.image' => 'File harus berupa gambar',
            'photo.max' => 'Ukuran foto maksimal 2MB',
        ]);

        $data = [
            'member_id' => $validated['nim'],
            'branch_id' => $validated['branch_id'],
            'member_type_id' => $validated['member_type_id'],
            'phone' => $validated['phone'],
            'gender' => $validated['gender'],
            'profile_completed' => true,
        ];

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('members', 'public');
        }

        $member->update($data);

        return redirect()->route('member.dashboard')
            ->with('success', 'Profil berhasil dilengkapi.');
    }
}
