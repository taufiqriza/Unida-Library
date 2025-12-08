<x-opac.layout title="Dashboard Anggota">
    <div class="max-w-7xl mx-auto px-4 py-6 lg:py-8">
        <!-- Profile Header -->
        <div class="bg-gradient-to-r from-primary-600 to-primary-800 rounded-2xl p-6 mb-6 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
            
            <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 lg:w-20 lg:h-20 bg-white/20 rounded-2xl flex items-center justify-center">
                        @if($member->photo)
                            <img src="{{ asset('storage/' . $member->photo) }}" alt="{{ $member->name }}" class="w-full h-full object-cover rounded-2xl">
                        @else
                            <i class="fas fa-user text-3xl lg:text-4xl text-white/80"></i>
                        @endif
                    </div>
                    <div>
                        <h1 class="text-xl lg:text-2xl font-bold">{{ $member->name }}</h1>
                        <p class="text-primary-200 text-sm">{{ $member->member_id }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            @if($member->is_active && !$member->isExpired())
                                <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-200 text-xs rounded-full">
                                    <i class="fas fa-check-circle mr-1"></i> Aktif
                                </span>
                            @else
                                <span class="px-2 py-0.5 bg-red-500/20 text-red-200 text-xs rounded-full">
                                    <i class="fas fa-times-circle mr-1"></i> {{ $member->isExpired() ? 'Kadaluarsa' : 'Tidak Aktif' }}
                                </span>
                            @endif
                            <span class="text-primary-200 text-xs">
                                <i class="fas fa-calendar mr-1"></i> s/d {{ $member->expire_date?->format('d M Y') ?? '-' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('opac.logout') }}" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-sm font-medium rounded-xl transition flex items-center gap-2">
                        <i class="fas fa-sign-out-alt"></i> <span class="hidden sm:inline">Keluar</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-book-reader text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $loans->count() }}</p>
                        <p class="text-xs text-gray-500">Dipinjam</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-check-circle text-emerald-600"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $history->count() }}</p>
                        <p class="text-xs text-gray-500">Dikembalikan</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-orange-600"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $loans->where('due_date', '<', now())->count() }}</p>
                        <p class="text-xs text-gray-500">Terlambat</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-money-bill-wave text-red-600"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $fines->count() > 0 ? 'Rp ' . number_format($fines->sum('amount'), 0, ',', '.') : '0' }}</p>
                        <p class="text-xs text-gray-500">Denda</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Active Loans -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                        <h2 class="font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-book-reader text-primary-500"></i> Peminjaman Aktif
                        </h2>
                        <span class="px-2 py-1 bg-primary-100 text-primary-700 text-xs font-medium rounded-lg">{{ $loans->count() }} buku</span>
                    </div>
                    <div class="p-5">
                        @if($loans->count() > 0)
                        <div class="space-y-4">
                            @foreach($loans as $loan)
                            <div class="flex gap-4 p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition">
                                <div class="w-14 h-20 bg-gradient-to-br from-primary-100 to-primary-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                    @if($loan->item?->book?->cover)
                                        <img src="{{ asset('storage/' . $loan->item->book->cover) }}" alt="" class="w-full h-full object-cover rounded-lg">
                                    @else
                                        <i class="fas fa-book text-primary-400 text-xl"></i>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-gray-900 text-sm line-clamp-2">{{ $loan->item?->book?->title ?? '-' }}</h3>
                                    <p class="text-xs text-gray-500 mt-1">{{ $loan->item?->book?->author ?? '' }}</p>
                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2">
                                        <span class="text-xs text-gray-400">
                                            <i class="fas fa-barcode mr-1"></i> {{ $loan->item?->barcode }}
                                        </span>
                                        <span class="text-xs text-gray-400">
                                            <i class="fas fa-calendar-plus mr-1"></i> {{ $loan->loan_date?->format('d M Y') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    @if($loan->due_date < now())
                                        <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-medium rounded-lg">
                                            <i class="fas fa-exclamation-circle mr-1"></i> Terlambat
                                        </span>
                                        <p class="text-xs text-red-600 mt-1">{{ $loan->due_date->diffForHumans() }}</p>
                                    @else
                                        <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-xs font-medium rounded-lg">
                                            <i class="fas fa-clock mr-1"></i> {{ $loan->due_date->diffInDays(now()) }} hari lagi
                                        </span>
                                        <p class="text-xs text-gray-500 mt-1">{{ $loan->due_date?->format('d M Y') }}</p>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-10">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-book-open text-2xl text-gray-300"></i>
                            </div>
                            <p class="text-gray-500 text-sm">Tidak ada peminjaman aktif</p>
                            <a href="{{ route('opac.catalog') }}" class="inline-flex items-center gap-2 mt-3 px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">
                                <i class="fas fa-search"></i> Cari Buku
                            </a>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Loan History -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-5 border-b border-gray-100">
                        <h2 class="font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-history text-emerald-500"></i> Riwayat Peminjaman
                        </h2>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($history as $loan)
                        <div class="p-4 flex items-center gap-3 hover:bg-gray-50 transition">
                            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-check text-emerald-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900 line-clamp-1">{{ $loan->item?->book?->title ?? '-' }}</p>
                                <p class="text-xs text-gray-400">Dikembalikan {{ $loan->return_date?->format('d M Y') }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="p-8 text-center">
                            <p class="text-gray-500 text-sm">Belum ada riwayat peminjaman</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Member Info -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-5 border-b border-gray-100">
                        <h2 class="font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-id-card text-primary-500"></i> Informasi Anggota
                        </h2>
                    </div>
                    <div class="p-5 space-y-3">
                        <div class="flex items-center justify-between py-2 border-b border-gray-50">
                            <span class="text-sm text-gray-500">Email</span>
                            <span class="text-sm text-gray-900">{{ $member->email ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-50">
                            <span class="text-sm text-gray-500">No. HP</span>
                            <span class="text-sm text-gray-900">{{ $member->phone ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-50">
                            <span class="text-sm text-gray-500">Tipe</span>
                            <span class="text-sm text-gray-900">{{ $member->memberType?->name ?? 'Umum' }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm text-gray-500">Terdaftar</span>
                            <span class="text-sm text-gray-900">{{ $member->register_date?->format('d M Y') ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Fines -->
                @if($fines->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-orange-200 overflow-hidden">
                    <div class="p-5 border-b border-orange-100 bg-orange-50">
                        <h2 class="font-bold text-orange-800 flex items-center gap-2">
                            <i class="fas fa-exclamation-circle"></i> Denda Belum Dibayar
                        </h2>
                    </div>
                    <div class="p-5">
                        <div class="space-y-3">
                            @foreach($fines as $fine)
                            <div class="p-3 bg-orange-50 rounded-xl">
                                <p class="text-sm text-gray-700">{{ $fine->description ?? 'Denda keterlambatan' }}</p>
                                <p class="text-lg font-bold text-orange-600">Rp {{ number_format($fine->amount, 0, ',', '.') }}</p>
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-4 pt-4 border-t border-orange-200">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700">Total Denda</span>
                                <span class="text-xl font-bold text-orange-600">Rp {{ number_format($fines->sum('amount'), 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Quick Links -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
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

                <!-- Help -->
                <div class="bg-gradient-to-br from-primary-500 to-primary-700 rounded-2xl p-5 text-white">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-headset text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold">Butuh Bantuan?</h3>
                            <p class="text-primary-200 text-sm mt-1">Hubungi pustakawan kami</p>
                            <a href="https://wa.me/6285183053934" target="_blank" class="inline-flex items-center gap-2 mt-3 px-3 py-1.5 bg-white/20 hover:bg-white/30 rounded-lg text-sm transition">
                                <i class="fab fa-whatsapp"></i> Chat WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-opac.layout>
