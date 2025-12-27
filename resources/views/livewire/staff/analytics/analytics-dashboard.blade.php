<div>
@section('title', 'Analytics')

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-orange-500/30">
                <i class="fas fa-chart-line text-2xl"></i>
            </div>
            <div>
                <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Analytics</h1>
                <p class="text-sm text-gray-500">Statistik kunjungan online & offline</p>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-2 border-b border-gray-200">
        <button wire:click="$set('activeTab', 'online')" class="px-6 py-3 font-semibold text-sm transition border-b-2 {{ $activeTab === 'online' ? 'text-blue-600 border-blue-600' : 'text-gray-500 border-transparent hover:text-gray-700' }}">
            <i class="fas fa-globe mr-2"></i>Website (Online)
        </button>
        <button wire:click="$set('activeTab', 'offline')" class="px-6 py-3 font-semibold text-sm transition border-b-2 {{ $activeTab === 'offline' ? 'text-blue-600 border-blue-600' : 'text-gray-500 border-transparent hover:text-gray-700' }}">
            <i class="fas fa-door-open mr-2"></i>Kunjungan (Offline)
        </button>
    </div>

    @if($activeTab === 'online')
    {{-- ONLINE TAB --}}
    <div wire:init="loadData">
        {{-- Period Filter --}}
        <div class="flex items-center gap-3 mb-6">
            <select wire:model.live="period" class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700">
                <option value="7d">7 Hari Terakhir</option>
                <option value="30d">30 Hari Terakhir</option>
                <option value="90d">90 Hari Terakhir</option>
            </select>
            <button wire:click="loadData" class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
                <i class="fas fa-sync-alt" wire:loading.class="fa-spin" wire:target="loadData"></i>
            </button>
            @if(!$isConfigured)
            <span class="text-xs text-amber-600 bg-amber-50 px-3 py-1 rounded-full">Mode Demo</span>
            @endif
        </div>

        {{-- Quick Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-6 gap-4 mb-6">
            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-5 text-white">
                <p class="text-blue-100 text-xs mb-1">Total Pengguna</p>
                <p class="text-3xl font-bold">{{ number_format($stats['users'] ?? 0) }}</p>
            </div>
            <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-5 text-white">
                <p class="text-emerald-100 text-xs mb-1">Pageviews</p>
                <p class="text-3xl font-bold">{{ number_format($stats['pageviews'] ?? 0) }}</p>
            </div>
            <div class="bg-white rounded-2xl p-5 border border-gray-100">
                <p class="text-gray-500 text-xs mb-1">Sesi</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['sessions'] ?? 0) }}</p>
            </div>
            <div class="bg-white rounded-2xl p-5 border border-gray-100">
                <p class="text-gray-500 text-xs mb-1">Pengguna Baru</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['newUsers'] ?? 0) }}</p>
            </div>
            <div class="bg-white rounded-2xl p-5 border border-gray-100">
                <p class="text-gray-500 text-xs mb-1">Bounce Rate</p>
                <p class="text-3xl font-bold text-gray-900">{{ $stats['bounceRate'] ?? 0 }}%</p>
            </div>
            <div class="bg-white rounded-2xl p-5 border border-gray-100">
                <p class="text-gray-500 text-xs mb-1">Durasi Rata-rata</p>
                <p class="text-3xl font-bold text-gray-900">{{ gmdate('i:s', $stats['avgDuration'] ?? 0) }}</p>
            </div>
        </div>

        {{-- Charts Row --}}
        <div class="grid lg:grid-cols-2 gap-6">
            {{-- Top Pages --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h3 class="font-bold text-gray-900 mb-4">Halaman Populer</h3>
                <div class="space-y-3">
                    @foreach($topPages as $page)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 truncate flex-1">{{ $page['path'] }}</span>
                        <span class="text-sm font-bold text-gray-900 ml-4">{{ number_format($page['views']) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            {{-- Devices --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h3 class="font-bold text-gray-900 mb-4">Perangkat</h3>
                <div class="space-y-3">
                    @foreach($devices as $device)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">{{ $device['device'] }}</span>
                        <span class="text-sm font-bold text-gray-900">{{ number_format($device['users']) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @else
    {{-- OFFLINE TAB - Kunjungan Perpustakaan --}}
    <div>
        {{-- Filters --}}
        <div class="flex flex-wrap items-center gap-3 mb-6">
            <select wire:model.live="visitPeriod" class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700">
                <option value="today">Hari Ini</option>
                <option value="7d">7 Hari Terakhir</option>
                <option value="30d">30 Hari Terakhir</option>
                <option value="90d">90 Hari Terakhir</option>
            </select>
            <select wire:model.live="visitBranch" class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700">
                <option value="">Semua Cabang</option>
                @foreach($branches as $branch)
                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                @endforeach
            </select>
            <button wire:click="loadVisitData" class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
                <i class="fas fa-sync-alt" wire:loading.class="fa-spin" wire:target="loadVisitData"></i>
            </button>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-5 text-white">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <p class="text-blue-100 text-xs mb-1">Total Kunjungan</p>
                <p class="text-3xl font-bold">{{ number_format($visitStats['total'] ?? 0) }}</p>
            </div>
            <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-5 text-white">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-id-card"></i>
                    </div>
                </div>
                <p class="text-emerald-100 text-xs mb-1">Anggota</p>
                <p class="text-3xl font-bold">{{ number_format($visitStats['members'] ?? 0) }}</p>
            </div>
            <div class="bg-gradient-to-br from-violet-500 to-purple-600 rounded-2xl p-5 text-white">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                <p class="text-violet-100 text-xs mb-1">Tamu</p>
                <p class="text-3xl font-bold">{{ number_format($visitStats['guests'] ?? 0) }}</p>
            </div>
            <div class="bg-white rounded-2xl p-5 border border-gray-100">
                <p class="text-gray-500 text-xs mb-1">Hari Ini</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($visitStats['today'] ?? 0) }}</p>
            </div>
            <div class="bg-white rounded-2xl p-5 border border-gray-100">
                <p class="text-gray-500 text-xs mb-1">Rata-rata/Hari</p>
                <p class="text-3xl font-bold text-gray-900">{{ $visitStats['avg_daily'] ?? 0 }}</p>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-6 mb-6">
            {{-- By Purpose --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h3 class="font-bold text-gray-900 mb-4"><i class="fas fa-bullseye text-blue-500 mr-2"></i>Berdasarkan Tujuan</h3>
                <div class="space-y-3">
                    @php $purposes = ['baca' => 'Membaca', 'pinjam' => 'Pinjam Buku', 'belajar' => 'Belajar', 'penelitian' => 'Penelitian', 'lainnya' => 'Lainnya']; @endphp
                    @foreach($purposes as $key => $label)
                    @php $count = $visitByPurpose[$key] ?? 0; $total = array_sum($visitByPurpose) ?: 1; $pct = round($count / $total * 100); @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">{{ $label }}</span>
                            <span class="font-bold text-gray-900">{{ number_format($count) }}</span>
                        </div>
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Peak Hours --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h3 class="font-bold text-gray-900 mb-4"><i class="fas fa-clock text-amber-500 mr-2"></i>Jam Sibuk</h3>
                <div class="grid grid-cols-6 gap-1">
                    @for($h = 7; $h <= 18; $h++)
                    @php $count = $visitByHour[$h] ?? 0; $max = max($visitByHour) ?: 1; $intensity = round($count / $max * 100); @endphp
                    <div class="text-center">
                        <div class="h-16 flex items-end justify-center mb-1">
                            <div class="w-full rounded-t" style="height: {{ max($intensity, 5) }}%; background: linear-gradient(to top, #3b82f6, #6366f1);"></div>
                        </div>
                        <span class="text-[10px] text-gray-500">{{ $h }}</span>
                    </div>
                    @endfor
                </div>
            </div>

            {{-- Top Members --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h3 class="font-bold text-gray-900 mb-4"><i class="fas fa-trophy text-amber-500 mr-2"></i>Pengunjung Aktif</h3>
                <div class="space-y-2">
                    @forelse($visitTopMembers as $i => $v)
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-full bg-{{ $i < 3 ? 'amber' : 'gray' }}-100 text-{{ $i < 3 ? 'amber' : 'gray' }}-600 text-xs font-bold flex items-center justify-center">{{ $i + 1 }}</span>
                        <span class="flex-1 text-sm text-gray-700 truncate">{{ $v['member']['name'] ?? 'Unknown' }}</span>
                        <span class="text-sm font-bold text-gray-900">{{ $v['visit_count'] }}x</span>
                    </div>
                    @empty
                    <p class="text-gray-400 text-sm text-center py-4">Belum ada data</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Recent Visits Table --}}
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-900"><i class="fas fa-history text-blue-500 mr-2"></i>Kunjungan Terbaru</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Waktu</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Pengunjung</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tipe</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tujuan</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Cabang</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($visitRecent as $visit)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-500">{{ \Carbon\Carbon::parse($visit['visited_at'])->format('d/m H:i') }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                {{ $visit['visitor_type'] === 'member' ? ($visit['member']['name'] ?? '-') : $visit['guest_name'] }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $visit['visitor_type'] === 'member' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $visit['visitor_type'] === 'member' ? 'Anggota' : 'Tamu' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 capitalize">{{ $visit['purpose'] }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $visit['branch']['name'] ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada data kunjungan</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
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
</div>
