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
    
    // NIM check
    public $nimExistingMember = null;

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
        
        // Auto-detect from email (mahasiswa, dosen, tendik)
        $this->autoDetectFromEmail();
    }
    
    /**
     * Auto-detect matching data from email
     * Priority: 1) Employee (dosen/tendik) by email, 2) Student by NIM in email, 3) Name matching
     */
    protected function autoDetectFromEmail()
    {
        $email = $this->member->email;
        if (!$email) return;
        
        $emailParts = explode('@', $email);
        $username = $emailParts[0] ?? '';
        $domain = $emailParts[1] ?? '';
        if (!$username) return;
        
        // === PRIORITY 1: Check Employee table by email (Dosen/Tendik) ===
        if (str_contains($domain, 'unida.gontor') && !str_contains($domain, 'student')) {
            $employee = \App\Models\Employee::where('email', $email)->first();
            if ($employee) {
                $this->autoDetected = true;
                $this->entryMode = $employee->type; // 'dosen' or 'tendik'
                $this->selectedEmployee = $employee;
                $this->nim = $employee->niy ?? '';
                $this->searchName = $employee->full_name ?? $employee->name;
                
                // Set member type
                if ($employee->type === 'dosen') {
                    $this->member_type_id = MemberType::where('name', 'like', '%Dosen%')->first()?->id;
                } else {
                    $this->member_type_id = MemberType::firstOrCreate(['name' => 'Tendik'], ['description' => 'Tenaga Kependidikan'])->id;
                }
                
                // Set faculty/department if available
                if ($employee->faculty) {
                    $faculty = Faculty::where('name', 'like', "%{$employee->faculty}%")->first();
                    $this->faculty_id = $faculty?->id;
                }
                return;
            }
        }
        
        // === PRIORITY 2: Direct NIM match from student email ===
        if (preg_match('/^(\d{12,15})@(student|stu|mhs)/', $email, $nimMatch)) {
            $nimFromEmail = $nimMatch[1];
            $directMatch = Member::with(['department', 'branch'])
                ->where('member_id', $nimFromEmail)
                ->where('profile_completed', false)
                ->where(fn($q) => $q->whereNull('email')->orWhere('email', ''))
                ->first();
            
            if ($directMatch) {
                $this->autoDetected = true;
                $this->entryMode = 'mahasiswa';
                $this->selectedPddiktiId = $directMatch->id;
                $this->selectedPddikti = $directMatch;
                $this->nim = $directMatch->member_id;
                $this->branch_id = $directMatch->branch_id;
                $this->department_id = $directMatch->department_id;
                $this->faculty_id = $directMatch->faculty_id;
                $this->gender = $directMatch->gender ?? '';
                $this->member_type_id = MemberType::where('name', 'like', '%Mahasiswa%')->first()?->id;
                return;
            }
        }
        
        // === PRIORITY 3: Name matching for students (existing logic) ===
        $this->autoDetectByName();
    }
    
    /**
     * Auto-detect by name matching (for students without NIM in email)
     */
    protected function autoDetectByName()
    {
        $email = $this->member->email;
        $googleName = $this->member->name ?? '';
        if (!$googleName) return;
        
        $emailParts = explode('@', $email);
        $username = $emailParts[0] ?? '';
        $domain = $emailParts[1] ?? '';
        
        // Extract prodi from domain
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
        
        // Score-based matching
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
     * Search all data (mahasiswa from SIAKAD + dosen/tendik from Employee)
     */
    public function searchPddikti()
    {
        $this->isSearching = true;
        $this->searchResults = [];
        $this->employeeResults = [];
        $this->selectedPddiktiId = null;
        $this->selectedPddikti = null;
        $this->selectedEmployee = null;
        
        $search = trim($this->searchName);
        
        if (strlen($search) < 2) {
            $this->isSearching = false;
            return;
        }

        $isNumeric = preg_match('/^\d{4,}$/', $search);
        $searchUpper = strtoupper($search);
        
        // === Search Mahasiswa (SIAKAD) ===
        if ($isNumeric && strlen($search) >= 10) {
            // NIM search
            $mahasiswa = Member::with(['department', 'branch'])
                ->where(fn($q) => $q->where('member_id', $search)->orWhere('nim_nidn', $search))
                ->where(fn($q) => $q->whereNull('email')->orWhere('email', ''))
                ->where('profile_completed', false)
                ->limit(5)->get();
            $mahasiswa->each(fn($r) => $r->_matchScore = 100);
        } else {
            // Name search
            $mahasiswa = Member::with(['department', 'branch'])
                ->where(fn($q) => $q->whereNull('email')->orWhere('email', ''))
                ->where('profile_completed', false)
                ->where('name', 'like', "%{$search}%")
                ->limit(10)->get();
            
            $mahasiswa->each(function($r) use ($searchUpper) {
                $nameUpper = strtoupper($r->name);
                if ($nameUpper === $searchUpper) $r->_matchScore = 100;
                elseif (str_starts_with($nameUpper, $searchUpper)) $r->_matchScore = 90;
                else {
                    similar_text($searchUpper, $nameUpper, $percent);
                    $r->_matchScore = (int) round($percent);
                }
            });
        }
        
        // === Search Dosen/Tendik (Employee) ===
        if ($isNumeric) {
            // NIY/NIDN search
            $employees = \App\Models\Employee::where(fn($q) => $q->where('niy', $search)->orWhere('nidn', $search))
                ->limit(5)->get();
            $employees->each(fn($e) => $e->_matchScore = 100);
        } else {
            // Name search
            $employees = \App\Models\Employee::where(fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('full_name', 'like', "%{$search}%"))
                ->limit(10)->get();
            
            $employees->each(function($e) use ($searchUpper) {
                $nameUpper = strtoupper($e->full_name ?? $e->name);
                if ($nameUpper === $searchUpper) $e->_matchScore = 100;
                elseif (str_starts_with($nameUpper, $searchUpper)) $e->_matchScore = 90;
                else {
                    similar_text($searchUpper, $nameUpper, $percent);
                    $e->_matchScore = (int) round($percent);
                }
            });
        }
        
        $this->searchResults = $mahasiswa->filter(fn($r) => ($r->_matchScore ?? 0) >= 20)->sortByDesc('_matchScore')->values();
        $this->employeeResults = $employees->filter(fn($e) => ($e->_matchScore ?? 0) >= 20)->sortByDesc('_matchScore')->values();
        
        $this->isSearching = false;
    }
    
    // Property for employee search results
    public $employeeResults = [];

    /**
     * Select a SIAKAD record (member data)
     */
    public function selectPddikti(int $id)
    {
        $this->selectedPddiktiId = $id;
        $this->selectedPddikti = Member::find($id);
        $this->selectedEmployee = null; // Clear employee selection
        $this->entryMode = 'mahasiswa';
        
        if ($this->selectedPddikti) {
            $this->nim = $this->selectedPddikti->member_id ?? '';
            $this->branch_id = $this->selectedPddikti->branch_id;
            $this->department_id = $this->selectedPddikti->department_id;
            $this->faculty_id = $this->selectedPddikti->faculty_id;
            $this->gender = $this->selectedPddikti->gender ?? '';
            $this->member_type_id = MemberType::where('name', 'like', '%Mahasiswa%')->first()?->id;
        }
    }

    /**
     * Proceed to form with selected PDDikti data
     */
    public function proceedWithSelection()
    {
        if (!$this->selectedPddiktiId && !$this->selectedEmployee && !$this->showManualEntry) {
            return;
        }
        
        $this->step = 2;
    }

    // Property for existing employee found
    public $niyExistingEmployee = null;

    /**
     * Check if NIM/NIY exists (called on input change)
     */
    public function updatedNim($value)
    {
        $this->nimExistingMember = null;
        $this->niyExistingEmployee = null;
        
        if (strlen($value) >= 4) {
            // Check Employee table (NIY/NIDN for dosen/tendik)
            $employee = \App\Models\Employee::where('niy', $value)
                ->orWhere('nidn', $value)
                ->first();
            
            if ($employee) {
                $this->niyExistingEmployee = $employee;
                return;
            }
            
            // Check Member table (NIM for mahasiswa) - only if 10+ digits
            if (strlen($value) >= 10) {
                $existing = Member::with(['department', 'branch'])
                    ->where('member_id', $value)
                    ->where('id', '!=', $this->member->id)
                    ->where(fn($q) => $q->whereNull('email')->orWhere('email', ''))
                    ->where('profile_completed', false)
                    ->first();
                
                if ($existing) {
                    $this->nimExistingMember = $existing;
                }
            }
        }
    }

    /**
     * Link to existing employee from NIY check
     */
    public function linkToExistingEmployee()
    {
        if (!$this->niyExistingEmployee) return;
        
        $this->selectEmployee($this->niyExistingEmployee->id);
        $this->niyExistingEmployee = null;
        $this->step = 1; // Go back to confirm
        $this->showManualEntry = false;
    }

    /**
     * Link to existing SIAKAD member from NIM check
     */
    public function linkToExistingMember()
    {
        if (!$this->nimExistingMember) {
            return;
        }
        
        $this->selectedPddiktiId = $this->nimExistingMember->id;
        $this->selectedPddikti = $this->nimExistingMember;
        $this->branch_id = $this->nimExistingMember->branch_id;
        $this->department_id = $this->nimExistingMember->department_id;
        $this->faculty_id = $this->nimExistingMember->faculty_id;
        $this->gender = $this->nimExistingMember->gender ?? $this->gender;
        
        $mahasiswaType = MemberType::where('name', 'like', '%Mahasiswa%')->first();
        $this->member_type_id = $mahasiswaType?->id ?? $this->nimExistingMember->member_type_id;
        
        $this->nimExistingMember = null;
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
     * Skip as Dosen - go to search employee step
     */
    public function skipAsDosen()
    {
        $this->entryMode = 'dosen';
        $this->showManualEntry = false;
        $this->selectedPddiktiId = null;
        $this->selectedPddikti = null;
        $this->selectedEmployee = null;
        $this->searchResults = collect();
        $this->searchName = '';
        
        // Auto-select Dosen member type
        $dosenType = MemberType::where('name', 'like', '%Dosen%')->first();
        $this->member_type_id = $dosenType?->id ?? 2;
        
        // Try auto-detect by email
        $employee = \App\Models\Employee::where('email', $this->member->email)
            ->where('type', 'dosen')
            ->first();
        
        if ($employee) {
            $this->selectEmployee($employee->id);
            $this->autoDetected = true;
        }
        
        // Stay on step 1 for search
    }
    
    /**
     * Skip as Tendik - go to search employee step
     */
    public function skipAsTendik()
    {
        $this->entryMode = 'tendik';
        $this->showManualEntry = false;
        $this->selectedPddiktiId = null;
        $this->selectedPddikti = null;
        $this->selectedEmployee = null;
        $this->searchResults = collect();
        $this->searchName = '';
        
        // Auto-select Tendik member type
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
        
        // Try auto-detect by email
        $employee = \App\Models\Employee::where('email', $this->member->email)
            ->where('type', 'tendik')
            ->first();
        
        if ($employee) {
            $this->selectEmployee($employee->id);
            $this->autoDetected = true;
        }
    }
    
    /**
     * Select an employee record
     */
    public function selectEmployee($id)
    {
        $employee = \App\Models\Employee::find($id);
        if (!$employee) return;
        
        $this->selectedEmployee = $employee;
        $this->selectedPddiktiId = null; // Clear mahasiswa selection
        $this->selectedPddikti = null;
        $this->entryMode = $employee->type; // 'dosen' or 'tendik'
        
        $this->nim = $employee->niy ?? '';
        $this->satker = $employee->satker ?? '';
        $this->gender = $employee->gender ?? '';
        
        // Set member type
        if ($employee->type === 'dosen') {
            $this->member_type_id = MemberType::where('name', 'like', '%Dosen%')->first()?->id;
        } else {
            $this->member_type_id = MemberType::firstOrCreate(['name' => 'Tendik'], ['description' => 'Tenaga Kependidikan'])->id;
        }
        
        // Set faculty if available
        if ($employee->faculty) {
            $faculty = Faculty::where('name', 'like', "%{$employee->faculty}%")->first();
            $this->faculty_id = $faculty?->id;
            if ($this->faculty_id) {
                $this->departments = Department::where('faculty_id', $this->faculty_id)->orderBy('name')->get();
            }
        }
        
        // Set department if available
        if ($employee->prodi) {
            $dept = Department::where('name', 'like', "%{$employee->prodi}%")->first();
            $this->department_id = $dept?->id;
        }
    }
    
    /**
     * Quick confirm for employee (dosen/tendik)
     */
    public function quickConfirmEmployee()
    {
        if (!$this->selectedEmployee) return;
        
        $emp = $this->selectedEmployee;
        
        // Update current member with employee data
        $this->member->update([
            'name' => $emp->full_name ?? $emp->name,
            'member_id' => $emp->niy,
            'nim_nidn' => $emp->nidn,
            'member_type_id' => $this->member_type_id,
            'branch_id' => $this->branch_id ?? Branch::first()?->id,
            'faculty_id' => $this->faculty_id,
            'department_id' => $this->department_id,
            'gender' => $emp->gender ?? $this->gender,
            'profile_completed' => true,
        ]);
        
        return redirect()->route('member.dashboard')
            ->with('success', 'Profil berhasil ditautkan dengan data SDM!');
    }
    
    // Property for selected employee
    public $selectedEmployee = null;

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
