<x-opac.layout title="Beranda">
    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 text-white py-8 lg:py-16 relative overflow-hidden">
        <!-- Decorative elements -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full -ml-24 -mb-24"></div>
        
        <div class="max-w-7xl mx-auto px-4 text-center relative z-10">
            <h1 class="text-2xl lg:text-4xl font-bold mb-2">Perpustakaan UNIDA Gontor</h1>
            <p class="text-blue-200 text-sm lg:text-base mb-6">Universitas Darussalam Gontor</p>
            
            <!-- Search Box - Powerful Rounded Full Design -->
            <form action="{{ route('opac.search') }}" method="GET" class="max-w-2xl mx-auto">
                <div class="relative group">
                    <!-- Glow effect -->
                    <div class="absolute -inset-1 bg-gradient-to-r from-blue-400 via-white to-blue-400 rounded-full opacity-30 group-hover:opacity-50 blur-lg transition duration-500"></div>
                    
                    <!-- Search input container -->
                    <div class="relative flex items-center bg-white rounded-full shadow-2xl shadow-blue-900/30 overflow-hidden">
                        <div class="pl-5 pr-2">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input 
                            type="search" 
                            name="q" 
                            placeholder="Cari buku, e-book, tugas akhir, berita..." 
                            class="flex-1 px-2 py-4 lg:py-5 text-gray-700 text-sm lg:text-base focus:outline-none bg-transparent"
                            autocomplete="off"
                        >
                        <button type="submit" class="m-1.5 px-6 lg:px-8 py-2.5 lg:py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-full transition-all duration-300 shadow-lg shadow-blue-600/30 hover:shadow-blue-600/50 flex items-center gap-2">
                            <span class="hidden sm:inline">Cari</span>
                            <i class="fas fa-arrow-right text-sm"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Quick search tags -->
                <div class="mt-4 flex flex-wrap items-center justify-center gap-2">
                    <span class="text-blue-200 text-xs">Populer:</span>
                    <a href="{{ route('opac.search') }}?q=islam" class="px-3 py-1 bg-white/10 hover:bg-white/20 text-white text-xs rounded-full transition">Islam</a>
                    <a href="{{ route('opac.search') }}?q=ekonomi" class="px-3 py-1 bg-white/10 hover:bg-white/20 text-white text-xs rounded-full transition">Ekonomi</a>
                    <a href="{{ route('opac.search') }}?q=pendidikan" class="px-3 py-1 bg-white/10 hover:bg-white/20 text-white text-xs rounded-full transition">Pendidikan</a>
                    <a href="{{ route('opac.search') }}?q=hukum" class="px-3 py-1 bg-white/10 hover:bg-white/20 text-white text-xs rounded-full transition hidden sm:inline-block">Hukum</a>
                </div>
            </form>
        </div>
    </section>

    <!-- Stats -->
    <section class="max-w-7xl mx-auto px-3 lg:px-4 -mt-6 lg:-mt-10 relative z-10">
        <div class="grid grid-cols-3 md:grid-cols-5 gap-2 lg:gap-3">
            <div class="bg-white rounded-xl p-3 lg:p-4 shadow-lg flex items-center gap-3">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-book text-blue-600 text-sm lg:text-lg"></i>
                </div>
                <div>
                    <div class="text-lg lg:text-2xl font-bold text-gray-900">{{ number_format($stats['books']) }}</div>
                    <div class="text-[10px] lg:text-xs text-gray-500">Judul</div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-3 lg:p-4 shadow-lg flex items-center gap-3">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-copy text-emerald-600 text-sm lg:text-lg"></i>
                </div>
                <div>
                    <div class="text-lg lg:text-2xl font-bold text-gray-900">{{ number_format($stats['items']) }}</div>
                    <div class="text-[10px] lg:text-xs text-gray-500">Eksemplar</div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-3 lg:p-4 shadow-lg flex items-center gap-3">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-building text-purple-600 text-sm lg:text-lg"></i>
                </div>
                <div>
                    <div class="text-lg lg:text-2xl font-bold text-gray-900">{{ number_format($stats['branches']) }}</div>
                    <div class="text-[10px] lg:text-xs text-gray-500">Cabang</div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-3 lg:p-4 shadow-lg hidden md:flex items-center gap-3">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-file-pdf text-orange-600 text-sm lg:text-lg"></i>
                </div>
                <div>
                    <div class="text-lg lg:text-2xl font-bold text-gray-900">{{ number_format($stats['ebooks']) }}</div>
                    <div class="text-[10px] lg:text-xs text-gray-500">E-Book</div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-3 lg:p-4 shadow-lg hidden md:flex items-center gap-3">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-pink-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-graduation-cap text-pink-600 text-sm lg:text-lg"></i>
                </div>
                <div>
                    <div class="text-lg lg:text-2xl font-bold text-gray-900">{{ number_format($stats['etheses']) }}</div>
                    <div class="text-[10px] lg:text-xs text-gray-500">E-Thesis</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Actions: Unggah & Plagiasi -->
    <section class="max-w-7xl mx-auto px-3 lg:px-4 py-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 lg:gap-4">
            <!-- Unggah Tugas Akhir -->
            <a href="{{ route('opac.page', 'unggah-tugas-akhir') }}" class="bg-gradient-to-r from-purple-500 to-indigo-600 rounded-xl p-4 text-white hover:shadow-lg hover:shadow-purple-200 transition group flex items-center gap-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-cloud-upload-alt text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-sm lg:text-base">Unggah Tugas Akhir</h3>
                    <p class="text-purple-200 text-[10px] lg:text-xs">Upload skripsi, tesis & disertasi</p>
                </div>
                <i class="fas fa-chevron-right text-purple-200 group-hover:translate-x-1 transition"></i>
            </a>
            
            <!-- Cek Plagiasi -->
            <div class="bg-gradient-to-r from-gray-100 to-gray-200 rounded-xl p-4 flex items-center gap-4 relative">
                <div class="absolute top-2 right-2">
                    <span class="px-1.5 py-0.5 bg-amber-400 text-amber-900 text-[8px] font-bold rounded">SOON</span>
                </div>
                <div class="w-12 h-12 bg-gray-300 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-search-plus text-gray-500 text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-gray-500 text-sm lg:text-base">Cek Plagiasi</h3>
                    <p class="text-gray-400 text-[10px] lg:text-xs">Dalam pengembangan</p>
                </div>
                <i class="fas fa-chevron-right text-gray-300"></i>
            </div>
        </div>
    </section>

    <!-- New Books -->
    @if($newBooks->count() > 0)
    <section class="max-w-7xl mx-auto px-4 py-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg lg:text-xl font-bold text-gray-900"><i class="fas fa-sparkles text-blue-500 mr-2"></i>Koleksi Terbaru</h2>
            <a href="{{ route('opac.search') . '?type=book' }}?sort=latest" class="text-sm text-blue-600 hover:text-blue-700">Lihat Semua <i class="fas fa-arrow-right ml-1"></i></a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 lg:gap-4">
            @foreach($newBooks as $book)
            <a href="{{ route('opac.catalog.show', $book['id']) }}" class="bg-white rounded-xl overflow-hidden shadow-lg shadow-gray-200/50 hover:shadow-xl hover:-translate-y-1 transition group">
                <div class="aspect-[3/4] bg-gradient-to-br from-blue-100 to-blue-50 flex items-center justify-center">
                    @if($book['cover'])
                        <img src="{{ $book['cover'] }}" alt="{{ $book['title'] }}" class="w-full h-full object-cover">
                    @else
                        <i class="fas fa-book text-4xl text-blue-300"></i>
                    @endif
                </div>
                <div class="p-3">
                    <h3 class="font-semibold text-gray-900 text-sm line-clamp-2 group-hover:text-blue-600">{{ $book['title'] }}</h3>
                    <p class="text-xs text-gray-500 mt-1">{{ $book['authors'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $book['publish_year'] }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Popular Books -->
    @if($popularBooks->count() > 0)
    <section class="bg-white py-8 lg:py-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg lg:text-xl font-bold text-gray-900"><i class="fas fa-fire text-orange-500 mr-2"></i>Paling Diminati</h2>
                <a href="{{ route('opac.search') . '?type=book' }}?sort=popular" class="text-sm text-blue-600 hover:text-blue-700">Lihat Semua <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
                @foreach($popularBooks as $book)
                <a href="{{ route('opac.catalog.show', $book['id']) }}" class="flex gap-3 p-3 rounded-xl border border-gray-100 hover:border-blue-200 hover:bg-blue-50/50 transition group">
                    <div class="w-14 h-18 bg-gradient-to-br from-blue-100 to-blue-50 rounded-lg flex-shrink-0 flex items-center justify-center overflow-hidden">
                        @if($book['cover'])
                            <img src="{{ $book['cover'] }}" alt="{{ $book['title'] }}" class="w-full h-full object-cover">
                        @else
                            <i class="fas fa-book text-blue-300"></i>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 text-sm line-clamp-2 group-hover:text-blue-600">{{ $book['title'] }}</h3>
                        <p class="text-xs text-gray-500 mt-1">{{ $book['authors'] }}</p>
                        <p class="text-xs text-blue-600 mt-1"><i class="fas fa-chart-line mr-1"></i>{{ $book['loans_count'] }} peminjaman</p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- News -->
    @if(count($news) > 0)
    <section class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg lg:text-xl font-bold text-gray-900"><i class="fas fa-newspaper text-emerald-500 mr-2"></i>Berita & Pengumuman</h2>
            <a href="{{ route('opac.search') . '?type=news' }}" class="text-sm text-blue-600 hover:text-blue-700">Lihat Semua <i class="fas fa-arrow-right ml-1"></i></a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
            @foreach($news as $item)
            <a href="{{ route('opac.news.show', $item['slug']) }}" class="bg-white rounded-xl overflow-hidden shadow-lg shadow-gray-200/50 hover:shadow-xl transition group">
                <div class="aspect-video bg-gradient-to-br from-emerald-100 to-emerald-50 flex items-center justify-center">
                    @if($item['image'])
                        <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="w-full h-full object-cover">
                    @else
                        <i class="fas fa-newspaper text-3xl text-emerald-300"></i>
                    @endif
                </div>
                <div class="p-4">
                    <p class="text-xs text-gray-400 mb-1">{{ $item['published_at'] }}</p>
                    <h3 class="font-semibold text-gray-900 text-sm line-clamp-2 group-hover:text-blue-600">{{ $item['title'] }}</h3>
                </div>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Branches -->
    @if(count($branches) > 0)
    <section class="max-w-7xl mx-auto px-3 lg:px-4 py-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4"><i class="fas fa-map-marker-alt text-blue-500 mr-2"></i>Lokasi Perpustakaan</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($branches as $branch)
            <div class="bg-white rounded-lg p-3 shadow-lg shadow-gray-200/50 flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-building text-blue-600"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 text-sm">{{ $branch['name'] }}</h3>
                    @if($branch['address'])
                    <p class="text-xs text-gray-500">{{ $branch['address'] }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Contact Section -->
    <section class="bg-white py-8 lg:py-12 lg:hidden">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-lg font-bold text-gray-900 mb-4 text-center"><i class="fas fa-headset text-blue-500 mr-2"></i>Hubungi Kami</h2>
            <div class="grid grid-cols-1 gap-3">
                <a href="mailto:library@unida.gontor.ac.id" class="flex items-center gap-3 p-4 bg-blue-50 rounded-xl">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-envelope text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Email</p>
                        <p class="text-sm font-medium text-gray-900">library@unida.gontor.ac.id</p>
                    </div>
                </a>
                <a href="https://wa.me/6285183053934" target="_blank" class="flex items-center gap-3 p-4 bg-green-50 rounded-xl">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fab fa-whatsapp text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">WhatsApp</p>
                        <p class="text-sm font-medium text-gray-900">0851-8305-3934</p>
                    </div>
                </a>
                <a href="https://library.unida.gontor.ac.id" target="_blank" class="flex items-center gap-3 p-4 bg-purple-50 rounded-xl">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-globe text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Website</p>
                        <p class="text-sm font-medium text-gray-900">library.unida.gontor.ac.id</p>
                    </div>
                </a>
            </div>
        </div>
    </section>
</x-opac.layout>
