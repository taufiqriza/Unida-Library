<div>
    <div class="lg:max-w-7xl lg:mx-auto lg:px-4 lg:py-8">
        
        {{-- Loading State --}}
        @if($loading)
        <div class="flex items-center justify-center py-32">
            <div class="text-center">
                <div class="w-16 h-16 border-4 border-cyan-500 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
                <p class="text-gray-500">Memuat data dari Open Library...</p>
            </div>
        </div>
        @elseif($error)
        {{-- Error State --}}
        <div class="max-w-2xl mx-auto px-4 py-16 text-center">
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-exclamation-triangle text-3xl text-red-500"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">Gagal Memuat Data</h2>
            <p class="text-gray-500 mb-6">{{ $error }}</p>
            <a href="{{ route('opac.search') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-600 text-white rounded-xl font-medium hover:bg-primary-700 transition">
                <i class="fas fa-arrow-left"></i> Kembali ke Pencarian
            </a>
        </div>
        @elseif($book)
        {{-- Hero Section with Cover --}}
        <div class="relative bg-gradient-to-b from-cyan-600 via-cyan-700 to-teal-800 lg:rounded-2xl lg:overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                @if($book['cover_url'] ?? null)
                    <img src="{{ $book['cover_url'] }}" class="w-full h-full object-cover opacity-20 blur-2xl scale-110">
                @endif
            </div>
            
            {{-- Back Button --}}
            <div class="relative z-10 px-4 pt-4 lg:px-6 lg:pt-6 flex items-center justify-between">
                <button onclick="window.history.back()" class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 hover:bg-white/30 text-white text-sm rounded-xl transition backdrop-blur-sm">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </button>
                
                {{-- Breadcrumb (Desktop) --}}
                <nav class="hidden lg:flex items-center gap-2 text-sm text-white/70">
                    <a href="{{ route('opac.home') }}" class="hover:text-white">Beranda</a>
                    <span>/</span>
                    <a href="{{ route('opac.search') }}?type=external" class="hover:text-white">Open Library</a>
                    <span>/</span>
                    <span class="text-white truncate max-w-[200px]">{{ Str::limit($book['title'], 30) }}</span>
                </nav>
            </div>
            
            {{-- Cover & Basic Info --}}
            <div class="relative z-10 px-4 pb-6 pt-4 lg:p-8">
                <div class="flex flex-col items-center lg:flex-row lg:items-end gap-4 lg:gap-8">
                    <div class="w-40 lg:w-52 flex-shrink-0">
                        <div class="aspect-[3/4] bg-white rounded-xl shadow-2xl overflow-hidden ring-4 ring-white/20">
                            @if($book['cover_url'] ?? null)
                                <img 
                                    src="{{ $book['cover_url'] }}" 
                                    alt="{{ $book['title'] }}" 
                                    class="w-full h-full object-cover"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                >
                                <div class="hidden w-full h-full bg-gradient-to-br from-cyan-100 to-cyan-50 items-center justify-center">
                                    <i class="fas fa-globe text-5xl text-cyan-300"></i>
                                </div>
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-cyan-100 to-cyan-50 flex items-center justify-center">
                                    <i class="fas fa-globe text-5xl text-cyan-300"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="text-center lg:text-left flex-1">
                        <span class="inline-block px-3 py-1 bg-white/20 text-white text-xs font-semibold rounded-full mb-2">
                            <i class="fas fa-globe mr-1"></i> Open Library
                        </span>
                        <h1 class="text-xl lg:text-3xl font-bold text-white leading-tight">{{ $book['title'] }}</h1>
                        <p class="text-cyan-200 mt-2 text-sm lg:text-base">
                            {{ is_array($book['authors'] ?? null) ? implode(', ', $book['authors']) : ($book['authors'] ?? 'Penulis tidak diketahui') }}
                        </p>
                        
                        <div class="flex items-center justify-center lg:justify-start gap-4 mt-4">
                            @if($book['first_publish_year'] ?? $book['first_publish_date'] ?? $book['publish_date'] ?? null)
                            <div class="text-center">
                                <div class="text-2xl font-bold text-white">{{ $book['first_publish_year'] ?? $book['first_publish_date'] ?? $book['publish_date'] }}</div>
                                <div class="text-xs text-cyan-200">Tahun</div>
                            </div>
                            <div class="w-px h-10 bg-white/20"></div>
                            @endif
                            @if($this->borrowable)
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-400"><i class="fas fa-check"></i></div>
                                <div class="text-xs text-cyan-200">Tersedia</div>
                            </div>
                            @else
                            <div class="text-center">
                                <div class="text-2xl font-bold text-yellow-400"><i class="fas fa-info"></i></div>
                                <div class="text-xs text-cyan-200">Metadata</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Content Cards --}}
        <div class="px-4 lg:px-0 mt-6 relative z-20 space-y-4 pb-8">
            
            {{-- Access Section --}}
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-4 py-3 bg-gradient-to-r from-cyan-50 to-white border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-book-open text-cyan-500"></i>
                        Akses Buku
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    {{-- Read Online --}}
                    @if($this->borrowable)
                    <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-book-reader text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Baca Online</p>
                                <p class="text-xs text-green-600 flex items-center gap-1">
                                    <i class="fas fa-unlock"></i> Tersedia di Internet Archive
                                </p>
                            </div>
                        </div>
                        <a href="{{ $this->readUrl }}" target="_blank" rel="noopener" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                            <i class="fas fa-external-link-alt"></i>
                            <span class="hidden sm:inline">Baca</span>
                        </a>
                    </div>
                    @endif

                    {{-- Open Library Link --}}
                    <div class="flex items-center justify-between p-4 bg-cyan-50 border border-cyan-200 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-cyan-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-globe text-cyan-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Open Library</p>
                                <p class="text-xs text-cyan-600 flex items-center gap-1">
                                    <i class="fas fa-link"></i> Lihat di situs asli
                                </p>
                            </div>
                        </div>
                        <a href="{{ $book['url'] ?? '#' }}" target="_blank" rel="noopener" class="px-4 py-2 bg-cyan-600 text-white text-sm font-medium rounded-lg hover:bg-cyan-700 transition flex items-center gap-2">
                            <i class="fas fa-external-link-alt"></i>
                            <span class="hidden sm:inline">Kunjungi</span>
                        </a>
                    </div>

                    {{-- Info Box --}}
                    <div class="mt-2 p-3 bg-amber-50 border border-amber-200 rounded-xl">
                        <p class="text-xs text-amber-700 flex items-start gap-2">
                            <i class="fas fa-info-circle mt-0.5"></i>
                            <span>Buku ini berasal dari Open Library, perpustakaan digital gratis dengan 4+ juta buku. Beberapa buku dapat dibaca secara online melalui Internet Archive.</span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Detail Info - Side by Side Layout --}}
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-info-circle text-cyan-500"></i>
                        Informasi Buku
                    </h3>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        {{-- Penulis --}}
                        @if(!empty($book['authors']))
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-user text-cyan-600 text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-gray-500 uppercase tracking-wide">Penulis</p>
                                <p class="text-sm font-semibold text-gray-900 truncate">
                                    {{ is_array($book['authors']) ? implode(', ', array_slice($book['authors'], 0, 3)) : $book['authors'] }}
                                </p>
                            </div>
                        </div>
                        @endif
                        
                        {{-- Tahun --}}
                        @if($book['first_publish_year'] ?? $book['first_publish_date'] ?? $book['publish_date'] ?? null)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-calendar text-amber-600 text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-gray-500 uppercase tracking-wide">Tahun Terbit</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $book['first_publish_year'] ?? $book['first_publish_date'] ?? $book['publish_date'] }}</p>
                            </div>
                        </div>
                        @endif
                        
                        {{-- Penerbit --}}
                        @if(!empty($book['publishers']))
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-building text-purple-600 text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-gray-500 uppercase tracking-wide">Penerbit</p>
                                <p class="text-sm font-semibold text-gray-900 truncate">
                                    {{ is_array($book['publishers']) ? ($book['publishers'][0] ?? '-') : $book['publishers'] }}
                                </p>
                            </div>
                        </div>
                        @endif
                        
                        {{-- Halaman --}}
                        @if($book['number_of_pages'] ?? null)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-file-alt text-blue-600 text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-gray-500 uppercase tracking-wide">Halaman</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $book['number_of_pages'] }} halaman</p>
                            </div>
                        </div>
                        @endif
                        
                        {{-- ISBN --}}
                        @if($book['isbn_13'] ?? $book['isbn_10'] ?? $book['isbn'] ?? null)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-barcode text-emerald-600 text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-gray-500 uppercase tracking-wide">ISBN</p>
                                <p class="text-sm font-semibold text-gray-900 font-mono">{{ $book['isbn_13'] ?? $book['isbn_10'] ?? $book['isbn'] }}</p>
                            </div>
                        </div>
                        @endif
                        
                        {{-- Sumber --}}
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-rose-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-globe text-rose-600 text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-gray-500 uppercase tracking-wide">Sumber</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $book['source'] ?? 'Open Library' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Description --}}
            @if($book['description'] ?? null)
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-align-left text-cyan-500"></i>
                        Deskripsi
                    </h3>
                </div>
                <div class="p-4">
                    <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ $book['description'] }}</p>
                </div>
            </div>
            @endif

            {{-- Subjects --}}
            @if(!empty($book['subjects']))
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-tags text-amber-500"></i>
                        Subjek & Kategori
                    </h3>
                </div>
                <div class="p-4">
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($book['subjects'] as $subject)
                            <span class="px-2.5 py-1 bg-cyan-50 text-cyan-700 text-xs font-medium rounded-lg border border-cyan-100">{{ $subject }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Share Button --}}
            <div class="bg-white rounded-2xl p-4 shadow-lg">
                <div class="flex gap-3">
                    <button onclick="navigator.share ? navigator.share({title: '{{ $book['title'] }}', url: window.location.href}) : navigator.clipboard.writeText(window.location.href).then(() => alert('Link disalin!'))" class="flex-1 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl flex items-center justify-center gap-2 hover:bg-gray-200 transition">
                        <i class="fas fa-share-alt"></i>
                        <span>Bagikan</span>
                    </button>
                    <a href="{{ route('opac.search') }}" class="flex-1 py-3 bg-cyan-600 text-white font-semibold rounded-xl flex items-center justify-center gap-2 hover:bg-cyan-700 transition">
                        <i class="fas fa-search"></i>
                        <span>Cari Lagi</span>
                    </a>
                </div>
            </div>
            
        </div>
        @endif
    </div>
</div>
