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
                    @if($isSuperAdmin && !$filterBranch) Semua Cabang @else {{ $userBranch->name ?? 'Cabang' }} @endif
                    • {{ number_format($stats['total_books']) }} judul, {{ number_format($stats['total_items']) }} eksemplar
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if($isSuperAdmin)
            <select wire:model.live="filterBranch" class="px-3 py-2 bg-violet-50 border border-violet-200 rounded-lg text-sm focus:ring-2 focus:ring-violet-500/20 font-medium text-violet-700">
                <option value="">Semua Cabang</option>
                @foreach($branches as $branch)<option value="{{ $branch->id }}">{{ $branch->name }}</option>@endforeach
            </select>
            @endif
            @if(in_array($activeTab, ['biblio']))
            <a href="{{ route('staff.biblio.import') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-medium rounded-xl shadow-lg shadow-emerald-500/25 transition text-sm">
                <i class="fas fa-file-import"></i><span>Import Excel</span>
            </a>
            <a href="{{ route('staff.biblio.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-medium rounded-xl shadow-lg shadow-blue-500/25 transition text-sm">
                <i class="fas fa-plus"></i><span>Tambah Buku</span>
            </a>
            @elseif(in_array($activeTab, ['authors', 'publishers', 'subjects', 'locations', 'places', 'gmd']))
            <button wire:click="openModal('create')" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-medium rounded-xl shadow-lg shadow-emerald-500/25 transition text-sm">
                <i class="fas fa-plus"></i><span>Tambah Data</span>
            </button>
            @endif
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-3">
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl p-4 text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center"><i class="fas fa-book text-lg"></i></div>
                <div><p class="text-2xl font-bold">{{ number_format($stats['total_books']) }}</p><p class="text-xs text-blue-100">Total Judul</p></div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl p-4 text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center"><i class="fas fa-copy text-lg"></i></div>
                <div><p class="text-2xl font-bold">{{ number_format($stats['total_items']) }}</p><p class="text-xs text-emerald-100">Eksemplar</p></div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-purple-500 to-violet-600 rounded-xl p-4 text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center"><i class="fas fa-user-edit text-lg"></i></div>
                <div><p class="text-2xl font-bold">{{ number_format($stats['total_authors']) }}</p><p class="text-xs text-purple-100">Penulis</p></div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-amber-500 to-orange-600 rounded-xl p-4 text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center"><i class="fas fa-hand-holding text-lg"></i></div>
                <div><p class="text-2xl font-bold">{{ number_format($stats['active_loans']) }}</p><p class="text-xs text-amber-100">Dipinjam</p></div>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-lg flex items-center justify-center"><i class="fas fa-star text-white text-lg"></i></div>
                <div><p class="text-2xl font-bold text-gray-900">{{ number_format($stats['recent_additions']) }}</p><p class="text-xs text-gray-500">Baru (7 hari)</p></div>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-red-400 to-rose-500 rounded-lg flex items-center justify-center"><i class="fas fa-exclamation-triangle text-white text-lg"></i></div>
                <div><p class="text-2xl font-bold text-gray-900">{{ number_format($stats['books_without_items']) }}</p><p class="text-xs text-gray-500">Tanpa Item</p></div>
            </div>
        </div>
    </div>

    {{-- Tabs - Modern Pill Style --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-1.5">
        <div class="flex gap-1">
            <button wire:click="setTab('biblio')" class="flex-1 px-4 py-3 rounded-xl font-semibold text-sm transition flex items-center justify-center gap-2 {{ $activeTab === 'biblio' ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-lg' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas fa-book"></i><span>Bibliografi</span>
            </button>
            <button wire:click="setTab('items')" class="flex-1 px-4 py-3 rounded-xl font-semibold text-sm transition flex items-center justify-center gap-2 {{ $activeTab === 'items' ? 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-lg' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas fa-barcode"></i><span>Eksemplar</span>
            </button>
            <button wire:click="setTab('authors')" class="flex-1 px-4 py-3 rounded-xl font-semibold text-sm transition flex items-center justify-center gap-2 {{ $activeTab === 'authors' ? 'bg-gradient-to-r from-purple-500 to-violet-600 text-white shadow-lg' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas fa-user-edit"></i><span>Penulis</span>
            </button>
            <button wire:click="setTab('publishers')" class="flex-1 px-4 py-3 rounded-xl font-semibold text-sm transition flex items-center justify-center gap-2 {{ $activeTab === 'publishers' ? 'bg-gradient-to-r from-indigo-500 to-blue-600 text-white shadow-lg' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas fa-building"></i><span>Penerbit</span>
            </button>
            <button wire:click="setTab('subjects')" class="flex-1 px-4 py-3 rounded-xl font-semibold text-sm transition flex items-center justify-center gap-2 {{ $activeTab === 'subjects' ? 'bg-gradient-to-r from-amber-500 to-orange-600 text-white shadow-lg' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas fa-tags"></i><span>Subjek</span>
            </button>
            <button wire:click="setTab('locations')" class="flex-1 px-4 py-3 rounded-xl font-semibold text-sm transition flex items-center justify-center gap-2 {{ $activeTab === 'locations' ? 'bg-gradient-to-r from-rose-500 to-pink-600 text-white shadow-lg' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas fa-map-marker-alt"></i><span>Lokasi</span>
            </button>
            <button wire:click="setTab('places')" class="flex-1 px-4 py-3 rounded-xl font-semibold text-sm transition flex items-center justify-center gap-2 {{ $activeTab === 'places' ? 'bg-gradient-to-r from-cyan-500 to-teal-600 text-white shadow-lg' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas fa-city"></i><span>Kota</span>
            </button>
            <button wire:click="setTab('gmd')" class="flex-1 px-4 py-3 rounded-xl font-semibold text-sm transition flex items-center justify-center gap-2 {{ $activeTab === 'gmd' ? 'bg-gradient-to-r from-violet-500 to-purple-600 text-white shadow-lg' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas fa-compact-disc"></i><span>Media</span>
            </button>
        </div>
    </div>

    {{-- Search --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari..." class="w-full pl-9 pr-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 rounded-lg text-sm">
            </div>
            @if($activeTab === 'biblio')
            <div class="flex items-center bg-gray-100 rounded-lg p-1">
                <button wire:click="setViewMode('list')" class="px-3 py-1.5 rounded-md text-sm font-medium transition {{ $viewMode === 'list' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500' }}"><i class="fas fa-list"></i></button>
                <button wire:click="setViewMode('grid')" class="px-3 py-1.5 rounded-md text-sm font-medium transition {{ $viewMode === 'grid' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500' }}"><i class="fas fa-th-large"></i></button>
            </div>
            @elseif($activeTab === 'items' && count($selectedItems) > 0)
            <button wire:click="printBarcodes" class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-sm font-medium rounded-lg shadow hover:shadow-lg transition flex items-center gap-2">
                <i class="fas fa-print"></i>Cetak Barcode ({{ count($selectedItems) }})
            </button>
            @endif
        </div>
    </div>

    {{-- Content --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        @if($activeTab === 'biblio')
            @include('livewire.staff.biblio.partials.tab-biblio')
        @elseif($activeTab === 'items')
            @include('livewire.staff.biblio.partials.tab-items')
        @else
            @include('livewire.staff.biblio.partials.tab-master')
        @endif
    </div>

    {{-- Modals --}}
    @include('livewire.staff.biblio.partials.modal-quickview')
    @include('livewire.staff.biblio.partials.modal-delete')
    @include('livewire.staff.biblio.partials.modal-print')
    @include('livewire.staff.biblio.partials.modal-master')
</div>
