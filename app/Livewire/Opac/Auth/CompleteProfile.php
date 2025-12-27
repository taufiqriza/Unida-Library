<?php

namespace App\Livewire\Opac\Auth;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Member;
use App\Models\MemberType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class CompleteProfile extends Component
{
    use WithFileUploads;

    public $member;
    
    // Step tracking
    public int $step = 1; // 1 = Search, 2 = Form
    
    // PDDikti Search
    public string $searchName = '';
    public $searchResults = [];
    public ?int $selectedPddiktiId = null;
    public $selectedPddikti = null;
    public bool $isSearching = false;
    public bool $showManualEntry = false;
    public bool $autoDetected = false; // Track if data was auto-detected from email
    public string $entryMode = 'mahasiswa'; // mahasiswa, dosen, tendik, manual
    
    // Tendik-specific fields
    public string $satker = ''; // Satuan Kerja (manual input for Tendik)
    
    // Form fields
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

        // Pre-fill with Google name if available
        $this->searchName = $this->member->name ?? '';
        $this->phone = $this->member->phone ?? '';
        $this->gender = $this->member->gender ?? '';
        
        $this->branches = Branch::where('is_active', true)->orderBy('name')->get();
        $this->faculties = Faculty::orderBy('name')->get();
        $this->memberTypes = MemberType::orderBy('name')->distinct()->get(['id', 'name']);
        
        // Auto-detect member from email pattern
        $this->autoDetectFromEmail();
    }
    /**
     * Auto-detect matching member from logged-in email
     * Uses multi-factor scoring for ultra-high accuracy
     */
    protected function autoDetectFromEmail()
    {
        $email = $this->member->email;
        if (!$email) return;
        
        // Extract parts
        $emailParts = explode('@', $email);
        $username = $emailParts[0] ?? '';
        $domain = $emailParts[1] ?? '';
        if (!$username) return;
        
        $googleName = $this->member->name ?? '';
        
        // === PHASE 1: Extract all possible identifiers ===
        
        // Extract prodi from domain (e.g., student.ei.unida.gontor.ac.id → EI)
        $prodiCode = null;
        if (preg_match('/student\.([a-z0-9]+)\.unida/', $domain, $dm)) {
            $prodiCode = strtoupper($dm[1]);
        }
        
        // Extract NIM patterns (10-15 digit numbers)
        $nimFromEmail = null;
        $nimFromGoogleName = null;
        if (preg_match('/(\d{10,15})/', $username, $m)) $nimFromEmail = $m[1];
        if (preg_match('/(\d{10,15})/', $googleName, $m)) $nimFromGoogleName = $m[1];
        
        // Clean names
        $cleanGoogleName = strtoupper(trim(preg_replace('/[\d]+/', '', $googleName)));
        $cleanEmailName = strtoupper(trim(preg_replace('/[\d._-]+/', ' ', $username)));
        
        // Get department ID if prodi matched
        $prodiDeptId = null;
        if ($prodiCode) {
            $dept = \App\Models\Department::where('code', $prodiCode)->first();
            $prodiDeptId = $dept?->id;
        }
        
        // === PHASE 2: Score-based matching ===
        
        // Base query: unlinked members only
        $candidates = Member::with(['department', 'branch'])
            ->where('profile_completed', false)
            ->where(function($q) {
                $q->whereNull('email')->orWhere('email', '');
            })
            ->limit(100)
            ->get();
        
        $bestMatch = null;
        $bestScore = 0;
        $topMatches = []; // Collect all qualifying matches
        
        foreach ($candidates as $member) {
            $score = 0;
            $memberNameUpper = strtoupper($member->name);
            
            // === SCORING FACTORS ===
            
            // 1. NIM exact match (100 points - definitive)
            if ($nimFromEmail && $member->member_id === $nimFromEmail) {
                $score += 100;
            }
            if ($nimFromGoogleName && $member->member_id === $nimFromGoogleName) {
                $score += 100;
            }
            
            // 2. Prodi match from email domain (20 points)
            if ($prodiDeptId && $member->department_id == $prodiDeptId) {
                $score += 20;
            }
            
            // 3. Google name exact match (50 points)
            if ($cleanGoogleName && $memberNameUpper === $cleanGoogleName) {
                $score += 50;
            }
            // 3b. Google name contains (30 points)
            elseif ($cleanGoogleName && str_contains($memberNameUpper, $cleanGoogleName)) {
                $score += 30;
            }
            // 3c. Member name contains Google name (25 points)
            elseif ($cleanGoogleName && str_contains($cleanGoogleName, $memberNameUpper)) {
                $score += 25;
            }
            
            // 4. Word-by-word matching from Google name (5 points per word)
            if ($cleanGoogleName) {
                $googleWords = array_filter(explode(' ', $cleanGoogleName), fn($w) => strlen($w) >= 3);
                $memberWords = array_filter(explode(' ', $memberNameUpper), fn($w) => strlen($w) >= 3);
                $matchedWords = array_intersect($googleWords, $memberWords);
                $score += count($matchedWords) * 5;
                
                // Bonus for high word overlap (15 points if >60% match)
                if (count($googleWords) > 0 && count($matchedWords) / count($googleWords) > 0.6) {
                    $score += 15;
                }
            }
            
            // 5. Email username match (15 points)
            if ($cleanEmailName && str_contains($memberNameUpper, $cleanEmailName)) {
                $score += 15;
            }
            
            // 6. First name match bonus (10 points)
            if ($cleanGoogleName) {
                $googleFirstName = explode(' ', $cleanGoogleName)[0] ?? '';
                $memberFirstName = explode(' ', $memberNameUpper)[0] ?? '';
                if ($googleFirstName && $memberFirstName && $googleFirstName === $memberFirstName) {
                    $score += 10;
                }
            }
            // Track all matches above threshold with scores
            if ($score >= 30) {
                $member->_matchScore = $score;
                $topMatches[] = $member;
            }
            
            // Track best match
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $member;
            }
        }
        
        // === PHASE 3: Smart Selection ===
        
        // Sort top matches by score descending
        usort($topMatches, fn($a, $b) => $b->_matchScore <=> $a->_matchScore);
        
        // Case 1: Single high-confidence match (score >= 50 or NIM match)
        if ($bestScore >= 50) {
            $matchedMember = $bestMatch;
            $this->autoDetected = true;
            $this->searchResults = collect([$matchedMember]);
            $this->selectedPddiktiId = $matchedMember->id;
            $this->selectedPddikti = $matchedMember;
            $this->nim = $matchedMember->member_id ?? '';
            $this->branch_id = $matchedMember->branch_id;
            $this->department_id = $matchedMember->department_id;
            $this->faculty_id = $matchedMember->faculty_id;
            $this->gender = $matchedMember->gender ?? '';
            
            $mahasiswaType = MemberType::where('name', 'like', '%Mahasiswa%')->first();
            $this->member_type_id = $mahasiswaType?->id ?? $matchedMember->member_type_id;
            return;
        }
        
        // Case 2: Multiple candidates with similar scores (show top 5 for user to choose)
        if (count($topMatches) > 1) {
            $this->autoDetected = false; // Let user choose
            $this->searchResults = collect(array_slice($topMatches, 0, 5));
            return;
        }
        
        // Case 3: Single match but lower confidence (30-49) - show but let user confirm
        if (count($topMatches) === 1) {
            $matchedMember = $topMatches[0];
            $this->autoDetected = false; // Show as suggestion, not auto-detected
            $this->searchResults = collect([$matchedMember]);
            return;
        }
        
        // Case 4: No matches found - user will need to search or enter manually
        // searchResults stays empty, UI will show appropriate message
    }
    
    /**
     * Quick confirm - link auto-detected data without form
     */
    public function quickConfirm()
    {
        if (!$this->selectedPddiktiId || !$this->selectedPddikti) {
            return;
        }
        
        $selectedMember = Member::find($this->selectedPddiktiId);
        if (!$selectedMember) {
            return;
        }
        
        // Transfer email and social accounts from temp member to selected member
        $selectedMember->update([
            'email' => $this->member->email,
            'profile_completed' => true,
        ]);
        
        // Transfer social accounts
        \App\Models\SocialAccount::where('member_id', $this->member->id)
            ->update(['member_id' => $selectedMember->id]);
        
        // Delete the temporary member
        $this->member->delete();
        
        // Login as the linked member
        Auth::guard('member')->login($selectedMember);
        
        return redirect()->route('member.dashboard')
            ->with('success', 'Profil berhasil ditautkan dengan data SIAKAD!');
    }

    /**
     * Search student data by name in members table (SIAKAD data)
     */
    public function searchPddikti()
    {
        $this->isSearching = true;
        $this->searchResults = [];
        $this->selectedPddiktiId = null;
        $this->selectedPddikti = null;
        
        if (strlen(trim($this->searchName)) < 3) {
            $this->isSearching = false;
            return;
        }

        // Search in members table (imported from SIAKAD) 
        // Find members with similar name that haven't completed profile and no email yet
        $searchName = trim($this->searchName);
        $words = explode(' ', $searchName);
        
        $this->searchResults = Member::with(['department', 'branch'])
            ->where(function($q) use ($searchName, $words) {
                $q->where('name', 'like', "%{$searchName}%");
                foreach ($words as $word) {
                    if (strlen($word) >= 2) {
                        $q->orWhere('name', 'like', "%{$word}%");
                    }
                }
            })
            ->where(function($q) {
                $q->whereNull('email')->orWhere('email', '');
            })
            ->where('profile_completed', false)
            ->orderBy('name')
            ->limit(20)
            ->get();

        $this->isSearching = false;
    }

    /**
     * Select a SIAKAD record (member data)
     */
    public function selectPddikti(int $id)
    {
        $this->selectedPddiktiId = $id;
        $this->selectedPddikti = Member::find($id);
        
        if ($this->selectedPddikti) {
            // Auto-fill NIM from member data
            $this->nim = $this->selectedPddikti->member_id ?? '';
            
            // Auto-fill from selected member
            $this->branch_id = $this->selectedPddikti->branch_id;
            $this->department_id = $this->selectedPddikti->department_id;
            $this->faculty_id = $this->selectedPddikti->faculty_id;
            $this->gender = $this->selectedPddikti->gender ?? '';
            
            // Set member type to Mahasiswa by default
            $mahasiswaType = MemberType::where('name', 'like', '%Mahasiswa%')->first();
            $this->member_type_id = $mahasiswaType?->id ?? $this->selectedPddikti->member_type_id;
        }
    }

    /**
     * Proceed to form with selected PDDikti data
     */
    public function proceedWithSelection()
    {
        if (!$this->selectedPddiktiId && !$this->showManualEntry) {
            return;
        }
        
        $this->step = 2;
    }

    /**
     * Skip PDDikti search and go to manual entry
     */
    public function skipToManualEntry()
    {
        $this->showManualEntry = true;
        $this->selectedPddiktiId = null;
        $this->selectedPddikti = null;
        $this->step = 2;
    }

    /**
     * Go back to search step
     */
    public function backToSearch()
    {
        $this->step = 1;
        $this->showManualEntry = false;
    }

    /**
     * Skip as Dosen - auto-select dosen member type
     */
    public function skipAsDosen()
    {
        $this->showManualEntry = true;
        $this->selectedPddiktiId = null;
        $this->selectedPddikti = null;
        $this->entryMode = 'dosen';
        
        // Auto-select Dosen member type
        $dosenType = MemberType::where('name', 'like', '%Dosen%')->first();
        $this->member_type_id = $dosenType?->id ?? 1;
        
        $this->step = 2;
    }
    
    /**
     * Skip as Tendik - auto-select tendik member type with manual satker
     */
    public function skipAsTendik()
    {
        $this->showManualEntry = true;
        $this->selectedPddiktiId = null;
        $this->selectedPddikti = null;
        $this->entryMode = 'tendik';
        
        // Auto-select Tendik member type (or create if not exists)
        $tendikType = MemberType::firstOrCreate(
            ['name' => 'Tendik'],
            ['description' => 'Tenaga Kependidikan']
        );
        $this->member_type_id = $tendikType->id;
        
        // Auto-set branch to Pusat
        $pusatBranch = Branch::where('name', 'like', '%Pusat%')
            ->orWhere('name', 'like', '%Siman%')
            ->first();
        $this->branch_id = $pusatBranch?->id;
        
        $this->step = 2;
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
        $rules = [
            'nim' => 'required|string|max:30|unique:members,member_id,' . $this->member->id,
            'phone' => 'required|string|max:20',
            'gender' => 'required|in:M,F',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
        
        // Tendik: only need satker
        if ($this->entryMode === 'tendik') {
            $rules['satker'] = 'required|string|max:100';
        } else {
            // Mahasiswa/Dosen/Manual: need branch, faculty, department
            $rules['branch_id'] = 'required|exists:branches,id';
            $rules['member_type_id'] = 'required|exists:member_types,id';
            $rules['faculty_id'] = 'required|exists:faculties,id';
            $rules['department_id'] = 'required|exists:departments,id';
        }
        
        return $rules;
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
            'nim_nidn' => $this->nim,
            'branch_id' => $this->branch_id,
            'member_type_id' => $this->member_type_id,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'profile_completed' => true,
        ];
        
        // Add faculty and department only if not Tendik
        if ($this->entryMode !== 'tendik') {
            $data['faculty_id'] = $this->faculty_id;
            $data['department_id'] = $this->department_id;
        }
        
        // Store satker in notes field for Tendik (or custom field)
        if ($this->entryMode === 'tendik' && $this->satker) {
            $data['notes'] = 'Satker: ' . $this->satker;
        }

        // If user selected an existing member record, merge data and delete duplicate
        if ($this->selectedPddiktiId && $this->selectedPddikti && $this->selectedPddikti->id !== $this->member->id) {
            $selectedMember = Member::find($this->selectedPddiktiId);
            if ($selectedMember) {
                // Update selected member with form data + email from Google
                $selectedMember->update([
                    'email' => $this->member->email,
                    'member_id' => $this->nim, // Update NIM from form
                    'nim_nidn' => $this->nim,
                    'phone' => $this->phone,
                    'gender' => $this->gender,
                    'photo' => $this->photo ? $this->photo->store('members', 'public') : $selectedMember->photo,
                    'profile_completed' => true,
                ]);
                
                // Transfer social accounts
                \App\Models\SocialAccount::where('member_id', $this->member->id)
                    ->update(['member_id' => $selectedMember->id]);
                
                // Delete temp member
                $this->member->delete();
                
                Auth::guard('member')->login($selectedMember);
                
                return redirect()->route('member.dashboard')
                    ->with('success', 'Profil berhasil ditautkan dengan data SIAKAD.');
            }
        }

        if ($this->photo) {
            $data['photo'] = $this->photo->store('members', 'public');
        }

        // Update NIM for non-merge case too
        $data['nim_nidn'] = $this->nim;
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
