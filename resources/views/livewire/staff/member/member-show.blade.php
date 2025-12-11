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

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('staff.member.index') }}" 
               class="w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center transition">
                <i class="fas fa-arrow-left text-gray-600"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Detail Anggota</h1>
                <p class="text-sm text-gray-500">{{ $member->member_id }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if($member->isExpired())
                <button wire:click="extendMembership" 
                        class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-medium rounded-lg transition text-sm">
                    <i class="fas fa-rotate mr-1"></i> Perpanjang
                </button>
            @endif
            <a href="{{ route('staff.member.edit', $member->id) }}" 
               class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition text-sm">
                <i class="fas fa-edit mr-1"></i> Edit
            </a>
            <a href="{{ route('member.card', $member->id) }}" 
               target="_blank"
               class="px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white font-medium rounded-lg transition text-sm">
                <i class="fas fa-id-card mr-1"></i> Cetak Kartu
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Left Column - Profile Card --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                {{-- Photo & Status --}}
                <div class="bg-gradient-to-br from-purple-500 to-pink-500 p-6 text-center">
                    @if($member->photo)
                        <img src="{{ asset('storage/' . $member->photo) }}" 
                             class="w-28 h-36 object-cover rounded-xl mx-auto border-4 border-white shadow-lg">
                    @else
                        <div class="w-28 h-36 bg-white/20 backdrop-blur rounded-xl mx-auto flex items-center justify-center text-white text-4xl font-bold">
                            {{ strtoupper(substr($member->name, 0, 1)) }}
                        </div>
                    @endif
                    <h2 class="text-xl font-bold text-white mt-4">{{ $member->name }}</h2>
                    <p class="text-purple-100 text-sm font-mono">{{ $member->member_id }}</p>
                    <div class="flex items-center justify-center gap-2 mt-3">
                        <span class="px-3 py-1 bg-white/20 backdrop-blur text-white text-xs font-medium rounded-full">
                            {{ $member->memberType->name ?? 'Member' }}
                        </span>
                        @if($member->is_active)
                            <span class="px-3 py-1 bg-emerald-400 text-white text-xs font-medium rounded-full">Aktif</span>
                        @else
                            <span class="px-3 py-1 bg-red-400 text-white text-xs font-medium rounded-full">Nonaktif</span>
                        @endif
                    </div>
                </div>

                {{-- Stats --}}
                <div class="grid grid-cols-2 divide-x divide-gray-100">
                    <div class="p-4 text-center">
                        <p class="text-2xl font-bold text-blue-600">{{ count($activeLoans) }}</p>
                        <p class="text-xs text-gray-500">Dipinjam</p>
                    </div>
                    <div class="p-4 text-center">
                        <p class="text-2xl font-bold text-emerald-600">{{ max(0, ($member->memberType->loan_limit ?? 3) - count($activeLoans)) }}</p>
                        <p class="text-xs text-gray-500">Sisa Kuota</p>
                    </div>
                </div>

                {{-- Membership Status --}}
                <div class="p-4 border-t border-gray-100">
                    @if($member->isExpired())
                        <div class="p-3 bg-red-50 rounded-lg text-center">
                            <i class="fas fa-exclamation-triangle text-red-500 text-lg mb-1"></i>
                            <p class="text-sm font-medium text-red-700">Keanggotaan Kadaluarsa</p>
                            <p class="text-xs text-red-500 mt-1">{{ $member->expire_date?->format('d M Y') }}</p>
                        </div>
                    @else
                        <div class="p-3 bg-emerald-50 rounded-lg text-center">
                            <i class="fas fa-check-circle text-emerald-500 text-lg mb-1"></i>
                            <p class="text-sm font-medium text-emerald-700">Keanggotaan Aktif</p>
                            <p class="text-xs text-emerald-500 mt-1">Berlaku s/d {{ $member->expire_date?->format('d M Y') }}</p>
                        </div>
                    @endif
                </div>

                {{-- Toggle Active --}}
                <div class="p-4 border-t border-gray-100 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">Status Aktif</span>
                    <button wire:click="toggleActive" 
                            class="relative w-12 h-6 rounded-full transition {{ $member->is_active ? 'bg-emerald-500' : 'bg-gray-300' }}">
                        <span class="absolute top-1 {{ $member->is_active ? 'right-1' : 'left-1' }} w-4 h-4 bg-white rounded-full shadow transition-all"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Right Column - Details & Loans --}}
        <div class="lg:col-span-2 space-y-5">
            {{-- Info Details --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="font-bold text-gray-900 mb-4"><i class="fas fa-info-circle text-blue-500 mr-2"></i>Informasi Lengkap</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500">Email</p>
                        <p class="font-medium text-gray-900">{{ $member->email ?: '-' }}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500">Telepon</p>
                        <p class="font-medium text-gray-900">{{ $member->phone ?: '-' }}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500">Jenis Kelamin</p>
                        <p class="font-medium text-gray-900">{{ $member->gender === 'M' ? 'Laki-laki' : ($member->gender === 'F' ? 'Perempuan' : '-') }}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500">Tanggal Lahir</p>
                        <p class="font-medium text-gray-900">{{ $member->birth_date?->format('d M Y') ?: '-' }}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg md:col-span-2">
                        <p class="text-xs text-gray-500">Alamat</p>
                        <p class="font-medium text-gray-900">{{ $member->address ?: '-' }} {{ $member->city ? ', ' . $member->city : '' }}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500">Fakultas</p>
                        <p class="font-medium text-gray-900">{{ $member->faculty?->name ?: '-' }}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500">Program Studi</p>
                        <p class="font-medium text-gray-900">{{ $member->department?->name ?: '-' }}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500">Tanggal Daftar</p>
                        <p class="font-medium text-gray-900">{{ $member->register_date?->format('d M Y') }}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500">Cabang</p>
                        <p class="font-medium text-gray-900">{{ $member->branch?->name }}</p>
                    </div>
                </div>

                @if($member->notes)
                    <div class="mt-4 p-3 bg-amber-50 rounded-lg">
                        <p class="text-xs text-amber-600 mb-1"><i class="fas fa-sticky-note mr-1"></i>Catatan</p>
                        <p class="text-sm text-amber-800">{{ $member->notes }}</p>
                    </div>
                @endif
            </div>

            {{-- Active Loans --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-900">
                        <i class="fas fa-book-open text-blue-500 mr-2"></i>Pinjaman Aktif ({{ count($activeLoans) }})
                    </h3>
                </div>
                
                @if(count($activeLoans) > 0)
                    <div class="divide-y divide-gray-100">
                        @foreach($activeLoans as $loan)
                            @php $isOverdue = $loan->due_date < now(); @endphp
                            <div class="p-4 flex items-center gap-4 {{ $isOverdue ? 'bg-red-50' : '' }}">
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-900 truncate">{{ $loan->item->book->title ?? '-' }}</p>
                                    <p class="text-xs text-gray-500 font-mono">{{ $loan->item->barcode }}</p>
                                </div>
                                <div class="text-right">
                                    @if($isOverdue)
                                        <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-medium rounded-full">
                                            {{ now()->diffInDays($loan->due_date) }} hari terlambat
                                        </span>
                                    @else
                                        <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-xs font-medium rounded-full">
                                            {{ $loan->due_date->diffInDays(now()) }} hari lagi
                                        </span>
                                    @endif
                                    <p class="text-xs text-gray-400 mt-1">Tempo: {{ $loan->due_date->format('d M Y') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-gray-300 text-2xl mb-2"></i>
                        <p>Tidak ada pinjaman aktif</p>
                    </div>
                @endif
            </div>

            {{-- Loan History --}}
            @if(count($loanHistory) > 0)
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-900">
                        <i class="fas fa-history text-gray-500 mr-2"></i>Riwayat Pinjaman Terakhir
                    </h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($loanHistory as $loan)
                        <div class="p-4 flex items-center gap-4">
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-700 truncate">{{ $loan->item->book->title ?? '-' }}</p>
                            </div>
                            <div class="text-right text-xs text-gray-500">
                                <p>Dikembalikan: {{ $loan->return_date?->format('d M Y') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
