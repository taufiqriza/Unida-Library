<div class="space-y-5">
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 via-pink-500 to-rose-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-purple-500/25">
                <i class="fas fa-users text-xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Data Anggota</h1>
                <p class="text-sm text-gray-500">
                    @if($isSuperAdmin && !$filterBranchId) Semua Kampus @else {{ auth()->user()->branch->name ?? 'Cabang' }} @endif
                    • {{ number_format($stats['total']) }} anggota
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if($isSuperAdmin)
            <select wire:model.live="filterBranchId" class="px-3 py-2 bg-violet-50 border border-violet-200 rounded-lg text-sm font-medium text-violet-700">
                <option value="">Semua Kampus</option>
                @foreach($branches as $branch)<option value="{{ $branch->id }}">{{ $branch->name }}</option>@endforeach
            </select>
            @endif
            <a href="{{ route('staff.member.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-medium rounded-xl shadow-lg shadow-purple-500/25 transition text-sm">
                <i class="fas fa-plus"></i><span>Tambah Anggota</span>
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-3">
        <div class="bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl p-4 text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center"><i class="fas fa-users text-lg"></i></div>
                <div><p class="text-2xl font-bold">{{ number_format($stats['total']) }}</p><p class="text-xs text-purple-100">Total</p></div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl p-4 text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center"><i class="fas fa-user-check text-lg"></i></div>
                <div><p class="text-2xl font-bold">{{ number_format($stats['active']) }}</p><p class="text-xs text-emerald-100">Aktif</p></div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl p-4 text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center"><i class="fas fa-user-graduate text-lg"></i></div>
                <div><p class="text-2xl font-bold">{{ number_format($stats['mahasiswa']) }}</p><p class="text-xs text-blue-100">Mahasiswa</p></div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-amber-500 to-orange-600 rounded-xl p-4 text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center"><i class="fas fa-chalkboard-teacher text-lg"></i></div>
                <div><p class="text-2xl font-bold">{{ number_format($stats['dosen']) }}</p><p class="text-xs text-amber-100">Dosen</p></div>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-red-400 to-rose-500 rounded-lg flex items-center justify-center"><i class="fas fa-user-clock text-white text-lg"></i></div>
                <div><p class="text-2xl font-bold text-gray-900">{{ number_format($stats['expired']) }}</p><p class="text-xs text-gray-500">Kadaluarsa</p></div>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-lg flex items-center justify-center"><i class="fas fa-user-plus text-white text-lg"></i></div>
                <div><p class="text-2xl font-bold text-gray-900">{{ number_format($stats['new_this_month']) }}</p><p class="text-xs text-gray-500">Baru</p></div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-1.5">
        <div class="flex gap-1">
            <button wire:click="setTab('all')" class="flex-1 px-4 py-3 rounded-xl font-semibold text-sm transition flex items-center justify-center gap-2 {{ $activeTab === 'all' ? 'bg-gradient-to-r from-purple-500 to-pink-600 text-white shadow-lg' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas fa-users"></i><span>Semua</span>
                <span class="px-2 py-0.5 {{ $activeTab === 'all' ? 'bg-white/20' : 'bg-gray-200' }} rounded-full text-xs">{{ number_format($stats['total']) }}</span>
            </button>
            <button wire:click="setTab('mahasiswa')" class="flex-1 px-4 py-3 rounded-xl font-semibold text-sm transition flex items-center justify-center gap-2 {{ $activeTab === 'mahasiswa' ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-lg' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas fa-user-graduate"></i><span>Mahasiswa</span>
                <span class="px-2 py-0.5 {{ $activeTab === 'mahasiswa' ? 'bg-white/20' : 'bg-gray-200' }} rounded-full text-xs">{{ number_format($stats['mahasiswa']) }}</span>
            </button>
            <button wire:click="setTab('dosen')" class="flex-1 px-4 py-3 rounded-xl font-semibold text-sm transition flex items-center justify-center gap-2 {{ $activeTab === 'dosen' ? 'bg-gradient-to-r from-amber-500 to-orange-600 text-white shadow-lg' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas fa-chalkboard-teacher"></i><span>Dosen</span>
                <span class="px-2 py-0.5 {{ $activeTab === 'dosen' ? 'bg-white/20' : 'bg-gray-200' }} rounded-full text-xs">{{ number_format($stats['dosen']) }}</span>
            </button>
            <button wire:click="setTab('karyawan')" class="flex-1 px-4 py-3 rounded-xl font-semibold text-sm transition flex items-center justify-center gap-2 {{ $activeTab === 'karyawan' ? 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-lg' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas fa-user-tie"></i><span>Tendik</span>
                <span class="px-2 py-0.5 {{ $activeTab === 'karyawan' ? 'bg-white/20' : 'bg-gray-200' }} rounded-full text-xs">{{ number_format($stats['karyawan']) }}</span>
            </button>
            @if($canSeeSantri)
            <button wire:click="setTab('santri')" class="flex-1 px-4 py-3 rounded-xl font-semibold text-sm transition flex items-center justify-center gap-2 {{ $activeTab === 'santri' ? 'bg-gradient-to-r from-cyan-500 to-teal-600 text-white shadow-lg' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas fa-mosque"></i><span>Santri</span>
                <span class="px-2 py-0.5 {{ $activeTab === 'santri' ? 'bg-white/20' : 'bg-gray-200' }} rounded-full text-xs">{{ number_format($stats['santri']) }}</span>
            </button>
            @endif
            <button wire:click="setTab('umum')" class="flex-1 px-4 py-3 rounded-xl font-semibold text-sm transition flex items-center justify-center gap-2 {{ $activeTab === 'umum' ? 'bg-gradient-to-r from-violet-500 to-purple-600 text-white shadow-lg' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas fa-user"></i><span>Umum</span>
                <span class="px-2 py-0.5 {{ $activeTab === 'umum' ? 'bg-white/20' : 'bg-gray-200' }} rounded-full text-xs">{{ number_format($stats['umum']) }}</span>
            </button>
        </div>
    </div>

    {{-- Search & Filters --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama, ID, NIM, email..." class="w-full pl-9 pr-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 rounded-lg text-sm">
            </div>
            <div class="flex items-center gap-2">
                <select wire:model.live="filterStatus" class="px-3 py-2.5 bg-gray-50 border-transparent rounded-lg text-sm">
                    <option value="">Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                </select>
                <select wire:model.live="filterExpired" class="px-3 py-2.5 bg-gray-50 border-transparent rounded-lg text-sm">
                    <option value="">Masa Berlaku</option>
                    <option value="valid">Berlaku</option>
                    <option value="expired">Kadaluarsa</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Members Table --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        @if($members->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gradient-to-r from-purple-500 to-pink-600 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium">Anggota</th>
                        <th class="px-4 py-3 text-left font-medium w-28">Tipe</th>
                        <th class="px-4 py-3 text-left font-medium">NIM/NIDN</th>
                        <th class="px-4 py-3 text-left font-medium">Kontak</th>
                        <th class="px-4 py-3 text-center font-medium w-32">Kadaluarsa</th>
                        <th class="px-4 py-3 text-center font-medium w-20">Pinjam</th>
                        <th class="px-4 py-3 text-center font-medium w-20">Status</th>
                        <th class="px-4 py-3 text-center font-medium w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($members as $member)
                    @php $isExpired = $member->expire_date && $member->expire_date < now(); @endphp
                    <tr class="hover:bg-purple-50/30 transition {{ $isExpired ? 'bg-red-50/30' : '' }}">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if($member->photo)
                                <img src="{{ asset('storage/' . $member->photo) }}" class="w-10 h-10 rounded-full object-cover border-2 border-white shadow">
                                @else
                                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white font-bold text-sm">{{ strtoupper(substr($member->name, 0, 1)) }}</div>
                                @endif
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $member->name }}</p>
                                    <p class="text-xs text-gray-500 font-mono">{{ $member->member_id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @php
                            $typeColors = [
                                'Mahasiswa' => 'bg-blue-100 text-blue-700',
                                'Dosen' => 'bg-amber-100 text-amber-700',
                                'Karyawan' => 'bg-emerald-100 text-emerald-700',
                                'Tendik' => 'bg-emerald-100 text-emerald-700',
                                'Santri' => 'bg-cyan-100 text-cyan-700',
                                'Umum' => 'bg-violet-100 text-violet-700',
                            ];
                            $color = $typeColors[$member->memberType?->name] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <span class="px-2 py-1 {{ $color }} text-xs font-medium rounded-full">{{ $member->memberType?->name ?? '-' }}</span>
                        </td>
                        <td class="px-4 py-3"><span class="text-sm text-gray-700">{{ $member->nim_nidn ?: '-' }}</span></td>
                        <td class="px-4 py-3">
                            @if($member->email)<p class="text-xs text-gray-600 truncate max-w-[150px]"><i class="fas fa-envelope text-gray-400 mr-1"></i>{{ $member->email }}</p>@endif
                            @if($member->phone)<p class="text-xs text-gray-500"><i class="fas fa-phone text-gray-400 mr-1"></i>{{ $member->phone }}</p>@endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $isExpired ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">
                                {{ $member->expire_date?->format('d M Y') ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($member->loans_count > 0)
                            <span class="px-2 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-full">{{ $member->loans_count }}</span>
                            @else<span class="text-gray-400">-</span>@endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button wire:click="toggleActive({{ $member->id }})" class="relative w-10 h-5 rounded-full transition {{ $member->is_active ? 'bg-emerald-500' : 'bg-gray-300' }}">
                                <span class="absolute top-0.5 {{ $member->is_active ? 'right-0.5' : 'left-0.5' }} w-4 h-4 bg-white rounded-full shadow transition-all"></span>
                            </button>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1">
                                @if($isExpired)
                                <button wire:click="extendMembership({{ $member->id }})" class="p-2 text-emerald-500 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition" title="Perpanjang"><i class="fas fa-rotate"></i></button>
                                @endif
                                <button wire:click="showDetail({{ $member->id }})" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition" title="Detail"><i class="fas fa-eye"></i></button>
                                <a href="{{ route('staff.member.edit', $member->id) }}" class="p-2 text-blue-500 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition" title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="{{ route('member.card', $member->id) }}" target="_blank" class="p-2 text-purple-500 hover:text-purple-700 hover:bg-purple-50 rounded-lg transition" title="Cetak Kartu"><i class="fas fa-id-card"></i></a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100 bg-gray-50">{{ $members->links() }}</div>
        @else
        <div class="flex flex-col items-center justify-center py-16 text-center px-4">
            <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center mb-4 shadow-lg">
                <i class="fas fa-users text-3xl text-white"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">Belum Ada Anggota</h3>
            <p class="text-gray-500 text-sm mb-4">{{ $search ? 'Tidak ditemukan anggota sesuai pencarian.' : 'Mulai dengan menambahkan anggota pertama.' }}</p>
            @if(!$search)
            <a href="{{ route('staff.member.create') }}" class="px-5 py-2.5 bg-gradient-to-r from-purple-500 to-pink-600 text-white text-sm font-medium rounded-xl hover:shadow-lg transition shadow-md">
                <i class="fas fa-plus mr-2"></i>Tambah Anggota
            </a>
            @endif
        </div>
        @endif
    </div>

    {{-- Member Detail Modal --}}
    @if($showDetailModal && $selectedMember)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background: rgba(0,0,0,0.6);">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4 text-white flex items-center justify-between">
                <h3 class="text-lg font-bold">Detail Anggota</h3>
                <button wire:click="closeDetail" class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center"><i class="fas fa-times"></i></button>
            </div>
            <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-400 to-pink-500 rounded-xl flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                        @if($selectedMember->photo)<img src="{{ asset('storage/' . $selectedMember->photo) }}" class="w-full h-full object-cover rounded-xl">@else{{ strtoupper(substr($selectedMember->name, 0, 1)) }}@endif
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-900">{{ $selectedMember->name }}</h4>
                        <p class="text-sm text-gray-500">{{ $selectedMember->member_id }}</p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium mt-1 {{ $selectedMember->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">{{ $selectedMember->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div class="bg-gray-50 rounded-xl p-3"><p class="text-gray-500 text-xs mb-1">Tipe</p><p class="font-medium text-gray-900">{{ $selectedMember->memberType?->name ?? '-' }}</p></div>
                    <div class="bg-gray-50 rounded-xl p-3"><p class="text-gray-500 text-xs mb-1">Cabang</p><p class="font-medium text-gray-900">{{ $selectedMember->branch?->name ?? '-' }}</p></div>
                    <div class="bg-gray-50 rounded-xl p-3"><p class="text-gray-500 text-xs mb-1">NIM/NIDN</p><p class="font-medium text-gray-900">{{ $selectedMember->nim_nidn ?? '-' }}</p></div>
                    <div class="bg-gray-50 rounded-xl p-3"><p class="text-gray-500 text-xs mb-1">Jenis Kelamin</p><p class="font-medium text-gray-900">{{ $selectedMember->gender == 'L' ? 'Laki-laki' : ($selectedMember->gender == 'P' ? 'Perempuan' : '-') }}</p></div>
                    <div class="bg-gray-50 rounded-xl p-3 col-span-2"><p class="text-gray-500 text-xs mb-1">Email</p><p class="font-medium text-gray-900">{{ $selectedMember->email ?? '-' }}</p></div>
                    <div class="bg-gray-50 rounded-xl p-3"><p class="text-gray-500 text-xs mb-1">Fakultas</p><p class="font-medium text-gray-900">{{ $selectedMember->faculty?->name ?? '-' }}</p></div>
                    <div class="bg-gray-50 rounded-xl p-3"><p class="text-gray-500 text-xs mb-1">Prodi</p><p class="font-medium text-gray-900">{{ $selectedMember->department?->name ?? '-' }}</p></div>
                    <div class="bg-gray-50 rounded-xl p-3"><p class="text-gray-500 text-xs mb-1">Tgl Daftar</p><p class="font-medium text-gray-900">{{ $selectedMember->register_date?->format('d M Y') ?? '-' }}</p></div>
                    <div class="bg-gray-50 rounded-xl p-3"><p class="text-gray-500 text-xs mb-1">Kadaluarsa</p><p class="font-medium {{ $selectedMember->expire_date && $selectedMember->expire_date < now() ? 'text-red-600' : 'text-gray-900' }}">{{ $selectedMember->expire_date?->format('d M Y') ?? '-' }}</p></div>
                </div>
            </div>
            <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-2">
                <button wire:click="closeDetail" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition">Tutup</button>
                <a href="{{ route('staff.member.edit', $selectedMember->id) }}" class="px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-medium rounded-xl transition"><i class="fas fa-edit mr-1"></i>Edit</a>
            </div>
        </div>
    </div>
    @endif
</div>
