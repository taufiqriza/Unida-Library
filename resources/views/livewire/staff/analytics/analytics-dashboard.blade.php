@section('title', 'Website Analytics')

<div class="space-y-6" wire:init="loadData">

    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-orange-500/30">
                <i class="fas fa-chart-line text-2xl"></i>
            </div>
            <div>
                <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Website Analytics</h1>
                <p class="text-sm text-gray-500 flex items-center gap-2">
                    @if($isConfigured)
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                        Google Analytics Terhubung
                    @else
                        <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                        Mode Demo (Belum Dikonfigurasi)
                    @endif
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <select wire:model.live="period" class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="7d">7 Hari Terakhir</option>
                <option value="30d">30 Hari Terakhir</option>
                <option value="90d">90 Hari Terakhir</option>
            </select>
            <button wire:click="loadData" class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition flex items-center gap-2">
                <i class="fas fa-sync-alt" wire:loading.class="fa-spin" wire:target="loadData"></i>
                Refresh
            </button>
        </div>
    </div>

    @if(!$isConfigured)
    {{-- Configuration Notice --}}
    <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-2xl p-5">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-cog text-amber-600 text-xl"></i>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-amber-800 mb-1">Konfigurasi Diperlukan</h3>
                <p class="text-amber-700 text-sm mb-3">
                    Untuk menampilkan data analytics dari website, silakan konfigurasi Google Analytics di halaman pengaturan.
                    Data yang ditampilkan saat ini adalah data demo.
                </p>
                <a href="{{ route('filament.admin.pages.app-settings') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg transition">
                    <i class="fas fa-cog"></i> Buka Pengaturan
                </a>
            </div>
        </div>
    </div>
    @endif

    {{-- Quick Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-6 gap-4">
        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-5 text-white">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <p class="text-blue-100 text-xs mb-1">Total Pengguna</p>
            <p class="text-3xl font-bold">{{ number_format($stats['users'] ?? 0) }}</p>
        </div>

        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-5 text-white">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-eye"></i>
                </div>
            </div>
            <p class="text-emerald-100 text-xs mb-1">Pageviews</p>
            <p class="text-3xl font-bold">{{ number_format($stats['pageviews'] ?? 0) }}</p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-gradient-to-br from-purple-400 to-purple-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-bolt"></i>
                </div>
            </div>
            <p class="text-gray-500 text-xs mb-1">Sesi</p>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['sessions'] ?? 0) }}</p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-user-plus"></i>
                </div>
            </div>
            <p class="text-gray-500 text-xs mb-1">Pengguna Baru</p>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['newUsers'] ?? 0) }}</p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-gradient-to-br from-red-400 to-rose-500 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-times-circle"></i>
                </div>
            </div>
            <p class="text-gray-500 text-xs mb-1">Bounce Rate</p>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['bounceRate'] ?? 0 }}%</p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <p class="text-gray-500 text-xs mb-1">Durasi Rata-rata</p>
            <p class="text-3xl font-bold text-gray-900">{{ gmdate('i:s', $stats['avgDuration'] ?? 0) }}</p>
        </div>
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5" wire:ignore>
        {{-- Page Views Chart --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-gray-900 flex items-center gap-2"><i class="fas fa-chart-area text-blue-500"></i>Traffic Harian</h3>
                    <p class="text-xs text-gray-500">Pageviews & pengguna aktif</p>
                </div>
                <div class="flex items-center gap-3 text-xs">
                    <span class="flex items-center gap-1"><span class="w-3 h-3 bg-blue-500 rounded"></span>Views</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 bg-emerald-500 rounded"></span>Users</span>
                </div>
            </div>
            <div class="p-5">
                <div style="height: 280px;"><canvas id="trafficChart"></canvas></div>
            </div>
        </div>

        {{-- Devices Chart --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-900 flex items-center gap-2"><i class="fas fa-mobile-alt text-purple-500"></i>Perangkat</h3>
                <p class="text-xs text-gray-500">Distribusi pengunjung</p>
            </div>
            <div class="p-5">
                <div style="height: 280px;"><canvas id="devicesChart"></canvas></div>
            </div>
        </div>
    </div>

    {{-- Tables --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {{-- Top Pages --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-900 flex items-center gap-2"><i class="fas fa-file-alt text-blue-500"></i>Halaman Populer</h3>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($topPages as $index => $page)
                <div class="px-5 py-3 flex items-center gap-3 hover:bg-gray-50 transition">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-white text-xs font-bold">
                        {{ $index + 1 }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-900 text-sm truncate">{{ $page['path'] }}</p>
                    </div>
                    <span class="px-3 py-1.5 text-xs font-bold bg-blue-100 text-blue-700 rounded-full">
                        {{ number_format($page['views']) }}
                    </span>
                </div>
                @empty
                <div class="p-8 text-center text-gray-400">
                    <i class="fas fa-inbox text-3xl mb-2"></i>
                    <p class="text-sm">Belum ada data</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Traffic Sources --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-900 flex items-center gap-2"><i class="fas fa-globe text-emerald-500"></i>Sumber Traffic</h3>
            </div>
            <div class="divide-y divide-gray-50">
                @php
                    $sourceIcons = [
                        'Direct' => 'fas fa-link',
                        'Organic Search' => 'fas fa-search',
                        'Referral' => 'fas fa-external-link-alt',
                        'Social' => 'fas fa-share-alt',
                        'Email' => 'fas fa-envelope',
                        'Paid Search' => 'fas fa-ad',
                    ];
                    $sourceColors = [
                        'Direct' => 'from-blue-500 to-blue-600',
                        'Organic Search' => 'from-emerald-500 to-emerald-600',
                        'Referral' => 'from-purple-500 to-purple-600',
                        'Social' => 'from-pink-500 to-pink-600',
                        'Email' => 'from-amber-500 to-amber-600',
                        'Paid Search' => 'from-red-500 to-red-600',
                    ];
                @endphp
                @forelse($trafficSources as $source)
                <div class="px-5 py-3 flex items-center gap-3 hover:bg-gray-50 transition">
                    <div class="w-9 h-9 bg-gradient-to-br {{ $sourceColors[$source['source']] ?? 'from-gray-500 to-gray-600' }} rounded-lg flex items-center justify-center text-white">
                        <i class="{{ $sourceIcons[$source['source']] ?? 'fas fa-chart-pie' }} text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-900 text-sm">{{ $source['source'] }}</p>
                    </div>
                    <span class="px-3 py-1.5 text-xs font-bold bg-emerald-100 text-emerald-700 rounded-full">
                        {{ number_format($source['sessions']) }} sesi
                    </span>
                </div>
                @empty
                <div class="p-8 text-center text-gray-400">
                    <i class="fas fa-inbox text-3xl mb-2"></i>
                    <p class="text-sm">Belum ada data</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const pageViewsData = @json($pageViews);
const devicesData = @json($devices);

function initCharts() {
    Chart.helpers.each(Chart.instances, (instance) => instance.destroy());
    
    const trafficEl = document.getElementById('trafficChart');
    const devicesEl = document.getElementById('devicesChart');
    
    if (trafficEl && pageViewsData.length > 0) {
        const ctx = trafficEl.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 280);
        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.3)');
        gradient.addColorStop(1, 'rgba(59, 130, 246, 0.02)');
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: pageViewsData.map(d => d.date),
                datasets: [
                    {
                        label: 'Views',
                        data: pageViewsData.map(d => d.views),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 3,
                        pointBackgroundColor: 'rgb(59, 130, 246)'
                    },
                    {
                        label: 'Users',
                        data: pageViewsData.map(d => d.users),
                        borderColor: 'rgb(16, 185, 129)',
                        backgroundColor: 'transparent',
                        borderDash: [5, 5],
                        tension: 0.4,
                        pointRadius: 3,
                        pointBackgroundColor: 'rgb(16, 185, 129)'
                    }
                ]
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
    
    if (devicesEl && devicesData.length > 0) {
        const colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444'];
        
        new Chart(devicesEl.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: devicesData.map(d => d.device),
                datasets: [{
                    data: devicesData.map(d => d.users),
                    backgroundColor: colors.slice(0, devicesData.length),
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 20, font: { size: 12 } }
                    }
                },
                cutout: '65%'
            }
        });
    }
}

initCharts();
document.addEventListener('livewire:navigated', initCharts);

Livewire.on('dataLoaded', () => {
    setTimeout(initCharts, 100);
});
</script>
@endpush
