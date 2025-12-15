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
        </div>
    </section>

    <!-- Stats Bar - Individual Cards -->
    <section class="max-w-7xl mx-auto px-3 lg:px-4 -mt-6 lg:-mt-10 relative z-10">
        <div class="grid grid-cols-3 md:grid-cols-5 gap-2 lg:gap-3">
            <div class="bg-white rounded-xl p-3 lg:p-4 shadow-lg flex items-center gap-3">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-book text-blue-600 text-sm lg:text-lg"></i>
                </div>
                <div>
                    <div class="text-lg lg:text-2xl font-bold text-gray-900">{{ number_format($stats['books']) }}</div>
                    <div class="text-[10px] lg:text-xs text-gray-500">Judul Buku</div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-3 lg:p-4 shadow-lg flex items-center gap-3">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-graduation-cap text-purple-600 text-sm lg:text-lg"></i>
                </div>
                <div>
                    <div class="text-lg lg:text-2xl font-bold text-gray-900">{{ number_format($stats['etheses']) }}</div>
                    <div class="text-[10px] lg:text-xs text-gray-500">E-Thesis</div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-3 lg:p-4 shadow-lg flex items-center gap-3">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-book-quran text-emerald-600 text-sm lg:text-lg"></i>
                </div>
                <div>
                    <div class="text-lg lg:text-2xl font-bold text-gray-900">8,425</div>
                    <div class="text-[10px] lg:text-xs text-gray-500">Kitab Shamela</div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-3 lg:p-4 shadow-lg hidden md:flex items-center gap-3">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-newspaper text-red-600 text-sm lg:text-lg"></i>
                </div>
                <div>
                    <div class="text-lg lg:text-2xl font-bold text-gray-900">{{ number_format($stats['journals']) }}</div>
                    <div class="text-[10px] lg:text-xs text-gray-500">Jurnal UNIDA</div>
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
        </div>
    </section>

    <!-- Welcome & Services Section -->
    <section class="max-w-7xl mx-auto px-3 lg:px-4 py-8 lg:py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Welcome Message -->
            <div class="lg:col-span-2">
                <div class="bg-gradient-to-br from-slate-50 to-white rounded-2xl p-6 lg:p-8 border border-gray-100 h-full">
                    <div class="flex items-start gap-4 mb-6">
                        <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center shadow-lg border border-gray-100 flex-shrink-0 p-2">
                            <img src="{{ asset('storage/logo-portal.png') }}" alt="Logo" class="w-full h-full object-contain">
                        </div>
                        <div>
                            <h2 class="text-xl lg:text-2xl font-bold text-gray-900 mb-1">Selamat Datang di Perpustakaan UNIDA</h2>
                            <p class="text-gray-500 text-sm">Universitas Darussalam Gontor</p>
                        </div>
                    </div>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        Perpustakaan Universitas Darussalam Gontor hadir sebagai pusat sumber belajar dan penelitian yang modern. 
                        Kami menyediakan akses ke ribuan koleksi buku, jurnal ilmiah, e-book, dan sumber daya digital lainnya 
                        untuk mendukung kegiatan akademik sivitas akademika UNIDA Gontor.
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('opac.page', 'visi-misi') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-100 text-blue-700 rounded-lg text-sm font-medium hover:bg-blue-200 transition">
                            <i class="fas fa-bullseye"></i> Visi & Misi
                        </a>
                        <a href="{{ route('opac.page', 'jam-layanan') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-100 text-emerald-700 rounded-lg text-sm font-medium hover:bg-emerald-200 transition">
                            <i class="fas fa-clock"></i> Jam Layanan
                        </a>
                        <a href="{{ route('opac.page', 'fasilitas') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-100 text-purple-700 rounded-lg text-sm font-medium hover:bg-purple-200 transition">
                            <i class="fas fa-building"></i> Fasilitas
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Quick Services -->
            <div class="space-y-3">
                <h3 class="font-bold text-gray-900 flex items-center gap-2 mb-4">
                    <i class="fas fa-bolt text-amber-500"></i> Layanan Cepat
                </h3>
                <a href="{{ route('opac.panduan.thesis') }}" class="block bg-gradient-to-r from-purple-500 to-indigo-600 rounded-xl p-4 text-white hover:shadow-lg hover:shadow-purple-200 transition group">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-cloud-upload-alt text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-sm">Unggah Tugas Akhir</h4>
                            <p class="text-purple-200 text-xs">Upload skripsi, tesis & disertasi</p>
                        </div>
                        <i class="fas fa-chevron-right text-purple-200 group-hover:translate-x-1 transition"></i>
                    </div>
                </a>
                <a href="{{ route('opac.panduan.plagiarism') }}" class="block bg-gradient-to-r from-teal-500 to-emerald-600 rounded-xl p-4 text-white hover:shadow-lg hover:shadow-teal-200 transition group">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-shield-alt text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-sm">Cek Plagiasi</h4>
                            <p class="text-teal-200 text-xs">Scan dengan iThenticate</p>
                        </div>
                        <i class="fas fa-chevron-right text-teal-200 group-hover:translate-x-1 transition"></i>
                    </div>
                </a>
                <a href="{{ route('opac.panduan.member') }}" class="block bg-gradient-to-r from-blue-500 to-cyan-600 rounded-xl p-4 text-white hover:shadow-lg hover:shadow-blue-200 transition group">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-id-card text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-sm">Panduan Member</h4>
                            <p class="text-blue-200 text-xs">Login & fitur perpustakaan</p>
                        </div>
                        <i class="fas fa-chevron-right text-blue-200 group-hover:translate-x-1 transition"></i>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- Premium Digital Collections -->
    <section class="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 py-10 lg:py-14 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -mr-48 -mt-48"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/5 rounded-full -ml-32 -mb-32"></div>
        <div class="max-w-7xl mx-auto px-3 lg:px-4 relative">
            <div class="text-center mb-8">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 rounded-full text-white text-sm font-medium mb-4">
                    <i class="fas fa-crown text-amber-300"></i> Koleksi Premium
                </div>
                <h2 class="text-2xl lg:text-3xl font-bold text-white mb-2">Koleksi Digital Eksklusif</h2>
                <p class="text-blue-200">Akses perpustakaan digital klasik dan warisan sejarah</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
                <!-- Maktabah Shamela -->
                <a href="{{ route('opac.shamela.index') }}" class="group relative bg-gradient-to-br from-emerald-600 to-teal-700 rounded-2xl p-6 overflow-hidden hover:shadow-2xl hover:shadow-emerald-500/30 transition-all hover:-translate-y-1">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                    <div class="relative">
                        <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition">
                            <i class="fas fa-book-quran text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Maktabah Shamela</h3>
                        <p class="text-emerald-200 text-sm mb-4">Perpustakaan digital kitab-kitab Islam klasik lengkap</p>
                        <div class="flex items-center justify-between">
                            <span class="px-3 py-1 bg-white/20 rounded-full text-white text-xs font-medium">8,425 Kitab</span>
                            <i class="fas fa-arrow-right text-white/70 group-hover:translate-x-2 transition"></i>
                        </div>
                    </div>
                </a>
                
                <!-- Universitaria -->
                <a href="{{ route('opac.universitaria.index') }}" class="group relative bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl p-6 overflow-hidden hover:shadow-2xl hover:shadow-amber-500/30 transition-all hover:-translate-y-1">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                    <div class="absolute top-3 right-3">
                        <span class="px-2 py-1 bg-white/25 rounded-full text-[10px] font-bold text-white"><i class="fas fa-crown mr-1"></i>PREMIUM</span>
                    </div>
                    <div class="relative">
                        <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition">
                            <i class="fas fa-landmark text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Universitaria</h3>
                        <p class="text-amber-100 text-sm mb-4">Koleksi warisan sejarah Pondok Modern Gontor</p>
                        <div class="flex items-center justify-between">
                            <span class="px-3 py-1 bg-white/20 rounded-full text-white text-xs font-medium">Heritage</span>
                            <i class="fas fa-arrow-right text-white/70 group-hover:translate-x-2 transition"></i>
                        </div>
                    </div>
                </a>
                
                <!-- Database Jurnal -->
                <a href="{{ route('opac.database-access') }}" class="group relative bg-gradient-to-br from-indigo-600 to-blue-700 rounded-2xl p-6 overflow-hidden hover:shadow-2xl hover:shadow-indigo-500/30 transition-all hover:-translate-y-1">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                    <div class="relative">
                        <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition">
                            <i class="fas fa-database text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Database Jurnal</h3>
                        <p class="text-indigo-200 text-sm mb-4">Akses Gale Academic & ProQuest via FPPTI</p>
                        <div class="flex items-center justify-between">
                            <span class="px-3 py-1 bg-white/20 rounded-full text-white text-xs font-medium">120K+ Jurnal</span>
                            <i class="fas fa-arrow-right text-white/70 group-hover:translate-x-2 transition"></i>
                        </div>
                    </div>
                </a>
                
                <!-- E-Resources -->
                <a href="{{ route('opac.page', 'e-resources') }}" class="group relative bg-gradient-to-br from-violet-600 to-purple-700 rounded-2xl p-6 overflow-hidden hover:shadow-2xl hover:shadow-violet-500/30 transition-all hover:-translate-y-1">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                    <div class="relative">
                        <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition">
                            <i class="fas fa-globe text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">E-Resources</h3>
                        <p class="text-violet-200 text-sm mb-4">Open Library, iPusnas, PDF Drive & lainnya</p>
                        <div class="flex items-center justify-between">
                            <span class="px-3 py-1 bg-white/20 rounded-full text-white text-xs font-medium">Akses Gratis</span>
                            <i class="fas fa-arrow-right text-white/70 group-hover:translate-x-2 transition"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- Discovery Section with Tabs -->
    <section class="max-w-7xl mx-auto px-4 py-10 lg:py-14" x-data="{ activeTab: 'books' }">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xl lg:text-2xl font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-sparkles text-blue-500"></i> Jelajahi Koleksi
                </h2>
                <p class="text-gray-500 text-sm mt-1">Temukan koleksi terbaru perpustakaan</p>
            </div>
            
            <!-- Tab Navigation -->
            <div class="flex items-center gap-1 bg-gray-100 rounded-xl p-1 overflow-x-auto">
                <button @click="activeTab = 'books'" :class="activeTab === 'books' ? 'bg-white shadow text-blue-600' : 'text-gray-600 hover:text-gray-900'" class="px-4 py-2 rounded-lg text-sm font-medium transition whitespace-nowrap">
                    <i class="fas fa-book mr-1.5"></i>Buku
                </button>
                <button @click="activeTab = 'ethesis'" :class="activeTab === 'ethesis' ? 'bg-white shadow text-purple-600' : 'text-gray-600 hover:text-gray-900'" class="px-4 py-2 rounded-lg text-sm font-medium transition whitespace-nowrap">
                    <i class="fas fa-graduation-cap mr-1.5"></i>E-Thesis
                </button>
                <button @click="activeTab = 'ebooks'" :class="activeTab === 'ebooks' ? 'bg-white shadow text-orange-600' : 'text-gray-600 hover:text-gray-900'" class="px-4 py-2 rounded-lg text-sm font-medium transition whitespace-nowrap">
                    <i class="fas fa-file-pdf mr-1.5"></i>E-Book
                </button>
                <button @click="activeTab = 'journals'" :class="activeTab === 'journals' ? 'bg-white shadow text-emerald-600' : 'text-gray-600 hover:text-gray-900'" class="px-4 py-2 rounded-lg text-sm font-medium transition whitespace-nowrap">
                    <i class="fas fa-newspaper mr-1.5"></i>Jurnal
                </button>
            </div>
        </div>

        <!-- Books Tab -->
        <div x-show="activeTab === 'books'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
            @if($newBooks->count() > 0)
            <div class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-6 lg:grid-cols-8 gap-2 lg:gap-3">
                @foreach($newBooks->take(8) as $index => $book)
                <a href="{{ route('opac.catalog.show', $book['id']) }}" class="group">
                    <div class="aspect-[2/3] rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 group-hover:-translate-y-1 relative bg-gradient-to-br from-blue-50 to-slate-50">
                        @if($book['cover'])
                            <img src="{{ $book['cover'] }}" alt="{{ $book['title'] }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-book text-2xl text-blue-200"></i>
                            </div>
                        @endif
                        @if($index < 3)
                        <div class="absolute top-1.5 left-1.5 bg-gradient-to-r from-blue-500 to-indigo-500 text-white text-[7px] px-1.5 py-0.5 rounded-full font-semibold uppercase tracking-wide shadow">
                            <i class="fas fa-certificate text-[6px] mr-0.5"></i>Baru
                        </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-2">
                            <h3 class="text-white text-[10px] lg:text-xs font-medium line-clamp-2 leading-tight">{{ $book['title'] }}</h3>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            <div class="text-center mt-6">
                <a href="{{ route('opac.search') }}?type=book&sort=latest" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-medium transition">
                    Lihat Semua Buku <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            @else
            <div class="text-center py-12 text-gray-400">
                <i class="fas fa-book text-4xl mb-3"></i>
                <p>Belum ada buku terbaru</p>
            </div>
            @endif
        </div>

        <!-- E-Thesis Tab -->
        <div x-show="activeTab === 'ethesis'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
            @if($latestEtheses->count() > 0)
            <div class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-6 lg:grid-cols-8 gap-2 lg:gap-3">
                @foreach($latestEtheses->take(8) as $thesis)
                <a href="{{ route('opac.ethesis.show', $thesis->id) }}" class="group">
                    <div class="aspect-[2/3] rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 group-hover:-translate-y-1 relative bg-gradient-to-br from-purple-100 to-indigo-100">
                        <div class="w-full h-full flex flex-col items-center justify-center p-3 text-center">
                            <i class="fas fa-graduation-cap text-3xl text-purple-300 mb-2"></i>
                            <span class="text-[9px] text-purple-400 uppercase tracking-wide font-medium">Thesis</span>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-purple-900/90 via-purple-900/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-2">
                            <h3 class="text-white text-[10px] lg:text-xs font-medium line-clamp-3 leading-tight">{{ $thesis->title }}</h3>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            <div class="text-center mt-6">
                <a href="{{ route('opac.search') }}?type=ethesis" class="inline-flex items-center gap-2 px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-sm font-medium transition">
                    Lihat Semua E-Thesis <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            @else
            <div class="text-center py-12 text-gray-400">
                <i class="fas fa-graduation-cap text-4xl mb-3"></i>
                <p>Belum ada e-thesis terbaru</p>
            </div>
            @endif
        </div>

        <!-- E-Books Tab -->
        <div x-show="activeTab === 'ebooks'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
            @if($latestEbooks->count() > 0)
            <div class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-6 lg:grid-cols-8 gap-2 lg:gap-3">
                @foreach($latestEbooks->take(8) as $ebook)
                <a href="{{ route('opac.ebook.show', $ebook->id) }}" class="group">
                    <div class="aspect-[2/3] rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 group-hover:-translate-y-1 relative bg-gradient-to-br from-orange-100 to-red-100">
                        <div class="w-full h-full flex flex-col items-center justify-center p-3 text-center">
                            <i class="fas fa-file-pdf text-3xl text-orange-300 mb-2"></i>
                            <span class="text-[9px] text-orange-400 uppercase tracking-wide font-medium">E-Book</span>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-orange-900/90 via-orange-900/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-2">
                            <h3 class="text-white text-[10px] lg:text-xs font-medium line-clamp-3 leading-tight">{{ $ebook->title }}</h3>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            <div class="text-center mt-6">
                <a href="{{ route('opac.search') }}?type=ebook" class="inline-flex items-center gap-2 px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-sm font-medium transition">
                    Lihat Semua E-Book <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            @else
            <div class="text-center py-12 text-gray-400">
                <i class="fas fa-file-pdf text-4xl mb-3"></i>
                <p>Belum ada e-book terbaru</p>
            </div>
            @endif
        </div>

        <!-- Journals Tab -->
        <div x-show="activeTab === 'journals'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
            @if($latestJournals->count() > 0)
            <div class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-6 lg:grid-cols-8 gap-2 lg:gap-3">
                @foreach($latestJournals->take(8) as $journal)
                <a href="{{ route('opac.journals.show', $journal->id) }}" class="group">
                    <div class="aspect-[2/3] rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 group-hover:-translate-y-1 relative bg-gradient-to-br from-emerald-100 to-teal-100">
                        <div class="w-full h-full flex flex-col items-center justify-center p-3 text-center">
                            <i class="fas fa-file-lines text-3xl text-emerald-300 mb-2"></i>
                            <span class="text-[9px] text-emerald-400 uppercase tracking-wide font-medium">Journal</span>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-emerald-900/90 via-emerald-900/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-2">
                            <h3 class="text-white text-[10px] lg:text-xs font-medium line-clamp-3 leading-tight">{{ $journal->title }}</h3>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            <div class="text-center mt-6">
                <a href="{{ route('opac.journals.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-medium transition">
                    Lihat Semua Jurnal <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            @else
            <div class="text-center py-12 text-gray-400">
                <i class="fas fa-newspaper text-4xl mb-3"></i>
                <p>Belum ada jurnal terbaru</p>
            </div>
            @endif
        </div>
    </section>

    <!-- Recommended Books -->
    @if($popularBooks->count() > 0)
    <section class="bg-gradient-to-b from-amber-50 to-white py-8 lg:py-10">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg lg:text-xl font-bold text-gray-900"><i class="fas fa-star text-amber-500 mr-2"></i>Rekomendasi untuk Anda</h2>
                <a href="{{ route('opac.search') . '?type=book' }}" class="text-sm text-amber-600 hover:text-amber-700">Lihat Semua <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
            <div class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-6 lg:grid-cols-8 gap-2 lg:gap-3">
                @foreach($popularBooks->take(8) as $index => $book)
                <a href="{{ route('opac.catalog.show', $book['id']) }}" class="group">
                    <div class="aspect-[2/3] rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 group-hover:-translate-y-1 relative bg-gradient-to-br from-amber-50 to-slate-50">
                        @if($book['cover'])
                            <img src="{{ $book['cover'] }}" alt="{{ $book['title'] }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-book text-2xl text-amber-200"></i>
                            </div>
                        @endif
                        @if($index < 3)
                        <div class="absolute top-1.5 left-1.5 w-5 h-5 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full flex items-center justify-center shadow-lg">
                            <span class="text-white text-[9px] font-bold">{{ $index + 1 }}</span>
                        </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-2">
                            <h3 class="text-white text-[10px] lg:text-xs font-medium line-clamp-2 leading-tight">{{ $book['title'] }}</h3>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- News & Events -->
    @if(count($news) > 0)
    <section class="max-w-7xl mx-auto px-4 py-8 lg:py-10">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-newspaper text-blue-500"></i> Berita & Pengumuman
            </h2>
            <a href="{{ route('opac.news.index') }}" class="text-sm text-blue-600 hover:text-blue-700">Lihat Semua <i class="fas fa-arrow-right ml-1"></i></a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($news as $item)
            <a href="{{ route('opac.news.show', $item['slug']) }}" class="bg-white rounded-xl overflow-hidden shadow-lg shadow-gray-200/50 hover:shadow-xl transition group">
                <div class="aspect-video bg-gradient-to-br from-blue-100 to-blue-50 flex items-center justify-center overflow-hidden">
                    @if($item['image'])
                        <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    @else
                        <i class="fas fa-newspaper text-3xl text-blue-300"></i>
                    @endif
                </div>
                <div class="p-4">
                    <p class="text-xs text-gray-400 mb-1">{{ $item['published_at'] }}</p>
                    <h3 class="font-semibold text-gray-900 text-sm line-clamp-2 group-hover:text-blue-600 transition">{{ $item['title'] }}</h3>
                </div>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Library Info Footer -->
    <section class="bg-slate-100 py-6 lg:py-8">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <!-- Branches - Compact Inline -->
                @if(count($branches) > 0)
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fas fa-map-marker-alt text-blue-500"></i>
                        <span class="font-semibold text-gray-900 text-sm">Lokasi:</span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($branches as $branch)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white rounded-full text-xs text-gray-700 shadow-sm">
                            <i class="fas fa-building text-blue-400 text-[10px]"></i>
                            {{ $branch['name'] }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <!-- Contact - Compact Inline -->
                <div class="flex flex-wrap items-center gap-3 lg:gap-4">
                    <a href="mailto:library@unida.gontor.ac.id" class="inline-flex items-center gap-2 px-3 py-2 bg-white rounded-lg text-sm text-gray-700 hover:shadow-md transition">
                        <i class="fas fa-envelope text-blue-500"></i>
                        <span class="hidden sm:inline">library@unida.gontor.ac.id</span>
                        <span class="sm:hidden">Email</span>
                    </a>
                    <a href="https://wa.me/6285183053934" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 bg-white rounded-lg text-sm text-gray-700 hover:shadow-md transition">
                        <i class="fab fa-whatsapp text-green-500"></i>
                        <span>0851-8305-3934</span>
                    </a>
                    <a href="https://www.instagram.com/libraryunidagontor" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 bg-white rounded-lg text-sm text-gray-700 hover:shadow-md transition">
                        <i class="fab fa-instagram text-pink-500"></i>
                        <span class="hidden sm:inline">@libraryunidagontor</span>
                        <span class="sm:hidden">IG</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Advanced Search Modal Component -->
    <x-opac.advanced-search-modal />
</div>
