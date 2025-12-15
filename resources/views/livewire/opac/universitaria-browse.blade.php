<div class="min-h-screen bg-gradient-to-b from-slate-50 to-white">
    {{-- Hero Section --}}
    <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-800 text-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full -translate-y-1/2 translate-x-1/3"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-white rounded-full translate-y-1/2 -translate-x-1/3"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 relative">
            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-sm text-blue-200 mb-6">
                <a href="{{ route('opac.home') }}" class="hover:text-white transition">
                    <i class="fas fa-home"></i>
                </a>
                <i class="fas fa-chevron-right text-xs"></i>
                <span class="text-white font-medium">Universitaria</span>
            </nav>

            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <h1 class="text-4xl lg:text-5xl font-bold mb-3">
                        <span class="text-amber-300">Universitaria</span>
                    </h1>
                    <h2 class="text-xl lg:text-2xl font-light text-blue-100 mb-4">
                        Warisan Intelektual <span class="font-semibold">PMDG</span>
                    </h2>
                    <p class="text-blue-200 max-w-2xl leading-relaxed">
                        Universitaria adalah koleksi berharga yang menghimpun warisan intelektual dan sejarah 
                        Pondok Modern Darussalam Gontor. Melalui buku peringatan dan manuskripnya, Universitaria 
                        menjadi saksi bisu perjalanan intelektual dan spiritual yang memahat jejak-jejak sejarah 
                        dan pemikiran di lembaga ini.
                    </p>
                </div>
                
                {{-- Stats --}}
                <div class="flex gap-4">
                    <div class="bg-white/10 backdrop-blur rounded-2xl px-6 py-4 text-center">
                        <p class="text-3xl font-bold text-amber-300">{{ $ebooks->total() }}</p>
                        <p class="text-sm text-blue-200">Dokumen</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur rounded-2xl px-6 py-4 text-center">
                        <p class="text-3xl font-bold text-amber-300">{{ count($categories) }}</p>
                        <p class="text-sm text-blue-200">Kategori</p>
                    </div>
                </div>
            </div>

            {{-- Search --}}
            <div class="mt-8 max-w-2xl">
                <div class="relative">
                    <input type="text" 
                           wire:model.live.debounce.300ms="search"
                           placeholder="Cari dokumen bersejarah..."
                           class="w-full pl-12 pr-4 py-4 bg-white/10 backdrop-blur border border-white/20 rounded-2xl text-white placeholder-blue-200 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-blue-200"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Notice Banner --}}
    <div class="bg-gradient-to-r from-amber-50 to-orange-50 border-b border-amber-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <div class="flex items-center gap-3 text-amber-800">
                <i class="fas fa-shield-alt"></i>
                <p class="text-sm">
                    <strong>Koleksi Premium & Bersejarah</strong> - Dokumen ini dilindungi dan hanya dapat dibaca online. 
                    Silakan login untuk mengakses.
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Categories Grid --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            {{-- All Categories --}}
            <button wire:click="selectCategory(null)" 
                    class="group relative rounded-2xl p-5 text-left transition-all duration-300 overflow-hidden
                           {{ !$selectedCategory ? 'bg-gradient-to-br from-blue-600 to-indigo-700 text-white shadow-lg shadow-blue-500/25' : 'bg-white hover:bg-blue-50 border border-gray-100' }}">
                <div class="relative z-10">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-3
                                {{ !$selectedCategory ? 'bg-white/20' : 'bg-gradient-to-br from-blue-100 to-indigo-100' }}">
                        <i class="fas fa-layer-group text-lg {{ !$selectedCategory ? 'text-white' : 'text-blue-600' }}"></i>
                    </div>
                    <h3 class="font-bold {{ !$selectedCategory ? 'text-white' : 'text-gray-900' }}">Semua Koleksi</h3>
                    <p class="text-sm {{ !$selectedCategory ? 'text-blue-200' : 'text-gray-500' }}">{{ $ebooks->total() }} Dokumen</p>
                </div>
            </button>

            @foreach($categories as $category)
            <button wire:click="selectCategory('{{ $category->slug }}')" 
                    class="group relative rounded-2xl p-5 text-left transition-all duration-300 overflow-hidden
                           {{ $selectedCategory === $category->slug ? 'bg-gradient-to-br from-blue-600 to-indigo-700 text-white shadow-lg shadow-blue-500/25' : 'bg-white hover:bg-blue-50 border border-gray-100' }}">
                <div class="relative z-10">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-3
                                {{ $selectedCategory === $category->slug ? 'bg-white/20' : 'bg-gradient-to-br from-amber-100 to-orange-100' }}">
                        <i class="fas {{ $category->icon ?? 'fa-folder' }} text-lg {{ $selectedCategory === $category->slug ? 'text-white' : 'text-amber-600' }}"></i>
                    </div>
                    <h3 class="font-bold {{ $selectedCategory === $category->slug ? 'text-white' : 'text-gray-900' }}">{{ $category->name }}</h3>
                    <p class="text-sm {{ $selectedCategory === $category->slug ? 'text-blue-200' : 'text-gray-500' }}">{{ $category->ebooks_count }} Dokumen</p>
                </div>
            </button>
            @endforeach
        </div>

        {{-- Results --}}
        @if($ebooks->count() > 0)
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($ebooks as $ebook)
            <a href="{{ route('opac.ebook.show', $ebook) }}" 
               class="group bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-xl hover:border-blue-200 transition-all duration-300">
                {{-- Cover --}}
                <div class="aspect-[3/4] bg-gradient-to-br from-blue-100 to-indigo-100 relative overflow-hidden">
                    @if($ebook->cover_image)
                        <img src="{{ $ebook->cover_url }}" alt="{{ $ebook->title }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center p-4">
                            <i class="fas fa-book-open text-4xl text-blue-300 mb-2"></i>
                            <p class="text-xs text-center text-blue-400 font-medium line-clamp-3">{{ $ebook->title }}</p>
                        </div>
                    @endif
                    
                    {{-- Protected Badge --}}
                    <div class="absolute top-2 right-2">
                        <span class="px-2 py-1 bg-amber-500 text-white text-[10px] font-bold rounded-full flex items-center gap-1 shadow">
                            <i class="fas fa-crown text-[8px]"></i> Premium
                        </span>
                    </div>
                    
                    {{-- Year Badge --}}
                    @if($ebook->publish_year)
                    <div class="absolute bottom-2 left-2">
                        <span class="px-2 py-1 bg-black/50 backdrop-blur text-white text-xs font-medium rounded-lg">
                            {{ $ebook->publish_year }}
                        </span>
                    </div>
                    @endif
                </div>
                
                {{-- Info --}}
                <div class="p-4">
                    <h3 class="font-semibold text-gray-900 text-sm line-clamp-2 group-hover:text-blue-600 transition">
                        {{ $ebook->title }}
                    </h3>
                    <div class="flex items-center gap-2 mt-2 text-xs text-gray-500">
                        <i class="fas fa-file-pdf text-red-500"></i>
                        <span>{{ $ebook->file_size }} MB</span>
                        @if(!$ebook->is_downloadable)
                            <span class="text-amber-600"><i class="fas fa-eye"></i> Baca saja</span>
                        @endif
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $ebooks->links() }}
        </div>
        @else
        <div class="text-center py-16">
            <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-search text-3xl text-blue-400"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Tidak ditemukan</h3>
            <p class="text-gray-500">Coba ubah kata kunci pencarian atau pilih kategori lain</p>
        </div>
        @endif
    </div>
</div>
