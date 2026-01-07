<div>
    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-amber-600 via-orange-600 to-red-700 text-white py-12 lg:py-20 relative overflow-hidden">
        <!-- Decorative elements -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -mr-48 -mt-48"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/5 rounded-full -ml-32 -mb-32"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full h-full">
            <div class="w-full h-full bg-gradient-to-r from-transparent via-white/5 to-transparent transform -rotate-12"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 text-center relative z-10">
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 rounded-full text-sm font-medium mb-6">
                <i class="fas fa-scroll text-amber-300"></i>
                <span>Koleksi Perpustakaan Nasional</span>
            </div>
            <h1 class="text-3xl lg:text-5xl font-bold mb-4">Naskah Nusantara</h1>
            <h2 class="text-xl lg:text-2xl text-orange-200 mb-6">Warisan Dokumenter Indonesia</h2>
            <p class="text-orange-200 text-lg max-w-2xl mx-auto">
                Jelajahi koleksi naskah kuno dan warisan dokumenter Nusantara dari Perpustakaan Nasional Republik Indonesia
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
                        placeholder="Cari naskah atau dokumen..."
                        class="w-full pl-12 pr-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent text-lg"
                    >
                </div>
            </div>

            <!-- Filters -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cari Berdasarkan</label>
                    <select wire:model.live="searchType" class="w-full border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500">
                        <option value="title">Judul</option>
                        <option value="author">Pengarang</option>
                        <option value="subject">Subjek</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bahasa</label>
                    <select wire:model.live="selectedLanguage" class="w-full border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500">
                        <option value="">Semua Bahasa</option>
                        @foreach($languages as $language)
                            <option value="{{ $language }}">{{ $language }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Koleksi</label>
                    <select wire:model.live="selectedType" class="w-full border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500">
                        <option value="">Semua Jenis</option>
                        @foreach($types as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button 
                        wire:click="clearFilters"
                        class="w-full px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition font-medium"
                    >
                        <i class="fas fa-times mr-2"></i>Reset Filter
                    </button>
                </div>
            </div>

            <!-- Results Count -->
            <div class="flex items-center justify-between mb-6">
                <p class="text-gray-600">
                    Menampilkan <span class="font-semibold text-orange-600">{{ $this->manuscripts['data']->count() }}</span> 
                    dari <span class="font-semibold">{{ number_format($this->manuscripts['total']) }}</span> naskah
                </p>
            </div>
        </div>
    </section>

    <!-- Manuscripts Grid -->
    <section class="max-w-7xl mx-auto px-4 py-8">
        @if($this->manuscripts['data']->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 lg:gap-6">
                @foreach($this->manuscripts['data'] as $manuscript)
                    <a href="{{ route('opac.khastara.detail', $manuscript['id']) }}" class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 hover:-translate-y-1">
                        <!-- Cover -->
                        <div class="aspect-[3/4] bg-gradient-to-br from-amber-100 to-orange-100 relative overflow-hidden">
                            <img 
                                src="{{ $manuscript['cover'] }}" 
                                alt="{{ $manuscript['title'] }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                loading="lazy"
                                onerror="this.src='/assets/images/placeholder/manuscript.jpg'"
                            >
                            <!-- Type Badge -->
                            @if($manuscript['type'])
                                <div class="absolute top-2 left-2 px-2 py-1 bg-orange-600 text-white text-xs font-medium rounded-full">
                                    {{ $manuscript['type'] }}
                                </div>
                            @endif
                            <!-- External Link Icon -->
                            <div class="absolute top-2 right-2 w-8 h-8 bg-black/50 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <i class="fas fa-eye text-white text-xs"></i>
                            </div>
                        </div>
                        
                        <!-- Info -->
                        <div class="p-4">
                            <h3 class="font-bold text-gray-900 text-sm leading-tight mb-1 line-clamp-2">
                                {{ $manuscript['title'] }}
                            </h3>
                            @if($manuscript['language'])
                                <p class="text-xs text-gray-500 mb-1">{{ $manuscript['language'] }}</p>
                            @endif
                            @if($manuscript['date'])
                                <p class="text-xs text-orange-600 font-medium">{{ $manuscript['date'] }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($this->manuscripts['total'] > 12)
            <div class="mt-8 flex justify-center">
                <div class="flex items-center gap-2">
                    @if($this->getPage() > 1)
                        <button wire:click="previousPage" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm">
                            <i class="fas fa-chevron-left mr-1"></i>Sebelumnya
                        </button>
                    @endif
                    
                    <span class="px-4 py-2 text-sm text-gray-600">
                        Halaman {{ $this->getPage() }} dari {{ ceil($this->manuscripts['total'] / 12) }}
                    </span>
                    
                    @if($this->getPage() < ceil($this->manuscripts['total'] / 12))
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
                    <i class="fas fa-scroll text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Tidak ada naskah ditemukan</h3>
                <p class="text-gray-600 mb-6">Coba ubah kata kunci pencarian atau filter yang digunakan</p>
                <button 
                    wire:click="clearFilters"
                    class="px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium transition"
                >
                    Reset Semua Filter
                </button>
            </div>
        @endif
    </section>

    <!-- Info Section -->
    <section class="bg-gradient-to-r from-amber-50 to-orange-50 py-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Tentang Koleksi Khastara</h2>
                <p class="text-gray-600 max-w-3xl mx-auto">
                    Khastara adalah Portal Satu Pintu Naskah Kuno dan Warisan Dokumenter Nusantara dari Perpustakaan Nasional Republik Indonesia. 
                    Melalui koleksi ini, Anda dapat menjelajahi karya-karya intelektual bangsa Indonesia pada masa lalu.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-scroll text-2xl text-amber-600"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Naskah Kuno</h3>
                    <p class="text-sm text-gray-600">Koleksi naskah bersejarah dari berbagai daerah di Indonesia</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-book-open text-2xl text-orange-600"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Warisan Dokumenter</h3>
                    <p class="text-sm text-gray-600">Dokumen bersejarah yang menjadi warisan budaya bangsa</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-globe-asia text-2xl text-red-600"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Akses Digital</h3>
                    <p class="text-sm text-gray-600">Akses mudah ke koleksi digital dari Perpustakaan Nasional</p>
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
</style>
