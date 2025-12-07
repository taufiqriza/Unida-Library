<x-opac.layout title="Beranda">
    <!-- Hero Section -->
    <section class="gradient-blue py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center text-white">
                <h1 class="text-3xl md:text-5xl font-bold mb-4">Selamat Datang di Perpustakaan</h1>
                <p class="text-blue-100 text-lg mb-8 max-w-2xl mx-auto">Temukan ribuan koleksi buku, e-book, dan karya ilmiah untuk mendukung pembelajaran Anda</p>
                
                <!-- Search Box -->
                <form action="{{ route('opac.catalog') }}" method="GET" class="max-w-2xl mx-auto">
                    <div class="flex bg-white rounded-xl shadow-xl shadow-blue-900/20 overflow-hidden">
                        <input type="text" name="q" placeholder="Cari judul, pengarang, atau ISBN..." class="flex-1 px-6 py-4 text-gray-700 focus:outline-none">
                        <button type="submit" class="px-6 py-4 bg-blue-600 hover:bg-blue-700 transition">
                            <i class="fas fa-search text-white"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="max-w-7xl mx-auto px-4 -mt-8">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="bg-white rounded-xl p-4 shadow-lg shadow-gray-200/50 text-center">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-book text-blue-600"></i>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['books']) }}</div>
                <div class="text-xs text-gray-500">Judul Buku</div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-lg shadow-gray-200/50 text-center">
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-copy text-emerald-600"></i>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['items']) }}</div>
                <div class="text-xs text-gray-500">Eksemplar</div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-lg shadow-gray-200/50 text-center">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-users text-purple-600"></i>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['members']) }}</div>
                <div class="text-xs text-gray-500">Anggota</div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-lg shadow-gray-200/50 text-center">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-file-pdf text-orange-600"></i>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['ebooks']) }}</div>
                <div class="text-xs text-gray-500">E-Book</div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-lg shadow-gray-200/50 text-center col-span-2 md:col-span-1">
                <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-graduation-cap text-pink-600"></i>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['etheses']) }}</div>
                <div class="text-xs text-gray-500">E-Thesis</div>
            </div>
        </div>
    </section>

    <!-- New Books -->
    <section class="max-w-7xl mx-auto px-4 py-12">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900"><i class="fas fa-sparkles text-blue-500 mr-2"></i>Koleksi Terbaru</h2>
            <a href="{{ route('opac.catalog') }}?sort=latest" class="text-sm text-blue-600 hover:text-blue-700">Lihat Semua <i class="fas fa-arrow-right ml-1"></i></a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
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

    <!-- Popular Books -->
    <section class="bg-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900"><i class="fas fa-fire text-orange-500 mr-2"></i>Paling Diminati</h2>
                <a href="{{ route('opac.catalog') }}?sort=popular" class="text-sm text-blue-600 hover:text-blue-700">Lihat Semua <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($popularBooks as $book)
                <a href="{{ route('opac.catalog.show', $book['id']) }}" class="flex gap-3 p-3 rounded-xl border border-gray-100 hover:border-blue-200 hover:bg-blue-50/50 transition group">
                    <div class="w-16 h-20 bg-gradient-to-br from-blue-100 to-blue-50 rounded-lg flex-shrink-0 flex items-center justify-center overflow-hidden">
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

    <!-- News -->
    @if(count($news) > 0)
    <section class="max-w-7xl mx-auto px-4 py-12">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900"><i class="fas fa-newspaper text-emerald-500 mr-2"></i>Berita & Pengumuman</h2>
            <a href="{{ route('opac.news') }}" class="text-sm text-blue-600 hover:text-blue-700">Lihat Semua <i class="fas fa-arrow-right ml-1"></i></a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
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
                    <p class="text-xs text-gray-500 mt-2 line-clamp-2">{{ $item['excerpt'] }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Branches -->
    @if(count($branches) > 0)
    <section class="bg-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-xl font-bold text-gray-900 mb-6 text-center"><i class="fas fa-building text-blue-500 mr-2"></i>Lokasi Perpustakaan</h2>
            <div class="grid grid-cols-1 md:grid-cols-{{ min(count($branches), 3) }} gap-4">
                @foreach($branches as $branch)
                <div class="p-4 rounded-xl border border-gray-100 hover:border-blue-200 transition">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-map-marker-alt text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $branch['name'] }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ $branch['address'] }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif
</x-opac.layout>
