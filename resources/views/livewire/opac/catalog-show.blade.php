<div>
    <div class="lg:max-w-7xl lg:mx-auto lg:px-4 lg:py-8">
        
        {{-- Hero Section with Cover --}}
        <div class="relative bg-gradient-to-b from-blue-600 via-blue-700 to-blue-800 lg:rounded-2xl lg:overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                @if($book->cover_url)
                    <img src="{{ $book->cover_url }}" class="w-full h-full object-cover opacity-20 blur-2xl scale-110">
                @endif
            </div>
            
            {{-- Back Button --}}
            <div class="relative z-10 px-4 pt-4 lg:px-6 lg:pt-6 flex items-center justify-between">
                <button onclick="window.history.back()" class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 hover:bg-white/30 text-white text-sm rounded-xl transition backdrop-blur-sm">
                    <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i>
                    <span>{{ __('opac.catalog_show.back') }}</span>
                </button>
                
                {{-- Breadcrumb (Desktop) --}}
                <nav class="hidden lg:flex items-center gap-2 text-sm text-white/70">
                    <a href="{{ route('opac.home') }}" class="hover:text-white">{{ __('opac.catalog_show.home') }}</a>
                    <span>/</span>
                    <a href="{{ route('opac.search') }}?type=book" class="hover:text-white">{{ __('opac.catalog_show.books') }}</a>
                    <span>/</span>
                    <span class="text-white truncate max-w-[200px]">{{ Str::limit($book->title, 30) }}</span>
                </nav>
            </div>
            
            {{-- Cover & Basic Info --}}
            <div class="relative z-10 px-4 pb-6 pt-4 lg:p-8">
                <div class="flex flex-col items-center lg:flex-row lg:items-end gap-4 lg:gap-8">
                    <div class="w-40 lg:w-52 flex-shrink-0">
                        <div class="aspect-[3/4] bg-white rounded-xl shadow-2xl overflow-hidden ring-4 ring-white/20">
                            @if($book->cover_url)
                                <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                            @else
                                {{-- Elegant Default Cover --}}
                                <div class="w-full h-full bg-gradient-to-br from-blue-600 via-indigo-500 to-purple-600 flex flex-col items-center justify-center p-4 text-center">
                                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-3">
                                        <i class="fas fa-book text-2xl text-white/80"></i>
                                    </div>
                                    <p class="text-white font-bold text-sm leading-tight line-clamp-4">{{ Str::limit($book->title, 60) }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="text-center lg:text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} flex-1">
                        <h1 class="text-xl lg:text-3xl font-bold text-white leading-tight">{{ $book->title }}</h1>
                        <p class="text-blue-200 mt-2 text-sm lg:text-base">{{ $book->author_names ?: __('opac.catalog_show.author_unknown') }}</p>
                        
                        <div class="flex items-center justify-center lg:justify-start gap-4 mt-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-white">{{ $book->items->count() }}</div>
                                <div class="text-xs text-blue-200">{{ __('opac.catalog_show.copies') }}</div>
                            </div>
                            <div class="w-px h-10 bg-white/20"></div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-400">{{ $book->items->where('status', 'available')->count() }}</div>
                                <div class="text-xs text-blue-200">{{ __('opac.catalog_show.available') }}</div>
                            </div>
                            <div class="w-px h-10 bg-white/20"></div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-white">{{ $book->publish_year ?? '-' }}</div>
                                <div class="text-xs text-blue-200">{{ __('opac.catalog_show.year') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Content Cards --}}
        <div class="px-4 lg:px-0 mt-6 relative z-20 space-y-4 pb-8">
            
            {{-- Detail Info - Side by Side Layout --}}
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        {{ __('opac.catalog_show.book_info') }}
                    </h3>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        {{-- ISBN --}}
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-barcode text-blue-600 text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-gray-500 uppercase tracking-wide">{{ __('opac.catalog_show.isbn') }}</p>
                                <p class="text-sm font-semibold text-gray-900 font-mono truncate">{{ $book->isbn ?? '-' }}</p>
                            </div>
                        </div>
                        
                        {{-- Penerbit --}}
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-building text-purple-600 text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-gray-500 uppercase tracking-wide">{{ __('opac.catalog_show.publisher') }}</p>
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $book->publisher?->name ?? '-' }}</p>
                            </div>
                        </div>
                        
                        {{-- Tahun Terbit --}}
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-calendar text-amber-600 text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-gray-500 uppercase tracking-wide">{{ __('opac.catalog_show.publish_year') }}</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $book->publish_year ?? '-' }}</p>
                            </div>
                        </div>
                        
                        {{-- Bahasa --}}
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-globe text-emerald-600 text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-gray-500 uppercase tracking-wide">{{ __('opac.catalog_show.language') }}</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $book->language ?? '-' }}</p>
                            </div>
                        </div>
                        
                        {{-- Halaman --}}
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-rose-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-file-alt text-rose-600 text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-gray-500 uppercase tracking-wide">{{ __('opac.catalog_show.pages') }}</p>
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $book->collation ?? '-' }}</p>
                            </div>
                        </div>
                        
                        {{-- No. Panggil --}}
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-tag text-indigo-600 text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-gray-500 uppercase tracking-wide">{{ __('opac.catalog_show.call_number') }}</p>
                                <p class="text-sm font-semibold text-gray-900 font-mono truncate">{{ $book->call_number ?? '-' }}</p>
                            </div>
                        </div>
                        
                        {{-- Edisi --}}
                        @if($book->edition)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-layer-group text-teal-600 text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-gray-500 uppercase tracking-wide">{{ __('opac.catalog_show.edition') }}</p>
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $book->edition }}</p>
                            </div>
                        </div>
                        @endif
                        
                        {{-- Tempat Terbit --}}
                        @if($book->publish_place)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-map-marker-alt text-cyan-600 text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-gray-500 uppercase tracking-wide">{{ __('opac.catalog_show.publish_place') }}</p>
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $book->publish_place }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    {{-- Subjek --}}
                    @if($book->subjects->count() > 0)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-violet-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-tags text-violet-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-[10px] text-gray-500 uppercase tracking-wide mb-2">{{ __('opac.catalog_show.subject') }}</p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($book->subjects as $subject)
                                        <a href="{{ route('opac.search') }}?subject={{ $subject->id }}" class="px-2.5 py-1 bg-blue-50 text-blue-700 text-xs font-medium rounded-lg border border-blue-100 hover:bg-blue-100 transition">{{ $subject->name }}</a>
                                    @endforeach
                                </div>
                            </div>
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
                        {{ __('opac.catalog_show.abstract') }}
                    </h3>
                </div>
                <div class="p-4">
                    <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ $book->abstract }}</p>
                </div>
            </div>
            @endif

            {{-- Availability --}}
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-4 py-3 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-green-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-green-500"></i>
                        {{ __('opac.catalog_show.availability') }}
                    </h3>
                </div>
                @if($book->items->count() > 0)
                <div class="p-4 space-y-2">
                    @foreach($book->items as $item)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <div class="w-10 h-10 {{ $item->status === 'available' ? 'bg-green-100' : 'bg-orange-100' }} rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-book {{ $item->status === 'available' ? 'text-green-600' : 'text-orange-600' }} text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900 font-mono">{{ $item->barcode }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $item->branch?->name ?? __('opac.catalog_show.location_unknown') }}</p>
                            </div>
                        </div>
                        @if($item->status === 'available')
                            <span class="px-3 py-1.5 bg-green-100 text-green-700 rounded-full text-xs font-semibold flex items-center gap-1">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                {{ __('opac.catalog_show.available') }}
                            </span>
                        @elseif($item->status === 'borrowed')
                            <span class="px-3 py-1.5 bg-orange-100 text-orange-700 rounded-full text-xs font-semibold flex items-center gap-1">
                                <span class="w-1.5 h-1.5 bg-orange-500 rounded-full"></span>
                                {{ __('opac.catalog_show.borrowed') }}
                            </span>
                        @else
                            <span class="px-3 py-1.5 bg-gray-100 text-gray-600 rounded-full text-xs font-semibold">
                                {{ ucfirst($item->status) }}
                            </span>
                        @endif
                    </div>
                    @endforeach
                    
                    {{-- Reservation Button --}}
                    @if($book->items->where('status', 'available')->count() === 0)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        @auth('member')
                            @if($this->existingReservation)
                                <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-clock text-blue-600"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-blue-800">Anda sudah reservasi buku ini</p>
                                            <p class="text-xs text-blue-600">
                                                @if($this->existingReservation->status === 'ready')
                                                    Buku siap diambil hingga {{ $this->existingReservation->expires_at->format('d M Y H:i') }}
                                                @else
                                                    Antrian ke-{{ $this->existingReservation->queue_position }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @elseif($this->canReserve)
                                <button wire:click="reserve" wire:loading.attr="disabled" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl flex items-center justify-center gap-2 transition disabled:opacity-50">
                                    <i class="fas fa-bookmark" wire:loading.remove wire:target="reserve"></i>
                                    <i class="fas fa-spinner fa-spin" wire:loading wire:target="reserve"></i>
                                    <span>Reservasi Buku Ini</span>
                                </button>
                                <p class="text-xs text-gray-500 text-center mt-2">Anda akan diberitahu saat buku tersedia</p>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="block w-full py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl text-center transition">
                                <i class="fas fa-sign-in-alt mr-2"></i>Login untuk Reservasi
                            </a>
                        @endauth
                    </div>
                    @endif
                </div>
                @else
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-box-open text-2xl text-gray-400"></i>
                    </div>
                    <p class="text-sm text-gray-500">{{ __('opac.catalog_show.no_copies') }}</p>
                </div>
                @endif
            </div>

            {{-- Share Button --}}
            <div class="bg-white rounded-2xl p-4 shadow-lg">
                <div class="flex gap-3">
                    <button onclick="navigator.share ? navigator.share({title: '{{ $book->title }}', url: window.location.href}) : navigator.clipboard.writeText(window.location.href).then(() => alert('{{ __('opac.catalog_show.link_copied') }}'))" class="flex-1 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl flex items-center justify-center gap-2 hover:bg-gray-200 transition">
                        <i class="fas fa-share-alt"></i>
                        <span>{{ __('opac.catalog_show.share') }}</span>
                    </button>
                    <button onclick="window.print()" class="flex-1 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl flex items-center justify-center gap-2 hover:bg-gray-200 transition">
                        <i class="fas fa-print"></i>
                        <span>{{ __('opac.catalog_show.print') }}</span>
                    </button>
                </div>
            </div>

            {{-- Related Books --}}
            @if($relatedBooks->count() > 0)
            <div class="pt-4">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900">{{ __('opac.catalog_show.related_books') }}</h2>
                    <a href="{{ route('opac.search') . '?type=book' }}@if($book->subjects->first())&subject={{ $book->subjects->first()->id }}@endif" class="text-sm text-blue-600">
                        {{ __('opac.catalog_show.view_all') }} <i class="fas fa-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} text-xs {{ app()->getLocale() === 'ar' ? 'mr-1' : 'ml-1' }}"></i>
                    </a>
                </div>
                <div class="flex gap-3 overflow-x-auto pb-2 -mx-4 px-4 lg:mx-0 lg:px-0 scrollbar-hide">
                    @foreach($relatedBooks as $related)
                    <a href="{{ route('opac.catalog.show', $related->id) }}" class="flex-shrink-0 w-32 lg:w-40 group">
                        <div class="aspect-[3/4] bg-white rounded-xl shadow-lg overflow-hidden mb-2">
                            @if($related->cover_url)
                                <img src="{{ $related->cover_url }}" alt="{{ $related->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-blue-600 via-indigo-500 to-purple-600 flex flex-col items-center justify-center p-2 text-center group-hover:scale-105 transition duration-300">
                                    <i class="fas fa-book text-xl text-white/70 mb-1"></i>
                                    <p class="text-white font-bold text-[10px] leading-tight line-clamp-3">{{ Str::limit($related->title, 40) }}</p>
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
</div>
