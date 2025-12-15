<div>
    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 text-white py-10 lg:py-16 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full -ml-24 -mb-24"></div>
        
        <div class="max-w-7xl mx-auto px-4 relative">
            <div class="text-center">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 rounded-full text-sm mb-4">
                    <i class="fas fa-newspaper"></i>
                    <span>Berita & Pengumuman</span>
                </div>
                <h1 class="text-3xl lg:text-4xl font-bold mb-4">Berita Perpustakaan</h1>
                <p class="text-blue-200 max-w-2xl mx-auto">Ikuti informasi terbaru, kegiatan, dan pengumuman dari Perpustakaan UNIDA Gontor</p>
            </div>
            
            <!-- Search Bar -->
            <div class="max-w-xl mx-auto mt-8">
                <div class="relative">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari berita..." 
                        class="w-full px-5 py-4 pl-12 rounded-xl bg-white/10 backdrop-blur-sm text-white placeholder-blue-200 border border-white/20 focus:outline-none focus:ring-2 focus:ring-white/30"
                    >
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-blue-200"></i>
                    @if($search)
                    <button wire:click="$set('search', '')" class="absolute right-4 top-1/2 -translate-y-1/2 text-blue-200 hover:text-white">
                        <i class="fas fa-times"></i>
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="max-w-7xl mx-auto px-4 py-8 lg:py-12">
        <div class="flex flex-col lg:flex-row gap-8">
            
            <!-- Left: News Grid -->
            <div class="flex-1">
                <!-- Filters Bar -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <!-- Category Pills -->
                    <div class="flex flex-wrap gap-2">
                        <button 
                            wire:click="setCategory(null)"
                            class="px-4 py-2 rounded-full text-sm font-medium transition {{ !$categoryId ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                        >
                            Semua <span class="ml-1 opacity-70">({{ $totalNews }})</span>
                        </button>
                        @foreach($categories as $cat)
                        <button 
                            wire:click="setCategory({{ $cat->id }})"
                            class="px-4 py-2 rounded-full text-sm font-medium transition {{ $categoryId == $cat->id ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                        >
                            {{ $cat->name }} <span class="ml-1 opacity-70">({{ $cat->news_count }})</span>
                        </button>
                        @endforeach
                    </div>
                    
                    <!-- Sort Dropdown -->
                    <select 
                        wire:model.live="sortBy"
                        class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="latest">Terbaru</option>
                        <option value="oldest">Terlama</option>
                        <option value="popular">Populer</option>
                    </select>
                </div>

                <!-- Pinned News -->
                @if($pinnedNews && !$search && !$categoryId)
                <div class="mb-8">
                    <a href="{{ route('opac.news.show', $pinnedNews->slug) }}" class="block bg-gradient-to-r from-amber-50 to-orange-50 rounded-2xl overflow-hidden border border-amber-200 hover:shadow-xl transition group">
                        <div class="flex flex-col md:flex-row">
                            <div class="md:w-2/5 aspect-video md:aspect-auto bg-gradient-to-br from-amber-100 to-orange-100 flex items-center justify-center overflow-hidden">
                                @if($pinnedNews->image_url)
                                    <img src="{{ $pinnedNews->image_url }}" alt="{{ $pinnedNews->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                @else
                                    <i class="fas fa-newspaper text-5xl text-amber-300"></i>
                                @endif
                            </div>
                            <div class="md:w-3/5 p-6">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="px-2 py-1 bg-amber-500 text-white text-xs font-bold rounded"><i class="fas fa-thumbtack mr-1"></i>Pinned</span>
                                    @if($pinnedNews->category)
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded">{{ $pinnedNews->category->name }}</span>
                                    @endif
                                </div>
                                <h2 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition line-clamp-2">{{ $pinnedNews->title }}</h2>
                                <p class="text-gray-600 text-sm line-clamp-2 mb-4">{{ $pinnedNews->excerpt }}</p>
                                <div class="flex items-center text-xs text-gray-500">
                                    <i class="far fa-calendar mr-1"></i>
                                    {{ $pinnedNews->published_at->format('d M Y') }}
                                    <span class="mx-2">•</span>
                                    <i class="far fa-eye mr-1"></i>
                                    {{ number_format($pinnedNews->views) }} views
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                @endif

                <!-- News Grid -->
                @if($news->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($news as $item)
                    <a href="{{ route('opac.news.show', $item->slug) }}" class="bg-white rounded-xl overflow-hidden shadow-lg shadow-gray-200/50 hover:shadow-xl transition group">
                        <div class="aspect-video bg-gradient-to-br from-blue-100 to-slate-100 flex items-center justify-center overflow-hidden">
                            @if($item->image_url)
                                <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @else
                                <i class="fas fa-newspaper text-4xl text-blue-300"></i>
                            @endif
                        </div>
                        <div class="p-4">
                            <div class="flex items-center gap-2 mb-2">
                                @if($item->category)
                                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-medium rounded">{{ $item->category->name }}</span>
                                @endif
                                @if($item->is_featured)
                                <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-xs font-medium rounded"><i class="fas fa-star text-[10px]"></i></span>
                                @endif
                            </div>
                            <h3 class="font-semibold text-gray-900 line-clamp-2 group-hover:text-blue-600 transition mb-2">{{ $item->title }}</h3>
                            <p class="text-gray-500 text-sm line-clamp-2 mb-3">{{ $item->excerpt }}</p>
                            <div class="flex items-center justify-between text-xs text-gray-400">
                                <span><i class="far fa-calendar mr-1"></i>{{ $item->published_at->format('d M Y') }}</span>
                                <span><i class="far fa-eye mr-1"></i>{{ number_format($item->views) }}</span>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $news->links() }}
                </div>
                @else
                <div class="text-center py-16 bg-gray-50 rounded-2xl">
                    <i class="fas fa-newspaper text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Tidak ada berita</h3>
                    <p class="text-gray-500">
                        @if($search)
                            Tidak ditemukan berita dengan kata kunci "{{ $search }}"
                        @else
                            Belum ada berita yang dipublikasikan
                        @endif
                    </p>
                    @if($search || $categoryId)
                    <button wire:click="$set('search', ''); $set('categoryId', null)" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition">
                        Reset Filter
                    </button>
                    @endif
                </div>
                @endif
            </div>

            <!-- Right: Sidebar -->
            <div class="lg:w-80 space-y-6">
                <!-- Featured News -->
                @if($featuredNews->count() > 0)
                <div class="bg-white rounded-2xl p-5 shadow-lg shadow-gray-200/50">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-star text-amber-500"></i> Berita Unggulan
                    </h3>
                    <div class="space-y-4">
                        @foreach($featuredNews as $item)
                        <a href="{{ route('opac.news.show', $item->slug) }}" class="flex gap-3 group">
                            <div class="w-20 h-16 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                @if($item->image_url)
                                    <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="fas fa-newspaper text-gray-300"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-900 line-clamp-2 group-hover:text-blue-600 transition">{{ $item->title }}</h4>
                                <p class="text-xs text-gray-400 mt-1">{{ $item->published_at->format('d M Y') }}</p>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- E-Resources Promo -->
                <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-5 text-white">
                    <h3 class="font-bold mb-4 flex items-center gap-2">
                        <i class="fas fa-gem"></i> E-Resources Premium
                    </h3>
                    
                    <div class="space-y-3">
                        <!-- Shamela -->
                        <a href="{{ route('opac.shamela.index') }}" class="block p-3 bg-white/10 rounded-xl hover:bg-white/20 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-emerald-500/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-book-quran text-emerald-300"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-sm">Maktabah Shamela</h4>
                                    <p class="text-blue-200 text-xs">8,425 Kitab Islam Klasik</p>
                                </div>
                            </div>
                        </a>
                        
                        <!-- Universitaria -->
                        <a href="{{ route('opac.universitaria.index') }}" class="block p-3 bg-white/10 rounded-xl hover:bg-white/20 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-amber-500/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-landmark text-amber-300"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-sm">Universitaria</h4>
                                    <p class="text-blue-200 text-xs">Warisan Sejarah PMDG</p>
                                </div>
                            </div>
                        </a>
                        
                        <!-- Database -->
                        <a href="{{ route('opac.database-access') }}" class="block p-3 bg-white/10 rounded-xl hover:bg-white/20 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-purple-500/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-database text-purple-300"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-sm">Database Jurnal</h4>
                                    <p class="text-blue-200 text-xs">Gale & ProQuest (120K+)</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <a href="{{ route('opac.page', 'e-resources') }}" class="mt-4 block text-center py-2 bg-white/20 rounded-lg text-sm font-medium hover:bg-white/30 transition">
                        Semua E-Resources <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <!-- Quick Links -->
                <div class="bg-white rounded-2xl p-5 shadow-lg shadow-gray-200/50">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-bolt text-blue-500"></i> Akses Cepat
                    </h3>
                    
                    <div class="space-y-2">
                        <a href="{{ route('opac.search') }}?type=book" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition">
                            <i class="fas fa-book text-blue-500 w-5 text-center"></i>
                            <span class="text-sm text-gray-700">Cari Buku</span>
                        </a>
                        <a href="{{ route('opac.search') }}?type=ethesis" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition">
                            <i class="fas fa-graduation-cap text-purple-500 w-5 text-center"></i>
                            <span class="text-sm text-gray-700">E-Thesis</span>
                        </a>
                        <a href="{{ route('opac.journals.index') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition">
                            <i class="fas fa-newspaper text-emerald-500 w-5 text-center"></i>
                            <span class="text-sm text-gray-700">Jurnal UNIDA</span>
                        </a>
                        <a href="{{ route('opac.page', 'panduan-opac') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition">
                            <i class="fas fa-question-circle text-orange-500 w-5 text-center"></i>
                            <span class="text-sm text-gray-700">Panduan OPAC</span>
                        </a>
                    </div>
                </div>

                <!-- Follow Us -->
                <div class="bg-gradient-to-br from-pink-500 to-rose-600 rounded-2xl p-5 text-white">
                    <h3 class="font-bold mb-4 flex items-center gap-2">
                        <i class="fab fa-instagram"></i> Ikuti Kami
                    </h3>
                    <p class="text-pink-100 text-sm mb-4">Dapatkan info terbaru di sosial media kami</p>
                    <a href="https://www.instagram.com/libraryunidagontor" target="_blank" class="block text-center py-3 bg-white text-pink-600 rounded-xl font-bold text-sm hover:bg-pink-50 transition">
                        <i class="fab fa-instagram mr-2"></i>@libraryunidagontor
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>
