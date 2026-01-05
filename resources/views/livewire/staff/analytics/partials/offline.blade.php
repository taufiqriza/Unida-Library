{{-- OFFLINE TAB --}}
<div>
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <div class="flex bg-gray-100 rounded-xl p-1">
            @foreach(['today' => 'Hari Ini', '7d' => '7 Hari', '30d' => '30 Hari', '90d' => '90 Hari'] as $key => $label)
            <button wire:click="setVisitPeriod('{{ $key }}')" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $visitPeriod === $key ? 'bg-white text-gray-900 shadow' : 'text-gray-600 hover:text-gray-900' }}">
                {{ $label }}
            </button>
            @endforeach
        </div>
        <select wire:model.live="visitBranch" class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700">
            <option value="">Semua Cabang</option>
            @foreach($branches as $branch)<option value="{{ $branch->id }}">{{ $branch->name }}</option>@endforeach
        </select>
        <button wire:click="loadVisitData" class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50">
            <i class="fas fa-sync-alt" wire:loading.class="fa-spin" wire:target="loadVisitData"></i>
        </button>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-5 text-white shadow-lg shadow-blue-500/25">
            <div class="flex items-center justify-between mb-3">
                <span class="text-blue-100 text-xs font-medium">Total Kunjungan</span>
                <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-door-open text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-black">{{ number_format($visitStats['total'] ?? 0) }}</p>
        </div>
        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-5 text-white shadow-lg shadow-emerald-500/25">
            <div class="flex items-center justify-between mb-3">
                <span class="text-emerald-100 text-xs font-medium">Anggota</span>
                <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-id-card text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-black">{{ number_format($visitStats['members'] ?? 0) }}</p>
        </div>
        <div class="bg-gradient-to-br from-violet-500 to-purple-600 rounded-2xl p-5 text-white shadow-lg shadow-violet-500/25">
            <div class="flex items-center justify-between mb-3">
                <span class="text-violet-100 text-xs font-medium">Tamu</span>
                <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-black">{{ number_format($visitStats['guests'] ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-xs font-medium">Hari Ini</span>
                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-day text-gray-500 text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-black text-gray-900">{{ number_format($visitStats['today'] ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-xs font-medium">Rata-rata/Hari</span>
                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-gray-500 text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-black text-gray-900">{{ $visitStats['avg_daily'] ?? 0 }}</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-bullseye text-blue-500"></i> Berdasarkan Tujuan
            </h3>
            <div class="space-y-4">
                @php $purposes = ['baca' => ['Membaca', 'fa-book-open', 'blue'], 'pinjam' => ['Pinjam Buku', 'fa-hand-holding', 'green'], 'belajar' => ['Belajar', 'fa-graduation-cap', 'purple'], 'penelitian' => ['Penelitian', 'fa-microscope', 'amber'], 'lainnya' => ['Lainnya', 'fa-ellipsis-h', 'gray']]; $totalPurpose = array_sum($visitByPurpose) ?: 1; @endphp
                @foreach($purposes as $key => [$label, $icon, $color])
                @php $count = $visitByPurpose[$key] ?? 0; $pct = round($count / $totalPurpose * 100); @endphp
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 bg-{{ $color }}-100 text-{{ $color }}-600 rounded-lg flex items-center justify-center">
                                <i class="fas {{ $icon }} text-xs"></i>
                            </div>
                            <span class="text-sm text-gray-700">{{ $label }}</span>
                        </div>
                        <span class="text-sm font-bold text-gray-900">{{ number_format($count) }}</span>
                    </div>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-{{ $color }}-500 rounded-full" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-clock text-amber-500"></i> Jam Sibuk
            </h3>
            <div class="grid grid-cols-6 gap-1">
                @for($h = 7; $h <= 18; $h++)
                @php $count = $visitByHour[$h] ?? 0; $max = max($visitByHour) ?: 1; $intensity = $count / $max; 
                $bg = $intensity > 0.8 ? 'bg-amber-500' : ($intensity > 0.6 ? 'bg-amber-400' : ($intensity > 0.4 ? 'bg-amber-300' : ($intensity > 0.2 ? 'bg-amber-200' : ($intensity > 0 ? 'bg-amber-100' : 'bg-gray-100')))); @endphp
                <div class="text-center">
                    <div class="h-16 {{ $bg }} rounded-lg mb-1 flex items-end justify-center pb-1 transition hover:scale-105" title="{{ $h }}:00 - {{ $count }} kunjungan">
                        @if($count > 0)<span class="text-[10px] font-bold {{ $intensity > 0.4 ? 'text-white' : 'text-amber-700' }}">{{ $count }}</span>@endif
                    </div>
                    <span class="text-[10px] text-gray-500">{{ $h }}</span>
                </div>
                @endfor
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-trophy text-amber-500"></i> Pengunjung Aktif
            </h3>
            <div class="space-y-2 max-h-64 overflow-y-auto">
                @forelse($visitTopMembers as $i => $v)
                <div class="flex items-center gap-3 py-2 border-b border-gray-50 last:border-0">
                    <span class="w-6 h-6 rounded-full {{ $i < 3 ? 'bg-gradient-to-br from-amber-400 to-orange-500 text-white' : 'bg-gray-100 text-gray-600' }} text-xs font-bold flex items-center justify-center">{{ $i + 1 }}</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $v['member']['name'] ?? 'Unknown' }}</p>
                        <p class="text-xs text-gray-500">{{ $v['member']['member_id'] ?? '' }}</p>
                    </div>
                    <span class="text-sm font-bold text-gray-900">{{ $v['visit_count'] }}x</span>
                </div>
                @empty
                <p class="text-gray-400 text-sm text-center py-4">Belum ada data</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-history text-blue-500"></i> Kunjungan Terbaru
            </h3>
            <span class="text-xs text-gray-500">20 terakhir</span>
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
                        <td class="px-6 py-4 text-sm text-gray-500">{{ \Carbon\Carbon::parse($visit['visited_at'])->setTimezone('Asia/Jakarta')->format('d/m H:i') }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $visit['visitor_type'] === 'member' ? ($visit['member']['name'] ?? '-') : $visit['guest_name'] }}</td>
                        <td class="px-6 py-4"><span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $visit['visitor_type'] === 'member' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">{{ $visit['visitor_type'] === 'member' ? 'Anggota' : 'Tamu' }}</span></td>
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
