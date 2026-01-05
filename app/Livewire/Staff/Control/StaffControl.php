<?php

namespace App\Livewire\Staff\Control;

use App\Models\User;
use App\Models\Branch;
use App\Models\ActivityLog;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;

class StaffControl extends Component
{
    use WithPagination;

    public $mainTab = 'staff'; // 'staff', 'approval', 'activity', 'branches'
    public $activeTab = 'all'; // for staff: all, super_admin, admin, librarian, staff
    public $search = '';
    public $selectedUser = null;
    public $showModal = false;
    public $rejectionReason = '';
    
    // Create User Form
    public $showCreateModal = false;
    public $createForm = [
        'name' => '',
        'email' => '',
        'password' => '',
        'branch_id' => '',
        'role' => 'staff',
        'is_active' => true,
    ];

    // Branch Form
    public $showBranchModal = false;
    public $editingBranch = null;
    public $branchViewOnly = false;
    public $branchForm = [
        'name' => '',
        'code' => '',
        'address' => '',
        'phone' => '',
        'email' => '',
        'is_active' => true,
    ];

    // Activity Log Filters
    public $activityBranchId = '';
    public $activityModule = '';
    public $activityAction = '';
    public $activityUserId = '';
    public $activityDateStart = '';
    public $activityDateEnd = '';

    protected $queryString = ['mainTab', 'activeTab', 'search'];

    protected $rules = [
        'createForm.name' => 'required|string|max:255',
        'createForm.email' => 'required|email|unique:users,email',
        'createForm.password' => 'required|min:8',
        'createForm.branch_id' => 'nullable|exists:branches,id',
        'createForm.role' => 'required|in:super_admin,admin,librarian,staff,pustakawan',
    ];

    protected $messages = [
        'createForm.name.required' => 'Nama wajib diisi',
        'createForm.email.required' => 'Email wajib diisi',
        'createForm.email.email' => 'Format email tidak valid',
        'createForm.email.unique' => 'Email sudah terdaftar',
        'createForm.password.required' => 'Password wajib diisi',
        'createForm.password.min' => 'Password minimal 8 karakter',
    ];

    public function updatingSearch() { $this->resetPage(); }

    public function setMainTab($tab)
    {
        $this->mainTab = $tab;
        
        if ($tab === 'staff') {
            $this->activeTab = 'all';
        } elseif ($tab === 'approval') {
            $this->activeTab = 'pending';
        } elseif ($tab === 'activity') {
            // Set default date range (last 7 days)
            $this->activityDateStart = now()->subDays(7)->format('Y-m-d');
            $this->activityDateEnd = now()->format('Y-m-d');
        }
        
        $this->resetPage();
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->createForm = [
            'name' => '',
            'email' => '',
            'password' => '',
            'branch_id' => auth()->user()->branch_id ?? '',
            'role' => 'staff',
            'is_active' => true,
        ];
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetValidation();
    }

