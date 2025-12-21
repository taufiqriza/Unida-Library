<div>
    {{-- Loading State --}}
    @if($loading)
    <div class="min-h-screen flex items-center justify-center bg-gray-50">
        <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-emerald-500 mx-auto"></div>
            <p class="mt-4 text-gray-600">{{ __('opac.kubuku.loading') }}</p>
        </div>
    </div>
    @elseif($error)
    {{-- Error State --}}
    <div class="min-h-screen flex items-center justify-center bg-gray-50">
        <div class="text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-2xl text-red-500"></i>
            </div>
            <h2 class="text-xl font-semibold text-gray-900 mb-2">{{ $error }}</h2>
            <a href="{{ route('opac.search') }}?type=ebook" class="text-emerald-600 hover:underline">
                {{ __('opac.kubuku.back_to_search') }}
            </a>
        </div>
    </div>
    @else
    <div class="lg:max-w-7xl lg:mx-auto lg:px-4 lg:py-8">
        
        {{-- Hero Section --}}
        <div class="relative bg-gradient-to-b from-emerald-500 via-emerald-600 to-teal-600 lg:rounded-2xl lg:overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                @if($ebook['cover'] ?? null)
                    <img src="{{ $ebook['cover'] }}" class="w-full h-full object-cover opacity-20 blur-2xl scale-110">
                @endif
            </div>
            
            {{-- Back Button --}}
            <div class="relative z-10 px-4 pt-4 lg:px-6 lg:pt-6 flex items-center justify-between">
                <button onclick="window.history.back()" class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 hover:bg-white/30 text-white text-sm rounded-xl transition backdrop-blur-sm">
                    <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i>
                    <span>{{ __('opac.kubuku.back') }}</span>
                </button>
                
                {{-- Breadcrumb (Desktop) --}}
                <nav class="hidden lg:flex items-center gap-2 text-sm text-white/70">
                    <a href="{{ route('opac.home') }}" class="hover:text-white">{{ __('opac.kubuku.home') }}</a>
                    <span>/</span>
                    <a href="{{ route('opac.search') }}?type=ebook" class="hover:text-white">E-Book</a>
                    <span>/</span>
                    <span class="text-white truncate max-w-[200px]">{{ Str::limit($ebook['title'], 30) }}</span>
                </nav>
            </div>
            
            {{-- Cover & Basic Info --}}
            <div class="relative z-10 px-4 pb-6 pt-4 lg:p-8">
                <div class="flex flex-col items-center lg:flex-row lg:items-end gap-4 lg:gap-8">
                    <div class="w-40 lg:w-52 flex-shrink-0">
                        <div class="aspect-[3/4] bg-white rounded-xl shadow-2xl overflow-hidden ring-4 ring-white/20">
                            @if($ebook['cover'] ?? null)
                                <img src="{{ $ebook['cover'] }}" alt="{{ $ebook['title'] }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-emerald-100 to-emerald-50 flex items-center justify-center">
                                    <i class="fas fa-book-reader text-5xl text-emerald-300"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="text-center lg:text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} flex-1">
                        <span class="inline-block px-3 py-1 bg-white/20 text-white text-xs font-semibold rounded-full mb-2">
                            <i class="fas fa-book-reader mr-1"></i> KUBUKU
                        </span>
                        <h1 class="text-xl lg:text-3xl font-bold text-white leading-tight">{{ $ebook['title'] }}</h1>
                        <p class="text-emerald-200 mt-2 text-sm lg:text-base">{{ $ebook['author'] ?? '-' }}</p>
                        
                        <div class="flex items-center justify-center lg:justify-start gap-4 mt-4">
                            @if($ebook['year'] ?? null)
                            <div class="text-center">
                                <div class="text-2xl font-bold text-white">{{ $ebook['year'] }}</div>
                                <div class="text-xs text-emerald-200">{{ __('opac.kubuku.year') }}</div>
                            </div>
                            @endif
                            @if($ebook['pages'] ?? null)
                            <div class="w-px h-10 bg-white/20"></div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-white">{{ $ebook['pages'] }}</div>
                                <div class="text-xs text-emerald-200">{{ __('opac.kubuku.pages') }}</div>
                            </div>
                            @endif
                            @if($ebook['category'] ?? null)
                            <div class="w-px h-10 bg-white/20"></div>
                            <div class="text-center">
                                <div class="text-lg font-bold text-white">{{ $ebook['category'] }}</div>
                                <div class="text-xs text-emerald-200">{{ __('opac.kubuku.category') }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Content Cards --}}
        <div class="px-4 lg:px-0 -mt-4 relative z-20 space-y-4 pb-8">
            
            {{-- Action Buttons --}}
            <div class="bg-white rounded-2xl p-4 shadow-lg flex gap-3">
                <button 
                    wire:click="openReader"
                    class="flex-1 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-semibold rounded-xl shadow-lg shadow-emerald-500/30 flex items-center justify-center gap-2 hover:from-emerald-600 hover:to-teal-600 transition"
                >
                    <i class="fas fa-book-reader"></i>
                    <span>{{ __('opac.kubuku.read_now') }}</span>
                </button>
                <button 
                    onclick="navigator.share ? navigator.share({title: '{{ $ebook['title'] }}', url: window.location.href}) : navigator.clipboard.writeText(window.location.href).then(() => alert('Link copied!'))"
                    class="flex-1 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl flex items-center justify-center gap-2 hover:bg-gray-200 transition"
                >
                    <i class="fas fa-share-alt"></i>
                    <span>{{ __('opac.kubuku.share') }}</span>
                </button>
            </div>

            {{-- KUBUKU Notice --}}
            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-info-circle text-emerald-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-emerald-900">{{ __('opac.kubuku.reader_notice_title') }}</h4>
                        <p class="text-sm text-emerald-700 mt-1">{{ __('opac.kubuku.reader_notice_desc') }}</p>
                    </div>
                </div>
            </div>

            {{-- Detail Info --}}
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-info-circle text-emerald-500"></i>
                        {{ __('opac.kubuku.ebook_info') }}
                    </h3>
                </div>
                <div class="divide-y divide-gray-100">
                    <div class="flex items-center px-4 py-3">
                        <span class="w-28 text-sm text-gray-500 flex-shrink-0">{{ __('opac.kubuku.author') }}</span>
                        <span class="text-sm text-gray-900 font-medium">{{ $ebook['author'] ?? '-' }}</span>
                    </div>
                    <div class="flex items-center px-4 py-3">
                        <span class="w-28 text-sm text-gray-500 flex-shrink-0">{{ __('opac.kubuku.publisher') }}</span>
                        <span class="text-sm text-gray-900 font-medium">{{ $ebook['publisher'] ?? '-' }}</span>
                    </div>
                    <div class="flex items-center px-4 py-3">
                        <span class="w-28 text-sm text-gray-500 flex-shrink-0">{{ __('opac.kubuku.year') }}</span>
                        <span class="text-sm text-gray-900 font-medium">{{ $ebook['year'] ?? '-' }}</span>
                    </div>
                    @if($ebook['isbn'] ?? null)
                    <div class="flex items-center px-4 py-3">
                        <span class="w-28 text-sm text-gray-500 flex-shrink-0">ISBN</span>
                        <span class="text-sm text-gray-900 font-medium">{{ $ebook['isbn'] }}</span>
                    </div>
                    @endif
                    @if($ebook['category'] ?? null)
                    <div class="flex items-center px-4 py-3">
                        <span class="w-28 text-sm text-gray-500 flex-shrink-0">{{ __('opac.kubuku.category') }}</span>
                        <span class="text-sm text-gray-900 font-medium">{{ $ebook['category'] }}</span>
                    </div>
                    @endif
                    @if($ebook['subcategory'] ?? null)
                    <div class="flex items-center px-4 py-3">
                        <span class="w-28 text-sm text-gray-500 flex-shrink-0">{{ __('opac.kubuku.subcategory') }}</span>
                        <span class="text-sm text-gray-900 font-medium">{{ $ebook['subcategory'] }}</span>
                    </div>
                    @endif
                    <div class="flex items-center px-4 py-3">
                        <span class="w-28 text-sm text-gray-500 flex-shrink-0">{{ __('opac.kubuku.source') }}</span>
                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-emerald-100 text-emerald-700 text-xs font-medium rounded-full">
                            <i class="fas fa-book-reader"></i> KUBUKU E-Library
                        </span>
                    </div>
                </div>
            </div>

            {{-- Synopsis --}}
            @if($ebook['synopsis'] ?? $ebook['description'] ?? null)
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-align-left text-emerald-500"></i>
                        {{ __('opac.kubuku.synopsis') }}
                    </h3>
                </div>
                <div class="p-4">
                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $ebook['synopsis'] ?? $ebook['description'] }}</p>
                </div>
            </div>
            @endif

        </div>
    </div>

    {{-- Read Confirmation Modal --}}
    @if($showReadModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" wire:click.self="cancelRead">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md mx-4 w-full overflow-hidden">
            <div class="p-6">
                <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-book-reader text-2xl text-emerald-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 text-center mb-2">{{ __('opac.kubuku.read_confirm_title') }}</h3>
                <p class="text-gray-600 text-center text-sm mb-6">{{ __('opac.kubuku.read_confirm_desc') }}</p>
                
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 mb-6">
                    <div class="flex items-start gap-2">
                        <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5"></i>
                        <p class="text-sm text-amber-700">{{ __('opac.kubuku.desktop_app_notice') }}</p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button wire:click="cancelRead" class="flex-1 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition">
                        {{ __('opac.kubuku.cancel') }}
                    </button>
                    <button wire:click="confirmRead" class="flex-1 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-semibold rounded-xl hover:from-emerald-600 hover:to-teal-600 transition">
                        {{ __('opac.kubuku.open_reader') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endif

    {{-- JavaScript to open URL in new tab --}}
    @script
    <script>
        $wire.on('open-url', ({ url }) => {
            window.open(url, '_blank');
        });
    </script>
    @endscript
</div>
