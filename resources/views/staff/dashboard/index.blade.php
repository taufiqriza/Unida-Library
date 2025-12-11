@extends('staff.layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    .stat-card-premium { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
    .stat-card-premium:hover { transform: translateY(-4px) scale(1.01); box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.15); }
    .counter-value { animation: countUp 0.8s ease-out forwards; }
    @keyframes countUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .pulse-ring::before { content: ''; position: absolute; inset: -4px; border-radius: inherit; border: 2px solid currentColor; opacity: 0.3; animation: pulse-ring 2s ease-out infinite; }
    @keyframes pulse-ring { 0% { transform: scale(1); opacity: 0.3; } 100% { transform: scale(1.1); opacity: 0; } }
    .table-row-premium { transition: all 0.2s ease; }
    .table-row-premium:hover { background: linear-gradient(90deg, rgba(59, 130, 246, 0.05) 0%, transparent 100%); }
    .chart-container { position: relative; height: 220px; width: 100%; }
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
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-5">
        <div class="stat-card-premium relative bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-600 rounded-2xl p-5 text-white overflow-hidden group cursor-pointer">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-10 -mt-10 group-hover:scale-110 transition-transform"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center"><i class="fas fa-arrow-right-from-bracket"></i></div>
                    <div class="px-2 py-1 bg-white/20 rounded-full text-[10px] font-medium">Hari Ini</div>
                </div>
                <p class="text-blue-100 text-xs font-medium mb-1">Peminjaman</p>
                <p class="text-2xl lg:text-3xl font-bold counter-value">{{ $stats['loans_today'] }}</p>
            </div>
        </div>

        <div class="stat-card-premium relative bg-gradient-to-br from-emerald-500 via-emerald-600 to-teal-600 rounded-2xl p-5 text-white overflow-hidden group cursor-pointer">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-10 -mt-10 group-hover:scale-110 transition-transform"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center"><i class="fas fa-arrow-right-to-bracket"></i></div>
                    <div class="px-2 py-1 bg-white/20 rounded-full text-[10px] font-medium">Hari Ini</div>
                </div>
                <p class="text-emerald-100 text-xs font-medium mb-1">Pengembalian</p>
                <p class="text-2xl lg:text-3xl font-bold counter-value">{{ $stats['returns_today'] }}</p>
            </div>
        </div>

        <div class="stat-card-premium relative bg-white rounded-2xl p-5 border border-gray-100 shadow-sm overflow-hidden group cursor-pointer">
            <div class="absolute top-0 right-0 w-24 h-24 bg-red-50 rounded-full -mr-8 -mt-8 group-hover:scale-110 transition-transform"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-red-400 to-red-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-red-500/30 relative {{ $stats['overdue'] > 0 ? 'pulse-ring' : '' }}"><i class="fas fa-clock"></i></div>
                    @if($stats['overdue'] > 0)
                    <span class="flex items-center gap-1 px-2 py-1 bg-red-50 text-red-600 rounded-full text-[10px] font-semibold animate-pulse"><i class="fas fa-exclamation text-[8px]"></i>Perlu Tindakan</span>
                    @endif
                </div>
                <p class="text-gray-500 text-xs font-medium mb-1">Terlambat</p>
                <p class="text-2xl lg:text-3xl font-bold text-gray-900 counter-value">{{ $stats['overdue'] }}</p>
            </div>
        </div>

        <div class="stat-card-premium relative bg-white rounded-2xl p-5 border border-gray-100 shadow-sm overflow-hidden group cursor-pointer">
            <div class="absolute top-0 right-0 w-24 h-24 bg-amber-50 rounded-full -mr-8 -mt-8 group-hover:scale-110 transition-transform"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-amber-500/30"><i class="fas fa-coins"></i></div>
                </div>
                <p class="text-gray-500 text-xs font-medium mb-1">Denda Belum Bayar</p>
                <p class="text-2xl lg:text-3xl font-bold bg-gradient-to-r from-amber-600 to-orange-600 bg-clip-text text-transparent counter-value">Rp {{ number_format($stats['unpaid_fines']/1000, 0) }}K</p>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Daily Chart -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-gray-900 flex items-center gap-2"><i class="fas fa-chart-bar text-blue-500"></i>Sirkulasi 7 Hari Terakhir</h3>
                    <p class="text-xs text-gray-500">Trend peminjaman dan pengembalian</p>
                </div>
                <div class="flex items-center gap-4 text-xs">
                    <span class="flex items-center gap-1"><span class="w-3 h-3 bg-blue-500 rounded-full"></span>Pinjam</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 bg-emerald-500 rounded-full"></span>Kembali</span>
                </div>
            </div>
            <div class="p-5">
                <div class="chart-container"><canvas id="dailyChart"></canvas></div>
            </div>
        </div>

        <!-- Monthly Chart -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-900 flex items-center gap-2"><i class="fas fa-chart-line text-indigo-500"></i>Bulanan {{ date('Y') }}</h3>
                <p class="text-xs text-gray-500">Total peminjaman per bulan</p>
            </div>
            <div class="p-5">
                <div class="chart-container"><canvas id="monthlyChart"></canvas></div>
            </div>
        </div>
    </div>

    <!-- Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <!-- Recent Loans -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-gray-900 flex items-center gap-2"><i class="fas fa-clock-rotate-left text-blue-500"></i>Transaksi Terbaru</h3>
                    <p class="text-xs text-gray-500">10 transaksi terakhir</p>
                </div>
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
                                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-white text-xs font-bold">{{ strtoupper(substr($loan->member?->name ?? 'N', 0, 1)) }}</div>
                                    <div>
                                        <p class="font-semibold text-gray-900 text-sm">{{ Str::limit($loan->member?->name, 12) }}</p>
                                        <p class="text-xs text-gray-400">{{ $loan->loan_date?->format('d/m H:i') }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3"><p class="text-sm text-gray-700">{{ Str::limit($loan->item?->book?->title, 18) }}</p></td>
                            <td class="px-5 py-3">
                                @if($loan->is_returned)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 text-[10px] font-semibold bg-emerald-50 text-emerald-700 rounded-full border border-emerald-200"><i class="fas fa-check-circle"></i>Kembali</span>
                                @elseif($loan->due_date < now())
                                    <span class="inline-flex items-center gap-1 px-2 py-1 text-[10px] font-semibold bg-red-50 text-red-700 rounded-full border border-red-200"><i class="fas fa-exclamation-circle"></i>Terlambat</span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-1 text-[10px] font-semibold bg-blue-50 text-blue-700 rounded-full border border-blue-200"><i class="fas fa-book-open"></i>Dipinjam</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center py-12 text-gray-400">Belum ada transaksi</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Overdue -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-gray-900 flex items-center gap-2"><i class="fas fa-exclamation-triangle text-red-500"></i>Peminjaman Terlambat</h3>
                </div>
                @if($stats['overdue'] > 0)<span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">{{ $stats['overdue'] }}</span>@endif
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
                                    <div class="w-8 h-8 bg-gradient-to-br from-red-500 to-orange-600 rounded-lg flex items-center justify-center text-white text-xs font-bold">{{ strtoupper(substr($loan->member?->name ?? 'N', 0, 1)) }}</div>
                                    <p class="font-semibold text-gray-900 text-sm">{{ Str::limit($loan->member?->name, 12) }}</p>
                                </div>
                            </td>
                            <td class="px-5 py-3"><p class="text-sm text-gray-700">{{ Str::limit($loan->item?->book?->title, 18) }}</p></td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold bg-gradient-to-r from-red-500 to-orange-500 text-white rounded-full"><i class="fas fa-fire text-[10px]"></i>{{ $loan->due_date?->diffInDays(now()) }} hari</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center py-12"><div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mx-auto mb-2"><i class="fas fa-check-circle text-emerald-500 text-xl"></i></div><p class="text-emerald-600 font-medium">Tidak ada yang terlambat!</p></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Collection Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['icon' => 'fa-book', 'value' => $stats['total_books'], 'label' => 'Total Judul'],
            ['icon' => 'fa-layer-group', 'value' => $stats['total_items'], 'label' => 'Total Eksemplar'],
            ['icon' => 'fa-users', 'value' => $stats['total_members'], 'label' => 'Total Anggota'],
            ['icon' => 'fa-book-open-reader', 'value' => $stats['active_loans'], 'label' => 'Sedang Dipinjam'],
        ] as $stat)
        <div class="stat-card-premium bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-slate-100 to-slate-200 rounded-xl flex items-center justify-center"><i class="fas {{ $stat['icon'] }} text-slate-600 text-lg"></i></div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 counter-value">{{ number_format($stat['value']) }}</p>
                    <p class="text-xs text-gray-500 font-medium">{{ $stat['label'] }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const chartData = @json($chartData);

// Daily Chart
new Chart(document.getElementById('dailyChart').getContext('2d'), {
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

// Monthly Chart
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
const gradient = monthlyCtx.createLinearGradient(0, 0, 0, 220);
gradient.addColorStop(0, 'rgba(99, 102, 241, 0.8)');
gradient.addColorStop(1, 'rgba(99, 102, 241, 0.2)');

new Chart(monthlyCtx, {
    type: 'line',
    data: {
        labels: chartData.monthly.labels,
        datasets: [{
            label: 'Peminjaman',
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
</script>
@endpush
