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
        <div class="flex items-center gap-3 mb-6">
            <select wire:model.live="period" class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700">
                <option value="7d">7 Hari Terakhir</option>
                <option value="30d">30 Hari Terakhir</option>
                <option value="90d">90 Hari Terakhir</option>
            </select>
            <button wire:click="loadData" class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50">
                <i class="fas fa-sync-alt" wire:loading.class="fa-spin" wire:target="loadData"></i>
            </button>
            @if(!$isConfigured)<span class="text-xs text-amber-600 bg-amber-50 px-3 py-1 rounded-full">Mode Demo</span>@endif
        </div>

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

        <div class="grid lg:grid-cols-2 gap-6">
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
    {{-- OFFLINE TAB --}}
    <div>
        <div class="flex flex-wrap items-center gap-3 mb-6">
            <select wire:model.live="visitPeriod" class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700">
                <option value="today">Hari Ini</option>
                <option value="7d">7 Hari Terakhir</option>
                <option value="30d">30 Hari Terakhir</option>
                <option value="90d">90 Hari Terakhir</option>
            </select>
            <select wire:model.live="visitBranch" class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700">
                <option value="">Semua Cabang</option>
                @foreach($branches as $branch)<option value="{{ $branch->id }}">{{ $branch->name }}</option>@endforeach
            </select>
            <button wire:click="loadVisitData" class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50">
                <i class="fas fa-sync-alt" wire:loading.class="fa-spin" wire:target="loadVisitData"></i>
            </button>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-5 text-white">
                <p class="text-blue-100 text-xs mb-1">Total Kunjungan</p>
                <p class="text-3xl font-bold">{{ number_format($visitStats['total'] ?? 0) }}</p>
            </div>
            <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-5 text-white">
                <p class="text-emerald-100 text-xs mb-1">Anggota</p>
                <p class="text-3xl font-bold">{{ number_format($visitStats['members'] ?? 0) }}</p>
            </div>
            <div class="bg-gradient-to-br from-violet-500 to-purple-600 rounded-2xl p-5 text-white">
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

            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h3 class="font-bold text-gray-900 mb-4"><i class="fas fa-trophy text-amber-500 mr-2"></i>Pengunjung Aktif</h3>
                <div class="space-y-2">
                    @forelse($visitTopMembers as $i => $v)
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-full {{ $i < 3 ? 'bg-amber-100 text-amber-600' : 'bg-gray-100 text-gray-600' }} text-xs font-bold flex items-center justify-center">{{ $i + 1 }}</span>
                        <span class="flex-1 text-sm text-gray-700 truncate">{{ $v['member']['name'] ?? 'Unknown' }}</span>
                        <span class="text-sm font-bold text-gray-900">{{ $v['visit_count'] }}x</span>
                    </div>
                    @empty
                    <p class="text-gray-400 text-sm text-center py-4">Belum ada data</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
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
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $visit['visitor_type'] === 'member' ? ($visit['member']['name'] ?? '-') : $visit['guest_name'] }}</td>
                            <td class="px-6 py-4"><span class="px-2 py-1 text-xs font-medium rounded-full {{ $visit['visitor_type'] === 'member' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">{{ $visit['visitor_type'] === 'member' ? 'Anggota' : 'Tamu' }}</span></td>
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
