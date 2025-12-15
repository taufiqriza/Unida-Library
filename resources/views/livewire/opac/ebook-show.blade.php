<div>
    <div class="lg:max-w-7xl lg:mx-auto lg:px-4 lg:py-8">
        
        {{-- Hero Section --}}
        <div class="relative bg-gradient-to-b from-orange-500 via-orange-600 to-red-600 lg:rounded-2xl lg:overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                @if($ebook->cover_url)
                    <img src="{{ $ebook->cover_url }}" class="w-full h-full object-cover opacity-20 blur-2xl scale-110">
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
                    <a href="{{ route('opac.search') }}?type=ebook" class="hover:text-white">E-Book</a>
                    <span>/</span>
                    <span class="text-white truncate max-w-[200px]">{{ Str::limit($ebook->title, 30) }}</span>
                </nav>
            </div>
            
            {{-- Cover & Basic Info --}}
            <div class="relative z-10 px-4 pb-6 pt-4 lg:p-8">
                <div class="flex flex-col items-center lg:flex-row lg:items-end gap-4 lg:gap-8">
                    <div class="w-40 lg:w-52 flex-shrink-0">
                        <div class="aspect-[3/4] bg-white rounded-xl shadow-2xl overflow-hidden ring-4 ring-white/20">
                            @if($ebook->cover_url)
                                <img src="{{ $ebook->cover_url }}" alt="{{ $ebook->title }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-orange-100 to-orange-50 flex items-center justify-center">
                                    <i class="fas fa-file-pdf text-5xl text-orange-300"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="text-center lg:text-left flex-1">
                        <span class="inline-block px-3 py-1 bg-white/20 text-white text-xs font-semibold rounded-full mb-2">{{ strtoupper($ebook->file_format ?? 'PDF') }}</span>
                        <h1 class="text-xl lg:text-3xl font-bold text-white leading-tight">{{ $ebook->title }}</h1>
                        <p class="text-orange-200 mt-2 text-sm lg:text-base">{{ $ebook->author_names ?: 'Penulis tidak diketahui' }}</p>
                        
                        <div class="flex items-center justify-center lg:justify-start gap-4 mt-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-white">{{ $ebook->publish_year ?? '-' }}</div>
                                <div class="text-xs text-orange-200">Tahun</div>
                            </div>
                            <div class="w-px h-10 bg-white/20"></div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-white">{{ $ebook->pages ?? '-' }}</div>
                                <div class="text-xs text-orange-200">Halaman</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Content Cards --}}
        <div class="px-4 lg:px-0 -mt-4 relative z-20 space-y-4 pb-8">
            
            {{-- Action Buttons --}}
            <div class="bg-white rounded-2xl p-4 shadow-lg flex gap-3">
                @if($ebook->file_path)
                <a href="{{ asset('storage/' . $ebook->file_path) }}" target="_blank" class="flex-1 py-3 bg-gradient-to-r from-orange-500 to-red-500 text-white font-semibold rounded-xl shadow-lg shadow-orange-500/30 flex items-center justify-center gap-2">
                    <i class="fas fa-book-reader"></i>
                    <span>Baca Sekarang</span>
                </a>
                @endif
                <button class="flex-1 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl flex items-center justify-center gap-2 hover:bg-gray-200 transition">
                    <i class="fas fa-share-alt"></i>
                    <span>Bagikan</span>
                </button>
            </div>

            {{-- Detail Info --}}
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-info-circle text-orange-500"></i>
                        Informasi E-Book
                    </h3>
                </div>
                <div class="divide-y divide-gray-100">
                    <div class="flex items-center px-4 py-3">
                        <span class="w-28 text-sm text-gray-500 flex-shrink-0">Penulis</span>
                        <span class="text-sm text-gray-900 font-medium">{{ $ebook->author_names ?: '-' }}</span>
                    </div>
                    <div class="flex items-center px-4 py-3">
                        <span class="w-28 text-sm text-gray-500 flex-shrink-0">Penerbit</span>
                        <span class="text-sm text-gray-900 font-medium">{{ $ebook->publisher ?? '-' }}</span>
                    </div>
                    <div class="flex items-center px-4 py-3">
                        <span class="w-28 text-sm text-gray-500 flex-shrink-0">Tahun</span>
                        <span class="text-sm text-gray-900 font-medium">{{ $ebook->publish_year ?? '-' }}</span>
                    </div>
                    <div class="flex items-center px-4 py-3">
                        <span class="w-28 text-sm text-gray-500 flex-shrink-0">Bahasa</span>
                        <span class="text-sm text-gray-900 font-medium">{{ $ebook->language ?? '-' }}</span>
                    </div>
                    <div class="flex items-center px-4 py-3">
                        <span class="w-28 text-sm text-gray-500 flex-shrink-0">Format</span>
                        <span class="text-sm text-gray-900 font-medium font-mono bg-orange-100 px-2 py-0.5 rounded">{{ strtoupper($ebook->file_format ?? 'PDF') }}</span>
                    </div>
                    <div class="flex items-center px-4 py-3">
                        <span class="w-28 text-sm text-gray-500 flex-shrink-0">Halaman</span>
                        <span class="text-sm text-gray-900 font-medium">{{ $ebook->pages ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- Abstract --}}
            @if($ebook->abstract)
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-align-left text-orange-500"></i>
                        Deskripsi
                    </h3>
                </div>
                <div class="p-4">
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $ebook->abstract }}</p>
                </div>
            </div>
            @endif

            {{-- Related --}}
            @if($relatedEbooks->count() > 0)
            <div class="pt-4">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900">E-Book Lainnya</h2>
                </div>
                <div class="flex gap-3 overflow-x-auto pb-2 -mx-4 px-4 lg:mx-0 lg:px-0 scrollbar-hide">
                    @foreach($relatedEbooks as $related)
                    <a href="{{ route('opac.ebook.show', $related->id) }}" class="flex-shrink-0 w-32 lg:w-40 group">
                        <div class="aspect-[3/4] bg-white rounded-xl shadow-lg overflow-hidden mb-2">
                            @if($related->cover_url)
                                <img src="{{ $related->cover_url }}" alt="{{ $related->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-orange-100 to-orange-50 flex items-center justify-center">
                                    <i class="fas fa-file-pdf text-2xl text-orange-300"></i>
                                </div>
                            @endif
                        </div>
                        <h3 class="text-sm font-medium text-gray-900 line-clamp-2 group-hover:text-orange-600 transition">{{ $related->title }}</h3>
                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ $related->author_names ?: '-' }}</p>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
            
        </div>
    </div>
</div>
