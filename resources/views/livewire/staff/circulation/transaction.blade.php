@push('styles')
<style>
    .stat-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 12px 24px -8px rgba(0, 0, 0, 0.12); }
    .scan-input { transition: all 0.2s ease; }
    .scan-input:focus { transform: scale(1.02); }
    .loan-row { transition: all 0.2s ease; }
    .loan-row:hover { background: linear-gradient(90deg, rgba(59, 130, 246, 0.05) 0%, transparent 100%); }
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
    @if(session('warning'))
        <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl flex items-center gap-3">
            <div class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center text-white">
                <i class="fas fa-exclamation"></i>
            </div>
            <p class="text-amber-700 font-medium">{{ session('warning') }}</p>
        </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-purple-500/25">
                <i class="fas fa-exchange-alt text-xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Sirkulasi</h1>
                <p class="text-sm text-gray-500">Peminjaman & Pengembalian Buku</p>
            </div>
        </div>

        @if($activeMember)
            <button wire:click="endTransaction" 
                    class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition text-sm">
                <i class="fas fa-stop mr-2"></i>Selesai Transaksi
            </button>
        @endif
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-3 gap-3">
        <div class="stat-card bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl p-4 text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-arrow-right-from-bracket"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold">{{ $todayLoans }}</p>
                    <p class="text-xs text-blue-100">Pinjam Hari Ini</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl p-4 text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-arrow-right-to-bracket"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold">{{ $todayReturns }}</p>
                    <p class="text-xs text-emerald-100">Kembali Hari Ini</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-red-400 to-rose-500 rounded-lg flex items-center justify-center text-white">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $overdueCount }}</p>
                    <p class="text-xs text-gray-500">Terlambat</p>
                </div>
            </div>
        </div>
    </div>

    @if($activeMember)
        {{-- Active Transaction --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            {{-- Member Info --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-gray-900">Anggota Aktif</h3>
                        @if($activeMember->isExpired())
                            <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full">Kadaluarsa</span>
                        @else
                            <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-full">Aktif</span>
                        @endif
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex gap-4">
                        {{-- Photo --}}
                        <div class="flex-shrink-0">
                            @if($activeMember->photo)
                                <img src="{{ asset('storage/' . $activeMember->photo) }}" class="w-20 h-24 object-cover rounded-lg border">
                            @else
                                <div class="w-20 h-24 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-white text-3xl font-bold">
                                    {{ strtoupper(substr($activeMember->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-gray-900 truncate">{{ $activeMember->name }}</h4>
                            <p class="text-sm text-gray-500 font-mono">{{ $activeMember->member_id }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $activeMember->memberType->name ?? 'Member' }}</p>
                            
                            <div class="flex gap-2 mt-3">
                                <div class="flex-1 text-center py-2 bg-blue-50 rounded-lg">
                                    <p class="text-lg font-bold text-blue-600">{{ count($activeLoans) }}</p>
                                    <p class="text-[10px] text-blue-500">Dipinjam</p>
                                </div>
                                <div class="flex-1 text-center py-2 bg-emerald-50 rounded-lg">
                                    <p class="text-lg font-bold text-emerald-600">{{ max(0, ($activeMember->memberType->loan_limit ?? 3) - count($activeLoans)) }}</p>
                                    <p class="text-[10px] text-emerald-500">Sisa Kuota</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($activeMember->isExpired())
                        <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-xs text-red-600"><i class="fas fa-exclamation-triangle mr-1"></i>Keanggotaan kadaluarsa. Peminjaman tidak diizinkan.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Loan Input + Loans List --}}
            <div class="lg:col-span-2 space-y-4">
                {{-- Loan Input --}}
                @unless($activeMember->isExpired())
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                    <h3 class="font-bold text-gray-900 mb-3">
                        <i class="fas fa-barcode text-blue-500 mr-2"></i>Pinjam Buku
                    </h3>
                    <div class="flex gap-3">
                        <input wire:model="itemBarcode" 
                               wire:keydown.enter="loanItem"
                               type="text" 
                               placeholder="Scan barcode item..."
                               class="scan-input flex-1 px-4 py-3 bg-gray-50 border-2 border-gray-200 focus:border-blue-500 focus:bg-white rounded-xl text-lg font-mono"
                               autofocus>
                        <button wire:click="loanItem" 
                                class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-xl transition">
                            <i class="fas fa-plus mr-2"></i>Pinjam
                        </button>
                    </div>
                </div>
                @endunless

                {{-- Active Loans Table --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-gray-100">
                        <h3 class="font-bold text-gray-900">
                            <i class="fas fa-list text-indigo-500 mr-2"></i>Daftar Pinjaman ({{ count($activeLoans) }})
                        </h3>
                    </div>

                    @if(count($activeLoans) > 0)
                        <div class="divide-y divide-gray-100">
                            @foreach($activeLoans as $loan)
                                @php
                                    $isOverdue = $loan->due_date < now();
                                    $daysLeft = (int) now()->diffInDays($loan->due_date, false);
                                @endphp
                                <div class="loan-row p-4 {{ $isOverdue ? 'bg-red-50' : '' }}">
                                    <div class="flex items-center gap-4">
                                        {{-- Book Info --}}
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-gray-900 truncate">{{ Str::limit($loan->item->book->title ?? '-', 40) }}</p>
                                            <div class="flex items-center gap-3 mt-1 text-xs text-gray-500">
                                                <span class="font-mono bg-gray-100 px-2 py-0.5 rounded">{{ $loan->item->barcode }}</span>
                                                <span><i class="fas fa-calendar mr-1"></i>{{ $loan->loan_date->format('d/m/Y') }}</span>
                                                <span><i class="fas fa-clock mr-1"></i>{{ $loan->due_date->format('d/m/Y') }}</span>
                                            </div>
                                        </div>

                                        {{-- Status --}}
                                        <div>
                                            @if($isOverdue)
                                                <span class="px-3 py-1.5 bg-gradient-to-r from-red-500 to-rose-500 text-white text-xs font-bold rounded-full">
                                                    {{ abs($daysLeft) }} hari terlambat
                                                </span>
                                            @else
                                                <span class="px-3 py-1.5 bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-full">
                                                    {{ $daysLeft }} hari lagi
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Actions --}}
                                        <div class="flex items-center gap-2">
                                            <button wire:click="returnItem({{ $loan->id }})" 
                                                    class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition">
                                                Kembali
                                            </button>
                                            @if(!$isOverdue && $loan->extend_count < 2)
                                                <button wire:click="extendLoan({{ $loan->id }})" 
                                                        class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition"
                                                        title="Perpanjang">
                                                    +7
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-8 text-center">
                            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-inbox text-gray-400 text-xl"></i>
                            </div>
                            <p class="text-gray-500">Tidak ada pinjaman aktif</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @else
        {{-- Start Transaction --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50">
                <h2 class="text-lg font-bold text-gray-900">Mulai Transaksi</h2>
                <p class="text-sm text-gray-500">Scan kartu anggota atau masukkan nomor anggota untuk memulai</p>
            </div>
            <div class="p-6">
                <div class="max-w-md">
                    <label class="block text-sm font-medium text-gray-700 mb-2">No. Anggota</label>
                    <div class="flex gap-3">
                        <input wire:model="memberBarcode" 
                               wire:keydown.enter="startTransaction"
                               type="text" 
                               placeholder="Scan atau ketik nomor anggota..."
                               class="scan-input flex-1 px-4 py-3 bg-gray-50 border-2 border-gray-200 focus:border-indigo-500 focus:bg-white rounded-xl text-lg"
                               autofocus>
                        <button wire:click="startTransaction" 
                                class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl transition">
                            <i class="fas fa-play mr-2"></i>Mulai
                        </button>
                    </div>
                </div>

                {{-- Quick Info --}}
                <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="p-4 bg-blue-50 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center text-white">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-blue-900">Scan Kartu</p>
                                <p class="text-xs text-blue-600">Untuk memulai transaksi</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 bg-emerald-50 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-emerald-500 rounded-lg flex items-center justify-center text-white">
                                <i class="fas fa-barcode"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-emerald-900">Scan Buku</p>
                                <p class="text-xs text-emerald-600">Untuk pinjam/kembali</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 bg-purple-50 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center text-white">
                                <i class="fas fa-clock-rotate-left"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-purple-900">Perpanjang</p>
                                <p class="text-xs text-purple-600">Maksimal 2x perpanjangan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
