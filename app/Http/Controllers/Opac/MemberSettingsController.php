<?php

namespace App\Http\Controllers\Opac;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Faculty;
use App\Models\MemberType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MemberSettingsController extends Controller
{
    public function index()
    {
        $member = Auth::guard('member')->user();
        
        // Kita load data referensi kalau-kalau nanti user mau enable field akademik
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        $faculties = Faculty::orderBy('name')->get();
        $memberTypes = MemberType::orderBy('name')->distinct()->get(['id', 'name']);

        return view('opac.member.settings', compact('member', 'branches', 'faculties', 'memberTypes'));
    }

    public function update(Request $request)
    {
        $member = Auth::guard('member')->user();

        // Validasi diselaraskan dengan CompleteProfileController tapi tanpa unique NIM check (karena NIM readonly)
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'phone' => 'required|string|max:20',
            'gender' => 'required|in:M,F',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'name.required' => 'Nama Lengkap wajib diisi',
            'phone.required' => 'Nomor Telepon/WA wajib diisi',
            'photo.image' => 'File harus berupa gambar',
            'photo.max' => 'Ukuran foto maksimal 2MB',
        ]);

        $data = [
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'gender' => $validated['gender'],
        ];

        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($member->photo && Storage::disk('public')->exists($member->photo)) {
                Storage::disk('public')->delete($member->photo);
            }
            $data['photo'] = $request->file('photo')->store('members', 'public');
        }

        $member->update($data);

        return redirect()->route('opac.member.settings')
            ->with('success', 'Profil berhasil diperbarui.');
    }
}
