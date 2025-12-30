<div class="space-y-6" x-data="analyticsPage()">
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-orange-500/30">
                <i class="fas fa-chart-line text-2xl"></i>
            </div>
            <div>
                <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Analytics Dashboard</h1>
                <p class="text-sm text-gray-500">Statistik pengunjung website & perpustakaan</p>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-2 border-b border-gray-200">
        <button wire:click="$set('activeTab', 'online')" class="px-6 py-3 font-semibold text-sm transition border-b-2 {{ $activeTab === 'online' ? 'text-blue-600 border-blue-600' : 'text-gray-500 border-transparent hover:text-gray-700' }}">
            <i class="fas fa-globe mr-2"></i>Website Analytics
        </button>
        <button wire:click="$set('activeTab', 'offline')" class="px-6 py-3 font-semibold text-sm transition border-b-2 {{ $activeTab === 'offline' ? 'text-blue-600 border-blue-600' : 'text-gray-500 border-transparent hover:text-gray-700' }}">
            <i class="fas fa-door-open mr-2"></i>Kunjungan Fisik
        </button>
    </div>

    @if($activeTab === 'online')
    <div wire:init="loadData">
        {{-- REALTIME SECTION --}}
        @if($isConfigured)
        <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 rounded-3xl p-6 mb-6 text-white relative overflow-hidden" wire:poll.30s="loadRealtime">
            {{-- Background pattern --}}
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;0.4&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
            </div>
            
            <div class="relative">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <div class="w-4 h-4 bg-green-500 rounded-full animate-pulse"></div>
                            <div class="absolute inset-0 w-4 h-4 bg-green-500 rounded-full animate-ping"></div>
                        </div>
                        <h3 class="font-bold text-xl">Realtime Overview</h3>
                        <span class="text-xs text-slate-400 bg-slate-700/50 px-3 py-1 rounded-full">Live • 30 menit terakhir</span>
                    </div>
                </div>

                {{-- Realtime Stats --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="bg-gradient-to-br from-green-500/20 to-emerald-500/20 backdrop-blur-sm rounded-2xl p-5 border border-green-500/30">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 bg-green-500/30 rounded-xl flex items-center justify-center">
                                <i class="fas fa-users text-green-400"></i>
                            </div>
                            <span class="text-slate-300 text-sm">Pengunjung Aktif</span>
                        </div>
                        @if($isLoading)
                        <div class="h-12 bg-white/10 rounded-lg animate-pulse"></div>
                        @else
                        <p class="text-5xl font-black text-green-400">{{ $realtime['activeUsers'] ?? 0 }}</p>
                        @endif
                    </div>
                    <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-5 border border-white/10">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 bg-blue-500/30 rounded-xl flex items-center justify-center">
                                <i class="fas fa-eye text-blue-400"></i>
                            </div>
                            <span class="text-slate-300 text-sm">Pageviews</span>
                        </div>
                        @if($isLoading)
                        <div class="h-10 bg-white/10 rounded-lg animate-pulse"></div>
                        @else
                        <p class="text-4xl font-bold text-white">{{ $realtime['pageviews30min'] ?? 0 }}</p>
                        @endif
                    </div>
                    <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-5 border border-white/10">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 bg-purple-500/30 rounded-xl flex items-center justify-center">
                                <i class="fas fa-bolt text-purple-400"></i>
                            </div>
                            <span class="text-slate-300 text-sm">Events</span>
                        </div>
                        @if($isLoading)
                        <div class="h-10 bg-white/10 rounded-lg animate-pulse"></div>
                        @else
                        <p class="text-4xl font-bold text-white">{{ $realtime['events30min'] ?? 0 }}</p>
                        @endif
                    </div>
                    <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-5 border border-white/10">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 bg-amber-500/30 rounded-xl flex items-center justify-center">
                                <i class="fas fa-globe-asia text-amber-400"></i>
                            </div>
                            <span class="text-slate-300 text-sm">Negara</span>
                        </div>
                        @if($isLoading)
                        <div class="h-10 bg-white/10 rounded-lg animate-pulse"></div>
                        @else
                        <p class="text-4xl font-bold text-white">{{ count($realtime['countries'] ?? []) }}</p>
                        @endif
                    </div>
                </div>

                {{-- Realtime Details Grid --}}
                <div class="grid lg:grid-cols-3 gap-4">
                    {{-- Active Pages --}}
                    <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-5 border border-white/10">
                        <h4 class="font-semibold text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-file-alt text-blue-400"></i> Halaman Aktif
                        </h4>
                        <div class="space-y-2 max-h-48 overflow-y-auto">
                            @forelse($realtime['pages'] ?? [] as $page)
                            <div class="flex items-center justify-between py-2 border-b border-white/5 last:border-0">
                                <span class="text-slate-300 text-sm truncate flex-1 mr-3" title="{{ $page['name'] }}">{{ Str::limit($page['name'], 30) }}</span>
                                <span class="bg-blue-500/20 text-blue-300 px-2.5 py-1 rounded-lg text-xs font-bold">{{ $page['users'] }}</span>
                            </div>
                            @empty
                            <p class="text-slate-500 text-sm text-center py-4">Tidak ada aktivitas</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Countries with flags --}}
                    <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-5 border border-white/10">
                        <h4 class="font-semibold text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-globe text-green-400"></i> Lokasi Pengunjung
                        </h4>
                        <div class="space-y-2 max-h-48 overflow-y-auto">
                            @forelse($realtime['countries'] ?? [] as $country)
                            <div class="flex items-center justify-between py-2 border-b border-white/5 last:border-0">
                                <div class="flex items-center gap-2">
                                    @if($country['code'])
                                    <img src="https://flagcdn.com/24x18/{{ strtolower($country['code']) }}.png" class="w-6 h-4 rounded shadow" alt="{{ $country['name'] }}" onerror="this.style.display='none'">
                                    @endif
                                    <span class="text-slate-300 text-sm">{{ $country['name'] }}</span>
                                </div>
                                <span class="bg-green-500/20 text-green-300 px-2.5 py-1 rounded-lg text-xs font-bold">{{ $country['users'] }}</span>
                            </div>
                            @empty
                            <p class="text-slate-500 text-sm text-center py-4">Tidak ada data</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Devices & Sources --}}
                    <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-5 border border-white/10">
                        <h4 class="font-semibold text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-laptop text-purple-400"></i> Perangkat & Sumber
                        </h4>
                        <div class="space-y-3">
                            @foreach($realtime['devices'] ?? [] as $device)
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $device['name'] === 'Desktop' ? 'bg-blue-500/20 text-blue-400' : ($device['name'] === 'Mobile' ? 'bg-green-500/20 text-green-400' : 'bg-purple-500/20 text-purple-400') }}">
                                    <i class="fas {{ $device['name'] === 'Desktop' ? 'fa-desktop' : ($device['name'] === 'Mobile' ? 'fa-mobile-alt' : 'fa-tablet-alt') }}"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-slate-300">{{ $device['name'] }}</span>
                                        <span class="text-white font-bold">{{ $device['users'] }}</span>
                                    </div>
                                    @php $total = collect($realtime['devices'] ?? [])->sum('users') ?: 1; $pct = round($device['users'] / $total * 100); @endphp
                                    <div class="h-1.5 bg-white/10 rounded-full overflow-hidden">
                                        <div class="h-full bg-gradient-to-r from-blue-500 to-purple-500 rounded-full" style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            
                            <div class="border-t border-white/10 pt-3 mt-3">
                                <p class="text-xs text-slate-400 mb-2">Traffic Sources</p>
                                @foreach(array_slice($realtime['sources'] ?? [], 0, 3) as $source)
                                <div class="flex justify-between text-sm py-1">
                                    <span class="text-slate-400">{{ $source['name'] }}</span>
                                    <span class="text-slate-300">{{ $source['users'] }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Period Filter --}}
        <div class="flex flex-wrap items-center gap-3 mb-6">
            <div class="flex bg-gray-100 rounded-xl p-1">
                @foreach(['7d' => '7 Hari', '30d' => '30 Hari', '90d' => '90 Hari'] as $key => $label)
                <button wire:click="$set('period', '{{ $key }}')" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $period === $key ? 'bg-white text-gray-900 shadow' : 'text-gray-600 hover:text-gray-900' }}">
                    {{ $label }}
                </button>
                @endforeach
            </div>
            <button wire:click="loadData" class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 flex items-center gap-2">
                <i class="fas fa-sync-alt" wire:loading.class="fa-spin" wire:target="loadData"></i> Refresh
            </button>
            @if(!$isConfigured)
            <span class="text-xs text-amber-600 bg-amber-50 px-3 py-2 rounded-xl flex items-center gap-2">
                <i class="fas fa-exclamation-triangle"></i> Service Account belum dikonfigurasi
            </span>
            @endif
        </div>

        {{-- Main Stats Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-5 text-white shadow-lg shadow-blue-500/25">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-blue-100 text-xs font-medium">Total Pengguna</span>
                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-sm"></i>
                    </div>
                </div>
                <p class="text-3xl font-black">{{ number_format($stats['users'] ?? 0) }}</p>
                <p class="text-blue-200 text-xs mt-1">{{ number_format($stats['newUsers'] ?? 0) }} pengguna baru</p>
            </div>
            <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-5 text-white shadow-lg shadow-emerald-500/25">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-emerald-100 text-xs font-medium">Pageviews</span>
                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-eye text-sm"></i>
                    </div>
                </div>
                <p class="text-3xl font-black">{{ number_format($stats['pageviews'] ?? 0) }}</p>
                <p class="text-emerald-200 text-xs mt-1">{{ $stats['pagesPerSession'] ?? 0 }} halaman/sesi</p>
            </div>
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-gray-500 text-xs font-medium">Sesi</span>
                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-bar text-gray-500 text-sm"></i>
                    </div>
                </div>
                <p class="text-3xl font-black text-gray-900">{{ number_format($stats['sessions'] ?? 0) }}</p>
                <p class="text-gray-400 text-xs mt-1">{{ number_format($stats['engagedSessions'] ?? 0) }} engaged</p>
            </div>
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-gray-500 text-xs font-medium">Bounce Rate</span>
                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-sign-out-alt text-gray-500 text-sm"></i>
                    </div>
                </div>
                <p class="text-3xl font-black text-gray-900">{{ $stats['bounceRate'] ?? 0 }}%</p>
                <p class="text-gray-400 text-xs mt-1">{{ ($stats['bounceRate'] ?? 0) < 50 ? 'Bagus' : 'Perlu perbaikan' }}</p>
            </div>
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-gray-500 text-xs font-medium">Durasi Rata-rata</span>
                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-gray-500 text-sm"></i>
                    </div>
                </div>
                <p class="text-3xl font-black text-gray-900">{{ gmdate('i:s', $stats['avgDuration'] ?? 0) }}</p>
                <p class="text-gray-400 text-xs mt-1">per sesi</p>
            </div>
        </div>

        @include('livewire.staff.analytics.partials.charts')
        @include('livewire.staff.analytics.partials.details')
    </div>
    @else
    @include('livewire.staff.analytics.partials.offline')
    @endif
</div>
