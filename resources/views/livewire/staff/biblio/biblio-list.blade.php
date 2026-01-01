<div class="space-y-5">
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 via-indigo-500 to-purple-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-500/25">
                <i class="fas fa-book-open text-xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Katalog Bibliografi</h1>
                <p class="text-sm text-gray-500">
                    @if($isSuperAdmin && !$filterBranch)
                        Semua Cabang
                    @else
                        {{ $userBranch->name ?? 'Cabang' }}
                    @endif
                    • {{ number_format($stats['total_books']) }} judul, {{ number_format($stats['total_items']) }} eksemplar
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            {{-- Branch Filter for Super Admin --}}
            @if($isSuperAdmin)
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-violet-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-building text-violet-600 text-sm"></i>
                </div>
                <select wire:model.live="filterBranch" class="px-3 py-2 bg-violet-50 border border-violet-200 rounded-lg text-sm focus:ring-2 focus:ring-violet-500/20 font-medium text-violet-700">
                    <option value="">Semua Cabang</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <a href="{{ route('staff.biblio.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-medium rounded-xl shadow-lg shadow-blue-500/25 transition text-sm">
                <i class="fas fa-plus"></i>
                <span>Tambah Buku</span>
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl p-4 text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-book text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold">{{ number_format($stats['total_books']) }}</p>
                    <p class="text-xs text-blue-100">Total Judul</p>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl p-4 text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-copy text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold">{{ number_format($stats['total_items']) }}</p>
                    <p class="text-xs text-emerald-100">Eksemplar</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-star text-white text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['recent_additions']) }}</p>
                    <p class="text-xs text-gray-500">Baru (7 hari)</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-red-400 to-rose-500 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-white text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['books_without_items']) }}</p>
                    <p class="text-xs text-gray-500">Tanpa Item</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-1 inline-flex">
        <button wire:click="setTab('biblio')" 
                class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $activeTab === 'biblio' ? 'bg-blue-600 text-white shadow' : 'text-gray-600 hover:bg-gray-100' }}">
            <i class="fas fa-book mr-2"></i>Bibliografi
        </button>
        <button wire:click="setTab('items')" 
                class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $activeTab === 'items' ? 'bg-emerald-600 text-white shadow' : 'text-gray-600 hover:bg-gray-100' }}">
            <i class="fas fa-barcode mr-2"></i>Eksemplar
        </button>
    </div>

    {{-- Search & Actions --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input wire:model.live.debounce.300ms="search" type="text" 
                       placeholder="{{ $activeTab === 'biblio' ? 'Cari judul, ISBN, penulis...' : 'Cari barcode, inventaris, judul...' }}"
                       class="w-full pl-9 pr-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 rounded-lg text-sm">
            </div>
            
            @if($activeTab === 'biblio')
            <div class="flex items-center bg-gray-100 rounded-lg p-1">
                <button wire:click="setViewMode('list')" class="px-3 py-1.5 rounded-md text-sm font-medium transition {{ $viewMode === 'list' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500' }}">
                    <i class="fas fa-list"></i>
                </button>
                <button wire:click="setViewMode('grid')" class="px-3 py-1.5 rounded-md text-sm font-medium transition {{ $viewMode === 'grid' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500' }}">
                    <i class="fas fa-th-large"></i>
                </button>
            </div>
            @else
            {{-- Bulk Actions for Items --}}
            @if(count($selectedItems) > 0)
            <button wire:click="printBarcodes" class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-sm font-medium rounded-lg shadow hover:shadow-lg transition flex items-center gap-2">
                <i class="fas fa-print"></i>
                Cetak Barcode ({{ count($selectedItems) }})
            </button>
            @endif
            @endif
        </div>
    </div>

    {{-- Content --}}
    @if($activeTab === 'biblio')
        {{-- BIBLIO TAB --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            @if($books->count() > 0)
                @if($viewMode === 'list')
                <div class="divide-y divide-gray-100">
                    @foreach($books as $book)
                    <div class="p-4 hover:bg-blue-50/30 flex gap-4 transition">
                        <div class="relative w-14 h-20 flex-shrink-0">
                            <div class="w-full h-full bg-gray-100 rounded-lg overflow-hidden">
                                @if($book->image)
                                <img src="{{ asset('storage/' . $book->image) }}" class="w-full h-full object-cover" alt="">
                                @else
                                <div class="w-full h-full flex items-center justify-center text-gray-300"><i class="fas fa-book text-xl"></i></div>
                                @endif
                            </div>
                            <div class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-blue-600 rounded-full flex items-center justify-center text-white text-[10px] font-bold border-2 border-white shadow">
                                {{ $book->items->count() }}
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('staff.biblio.edit', $book->id) }}" class="font-semibold text-gray-900 hover:text-blue-600 transition block truncate">{{ $book->title }}</a>
                            <div class="flex flex-wrap gap-1 mt-1">
                                @foreach($book->authors->take(2) as $author)
                                <span class="text-xs text-purple-600 bg-purple-50 px-1.5 py-0.5 rounded">{{ $author->name }}</span>
                                @endforeach
                            </div>
                            <div class="flex flex-wrap items-center gap-3 mt-2 text-xs text-gray-500">
                                <span><i class="fas fa-barcode mr-1"></i>{{ $book->isbn ?: '-' }}</span>
                                <span class="font-mono bg-gray-100 px-1.5 py-0.5 rounded">{{ $book->call_number ?: '-' }}</span>
                                <span><i class="fas fa-calendar mr-1"></i>{{ $book->publish_year ?: '-' }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-1">
                            <button wire:click="quickView({{ $book->id }})" class="p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition" title="Lihat"><i class="fas fa-eye"></i></button>
                            <a href="{{ route('staff.biblio.edit', $book->id) }}" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit"><i class="fas fa-edit"></i></a>
                            <button wire:click="confirmDelete({{ $book->id }})" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 p-4">
                    @foreach($books as $book)
                    <div class="bg-gray-50 rounded-xl group relative">
                        <div class="absolute -top-1.5 -right-1.5 z-10 px-2 py-0.5 bg-blue-600 rounded-full text-white text-xs font-bold shadow border-2 border-white">{{ $book->items->count() }}</div>
                        <div class="relative aspect-[3/4] bg-gray-200 rounded-t-xl overflow-hidden">
                            @if($book->image)
                            <img src="{{ asset('storage/' . $book->image) }}" class="w-full h-full object-cover" alt="">
                            @else
                            <div class="w-full h-full flex items-center justify-center text-gray-300"><i class="fas fa-book text-4xl"></i></div>
                            @endif
                            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                                <button wire:click="quickView({{ $book->id }})" class="p-2 bg-white rounded-lg text-emerald-600 hover:bg-emerald-50"><i class="fas fa-eye"></i></button>
                                <a href="{{ route('staff.biblio.edit', $book->id) }}" class="p-2 bg-white rounded-lg text-blue-600 hover:bg-blue-50"><i class="fas fa-edit"></i></a>
                                <button wire:click="confirmDelete({{ $book->id }})" class="p-2 bg-white rounded-lg text-red-600 hover:bg-red-50"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                        <div class="p-3">
                            <a href="{{ route('staff.biblio.edit', $book->id) }}" class="font-medium text-gray-900 text-sm line-clamp-2 hover:text-blue-600">{{ $book->title }}</a>
                            <p class="text-xs font-mono text-gray-400 mt-1 truncate">{{ $book->call_number ?: '-' }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
                <div class="p-4 border-t border-gray-100 bg-gray-50">{{ $books->links() }}</div>
            @else
            <div class="flex flex-col items-center justify-center py-16 text-center px-4">
                <div class="w-16 h-16 bg-blue-100 rounded-xl flex items-center justify-center mb-4"><i class="fas fa-book-open text-2xl text-blue-500"></i></div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">Belum Ada Data</h3>
                <p class="text-gray-500 text-sm mb-4">{{ $search ? 'Tidak ditemukan buku yang sesuai.' : 'Mulai dengan menambahkan buku pertama.' }}</p>
                @if(!$search)
                <a href="{{ route('staff.biblio.create') }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition"><i class="fas fa-plus mr-2"></i>Tambah Buku</a>
                @endif
            </div>
            @endif
        </div>
    @else
        {{-- ITEMS TAB --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            @if($items->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left">
                                <input type="checkbox" wire:model.live="selectAll" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                            </th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Barcode</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Judul Buku</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">No. Panggil</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Lokasi</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Status</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($items as $item)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-4 py-3">
                                <input type="checkbox" wire:model.live="selectedItems" value="{{ $item->id }}" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded">{{ $item->barcode }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-900 truncate max-w-xs">{{ $item->book?->title ?? '-' }}</p>
                                <p class="text-xs text-gray-500">{{ $item->inventory_code }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-mono text-xs">{{ $item->call_number ?: $item->book?->call_number ?: '-' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs">{{ $item->location?->name ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @if($item->itemStatus)
                                <span class="px-2 py-1 text-xs rounded-full font-medium {{ $item->itemStatus->no_loan ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">
                                    {{ $item->itemStatus->name }}
                                </span>
                                @else
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('print.barcode', $item->id) }}" target="_blank" class="p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition inline-block" title="Cetak Barcode">
                                    <i class="fas fa-print"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
                <div class="text-sm text-gray-500">
                    @if(count($selectedItems) > 0)
                    <span class="font-medium text-emerald-600">{{ count($selectedItems) }} item dipilih</span>
                    @endif
                </div>
                {{ $items->links() }}
            </div>
            @else
            <div class="flex flex-col items-center justify-center py-16 text-center px-4">
                <div class="w-16 h-16 bg-emerald-100 rounded-xl flex items-center justify-center mb-4"><i class="fas fa-barcode text-2xl text-emerald-500"></i></div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">Belum Ada Eksemplar</h3>
                <p class="text-gray-500 text-sm">{{ $search ? 'Tidak ditemukan eksemplar yang sesuai.' : 'Tambahkan eksemplar melalui halaman edit buku.' }}</p>
            </div>
            @endif
        </div>
    @endif

    {{-- Quick View Modal --}}
    <template x-teleport="body">
        <div x-data="{ show: @entangle('quickViewId').live }" x-show="show" x-cloak style="position: fixed; inset: 0; z-index: 99999;" @keydown.escape.window="$wire.closeQuickView()">
            <div x-show="show" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" style="position: fixed; inset: 0; background: rgba(0,0,0,0.6);" @click="$wire.closeQuickView()"></div>
            <div style="position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; padding: 1rem; pointer-events: none;">
                <div x-show="show" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden" style="pointer-events: auto;">
                    @if($this->quickViewBook)
                    @php $book = $this->quickViewBook; @endphp
                    {{-- Header --}}
                    <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-blue-50 to-white">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-book text-blue-500"></i> Detail Bibliografi
                        </h3>
                        <button @click="$wire.closeQuickView()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    {{-- Content --}}
                    <div class="p-5 overflow-y-auto" style="max-height: calc(90vh - 140px);">
                        <div class="flex gap-5">
                            {{-- Cover --}}
                            <div class="w-32 flex-shrink-0">
                                <div class="aspect-[2/3] bg-gray-100 rounded-xl overflow-hidden">
                                    @if($book->image)
                                    <img src="{{ asset('storage/' . $book->image) }}" class="w-full h-full object-cover" alt="">
                                    @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-300"><i class="fas fa-book text-3xl"></i></div>
                                    @endif
                                </div>
                                <div class="mt-2 text-center">
                                    <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-lg">
                                        <i class="fas fa-copy mr-1"></i> {{ $book->items->count() }} eks
                                    </span>
                                </div>
                            </div>
                            {{-- Info --}}
                            <div class="flex-1 space-y-3">
                                <div>
                                    <h4 class="text-lg font-bold text-gray-900 leading-tight">{{ $book->title }}</h4>
                                    @if($book->authors->count())
                                    <p class="text-sm text-gray-600 mt-1">
                                        <i class="fas fa-user text-gray-400 mr-1"></i>
                                        {{ $book->authors->pluck('name')->implode(', ') }}
                                    </p>
                                    @endif
                                </div>
                                <div class="grid grid-cols-2 gap-3 text-sm">
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-400 w-20">ISBN</span>
                                        <span class="font-medium text-gray-900">{{ $book->isbn ?: '-' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-400 w-20">Tahun</span>
                                        <span class="font-medium text-gray-900">{{ $book->publish_year ?: '-' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-400 w-20">Penerbit</span>
                                        <span class="font-medium text-gray-900">{{ $book->publisher?->name ?: '-' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-400 w-20">Tempat</span>
                                        <span class="font-medium text-gray-900">{{ $book->place?->name ?: '-' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-400 w-20">Klasifikasi</span>
                                        <span class="font-mono font-medium text-purple-600">{{ $book->classification ?: '-' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-400 w-20">No. Panggil</span>
                                        <span class="font-mono font-medium text-blue-600">{{ $book->call_number ?: '-' }}</span>
                                    </div>
                                </div>
                                @if($book->subjects->count())
                                <div class="flex flex-wrap gap-1.5 pt-2">
                                    @foreach($book->subjects as $subject)
                                    <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-xs rounded-lg">{{ $subject->name }}</span>
                                    @endforeach
                                </div>
                                @endif
                                @if($book->abstract)
                                <div class="pt-2 border-t border-gray-100">
                                    <p class="text-xs text-gray-500 mb-1">Abstrak</p>
                                    <p class="text-sm text-gray-700 line-clamp-3">{{ $book->abstract }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    {{-- Footer --}}
                    <div class="p-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
                        <a href="{{ route('staff.biblio.show', $book->id) }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                            <i class="fas fa-external-link-alt mr-1"></i> Lihat Detail Lengkap
                        </a>
                        <div class="flex gap-2">
                            <a href="{{ route('staff.biblio.edit', $book->id) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </a>
                        </div>
                    </div>
                    @else
                    <div class="p-8 text-center">
                        <i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </template>

    {{-- Delete Confirmation Modal --}}
    <template x-teleport="body">
        <div x-data="{ show: @entangle('deleteConfirmId').live }" x-show="show" x-cloak style="position: fixed; inset: 0; z-index: 99999;">
            <div x-show="show" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" style="position: fixed; inset: 0; background: rgba(0,0,0,0.6);" @click="$wire.cancelDelete()"></div>
            <div style="position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; padding: 1rem; pointer-events: none;">
                <div x-show="show" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center" style="pointer-events: auto;">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Hapus Bibliografi?</h3>
                    <p class="text-gray-500 text-sm mb-6">Data buku dan semua eksemplar akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.</p>
                    <div class="flex gap-3">
                        <button wire:click="cancelDelete" class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition">
                            <i class="fas fa-times mr-1"></i> Batal
                        </button>
                        <button wire:click="delete" class="flex-1 px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white font-medium rounded-xl transition">
                            <i class="fas fa-trash mr-1"></i> Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- Print Barcode Modal --}}
    <template x-teleport="body">
        <div x-data="{ show: @entangle('showPrintModal').live }" x-show="show" x-cloak style="position: fixed; inset: 0; z-index: 99999;" @keydown.escape.window="$wire.closePrintModal()">
            <div x-show="show" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="position: fixed; inset: 0; background: rgba(0,0,0,0.6);" @click="$wire.closePrintModal()"></div>
            <div style="position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; padding: 1rem; pointer-events: none;">
                <div x-show="show" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden" style="pointer-events: auto;">
                    {{-- Header --}}
                    <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-white">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-print text-emerald-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Cetak Barcode</h3>
                                <p class="text-sm text-gray-500">{{ count($selectedItems) }} label akan dicetak</p>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Content --}}
                    <div class="p-5 space-y-4">
                        {{-- Print Info --}}
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                            <h4 class="text-sm font-semibold text-blue-800 flex items-center gap-2 mb-2">
                                <i class="fas fa-info-circle"></i> Informasi Cetak
                            </h4>
                            <ul class="text-xs text-blue-700 space-y-1">
                                <li><i class="fas fa-check mr-1.5 text-blue-500"></i> Ukuran label: <strong>9 × 3 cm</strong> (2 kolom per baris)</li>
                                <li><i class="fas fa-check mr-1.5 text-blue-500"></i> Kertas: <strong>A4</strong> (210 × 297 mm)</li>
                                <li><i class="fas fa-check mr-1.5 text-blue-500"></i> Margin: <strong>0.5 cm</strong> setiap sisi</li>
                                <li><i class="fas fa-check mr-1.5 text-blue-500"></i> Hasil: <strong>~18 label</strong> per halaman A4</li>
                            </ul>
                        </div>

                        {{-- Tips --}}
                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                            <h4 class="text-sm font-semibold text-amber-800 flex items-center gap-2 mb-2">
                                <i class="fas fa-lightbulb"></i> Tips Cetak
                            </h4>
                            <ul class="text-xs text-amber-700 space-y-1">
                                <li>• Gunakan kertas stiker untuk hasil terbaik</li>
                                <li>• Atur printer ke "Actual Size" / "100%"</li>
                                <li>• Nonaktifkan "Fit to Page" atau "Scale"</li>
                                <li>• Gunakan garis potong sebagai panduan</li>
                            </ul>
                        </div>

                        {{-- Preview Items --}}
                        <div>
                            <p class="text-xs text-gray-500 mb-2">Item yang akan dicetak:</p>
                            <div class="max-h-32 overflow-y-auto bg-gray-50 rounded-xl p-3 space-y-1.5">
                                @foreach($this->selectedItemsData->take(5) as $item)
                                <div class="flex items-center gap-2 text-xs">
                                    <span class="font-mono bg-white px-2 py-0.5 rounded border">{{ $item->barcode }}</span>
                                    <span class="text-gray-600 truncate">{{ Str::limit($item->book?->title, 40) }}</span>
                                </div>
                                @endforeach
                                @if(count($selectedItems) > 5)
                                <p class="text-xs text-gray-400 italic">... dan {{ count($selectedItems) - 5 }} lainnya</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="p-5 border-t border-gray-100 bg-gray-50 flex items-center justify-end gap-3">
                        <button wire:click="closePrintModal" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition">
                            <i class="fas fa-times mr-1"></i> Batal
                        </button>
                        <button wire:click="confirmPrint" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl transition">
                            <i class="fas fa-print mr-1"></i> Cetak Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>

@script
<script>
$wire.on('notify', ({ type, message }) => {
    Swal.fire({
        icon: type === 'success' ? 'success' : (type === 'error' ? 'error' : 'info'),
        title: type === 'success' ? 'Berhasil!' : (type === 'error' ? 'Gagal!' : 'Info'),
        text: message,
        timer: 2500,
        showConfirmButton: false,
        timerProgressBar: true
    });
});
</script>
@endscript
</div>
