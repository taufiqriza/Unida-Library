@section('title', 'Statistik Perpustakaan')

<div class="space-y-6" wire:init="loadStatistics">

    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-gradient-to-br from-violet-600 to-purple-700 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-violet-500/30">
                <i class="fas fa-chart-pie text-2xl"></i>
            </div>
            <div>
                <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Statistik Perpustakaan</h1>
                <p class="text-sm text-gray-500 flex items-center gap-2">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                    @if($viewMode === 'all')
                        Seluruh Jaringan Perpustakaan
                    @else
                        {{ $branches->find($selectedBranch)?->name ?? 'Cabang' }}
                    @endif
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            {{-- Branch Selector --}}
            <div class="relative">
                <select wire:model.live="selectedBranch" 
                        class="appearance-none pl-4 pr-10 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500 cursor-pointer min-w-[200px]">
                    <option value="">🌐 Semua Cabang</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">📍 {{ $branch->name }}</option>
                    @endforeach
                </select>
                <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
            </div>
            <button wire:click="loadStatistics" class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition flex items-center gap-2">
                <i class="fas fa-sync-alt" wire:loading.class="fa-spin" wire:target="loadStatistics"></i>
                <span class="hidden sm:inline">Refresh</span>
            </button>
        </div>
    </div>

    {{-- Network Overview (All Branches) --}}
    @if($viewMode === 'all' && count($branchStats) > 1)
    <div class="bg-gradient-to-br from-violet-600 via-purple-600 to-indigo-700 rounded-2xl p-6 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
        
        <div class="relative">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-network-wired text-xl"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold">Jaringan Perpustakaan UNIDA</h2>
                    <p class="text-violet-200 text-sm">{{ count($branchStats) }} Cabang Aktif</p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($branchStats as $branch)
                <div class="bg-white/10 backdrop-blur rounded-xl p-4 hover:bg-white/15 transition cursor-pointer" wire:click="$set('selectedBranch', {{ $branch['id'] }})">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-2 py-0.5 bg-white/20 rounded text-[10px] font-bold">{{ $branch['code'] }}</span>
                    </div>
                    <p class="font-semibold text-sm truncate">{{ $branch['name'] }}</p>
                    <div class="mt-2 grid grid-cols-2 gap-2 text-xs text-violet-200">
                        <div><i class="fas fa-book mr-1"></i>{{ number_format($branch['titles']) }}</div>
                        <div><i class="fas fa-users mr-1"></i>{{ number_format($branch['members']) }}</div>
                    </div>
                    <div class="mt-1 text-xs">
                        <span class="text-emerald-300"><i class="fas fa-arrow-up mr-1"></i>{{ $branch['loans_month'] }} pinjam/bln</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Main Stats Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Collection --}}
        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-5 text-white">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-book"></i>
                </div>
                <span class="px-2 py-1 bg-white/20 rounded-full text-[10px] font-medium">Koleksi</span>
            </div>
            <p class="text-blue-100 text-xs mb-1">Total Judul</p>
            <p class="text-3xl font-bold">{{ number_format($stats['total_titles'] ?? 0) }}</p>
            <p class="text-blue-200 text-xs mt-2">
                <i class="fas fa-layer-group mr-1"></i>{{ number_format($stats['total_items'] ?? 0) }} eksemplar
            </p>
        </div>

        {{-- Members --}}
        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-5 text-white">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users"></i>
                </div>
                <span class="px-2 py-1 bg-white/20 rounded-full text-[10px] font-medium">Anggota</span>
            </div>
            <p class="text-emerald-100 text-xs mb-1">Total Anggota</p>
            <p class="text-3xl font-bold">{{ number_format($stats['total_members'] ?? 0) }}</p>
            <p class="text-emerald-200 text-xs mt-2">
                <i class="fas fa-user-plus mr-1"></i>+{{ $stats['new_members_month'] ?? 0 }} bulan ini
            </p>
        </div>

        {{-- Circulation --}}
        <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl p-5 text-white">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-arrows-rotate"></i>
                </div>
                <span class="px-2 py-1 bg-white/20 rounded-full text-[10px] font-medium">Sirkulasi</span>
            </div>
            <p class="text-amber-100 text-xs mb-1">Aktif Dipinjam</p>
            <p class="text-3xl font-bold">{{ number_format($stats['active_loans'] ?? 0) }}</p>
            <p class="text-amber-200 text-xs mt-2">
                <i class="fas fa-calendar-check mr-1"></i>{{ number_format($stats['loans_this_month'] ?? 0) }} bulan ini
            </p>
        </div>

        {{-- Digital --}}
        <div class="bg-gradient-to-br from-violet-500 to-purple-600 rounded-2xl p-5 text-white">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-cloud"></i>
                </div>
                <span class="px-2 py-1 bg-white/20 rounded-full text-[10px] font-medium">Digital</span>
            </div>
            <p class="text-violet-100 text-xs mb-1">E-Book</p>
            <p class="text-3xl font-bold">{{ number_format($stats['total_ebooks'] ?? 0) }}</p>
            <p class="text-violet-200 text-xs mt-2">
                <i class="fas fa-graduation-cap mr-1"></i>{{ number_format($stats['total_ethesis'] ?? 0) }} E-Thesis
            </p>
        </div>
    </div>

    {{-- Detailed Stats --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- Item Status --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-boxes-stacked text-blue-500"></i>
                Status Eksemplar
            </h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-emerald-50 rounded-xl">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center text-white">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Tersedia</span>
                    </div>
                    <span class="text-lg font-bold text-emerald-600">{{ number_format($stats['available_items'] ?? 0) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-amber-50 rounded-xl">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center text-white">
                            <i class="fas fa-hand-holding text-xs"></i>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Dipinjam</span>
                    </div>
                    <span class="text-lg font-bold text-amber-600">{{ number_format($stats['on_loan_items'] ?? 0) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-red-50 rounded-xl">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-red-500 rounded-lg flex items-center justify-center text-white">
                            <i class="fas fa-clock text-xs"></i>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Terlambat</span>
                    </div>
                    <span class="text-lg font-bold text-red-600">{{ number_format($stats['overdue_loans'] ?? 0) }}</span>
                </div>
            </div>
        </div>

        {{-- Circulation Summary --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-chart-line text-violet-500"></i>
                Ringkasan Sirkulasi
            </h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Hari ini</span>
                    <span class="font-bold text-gray-900">{{ number_format($stats['loans_today'] ?? 0) }} pinjam</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Bulan ini</span>
                    <span class="font-bold text-gray-900">{{ number_format($stats['loans_this_month'] ?? 0) }} pinjam</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Tahun {{ date('Y') }}</span>
                    <span class="font-bold text-gray-900">{{ number_format($stats['loans_this_year'] ?? 0) }} pinjam</span>
                </div>
                <hr class="border-gray-100">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Anggota Aktif</span>
                    <span class="font-bold text-emerald-600">{{ number_format($stats['active_members'] ?? 0) }}</span>
                </div>
            </div>
        </div>

        {{-- Fines Summary --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-coins text-amber-500"></i>
                Denda
            </h3>
            <div class="space-y-3">
                <div class="p-4 bg-gradient-to-r from-red-50 to-orange-50 rounded-xl border border-red-100">
                    <p class="text-xs text-red-600 mb-1">Belum Dibayar</p>
                    <p class="text-2xl font-bold text-red-700">Rp {{ number_format($stats['unpaid_fines'] ?? 0) }}</p>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <span class="text-sm text-gray-600">Sudah Dibayar</span>
                    <span class="font-bold text-emerald-600">Rp {{ number_format($stats['paid_fines'] ?? 0) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <span class="text-sm text-gray-600">Total Denda</span>
                    <span class="font-bold text-gray-900">Rp {{ number_format($stats['total_fines'] ?? 0) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5" wire:ignore>
        {{-- Monthly Trend --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-chart-area text-violet-500"></i>
                        Tren 12 Bulan Terakhir
                    </h3>
                    <p class="text-xs text-gray-500">Peminjaman & Pengembalian</p>
                </div>
                <div class="flex items-center gap-3 text-xs">
                    <span class="flex items-center gap-1"><span class="w-3 h-3 bg-violet-500 rounded"></span>Pinjam</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 bg-emerald-500 rounded"></span>Kembali</span>
                </div>
            </div>
            <div class="p-5">
                <div style="height: 280px;"><canvas id="trendChart"></canvas></div>
            </div>
        </div>

        {{-- Top Categories --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-chart-pie text-amber-500"></i>
                    Top 10 Klasifikasi
                </h3>
                <p class="text-xs text-gray-500">Berdasarkan jumlah judul</p>
            </div>
            <div class="p-5">
                @if(count($topCategories) > 0)
                <div class="space-y-2 max-h-[280px] overflow-y-auto">
                    @foreach($topCategories as $index => $cat)
                    @php 
                        $maxTotal = max(array_column($topCategories, 'total'));
                        $percent = $maxTotal > 0 ? ($cat['total'] / $maxTotal) * 100 : 0;
                        $colors = ['bg-violet-500', 'bg-blue-500', 'bg-emerald-500', 'bg-amber-500', 'bg-rose-500', 'bg-cyan-500', 'bg-indigo-500', 'bg-teal-500', 'bg-orange-500', 'bg-pink-500'];
                    @endphp
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 {{ $colors[$index % count($colors)] }} rounded-lg flex items-center justify-center text-white text-xs font-bold">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-gray-700 truncate">{{ $cat['classification'] ?: 'Belum Diklasifikasi' }}</span>
                                <span class="text-sm font-bold text-gray-900">{{ number_format($cat['total']) }}</span>
                            </div>
                            <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full {{ $colors[$index % count($colors)] }} rounded-full transition-all" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8 text-gray-400">
                    <i class="fas fa-chart-pie text-4xl mb-2"></i>
                    <p>Tidak ada data klasifikasi</p>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const chartData = @json($chartData);

function initStatCharts() {
    Chart.helpers.each(Chart.instances, (instance) => instance.destroy());
    
    const trendEl = document.getElementById('trendChart');
    
    if (trendEl && chartData.monthly) {
        const ctx = trendEl.getContext('2d');
        const gradientLoans = ctx.createLinearGradient(0, 0, 0, 280);
        gradientLoans.addColorStop(0, 'rgba(139, 92, 246, 0.3)');
        gradientLoans.addColorStop(1, 'rgba(139, 92, 246, 0.02)');
        
        const gradientReturns = ctx.createLinearGradient(0, 0, 0, 280);
        gradientReturns.addColorStop(0, 'rgba(16, 185, 129, 0.3)');
        gradientReturns.addColorStop(1, 'rgba(16, 185, 129, 0.02)');
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.monthly.labels,
                datasets: [
                    {
                        label: 'Peminjaman',
                        data: chartData.monthly.loans,
                        borderColor: 'rgb(139, 92, 246)',
                        backgroundColor: gradientLoans,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: 'rgb(139, 92, 246)'
                    },
                    {
                        label: 'Pengembalian',
                        data: chartData.monthly.returns,
                        borderColor: 'rgb(16, 185, 129)',
                        backgroundColor: gradientReturns,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: 'rgb(16, 185, 129)'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false }
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: { 
                        grid: { display: false },
                        ticks: { font: { size: 10 } }
                    }
                }
            }
        });
    }
}

initStatCharts();
document.addEventListener('livewire:navigated', initStatCharts);
</script>
@endpush
