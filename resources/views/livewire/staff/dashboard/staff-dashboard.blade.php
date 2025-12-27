@section('title', 'Dashboard')

<div wire:init="loadData">
    {{-- Desktop Header (hidden on mobile) --}}
    <div class="hidden lg:flex lg:items-center lg:justify-between gap-4 mb-5">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-500/30">
                <i class="fas fa-chart-line text-2xl"></i>
            </div>
            <div>
                <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Dashboard</h1>
                <p class="text-sm text-gray-500 flex items-center gap-2">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                    {{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            {{-- Google Account Notice --}}
            @if(!auth()->user()->socialAccounts()->where('provider', 'google')->exists())
            <div x-data="{ show: !sessionStorage.getItem('hideGoogleNotice') }" x-show="show" x-cloak
                 class="flex items-center gap-2 px-3 py-2 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200/60 rounded-xl shadow-sm">
                <div class="w-6 h-6 bg-white rounded-md shadow-sm flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span class="text-[10px] text-gray-400 leading-tight">Login lebih mudah</span>
                    <a href="{{ route('staff.profile') }}" class="text-xs text-blue-700 hover:text-blue-800 font-semibold leading-tight">Hubungkan Google →</a>
                </div>
                <button @click="show = false; sessionStorage.setItem('hideGoogleNotice', '1')" class="w-5 h-5 flex items-center justify-center text-gray-400 hover:text-gray-600 rounded-full transition">
                    <i class="fas fa-times text-[10px]"></i>
                </button>
            </div>
            @endif
            <button wire:click="loadData" class="px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 transition flex items-center gap-2">
                <i class="fas fa-sync-alt text-xs" wire:loading.class="fa-spin" wire:target="loadData"></i>
                Refresh
            </button>
        </div>
    </div>

    <div class="space-y-5">
        {{-- Mobile Header - App Style (hidden on desktop) --}}
        <div class="lg:hidden -mx-4 -mt-4 px-4 pt-4 pb-6 bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 rounded-b-3xl shadow-lg">
            {{-- Greeting --}}
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-blue-200 text-sm">Selamat {{ now()->hour < 12 ? 'Pagi' : (now()->hour < 15 ? 'Siang' : (now()->hour < 18 ? 'Sore' : 'Malam')) }} 👋</p>
                    <h1 class="text-white text-xl font-bold">{{ explode(' ', auth()->user()->name)[0] }}</h1>
                </div>
                <button wire:click="loadData" class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-sync-alt" wire:loading.class="fa-spin" wire:target="loadData"></i>
                </button>
            </div>
            
        {{-- Today's Stats Row --}}
        <div class="flex gap-3">
            <div class="flex-1 bg-white/20 backdrop-blur rounded-2xl p-3 text-white">
                <div class="flex items-center gap-2 mb-1">
                    <i class="fas fa-arrow-right-from-bracket text-sm opacity-80"></i>
                    <span class="text-xs opacity-80">Pinjam</span>
                </div>
                <p class="text-2xl font-bold">{{ $stats['loans_today'] ?? 0 }}</p>
            </div>
            <div class="flex-1 bg-white/20 backdrop-blur rounded-2xl p-3 text-white">
                <div class="flex items-center gap-2 mb-1">
                    <i class="fas fa-arrow-right-to-bracket text-sm opacity-80"></i>
                    <span class="text-xs opacity-80">Kembali</span>
                </div>
                <p class="text-2xl font-bold">{{ $stats['returns_today'] ?? 0 }}</p>
            </div>
            <div class="flex-1 bg-white/20 backdrop-blur rounded-2xl p-3 text-white {{ ($stats['overdue'] ?? 0) > 0 ? 'ring-2 ring-red-400' : '' }}">
                <div class="flex items-center gap-2 mb-1">
                    <i class="fas fa-clock text-sm {{ ($stats['overdue'] ?? 0) > 0 ? 'text-red-300' : 'opacity-80' }}"></i>
                    <span class="text-xs opacity-80">Terlambat</span>
                </div>
                <p class="text-2xl font-bold {{ ($stats['overdue'] ?? 0) > 0 ? 'text-red-200' : '' }}">{{ $stats['overdue'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    {{-- Desktop Quick Stats - Today (hidden on mobile) --}}
    <div class="hidden lg:grid lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-5 text-white">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-arrow-right-from-bracket"></i>
                </div>
                <span class="px-2 py-1 bg-white/20 rounded-full text-[10px] font-medium">Hari Ini</span>
            </div>
            <p class="text-blue-100 text-xs mb-1">Peminjaman</p>
            <p class="text-3xl font-bold">{{ $stats['loans_today'] ?? 0 }}</p>
        </div>

        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-5 text-white">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-arrow-right-to-bracket"></i>
                </div>
                <span class="px-2 py-1 bg-white/20 rounded-full text-[10px] font-medium">Hari Ini</span>
            </div>
            <p class="text-emerald-100 text-xs mb-1">Pengembalian</p>
            <p class="text-3xl font-bold">{{ $stats['returns_today'] ?? 0 }}</p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-gradient-to-br from-red-400 to-red-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-red-500/30">
                    <i class="fas fa-clock"></i>
                </div>
                @if(($stats['overdue'] ?? 0) > 0)
                <span class="px-2 py-1 bg-red-100 text-red-600 rounded-full text-[10px] font-semibold animate-pulse">Perlu Tindakan</span>
                @endif
            </div>
            <p class="text-gray-500 text-xs mb-1">Terlambat</p>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['overdue'] ?? 0 }}</p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-amber-500/30">
                    <i class="fas fa-coins"></i>
                </div>
            </div>
            <p class="text-gray-500 text-xs mb-1">Denda Belum Bayar</p>
            <p class="text-3xl font-bold text-amber-600">Rp {{ number_format(($stats['unpaid_fines'] ?? 0)/1000, 0) }}K</p>
        </div>
    </div>

    {{-- Visitor Quick Action --}}
    @php
        $user = auth()->user();
        $branchId = $user->branch_id;
        $isSuper = $user->role === 'super_admin';
        $visitorBranch = $branchId ? \App\Models\Branch::find($branchId) : null;
    @endphp
    @if($visitorBranch)
    <a href="{{ route('visitor.kiosk', $visitorBranch->code) }}" target="_blank"
       class="flex items-center justify-between w-full px-4 py-3 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 rounded-xl shadow transition active:scale-[0.98]">
        <div class="flex items-center gap-3">
            <i class="fas fa-door-open text-white"></i>
            <span class="text-white font-semibold">Visitor</span>
        </div>
        <i class="fas fa-external-link-alt text-white/70 text-sm"></i>
    </a>
    @elseif($isSuper)
    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" class="flex items-center justify-between w-full px-4 py-3 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 rounded-xl shadow transition active:scale-[0.98]">
            <div class="flex items-center gap-3">
                <i class="fas fa-door-open text-white"></i>
                <span class="text-white font-semibold">Visitor</span>
            </div>
            <i class="fas fa-chevron-down text-white/70 text-sm transition-transform" :class="open && 'rotate-180'"></i>
        </button>
        <div x-show="open" @click.away="open = false" x-transition
             class="absolute z-50 mt-2 w-full bg-white rounded-xl shadow-xl border border-gray-100 py-2 max-h-64 overflow-y-auto">
            @foreach(\App\Models\Branch::where('is_active', true)->orderBy('name')->get() as $branch)
            <a href="{{ route('visitor.kiosk', $branch->code) }}" target="_blank"
               class="flex items-center justify-between px-4 py-2.5 hover:bg-amber-50 transition">
                <span class="text-gray-700">{{ $branch->name }}</span>
                <i class="fas fa-external-link-alt text-gray-400 text-xs"></i>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Mobile Quick Actions Grid --}}
    <div class="lg:hidden">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-bold text-gray-900">Aksi Cepat</h3>
        </div>
        <div class="grid grid-cols-4 gap-2">
            <a href="{{ route('staff.circulation.index') }}" class="flex flex-col items-center gap-2 p-3 bg-white rounded-2xl shadow-sm border border-gray-100 active:scale-95 transition">
                <div class="w-11 h-11 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-qrcode"></i>
                </div>
                <span class="text-[11px] font-medium text-gray-700 text-center">Scan</span>
            </a>
            <a href="{{ route('staff.attendance.index') }}" class="flex flex-col items-center gap-2 p-3 bg-white rounded-2xl shadow-sm border border-gray-100 active:scale-95 transition">
                <div class="w-11 h-11 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-fingerprint"></i>
                </div>
                <span class="text-[11px] font-medium text-gray-700 text-center">Absen</span>
            </a>
            <a href="{{ route('staff.member.index') }}" class="flex flex-col items-center gap-2 p-3 bg-white rounded-2xl shadow-sm border border-gray-100 active:scale-95 transition">
                <div class="w-11 h-11 bg-gradient-to-br from-violet-500 to-purple-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-users"></i>
                </div>
                <span class="text-[11px] font-medium text-gray-700 text-center">Anggota</span>
            </a>
            <a href="{{ route('staff.biblio.index') }}" class="flex flex-col items-center gap-2 p-3 bg-white rounded-2xl shadow-sm border border-gray-100 active:scale-95 transition">
                <div class="w-11 h-11 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-book"></i>
                </div>
                <span class="text-[11px] font-medium text-gray-700 text-center">Katalog</span>
            </a>
        </div>
    </div>

    {{-- Mobile Stats Cards --}}
    <div class="lg:hidden">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-bold text-gray-900">Koleksi</h3>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-violet-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-book text-violet-600"></i>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_books'] ?? 0) }}</p>
                        <p class="text-[10px] text-gray-500">Judul Buku</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-layer-group text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_items'] ?? 0) }}</p>
                        <p class="text-[10px] text-gray-500">Eksemplar</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-users text-emerald-600"></i>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_members'] ?? 0) }}</p>
                        <p class="text-[10px] text-gray-500">Anggota</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-book-open-reader text-amber-600"></i>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['active_loans'] ?? 0) }}</p>
                        <p class="text-[10px] text-gray-500">Dipinjam</p>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Unpaid Fines Alert (Mobile) --}}
        @if(($stats['unpaid_fines'] ?? 0) > 0)
        <div class="mt-3 bg-gradient-to-r from-amber-500 to-orange-500 rounded-2xl p-4 text-white flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-coins"></i>
                </div>
                <div>
                    <p class="text-xs opacity-90">Denda Belum Dibayar</p>
                    <p class="text-xl font-bold">Rp {{ number_format($stats['unpaid_fines'] ?? 0) }}</p>
                </div>
            </div>
            <i class="fas fa-chevron-right opacity-50"></i>
        </div>
        @endif
    </div>

    {{-- Desktop Collection Stats (hidden on mobile) --}}
    <div class="hidden lg:grid lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-violet-100 to-purple-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-book text-violet-600 text-lg"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_books'] ?? 0) }}</p>
                <p class="text-xs text-gray-500">Total Judul</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-cyan-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-layer-group text-blue-600 text-lg"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_items'] ?? 0) }}</p>
                <p class="text-xs text-gray-500">Total Eksemplar</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-emerald-100 to-green-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-users text-emerald-600 text-lg"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_members'] ?? 0) }}</p>
                <p class="text-xs text-gray-500">Total Anggota</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-amber-100 to-orange-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-book-open-reader text-amber-600 text-lg"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['active_loans'] ?? 0) }}</p>
                <p class="text-xs text-gray-500">Sedang Dipinjam</p>
            </div>
        </div>
    </div>

    {{-- Charts - Hidden on mobile, shown on desktop --}}
    <div class="hidden lg:grid lg:grid-cols-3 gap-5" wire:ignore>
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-gray-900 flex items-center gap-2"><i class="fas fa-chart-bar text-blue-500"></i>Sirkulasi 7 Hari</h3>
                    <p class="text-xs text-gray-500">Trend peminjaman & pengembalian</p>
                </div>
                <div class="flex items-center gap-3 text-xs">
                    <span class="flex items-center gap-1"><span class="w-3 h-3 bg-blue-500 rounded"></span>Pinjam</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 bg-emerald-500 rounded"></span>Kembali</span>
                </div>
            </div>
            <div class="p-5">
                <div style="height: 220px;"><canvas id="dailyChart"></canvas></div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-900 flex items-center gap-2"><i class="fas fa-chart-line text-indigo-500"></i>Bulanan {{ date('Y') }}</h3>
                <p class="text-xs text-gray-500">Total peminjaman per bulan</p>
            </div>
            <div class="p-5">
                <div style="height: 220px;"><canvas id="monthlyChart"></canvas></div>
            </div>
        </div>
    </div>

    {{-- Mobile Activity Feed --}}
    <div class="lg:hidden">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-bold text-gray-900">Aktivitas Terbaru</h3>
            <a href="{{ route('staff.circulation.index') }}" class="text-blue-600 text-sm font-medium">Lihat Semua</a>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            @forelse($recentLoans->take(5) as $loan)
            <div class="px-4 py-3 flex items-center gap-3 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                <div class="w-10 h-10 bg-gradient-to-br {{ $loan->is_returned ? 'from-emerald-500 to-teal-600' : ($loan->due_date < now() && !$loan->is_returned ? 'from-red-500 to-orange-500' : 'from-blue-500 to-indigo-600') }} rounded-xl flex items-center justify-center text-white text-sm font-bold">
                    <i class="fas {{ $loan->is_returned ? 'fa-arrow-right-to-bracket' : ($loan->due_date < now() ? 'fa-exclamation' : 'fa-arrow-right-from-bracket') }}"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900 text-sm truncate">{{ $loan->member?->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ Str::limit($loan->item?->book?->title, 30) }}</p>
                </div>
                <div class="text-right flex-shrink-0">
                    @if($loan->is_returned)
                    <span class="px-2 py-1 text-[10px] font-semibold bg-emerald-100 text-emerald-700 rounded-lg">Kembali</span>
                    @elseif($loan->due_date < now())
                    <span class="px-2 py-1 text-[10px] font-semibold bg-red-100 text-red-700 rounded-lg">Terlambat</span>
                    @else
                    <span class="px-2 py-1 text-[10px] font-semibold bg-blue-100 text-blue-700 rounded-lg">Dipinjam</span>
                    @endif
                    <p class="text-[10px] text-gray-400 mt-1">{{ $loan->loan_date?->diffForHumans(short: true) }}</p>
                </div>
            </div>
            @empty
            <div class="p-6 text-center text-gray-400">
                <i class="fas fa-inbox text-2xl mb-2"></i>
                <p class="text-sm">Belum ada transaksi</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Desktop Tables (hidden on mobile) --}}
    <div class="hidden lg:grid lg:grid-cols-2 gap-5">
        {{-- Recent Loans --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-900 flex items-center gap-2"><i class="fas fa-clock-rotate-left text-blue-500"></i>Transaksi Terbaru</h3>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentLoans as $loan)
                <div class="px-5 py-3 flex items-center gap-3 hover:bg-gray-50 transition">
                    <div class="w-9 h-9 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-white text-xs font-bold">
                        {{ strtoupper(substr($loan->member?->name ?? 'N', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 text-sm truncate">{{ $loan->member?->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ $loan->item?->book?->title }}</p>
                    </div>
                    <div class="text-right">
                        @if($loan->is_returned)
                        <span class="px-2 py-1 text-[10px] font-semibold bg-emerald-100 text-emerald-700 rounded-full">Kembali</span>
                        @elseif($loan->due_date < now())
                        <span class="px-2 py-1 text-[10px] font-semibold bg-red-100 text-red-700 rounded-full">Terlambat</span>
                        @else
                        <span class="px-2 py-1 text-[10px] font-semibold bg-blue-100 text-blue-700 rounded-full">Dipinjam</span>
                        @endif
                        <p class="text-[10px] text-gray-400 mt-1">{{ $loan->loan_date?->format('d/m H:i') }}</p>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-gray-400">
                    <i class="fas fa-inbox text-3xl mb-2"></i>
                    <p class="text-sm">Belum ada transaksi</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Overdue --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-900 flex items-center gap-2"><i class="fas fa-exclamation-triangle text-red-500"></i>Terlambat</h3>
                @if(($stats['overdue'] ?? 0) > 0)
                <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">{{ $stats['overdue'] }}</span>
                @endif
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($overdueLoans as $loan)
                <div class="px-5 py-3 flex items-center gap-3 hover:bg-red-50/50 transition">
                    <div class="w-9 h-9 bg-gradient-to-br from-red-500 to-orange-600 rounded-lg flex items-center justify-center text-white text-xs font-bold">
                        {{ strtoupper(substr($loan->member?->name ?? 'N', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 text-sm truncate">{{ $loan->member?->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ $loan->item?->book?->title }}</p>
                    </div>
                    <span class="px-3 py-1.5 text-xs font-bold bg-gradient-to-r from-red-500 to-orange-500 text-white rounded-full">
                        {{ $loan->due_date?->diffInDays(now()) }} hari
                    </span>
                </div>
                @empty
                <div class="p-8 text-center">
                    <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-check-circle text-emerald-500 text-xl"></i>
                    </div>
                    <p class="text-emerald-600 font-medium text-sm">Tidak ada yang terlambat!</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const chartData = @json($chartData);

function initCharts() {
    Chart.helpers.each(Chart.instances, (instance) => instance.destroy());
    
    const dailyEl = document.getElementById('dailyChart');
    const monthlyEl = document.getElementById('monthlyChart');
    
    if (dailyEl) {
        new Chart(dailyEl.getContext('2d'), {
            type: 'bar',
            data: {
                labels: chartData.daily.labels,
                datasets: [
                    { label: 'Pinjam', data: chartData.daily.loans, backgroundColor: 'rgba(59, 130, 246, 0.8)', borderRadius: 6 },
                    { label: 'Kembali', data: chartData.daily.returns, backgroundColor: 'rgba(16, 185, 129, 0.8)', borderRadius: 6 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: 'rgba(0,0,0,0.05)' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }
    
    if (monthlyEl) {
        const ctx = monthlyEl.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 220);
        gradient.addColorStop(0, 'rgba(99, 102, 241, 0.8)');
        gradient.addColorStop(1, 'rgba(99, 102, 241, 0.1)');
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.monthly.labels,
                datasets: [{
                    data: chartData.monthly.totals,
                    borderColor: 'rgb(99, 102, 241)',
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgb(99, 102, 241)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                    x: { grid: { display: false }, ticks: { font: { size: 10 } } }
                }
            }
        });
    }
}

initCharts();
document.addEventListener('livewire:navigated', initCharts);
</script>
@endpush
