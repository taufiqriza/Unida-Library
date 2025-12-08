<x-opac.layout :title="$book->title">
    {{-- Mobile: Full-width immersive design --}}
    <div class="lg:max-w-7xl lg:mx-auto lg:px-4 lg:py-8">
        
        {{-- Hero Section with Cover --}}
        <div class="relative bg-gradient-to-b from-blue-600 via-blue-700 to-blue-800 lg:rounded-2xl lg:overflow-hidden">
            {{-- Decorative blur --}}
            <div class="absolute inset-0 overflow-hidden">
                @if($book->cover_url)
                    <img src="{{ $book->cover_url }}" class="w-full h-full object-cover opacity-20 blur-2xl scale-110">
                @endif
            </div>
            
            {{-- Back Button (Mobile) --}}
            <div class="relative z-10 px-4 pt-4 lg:hidden">
                <a href="{{ route('opac.search') }}" class="inline-flex items-center gap-2 text-white/80 hover:text-white text-sm">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
            
            {{-- Breadcrumb (Desktop) --}}
            <nav class="hidden lg:block relative z-10 px-6 pt-6 text-sm text-white/70">
                <a href="{{ route('opac.home') }}" class="hover:text-white">Beranda</a>
                <span class="mx-2">/</span>
                <a href="{{ route('opac.search') }}" class="hover:text-white">Pencarian</a>
                <span class="mx-2">/</span>
                <span class="text-white">{{ Str::limit($book->title, 40) }}</span>
            </nav>
            
            {{-- Cover & Basic Info --}}
            <div class="relative z-10 px-4 pb-6 pt-4 lg:p-8">
                <div class="flex flex-col items-center lg:flex-row lg:items-end gap-4 lg:gap-8">
                    {{-- Cover --}}
                    <div class="w-40 lg:w-52 flex-shrink-0">
                        <div class="aspect-[3/4] bg-white rounded-xl shadow-2xl overflow-hidden ring-4 ring-white/20">
                            @if($book->cover_url)
                                <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-blue-100 to-blue-50 flex items-center justify-center">
                                    <i class="fas fa-book text-5xl text-blue-300"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Title & Author --}}
                    <div class="text-center lg:text-left flex-1">
                        <h1 class="text-xl lg:text-3xl font-bold text-white leading-tight">{{ $book->title }}</h1>
                        <p class="text-blue-200 mt-2 text-sm lg:text-base">{{ $book->author_names ?: 'Penulis tidak diketahui' }}</p>
                        
                        {{-- Quick Stats --}}
                        <div class="flex items-center justify-center lg:justify-start gap-4 mt-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-white">{{ $book->items->count() }}</div>
                                <div class="text-xs text-blue-200">Eksemplar</div>
                            </div>
                            <div class="w-px h-10 bg-white/20"></div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-400">{{ $book->items->where('status', 'available')->count() }}</div>
                                <div class="text-xs text-blue-200">Tersedia</div>
                            </div>
                            <div class="w-px h-10 bg-white/20"></div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-white">{{ $book->publish_year ?? '-' }}</div>
                                <div class="text-xs text-blue-200">Tahun</div>
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
                <button class="flex-1 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/30 flex items-center justify-center gap-2">
                    <i class="fas fa-bookmark"></i>
                    <span>Simpan</span>
                </button>
                <button class="flex-1 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl flex items-center justify-center gap-2 hover:bg-gray-200 transition">
                    <i class="fas fa-share-alt"></i>
                    <span>Bagikan</span>
                </button>
            </div>

            {{-- Detail Info --}}
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        Informasi Buku
                    </h3>
                </div>
                <div class="divide-y divide-gray-100">
                    <div class="flex items-center px-4 py-3">
                        <span class="w-28 text-sm text-gray-500 flex-shrink-0">ISBN</span>
                        <span class="text-sm text-gray-900 font-medium">{{ $book->isbn ?? '-' }}</span>
                    </div>
                    <div class="flex items-center px-4 py-3">
                        <span class="w-28 text-sm text-gray-500 flex-shrink-0">Penerbit</span>
                        <span class="text-sm text-gray-900 font-medium">{{ $book->publisher?->name ?? '-' }}</span>
                    </div>
                    <div class="flex items-center px-4 py-3">
                        <span class="w-28 text-sm text-gray-500 flex-shrink-0">Tahun Terbit</span>
                        <span class="text-sm text-gray-900 font-medium">{{ $book->publish_year ?? '-' }}</span>
                    </div>
                    <div class="flex items-center px-4 py-3">
                        <span class="w-28 text-sm text-gray-500 flex-shrink-0">Bahasa</span>
                        <span class="text-sm text-gray-900 font-medium">{{ $book->language ?? '-' }}</span>
                    </div>
                    <div class="flex items-center px-4 py-3">
                        <span class="w-28 text-sm text-gray-500 flex-shrink-0">Halaman</span>
                        <span class="text-sm text-gray-900 font-medium">{{ $book->collation ?? '-' }}</span>
                    </div>
                    <div class="flex items-center px-4 py-3">
                        <span class="w-28 text-sm text-gray-500 flex-shrink-0">No. Panggil</span>
                        <span class="text-sm text-gray-900 font-medium font-mono bg-gray-100 px-2 py-0.5 rounded">{{ $book->call_number ?? '-' }}</span>
                    </div>
                    <div class="flex items-center px-4 py-3">
                        <span class="w-28 text-sm text-gray-500 flex-shrink-0">Edisi</span>
                        <span class="text-sm text-gray-900 font-medium">{{ $book->edition ?? '-' }}</span>
                    </div>
                    @if($book->subjects->count() > 0)
                    <div class="flex items-start px-4 py-3">
                        <span class="w-28 text-sm text-gray-500 flex-shrink-0 pt-0.5">Subjek</span>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($book->subjects as $subject)
                                <span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-xs rounded-full">{{ $subject->name }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Abstract --}}
            @if($book->abstract)
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-align-left text-blue-500"></i>
                        Abstrak
                    </h3>
                </div>
                <div class="p-4">
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $book->abstract }}</p>
                </div>
            </div>
            @endif

            {{-- Availability --}}
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-blue-500"></i>
                        Ketersediaan Eksemplar
                    </h3>
                </div>
                @if($book->items->count() > 0)
                <div class="divide-y divide-gray-100">
                    @foreach($book->items as $item)
                    <div class="px-4 py-3 flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-gray-900 font-mono">{{ $item->barcode }}</div>
                            <div class="text-xs text-gray-500 flex items-center gap-1 mt-0.5">
                                <i class="fas fa-building text-gray-400"></i>
                                {{ $item->branch?->name ?? 'Lokasi tidak diketahui' }}
                            </div>
                        </div>
                        @if($item->status === 'available')
                            <span class="px-3 py-1.5 bg-green-100 text-green-700 rounded-full text-xs font-semibold flex items-center gap-1">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                Tersedia
                            </span>
                        @elseif($item->status === 'borrowed')
                            <span class="px-3 py-1.5 bg-orange-100 text-orange-700 rounded-full text-xs font-semibold flex items-center gap-1">
                                <span class="w-1.5 h-1.5 bg-orange-500 rounded-full"></span>
                                Dipinjam
                            </span>
                        @else
                            <span class="px-3 py-1.5 bg-gray-100 text-gray-600 rounded-full text-xs font-semibold">
                                {{ ucfirst($item->status) }}
                            </span>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-box-open text-2xl text-gray-400"></i>
                    </div>
                    <p class="text-sm text-gray-500">Tidak ada eksemplar tersedia</p>
                </div>
                @endif
            </div>

            {{-- Related Books --}}
            @if($relatedBooks->count() > 0)
            <div class="pt-4">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Buku Terkait</h2>
                    <a href="{{ route('opac.search') . '?type=book' }}?subject={{ $book->subjects->first()?->id }}" class="text-sm text-blue-600">
                        Lihat Semua <i class="fas fa-chevron-right text-xs ml-1"></i>
                    </a>
                </div>
                <div class="flex gap-3 overflow-x-auto pb-2 -mx-4 px-4 lg:mx-0 lg:px-0 scrollbar-hide">
                    @foreach($relatedBooks as $related)
                    <a href="{{ route('opac.catalog.show', $related->id) }}" class="flex-shrink-0 w-32 lg:w-40 group">
                        <div class="aspect-[3/4] bg-white rounded-xl shadow-lg overflow-hidden mb-2">
                            @if($related->cover_url)
                                <img src="{{ $related->cover_url }}" alt="{{ $related->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-blue-100 to-blue-50 flex items-center justify-center">
                                    <i class="fas fa-book text-2xl text-blue-300"></i>
                                </div>
                            @endif
                        </div>
                        <h3 class="text-sm font-medium text-gray-900 line-clamp-2 group-hover:text-blue-600 transition">{{ $related->title }}</h3>
                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ $related->author_names ?: '-' }}</p>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
            
        </div>
    </div>
</x-opac.layout>
