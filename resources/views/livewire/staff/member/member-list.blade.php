@push('styles')
<style>
    .stat-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 12px 24px -8px rgba(0, 0, 0, 0.12); }
    .member-row { transition: all 0.2s ease; }
    .member-row:hover { background: linear-gradient(90deg, rgba(59, 130, 246, 0.05) 0%, transparent 100%); }
</style>
@endpush

<div class="space-y-5">
    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-3">
            <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center text-white">
                <i class="fas fa-check"></i>
            </div>
            <p class="text-emerald-700 font-medium">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3">
            <div class="w-8 h-8 bg-red-500 rounded-lg flex items-center justify-center text-white">
                <i class="fas fa-times"></i>
            </div>
            <p class="text-red-700 font-medium">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 via-pink-500 to-rose-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-purple-500/25">
                <i class="fas fa-users text-xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Data Anggota</h1>
                <p class="text-sm text-gray-500">
                    @if($isSuperAdmin && !$filterBranchId)
                        Semua Kampus • {{ $stats['total'] }} anggota
                    @elseif($isSuperAdmin && $filterBranchId)
                        {{ $branches->firstWhere('id', $filterBranchId)?->name ?? 'Cabang' }} • {{ $stats['total'] }} anggota
                    @else
                        {{ auth()->user()->branch->name ?? 'Cabang' }} • {{ $stats['total'] }} anggota
                    @endif
                </p>
            </div>
        </div>

        <a href="{{ route('staff.member.create') }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-medium rounded-xl shadow-lg shadow-purple-500/25 transition text-sm">
            <i class="fas fa-plus"></i>
            <span>Tambah Anggota</span>
        </a>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="stat-card bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl p-4 text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold">{{ number_format($stats['total']) }}</p>
                    <p class="text-xs text-purple-100">Total Anggota</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl p-4 text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-check"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold">{{ number_format($stats['active']) }}</p>
                    <p class="text-xs text-emerald-100">Aktif</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-red-400 to-rose-500 rounded-lg flex items-center justify-center text-white">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['expired']) }}</p>
                    <p class="text-xs text-gray-500">Kadaluarsa</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-lg flex items-center justify-center text-white">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['new_this_month']) }}</p>
                    <p class="text-xs text-gray-500">Baru Bulan Ini</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Search & Filters --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3">
        <div class="flex flex-col lg:flex-row gap-3">
            {{-- Search --}}
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input wire:model.live.debounce.300ms="search" 
                       type="text" 
                       placeholder="Cari nama, ID, email, atau telepon..."
                       class="w-full pl-9 pr-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 rounded-lg text-sm">
            </div>
            
            {{-- Filters --}}
            <div class="flex flex-wrap items-center gap-2">
                {{-- Branch Filter (Super Admin only) --}}
                @if($isSuperAdmin)
                <select wire:model.live="filterBranchId" class="px-3 py-2.5 bg-violet-50 border-violet-200 text-violet-700 rounded-lg text-sm font-medium">
                    <option value="">🌐 Semua Kampus</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
                @endif
                
                <select wire:model.live="filterType" class="px-3 py-2.5 bg-gray-50 border-transparent rounded-lg text-sm">
                    <option value="">Semua Tipe</option>
                    @foreach($memberTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
                <select wire:model.live="filterStatus" class="px-3 py-2.5 bg-gray-50 border-transparent rounded-lg text-sm">
                    <option value="">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                </select>
                <select wire:model.live="filterExpired" class="px-3 py-2.5 bg-gray-50 border-transparent rounded-lg text-sm">
                    <option value="">Semua</option>
                    <option value="valid">Masih Berlaku</option>
                    <option value="expired">Kadaluarsa</option>
                </select>
                <select wire:model.live="filterLinked" class="px-3 py-2.5 bg-gray-50 border-transparent rounded-lg text-sm">
                    <option value="">Login</option>
                    <option value="linked">✓ Terhubung</option>
                    <option value="unlinked">✗ Belum</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Results Info --}}
    @if($members->total() > 0)
    <div class="text-sm text-gray-500">
        Menampilkan <span class="font-medium text-gray-700">{{ $members->firstItem() }}-{{ $members->lastItem() }}</span> 
        dari <span class="font-medium text-gray-700">{{ $members->total() }}</span> anggota
    </div>
    @endif

    {{-- Members Table --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        @if($members->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-50 to-gray-100 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-4 py-3">Anggota</th>
                            <th class="px-4 py-3">Tipe</th>
                            <th class="px-4 py-3">Kontak</th>
                            <th class="px-4 py-3 text-center">Kadaluarsa</th>
                            <th class="px-4 py-3 text-center">Pinjaman</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($members as $member)
                            @php
                                $isExpired = $member->expire_date < now();
                            @endphp
                            <tr class="member-row {{ $isExpired ? 'bg-red-50/50' : '' }}">
                                {{-- Member Info --}}
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        @if($member->photo)
                                            <img src="{{ asset('storage/' . $member->photo) }}" class="w-10 h-10 rounded-full object-cover border-2 border-white shadow">
                                        @else
                                            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white font-bold">
                                                {{ strtoupper(substr($member->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $member->name }}</p>
                                            <div class="flex items-center gap-1.5">
                                                <p class="text-xs text-gray-500 font-mono">{{ $member->member_id }}</p>
                                                @if($member->email && $member->profile_completed)
                                                <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 bg-green-100 text-green-700 text-[10px] font-medium rounded" title="Terhubung: {{ $member->email }}">
                                                    <svg class="w-3 h-3" viewBox="0 0 24 24"><path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Type --}}
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 bg-purple-100 text-purple-700 text-xs font-medium rounded-full">
                                        {{ $member->memberType->name ?? '-' }}
                                    </span>
                                </td>

                                {{-- Contact --}}
                                <td class="px-4 py-3">
                                    <div class="text-sm">
                                        @if($member->email)
                                            <p class="text-gray-600"><i class="fas fa-envelope text-gray-400 mr-1 text-xs"></i>{{ Str::limit($member->email, 20) }}</p>
                                        @endif
                                        @if($member->phone)
                                            <p class="text-gray-500 text-xs"><i class="fas fa-phone text-gray-400 mr-1"></i>{{ $member->phone }}</p>
                                        @endif
                                    </div>
                                </td>

                                {{-- Expire Date --}}
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $isExpired ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">
                                        {{ $member->expire_date?->format('d M Y') }}
                                    </span>
                                </td>

                                {{-- Loans Count --}}
                                <td class="px-4 py-3 text-center">
                                    @if($member->loans_count > 0)
                                        <span class="px-2 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-full">
                                            {{ $member->loans_count }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="px-4 py-3 text-center">
                                    <button wire:click="toggleActive({{ $member->id }})" 
                                            class="relative w-10 h-5 rounded-full transition {{ $member->is_active ? 'bg-emerald-500' : 'bg-gray-300' }}">
                                        <span class="absolute top-0.5 {{ $member->is_active ? 'right-0.5' : 'left-0.5' }} w-4 h-4 bg-white rounded-full shadow transition-all"></span>
                                    </button>
                                </td>

                                {{-- Actions --}}
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-1">
                                        @if($isExpired)
                                            <button wire:click="extendMembership({{ $member->id }})" 
                                                    class="p-2 text-emerald-500 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition"
                                                    title="Perpanjang">
                                                <i class="fas fa-rotate"></i>
                                            </button>
                                        @endif
                                        <button wire:click="showDetail({{ $member->id }})" 
                                           class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition"
                                           title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="{{ route('staff.member.edit', $member->id) }}" 
                                           class="p-2 text-blue-500 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition"
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('member.card', $member->id) }}" 
                                           target="_blank"
                                           class="p-2 text-purple-500 hover:text-purple-700 hover:bg-purple-50 rounded-lg transition"
                                           title="Cetak Kartu">
                                            <i class="fas fa-id-card"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="p-4 border-t border-gray-100 bg-gray-50">
                {{ $members->links() }}
            </div>
        @else
            {{-- Empty State --}}
            <div class="flex flex-col items-center justify-center py-16 text-center px-4">
                <div class="w-16 h-16 bg-purple-100 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-users text-2xl text-purple-500"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">Belum Ada Anggota</h3>
                <p class="text-gray-500 text-sm mb-4">
                    @if($search || $filterType || $filterStatus || $filterExpired)
                        Tidak ditemukan anggota sesuai filter.
                    @else
                        Mulai dengan menambahkan anggota pertama.
                    @endif
                </p>
                @if($search || $filterType || $filterStatus || $filterExpired)
                    <button wire:click="$set('search', ''); $set('filterType', ''); $set('filterStatus', ''); $set('filterExpired', '')" 
                            class="text-purple-600 hover:underline text-sm">
                        Reset Filter
                    </button>
                @else
                    <a href="{{ route('staff.member.create') }}" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm">
                        Tambah Anggota
                    </a>
                @endif
            </div>
        @endif
    </div>

    {{-- Member Detail Modal --}}
    @if($showDetailModal && $selectedMember)
    <div class="fixed inset-0 z-50 overflow-y-auto" x-data x-init="document.body.classList.add('overflow-hidden')" x-on:remove="document.body.classList.remove('overflow-hidden')">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" wire:click="closeDetail"></div>
            
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full mx-auto overflow-hidden">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4 text-white">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold">Detail Anggota</h3>
                        <button wire:click="closeDetail" class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                {{-- Content --}}
                <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                    {{-- Photo & Name --}}
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-purple-400 to-pink-500 rounded-xl flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                            @if($selectedMember->photo)
                                <img src="{{ Storage::url($selectedMember->photo) }}" class="w-full h-full object-cover rounded-xl">
                            @else
                                {{ strtoupper(substr($selectedMember->name, 0, 1)) }}
                            @endif
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-gray-900">{{ $selectedMember->name }}</h4>
                            <p class="text-sm text-gray-500">{{ $selectedMember->member_id }}</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium mt-1 {{ $selectedMember->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                {{ $selectedMember->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                    </div>

                    {{-- Info Grid --}}
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-gray-500 text-xs mb-1">Tipe</p>
                            <p class="font-medium text-gray-900">{{ $selectedMember->memberType?->name ?? '-' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-gray-500 text-xs mb-1">Cabang</p>
                            <p class="font-medium text-gray-900">{{ $selectedMember->branch?->name ?? '-' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-gray-500 text-xs mb-1">NIM/NIDN</p>
                            <p class="font-medium text-gray-900">{{ $selectedMember->nim_nidn ?? '-' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-gray-500 text-xs mb-1">Jenis Kelamin</p>
                            <p class="font-medium text-gray-900">{{ $selectedMember->gender == 'L' ? 'Laki-laki' : ($selectedMember->gender == 'P' ? 'Perempuan' : '-') }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3 col-span-2">
                            <p class="text-gray-500 text-xs mb-1">Email</p>
                            <p class="font-medium text-gray-900">{{ $selectedMember->email ?? '-' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-gray-500 text-xs mb-1">Fakultas</p>
                            <p class="font-medium text-gray-900">{{ $selectedMember->faculty?->name ?? '-' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-gray-500 text-xs mb-1">Prodi</p>
                            <p class="font-medium text-gray-900">{{ $selectedMember->department?->name ?? '-' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-gray-500 text-xs mb-1">Tgl Daftar</p>
                            <p class="font-medium text-gray-900">{{ $selectedMember->register_date?->format('d M Y') ?? '-' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-gray-500 text-xs mb-1">Tgl Kadaluarsa</p>
                            <p class="font-medium {{ $selectedMember->expire_date && $selectedMember->expire_date < now() ? 'text-red-600' : 'text-gray-900' }}">
                                {{ $selectedMember->expire_date?->format('d M Y') ?? '-' }}
                            </p>
                        </div>
                    </div>

                    {{-- Google Account Info --}}
                    <div class="border-t pt-4">
                        <h5 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                            Akun Google
                        </h5>
                        @if($selectedMember->profile_completed && $selectedMember->email)
                            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-3">
                                <div class="flex items-center gap-2 text-emerald-700">
                                    <i class="fas fa-check-circle"></i>
                                    <span class="text-sm font-medium">Terhubung</span>
                                </div>
                                <p class="text-xs text-emerald-600 mt-1">{{ $selectedMember->email }}</p>
                                @if($selectedMember->updated_at)
                                    <p class="text-xs text-emerald-500 mt-1">Terhubung: {{ $selectedMember->updated_at->format('d M Y H:i') }}</p>
                                @endif
                            </div>
                        @else
                            <div class="bg-gray-50 border border-gray-200 rounded-xl p-3">
                                <div class="flex items-center gap-2 text-gray-500">
                                    <i class="fas fa-times-circle"></i>
                                    <span class="text-sm font-medium">Belum Terhubung</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Last Login --}}
                    <div class="border-t pt-4">
                        <h5 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                            <i class="fas fa-clock text-gray-400"></i>
                            Aktivitas Login
                        </h5>
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-gray-500 text-xs mb-1">Terakhir Login</p>
                            <p class="font-medium text-gray-900">
                                {{ $selectedMember->last_login_at ? $selectedMember->last_login_at->format('d M Y H:i') : 'Belum pernah login' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-2">
                    <a href="{{ route('staff.member.edit', $selectedMember->id) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                    <button wire:click="closeDetail" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-lg transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
