<div class="min-h-screen bg-gradient-to-b from-slate-50 to-white" 
     x-data="universitariaReader()"
     x-on:keydown.escape.window="closeReader()">

    {{-- Hero Section --}}
    <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-800 text-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full -translate-y-1/2 translate-x-1/3"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-white rounded-full translate-y-1/2 -translate-x-1/3"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 relative">
            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-sm text-blue-200 mb-6">
                <a href="{{ route('opac.home') }}" class="hover:text-white transition">
                    <i class="fas fa-home"></i>
                </a>
                <i class="fas fa-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} text-xs"></i>
                <span class="text-white font-medium">Universitaria</span>
            </nav>

            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <h1 class="text-4xl lg:text-5xl font-bold mb-3">
                        <span class="text-amber-300">Universitaria</span>
                    </h1>
                    <h2 class="text-xl lg:text-2xl font-light text-blue-100 mb-4">
                        {{ __('opac.universitaria_browse.intellectual_heritage') }} <span class="font-semibold">PMDG</span>
                    </h2>
                    <p class="text-blue-200 max-w-2xl leading-relaxed">
                        {{ __('opac.universitaria_browse.description') }}
                    </p>
                </div>
                
                {{-- Stats --}}
                <div class="flex gap-4">
                    <div class="bg-white/10 backdrop-blur rounded-2xl px-6 py-4 text-center">
                        <p class="text-3xl font-bold text-amber-300">{{ $ebooks->total() }}</p>
                        <p class="text-sm text-blue-200">{{ __('opac.universitaria_browse.documents') }}</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur rounded-2xl px-6 py-4 text-center">
                        <p class="text-3xl font-bold text-amber-300">{{ count($categories) }}</p>
                        <p class="text-sm text-blue-200">{{ __('opac.universitaria_browse.categories') }}</p>
                    </div>
                </div>
            </div>

            {{-- Search --}}
            <div class="mt-8 max-w-2xl">
                <div class="relative">
                    <input type="text" 
                           wire:model.live.debounce.300ms="search"
                           placeholder="{{ __('opac.universitaria_browse.search_placeholder') }}"
                           class="w-full {{ app()->getLocale() === 'ar' ? 'pr-12 pl-4' : 'pl-12 pr-4' }} py-4 bg-white/10 backdrop-blur border border-white/20 rounded-2xl text-white placeholder-blue-200 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent">
                    <i class="fas fa-search absolute {{ app()->getLocale() === 'ar' ? 'right-4' : 'left-4' }} top-1/2 -translate-y-1/2 text-blue-200"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Notice Banner --}}
    <div class="bg-gradient-to-r from-amber-50 to-orange-50 border-b border-amber-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <div class="flex items-center gap-3 text-amber-800">
                <i class="fas fa-shield-alt"></i>
                <p class="text-sm">
                    <strong>{{ __('opac.universitaria_browse.premium_notice') }}</strong> - {{ __('opac.universitaria_browse.protected_notice') }}
                    @if(!auth()->check())
                        <a href="{{ route('login') }}" class="underline font-semibold">{{ __('opac.universitaria_browse.login_to_access') }}</a>
                    @endif
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Categories Grid --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            {{-- All Categories --}}
            <button wire:click="selectCategory(null)" 
                    class="group relative rounded-2xl p-5 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} transition-all duration-300 overflow-hidden
                           {{ !$selectedCategory ? 'bg-gradient-to-br from-blue-600 to-indigo-700 text-white shadow-lg shadow-blue-500/25' : 'bg-white hover:bg-blue-50 border border-gray-100' }}">
                <div class="relative z-10">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-3
                                {{ !$selectedCategory ? 'bg-white/20' : 'bg-gradient-to-br from-blue-100 to-indigo-100' }}">
                        <i class="fas fa-layer-group text-lg {{ !$selectedCategory ? 'text-white' : 'text-blue-600' }}"></i>
                    </div>
                    <h3 class="font-bold {{ !$selectedCategory ? 'text-white' : 'text-gray-900' }}">{{ __('opac.universitaria_browse.all_collection') }}</h3>
                    <p class="text-sm {{ !$selectedCategory ? 'text-blue-200' : 'text-gray-500' }}">{{ $ebooks->total() }} {{ __('opac.universitaria_browse.documents') }}</p>
                </div>
            </button>

            @foreach($categories as $category)
            <button wire:click="selectCategory('{{ $category->slug }}')" 
                    class="group relative rounded-2xl p-5 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} transition-all duration-300 overflow-hidden
                           {{ $selectedCategory === $category->slug ? 'bg-gradient-to-br from-blue-600 to-indigo-700 text-white shadow-lg shadow-blue-500/25' : 'bg-white hover:bg-blue-50 border border-gray-100' }}">
                <div class="relative z-10">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-3
                                {{ $selectedCategory === $category->slug ? 'bg-white/20' : 'bg-gradient-to-br from-amber-100 to-orange-100' }}">
                        <i class="fas {{ $category->icon ?? 'fa-folder' }} text-lg {{ $selectedCategory === $category->slug ? 'text-white' : 'text-amber-600' }}"></i>
                    </div>
                    <h3 class="font-bold {{ $selectedCategory === $category->slug ? 'text-white' : 'text-gray-900' }}">{{ $category->name }}</h3>
                    <p class="text-sm {{ $selectedCategory === $category->slug ? 'text-blue-200' : 'text-gray-500' }}">{{ $category->ebooks_count }} {{ __('opac.universitaria_browse.documents') }}</p>
                </div>
            </button>
            @endforeach
        </div>

        {{-- Results --}}
        @if($ebooks->count() > 0)
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($ebooks as $ebook)
            <div class="group bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-xl hover:border-blue-200 transition-all duration-300 cursor-pointer"
                 x-on:click="openReader('{{ asset('storage/' . $ebook->file_path) }}', '{{ addslashes($ebook->title) }}', {{ $ebook->file_size ?? 0 }})">
                {{-- Cover --}}
                <div class="aspect-[3/4] bg-gradient-to-br from-blue-100 to-indigo-100 relative overflow-hidden">
                    @if($ebook->cover_image)
                        <img src="{{ $ebook->cover_url }}" alt="{{ $ebook->title }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center p-4">
                            <i class="fas fa-book-open text-4xl text-blue-300 mb-2"></i>
                            <p class="text-xs text-center text-blue-400 font-medium line-clamp-3">{{ $ebook->title }}</p>
                        </div>
                    @endif
                    
                    {{-- Protected Badge --}}
                    <div class="absolute top-2 {{ app()->getLocale() === 'ar' ? 'left-2' : 'right-2' }}">
                        <span class="px-2 py-1 bg-amber-500 text-white text-[10px] font-bold rounded-full flex items-center gap-1 shadow">
                            <i class="fas fa-crown text-[8px]"></i> Premium
                        </span>
                    </div>
                    
                    {{-- Play Button Overlay --}}
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 flex items-center justify-center transition-all">
                        <div class="w-14 h-14 bg-white/90 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transform scale-75 group-hover:scale-100 transition-all shadow-lg">
                            <i class="fas fa-book-reader text-blue-600 text-xl"></i>
                        </div>
                    </div>
                    
                    {{-- Year Badge --}}
                    @if($ebook->publish_year)
                    <div class="absolute bottom-2 {{ app()->getLocale() === 'ar' ? 'right-2' : 'left-2' }}">
                        <span class="px-2 py-1 bg-black/50 backdrop-blur text-white text-xs font-medium rounded-lg">
                            {{ $ebook->publish_year }}
                        </span>
                    </div>
                    @endif
                </div>
                
                {{-- Info --}}
                <div class="p-4">
                    <h3 class="font-semibold text-gray-900 text-sm line-clamp-2 group-hover:text-blue-600 transition">
                        {{ $ebook->title }}
                    </h3>
                    <div class="flex items-center gap-2 mt-2 text-xs text-gray-500">
                        <i class="fas fa-file-pdf text-red-500"></i>
                        <span>{{ $ebook->file_size }} MB</span>
                        <span class="text-amber-600"><i class="fas fa-eye"></i> {{ __('opac.universitaria_browse.read_online') }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $ebooks->links() }}
        </div>
        @else
        <div class="text-center py-16">
            <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-search text-3xl text-blue-400"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('opac.universitaria_browse.not_found') }}</h3>
            <p class="text-gray-500">{{ __('opac.universitaria_browse.try_other') }}</p>
        </div>
        @endif
    </div>

    {{-- PDF Reader Modal --}}
    <div x-show="showReader" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-hidden"
         style="display: none;">
        
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="closeReader()"></div>
        
        {{-- Modal Content --}}
        <div class="relative h-full flex flex-col">
            {{-- Header --}}
            <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-800 px-4 lg:px-6 py-3 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-book-open text-white"></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-bold text-white truncate" x-text="currentTitle"></h3>
                        <p class="text-blue-200 text-xs flex items-center gap-2">
                            <i class="fas fa-crown text-amber-400"></i>
                            <span>{{ __('opac.universitaria_browse.universitaria_collection') }}</span>
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="openInNewTab()" class="px-3 py-2 bg-white/10 hover:bg-white/20 text-white text-sm rounded-lg transition flex items-center gap-2">
                        <i class="fas fa-external-link-alt"></i>
                        <span class="hidden sm:inline">{{ __('opac.universitaria_browse.open_new_tab') }}</span>
                    </button>
                    <button @click="closeReader()" class="w-10 h-10 bg-white/10 hover:bg-red-500/80 text-white rounded-lg flex items-center justify-center transition">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
            
            {{-- PDF Viewer Container --}}
            <div class="flex-1 relative bg-gray-100">
                {{-- Loading State --}}
                <div x-show="loading" class="absolute inset-0 flex items-center justify-center bg-gray-100">
                    <div class="text-center">
                        <div class="w-12 h-12 border-4 border-blue-500/30 border-t-blue-500 rounded-full animate-spin mx-auto mb-3"></div>
                        <p class="text-gray-600 font-medium">{{ __('opac.universitaria_browse.loading') }}</p>
                    </div>
                </div>
                
                {{-- PDF Frame --}}
                <iframe 
                    x-ref="pdfFrame"
                    x-on:load="onFrameLoad()"
                    class="w-full h-full"
                    frameborder="0"
                    allowfullscreen>
                </iframe>
            </div>
        </div>
    </div>

</div>

<script>
function universitariaReader() {
    return {
        showReader: false,
        loading: true,
        currentUrl: '',
        currentTitle: '',
        
        openReader(url, title, size = 0) {
            this.currentUrl = url;
            this.currentTitle = title;
            this.showReader = true;
            this.loading = true;
            
            document.body.style.overflow = 'hidden';
            this.$nextTick(() => {
                const frame = this.$refs.pdfFrame;
                if (frame) {
                    frame.src = url + '#toolbar=1&navpanes=0&scrollbar=1';
                }
            });
        },
        
        closeReader() {
            this.showReader = false;
            if (this.$refs.pdfFrame) {
                this.$refs.pdfFrame.src = 'about:blank';
            }
            document.body.style.overflow = '';
        },
        
        onFrameLoad() {
            this.loading = false;
        },
        
        openInNewTab() {
            window.open(this.currentUrl, '_blank');
        }
    }
}
</script>
        }
    }
}
</script>
