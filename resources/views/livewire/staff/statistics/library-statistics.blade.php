@section('title', 'Statistik Perpustakaan')

<div class="space-y-5">
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-gradient-to-br from-violet-600 via-purple-600 to-indigo-700 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-violet-500/30">
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
            @if(auth()->user()->role === 'super_admin')
            <div class="relative">
                <select wire:model.live="selectedBranch" 
                        class="appearance-none pl-4 pr-10 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 min-w-[180px]">
                    <option value="">🌐 Semua Cabang</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">📍 {{ $branch->name }}</option>
                    @endforeach
                </select>
                <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none text-xs"></i>
            </div>
            @endif
            <button wire:click="loadStatistics" class="px-4 py-2.5 bg-violet-600 hover:bg-violet-700 text-white rounded-xl text-sm font-medium transition flex items-center gap-2 shadow-lg shadow-violet-500/25">
                <i class="fas fa-sync-alt" wire:loading.class="fa-spin" wire:target="loadStatistics"></i>
                <span class="hidden sm:inline">Refresh</span>
            </button>
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" 
                        @if(auth()->user()->role === 'super_admin' && !$selectedBranch) disabled @endif
                        class="px-4 py-2.5 text-white rounded-xl text-sm font-medium transition flex items-center gap-2 shadow-lg
                        {{ auth()->user()->role === 'super_admin' && !$selectedBranch ? 'bg-gray-400 cursor-not-allowed' : 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-500/25' }}">
                    <i class="fas fa-file-pdf"></i>
                    <span class="hidden sm:inline">Export PDF</span>
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>
                @if(auth()->user()->role === 'super_admin' && !$selectedBranch)
                <div class="absolute right-0 mt-2 w-56 bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-700 z-50" x-show="false">
                    Pilih cabang terlebih dahulu
                </div>
                @endif
                <div x-show="open" @click.away="open = false" x-transition
                     class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-100 py-2 z-50">
                    @php 
                        $exportBranch = auth()->user()->role === 'super_admin' ? $selectedBranch : auth()->user()->branch_id;
                    @endphp
                    <a href="{{ route('staff.statistics.export', ['type' => 'overview', 'branch' => $exportBranch]) }}"
                       class="block px-4 py-2.5 text-left text-sm hover:bg-gray-50 flex items-center gap-3">
                        <i class="fas fa-chart-pie text-violet-500 w-5"></i>
                        <div>
                            <p class="font-medium text-gray-900">Ringkasan Umum</p>
                            <p class="text-xs text-gray-500">Executive Summary</p>
                        </div>
                    </a>
                    <a href="{{ route('staff.statistics.export', ['type' => 'collection', 'branch' => $exportBranch]) }}"
                       class="block px-4 py-2.5 text-left text-sm hover:bg-gray-50 flex items-center gap-3">
                        <i class="fas fa-book text-blue-500 w-5"></i>
                        <div>
                            <p class="font-medium text-gray-900">Analisis Koleksi</p>
                            <p class="text-xs text-gray-500">Klasifikasi, Media, Bahasa</p>
                        </div>
                    </a>
                    <a href="{{ route('staff.statistics.export', ['type' => 'circulation', 'branch' => $exportBranch]) }}"
                       class="block px-4 py-2.5 text-left text-sm hover:bg-gray-50 flex items-center gap-3">
                        <i class="fas fa-arrows-rotate text-amber-500 w-5"></i>
                        <div>
                            <p class="font-medium text-gray-900">Sirkulasi & Anggota</p>
                            <p class="text-xs text-gray-500">Peminjaman, Denda, Tren</p>
                        </div>
                    </a>
                    <hr class="my-2 border-gray-100">
                    <a href="{{ route('staff.statistics.export', ['type' => 'full', 'branch' => $exportBranch]) }}"
                       class="block px-4 py-2.5 text-left text-sm hover:bg-emerald-50 flex items-center gap-3">
                        <i class="fas fa-file-lines text-emerald-600 w-5"></i>
                        <div>
                            <p class="font-medium text-emerald-700">Laporan Lengkap</p>
                            <p class="text-xs text-emerald-600">Untuk Audit Mutu Internal</p>
                        </div>
                    </a>
                    <a href="{{ route('staff.statistics.export', ['type' => 'catalog', 'branch' => $exportBranch]) }}"
                       class="block px-4 py-2.5 text-left text-sm hover:bg-blue-50 flex items-center gap-3">
                        <i class="fas fa-file-csv text-blue-600 w-5"></i>
                        <div>
                            <p class="font-medium text-blue-700">Daftar Koleksi (CSV)</p>
                            <p class="text-xs text-blue-600">Judul, ISBN, No. Panggil</p>
                        </div>
                    </a>
                </div>
            </div>
            @if(auth()->user()->role === 'super_admin' && !$selectedBranch)
            <span class="text-xs text-amber-600 hidden lg:inline"><i class="fas fa-info-circle mr-1"></i>Pilih cabang untuk export</span>
            @endif
        </div>
    </div>

    {{-- Tab Navigation --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-1.5">
        <div class="flex gap-1 overflow-x-auto">
            @foreach([
                'overview' => ['icon' => 'fa-th-large', 'label' => 'Ringkasan'],
                'collection' => ['icon' => 'fa-book', 'label' => 'Koleksi'],
                'circulation' => ['icon' => 'fa-arrows-rotate', 'label' => 'Sirkulasi'],
                'digital' => ['icon' => 'fa-cloud', 'label' => 'Digital'],
            ] as $tab => $data)
            <button wire:click="setActiveTab('{{ $tab }}')" 
                    class="flex-1 px-4 py-2.5 rounded-xl font-medium text-sm transition flex items-center justify-center gap-2 whitespace-nowrap
                    {{ $activeTab === $tab ? 'bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-lg' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas {{ $data['icon'] }}"></i>
                <span class="hidden sm:inline">{{ $data['label'] }}</span>
            </button>
            @endforeach
        </div>
    </div>

    {{-- Overview Tab --}}
    @if($activeTab === 'overview')
    {{-- Quick Stats Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-4 text-white relative overflow-hidden group hover:shadow-xl transition">
            <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-book"></i>
                    </div>
                    <span class="px-2 py-0.5 bg-white/20 rounded-full text-[10px]">Koleksi</span>
                </div>
                <p class="text-2xl font-bold">{{ number_format($stats['total_titles'] ?? 0) }}</p>
                <p class="text-blue-200 text-xs">{{ number_format($stats['total_items'] ?? 0) }} eksemplar</p>
            </div>
        </div>

        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-4 text-white relative overflow-hidden group hover:shadow-xl transition">
            <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-users"></i>
                    </div>
                    <span class="px-2 py-0.5 bg-white/20 rounded-full text-[10px]">Anggota</span>
                </div>
                <p class="text-2xl font-bold">{{ number_format($stats['total_members'] ?? 0) }}</p>
                <p class="text-emerald-200 text-xs">+{{ $stats['new_members_month'] ?? 0 }} bulan ini</p>
            </div>
        </div>

        <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl p-4 text-white relative overflow-hidden group hover:shadow-xl transition">
            <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-arrows-rotate"></i>
                    </div>
                    <span class="px-2 py-0.5 bg-white/20 rounded-full text-[10px]">Sirkulasi</span>
                </div>
                <p class="text-2xl font-bold">{{ number_format($stats['active_loans'] ?? 0) }}</p>
                <p class="text-amber-200 text-xs">{{ number_format($stats['loans_this_month'] ?? 0) }} bulan ini</p>
            </div>
        </div>

        <div class="bg-gradient-to-br from-violet-500 to-purple-600 rounded-2xl p-4 text-white relative overflow-hidden group hover:shadow-xl transition">
            <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-cloud"></i>
                    </div>
                    <span class="px-2 py-0.5 bg-white/20 rounded-full text-[10px]">Digital</span>
                </div>
                <p class="text-2xl font-bold">{{ number_format($stats['total_ebooks'] ?? 0) }}</p>
                <p class="text-violet-200 text-xs">{{ number_format($stats['total_ethesis'] ?? 0) }} E-Thesis</p>
            </div>
        </div>
    </div>

    {{-- Branch Network (All View Only) --}}
    @if($viewMode === 'all' && count($branchStats) > 1)
    <div class="bg-gradient-to-br from-slate-800 via-slate-900 to-gray-900 rounded-2xl p-5 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.03\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-50"></div>
        <div class="relative">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-violet-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-network-wired text-lg"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold">Jaringan Perpustakaan</h2>
                    <p class="text-slate-400 text-sm">{{ count($branchStats) }} Cabang Aktif</p>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach($branchStats as $branch)
                <button wire:click="$set('selectedBranch', {{ $branch['id'] }})" 
                        class="bg-white/5 backdrop-blur border border-white/10 rounded-xl p-3 hover:bg-white/10 hover:border-violet-500/50 transition text-left group">
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2 py-0.5 bg-violet-500/30 rounded text-[10px] font-bold text-violet-300">{{ $branch['code'] }}</span>
                        <i class="fas fa-arrow-right text-xs text-slate-500 group-hover:text-violet-400 transition"></i>
                    </div>
                    <p class="font-semibold text-sm truncate mb-2">{{ $branch['name'] }}</p>
                    <div class="grid grid-cols-2 gap-2 text-xs text-slate-400">
                        <div><i class="fas fa-book mr-1 text-blue-400"></i>{{ number_format($branch['titles']) }}</div>
                        <div><i class="fas fa-users mr-1 text-emerald-400"></i>{{ number_format($branch['members']) }}</div>
                    </div>
                    <div class="mt-2 pt-2 border-t border-white/10">
                        <span class="text-emerald-400 text-xs"><i class="fas fa-arrow-trend-up mr-1"></i>{{ $branch['loans_month'] }} pinjam/bln</span>
                    </div>
                </button>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Charts & Details --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- Monthly Trend Chart --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
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
            <div class="p-5" wire:ignore>
                <div style="height: 260px;"><canvas id="trendChart"></canvas></div>
            </div>
        </div>

        {{-- Quick Status --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-signal text-blue-500"></i>
                Status Cepat
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
                            <i class="fas fa-exclamation text-xs"></i>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Terlambat</span>
                    </div>
                    <span class="text-lg font-bold text-red-600">{{ number_format($stats['overdue_loans'] ?? 0) }}</span>
                </div>
                <hr class="border-gray-100">
                <div class="p-3 bg-gradient-to-r from-red-50 to-orange-50 rounded-xl border border-red-100">
                    <p class="text-xs text-red-600 mb-1">Denda Belum Dibayar</p>
                    <p class="text-xl font-bold text-red-700">Rp {{ number_format($stats['unpaid_fines'] ?? 0) }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Collection Tab --}}
    @if($activeTab === 'collection')
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600">
                    <i class="fas fa-book text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_titles'] ?? 0) }}</p>
                    <p class="text-xs text-gray-500">Total Judul</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-violet-100 rounded-xl flex items-center justify-center text-violet-600">
                    <i class="fas fa-layer-group text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_items'] ?? 0) }}</p>
                    <p class="text-xs text-gray-500">Total Eksemplar</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-600">
                    <i class="fas fa-user-pen text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_authors'] ?? 0) }}</p>
                    <p class="text-xs text-gray-500">Pengarang</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center text-amber-600">
                    <i class="fas fa-building text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_publishers'] ?? 0) }}</p>
                    <p class="text-xs text-gray-500">Penerbit</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- By Classification --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-violet-50 to-indigo-50">
                <h3 class="font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-folder-tree text-violet-500"></i>
                    Berdasarkan Klasifikasi (DDC)
                </h3>
            </div>
            <div class="p-4 max-h-[400px] overflow-y-auto space-y-2">
                @forelse($byClassification as $index => $item)
                @php $maxCount = max(array_column($byClassification, 'count')); $pct = $maxCount > 0 ? ($item['count'] / $maxCount) * 100 : 0; @endphp
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-violet-500 to-indigo-600 rounded-lg flex items-center justify-center text-white text-xs font-bold">
                        {{ $item['classification'] }}
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">Kelas {{ $item['classification'] }}</span>
                            <span class="text-sm font-bold text-violet-600">{{ number_format($item['count']) }}</span>
                        </div>
                        <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-violet-500 to-indigo-500 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-400 py-8">Tidak ada data</p>
                @endforelse
            </div>
        </div>

        {{-- By Media Type --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-cyan-50">
                <h3 class="font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-photo-film text-blue-500"></i>
                    Berdasarkan Jenis Media
                </h3>
            </div>
            <div class="p-4 max-h-[400px] overflow-y-auto space-y-2">
                @php $colors = ['bg-blue-500', 'bg-emerald-500', 'bg-amber-500', 'bg-rose-500', 'bg-violet-500', 'bg-cyan-500']; @endphp
                @forelse($byMediaType as $index => $item)
                @php $maxCount = max(array_column($byMediaType, 'count')); $pct = $maxCount > 0 ? ($item['count'] / $maxCount) * 100 : 0; @endphp
                <div class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-lg transition">
                    <div class="w-10 h-10 {{ $colors[$index % count($colors)] }} rounded-lg flex items-center justify-center text-white">
                        <i class="fas fa-compact-disc"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">{{ $item['name'] }}</span>
                            <span class="text-sm font-bold text-gray-900">{{ number_format($item['count']) }}</span>
                        </div>
                        <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full {{ $colors[$index % count($colors)] }} rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-400 py-8">Tidak ada data</p>
                @endforelse
            </div>
        </div>

        {{-- By Language --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-teal-50">
                <h3 class="font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-language text-emerald-500"></i>
                    Berdasarkan Bahasa
                </h3>
            </div>
            <div class="p-4 grid grid-cols-2 gap-2">
                @forelse($byLanguage as $index => $item)
                <div class="p-3 bg-gray-50 rounded-xl hover:bg-emerald-50 transition">
                    <p class="text-lg font-bold text-gray-900">{{ number_format($item['count']) }}</p>
                    <p class="text-xs text-gray-500 uppercase">{{ $item['language'] ?: 'Unknown' }}</p>
                </div>
                @empty
                <p class="text-center text-gray-400 py-8 col-span-2">Tidak ada data</p>
                @endforelse
            </div>
        </div>

        {{-- By Year --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-amber-50 to-orange-50">
                <h3 class="font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-calendar text-amber-500"></i>
                    Berdasarkan Tahun Terbit
                </h3>
            </div>
            <div class="p-4 max-h-[300px] overflow-y-auto">
                <div class="flex flex-wrap gap-2">
                    @forelse($byYear as $item)
                    <div class="px-3 py-2 bg-amber-50 hover:bg-amber-100 rounded-lg transition cursor-default">
                        <span class="font-bold text-amber-700">{{ $item['publish_year'] }}</span>
                        <span class="text-xs text-amber-600 ml-1">({{ $item['count'] }})</span>
                    </div>
                    @empty
                    <p class="text-center text-gray-400 py-8 w-full">Tidak ada data</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Publishers & Authors --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-building text-rose-500"></i>
                    Top 10 Penerbit
                </h3>
            </div>
            <div class="p-4 space-y-2">
                @forelse($byPublisher as $index => $item)
                <div class="flex items-center gap-3">
                    <span class="w-6 h-6 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center text-xs font-bold">{{ $index + 1 }}</span>
                    <span class="flex-1 text-sm text-gray-700 truncate">{{ $item['name'] }}</span>
                    <span class="text-sm font-bold text-gray-900">{{ number_format($item['count']) }}</span>
                </div>
                @empty
                <p class="text-center text-gray-400 py-8">Tidak ada data</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-user-pen text-indigo-500"></i>
                    Top 12 Pengarang
                </h3>
            </div>
            <div class="p-4 space-y-2">
                @forelse($byAuthor as $index => $item)
                <div class="flex items-center gap-3">
                    <span class="w-6 h-6 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center text-xs font-bold">{{ $index + 1 }}</span>
                    <span class="flex-1 text-sm text-gray-700 truncate">{{ $item['name'] }}</span>
                    <span class="text-sm font-bold text-gray-900">{{ number_format($item['count']) }}</span>
                </div>
                @empty
                <p class="text-center text-gray-400 py-8">Tidak ada data</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Per Prodi & Per Tahun Input --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- By Department/Prodi --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-teal-50 to-cyan-50">
                <h3 class="font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-graduation-cap text-teal-500"></i>
                    Koleksi Per Prodi/Subjek
                </h3>
            </div>
            <div class="p-4 max-h-[400px] overflow-y-auto space-y-2">
                @forelse($byDepartment as $index => $item)
                @php $maxCount = count($byDepartment) > 0 ? max(array_column($byDepartment, 'count')) : 1; $pct = $maxCount > 0 ? ($item['count'] / $maxCount) * 100 : 0; @endphp
                <div class="flex items-center gap-3">
                    <span class="w-6 h-6 bg-teal-100 text-teal-600 rounded-full flex items-center justify-center text-xs font-bold">{{ $index + 1 }}</span>
                    <div class="flex-1">
                        <div class="flex justify-between mb-1">
                            <span class="text-sm text-gray-700 truncate">{{ $item['department'] }}</span>
                            <span class="text-sm font-bold text-gray-900">{{ number_format($item['count']) }}</span>
                        </div>
                        <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-teal-500 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-400 py-8">Tidak ada data</p>
                @endforelse
            </div>
        </div>

        {{-- By Input Year --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-pink-50 to-rose-50">
                <h3 class="font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-calendar-plus text-pink-500"></i>
                    Koleksi Per Tahun Input
                </h3>
                <p class="text-xs text-gray-500 mt-1">Jumlah buku yang diinput ke sistem per tahun</p>
            </div>
            <div class="p-4 max-h-[400px] overflow-y-auto space-y-2">
                @forelse($byInputYear as $index => $item)
                @php $maxCount = count($byInputYear) > 0 ? max(array_column($byInputYear, 'count')) : 1; $pct = $maxCount > 0 ? ($item['count'] / $maxCount) * 100 : 0; @endphp
                <div class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-lg transition">
                    <div class="w-14 h-10 bg-gradient-to-br from-pink-500 to-rose-600 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                        {{ $item['input_year'] }}
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between mb-1">
                            <span class="text-sm text-gray-600">Tahun {{ $item['input_year'] }}</span>
                            <span class="text-sm font-bold text-gray-900">{{ number_format($item['count']) }} buku</span>
                        </div>
                        <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-pink-500 to-rose-500 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-400 py-8">Tidak ada data</p>
                @endforelse
            </div>
        </div>
    </div>
    @endif

    {{-- Circulation Tab --}}
    @if($activeTab === 'circulation')
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl p-4 text-white">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <span class="text-3xl font-bold">{{ number_format($stats['loans_today'] ?? 0) }}</span>
            </div>
            <p class="text-blue-200 text-sm mt-2">Pinjam Hari Ini</p>
        </div>
        <div class="bg-gradient-to-br from-violet-500 to-purple-600 rounded-xl p-4 text-white">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-week"></i>
                </div>
                <span class="text-3xl font-bold">{{ number_format($stats['loans_this_month'] ?? 0) }}</span>
            </div>
            <p class="text-violet-200 text-sm mt-2">Pinjam Bulan Ini</p>
        </div>
        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl p-4 text-white">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar"></i>
                </div>
                <span class="text-3xl font-bold">{{ number_format($stats['loans_this_year'] ?? 0) }}</span>
            </div>
            <p class="text-emerald-200 text-sm mt-2">Pinjam Tahun Ini</p>
        </div>
        <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl p-4 text-white">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-hand-holding"></i>
                </div>
                <span class="text-3xl font-bold">{{ number_format($stats['active_loans'] ?? 0) }}</span>
            </div>
            <p class="text-amber-200 text-sm mt-2">Sedang Dipinjam</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- Members Stats --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-users text-emerald-500"></i>
                Statistik Anggota
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Anggota</span>
                    <span class="text-xl font-bold text-gray-900">{{ number_format($stats['total_members'] ?? 0) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Anggota Aktif</span>
                    <span class="text-xl font-bold text-emerald-600">{{ number_format($stats['active_members'] ?? 0) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Baru Bulan Ini</span>
                    <span class="text-xl font-bold text-blue-600">+{{ number_format($stats['new_members_month'] ?? 0) }}</span>
                </div>
            </div>
        </div>

        {{-- Fines --}}
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
                <div class="flex justify-between">
                    <span class="text-gray-600">Sudah Dibayar</span>
                    <span class="font-bold text-emerald-600">Rp {{ number_format($stats['paid_fines'] ?? 0) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total</span>
                    <span class="font-bold text-gray-900">Rp {{ number_format($stats['total_fines'] ?? 0) }}</span>
                </div>
            </div>
        </div>

        {{-- Overdue Alert --}}
        <div class="bg-gradient-to-br from-red-500 to-rose-600 rounded-2xl p-5 text-white">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold">Keterlambatan</h3>
                    <p class="text-red-200 text-sm">Perlu tindakan segera</p>
                </div>
            </div>
            <p class="text-5xl font-bold mb-2">{{ number_format($stats['overdue_loans'] ?? 0) }}</p>
            <p class="text-red-200">Pinjaman terlambat dikembalikan</p>
        </div>
    </div>
    @endif

    {{-- Digital Tab --}}
    @if($activeTab === 'digital')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-gradient-to-br from-violet-600 via-purple-600 to-indigo-700 rounded-2xl p-6 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative">
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mb-4">
                    <i class="fas fa-book-open text-3xl"></i>
                </div>
                <p class="text-violet-200 text-sm mb-1">E-Book Digital</p>
                <p class="text-5xl font-bold mb-2">{{ number_format($stats['total_ebooks'] ?? 0) }}</p>
                <p class="text-violet-200">Koleksi e-book tersedia untuk diunduh</p>
            </div>
        </div>

        <div class="bg-gradient-to-br from-emerald-600 via-teal-600 to-cyan-700 rounded-2xl p-6 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative">
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mb-4">
                    <i class="fas fa-graduation-cap text-3xl"></i>
                </div>
                <p class="text-emerald-200 text-sm mb-1">E-Thesis / Tugas Akhir</p>
                <p class="text-5xl font-bold mb-2">{{ number_format($stats['total_ethesis'] ?? 0) }}</p>
                <p class="text-emerald-200">Repositori karya ilmiah mahasiswa</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="text-center py-8">
            <div class="w-20 h-20 bg-violet-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-cloud text-violet-500 text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Koleksi Digital</h3>
            <p class="text-gray-500 max-w-md mx-auto">
                Koleksi digital seperti e-book dan e-thesis tersedia untuk seluruh cabang perpustakaan.
                Data ini tidak terpengaruh filter cabang.
            </p>
        </div>
    </div>
    @endif
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
        const gradientLoans = ctx.createLinearGradient(0, 0, 0, 260);
        gradientLoans.addColorStop(0, 'rgba(139, 92, 246, 0.4)');
        gradientLoans.addColorStop(1, 'rgba(139, 92, 246, 0.02)');
        
        const gradientReturns = ctx.createLinearGradient(0, 0, 0, 260);
        gradientReturns.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
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
                        pointBackgroundColor: 'rgb(139, 92, 246)',
                        borderWidth: 3
                    },
                    {
                        label: 'Pengembalian',
                        data: chartData.monthly.returns,
                        borderColor: 'rgb(16, 185, 129)',
                        backgroundColor: gradientReturns,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: 'rgb(16, 185, 129)',
                        borderWidth: 3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: { size: 14, weight: 'bold' }
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { font: { size: 11 } }
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
