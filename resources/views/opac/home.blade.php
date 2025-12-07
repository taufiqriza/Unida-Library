<x-opac.layout title="Beranda">
    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 text-white py-8 lg:py-16 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32"></div>
        
        <div class="max-w-7xl mx-auto px-4 text-center relative z-10">
            <h1 class="text-2xl lg:text-4xl font-bold mb-2">Perpustakaan UNIDA Gontor</h1>
            <p class="text-blue-200 text-sm lg:text-base mb-4">Universitas Darussalam Gontor</p>
            
            <!-- Search Box -->
            <form action="{{ route('opac.catalog') }}" method="GET" class="max-w-2xl mx-auto">
                <div class="flex bg-white rounded-xl shadow-xl shadow-blue-900/20 overflow-hidden">
                    <input type="text" name="q" placeholder="Cari judul, pengarang, ISBN..." class="flex-1 px-4 py-3 text-gray-700 text-sm focus:outline-none">
                    <button type="submit" class="px-4 py-3 bg-blue-600 hover:bg-blue-700 transition">
                        <i class="fas fa-search text-white"></i>
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- Stats -->
    <section class="max-w-7xl mx-auto px-3 lg:px-4 -mt-10 lg:-mt-12 relative z-10">
        <div class="grid grid-cols-3 md:grid-cols-5 gap-2 lg:gap-4">
            <div class="bg-white rounded-lg lg:rounded-xl p-2 lg:p-4 shadow-lg shadow-gray-200/50 text-center">
                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-1 lg:mb-2">
                    <i class="fas fa-book text-blue-600 text-xs lg:text-base"></i>
                </div>
                <div class="text-lg lg:text-2xl font-bold text-gray-900">{{ number_format($stats['books']) }}</div>
                <div class="text-[10px] lg:text-xs text-gray-500">Judul</div>
            </div>
            <div class="bg-white rounded-lg lg:rounded-xl p-2 lg:p-4 shadow-lg shadow-gray-200/50 text-center">
                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-emerald-100 rounded-lg flex items-center justify-center mx-auto mb-1 lg:mb-2">
                    <i class="fas fa-copy text-emerald-600 text-xs lg:text-base"></i>
                </div>
                <div class="text-lg lg:text-2xl font-bold text-gray-900">{{ number_format($stats['items']) }}</div>
                <div class="text-[10px] lg:text-xs text-gray-500">Eksemplar</div>
            </div>
            <div class="bg-white rounded-lg lg:rounded-xl p-2 lg:p-4 shadow-lg shadow-gray-200/50 text-center">
                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-1 lg:mb-2">
                    <i class="fas fa-users text-purple-600 text-xs lg:text-base"></i>
                </div>
                <div class="text-lg lg:text-2xl font-bold text-gray-900">{{ number_format($stats['members']) }}</div>
                <div class="text-[10px] lg:text-xs text-gray-500">Anggota</div>
            </div>
            <div class="bg-white rounded-lg lg:rounded-xl p-2 lg:p-4 shadow-lg shadow-gray-200/50 text-center hidden md:block">
                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-orange-100 rounded-lg flex items-center justify-center mx-auto mb-1 lg:mb-2">
                    <i class="fas fa-file-pdf text-orange-600 text-xs lg:text-base"></i>
                </div>
                <div class="text-lg lg:text-2xl font-bold text-gray-900">{{ number_format($stats['ebooks']) }}</div>
                <div class="text-[10px] lg:text-xs text-gray-500">E-Book</div>
            </div>
            <div class="bg-white rounded-lg lg:rounded-xl p-2 lg:p-4 shadow-lg shadow-gray-200/50 text-center hidden md:block">
                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-pink-100 rounded-lg flex items-center justify-center mx-auto mb-1 lg:mb-2">
                    <i class="fas fa-graduation-cap text-pink-600 text-xs lg:text-base"></i>
                </div>
                <div class="text-lg lg:text-2xl font-bold text-gray-900">{{ number_format($stats['etheses']) }}</div>
                <div class="text-[10px] lg:text-xs text-gray-500">E-Thesis</div>
            </div>
        </div>
    </section>

    <!-- Quick Access Cards -->
    <section class="max-w-7xl mx-auto px-3 lg:px-4 py-4 lg:py-8">
        <div class="grid grid-cols-4 lg:grid-cols-4 gap-2 lg:gap-4">
            <a href="{{ route('opac.catalog') }}" class="bg-white rounded-lg lg:rounded-xl p-3 lg:p-6 shadow-lg shadow-gray-200/50 hover:shadow-xl hover:-translate-y-1 transition-all text-center lg:text-left">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg lg:rounded-xl flex items-center justify-center mx-auto lg:mx-0 mb-2 lg:mb-3 shadow-lg shadow-blue-500/30">
                    <i class="fas fa-search text-white text-sm lg:text-lg"></i>
                </div>
                <h3 class="font-bold text-gray-900 text-[10px] lg:text-base">Katalog</h3>
            </a>
            <a href="{{ route('opac.ebooks') }}" class="bg-white rounded-lg lg:rounded-xl p-3 lg:p-6 shadow-lg shadow-gray-200/50 hover:shadow-xl hover:-translate-y-1 transition-all text-center lg:text-left">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-lg lg:rounded-xl flex items-center justify-center mx-auto lg:mx-0 mb-2 lg:mb-3 shadow-lg shadow-orange-500/30">
                    <i class="fas fa-file-pdf text-white text-sm lg:text-lg"></i>
                </div>
                <h3 class="font-bold text-gray-900 text-[10px] lg:text-base">E-Book</h3>
            </a>
            <a href="{{ route('opac.etheses') }}" class="bg-white rounded-lg lg:rounded-xl p-3 lg:p-6 shadow-lg shadow-gray-200/50 hover:shadow-xl hover:-translate-y-1 transition-all text-center lg:text-left">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-pink-500 to-purple-600 rounded-lg lg:rounded-xl flex items-center justify-center mx-auto lg:mx-0 mb-2 lg:mb-3 shadow-lg shadow-pink-500/30">
                    <i class="fas fa-graduation-cap text-white text-sm lg:text-lg"></i>
                </div>
                <h3 class="font-bold text-gray-900 text-[10px] lg:text-base">E-Thesis</h3>
            </a>
            @auth('member')
            <a href="{{ route('opac.member.dashboard') }}" class="bg-white rounded-lg lg:rounded-xl p-3 lg:p-6 shadow-lg shadow-gray-200/50 hover:shadow-xl hover:-translate-y-1 transition-all text-center lg:text-left">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg lg:rounded-xl flex items-center justify-center mx-auto lg:mx-0 mb-2 lg:mb-3 shadow-lg shadow-emerald-500/30">
                    <i class="fas fa-user text-white text-sm lg:text-lg"></i>
                </div>
                <h3 class="font-bold text-gray-900 text-[10px] lg:text-base">Akun</h3>
            </a>
            @else
            <a href="{{ route('opac.login') }}" class="bg-white rounded-lg lg:rounded-xl p-3 lg:p-6 shadow-lg shadow-gray-200/50 hover:shadow-xl hover:-translate-y-1 transition-all text-center lg:text-left">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg lg:rounded-xl flex items-center justify-center mx-auto lg:mx-0 mb-2 lg:mb-3 shadow-lg shadow-emerald-500/30">
                    <i class="fas fa-sign-in-alt text-white text-sm lg:text-lg"></i>
                </div>
                <h3 class="font-bold text-gray-900 text-[10px] lg:text-base">Masuk</h3>
            </a>
            @endauth
        </div>
    </section>

    <!-- New Books -->
    @if($newBooks->count() > 0)
    <section class="max-w-7xl mx-auto px-4 py-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg lg:text-xl font-bold text-gray-900"><i class="fas fa-sparkles text-blue-500 mr-2"></i>Koleksi Terbaru</h2>
            <a href="{{ route('opac.catalog') }}?sort=latest" class="text-sm text-blue-600 hover:text-blue-700">Lihat Semua <i class="fas fa-arrow-right ml-1"></i></a>
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
                <a href="{{ route('opac.catalog') }}?sort=popular" class="text-sm text-blue-600 hover:text-blue-700">Lihat Semua <i class="fas fa-arrow-right ml-1"></i></a>
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
            <a href="{{ route('opac.news') }}" class="text-sm text-blue-600 hover:text-blue-700">Lihat Semua <i class="fas fa-arrow-right ml-1"></i></a>
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
