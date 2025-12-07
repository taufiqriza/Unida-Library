<x-opac.layout title="Dashboard">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-lg shadow-gray-200/50 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 gradient-blue rounded-xl flex items-center justify-center">
                        <i class="fas fa-user text-2xl text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">{{ $member->name }}</h1>
                        <p class="text-sm text-gray-500">{{ $member->member_id }}</p>
                        <p class="text-xs text-gray-400">Berlaku s/d {{ $member->expire_date?->format('d M Y') ?? '-' }}</p>
                    </div>
                </div>
                <a href="{{ route('opac.logout') }}" class="px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg transition">
                    <i class="fas fa-sign-out-alt mr-1"></i> Keluar
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Active Loans -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg shadow-gray-200/50 p-6">
                    <h2 class="font-bold text-gray-900 mb-4"><i class="fas fa-book-reader text-blue-500 mr-2"></i>Peminjaman Aktif</h2>
                    @if($loans->count() > 0)
                    <div class="space-y-3">
                        @foreach($loans as $loan)
                        <div class="flex gap-3 p-3 border border-gray-100 rounded-lg">
                            <div class="w-12 h-16 bg-blue-50 rounded flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-book text-blue-400"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-medium text-gray-900 text-sm line-clamp-1">{{ $loan->item?->book?->title ?? '-' }}</h3>
                                <p class="text-xs text-gray-500">{{ $loan->item?->barcode }}</p>
                                <div class="flex items-center gap-3 mt-1 text-xs">
                                    <span class="text-gray-400">Pinjam: {{ $loan->loan_date?->format('d M Y') }}</span>
                                    <span class="{{ $loan->due_date < now() ? 'text-red-600 font-medium' : 'text-gray-400' }}">
                                        Kembali: {{ $loan->due_date?->format('d M Y') }}
                                        @if($loan->due_date < now())
                                        (Terlambat)
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-sm text-gray-500 text-center py-8">Tidak ada peminjaman aktif</p>
                    @endif
                </div>

                <!-- History -->
                <div class="bg-white rounded-xl shadow-lg shadow-gray-200/50 p-6 mt-6">
                    <h2 class="font-bold text-gray-900 mb-4"><i class="fas fa-history text-emerald-500 mr-2"></i>Riwayat Peminjaman</h2>
                    @if($history->count() > 0)
                    <div class="space-y-2">
                        @foreach($history as $loan)
                        <div class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-lg">
                            <div class="w-8 h-8 bg-emerald-50 rounded flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-check text-emerald-500 text-xs"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900 line-clamp-1">{{ $loan->item?->book?->title ?? '-' }}</p>
                            </div>
                            <span class="text-xs text-gray-400">{{ $loan->return_date?->format('d M Y') }}</span>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-sm text-gray-500 text-center py-4">Belum ada riwayat</p>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div>
                <!-- Fines -->
                <div class="bg-white rounded-xl shadow-lg shadow-gray-200/50 p-6">
                    <h2 class="font-bold text-gray-900 mb-4"><i class="fas fa-exclamation-circle text-orange-500 mr-2"></i>Denda</h2>
                    @if($fines->count() > 0)
                    <div class="space-y-2">
                        @foreach($fines as $fine)
                        <div class="p-3 bg-orange-50 rounded-lg">
                            <p class="text-sm text-gray-900">{{ $fine->description ?? 'Denda keterlambatan' }}</p>
                            <p class="text-lg font-bold text-orange-600">Rp {{ number_format($fine->amount, 0, ',', '.') }}</p>
                        </div>
                        @endforeach
                        <div class="pt-2 border-t border-gray-100">
                            <p class="text-sm text-gray-500">Total Denda</p>
                            <p class="text-xl font-bold text-orange-600">Rp {{ number_format($fines->sum('amount'), 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @else
                    <p class="text-sm text-gray-500 text-center py-4">Tidak ada denda</p>
                    @endif
                </div>

                <!-- Quick Links -->
                <div class="bg-white rounded-xl shadow-lg shadow-gray-200/50 p-6 mt-6">
                    <h2 class="font-bold text-gray-900 mb-4">Menu</h2>
                    <div class="space-y-2">
                        <a href="{{ route('opac.catalog') }}" class="flex items-center gap-3 p-3 hover:bg-blue-50 rounded-lg transition">
                            <i class="fas fa-search text-blue-500"></i>
                            <span class="text-sm text-gray-700">Cari Buku</span>
                        </a>
                        <a href="{{ route('opac.ebooks') }}" class="flex items-center gap-3 p-3 hover:bg-blue-50 rounded-lg transition">
                            <i class="fas fa-file-pdf text-orange-500"></i>
                            <span class="text-sm text-gray-700">E-Book</span>
                        </a>
                        <a href="{{ route('opac.etheses') }}" class="flex items-center gap-3 p-3 hover:bg-blue-50 rounded-lg transition">
                            <i class="fas fa-graduation-cap text-pink-500"></i>
                            <span class="text-sm text-gray-700">E-Thesis</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-opac.layout>