    public function createUser()
    {
        $this->validate();
        
        $currentUser = auth()->user();

        // Authorization check
        if ($currentUser->role !== 'super_admin') {
            // Admin cabang hanya bisa membuat staff saja
            if (!in_array($this->createForm['role'], ['librarian', 'staff', 'pustakawan'])) {
                $this->dispatch('notify', type: 'error', message: 'Anda hanya dapat membuat role Staff atau Pustakawan');
                return;
            }
            // Wajib branch_id sama dengan admin
            $this->createForm['branch_id'] = $currentUser->branch_id;
        }

        User::create([
            'name' => $this->createForm['name'],
            'email' => $this->createForm['email'],
            'password' => Hash::make($this->createForm['password']),
            'branch_id' => $this->createForm['branch_id'] ?: null,
            'role' => $this->createForm['role'],
            'is_active' => $this->createForm['is_active'],
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $this->dispatch('notify', type: 'success', message: 'User berhasil dibuat');
        $this->closeCreateModal();
    }

    public function viewUser($id)
    {
        $query = User::with('branch');
        
        if (auth()->user()->role !== 'super_admin') {
            $query->where('branch_id', auth()->user()->branch_id);
        }
        
        $this->selectedUser = $query->find($id);
        
        if (!$this->selectedUser) {
            $this->dispatch('notify', type: 'error', message: 'User tidak ditemukan atau tidak memiliki akses');
            return;
        }
        
        $this->showModal = true;
        $this->rejectionReason = '';
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedUser = null;
        $this->rejectionReason = '';
    }

    public function approveUser()
    {
        if (!$this->selectedUser) return;
        
        if (auth()->user()->role !== 'super_admin' && 
            $this->selectedUser->branch_id !== auth()->user()->branch_id) {
            $this->dispatch('notify', type: 'error', message: 'Tidak memiliki akses untuk menyetujui staff ini');
            return;
        }

        $this->selectedUser->update([
            'status' => 'approved',
            'is_active' => true,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $this->dispatch('notify', type: 'success', message: 'Staff berhasil disetujui dan dapat login');
        $this->closeModal();
    }

    public function rejectUser()
    {
        if (!$this->selectedUser) return;
        
        if (auth()->user()->role !== 'super_admin' && 
            $this->selectedUser->branch_id !== auth()->user()->branch_id) {
            $this->dispatch('notify', type: 'error', message: 'Tidak memiliki akses untuk menolak staff ini');
            return;
        }

        $this->validate(['rejectionReason' => 'required|min:10']);

        $this->selectedUser->update([
            'status' => 'rejected',
            'is_active' => false,
            'rejection_reason' => $this->rejectionReason,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $this->dispatch('notify', type: 'success', message: 'Pendaftaran staff ditolak');
        $this->closeModal();
    }

    public function toggleUserActive($id)
    {
        $user = User::find($id);
        if (!$user) return;
        
        if (auth()->user()->role !== 'super_admin' && $user->branch_id !== auth()->user()->branch_id) {
            $this->dispatch('notify', type: 'error', message: 'Tidak memiliki akses');
            return;
        }

        $user->update(['is_active' => !$user->is_active]);
        $this->dispatch('notify', type: 'success', message: 'Status user diperbarui');
    }

    public $showDeleteConfirm = false;
    public $userToDelete = null;

    public function confirmDeleteUser($id)
    {
        $user = User::find($id);
        if (!$user) return;
        
        // Cannot delete yourself
        if ($user->id === auth()->id()) {
            $this->dispatch('notify', type: 'error', message: 'Tidak dapat menghapus akun sendiri');
            return;
        }
        
        // Authorization check
        if (auth()->user()->role !== 'super_admin' && $user->branch_id !== auth()->user()->branch_id) {
            $this->dispatch('notify', type: 'error', message: 'Tidak memiliki akses untuk menghapus user ini');
            return;
        }
        
        $this->userToDelete = $user;
        $this->showDeleteConfirm = true;
    }

    public function cancelDelete()
    {
        $this->showDeleteConfirm = false;
        $this->userToDelete = null;
    }

    public function deleteUser()
    {
        if (!$this->userToDelete) return;
        
        $name = $this->userToDelete->name;
        $this->userToDelete->delete();
        
        $this->dispatch('notify', type: 'success', message: "User {$name} berhasil dihapus");
        $this->cancelDelete();
        $this->closeModal();
    }

    public function getStatsProperty()
    {
        $isSuperAdmin = auth()->user()->role === 'super_admin';
        $branchId = auth()->user()->branch_id;

        // Staff stats
        $staffQuery = User::whereIn('role', ['super_admin', 'admin', 'librarian', 'staff', 'pustakawan']);
        if (!$isSuperAdmin) {
            $staffQuery->where('branch_id', $branchId);
        }
        
        $staffStats = [
            'total' => (clone $staffQuery)->count(),
            'super_admin' => $isSuperAdmin ? User::where('role', 'super_admin')->count() : 0,
            'admin' => (clone $staffQuery)->where('role', 'admin')->count(),
            'librarian' => (clone $staffQuery)->whereIn('role', ['librarian', 'pustakawan'])->count(),
            'staff' => (clone $staffQuery)->where('role', 'staff')->count(),
        ];

        // Approval stats
        $approvalQuery = User::whereIn('role', ['staff', 'librarian', 'pustakawan']);
        if (!$isSuperAdmin) {
            $approvalQuery->where('branch_id', $branchId);
        }

        $approvalStats = [
            'pending' => (clone $approvalQuery)->where('status', 'pending')->count(),
            'approved' => (clone $approvalQuery)->where('status', 'approved')->count(),
            'rejected' => (clone $approvalQuery)->where('status', 'rejected')->count(),
        ];

        return [
            'staff' => $staffStats,
            'approval' => $approvalStats,
        ];
    }

    public function getBranchesProperty()
    {
        return Branch::orderBy('name')->get(['id', 'name']);
    }

    public function getRolesProperty()
    {
        $currentUser = auth()->user();
        
        if ($currentUser->role === 'super_admin') {
            return [
                'super_admin' => 'Super Admin',
                'admin' => 'Admin Cabang',
                'librarian' => 'Pustakawan',
                'staff' => 'Staff',
            ];
        }
        
        // Admin cabang hanya bisa buat staff level bawah
        return [
            'librarian' => 'Pustakawan',
            'staff' => 'Staff',
        ];
    }

    public function render()
    {
        $isSuperAdmin = auth()->user()->role === 'super_admin';
        $branchId = auth()->user()->branch_id;

        $users = null;
        $activityLogs = null;

        if ($this->mainTab === 'staff') {
            // Staff list
            $query = User::with('branch')
                ->whereIn('role', ['super_admin', 'admin', 'librarian', 'staff', 'pustakawan'])
                ->when(!$isSuperAdmin, fn($q) => $q->where('branch_id', $branchId))
                ->when($this->activeTab !== 'all', fn($q) => 
                    $this->activeTab === 'librarian' 
                        ? $q->whereIn('role', ['librarian', 'pustakawan'])
                        : $q->where('role', $this->activeTab)
                )
                ->when($this->search, fn($q) => 
                    $q->where(fn($q) => $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%"))
                );
            $users = $query->latest()->paginate(12);
        } elseif ($this->mainTab === 'approval') {
            // Approval list
            $query = User::with('branch')
                ->whereIn('role', ['staff', 'librarian', 'pustakawan'])
                ->where('status', $this->activeTab)
                ->when(!$isSuperAdmin, fn($q) => $q->where('branch_id', $branchId))
                ->when($this->search, fn($q) => 
                    $q->where(fn($q) => $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%"))
                );
            $users = $query->latest()->paginate(12);
        } elseif ($this->mainTab === 'activity') {
            // Activity logs
            $query = ActivityLog::with(['user', 'branch'])
                ->when(!$isSuperAdmin, fn($q) => $q->where('branch_id', $branchId))
                ->when($isSuperAdmin && $this->activityBranchId, fn($q) => 
                    $q->where('branch_id', $this->activityBranchId)
                )
                ->when($this->activityModule, fn($q) => $q->where('module', $this->activityModule))
                ->when($this->activityAction, fn($q) => $q->where('action', $this->activityAction))
                ->when($this->activityUserId, fn($q) => $q->where('user_id', $this->activityUserId))
                ->when($this->activityDateStart, fn($q) => 
                    $q->whereDate('created_at', '>=', $this->activityDateStart)
                )
                ->when($this->activityDateEnd, fn($q) => 
                    $q->whereDate('created_at', '<=', $this->activityDateEnd)
                )
                ->when($this->search, fn($q) => 
                    $q->where('description', 'like', "%{$this->search}%")
                );
            $activityLogs = $query->latest()->paginate(20);
        }

        // Get users for activity filter dropdown
        $staffUsers = User::select('id', 'name')
            ->whereIn('role', ['super_admin', 'admin', 'librarian', 'staff', 'pustakawan'])
            ->when(!$isSuperAdmin, fn($q) => $q->where('branch_id', $branchId))
            ->orderBy('name')
            ->get();

        return view('livewire.staff.control.staff-control', [
            'users' => $users,
            'activityLogs' => $activityLogs,
            'staffUsers' => $staffUsers,
            'stats' => $this->stats,
            'branches' => $this->branches,
            'roles' => $this->roles,
            'isSuperAdmin' => $isSuperAdmin,
            'modules' => [
                'auth' => 'Autentikasi',
                'user' => 'Pengguna',
                'biblio' => 'Bibliografi',
                'member' => 'Anggota',
                'circulation' => 'Sirkulasi',
                'attendance' => 'Kehadiran',
                'elibrary' => 'E-Library',
                'settings' => 'Pengaturan',
            ],
            'actions' => [
                'create' => 'Buat',
                'update' => 'Update',
                'delete' => 'Hapus',
                'login' => 'Login',
                'logout' => 'Logout',
                'view' => 'Lihat',
                'export' => 'Export',
                'import' => 'Import',
                'approve' => 'Setujui',
                'reject' => 'Tolak',
            ],
        ])->extends('staff.layouts.app')->section('content');
    }

    public function clearActivityFilters()
    {
        $this->activityBranchId = '';
        $this->activityModule = '';
        $this->activityAction = '';
        $this->activityUserId = '';
        $this->activityDateStart = now()->subDays(7)->format('Y-m-d');
        $this->activityDateEnd = now()->format('Y-m-d');
        $this->search = '';
    }

    // Branch Methods
    public function getBranchesListProperty()
    {
        $query = Branch::withCount(['users', 'books', 'items', 'members']);
        
        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('code', 'like', "%{$this->search}%");
            });
        }
        
        return $query->orderBy('name')->get();
    }

    public function openBranchModal($branchId = null, $viewOnly = false)
    {
        $user = auth()->user();
        $this->branchViewOnly = $viewOnly;
        
        if ($branchId) {
            $branch = Branch::find($branchId);
            if (!$branch) return;
            
            // Non super_admin hanya bisa edit branch sendiri, tapi bisa view semua
            if (!$viewOnly && $user->role !== 'super_admin' && $user->branch_id !== $branchId) {
                return;
            }
            
            $this->editingBranch = $branch;
            $this->branchForm = [
                'name' => $branch->name,
                'code' => $branch->code,
                'address' => $branch->address ?? '',
                'phone' => $branch->phone ?? '',
                'email' => $branch->email ?? '',
                'is_active' => $branch->is_active,
            ];
        } else {
            // Hanya super_admin yang bisa tambah branch baru
            if ($user->role !== 'super_admin') return;
            
            $this->editingBranch = null;
            $this->branchForm = [
                'name' => '',
                'code' => '',
                'address' => '',
                'phone' => '',
                'email' => '',
                'is_active' => true,
            ];
        }
        
        $this->showBranchModal = true;
    }

    public function saveBranch()
    {
        $user = auth()->user();
        
        $rules = [
            'branchForm.name' => 'required|string|max:255',
            'branchForm.address' => 'nullable|string',
            'branchForm.phone' => 'nullable|string|max:50',
            'branchForm.email' => 'nullable|email',
        ];
        
        // Code hanya bisa diubah super_admin
        if ($user->role === 'super_admin') {
            $rules['branchForm.code'] = 'required|string|max:50|unique:branches,code,' . ($this->editingBranch?->id ?? 'NULL');
        }
        
        $this->validate($rules);
        
        $data = [
            'name' => $this->branchForm['name'],
            'address' => $this->branchForm['address'] ?: null,
            'phone' => $this->branchForm['phone'] ?: null,
            'email' => $this->branchForm['email'] ?: null,
        ];
        
        // Hanya super_admin yang bisa ubah code dan is_active
        if ($user->role === 'super_admin') {
            $data['code'] = $this->branchForm['code'];
            $data['is_active'] = $this->branchForm['is_active'];
        }
        
        if ($this->editingBranch) {
            // Validasi akses edit
            if ($user->role !== 'super_admin' && $user->branch_id !== $this->editingBranch->id) {
                return;
            }
            $this->editingBranch->update($data);
            session()->flash('success', 'Branch berhasil diupdate');
        } else {
            if ($user->role !== 'super_admin') return;
            Branch::create($data);
            session()->flash('success', 'Branch berhasil ditambahkan');
        }
        
        $this->showBranchModal = false;
    }

    public function toggleBranchStatus($branchId)
    {
        if (auth()->user()->role !== 'super_admin') return;
        
        $branch = Branch::find($branchId);
        if ($branch) {
            $branch->update(['is_active' => !$branch->is_active]);
        }
    }
}

