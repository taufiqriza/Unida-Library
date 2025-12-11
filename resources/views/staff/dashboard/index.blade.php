@extends('staff.layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    /* Premium Card Animations */
    .stat-card-premium {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .stat-card-premium:hover {
        transform: translateY(-4px) scale(1.01);
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.15);
    }
    
    /* Animated Counter */
    .counter-value {
        animation: countUp 0.8s ease-out forwards;
    }
    @keyframes countUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Progress Bar Animation */
    .progress-animated {
        animation: progressFill 1s ease-out forwards;
    }
    @keyframes progressFill {
        from { width: 0; }
    }
    
    /* Pulse Ring for alerts */
    .pulse-ring::before {
        content: '';
        position: absolute;
        inset: -4px;
        border-radius: inherit;
        border: 2px solid currentColor;
        opacity: 0.3;
        animation: pulse-ring 2s ease-out infinite;
    }
    @keyframes pulse-ring {
        0% { transform: scale(1); opacity: 0.3; }
        100% { transform: scale(1.1); opacity: 0; }
    }
    
    /* Float Animation */
    .float-icon {
        animation: float 3s ease-in-out infinite;
    }
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }
    
    /* Quick Action Hover */
    .quick-action-premium {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .quick-action-premium:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px -8px rgba(0, 0, 0, 0.15);
    }
    .quick-action-premium:hover .action-icon {
        transform: scale(1.1);
    }
    
    /* Table Row Hover */
    .table-row-premium {
        transition: all 0.2s ease;
    }
    .table-row-premium:hover {
        background: linear-gradient(90deg, rgba(59, 130, 246, 0.05) 0%, transparent 100%);
    }
</style>
@endpush

