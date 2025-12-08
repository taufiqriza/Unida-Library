<x-opac.layout title="Dashboard Anggota">
    {{-- Mobile App Shell --}}
    <div class="min-h-screen bg-gray-50 lg:bg-transparent">
        {{-- Mobile Status Bar Style Header --}}
        <div class="lg:hidden sticky top-0 z-50 bg-gradient-to-r from-primary-600 to-primary-800 safe-area-top">
            <div class="flex items-center justify-between px-4 py-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                        @if($member->photo)
                            <img src="{{ asset('storage/' . $member->photo) }}" alt="{{ $member->name }}" class="w-full h-full object-cover rounded-full">
                        @else
                            <i class="fas fa-user text-white/80"></i>
                        @endif
                    </div>
                    <div>
                        <p class="text-white font-semibold text-sm">{{ Str::limit($member->name, 20) }}</p>
                        <p class="text-primary-200 text-xs">{{ $member->member_id }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if($member->is_active && !$member->isExpired())
                        <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                    @else
                        <span class="w-2 h-2 bg-red-400 rounded-full"></span>
                    @endif
                    <a href="{{ route('opac.logout') }}" class="w-9 h-9 bg-white/10 active:bg-white/20 text-white rounded-full flex items-center justify-center transition-all">
                        <i class="fas fa-sign-out-alt text-sm"></i>
                    </a>
                </div>
            </div>
        </div>

    <div class="max-w-7xl mx-auto px-3 sm:px-4 py-3 lg:py-8">
        <!-- Profile Header - Desktop Only -->
        <div class="hidden lg:block bg-gradient-to-r from-primary-600 to-primary-800 rounded-2xl p-6 mb-6 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            
            <div class="relative flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                        @if($member->photo)
                            <img src="{{ asset('storage/' . $member->photo) }}" alt="{{ $member->name }}" class="w-full h-full object-cover rounded-xl">
                        @else
                            <i class="fas fa-user text-2xl text-white/80"></i>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-xl font-bold truncate">{{ $member->name }}</h1>
                        <p class="text-primary-200 text-sm">{{ $member->member_id }}</p>
                        <div class="flex items-center gap-1.5 mt-1">
                            @if($member->is_active && !$member->isExpired())
                                <span class="px-1.5 py-0.5 bg-emerald-500/20 text-emerald-200 text-xs rounded-full">
                                    <i class="fas fa-check-circle"></i> Aktif
                                </span>
                            @else
                                <span class="px-1.5 py-0.5 bg-red-500/20 text-red-200 text-xs rounded-full">
                                    <i class="fas fa-times-circle"></i> {{ $member->isExpired() ? 'Expired' : 'Nonaktif' }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <a href="{{ route('opac.logout') }}" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-xl transition flex items-center justify-center gap-2">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="text-sm font-medium">Keluar</span>
                </a>
            </div>
        </div>

        <!-- Stats Cards - Filament Style (Icon Left, Text Right) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 lg:gap-4 mb-3 lg:mb-6">
            <div class="bg-white rounded-2xl p-3 lg:p-4 shadow-sm border border-gray-100/50 active:scale-[0.98] transition-transform">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 lg:w-12 lg:h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/25 flex-shrink-0">
                        <i class="fas fa-book-reader text-white text-sm lg:text-base"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-2xl lg:text-3xl font-bold text-gray-900 leading-none">{{ $loans->count() }}</p>
                        <p class="text-[11px] lg:text-xs text-gray-500 font-medium mt-0.5">Dipinjam</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-3 lg:p-4 shadow-sm border border-gray-100/50 active:scale-[0.98] transition-transform">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 lg:w-12 lg:h-12 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/25 flex-shrink-0">
                        <i class="fas fa-check-circle text-white text-sm lg:text-base"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-2xl lg:text-3xl font-bold text-gray-900 leading-none">{{ $history->count() }}</p>
                        <p class="text-[11px] lg:text-xs text-gray-500 font-medium mt-0.5">Dikembalikan</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-3 lg:p-4 shadow-sm border border-gray-100/50 active:scale-[0.98] transition-transform">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 lg:w-12 lg:h-12 bg-gradient-to-br from-orange-400 to-orange-600 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/25 flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-white text-sm lg:text-base"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-2xl lg:text-3xl font-bold text-gray-900 leading-none">{{ $loans->where('due_date', '<', now())->count() }}</p>
                        <p class="text-[11px] lg:text-xs text-gray-500 font-medium mt-0.5">Terlambat</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-3 lg:p-4 shadow-sm border border-gray-100/50 active:scale-[0.98] transition-transform">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 lg:w-12 lg:h-12 bg-gradient-to-br from-red-400 to-red-600 rounded-xl flex items-center justify-center shadow-lg shadow-red-500/25 flex-shrink-0">
                        <i class="fas fa-coins text-white text-sm lg:text-base"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-2xl lg:text-3xl font-bold text-gray-900 leading-none">{{ $fines->count() }}</p>
                        <p class="text-[11px] lg:text-xs text-gray-500 font-medium mt-0.5">Denda</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Menu - Mobile Native Style -->
        <div class="lg:hidden mb-3">
            <div class="bg-white rounded-2xl p-3 shadow-sm border border-gray-100/50">
                <div class="grid grid-cols-5 gap-1">
                    <a href="{{ route('opac.catalog') }}" class="flex flex-col items-center gap-1.5 p-2 rounded-xl active:bg-gray-50 transition-colors">
                        <div class="w-10 h-10 bg-gradient-to-br from-primary-400 to-primary-600 rounded-xl flex items-center justify-center shadow-lg shadow-primary-500/20">
                            <i class="fas fa-search text-white text-sm"></i>
                        </div>
                        <span class="text-[9px] font-medium text-gray-600">Katalog</span>
                    </a>
                    <a href="{{ route('opac.ebooks') }}" class="flex flex-col items-center gap-1.5 p-2 rounded-xl active:bg-gray-50 transition-colors">
                        <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-orange-600 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/20">
                            <i class="fas fa-book text-white text-sm"></i>
                        </div>
                        <span class="text-[9px] font-medium text-gray-600">E-Book</span>
                    </a>
                    <a href="{{ route('opac.etheses') }}" class="flex flex-col items-center gap-1.5 p-2 rounded-xl active:bg-gray-50 transition-colors">
                        <div class="w-10 h-10 bg-gradient-to-br from-pink-400 to-pink-600 rounded-xl flex items-center justify-center shadow-lg shadow-pink-500/20">
                            <i class="fas fa-graduation-cap text-white text-sm"></i>
                        </div>
                        <span class="text-[9px] font-medium text-gray-600">E-Thesis</span>
                    </a>
                    <a href="{{ route('opac.member.submissions') }}" class="flex flex-col items-center gap-1.5 p-2 rounded-xl active:bg-gray-50 transition-colors">
                        <div class="w-10 h-10 bg-gradient-to-br from-violet-400 to-violet-600 rounded-xl flex items-center justify-center shadow-lg shadow-violet-500/20">
                            <i class="fas fa-upload text-white text-sm"></i>
                        </div>
                        <span class="text-[9px] font-medium text-gray-600">Unggah</span>
                    </a>
                    <a href="{{ route('opac.news') }}" class="flex flex-col items-center gap-1.5 p-2 rounded-xl active:bg-gray-50 transition-colors">
                        <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/20">
                            <i class="fas fa-newspaper text-white text-sm"></i>
                        </div>
                        <span class="text-[9px] font-medium text-gray-600">Berita</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-4 lg:space-y-6">
                <!-- Active Loans -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100/50 overflow-hidden">
                    <div class="p-4 lg:p-5 border-b border-gray-100 flex items-center justify-between">
                        <h2 class="font-bold text-gray-900 text-sm lg:text-base flex items-center gap-2">
                            <div class="w-7 h-7 bg-primary-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-book-reader text-primary-600 text-xs"></i>
                            </div>
                            Peminjaman Aktif
                        </h2>
                        <span class="px-2.5 py-1 bg-primary-500 text-white text-[10px] lg:text-xs font-semibold rounded-lg shadow-sm">{{ $loans->count() }} buku</span>
                    </div>
                    <div class="p-3 lg:p-5">
                        @if($loans->count() > 0)
                        <div class="space-y-3">
                            @foreach($loans as $loan)
                            <div class="flex gap-3 p-3 bg-gray-50/80 rounded-2xl active:bg-gray-100 transition-colors">
                                <div class="w-12 h-16 lg:w-14 lg:h-20 bg-gradient-to-br from-primary-100 to-primary-200 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                                    @if($loan->item?->book?->cover)
                                        <img src="{{ asset('storage/' . $loan->item->book->cover) }}" alt="" class="w-full h-full object-cover rounded-xl">
                                    @else
                                        <i class="fas fa-book text-primary-400 text-base lg:text-xl"></i>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-gray-900 text-xs lg:text-sm line-clamp-2 leading-tight">{{ $loan->item?->book?->title ?? '-' }}</h3>
                                    <p class="text-[10px] lg:text-xs text-gray-400 mt-0.5">{{ Str::limit($loan->item?->book?->author ?? '', 25) }}</p>
                                    <div class="flex items-center gap-2 mt-2">
                                        @php
                                            $daysRemaining = (int) round(now()->startOfDay()->floatDiffInDays($loan->due_date->startOfDay(), false));
                                        @endphp
                                        @if($daysRemaining < 0)
                                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-red-500 text-white text-[10px] lg:text-xs font-semibold rounded-lg shadow-sm">
                                                <i class="fas fa-exclamation-circle"></i> Telat {{ abs($daysRemaining) }} hari
                                            </span>
                                        @elseif($daysRemaining == 0)
                                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-orange-500 text-white text-[10px] lg:text-xs font-semibold rounded-lg shadow-sm">
                                                <i class="fas fa-clock"></i> Hari ini
                                            </span>
                                        @elseif($daysRemaining <= 3)
                                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-amber-500 text-white text-[10px] lg:text-xs font-semibold rounded-lg shadow-sm">
                                                <i class="fas fa-hourglass-half"></i> {{ $daysRemaining }} hari
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-emerald-500 text-white text-[10px] lg:text-xs font-semibold rounded-lg shadow-sm">
                                                <i class="fas fa-calendar-check"></i> {{ $daysRemaining }} hari
                                            </span>
                                        @endif
                                        <span class="text-[10px] text-gray-400 font-medium">{{ $loan->due_date->format('d M') }}</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-10 lg:py-12">
                            <div class="w-16 h-16 lg:w-20 lg:h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-inner">
                                <i class="fas fa-book-open text-2xl lg:text-3xl text-gray-300"></i>
                            </div>
                            <p class="text-gray-500 text-sm font-medium">Tidak ada peminjaman aktif</p>
                            <p class="text-gray-400 text-xs mt-1">Yuk pinjam buku di perpustakaan!</p>
                            <a href="{{ route('opac.catalog') }}" class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 bg-gradient-to-r from-primary-500 to-primary-600 text-white text-sm font-semibold rounded-xl hover:from-primary-600 hover:to-primary-700 active:scale-95 transition-all shadow-lg shadow-primary-500/30">
                                <i class="fas fa-search"></i> Cari Buku
                            </a>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Loan History -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100/50 overflow-hidden">
                    <div class="p-4 lg:p-5 border-b border-gray-100">
                        <h2 class="font-bold text-gray-900 text-sm lg:text-base flex items-center gap-2">
                            <div class="w-7 h-7 bg-emerald-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-history text-emerald-600 text-xs"></i>
                            </div>
                            Riwayat Peminjaman
                        </h2>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @forelse($history as $loan)
                        <div class="p-3 lg:p-4 flex items-center gap-3 active:bg-gray-50 transition-colors">
                            <div class="w-9 h-9 lg:w-10 lg:h-10 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                                <i class="fas fa-check text-white text-xs lg:text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs lg:text-sm text-gray-900 font-medium line-clamp-1">{{ $loan->item?->book?->title ?? '-' }}</p>
                                <p class="text-[10px] lg:text-xs text-gray-400 mt-0.5">Dikembalikan {{ $loan->return_date?->format('d M Y') }}</p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-300 text-xs"></i>
                        </div>
                        @empty
                        <div class="p-8 lg:p-10 text-center">
                            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-inbox text-gray-300 text-lg"></i>
                            </div>
                            <p class="text-gray-500 text-sm">Belum ada riwayat</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-3 lg:space-y-6">
                <!-- Fines Alert -->
                @if($fines->count() > 0)
                <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-2xl shadow-sm border border-orange-200/50 overflow-hidden">
                    <div class="p-4 lg:p-5 border-b border-orange-200/50">
                        <h2 class="font-bold text-orange-800 text-sm lg:text-base flex items-center gap-2">
                            <div class="w-7 h-7 bg-orange-500 rounded-lg flex items-center justify-center">
                                <i class="fas fa-exclamation-circle text-white text-xs"></i>
                            </div>
                            Denda Aktif
                        </h2>
                    </div>
                    <div class="p-3 lg:p-5">
                        <div class="space-y-2">
                            @foreach($fines as $fine)
                            <div class="p-3 bg-white/80 rounded-xl border border-orange-100">
                                <p class="text-xs lg:text-sm text-gray-700 line-clamp-1 font-medium">{{ $fine->description ?? 'Denda keterlambatan' }}</p>
                                <p class="text-lg lg:text-xl font-bold text-orange-600 mt-1">Rp {{ number_format($fine->amount, 0, ',', '.') }}</p>
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-4 p-3 bg-orange-500 rounded-xl flex items-center justify-between">
                            <span class="text-sm font-medium text-orange-100">Total Denda</span>
                            <span class="text-xl font-bold text-white">Rp {{ number_format($fines->sum('amount'), 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Member Info - Collapsible on Mobile -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100/50 overflow-hidden" x-data="{ open: false }">
                    <button @click="open = !open" class="lg:hidden w-full p-4 flex items-center justify-between active:bg-gray-50 transition-colors">
                        <h2 class="font-bold text-gray-900 text-sm flex items-center gap-2">
                            <div class="w-7 h-7 bg-primary-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-id-card text-primary-600 text-xs"></i>
                            </div>
                            Info Anggota
                        </h2>
                        <div class="w-7 h-7 bg-gray-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </div>
                    </button>
                    <div class="hidden lg:block p-5 border-b border-gray-100">
                        <h2 class="font-bold text-gray-900 flex items-center gap-2">
                            <div class="w-7 h-7 bg-primary-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-id-card text-primary-600 text-xs"></i>
                            </div>
                            Informasi Anggota
                        </h2>
                    </div>
                    <div class="p-4 lg:p-5 space-y-0 border-t lg:border-t-0 border-gray-100" :class="{ 'hidden lg:block': !open }">
                        <div class="flex items-center justify-between py-3 border-b border-gray-50">
                            <span class="text-xs lg:text-sm text-gray-500 font-medium">Email</span>
                            <span class="text-xs lg:text-sm text-gray-900 truncate ml-2 font-medium">{{ $member->email ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between py-3 border-b border-gray-50">
                            <span class="text-xs lg:text-sm text-gray-500 font-medium">No. HP</span>
                            <span class="text-xs lg:text-sm text-gray-900 font-medium">{{ $member->phone ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between py-3 border-b border-gray-50">
                            <span class="text-xs lg:text-sm text-gray-500 font-medium">Tipe</span>
                            <span class="px-2 py-0.5 bg-primary-100 text-primary-700 text-xs font-semibold rounded-lg">{{ $member->memberType?->name ?? 'Umum' }}</span>
                        </div>
                        <div class="flex items-center justify-between py-3">
                            <span class="text-xs lg:text-sm text-gray-500 font-medium">Berlaku s/d</span>
                            <span class="text-xs lg:text-sm text-gray-900 font-medium">{{ $member->expire_date?->format('d M Y') ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Unggah Tugas Akhir CTA -->
                <a href="{{ route('opac.member.submissions') }}" class="block bg-gradient-to-br from-violet-500 to-purple-600 rounded-2xl p-4 lg:p-5 text-white shadow-lg shadow-violet-500/30 hover:shadow-xl hover:shadow-violet-500/40 transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0 backdrop-blur-sm group-hover:scale-110 transition-transform">
                            <i class="fas fa-upload text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-sm lg:text-base">Unggah Tugas Akhir</h3>
                            <p class="text-violet-200 text-xs lg:text-sm">Submit skripsi/tesis/disertasi</p>
                        </div>
                        <i class="fas fa-chevron-right text-white/60 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </a>

                <!-- Quick Links - Desktop Only -->
                <div class="hidden lg:block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-5 border-b border-gray-100">
                        <h2 class="font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-th-large text-primary-500"></i> Menu Cepat
                        </h2>
                    </div>
                    <div class="p-3">
                        <div class="grid grid-cols-2 gap-2">
                            <a href="{{ route('opac.catalog') }}" class="flex flex-col items-center gap-2 p-4 bg-gray-50 hover:bg-primary-50 rounded-xl transition group">
                                <div class="w-10 h-10 bg-primary-100 group-hover:bg-primary-200 rounded-xl flex items-center justify-center transition">
                                    <i class="fas fa-search text-primary-600"></i>
                                </div>
                                <span class="text-xs text-gray-700 group-hover:text-primary-700 font-medium">Katalog</span>
                            </a>
                            <a href="{{ route('opac.ebooks') }}" class="flex flex-col items-center gap-2 p-4 bg-gray-50 hover:bg-orange-50 rounded-xl transition group">
                                <div class="w-10 h-10 bg-orange-100 group-hover:bg-orange-200 rounded-xl flex items-center justify-center transition">
                                    <i class="fas fa-file-pdf text-orange-600"></i>
                                </div>
                                <span class="text-xs text-gray-700 group-hover:text-orange-700 font-medium">E-Book</span>
                            </a>
                            <a href="{{ route('opac.etheses') }}" class="flex flex-col items-center gap-2 p-4 bg-gray-50 hover:bg-pink-50 rounded-xl transition group">
                                <div class="w-10 h-10 bg-pink-100 group-hover:bg-pink-200 rounded-xl flex items-center justify-center transition">
                                    <i class="fas fa-graduation-cap text-pink-600"></i>
                                </div>
                                <span class="text-xs text-gray-700 group-hover:text-pink-700 font-medium">E-Thesis</span>
                            </a>
                            <a href="{{ route('opac.news') }}" class="flex flex-col items-center gap-2 p-4 bg-gray-50 hover:bg-emerald-50 rounded-xl transition group">
                                <div class="w-10 h-10 bg-emerald-100 group-hover:bg-emerald-200 rounded-xl flex items-center justify-center transition">
                                    <i class="fas fa-newspaper text-emerald-600"></i>
                                </div>
                                <span class="text-xs text-gray-700 group-hover:text-emerald-700 font-medium">Berita</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Help - Native Style -->
                <div class="bg-gradient-to-br from-primary-500 via-primary-600 to-primary-700 rounded-2xl p-4 lg:p-5 text-white shadow-lg shadow-primary-500/30">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0 backdrop-blur-sm">
                            <i class="fab fa-whatsapp text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-sm lg:text-base">Butuh Bantuan?</h3>
                            <p class="text-primary-100 text-xs lg:text-sm">Chat dengan pustakawan</p>
                        </div>
                        <a href="https://wa.me/6285183053934" target="_blank" class="px-4 py-2 bg-white text-primary-600 font-semibold rounded-xl text-xs lg:text-sm active:scale-95 transition-all shadow-lg">
                            Chat
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</x-opac.layout>
