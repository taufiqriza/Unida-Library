<div class="space-y-5">
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-gradient-to-br from-amber-500 via-orange-500 to-red-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-amber-500/25">
                <i class="fas fa-clipboard-check text-xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Stock Opname</h1>
                <p class="text-sm text-gray-500">Pengecekan inventaris koleksi</p>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-3 gap-3">
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 rounded-xl p-4 text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-play-circle text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold">{{ $pageStats['active'] }}</p>
                    <p class="text-xs text-amber-100">Sedang Berjalan</p>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-emerald-500 to-teal-500 rounded-xl p-4 text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check-circle text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold">{{ $pageStats['completed'] }}</p>
                    <p class="text-xs text-emerald-100">Selesai</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-boxes-stacked text-white text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($pageStats['total_items']) }}</p>
                    <p class="text-xs text-gray-500">Total Eksemplar</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Active Stock Opname Alert --}}
    @if($activeOpname)
    <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-2xl p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-amber-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-barcode text-white text-xl"></i>
                </div>
                <div>
                    <p class="font-bold text-amber-900">{{ $activeOpname->name }}</p>
                    <p class="text-sm text-amber-700">{{ $activeOpname->code }} • Dimulai {{ $activeOpname->start_date->format('d M Y') }}</p>
                </div>
            </div>
            <button wire:click="openScanner({{ $activeOpname->id }})" 
                    class="px-5 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-semibold rounded-xl shadow-lg shadow-amber-500/25 transition flex items-center gap-2">
                <i class="fas fa-qrcode"></i>
                <span>Mulai Scan</span>
            </button>
        </div>
        {{-- Progress --}}
        <div class="mt-4">
            <div class="flex items-center justify-between text-sm mb-1">
                <span class="text-amber-700">Progress: {{ $activeOpname->found_items + $activeOpname->missing_items }}/{{ $activeOpname->total_items }}</span>
                <span class="font-bold text-amber-900">{{ $activeOpname->total_items > 0 ? round((($activeOpname->found_items + $activeOpname->missing_items) / $activeOpname->total_items) * 100) : 0 }}%</span>
            </div>
            <div class="h-2 bg-amber-200 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-amber-500 to-orange-500 rounded-full transition-all" 
                     style="width: {{ $activeOpname->total_items > 0 ? (($activeOpname->found_items + $activeOpname->missing_items) / $activeOpname->total_items) * 100 : 0 }}%"></div>
            </div>
            <div class="flex gap-4 mt-2 text-xs">
                <span class="text-emerald-600"><i class="fas fa-check mr-1"></i>{{ $activeOpname->found_items }} ditemukan</span>
                <span class="text-red-600"><i class="fas fa-times mr-1"></i>{{ $activeOpname->missing_items }} hilang</span>
                <span class="text-amber-600"><i class="fas fa-clock mr-1"></i>{{ $activeOpname->total_items - $activeOpname->found_items - $activeOpname->missing_items }} pending</span>
            </div>
        </div>
    </div>
    @endif

    {{-- Stock Opname List --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
            <h3 class="font-semibold text-gray-900">Riwayat Stock Opname</h3>
        </div>
        
        @if($opnames->count() > 0)
        <div class="divide-y divide-gray-50">
            @foreach($opnames as $opname)
            <div class="p-4 hover:bg-gray-50/50 transition">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center
                            @if($opname->status === 'in_progress') bg-amber-100 text-amber-600
                            @elseif($opname->status === 'completed') bg-emerald-100 text-emerald-600
                            @elseif($opname->status === 'cancelled') bg-red-100 text-red-600
                            @else bg-gray-100 text-gray-500 @endif">
                            <i class="fas @if($opname->status === 'in_progress') fa-spinner fa-spin @elseif($opname->status === 'completed') fa-check @elseif($opname->status === 'cancelled') fa-ban @else fa-file @endif"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $opname->name }}</p>
                            <p class="text-xs text-gray-500">{{ $opname->code }} • {{ $opname->start_date->format('d M Y') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-right hidden sm:block">
                            <div class="flex items-center gap-3 text-sm">
                                <span class="text-emerald-600 font-medium">{{ $opname->found_items }} <i class="fas fa-check text-xs"></i></span>
                                <span class="text-red-600 font-medium">{{ $opname->missing_items }} <i class="fas fa-times text-xs"></i></span>
                                <span class="text-gray-400">/{{ $opname->total_items }}</span>
                            </div>
                        </div>
                        @if($opname->status === 'in_progress')
                        <button wire:click="openScanner({{ $opname->id }})" 
                                class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-lg transition flex items-center gap-2">
                            <i class="fas fa-qrcode"></i>
                            <span class="hidden sm:inline">Scan</span>
                        </button>
                        @else
                        <span class="px-3 py-1 text-xs font-medium rounded-full
                            @if($opname->status === 'completed') bg-emerald-100 text-emerald-700
                            @elseif($opname->status === 'cancelled') bg-red-100 text-red-700
                            @else bg-gray-100 text-gray-600 @endif">
                            {{ $opname->status === 'completed' ? 'Selesai' : ($opname->status === 'cancelled' ? 'Dibatalkan' : 'Draft') }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        @if($opnames->hasPages())
        <div class="px-4 py-3 border-t border-gray-100 bg-gray-50">
            {{ $opnames->links() }}
        </div>
        @endif
        @else
        <div class="p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-clipboard-list text-gray-300 text-2xl"></i>
            </div>
            <p class="text-gray-500 font-medium">Belum ada stock opname</p>
            <p class="text-sm text-gray-400 mt-1">Stock opname dibuat oleh admin melalui panel admin</p>
        </div>
        @endif
    </div>

    {{-- Scanner Modal --}}
    @if($showScanner && $activeOpname)
    <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ barcode: @entangle('barcode') }">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" wire:click="closeScanner"></div>
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold">{{ $activeOpname->name }}</h3>
                            <p class="text-amber-100 text-sm">{{ $activeOpname->code }}</p>
                        </div>
                        <button wire:click="closeScanner" class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                {{-- Stats --}}
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <div class="grid grid-cols-3 gap-3">
                        <div class="text-center p-3 bg-amber-100 rounded-xl">
                            <p class="text-2xl font-bold text-amber-700">{{ $this->stats['pending'] }}</p>
                            <p class="text-xs text-amber-600">Pending</p>
                        </div>
                        <div class="text-center p-3 bg-emerald-100 rounded-xl">
                            <p class="text-2xl font-bold text-emerald-700">{{ $this->stats['found'] }}</p>
                            <p class="text-xs text-emerald-600">Ditemukan</p>
                        </div>
                        <div class="text-center p-3 bg-red-100 rounded-xl">
                            <p class="text-2xl font-bold text-red-700">{{ $this->stats['missing'] }}</p>
                            <p class="text-xs text-red-600">Hilang</p>
                        </div>
                    </div>
                    {{-- Progress --}}
                    <div class="mt-3">
                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span>Progress</span>
                            <span class="font-bold">{{ $this->stats['total'] > 0 ? round((($this->stats['found'] + $this->stats['missing']) / $this->stats['total']) * 100) : 0 }}%</span>
                        </div>
                        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full transition-all" 
                                 style="width: {{ $this->stats['total'] > 0 ? (($this->stats['found'] + $this->stats['missing']) / $this->stats['total']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>

                {{-- Scanner Input --}}
                <div class="p-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-barcode mr-1"></i> Scan Barcode
                    </label>
                    <div class="flex gap-2">
                        <input type="text" 
                               wire:model="barcode"
                               wire:keydown.enter="scan"
                               placeholder="Ketik atau scan barcode..."
                               class="flex-1 px-4 py-3 border border-gray-300 rounded-xl text-lg font-mono focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                               autofocus>
                        <button wire:click="scan" 
                                wire:loading.attr="disabled"
                                class="px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-semibold rounded-xl transition disabled:opacity-50">
                            <span wire:loading.remove wire:target="scan">Scan</span>
                            <span wire:loading wire:target="scan"><i class="fas fa-spinner fa-spin"></i></span>
                        </button>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Tekan Enter setelah scan barcode</p>
                </div>

                {{-- Recent Scans --}}
                <div class="px-6 pb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Scan Terakhir</label>
                    <div class="bg-gray-50 rounded-xl border max-h-40 overflow-y-auto">
                        @if(count($recentScans) > 0)
                            @foreach($recentScans as $scan)
                            <div class="flex items-center justify-between px-4 py-2 border-b border-gray-100 last:border-0">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-check-circle text-emerald-500"></i>
                                    <span class="text-sm text-gray-700 truncate max-w-[200px]">{{ $scan['title'] }}</span>
                                </div>
                                <span class="text-xs text-gray-400">{{ $scan['time'] }}</span>
                            </div>
                            @endforeach
                        @else
                            <p class="text-center text-gray-400 text-sm py-6">Belum ada item yang di-scan</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Notification listener --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('notify', (data) => {
                // Simple notification - you can integrate with toast library
                alert(data[0].message);
            });
        });
    </script>
</div>