@section('content')
<div class="space-y-6">

    <!-- Premium Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="relative">
                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 via-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-blue-500/30">
                    <i class="fas fa-chart-line text-2xl"></i>
                </div>
                <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-emerald-400 rounded-full flex items-center justify-center border-2 border-white">
                    <i class="fas fa-check text-white text-[8px]"></i>
                </div>
            </div>
            <div>
                <h1 class="text-xl lg:text-2xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 bg-clip-text text-transparent">
                    Dashboard Perpustakaan
                </h1>
                <p class="text-sm text-gray-500 flex items-center gap-2">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                    Statistik Hari Ini • {{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('filament.admin.pages.circulation') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all shadow-lg shadow-blue-500/25">
                <i class="fas fa-qrcode"></i>
                <span>Mulai Sirkulasi</span>
            </a>
        </div>
    </div>

    <!-- Stats Cards - Premium Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-5">
        <!-- Pinjam Hari Ini -->
        <div class="stat-card-premium relative bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-600 rounded-2xl p-5 text-white overflow-hidden group cursor-pointer">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-10 -mt-10 group-hover:scale-110 transition-transform"></div>
            <div class="absolute bottom-0 left-0 w-20 h-20 bg-white/5 rounded-full -ml-8 -mb-8"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                        <i class="fas fa-arrow-right-from-bracket"></i>
                    </div>
                    <div class="px-2 py-1 bg-white/20 rounded-full text-[10px] font-medium">
                        Hari Ini
                    </div>
                </div>
                <p class="text-blue-100 text-xs font-medium mb-1">Peminjaman</p>
                <p class="text-2xl lg:text-3xl font-bold counter-value">{{ $stats['loans_today'] }}</p>
            </div>
        </div>

        <!-- Kembali Hari Ini -->
        <div class="stat-card-premium relative bg-gradient-to-br from-emerald-500 via-emerald-600 to-teal-600 rounded-2xl p-5 text-white overflow-hidden group cursor-pointer">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-10 -mt-10 group-hover:scale-110 transition-transform"></div>
            <div class="absolute bottom-0 left-0 w-20 h-20 bg-white/5 rounded-full -ml-8 -mb-8"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                        <i class="fas fa-arrow-right-to-bracket"></i>
                    </div>
                    <div class="px-2 py-1 bg-white/20 rounded-full text-[10px] font-medium">
                        Hari Ini
                    </div>
                </div>
                <p class="text-emerald-100 text-xs font-medium mb-1">Pengembalian</p>
                <p class="text-2xl lg:text-3xl font-bold counter-value">{{ $stats['returns_today'] }}</p>
            </div>
        </div>

        <!-- Terlambat -->
        <div class="stat-card-premium relative bg-white rounded-2xl p-5 border border-gray-100 shadow-sm overflow-hidden group cursor-pointer">
            <div class="absolute top-0 right-0 w-24 h-24 bg-red-50 rounded-full -mr-8 -mt-8 group-hover:scale-110 transition-transform"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-red-400 to-red-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-red-500/30 relative {{ $stats['overdue'] > 0 ? 'pulse-ring' : '' }}">
                        <i class="fas fa-clock"></i>
                    </div>
                    @if($stats['overdue'] > 0)
                    <span class="flex items-center gap-1 px-2 py-1 bg-red-50 text-red-600 rounded-full text-[10px] font-semibold animate-pulse">
                        <i class="fas fa-exclamation text-[8px]"></i>
                        Perlu Tindakan
                    </span>
                    @endif
                </div>
                <p class="text-gray-500 text-xs font-medium mb-1">Terlambat</p>
                <div class="flex items-end gap-2">
                    <p class="text-2xl lg:text-3xl font-bold text-gray-900 counter-value">{{ $stats['overdue'] }}</p>
                    <span class="text-xs text-gray-400 mb-1">buku</span>
                </div>
            </div>
        </div>

        <!-- Denda Belum Bayar -->
        <div class="stat-card-premium relative bg-white rounded-2xl p-5 border border-gray-100 shadow-sm overflow-hidden group cursor-pointer">
            <div class="absolute top-0 right-0 w-24 h-24 bg-amber-50 rounded-full -mr-8 -mt-8 group-hover:scale-110 transition-transform"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-amber-500/30">
                        <i class="fas fa-coins"></i>
                    </div>
                    @if($stats['unpaid_fines'] > 0)
                    <span class="flex items-center gap-1 px-2 py-1 bg-amber-50 text-amber-600 rounded-full text-[10px] font-semibold">
                        <i class="fas fa-arrow-up text-[8px]"></i>
                        Pending
                    </span>
                    @endif
                </div>
                <p class="text-gray-500 text-xs font-medium mb-1">Denda Belum Bayar</p>
                <p class="text-2xl lg:text-3xl font-bold bg-gradient-to-r from-amber-600 to-orange-600 bg-clip-text text-transparent counter-value">
                    Rp {{ number_format($stats['unpaid_fines']/1000, 0) }}K
                </p>
            </div>
        </div>
    </div>

    <!-- Quick Actions - Premium -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2">
            <i class="fas fa-bolt text-amber-500"></i>
            <h3 class="font-bold text-gray-900">Aksi Cepat</h3>
        </div>
        <div class="p-4 grid grid-cols-3 lg:grid-cols-6 gap-3">
            <a href="#" class="quick-action-premium flex flex-col items-center gap-3 p-4 bg-gradient-to-br from-slate-50 to-gray-100 rounded-2xl border border-gray-200 hover:border-blue-300 cursor-pointer">
                <div class="action-icon w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-500/30 transition-transform">
                    <i class="fas fa-qrcode text-lg"></i>
                </div>
                <span class="text-xs font-semibold text-gray-700 text-center">Scan Pinjam</span>
            </a>
            <a href="#" class="quick-action-premium flex flex-col items-center gap-3 p-4 bg-gradient-to-br from-slate-50 to-gray-100 rounded-2xl border border-gray-200 hover:border-emerald-300 cursor-pointer">
                <div class="action-icon w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-emerald-500/30 transition-transform">
                    <i class="fas fa-rotate-left text-lg"></i>
                </div>
                <span class="text-xs font-semibold text-gray-700 text-center">Scan Kembali</span>
            </a>
            <a href="#" class="quick-action-premium flex flex-col items-center gap-3 p-4 bg-gradient-to-br from-slate-50 to-gray-100 rounded-2xl border border-gray-200 hover:border-purple-300 cursor-pointer">
                <div class="action-icon w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-purple-500/30 transition-transform">
                    <i class="fas fa-user-plus text-lg"></i>
                </div>
                <span class="text-xs font-semibold text-gray-700 text-center">Member Baru</span>
            </a>
            <a href="#" class="quick-action-premium flex flex-col items-center gap-3 p-4 bg-gradient-to-br from-slate-50 to-gray-100 rounded-2xl border border-gray-200 hover:border-amber-300 cursor-pointer">
                <div class="action-icon w-12 h-12 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-amber-500/30 transition-transform">
                    <i class="fas fa-search text-lg"></i>
                </div>
                <span class="text-xs font-semibold text-gray-700 text-center">Cari Buku</span>
            </a>
            <a href="#" class="quick-action-premium flex flex-col items-center gap-3 p-4 bg-gradient-to-br from-slate-50 to-gray-100 rounded-2xl border border-gray-200 hover:border-indigo-300 cursor-pointer">
                <div class="action-icon w-12 h-12 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-500/30 transition-transform">
                    <i class="fas fa-id-card text-lg"></i>
                </div>
                <span class="text-xs font-semibold text-gray-700 text-center">Cetak Kartu</span>
            </a>
            <a href="#" class="quick-action-premium flex flex-col items-center gap-3 p-4 bg-gradient-to-br from-slate-50 to-gray-100 rounded-2xl border border-gray-200 hover:border-rose-300 cursor-pointer">
                <div class="action-icon w-12 h-12 bg-gradient-to-br from-rose-500 to-red-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-rose-500/30 transition-transform">
                    <i class="fas fa-receipt text-lg"></i>
                </div>
                <span class="text-xs font-semibold text-gray-700 text-center">Bayar Denda</span>
            </a>
        </div>
    </div>

    <!-- Tables Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <!-- Recent Loans -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-clock-rotate-left text-blue-500"></i>
                        Transaksi Terbaru
                    </h3>
                    <p class="text-xs text-gray-500">10 transaksi terakhir</p>
                </div>
                <a href="#" class="text-xs text-blue-600 hover:text-blue-700 font-medium">Lihat Semua →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="text-left text-xs text-gray-500 uppercase tracking-wider bg-gradient-to-r from-gray-50 to-gray-100">
                            <th class="px-5 py-3 font-semibold">Member</th>
                            <th class="px-5 py-3 font-semibold">Buku</th>
                            <th class="px-5 py-3 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentLoans as $loan)
                        <tr class="table-row-premium">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-white text-xs font-bold">
                                        {{ strtoupper(substr($loan->member?->name ?? 'N', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 text-sm">{{ Str::limit($loan->member?->name, 12) }}</p>
                                        <p class="text-xs text-gray-400">{{ $loan->loan_date?->format('d/m H:i') }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <p class="text-sm text-gray-700">{{ Str::limit($loan->item?->book?->title, 18) }}</p>
                            </td>
                            <td class="px-5 py-3">
                                @if($loan->is_returned)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 text-[10px] font-semibold bg-emerald-50 text-emerald-700 rounded-full border border-emerald-200">
                                        <i class="fas fa-check-circle"></i> Kembali
                                    </span>
                                @elseif($loan->due_date < now())
                                    <span class="inline-flex items-center gap-1 px-2 py-1 text-[10px] font-semibold bg-red-50 text-red-700 rounded-full border border-red-200">
                                        <i class="fas fa-exclamation-circle"></i> Terlambat
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-1 text-[10px] font-semibold bg-blue-50 text-blue-700 rounded-full border border-blue-200">
                                        <i class="fas fa-book-open"></i> Dipinjam
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-12">
                                <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-inbox text-gray-300 text-2xl"></i>
                                </div>
                                <p class="text-gray-500 font-medium">Belum ada transaksi</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Overdue Loans -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle text-red-500"></i>
                        Peminjaman Terlambat
                    </h3>
                    <p class="text-xs text-gray-500">Perlu segera ditindaklanjuti</p>
                </div>
                @if($stats['overdue'] > 0)
                    <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">{{ $stats['overdue'] }}</span>
                @endif
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="text-left text-xs text-gray-500 uppercase tracking-wider bg-gradient-to-r from-red-50 to-orange-50">
                            <th class="px-5 py-3 font-semibold">Member</th>
                            <th class="px-5 py-3 font-semibold">Buku</th>
                            <th class="px-5 py-3 font-semibold">Terlambat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($overdueLoans as $loan)
                        <tr class="table-row-premium">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gradient-to-br from-red-500 to-orange-600 rounded-lg flex items-center justify-center text-white text-xs font-bold">
                                        {{ strtoupper(substr($loan->member?->name ?? 'N', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 text-sm">{{ Str::limit($loan->member?->name, 12) }}</p>
                                        <p class="text-xs text-gray-400">{{ $loan->member?->phone }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <p class="text-sm text-gray-700">{{ Str::limit($loan->item?->book?->title, 18) }}</p>
                                <p class="text-xs text-gray-400">Due: {{ $loan->due_date?->format('d/m') }}</p>
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold bg-gradient-to-r from-red-500 to-orange-500 text-white rounded-full shadow-sm">
                                    <i class="fas fa-fire text-[10px]"></i>
                                    {{ $loan->due_date?->diffInDays(now()) }} hari
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-12">
                                <div class="w-16 h-16 bg-emerald-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-check-circle text-emerald-500 text-2xl"></i>
                                </div>
                                <p class="text-emerald-600 font-semibold">Tidak ada yang terlambat!</p>
                                <p class="text-gray-400 text-xs mt-1">Semua peminjaman tepat waktu</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Collection Stats - Bottom -->
    <div class="grid grid-cols-3 gap-4">
        <div class="stat-card-premium bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-slate-100 to-slate-200 rounded-xl flex items-center justify-center">
                    <i class="fas fa-book text-slate-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 counter-value">{{ number_format($stats['total_books']) }}</p>
                    <p class="text-xs text-gray-500 font-medium">Total Judul Buku</p>
                </div>
            </div>
        </div>
        <div class="stat-card-premium bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-slate-100 to-slate-200 rounded-xl flex items-center justify-center">
                    <i class="fas fa-layer-group text-slate-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 counter-value">{{ number_format($stats['total_items']) }}</p>
                    <p class="text-xs text-gray-500 font-medium">Total Eksemplar</p>
                </div>
            </div>
        </div>
        <div class="stat-card-premium bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-slate-100 to-slate-200 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-slate-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 counter-value">{{ number_format($stats['total_members']) }}</p>
                    <p class="text-xs text-gray-500 font-medium">Total Anggota</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
