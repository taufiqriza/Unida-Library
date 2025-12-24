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
                    <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i>
                    <span>{{ __('opac.ebook_show.back') }}</span>
                </button>
                
                {{-- Breadcrumb (Desktop) --}}
                <nav class="hidden lg:flex items-center gap-2 text-sm text-white/70">
                    <a href="{{ route('opac.home') }}" class="hover:text-white">{{ __('opac.ebook_show.home') }}</a>
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
                                {{-- Elegant Default Cover --}}
                                <div class="w-full h-full bg-gradient-to-br from-amber-600 via-orange-500 to-red-600 flex flex-col items-center justify-center p-4 text-center">
                                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-3">
                                        <i class="fas fa-book-open text-2xl text-white/80"></i>
                                    </div>
                                    <p class="text-white font-bold text-sm leading-tight line-clamp-4">{{ Str::limit($ebook->title, 60) }}</p>
                                    @if($ebook->file_format)
                                        <span class="mt-2 px-2 py-0.5 bg-white/20 text-white/80 text-[10px] font-semibold rounded">{{ $ebook->file_format }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="text-center lg:text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} flex-1">
                        <span class="inline-block px-3 py-1 bg-white/20 text-white text-xs font-semibold rounded-full mb-2">{{ strtoupper($ebook->file_format ?? 'PDF') }}</span>
                        <h1 class="text-xl lg:text-3xl font-bold text-white leading-tight">{{ $ebook->title }}</h1>
                        <p class="text-orange-200 mt-2 text-sm lg:text-base">{{ $ebook->author_names ?: __('opac.ebook_show.unknown_author') }}</p>
                        
                        <div class="flex items-center justify-center lg:justify-start gap-4 mt-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-white">{{ $ebook->publish_year ?? '-' }}</div>
                                <div class="text-xs text-orange-200">{{ __('opac.ebook_show.year') }}</div>
                            </div>
                            <div class="w-px h-10 bg-white/20"></div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-white">{{ $ebook->pages ?? '-' }}</div>
                                <div class="text-xs text-orange-200">{{ __('opac.ebook_show.pages') }}</div>
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
                @if($ebook->viewer_url)
                <a href="{{ $ebook->viewer_url }}" target="_blank" class="flex-1 py-3 bg-gradient-to-r from-orange-500 to-red-500 text-white font-semibold rounded-xl shadow-lg shadow-orange-500/30 flex items-center justify-center gap-2">
                    <i class="fas fa-book-reader"></i>
                    <span>{{ __('opac.ebook_show.read_now') }}</span>
                </a>
                @endif
                @if($ebook->is_downloadable && $ebook->download_url)
                <a href="{{ $ebook->download_url }}" target="_blank" class="flex-1 py-3 bg-emerald-500 text-white font-semibold rounded-xl shadow-lg shadow-emerald-500/30 flex items-center justify-center gap-2 hover:bg-emerald-600 transition">
                    <i class="fas fa-download"></i>
                    <span>Download</span>
                </a>
                @endif
                <button class="flex-1 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl flex items-center justify-center gap-2 hover:bg-gray-200 transition">
                    <i class="fas fa-share-alt"></i>
                    <span>{{ __('opac.ebook_show.share') }}</span>
                </button>
            </div>

            {{-- Detail Info --}}
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-info-circle text-orange-500"></i>
                        {{ __('opac.ebook_show.ebook_info') }}
                    </h3>
                </div>
                <div class="divide-y divide-gray-100">
                    <div class="flex items-center px-4 py-3">
                        <span class="w-28 text-sm text-gray-500 flex-shrink-0">{{ __('opac.ebook_show.author') }}</span>
                        <span class="text-sm text-gray-900 font-medium">{{ $ebook->author_names ?: '-' }}</span>
                    </div>
                    <div class="flex items-center px-4 py-3">
                        <span class="w-28 text-sm text-gray-500 flex-shrink-0">{{ __('opac.ebook_show.publisher') }}</span>
                        <span class="text-sm text-gray-900 font-medium">{{ $ebook->publisher ?? '-' }}</span>
                    </div>
                    <div class="flex items-center px-4 py-3">
                        <span class="w-28 text-sm text-gray-500 flex-shrink-0">{{ __('opac.ebook_show.year') }}</span>
                        <span class="text-sm text-gray-900 font-medium">{{ $ebook->publish_year ?? '-' }}</span>
                    </div>
                    <div class="flex items-center px-4 py-3">
                        <span class="w-28 text-sm text-gray-500 flex-shrink-0">{{ __('opac.ebook_show.language') }}</span>
                        <span class="text-sm text-gray-900 font-medium">{{ $ebook->language ?? '-' }}</span>
                    </div>
                    <div class="flex items-center px-4 py-3">
                        <span class="w-28 text-sm text-gray-500 flex-shrink-0">{{ __('opac.ebook_show.format') }}</span>
                        <span class="text-sm text-gray-900 font-medium font-mono bg-orange-100 px-2 py-0.5 rounded">{{ strtoupper($ebook->file_format ?? 'PDF') }}</span>
                    </div>
                    <div class="flex items-center px-4 py-3">
                        <span class="w-28 text-sm text-gray-500 flex-shrink-0">{{ __('opac.ebook_show.pages') }}</span>
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
                        {{ __('opac.ebook_show.description') }}
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
                    <h2 class="text-lg font-bold text-gray-900">{{ __('opac.ebook_show.other_ebooks') }}</h2>
                </div>
                <div class="flex gap-3 overflow-x-auto pb-2 -mx-4 px-4 lg:mx-0 lg:px-0 scrollbar-hide">
                    @foreach($relatedEbooks as $related)
                    <a href="{{ route('opac.ebook.show', $related->id) }}" class="flex-shrink-0 w-32 lg:w-40 group">
                        <div class="aspect-[3/4] bg-white rounded-xl shadow-lg overflow-hidden mb-2">
                            @if($related->cover_url)
                                <img src="{{ $related->cover_url }}" alt="{{ $related->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-amber-600 via-orange-500 to-red-600 flex flex-col items-center justify-center p-2 text-center group-hover:scale-105 transition duration-300">
                                    <i class="fas fa-book-open text-xl text-white/70 mb-1"></i>
                                    <p class="text-white font-bold text-[10px] leading-tight line-clamp-3">{{ Str::limit($related->title, 40) }}</p>
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
