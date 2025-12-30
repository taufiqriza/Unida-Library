{{-- Charts Section --}}
<div class="grid lg:grid-cols-2 gap-6 mb-6">
    {{-- Traffic Chart --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <h3 class="font-bold text-gray-900">Traffic Overview</h3>
            <div class="flex gap-4 text-xs">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 bg-blue-500 rounded-full"></span> Pageviews</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 bg-emerald-500 rounded-full"></span> Users</span>
            </div>
        </div>
        @if($isLoading)
        <div class="h-64 flex items-center justify-center">
            <div class="text-center">
                <i class="fas fa-spinner fa-spin text-2xl text-gray-300 mb-2"></i>
                <p class="text-sm text-gray-400">Memuat data...</p>
            </div>
        </div>
        @elseif(count($pageViews) > 0)
        <div class="h-64">
            <canvas id="trafficChart"></canvas>
        </div>
        @push('scripts')
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('trafficChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json(collect($pageViews)->pluck('date')),
                        datasets: [{
                            label: 'Pageviews',
                            data: @json(collect($pageViews)->pluck('views')),
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 2,
                        }, {
                            label: 'Users',
                            data: @json(collect($pageViews)->pluck('users')),
                            borderColor: '#10b981',
                            backgroundColor: 'transparent',
                            tension: 0.4,
                            borderWidth: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { grid: { display: false } },
                            y: { grid: { color: '#f3f4f6' } }
                        }
                    }
                });
            }
        });
        </script>
        @endpush
        @else
        <div class="h-64 flex items-center justify-center">
            <p class="text-gray-400">Tidak ada data</p>
        </div>
        @endif
    </div>

    {{-- Hourly Heatmap --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
        <h3 class="font-bold text-gray-900 mb-6">Aktivitas per Jam</h3>
        <div class="grid grid-cols-12 gap-1">
            @foreach($hourlyData as $hour => $count)
            @php 
                $max = max($hourlyData) ?: 1; 
                $intensity = $count / $max;
                $bg = $intensity > 0.8 ? 'bg-blue-600' : ($intensity > 0.6 ? 'bg-blue-500' : ($intensity > 0.4 ? 'bg-blue-400' : ($intensity > 0.2 ? 'bg-blue-300' : ($intensity > 0 ? 'bg-blue-200' : 'bg-gray-100'))));
            @endphp
            <div class="text-center">
                <div class="h-12 {{ $bg }} rounded-lg mb-1 flex items-center justify-center transition hover:scale-105 cursor-default" title="{{ $hour }}:00 - {{ number_format($count) }} users">
                    @if($count > 0)<span class="text-[10px] font-bold {{ $intensity > 0.4 ? 'text-white' : 'text-blue-700' }}">{{ $count > 999 ? round($count/1000, 1).'k' : $count }}</span>@endif
                </div>
                <span class="text-[10px] text-gray-400">{{ $hour }}</span>
            </div>
            @endforeach
        </div>
        <div class="flex items-center justify-center gap-2 mt-4 text-xs text-gray-500">
            <span>Rendah</span>
            <div class="flex gap-0.5">
                <div class="w-4 h-3 bg-gray-100 rounded"></div>
                <div class="w-4 h-3 bg-blue-200 rounded"></div>
                <div class="w-4 h-3 bg-blue-300 rounded"></div>
                <div class="w-4 h-3 bg-blue-400 rounded"></div>
                <div class="w-4 h-3 bg-blue-500 rounded"></div>
                <div class="w-4 h-3 bg-blue-600 rounded"></div>
            </div>
            <span>Tinggi</span>
        </div>
    </div>
</div>

{{-- User Types & Devices --}}
<div class="grid lg:grid-cols-3 gap-6 mb-6">
    {{-- User Types Donut --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
        <h3 class="font-bold text-gray-900 mb-4">Tipe Pengguna</h3>
        <div class="flex items-center justify-center gap-8">
            <div class="relative w-32 h-32">
                @php 
                    $newPct = ($stats['users'] ?? 1) > 0 ? round(($stats['newUsers'] ?? 0) / ($stats['users'] ?? 1) * 100) : 0;
                    $returnPct = 100 - $newPct;
                @endphp
                <svg class="w-32 h-32 transform -rotate-90">
                    <circle cx="64" cy="64" r="56" stroke="#e5e7eb" stroke-width="12" fill="none"/>
                    <circle cx="64" cy="64" r="56" stroke="url(#gradient1)" stroke-width="12" fill="none" stroke-dasharray="{{ $newPct * 3.52 }} 352" stroke-linecap="round"/>
                    <defs>
                        <linearGradient id="gradient1" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" style="stop-color:#3b82f6"/>
                            <stop offset="100%" style="stop-color:#8b5cf6"/>
                        </linearGradient>
                    </defs>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-2xl font-black text-gray-900">{{ $newPct }}%</span>
                    <span class="text-xs text-gray-500">Baru</span>
                </div>
            </div>
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full"></div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ number_format($stats['newUsers'] ?? 0) }}</p>
                        <p class="text-xs text-gray-500">Pengguna Baru</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-gray-300 rounded-full"></div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ number_format(($stats['users'] ?? 0) - ($stats['newUsers'] ?? 0)) }}</p>
                        <p class="text-xs text-gray-500">Kembali</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Devices --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
        <h3 class="font-bold text-gray-900 mb-4">Perangkat</h3>
        <div class="space-y-4">
            @php $totalDevices = collect($devices)->sum('value') ?: 1; @endphp
            @foreach($devices as $device)
            @php $pct = round($device['value'] / $totalDevices * 100); @endphp
            <div>
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $device['name'] === 'Desktop' ? 'bg-blue-100 text-blue-600' : ($device['name'] === 'Mobile' ? 'bg-green-100 text-green-600' : 'bg-purple-100 text-purple-600') }}">
                            <i class="fas {{ $device['name'] === 'Desktop' ? 'fa-desktop' : ($device['name'] === 'Mobile' ? 'fa-mobile-alt' : 'fa-tablet-alt') }}"></i>
                        </div>
                        <span class="font-medium text-gray-900">{{ $device['name'] }}</span>
                    </div>
                    <span class="text-sm font-bold text-gray-900">{{ $pct }}%</span>
                </div>
                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full {{ $device['name'] === 'Desktop' ? 'bg-blue-500' : ($device['name'] === 'Mobile' ? 'bg-green-500' : 'bg-purple-500') }}" style="width: {{ $pct }}%"></div>
                </div>
                <p class="text-xs text-gray-500 mt-1">{{ number_format($device['value']) }} pengguna</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Browsers --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
        <h3 class="font-bold text-gray-900 mb-4">Browser</h3>
        <div class="space-y-3">
            @php $totalBrowsers = collect($browsers)->sum('value') ?: 1; @endphp
            @foreach(array_slice($browsers, 0, 6) as $browser)
            @php 
                $pct = round($browser['value'] / $totalBrowsers * 100);
                $icon = match(strtolower($browser['name'])) {
                    'chrome' => 'fab fa-chrome text-yellow-500',
                    'safari' => 'fab fa-safari text-blue-500',
                    'firefox' => 'fab fa-firefox text-orange-500',
                    'edge' => 'fab fa-edge text-blue-600',
                    'opera' => 'fab fa-opera text-red-500',
                    default => 'fas fa-globe text-gray-500'
                };
            @endphp
            <div class="flex items-center gap-3">
                <i class="{{ $icon }} text-lg w-5"></i>
                <div class="flex-1">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-700">{{ $browser['name'] }}</span>
                        <span class="font-semibold text-gray-900">{{ $pct }}%</span>
                    </div>
                    <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
