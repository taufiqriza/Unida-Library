<div>
    <div class="lg:max-w-7xl lg:mx-auto lg:px-4 lg:py-8">
        
        {{-- Hero Section --}}
        <div class="relative bg-gradient-to-b from-blue-600 via-blue-700 to-indigo-800 lg:rounded-2xl lg:overflow-hidden">
            <div class="absolute inset-0 overflow-hidden opacity-30">
                <div class="w-full h-full bg-[url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.15\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
            </div>
            
            {{-- Back Button (Mobile) --}}
            <div class="relative z-10 px-4 pt-4 lg:hidden">
                <a href="{{ route('opac.search') }}?type=journal" class="inline-flex items-center gap-2 text-white/80 hover:text-white text-sm">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
            
            {{-- Breadcrumb (Desktop) --}}
            <nav class="hidden lg:block relative z-10 px-6 pt-6 text-sm text-white/70">
                <a href="{{ route('opac.home') }}" class="hover:text-white">Beranda</a>
                <span class="mx-2">/</span>
                <a href="{{ route('opac.search') }}?type=journal" class="hover:text-white">Artikel</a>
                <span class="mx-2">/</span>
                <span class="text-white">Detail</span>
            </nav>
            
            {{-- Cover & Basic Info --}}
            <div class="relative z-10 px-4 pb-6 pt-4 lg:p-8">
                <div class="flex flex-col items-center lg:flex-row lg:items-end gap-4 lg:gap-8">
                    {{-- Icon/Logo --}}
                    <div class="w-40 lg:w-52 flex-shrink-0">
                        <div class="aspect-[3/4] bg-white rounded-xl shadow-2xl overflow-hidden ring-4 ring-white/20 flex items-center justify-center">
                            <div class="text-center p-6">
                                <i class="fas fa-university text-5xl text-blue-600 mb-3"></i>
                                <p class="text-sm font-bold text-gray-700">Repository</p>
                                <p class="text-xs text-gray-500">UNIDA Gontor</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center lg:text-left flex-1">
                        {{-- Badge --}}
                        <span class="inline-block px-3 py-1 bg-white/20 rounded-full text-sm font-medium text-white mb-2">
                            <i class="fas fa-university mr-1"></i> Repository UNIDA
                        </span>
                        
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
                                <div class="text-xs text-blue-200">Tahun</div>
                            </div>
                            <div class="w-px h-10 bg-white/20"></div>
                            @endif
                            <div class="text-center">
                                <div class="text-2xl font-bold text-white">{{ number_format($article->views ?? 0) }}</div>
                                <div class="text-xs text-blue-200">Dilihat</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Content Cards --}}
        <div class="px-4 lg:px-0 mt-6 relative z-20 space-y-4 pb-8">
            
            {{-- Action Button --}}
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-4 py-3 bg-gradient-to-r from-blue-50 to-white border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-download text-blue-500"></i>
                        Akses Artikel
                    </h3>
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between p-4 bg-blue-50 border border-blue-200 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-university text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Buka di Repository UNIDA</p>
                                <p class="text-xs text-blue-600 flex items-center gap-1">
                                    <i class="fas fa-link"></i> repo.unida.gontor.ac.id
                                </p>
                            </div>
                        </div>
                        <a href="{{ $article->external_url ?? $article->url }}" target="_blank" rel="noopener" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                            <i class="fas fa-external-link-alt"></i>
                            <span class="hidden sm:inline">Buka</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Abstract --}}
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-file-alt text-blue-500"></i>
                        Abstrak
                    </h3>
                </div>
                <div class="p-4">
                    @if($article->abstract)
                        <p class="text-sm text-gray-600 leading-relaxed text-justify">{{ $article->abstract }}</p>
                    @else
                        <p class="text-gray-500 italic">Abstrak tidak tersedia</p>
                    @endif
                </div>
            </div>

            {{-- Detail Info --}}
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        Informasi Artikel
                    </h3>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        {{-- Jurnal --}}
                        @if($article->journal_name)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-book-open text-blue-600 text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-gray-500 uppercase tracking-wide">Jurnal</p>
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $article->journal_name }}</p>
                            </div>
                        </div>
                        @endif
                        
                        {{-- Volume --}}
                        @if($article->volume)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-layer-group text-purple-600 text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-gray-500 uppercase tracking-wide">Volume</p>
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
                                <p class="text-[10px] text-gray-500 uppercase tracking-wide">Nomor</p>
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
                                <p class="text-[10px] text-gray-500 uppercase tracking-wide">Halaman</p>
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
                                <p class="text-[10px] text-gray-500 uppercase tracking-wide">Tahun</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $article->publish_year }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Keywords --}}
            @if($article->keywords && count($article->keywords) > 0)
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-tags text-amber-500"></i>
                        Kata Kunci
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

            {{-- Repository Info --}}
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-100">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-university text-white"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">Repository UNIDA</h3>
                        <p class="text-xs text-gray-500">repo.unida.gontor.ac.id</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600">
                    Artikel ini bersumber dari Repository Universitas Darussalam Gontor.
                </p>
            </div>

            {{-- Share Button --}}
            <div class="bg-white rounded-2xl p-4 shadow-lg">
                <div class="flex gap-3">
                    <button onclick="navigator.share ? navigator.share({title: '{{ $article->title }}', url: window.location.href}) : navigator.clipboard.writeText(window.location.href).then(() => alert('Link disalin!'))" class="flex-1 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl flex items-center justify-center gap-2 hover:bg-gray-200 transition">
                        <i class="fas fa-share-alt"></i>
                        <span>Bagikan</span>
                    </button>
                    <a href="{{ route('opac.search') }}?type=journal" class="flex-1 py-3 bg-blue-600 text-white font-semibold rounded-xl flex items-center justify-center gap-2 hover:bg-blue-700 transition">
                        <i class="fas fa-search"></i>
                        <span>Cari Lagi</span>
                    </a>
                </div>
            </div>

            {{-- Related Articles --}}
            @if($related->count() > 0)
            <div class="pt-4">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Artikel Lainnya</h2>
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
