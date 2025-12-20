<div>
    <div class="lg:max-w-7xl lg:mx-auto lg:px-4 lg:py-8">
        
        {{-- Hero Section --}}
        <div class="relative bg-gradient-to-b from-blue-600 via-blue-700 to-indigo-800 lg:rounded-2xl lg:overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                @if($article->cover_url)
                    <img src="{{ $article->cover_url }}" class="w-full h-full object-cover opacity-20 blur-2xl scale-110">
                @endif
            </div>
            
            {{-- Back Button (Mobile) --}}
            <div class="relative z-10 px-4 pt-4 lg:hidden">
                <a href="{{ route('opac.journals.index') }}" class="inline-flex items-center gap-2 text-white/80 hover:text-white text-sm">
                    <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i>
                    <span>{{ __('opac.journal_show.back') }}</span>
                </a>
            </div>
            
            {{-- Breadcrumb (Desktop) --}}
            <nav class="hidden lg:block relative z-10 px-6 pt-6 text-sm text-white/70">
                <a href="{{ route('opac.home') }}" class="hover:text-white">{{ __('opac.journal_show.home') }}</a>
                <span class="mx-2">/</span>
                <a href="{{ route('opac.journals.index') }}" class="hover:text-white">{{ __('opac.journal_show.journal') }}</a>
                <span class="mx-2">/</span>
                <span class="text-white">{{ __('opac.journal_show.article_detail') }}</span>
            </nav>
            
            {{-- Cover & Basic Info --}}
            <div class="relative z-10 px-4 pb-6 pt-4 lg:p-8">
                <div class="flex flex-col items-center lg:flex-row lg:items-end gap-4 lg:gap-8">
                    {{-- Cover Image --}}
                    @if($article->cover_url)
                    <div class="w-40 lg:w-52 flex-shrink-0">
                        <div class="aspect-[3/4] bg-white rounded-xl shadow-2xl overflow-hidden ring-4 ring-white/20">
                            <img src="{{ $article->cover_url }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                        </div>
                    </div>
                    @endif
                    
                    <div class="text-center lg:text-left flex-1">
                        {{-- Journal Badge --}}
                        <div class="flex items-center justify-center lg:justify-start gap-2 mb-2">
                            <span class="px-3 py-1 bg-white/20 rounded-full text-sm font-medium text-white">
                                {{ $article->source?->name ?? $article->journal_name }}
                            </span>
                            @if($article->source?->sinta_rank)
                                <span class="px-2 py-1 bg-yellow-500 text-yellow-900 rounded-full text-xs font-bold">
                                    SINTA {{ $article->source->sinta_rank }}
                                </span>
                            @endif
                        </div>
                        
                        <h1 class="text-xl lg:text-3xl font-bold text-white leading-tight">{{ $article->title }}</h1>
                        
                        {{-- Authors --}}
                        @if($article->authors)
                        <p class="text-blue-200 mt-2 text-sm lg:text-base flex items-center justify-center lg:justify-start gap-2">
                            <i class="fas fa-users"></i>
                            {{ $article->authors_string }}
                        </p>
                        @endif
                        
                        <div class="flex items-center justify-center lg:justify-start gap-4 mt-4">
                            @if($article->publish_year)
                            <div class="text-center">
                                <div class="text-2xl font-bold text-white">{{ $article->publish_year }}</div>
                                <div class="text-xs text-blue-200">{{ __('opac.journal_show.year') }}</div>
                            </div>
                            <div class="w-px h-10 bg-white/20"></div>
                            @endif
                            <div class="text-center">
                                <div class="text-2xl font-bold text-white">{{ number_format($article->views) }}</div>
                                <div class="text-xs text-blue-200">{{ __('opac.journal_show.views') }}</div>
                            </div>
                            @if($article->volume)
                            <div class="w-px h-10 bg-white/20"></div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-white">Vol. {{ $article->volume }}</div>
                                <div class="text-xs text-blue-200">{{ __('opac.journal_show.volume') }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Content Cards --}}
        <div class="px-4 lg:px-0 mt-6 relative z-20 space-y-4 pb-8">
            
            {{-- Action Buttons --}}
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-4 py-3 bg-gradient-to-r from-blue-50 to-white border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-download text-blue-500"></i>
                        {{ __('opac.journal_show.access_article') }}
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    {{-- Open Journal --}}
                    <div class="flex items-center justify-between p-4 bg-blue-50 border border-blue-200 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-external-link-alt text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ __('opac.journal_show.open_in_journal') }}</p>
                                <p class="text-xs text-blue-600 flex items-center gap-1">
                                    <i class="fas fa-link"></i> {{ __('opac.journal_show.view_full_article') }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ $article->url }}" target="_blank" rel="noopener" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                            <i class="fas fa-external-link-alt"></i>
                            <span class="hidden sm:inline">{{ __('opac.journal_show.open') }}</span>
                        </a>
                    </div>

                    {{-- PDF --}}
                    @if($article->pdf_url)
                    <div class="flex items-center justify-between p-4 bg-red-50 border border-red-200 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-file-pdf text-red-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ __('opac.journal_show.download_pdf') }}</p>
                                <p class="text-xs text-red-600 flex items-center gap-1">
                                    <i class="fas fa-download"></i> {{ __('opac.journal_show.download_document') }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ $article->pdf_url }}" target="_blank" rel="noopener" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition flex items-center gap-2">
                            <i class="fas fa-download"></i>
                            <span class="hidden sm:inline">{{ __('opac.journal_show.download') }}</span>
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Abstract --}}
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-file-alt text-blue-500"></i>
                        {{ __('opac.journal_show.abstract') }}
                    </h3>
                </div>
                <div class="p-4">
                    @if($article->abstract)
                        <p class="text-sm text-gray-600 leading-relaxed text-justify">{{ $article->abstract }}</p>
                    @else
                        <p class="text-gray-500 italic">{{ __('opac.journal_show.abstract_not_available') }}</p>
                    @endif

                    @if($article->abstract_en)
                        <hr class="my-4">
                        <h4 class="font-semibold text-gray-800 mb-2">{{ __('opac.journal_show.abstract_en') }}</h4>
                        <p class="text-sm text-gray-600 leading-relaxed text-justify">{{ $article->abstract_en }}</p>
                    @endif
                </div>
            </div>

            {{-- Detail Info --}}
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        {{ __('opac.journal_show.article_info') }}
                    </h3>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        {{-- Jurnal --}}
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-book-open text-blue-600 text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-gray-500 uppercase tracking-wide">{{ __('opac.journal_show.journal') }}</p>
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $article->journal_name }}</p>
                            </div>
                        </div>
                        
                        {{-- Volume --}}
                        @if($article->volume)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-layer-group text-purple-600 text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-gray-500 uppercase tracking-wide">{{ __('opac.journal_show.volume') }}</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $article->volume }}</p>
                            </div>
                        </div>
                        @endif
                        
                        {{-- Issue --}}
                        @if($article->issue)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-hashtag text-emerald-600 text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-gray-500 uppercase tracking-wide">{{ __('opac.journal_show.issue') }}</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $article->issue }}</p>
                            </div>
                        </div>
                        @endif
                        
                        {{-- Pages --}}
                        @if($article->pages)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-file-alt text-amber-600 text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-gray-500 uppercase tracking-wide">{{ __('opac.journal_show.pages') }}</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $article->pages }}</p>
                            </div>
                        </div>
                        @endif
                        
                        {{-- Tahun --}}
                        @if($article->publish_year)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-rose-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-calendar text-rose-600 text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-gray-500 uppercase tracking-wide">{{ __('opac.journal_show.year') }}</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $article->publish_year }}</p>
                            </div>
                        </div>
                        @endif
                        
                        {{-- Tanggal Terbit --}}
                        @if($article->published_at)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-clock text-indigo-600 text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-gray-500 uppercase tracking-wide">{{ __('opac.journal_show.publish_date') }}</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $article->published_at->format('d M Y') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    {{-- DOI --}}
                    @if($article->doi)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-link text-cyan-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-[10px] text-gray-500 uppercase tracking-wide mb-1">DOI</p>
                                <a href="https://doi.org/{{ $article->doi }}" target="_blank" class="text-blue-600 hover:underline text-sm break-all">
                                    {{ $article->doi }}
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Keywords --}}
            @if($article->keywords && count($article->keywords) > 0)
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-tags text-amber-500"></i>
                        {{ __('opac.journal_show.keywords') }}
                    </h3>
                </div>
                <div class="p-4">
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($article->keywords as $keyword)
                            <span class="px-2.5 py-1 bg-blue-50 text-blue-700 text-xs font-medium rounded-lg border border-blue-100">{{ $keyword }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Journal Info --}}
            @if($article->source)
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-100">
                <h3 class="font-bold text-gray-900 mb-3">{{ $article->source->name }}</h3>
                @if($article->source->issn)
                    <p class="text-sm text-gray-600 mb-2">ISSN: {{ $article->source->issn }}</p>
                @endif
                @if($article->source->sinta_rank)
                    <span class="inline-block px-3 py-1 bg-yellow-400 text-yellow-900 rounded-full text-sm font-bold">
                        SINTA {{ $article->source->sinta_rank }}
                    </span>
                @endif
                <a href="{{ route('opac.journals.index', ['journal' => $article->journal_code]) }}" 
                   class="mt-4 block text-center px-4 py-2 bg-white text-blue-600 rounded-lg text-sm font-medium hover:bg-blue-600 hover:text-white transition border border-blue-200">
                    {{ __('opac.journal_show.view_all_articles') }}
                </a>
            </div>
            @endif

            {{-- Share Button --}}
            <div class="bg-white rounded-2xl p-4 shadow-lg">
                <div class="flex gap-3">
                    <button onclick="navigator.share ? navigator.share({title: '{{ $article->title }}', url: window.location.href}) : navigator.clipboard.writeText(window.location.href).then(() => alert('{{ __('opac.journal_show.link_copied') }}'))" class="flex-1 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl flex items-center justify-center gap-2 hover:bg-gray-200 transition">
                        <i class="fas fa-share-alt"></i>
                        <span>{{ __('opac.journal_show.share') }}</span>
                    </button>
                    <a href="{{ route('opac.journals.index') }}" class="flex-1 py-3 bg-blue-600 text-white font-semibold rounded-xl flex items-center justify-center gap-2 hover:bg-blue-700 transition">
                        <i class="fas fa-search"></i>
                        <span>{{ __('opac.journal_show.search_journal') }}</span>
                    </a>
                </div>
            </div>

            {{-- Related Articles --}}
            @if($related->count() > 0)
            <div class="pt-4">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900">{{ __('opac.journal_show.related_articles') }}</h2>
                </div>
                <div class="flex gap-3 overflow-x-auto pb-2 -mx-4 px-4 lg:mx-0 lg:px-0 scrollbar-hide">
                    @foreach($related as $rel)
                    <a href="{{ route('opac.journals.show', $rel) }}" class="flex-shrink-0 w-64 bg-white rounded-xl shadow-lg overflow-hidden group">
                        <div class="p-4">
                            <h3 class="text-sm font-medium text-gray-900 line-clamp-2 group-hover:text-blue-600 transition">{{ $rel->title }}</h3>
                            <p class="text-xs text-gray-500 mt-2">{{ $rel->publish_year }}</p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
            
        </div>
    </div>
</div>
