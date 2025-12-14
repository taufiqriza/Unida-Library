<div>
    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 text-white py-8 lg:py-16 relative overflow-hidden">
        <!-- Decorative elements -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full -ml-24 -mb-24"></div>
        
        <div class="max-w-7xl mx-auto px-4 text-center relative z-10">
            <h1 class="text-2xl lg:text-4xl font-bold mb-2">Perpustakaan UNIDA Gontor</h1>
            <p class="text-blue-200 text-sm lg:text-base mb-6">Universitas Darussalam Gontor</p>
            
            <!-- Search Box - Powerful Rounded Full Design -->
            <form id="searchForm" action="{{ route('opac.search') }}" method="GET" class="max-w-2xl mx-auto">
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
                            id="searchInput"
                            placeholder="Cari buku, e-book, tugas akhir, berita..." 
                            class="flex-1 px-2 py-4 lg:py-5 text-gray-700 text-sm lg:text-base focus:outline-none bg-transparent"
                            autocomplete="off"
                        >
                        <!-- Pill Switcher -->
                        <div class="m-1.5 flex items-center bg-gray-100 rounded-full p-1 gap-1">
                            <button type="submit" class="px-5 lg:px-6 py-2.5 lg:py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-full transition-all duration-300 shadow-lg shadow-blue-600/30 text-sm flex items-center gap-2">
                                <i class="fas fa-search"></i>
                                <span class="hidden sm:inline">Cari</span>
                            </button>
                            <button type="button" id="advancedBtn" onclick="openAdvancedSearch()" class="w-10 h-10 lg:w-11 lg:h-11 flex items-center justify-center text-gray-500 hover:text-blue-600 hover:bg-white rounded-full transition-all duration-300">
                                <i class="fas fa-sliders-h"></i>
                            </button>
                        </div>
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

            </script>
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
                    <i class="fas fa-newspaper text-purple-600 text-sm lg:text-lg"></i>
                </div>
                <div>
                    <div class="text-lg lg:text-2xl font-bold text-gray-900">{{ number_format($stats['journals']) }}</div>
                    <div class="text-[10px] lg:text-xs text-gray-500">Jurnal</div>
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

    <!-- Quick Actions: Unggah, Plagiasi, Member -->
    <section class="max-w-7xl mx-auto px-3 lg:px-4 py-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 lg:gap-4">
            <!-- Unggah Tugas Akhir -->
            <a href="{{ route('opac.panduan.thesis') }}" class="bg-gradient-to-r from-purple-500 to-indigo-600 rounded-xl p-4 text-white hover:shadow-lg hover:shadow-purple-200 transition group flex items-center gap-4">
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
            <a href="{{ route('opac.panduan.plagiarism') }}" class="bg-gradient-to-r from-teal-500 to-emerald-600 rounded-xl p-4 text-white hover:shadow-lg hover:shadow-teal-200 transition group flex items-center gap-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-shield-alt text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-sm lg:text-base">Cek Plagiasi</h3>
                    <p class="text-teal-200 text-[10px] lg:text-xs">Scan dokumen dengan iThenticate</p>
                </div>
                <i class="fas fa-chevron-right text-teal-200 group-hover:translate-x-1 transition"></i>
            </a>

            <!-- Panduan Member -->
            <a href="{{ route('opac.panduan.member') }}" class="bg-gradient-to-r from-blue-500 to-cyan-600 rounded-xl p-4 text-white hover:shadow-lg hover:shadow-blue-200 transition group flex items-center gap-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-user-circle text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-sm lg:text-base">Panduan Member</h3>
                    <p class="text-blue-200 text-[10px] lg:text-xs">Login & fitur perpustakaan</p>
                </div>
                <i class="fas fa-chevron-right text-blue-200 group-hover:translate-x-1 transition"></i>
            </a>
        </div>
    </section>

    <!-- E-Resources Section -->
    <section class="max-w-7xl mx-auto px-3 lg:px-4 py-4">
        <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
            <i class="fas fa-globe text-indigo-500"></i>
            Akses E-Resources
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 lg:gap-4">
            <!-- Database Jurnal Berlangganan -->
            <a href="{{ route('opac.database-access') }}" class="bg-gradient-to-br from-indigo-600 to-blue-700 rounded-xl p-4 lg:p-5 text-white hover:shadow-lg hover:shadow-indigo-200 transition group relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="relative flex items-start gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-database text-xl"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="font-bold text-base lg:text-lg">Database Jurnal</h3>
                            <span class="px-2 py-0.5 bg-amber-400 text-amber-900 text-[10px] font-bold rounded-full">KONSORSIUM</span>
                        </div>
                        <p class="text-indigo-200 text-xs mb-2">Gale Academic & ProQuest — 120K+ jurnal internasional</p>
                        <div class="flex items-center gap-1 text-indigo-300 text-xs">
                            <i class="fas fa-shield-alt"></i>
                            <span>FPPTI Jawa Timur</span>
                        </div>
                    </div>
                    <i class="fas fa-arrow-right text-indigo-300 group-hover:translate-x-1 transition self-center"></i>
                </div>
            </a>

            <!-- E-Book & Resources Eksternal -->
            <a href="{{ route('opac.page', 'journal-subscription') }}" class="bg-gradient-to-br from-emerald-600 to-teal-700 rounded-xl p-4 lg:p-5 text-white hover:shadow-lg hover:shadow-emerald-200 transition group relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="relative flex items-start gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-book-open text-xl"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="font-bold text-base lg:text-lg">E-Book Eksternal</h3>
                            <span class="px-2 py-0.5 bg-emerald-400 text-emerald-900 text-[10px] font-bold rounded-full">GRATIS</span>
                        </div>
                        <p class="text-emerald-200 text-xs mb-2">Shamela, Open Library, Internet Archive & lainnya</p>
                        <div class="flex items-center gap-1 text-emerald-300 text-xs">
                            <i class="fas fa-infinity"></i>
                            <span>100K+ sumber terbuka</span>
                        </div>
                    </div>
                    <i class="fas fa-arrow-right text-emerald-300 group-hover:translate-x-1 transition self-center"></i>
                </div>
            </a>
        </div>
    </section>

    <!-- New Books -->
    @if($newBooks->count() > 0)
    <section class="max-w-7xl mx-auto px-4 py-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg lg:text-xl font-bold text-gray-900"><i class="fas fa-sparkles text-blue-500 mr-2"></i>Koleksi Terbaru</h2>
            <a href="{{ route('opac.search') . '?type=book' }}?sort=latest" class="text-sm text-blue-600 hover:text-blue-700">Lihat Semua <i class="fas fa-arrow-right ml-1"></i></a>
        </div>
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-2 lg:gap-3">
            @foreach($newBooks as $index => $book)
            <a href="{{ route('opac.catalog.show', $book['id']) }}" class="group">
                <div class="aspect-[2/3] rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 group-hover:-translate-y-1 relative bg-gradient-to-br from-blue-50 to-slate-50">
                    @if($book['cover'])
                        <img src="{{ $book['cover'] }}" alt="{{ $book['title'] }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="fas fa-book text-2xl text-blue-200"></i>
                        </div>
                    @endif
                    <!-- New badge for first 3 -->
                    @if($index < 3)
                    <div class="absolute top-1.5 left-1.5 bg-gradient-to-r from-blue-500 to-indigo-500 text-white text-[7px] px-1.5 py-0.5 rounded-full font-semibold uppercase tracking-wide shadow">
                        <i class="fas fa-certificate text-[6px] mr-0.5"></i>Baru
                    </div>
                    @endif
                    <!-- Year badge -->
                    @if($book['publish_year'])
                    <div class="absolute top-1.5 right-1.5 bg-black/60 backdrop-blur-sm text-white text-[8px] px-1.5 py-0.5 rounded font-medium">
                        {{ $book['publish_year'] }}
                    </div>
                    @endif
                    <!-- Hover overlay with info -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-2">
                        <h3 class="text-white text-[10px] lg:text-xs font-medium line-clamp-2 leading-tight">{{ $book['title'] }}</h3>
                        <div class="flex items-center gap-1 mt-1">
                            <i class="fas fa-user text-[8px] text-blue-400"></i>
                            <p class="text-white/80 text-[8px] lg:text-[9px] truncate">{{ $book['authors'] }}</p>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Popular Books -->
    @if($popularBooks->count() > 0)
    <section class="bg-gradient-to-b from-slate-50 to-white py-8 lg:py-10">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg lg:text-xl font-bold text-gray-900"><i class="fas fa-star text-amber-500 mr-2"></i>Rekomendasi</h2>
                <a href="{{ route('opac.search') . '?type=book' }}" class="text-sm text-blue-600 hover:text-blue-700">Lihat Semua <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-2 lg:gap-3">
                @foreach($popularBooks as $index => $book)
                <a href="{{ route('opac.catalog.show', $book['id']) }}" class="group">
                    <div class="aspect-[2/3] rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 group-hover:-translate-y-1 relative bg-gradient-to-br from-amber-50 to-slate-50">
                        @if($book['cover'])
                            <img src="{{ $book['cover'] }}" alt="{{ $book['title'] }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-book text-2xl text-amber-200"></i>
                            </div>
                        @endif
                        <!-- Top badges -->
                        @if($index < 3)
                        <div class="absolute top-1.5 left-1.5 w-5 h-5 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full flex items-center justify-center shadow-lg">
                            <span class="text-white text-[9px] font-bold">{{ $index + 1 }}</span>
                        </div>
                        @endif
                        <!-- Year badge -->
                        @if($book['year'])
                        <div class="absolute top-1.5 right-1.5 bg-black/60 backdrop-blur-sm text-white text-[8px] px-1.5 py-0.5 rounded font-medium">
                            {{ $book['year'] }}
                        </div>
                        @endif
                        <!-- Hover overlay with info -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-2">
                            <h3 class="text-white text-[10px] lg:text-xs font-medium line-clamp-2 leading-tight">{{ $book['title'] }}</h3>
                            <div class="flex items-center gap-1 mt-1">
                                <i class="fas fa-user text-[8px] text-amber-400"></i>
                                <p class="text-white/80 text-[8px] lg:text-[9px] truncate">{{ $book['authors'] }}</p>
                            </div>
                            @if($book['publisher'])
                            <div class="flex items-center gap-1 mt-0.5">
                                <i class="fas fa-building text-[8px] text-blue-400"></i>
                                <p class="text-white/60 text-[8px] truncate">{{ $book['publisher'] }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- E-Thesis -->
    @if($latestEtheses->count() > 0)
    <section class="max-w-7xl mx-auto px-4 py-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg lg:text-xl font-bold text-gray-900"><i class="fas fa-graduation-cap text-purple-500 mr-2"></i>E-Thesis Terbaru</h2>
            <a href="{{ route('opac.search') }}?type=ethesis" class="text-sm text-purple-600 hover:text-purple-700">Lihat Semua <i class="fas fa-arrow-right ml-1"></i></a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-2 lg:gap-3">
            @foreach($latestEtheses->take(8) as $thesis)
            <a href="{{ route('opac.ethesis.show', $thesis->id) }}" class="group">
                <div class="aspect-[2/3] rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 group-hover:-translate-y-1 relative bg-gradient-to-br from-purple-100 to-indigo-100">
                    <div class="w-full h-full flex flex-col items-center justify-center p-3 text-center">
                        <i class="fas fa-graduation-cap text-3xl text-purple-300 mb-2"></i>
                        <span class="text-[9px] text-purple-400 uppercase tracking-wide font-medium">Thesis</span>
                    </div>
                    <!-- Hover overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-purple-900/90 via-purple-900/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-2">
                        <h3 class="text-white text-[10px] lg:text-xs font-medium line-clamp-3 leading-tight">{{ $thesis->title }}</h3>
                        <p class="text-white/70 text-[8px] mt-1 truncate">{{ $thesis->author ?? '-' }}</p>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Journals -->
    @if($latestJournals->count() > 0)
    <section class="bg-gradient-to-b from-slate-50 to-white py-6">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg lg:text-xl font-bold text-gray-900"><i class="fas fa-file-lines text-emerald-500 mr-2"></i>Artikel Jurnal Terbaru</h2>
                <a href="{{ route('opac.journals.index') }}" class="text-sm text-emerald-600 hover:text-emerald-700">Lihat Semua <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-2 lg:gap-3">
                @foreach($latestJournals->take(8) as $journal)
                <a href="{{ route('opac.journals.show', $journal->id) }}" class="group">
                    <div class="aspect-[2/3] rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 group-hover:-translate-y-1 relative bg-gradient-to-br from-emerald-100 to-teal-100">
                        <div class="w-full h-full flex flex-col items-center justify-center p-3 text-center">
                            <i class="fas fa-file-lines text-3xl text-emerald-300 mb-2"></i>
                            <span class="text-[9px] text-emerald-400 uppercase tracking-wide font-medium">Journal</span>
                        </div>
                        <!-- Hover overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-emerald-900/90 via-emerald-900/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-2">
                            <h3 class="text-white text-[10px] lg:text-xs font-medium line-clamp-3 leading-tight">{{ $journal->title }}</h3>
                            <p class="text-white/70 text-[8px] mt-1 truncate">{{ is_array($journal->authors) ? collect($journal->authors)->pluck('name')->first() : '-' }}</p>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- E-Books -->
    @if($latestEbooks->count() > 0)
    <section class="max-w-7xl mx-auto px-4 py-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg lg:text-xl font-bold text-gray-900"><i class="fas fa-file-pdf text-red-500 mr-2"></i>E-Book Terbaru</h2>
            <a href="{{ route('opac.search') }}?type=ebook" class="text-sm text-red-600 hover:text-red-700">Lihat Semua <i class="fas fa-arrow-right ml-1"></i></a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-2 lg:gap-3">
            @foreach($latestEbooks->take(8) as $ebook)
            <a href="{{ route('opac.ebook.show', $ebook->id) }}" class="group">
                <div class="aspect-[2/3] rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 group-hover:-translate-y-1 relative bg-gradient-to-br from-red-100 to-orange-100">
                    <div class="w-full h-full flex flex-col items-center justify-center p-3 text-center">
                        <i class="fas fa-file-pdf text-3xl text-red-300 mb-2"></i>
                        <span class="text-[9px] text-red-400 uppercase tracking-wide font-medium">E-Book</span>
                    </div>
                    <!-- Hover overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-red-900/90 via-red-900/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-2">
                        <h3 class="text-white text-[10px] lg:text-xs font-medium line-clamp-3 leading-tight">{{ $ebook->title }}</h3>
                        <p class="text-white/70 text-[8px] mt-1 truncate">{{ $ebook->author ?? '-' }}</p>
                    </div>
                </div>
            </a>
            @endforeach
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

    <!-- Advanced Search Modal Component -->
    <x-opac.advanced-search-modal />
</div>

