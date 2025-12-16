<div class="space-y-5">
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-gradient-to-br from-violet-600 to-indigo-700 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-violet-500/30">
                <i class="fas fa-cog text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Pengaturan</h1>
                <p class="text-sm text-gray-500">Kelola staff dan persetujuan akun</p>
            </div>
        </div>
        
        @if($mainTab === 'staff')
        <button wire:click="openCreateModal" 
                class="px-5 py-2.5 bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-700 hover:to-indigo-700 text-white font-semibold rounded-xl shadow-lg shadow-violet-500/25 transition flex items-center gap-2">
            <i class="fas fa-plus"></i>
            <span>Tambah User</span>
        </button>
        @endif
    </div>

    {{-- Main Tabs --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-1.5">
        <div class="flex gap-1">
            <button wire:click="setMainTab('staff')" 
                    class="flex-1 px-5 py-3 rounded-xl font-semibold text-sm transition flex items-center justify-center gap-2
                    {{ $mainTab === 'staff' ? 'bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-lg' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas fa-users"></i>
                <span>Manajemen Staff</span>
                <span class="px-2 py-0.5 {{ $mainTab === 'staff' ? 'bg-white/20' : 'bg-gray-200' }} rounded-full text-xs">
                    {{ $stats['staff']['total'] }}
                </span>
            </button>
            <button wire:click="setMainTab('approval')" 
                    class="flex-1 px-5 py-3 rounded-xl font-semibold text-sm transition flex items-center justify-center gap-2
                    {{ $mainTab === 'approval' ? 'bg-gradient-to-r from-amber-500 to-orange-600 text-white shadow-lg' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas fa-user-check"></i>
                <span>Persetujuan Akun</span>
                @if($stats['approval']['pending'] > 0)
                <span class="px-2.5 py-0.5 bg-red-500 text-white rounded-full text-xs animate-pulse">
                    {{ $stats['approval']['pending'] }}
                </span>
                @endif
            </button>
        </div>
    </div>

    @if($mainTab === 'staff')
    {{-- Staff Management - Compact Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-2">
        <button wire:click="setTab('all')" 
                class="bg-white rounded-xl px-3 py-2.5 border-2 transition hover:shadow-md flex items-center justify-between
                {{ $activeTab === 'all' ? 'border-violet-500 ring-2 ring-violet-500/20' : 'border-gray-100' }}">
            <div class="w-9 h-9 bg-gradient-to-br from-violet-500 to-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-users text-white text-sm"></i>
            </div>
            <div class="text-right">
                <p class="text-xl font-bold text-gray-900">{{ $stats['staff']['total'] }}</p>
                <p class="text-[10px] text-gray-500 uppercase">Semua</p>
            </div>
        </button>
        
        @if($isSuperAdmin)
        <button wire:click="setTab('super_admin')" 
                class="bg-white rounded-xl px-3 py-2.5 border-2 transition hover:shadow-md flex items-center justify-between
                {{ $activeTab === 'super_admin' ? 'border-red-500 ring-2 ring-red-500/20' : 'border-gray-100' }}">
            <div class="w-9 h-9 bg-gradient-to-br from-red-500 to-rose-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-crown text-white text-sm"></i>
            </div>
            <div class="text-right">
                <p class="text-xl font-bold text-gray-900">{{ $stats['staff']['super_admin'] }}</p>
                <p class="text-[10px] text-gray-500 uppercase">Super Admin</p>
            </div>
        </button>
        @endif
        
        <button wire:click="setTab('admin')" 
                class="bg-white rounded-xl px-3 py-2.5 border-2 transition hover:shadow-md flex items-center justify-between
                {{ $activeTab === 'admin' ? 'border-amber-500 ring-2 ring-amber-500/20' : 'border-gray-100' }}">
            <div class="w-9 h-9 bg-gradient-to-br from-amber-500 to-orange-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-user-shield text-white text-sm"></i>
            </div>
            <div class="text-right">
                <p class="text-xl font-bold text-gray-900">{{ $stats['staff']['admin'] }}</p>
                <p class="text-[10px] text-gray-500 uppercase">Admin</p>
            </div>
        </button>
        
        <button wire:click="setTab('librarian')" 
                class="bg-white rounded-xl px-3 py-2.5 border-2 transition hover:shadow-md flex items-center justify-between
                {{ $activeTab === 'librarian' ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-gray-100' }}">
            <div class="w-9 h-9 bg-gradient-to-br from-emerald-500 to-green-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-book-reader text-white text-sm"></i>
            </div>
            <div class="text-right">
                <p class="text-xl font-bold text-gray-900">{{ $stats['staff']['librarian'] }}</p>
                <p class="text-[10px] text-gray-500 uppercase">Pustakawan</p>
            </div>
        </button>
        
        <button wire:click="setTab('staff')" 
                class="bg-white rounded-xl px-3 py-2.5 border-2 transition hover:shadow-md flex items-center justify-between
                {{ $activeTab === 'staff' ? 'border-blue-500 ring-2 ring-blue-500/20' : 'border-gray-100' }}">
            <div class="w-9 h-9 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-user text-white text-sm"></i>
            </div>
            <div class="text-right">
                <p class="text-xl font-bold text-gray-900">{{ $stats['staff']['staff'] }}</p>
                <p class="text-[10px] text-gray-500 uppercase">Staff</p>
            </div>
        </button>
    </div>

    @else
    {{-- Approval Stats - Compact --}}
    <div class="grid grid-cols-3 gap-2">
        <button wire:click="setTab('pending')" 
                class="bg-white rounded-xl px-3 py-2.5 border-2 transition hover:shadow-md flex items-center justify-between
                {{ $activeTab === 'pending' ? 'border-amber-500 ring-2 ring-amber-500/20' : 'border-gray-100' }}">
            <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-clock text-white"></i>
            </div>
            <div class="text-right">
                <p class="text-xl font-bold text-gray-900">{{ $stats['approval']['pending'] }}</p>
                <p class="text-[10px] text-gray-500 uppercase">Menunggu</p>
            </div>
        </button>
        <button wire:click="setTab('approved')" 
                class="bg-white rounded-xl px-3 py-2.5 border-2 transition hover:shadow-md flex items-center justify-between
                {{ $activeTab === 'approved' ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-gray-100' }}">
            <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-green-500 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-check-circle text-white"></i>
            </div>
            <div class="text-right">
                <p class="text-xl font-bold text-gray-900">{{ $stats['approval']['approved'] }}</p>
                <p class="text-[10px] text-gray-500 uppercase">Disetujui</p>
            </div>
        </button>
        <button wire:click="setTab('rejected')" 
                class="bg-white rounded-xl px-3 py-2.5 border-2 transition hover:shadow-md flex items-center justify-between
                {{ $activeTab === 'rejected' ? 'border-red-500 ring-2 ring-red-500/20' : 'border-gray-100' }}">
            <div class="w-10 h-10 bg-gradient-to-br from-red-400 to-rose-500 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-times-circle text-white"></i>
            </div>
            <div class="text-right">
                <p class="text-xl font-bold text-gray-900">{{ $stats['approval']['rejected'] }}</p>
                <p class="text-[10px] text-gray-500 uppercase">Ditolak</p>
            </div>
        </button>
    </div>
    @endif

    {{-- Search --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3">
        <div class="relative">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama atau email..."
                   class="w-full pl-11 pr-4 py-3 bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-violet-500 text-sm">
        </div>
    </div>

    {{-- User List --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-gray-900">
                    @if($mainTab === 'staff')
                        {{ $activeTab === 'all' ? 'Semua Staff' : ($activeTab === 'super_admin' ? 'Super Admin' : ($activeTab === 'admin' ? 'Admin Cabang' : ($activeTab === 'librarian' ? 'Pustakawan' : 'Staff'))) }}
                    @else
                        {{ $activeTab === 'pending' ? 'Menunggu Persetujuan' : ($activeTab === 'approved' ? 'Disetujui' : 'Ditolak') }}
                    @endif
                </h3>
                <span class="text-sm text-gray-500">{{ $users->total() }} user</span>
            </div>
        </div>

        @if($users->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-4">
            @foreach($users as $user)
            <div class="group bg-gradient-to-br from-gray-50 to-white border border-gray-100 rounded-2xl p-4 hover:shadow-lg hover:border-violet-200 transition-all">
                <div class="flex items-start gap-4">
                    {{-- Avatar --}}
                    <div class="relative flex-shrink-0">
                        @if($user->photo)
                        <img src="{{ $user->getAvatarUrl(100) }}" class="w-14 h-14 rounded-xl object-cover">
                        @else
                        <div class="w-14 h-14 rounded-xl bg-gradient-to-br 
                            {{ $user->role === 'super_admin' ? 'from-red-500 to-rose-600' : 
                               ($user->role === 'admin' ? 'from-amber-500 to-orange-600' : 
                               (in_array($user->role, ['librarian', 'pustakawan']) ? 'from-emerald-500 to-green-600' : 'from-blue-500 to-cyan-600')) }}
                            flex items-center justify-center text-white text-lg font-bold shadow-lg">
                            {{ $user->getInitials() }}
                        </div>
                        @endif
                        <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full border-2 border-white 
                            {{ $user->is_active ? 'bg-emerald-400' : 'bg-gray-300' }}"></span>
                    </div>
                    
                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-gray-900 truncate">{{ $user->name }}</p>
                        <p class="text-sm text-gray-500 truncate">{{ $user->email }}</p>
                        <div class="flex items-center gap-2 mt-2 flex-wrap">
                            @php
                                $roleColors = [
                                    'super_admin' => 'bg-red-100 text-red-700',
                                    'admin' => 'bg-amber-100 text-amber-700',
                                    'librarian' => 'bg-emerald-100 text-emerald-700',
                                    'pustakawan' => 'bg-emerald-100 text-emerald-700',
                                    'staff' => 'bg-blue-100 text-blue-700',
                                ];
                                $roleLabels = [
                                    'super_admin' => 'Super Admin',
                                    'admin' => 'Admin',
                                    'librarian' => 'Pustakawan',
                                    'pustakawan' => 'Pustakawan',
                                    'staff' => 'Staff',
                                ];
                            @endphp
                            <span class="px-2 py-0.5 {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-700' }} rounded-lg text-xs font-semibold">
                                {{ $roleLabels[$user->role] ?? ucfirst($user->role) }}
                            </span>
                            @if($user->branch)
                            <span class="px-2 py-0.5 bg-violet-100 text-violet-700 rounded-lg text-xs font-medium truncate max-w-[100px]">
                                {{ $user->branch->name }}
                            </span>
                            @else
                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded-lg text-xs">Pusat</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                {{-- Actions --}}
                <div class="flex items-center gap-2 mt-4 pt-3 border-t border-gray-100">
                    @if($mainTab === 'approval' && $user->status === 'pending')
                    <button wire:click="viewUser({{ $user->id }})" 
                            class="flex-1 px-3 py-2 bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-700 hover:to-indigo-700 text-white font-medium text-sm rounded-lg transition">
                        <i class="fas fa-eye mr-1"></i> Review
                    </button>
                    @else
                    <button wire:click="viewUser({{ $user->id }})" 
                            class="flex-1 px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium text-sm rounded-lg transition">
                        <i class="fas fa-eye mr-1"></i> Detail
                    </button>
                    @if($user->id !== auth()->id())
                    <button wire:click="toggleUserActive({{ $user->id }})" 
                            class="px-3 py-2 {{ $user->is_active ? 'bg-red-100 hover:bg-red-200 text-red-600' : 'bg-emerald-100 hover:bg-emerald-200 text-emerald-600' }} font-medium text-sm rounded-lg transition"
                            title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                        <i class="fas {{ $user->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                    </button>
                    @endif
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        @if($users->hasPages())
        <div class="p-4 border-t border-gray-100 bg-gray-50">{{ $users->links() }}</div>
        @endif
        @else
        <div class="p-12 text-center">
            <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-users text-gray-300 text-3xl"></i>
            </div>
            <p class="text-gray-500 font-medium">Tidak ada data</p>
            <p class="text-sm text-gray-400 mt-1">Belum ada user yang sesuai filter</p>
        </div>
        @endif
    </div>

    {{-- Modals teleported to body for proper z-index --}}
    <template x-teleport="body">
        <div style="position: relative; z-index: 99999;">
            {{-- Detail Modal --}}
            @if($showModal && $selectedUser)
            <div class="fixed inset-0 overflow-hidden" x-data x-transition>
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="closeModal"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
                {{-- Header --}}
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-violet-600 to-indigo-700 text-white">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-lg">Detail User</h3>
                        <button wire:click="closeModal" class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                {{-- Body --}}
                <div class="p-6 space-y-4">
                    <div class="flex items-center gap-4">
                        @if($selectedUser->photo)
                        <img src="{{ $selectedUser->getAvatarUrl(150) }}" class="w-20 h-20 rounded-2xl object-cover shadow-lg">
                        @else
                        <div class="w-20 h-20 bg-gradient-to-br from-violet-500 to-indigo-600 rounded-2xl flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                            {{ $selectedUser->getInitials() }}
                        </div>
                        @endif
                        <div>
                            <p class="text-xl font-bold text-gray-900">{{ $selectedUser->name }}</p>
                            <p class="text-gray-500">{{ $selectedUser->email }}</p>
                            <div class="flex items-center gap-2 mt-2">
                                @php
                                    $roleColor = match($selectedUser->role) {
                                        'super_admin' => 'bg-red-100 text-red-700',
                                        'admin' => 'bg-amber-100 text-amber-700',
                                        'librarian', 'pustakawan' => 'bg-emerald-100 text-emerald-700',
                                        default => 'bg-blue-100 text-blue-700'
                                    };
                                @endphp
                                <span class="px-2.5 py-1 {{ $roleColor }} rounded-lg text-xs font-semibold">
                                    {{ \App\Models\User::getRoles()[$selectedUser->role] ?? ucfirst($selectedUser->role) }}
                                </span>
                                <span class="px-2.5 py-1 {{ $selectedUser->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }} rounded-lg text-xs font-medium">
                                    {{ $selectedUser->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-gray-500 text-xs mb-1">Cabang</p>
                            <p class="font-semibold text-gray-900">{{ $selectedUser->branch?->name ?? 'Pusat' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-gray-500 text-xs mb-1">Terdaftar</p>
                            <p class="font-semibold text-gray-900">{{ $selectedUser->created_at->format('d M Y') }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-gray-500 text-xs mb-1">Terakhir Online</p>
                            <p class="font-semibold text-gray-900">{{ $selectedUser->getOnlineStatusText() }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-gray-500 text-xs mb-1">Status</p>
                            <p class="font-semibold {{ $selectedUser->status === 'approved' ? 'text-emerald-600' : ($selectedUser->status === 'pending' ? 'text-amber-600' : 'text-red-600') }}">
                                {{ $selectedUser->status === 'approved' ? 'Disetujui' : ($selectedUser->status === 'pending' ? 'Menunggu' : 'Ditolak') }}
                            </p>
                        </div>
                    </div>

                    @if($selectedUser->status === 'pending')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan (jika ditolak)</label>
                        <textarea wire:model="rejectionReason" rows="2" 
                                  class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500" 
                                  placeholder="Masukkan alasan jika menolak..."></textarea>
                        @error('rejectionReason') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    @endif
                </div>

                {{-- Footer --}}
                @if($selectedUser->status === 'pending')
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3">
                    <button wire:click="rejectUser" class="px-5 py-2.5 bg-red-500 hover:bg-red-600 text-white font-medium rounded-xl transition flex items-center gap-2">
                        <i class="fas fa-times"></i> Tolak
                    </button>
                    <button wire:click="approveUser" class="px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white font-medium rounded-xl shadow-lg shadow-emerald-500/25 transition flex items-center gap-2">
                        <i class="fas fa-check"></i> Setujui
                    </button>
                </div>
                @else
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-between">
                    @if($selectedUser->id !== auth()->id())
                    <button wire:click="confirmDeleteUser({{ $selectedUser->id }})" 
                            class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-600 font-medium rounded-xl transition flex items-center gap-2">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                    @else
                    <div></div>
                    @endif
                    <button wire:click="closeModal" class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-xl transition">
                        Tutup
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Create User Modal --}}
    @if($showCreateModal)
    <div class="fixed inset-0 z-[99999] overflow-hidden" x-data x-transition>
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="closeCreateModal"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
                {{-- Header --}}
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-violet-600 to-indigo-700 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <h3 class="font-bold text-lg">Buat User Baru</h3>
                        </div>
                        <button wire:click="closeCreateModal" class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                {{-- Body --}}
                <form wire:submit="createUser" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" wire:model="createForm.name" 
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500"
                               placeholder="Masukkan nama lengkap">
                        @error('createForm.name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                        <input type="email" wire:model="createForm.email" 
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500"
                               placeholder="email@example.com">
                        @error('createForm.email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                        <input type="password" wire:model="createForm.password" 
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500"
                               placeholder="Minimal 8 karakter">
                        @error('createForm.password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Role</label>
                            <select wire:model="createForm.role" 
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                                @foreach($roles as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('createForm.role') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Cabang</label>
                            <select wire:model="createForm.branch_id" 
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500"
                                    {{ !$isSuperAdmin ? 'disabled' : '' }}>
                                @if($isSuperAdmin)
                                <option value="">Pusat (Tanpa Cabang)</option>
                                @endif
                                @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                        <input type="checkbox" wire:model="createForm.is_active" id="is_active" 
                               class="w-5 h-5 text-violet-600 border-gray-300 rounded focus:ring-violet-500">
                        <label for="is_active" class="text-sm font-medium text-gray-700">User Aktif (dapat login)</label>
                    </div>
                </form>

                {{-- Footer --}}
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3">
                    <button wire:click="closeCreateModal" class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-xl transition">
                        Batal
                    </button>
                    <button wire:click="createUser" class="px-5 py-2.5 bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-700 hover:to-indigo-700 text-white font-semibold rounded-xl shadow-lg shadow-violet-500/25 transition flex items-center gap-2">
                        <i class="fas fa-plus"></i> Buat User
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if($showDeleteConfirm && $userToDelete)
    <div class="fixed inset-0 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 text-center">
            <div class="w-16 h-16 bg-red-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-trash text-red-500 text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Hapus User?</h3>
            <p class="text-gray-500 text-sm mb-1">User berikut akan dihapus permanen:</p>
            <p class="font-semibold text-gray-900 mb-4">{{ $userToDelete->name }}</p>
            <p class="text-xs text-gray-400 mb-4">{{ $userToDelete->email }}</p>
            <div class="flex gap-3">
                <button wire:click="cancelDelete" class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition">
                    Batal
                </button>
                <button wire:click="deleteUser" class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-xl transition flex items-center justify-center gap-2">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </div>
        </div>
    </div>
    @endif
        </div>
    </template>

    {{-- Elegant Toast Notifications - Teleported to body for highest z-index --}}
    <template x-teleport="body">
        <div x-data="toastNotification()" 
             x-on:notify.window="show($event.detail)"
             style="position: fixed; top: 20px; right: 20px; z-index: 2147483647;"
             class="flex flex-col gap-2">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="toast.visible"
                 x-transition:enter="transform transition ease-out duration-300"
                 x-transition:enter-start="translate-x-full opacity-0"
                 x-transition:enter-end="translate-x-0 opacity-100"
                 x-transition:leave="transform transition ease-in duration-200"
                 x-transition:leave-start="translate-x-0 opacity-100"
                 x-transition:leave-end="translate-x-full opacity-0"
                 class="flex items-center gap-3 min-w-[320px] max-w-md px-4 py-3 rounded-xl shadow-2xl backdrop-blur-sm border"
                 :class="{
                     'bg-gradient-to-r from-emerald-500 to-green-600 text-white border-emerald-400': toast.type === 'success',
                     'bg-gradient-to-r from-red-500 to-rose-600 text-white border-red-400': toast.type === 'error',
                     'bg-gradient-to-r from-amber-500 to-orange-600 text-white border-amber-400': toast.type === 'warning',
                     'bg-gradient-to-r from-blue-500 to-indigo-600 text-white border-blue-400': toast.type === 'info'
                 }">
                <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center flex-shrink-0">
                    <i class="fas text-lg"
                       :class="{
                           'fa-check-circle': toast.type === 'success',
                           'fa-times-circle': toast.type === 'error',
                           'fa-exclamation-triangle': toast.type === 'warning',
                           'fa-info-circle': toast.type === 'info'
                       }"></i>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-sm" x-text="toast.type === 'success' ? 'Berhasil!' : (toast.type === 'error' ? 'Error!' : (toast.type === 'warning' ? 'Perhatian!' : 'Info'))"></p>
                    <p class="text-sm text-white/90" x-text="toast.message"></p>
                </div>
                <button @click="dismiss(toast.id)" class="w-8 h-8 rounded-lg bg-white/20 hover:bg-white/30 flex items-center justify-center transition">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </template>
        </div>
    </template>

    <script>
        function toastNotification() {
            return {
                toasts: [],
                show(data) {
                    const id = Date.now();
                    const toast = { id, type: data[0]?.type || 'info', message: data[0]?.message || '', visible: true };
                    this.toasts.push(toast);
                    
                    // Auto dismiss after 4 seconds
                    setTimeout(() => this.dismiss(id), 4000);
                },
                dismiss(id) {
                    const toast = this.toasts.find(t => t.id === id);
                    if (toast) {
                        toast.visible = false;
                        setTimeout(() => {
                            this.toasts = this.toasts.filter(t => t.id !== id);
                        }, 300);
                    }
                }
            }
        }
    </script>
</div>

