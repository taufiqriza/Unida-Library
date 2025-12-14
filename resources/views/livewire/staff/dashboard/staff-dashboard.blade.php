@section('title', 'Dashboard')

<div class="space-y-6" wire:init="loadData">

    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
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
        <button wire:click="loadData" class="self-start lg:self-auto px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition flex items-center gap-2">
            <i class="fas fa-sync-alt" wire:loading.class="fa-spin" wire:target="loadData"></i>
            Refresh
        </button>
    </div>

    {{-- Quick Stats - Today --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
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

    {{-- Collection Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
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

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5" wire:ignore>
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

    {{-- Tables --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
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
