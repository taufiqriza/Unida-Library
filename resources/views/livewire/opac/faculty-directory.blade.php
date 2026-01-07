<div>
    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-indigo-900 via-purple-900 to-blue-900 text-white py-12 lg:py-20 relative overflow-hidden">
        <!-- Decorative elements -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -mr-48 -mt-48"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/5 rounded-full -ml-32 -mb-32"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full h-full">
            <div class="w-full h-full bg-gradient-to-r from-transparent via-white/5 to-transparent transform rotate-12"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 text-center relative z-10">
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 rounded-full text-sm font-medium mb-6">
                <i class="fas fa-crown text-amber-300"></i>
                <span>Premium Directory</span>
            </div>
            <h1 class="text-3xl lg:text-5xl font-bold mb-4">Direktori Akademisi</h1>
            <h2 class="text-xl lg:text-2xl text-indigo-200 mb-6">Universitas Darussalam Gontor</h2>
            <p class="text-indigo-200 text-lg max-w-2xl mx-auto">
                Koleksi eksklusif profil lengkap dosen, akademisi, dan pimpinan universitas dengan sistem pencarian canggih
            </p>
        </div>
    </section>

    <!-- Search & Filter Section -->
    <section class="max-w-7xl mx-auto px-4 -mt-8 relative z-10">
        <div class="bg-white rounded-2xl shadow-2xl p-6 lg:p-8 border border-gray-100">
            <!-- Search Bar -->
            <div class="mb-6">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari nama dosen atau akademisi..."
                        class="w-full pl-12 pr-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-lg"
                    >
                </div>
            </div>

            <!-- Filters -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pangkat/Gelar</label>
                    <select wire:model.live="selectedRank" class="w-full border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                        <option value="">Semua Pangkat</option>
                        @foreach($ranks as $rank)
                            <option value="{{ $rank }}">{{ $rank }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Unit Kerja</label>
                    <select wire:model.live="selectedFaculty" class="w-full border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                        <option value="">Semua Unit</option>
                        @foreach($faculties as $faculty)
                            <option value="{{ $faculty }}">{{ $faculty }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Departemen</label>
                    <select wire:model.live="selectedDepartment" class="w-full border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                        <option value="">Semua Departemen</option>
                        @foreach($departments as $department)
                            <option value="{{ $department }}">{{ $department }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button 
                        wire:click="clearFilters"
                        class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition font-medium"
                    >
                        <i class="fas fa-times mr-2"></i>Reset
                    </button>
                    <div class="flex bg-gray-100 rounded-lg p-1">
                        <button 
                            wire:click="$set('viewMode', 'grid')"
                            class="px-3 py-1 rounded {{ $viewMode === 'grid' ? 'bg-white shadow text-indigo-600' : 'text-gray-600' }}"
                        >
                            <i class="fas fa-th"></i>
                        </button>
                        <button 
                            wire:click="$set('viewMode', 'list')"
                            class="px-3 py-1 rounded {{ $viewMode === 'list' ? 'bg-white shadow text-indigo-600' : 'text-gray-600' }}"
                        >
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Results Count -->
            <div class="flex items-center justify-between mb-6">
                <p class="text-gray-600">
                    Menampilkan <span class="font-semibold text-indigo-600">{{ $this->filteredData['data']->count() }}</span> 
                    dari <span class="font-semibold">{{ number_format($this->filteredData['total']) }}</span> akademisi
                </p>
            </div>
        </div>
    </section>

    <!-- Faculty Grid/List -->
    <section class="max-w-7xl mx-auto px-4 py-8">
        @if($this->filteredData['data']->count() > 0)
            @if($viewMode === 'grid')
                <!-- Grid View -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 lg:gap-6">
                    @foreach($this->filteredData['data'] as $faculty)
                        <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 hover:-translate-y-1">
                            <!-- Photo -->
                            <div class="aspect-[3/4] bg-gradient-to-br from-indigo-100 to-purple-100 relative overflow-hidden">
                                <img 
                                    src="{{ $faculty['thumbnail_url'] }}" 
                                    alt="{{ $faculty['name'] }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                    loading="lazy"
                                    onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjI2NyIgdmlld0JveD0iMCAwIDIwMCAyNjciIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIyMDAiIGhlaWdodD0iMjY3IiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0xMDAgMTMzLjVDMTE4LjIyNSAxMzMuNSAxMzMgMTE4LjcyNSAxMzMgMTAwLjVDMTMzIDgyLjI3NDYgMTE4LjIyNSA2Ny41IDEwMCA2Ny41QzgxLjc3NDYgNjcuNSA2NyA4Mi4yNzQ2IDY3IDEwMC41QzY3IDExOC43MjUgODEuNzc0NiAxMzMuNSAxMDAgMTMzLjVaIiBmaWxsPSIjOUI5QkEwIi8+CjxwYXRoIGQ9Ik0xNjcgMjAwLjVDMTY3IDE2My4zNTUgMTM3LjE0NSAxMzMuNSAxMDAgMTMzLjVDNjIuODU1IDEzMy41IDMzIDE2My4zNTUgMzMgMjAwLjVIMTY3WiIgZmlsbD0iIzlCOUJBMCIvPgo8L3N2Zz4K'"
                                >
                                <!-- Rank Badge -->
                                @if($faculty['rank'] !== 'Dosen')
                                    <div class="absolute top-2 left-2 px-2 py-1 bg-indigo-600 text-white text-xs font-medium rounded-full">
                                        {{ $faculty['rank'] }}
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Info -->
                            <div class="p-4">
                                <h3 class="font-bold text-gray-900 text-sm leading-tight mb-1 line-clamp-2">
                                    {{ $faculty['name'] }}
                                </h3>
                                <p class="text-xs text-gray-500 mb-1">{{ $faculty['faculty'] }}</p>
                                <p class="text-xs text-indigo-600 font-medium">{{ $faculty['department'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- List View -->
                <div class="space-y-4">
                    @foreach($this->filteredData['data'] as $faculty)
                        <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 border border-gray-100 overflow-hidden">
                            <div class="flex items-center p-6">
                                <!-- Photo -->
                                <div class="w-20 h-20 rounded-xl overflow-hidden bg-gradient-to-br from-indigo-100 to-purple-100 flex-shrink-0">
                                    <img 
                                        src="{{ $faculty['thumbnail_url'] }}" 
                                        alt="{{ $faculty['name'] }}"
                                        class="w-full h-full object-cover"
                                        loading="lazy"
                                        onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjI2NyIgdmlld0JveD0iMCAwIDIwMCAyNjciIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIyMDAiIGhlaWdodD0iMjY3IiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0xMDAgMTMzLjVDMTE4LjIyNSAxMzMuNSAxMzMgMTE4LjcyNSAxMzMgMTAwLjVDMTMzIDgyLjI3NDYgMTE4LjIyNSA2Ny41IDEwMCA2Ny41QzgxLjc3NDYgNjcuNSA2NyA4Mi4yNzQ2IDY3IDEwMC41QzY3IDExOC43MjUgODEuNzc0NiAxMzMuNSAxMDAgMTMzLjVaIiBmaWxsPSIjOUI5QkEwIi8+CjxwYXRoIGQ9Ik0xNjcgMjAwLjVDMTY3IDE2My4zNTUgMTM3LjE0NSAxMzMuNSAxMDAgMTMzLjVDNjIuODU1IDEzMy41IDMzIDE2My4zNTUgMzMgMjAwLjVIMTY3WiIgZmlsbD0iIzlCOUJBMCIvPgo8L3N2Zz4K'"
                                    >
                                </div>
                                
                                <!-- Info -->
                                <div class="ml-6 flex-1">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $faculty['name'] }}</h3>
                                            <div class="flex items-center gap-4 text-sm text-gray-600">
                                                <span class="flex items-center gap-1">
                                                    <i class="fas fa-graduation-cap text-indigo-500"></i>
                                                    {{ $faculty['rank'] }}
                                                </span>
                                                <span class="flex items-center gap-1">
                                                    <i class="fas fa-building text-purple-500"></i>
                                                    {{ $faculty['faculty'] }}
                                                </span>
                                                <span class="flex items-center gap-1">
                                                    <i class="fas fa-users text-emerald-500"></i>
                                                    {{ $faculty['department'] }}
                                                </span>
                                            </div>
                                        </div>
                                        <button class="px-4 py-2 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded-lg text-sm font-medium transition">
                                            <i class="fas fa-eye mr-1"></i>Lihat
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            
            <!-- Pagination -->
            @if($this->filteredData['last_page'] > 1)
            <div class="mt-8 flex justify-center">
                <div class="flex items-center gap-2">
                    @if($this->filteredData['current_page'] > 1)
                        <button wire:click="previousPage" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm">
                            <i class="fas fa-chevron-left mr-1"></i>Sebelumnya
                        </button>
                    @endif
                    
                    <span class="px-4 py-2 text-sm text-gray-600">
                        Halaman {{ $this->filteredData['current_page'] }} dari {{ $this->filteredData['last_page'] }}
                    </span>
                    
                    @if($this->filteredData['current_page'] < $this->filteredData['last_page'])
                        <button wire:click="nextPage" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm">
                            Selanjutnya<i class="fas fa-chevron-right ml-1"></i>
                        </button>
                    @endif
                </div>
            </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-search text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Tidak ada hasil ditemukan</h3>
                <p class="text-gray-600 mb-6">Coba ubah kata kunci pencarian atau filter yang digunakan</p>
                <button 
                    wire:click="clearFilters"
                    class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition"
                >
                    Reset Semua Filter
                </button>
            </div>
        @endif
    </section>

    <!-- Statistics Section -->
    <section class="bg-gradient-to-r from-indigo-50 to-purple-50 py-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-users text-2xl text-indigo-600"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-900">{{ count($facultyData) }}</div>
                    <div class="text-sm text-gray-600">Total Akademisi</div>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-graduation-cap text-2xl text-purple-600"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-900">{{ count($ranks) }}</div>
                    <div class="text-sm text-gray-600">Tingkat Pangkat</div>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-building text-2xl text-emerald-600"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-900">{{ count($faculties) }}</div>
                    <div class="text-sm text-gray-600">Unit Kerja</div>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-sitemap text-2xl text-amber-600"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-900">{{ count($departments) }}</div>
                    <div class="text-sm text-gray-600">Departemen</div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
