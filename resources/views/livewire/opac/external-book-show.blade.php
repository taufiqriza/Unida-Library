<div class="min-h-screen bg-gray-50">
    {{-- Breadcrumb --}}
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-4 py-3">
            <nav class="flex items-center gap-2 text-sm">
                <a href="{{ route('opac.home') }}" class="text-gray-500 hover:text-primary-600">
                    <i class="fas fa-home"></i>
                </a>
                <i class="fas fa-chevron-right text-gray-300 text-xs"></i>
                <a href="{{ route('opac.search') }}" class="text-gray-500 hover:text-primary-600">Pencarian</a>
                <i class="fas fa-chevron-right text-gray-300 text-xs"></i>
                <span class="text-gray-900 font-medium">Open Library</span>
            </nav>
        </div>
    </div>

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
    {{-- Book Detail --}}
    <div class="max-w-6xl mx-auto px-4 py-6 lg:py-10">
        <div class="flex flex-col lg:flex-row gap-8">
            {{-- Cover Section --}}
            <div class="lg:w-80 flex-shrink-0">
                <div class="sticky top-24">
                    {{-- Cover Image --}}
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="aspect-[2/3] bg-gradient-to-br from-slate-100 to-slate-50 relative">
                            @if($book['cover_url'] ?? null)
                                <img 
                                    src="{{ $book['cover_url'] }}" 
                                    alt="{{ $book['title'] }}" 
                                    class="w-full h-full object-cover"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                >
                                <div class="hidden absolute inset-0 items-center justify-center">
                                    <i class="fas fa-book text-5xl text-slate-300"></i>
                                </div>
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fas fa-book text-5xl text-slate-300"></i>
                                </div>
                            @endif
                            
                            {{-- Source Badge --}}
                            <div class="absolute top-3 left-3">
                                <span class="px-3 py-1.5 bg-cyan-500 text-white text-xs font-bold rounded-full shadow-lg flex items-center gap-1.5">
                                    <i class="fas fa-globe"></i> Open Library
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="mt-4 space-y-3">
                        @if($this->borrowable)
                        <a 
                            href="{{ $this->readUrl }}" 
                            target="_blank" 
                            rel="noopener"
                            class="w-full flex items-center justify-center gap-2 px-5 py-3 bg-gradient-to-r from-cyan-500 to-blue-500 text-white font-semibold rounded-xl hover:from-cyan-600 hover:to-blue-600 transition shadow-lg shadow-cyan-500/30"
                        >
                            <i class="fas fa-book-open"></i>
                            Baca Sekarang
                        </a>
                        @endif
                        
                        <a 
                            href="{{ $book['url'] ?? '#' }}" 
                            target="_blank" 
                            rel="noopener"
                            class="w-full flex items-center justify-center gap-2 px-5 py-3 bg-white text-gray-700 font-medium rounded-xl border border-gray-200 hover:bg-gray-50 hover:border-cyan-300 transition"
                        >
                            <i class="fas fa-external-link-alt"></i>
                            Lihat di Open Library
                        </a>
                        
                        <a 
                            href="{{ route('opac.search') }}" 
                            class="w-full flex items-center justify-center gap-2 px-5 py-3 text-gray-500 font-medium hover:text-primary-600 transition"
                        >
                            <i class="fas fa-arrow-left"></i>
                            Kembali ke Pencarian
                        </a>
                    </div>

                    {{-- Info Box --}}
                    <div class="mt-4 p-4 bg-amber-50 rounded-xl border border-amber-200">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-info-circle text-amber-500 mt-0.5"></i>
                            <div>
                                <p class="text-xs text-amber-800">
                                    Buku ini berasal dari <strong>Open Library</strong>, perpustakaan digital gratis dengan 4+ juta buku.
                                    @if($this->borrowable)
                                        <span class="block mt-1 text-emerald-700">✓ Tersedia untuk dibaca online</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Detail Section --}}
            <div class="flex-1 min-w-0">
                {{-- Title & Authors --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 leading-tight mb-4">
                        {{ $book['title'] }}
                    </h1>
                    
                    @if(!empty($book['authors']))
                    <div class="flex items-center gap-2 text-gray-600 mb-4">
                        <i class="fas fa-pen-nib text-gray-400"></i>
                        <span class="font-medium">
                            {{ is_array($book['authors']) ? implode(', ', $book['authors']) : $book['authors'] }}
                        </span>
                    </div>
                    @endif

                    {{-- Metadata Grid --}}
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
                        @if($book['first_publish_year'] ?? $book['first_publish_date'] ?? $book['publish_date'] ?? null)
                        <div class="p-3 bg-gray-50 rounded-xl">
                            <div class="text-xs text-gray-500 mb-1">Tahun Terbit</div>
                            <div class="font-semibold text-gray-900">
                                {{ $book['first_publish_year'] ?? $book['first_publish_date'] ?? $book['publish_date'] }}
                            </div>
                        </div>
                        @endif
                        
                        @if(!empty($book['publishers']))
                        <div class="p-3 bg-gray-50 rounded-xl">
                            <div class="text-xs text-gray-500 mb-1">Penerbit</div>
                            <div class="font-semibold text-gray-900 truncate">
                                {{ is_array($book['publishers']) ? ($book['publishers'][0] ?? '-') : $book['publishers'] }}
                            </div>
                        </div>
                        @endif
                        
                        @if($book['number_of_pages'] ?? null)
                        <div class="p-3 bg-gray-50 rounded-xl">
                            <div class="text-xs text-gray-500 mb-1">Halaman</div>
                            <div class="font-semibold text-gray-900">{{ $book['number_of_pages'] }} halaman</div>
                        </div>
                        @endif
                        
                        @if($book['isbn_13'] ?? $book['isbn_10'] ?? $book['isbn'] ?? null)
                        <div class="p-3 bg-gray-50 rounded-xl">
                            <div class="text-xs text-gray-500 mb-1">ISBN</div>
                            <div class="font-mono text-sm text-gray-900">
                                {{ $book['isbn_13'] ?? $book['isbn_10'] ?? $book['isbn'] }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Description --}}
                @if($book['description'] ?? null)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-align-left text-primary-500"></i>
                        Deskripsi
                    </h2>
                    <div class="prose prose-sm max-w-none text-gray-600 leading-relaxed">
                        {!! nl2br(e($book['description'])) !!}
                    </div>
                </div>
                @endif

                {{-- Subjects --}}
                @if(!empty($book['subjects']))
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-tags text-amber-500"></i>
                        Subjek & Kategori
                    </h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach($book['subjects'] as $subject)
                        <span class="px-3 py-1.5 bg-amber-50 text-amber-700 text-sm rounded-full border border-amber-200">
                            {{ $subject }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Access Info --}}
                <div class="bg-gradient-to-br from-cyan-50 to-blue-50 rounded-2xl border border-cyan-200 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-globe text-cyan-500"></i>
                        Informasi Akses
                    </h2>
                    
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-cyan-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-book-open text-cyan-600"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Sumber: Open Library</h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    Open Library adalah inisiatif dari Internet Archive yang menyediakan akses gratis ke jutaan buku digital.
                                </p>
                            </div>
                        </div>
                        
                        @if($this->borrowable)
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-check text-emerald-600"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Tersedia untuk Dibaca</h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    Buku ini tersedia di Internet Archive dan dapat dibaca secara online atau dipinjam secara digital.
                                </p>
                            </div>
                        </div>
                        @else
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-info text-gray-500"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Metadata Only</h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    Saat ini hanya informasi buku yang tersedia. Kunjungi Open Library untuk melihat ketersediaan versi digital.
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
