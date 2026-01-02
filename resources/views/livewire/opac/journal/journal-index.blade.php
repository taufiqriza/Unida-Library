<div>

@section('title', 'Jurnal Ilmiah')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-purple-50">
    
    <!-- Hero Header -->
    <div class="bg-gradient-to-r from-purple-600 via-indigo-600 to-blue-600 text-white">
        <div class="max-w-7xl mx-auto px-4 py-12">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-14 h-14 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center">
                    <i class="fas fa-file-lines text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold">Jurnal Ilmiah UNIDA Gontor</h1>
                    <p class="text-purple-100">{{ $sources->count() }} Jurnal • {{ $articles->total() }} Artikel</p>
                </div>
            </div>
            
            <!-- Search -->
            <form method="GET" class="mt-6">
                <div class="flex gap-3">
                    <div class="flex-1 relative">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="q" value="{{ request('q') }}" 
                            placeholder="Cari judul artikel, abstrak, atau penulis..."
                            class="w-full pl-12 pr-4 py-3 rounded-xl border-0 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-white/50">
                    </div>
                    <select name="journal" class="px-4 py-3 rounded-xl border-0 text-gray-900 bg-white min-w-[200px]">
                        <option value="">Semua Jurnal</option>
                        @foreach($sources as $source)
                            <option value="{{ $source->code }}" {{ request('journal') == $source->code ? 'selected' : '' }}>
                                {{ $source->name }}
                            </option>
                        @endforeach
                    </select>
                    <select name="year" class="px-4 py-3 rounded-xl border-0 text-gray-900 bg-white">
                        <option value="">Semua Tahun</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                    <select name="type" class="px-4 py-3 rounded-xl border-0 text-gray-900 bg-white">
                        <option value="">Semua Jenis</option>
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-6 py-3 bg-white text-purple-600 font-semibold rounded-xl hover:bg-purple-50 transition">
                        <i class="fas fa-search mr-2"></i>Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex gap-8">
            
            <!-- Sidebar: Journal List -->
            <div class="hidden lg:block w-72 flex-shrink-0 space-y-4">
                <!-- Type Filter -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
                    <div class="p-4 border-b border-gray-100">
                        <h3 class="font-bold text-gray-900">Jenis Publikasi</h3>
                    </div>
                    <div class="p-2">
                        <a href="{{ route('opac.journals.index', array_filter(['q' => request('q'), 'journal' => request('journal'), 'year' => request('year')])) }}" 
                           class="flex items-center justify-between px-3 py-2 rounded-lg {{ !request('type') ? 'bg-purple-50 text-purple-700' : 'hover:bg-gray-50' }}">
                            <span class="font-medium">Semua Jenis</span>
                            <span class="text-xs bg-gray-100 px-2 py-0.5 rounded-full">{{ $typeCounts->sum() }}</span>
                        </a>
                        @foreach($types as $key => $label)
                        <a href="{{ route('opac.journals.index', array_filter(['q' => request('q'), 'journal' => request('journal'), 'year' => request('year'), 'type' => $key])) }}" 
                           class="flex items-center justify-between px-3 py-2 rounded-lg {{ request('type') == $key ? 'bg-purple-50 text-purple-700' : 'hover:bg-gray-50' }}">
                            <span class="text-sm">{{ $label }}</span>
                            <span class="text-xs bg-gray-100 px-2 py-0.5 rounded-full">{{ $typeCounts[$key] ?? 0 }}</span>
                        </a>
                        @endforeach
                    </div>
                </div>

                <!-- Journal List -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm sticky top-24">
                    <div class="p-4 border-b border-gray-100">
                        <h3 class="font-bold text-gray-900">Daftar Jurnal</h3>
                    </div>
                    <div class="max-h-[60vh] overflow-y-auto p-2">
                        <a href="{{ route('opac.journals.index') }}" 
                           class="flex items-center justify-between px-3 py-2 rounded-lg {{ !request('journal') ? 'bg-purple-50 text-purple-700' : 'hover:bg-gray-50' }}">
                            <span class="font-medium">Semua Jurnal</span>
                            <span class="text-xs bg-gray-100 px-2 py-0.5 rounded-full">{{ $sources->sum('articles_count') }}</span>
                        </a>
                        @foreach($sources as $source)
                        <a href="{{ route('opac.journals.index', ['journal' => $source->code]) }}" 
                           class="flex items-center justify-between px-3 py-2 rounded-lg {{ request('journal') == $source->code ? 'bg-purple-50 text-purple-700' : 'hover:bg-gray-50' }}">
                            <span class="text-sm truncate" title="{{ $source->name }}">{{ $source->name }}</span>
                            <span class="text-xs bg-gray-100 px-2 py-0.5 rounded-full flex-shrink-0">{{ $source->articles_count }}</span>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex-1 min-w-0">
                
                @if(request('q') || request('journal') || request('year') || request('type'))
                <div class="mb-4 flex items-center gap-2 flex-wrap">
                    <span class="text-sm text-gray-500">Filter aktif:</span>
                    @if(request('q'))
                        <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm">
                            "{{ request('q') }}"
                            <a href="{{ route('opac.journals.index', array_filter(['journal' => request('journal'), 'year' => request('year'), 'type' => request('type')])) }}" class="ml-1 hover:text-purple-900">&times;</a>
                        </span>
                    @endif
                    @if(request('type'))
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">
                            {{ $types[request('type')] ?? request('type') }}
                            <a href="{{ route('opac.journals.index', array_filter(['q' => request('q'), 'journal' => request('journal'), 'year' => request('year')])) }}" class="ml-1 hover:text-green-900">&times;</a>
                        </span>
                    @endif
                    @if(request('journal'))
                        <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm">
                            {{ $sources->firstWhere('code', request('journal'))?->name }}
                            <a href="{{ route('opac.journals.index', array_filter(['q' => request('q'), 'year' => request('year')])) }}" class="ml-1 hover:text-indigo-900">&times;</a>
                        </span>
                    @endif
                    @if(request('year'))
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm">
                            {{ request('year') }}
                            <a href="{{ route('opac.journals.index', array_filter(['q' => request('q'), 'journal' => request('journal')])) }}" class="ml-1 hover:text-blue-900">&times;</a>
                        </span>
                    @endif
                    <a href="{{ route('opac.journals.index') }}" class="text-sm text-gray-500 hover:text-gray-700 ml-2">Reset semua</a>
                </div>
                @endif

                <!-- Results -->
                @if($articles->count() > 0)
                <div class="space-y-4">
                    @foreach($articles as $article)
                    <article class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-lg hover:border-purple-200 transition group">
                        <div class="flex gap-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center text-white flex-shrink-0">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <a href="{{ $article->url }}" target="_blank" rel="noopener" 
                                           class="text-lg font-semibold text-gray-900 hover:text-purple-600 line-clamp-2 group-hover:text-purple-600">
                                            {{ $article->title }}
                                            <i class="fas fa-external-link-alt text-xs ml-1 opacity-50"></i>
                                        </a>
                                        <p class="text-sm text-gray-600 mt-1">
                                            <i class="fas fa-user-pen text-gray-400 mr-1"></i>
                                            {{ $article->authors_string ?: 'Penulis tidak tersedia' }}
                                        </p>
                                    </div>
                                    <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-medium flex-shrink-0">
                                        {{ $article->journal_name }}
                                    </span>
                                </div>
                                
                                @if($article->abstract)
                                <p class="text-sm text-gray-500 mt-3 line-clamp-2">{{ Str::limit(strip_tags($article->abstract), 200) }}</p>
                                @endif
                                
                                <div class="flex items-center gap-4 mt-3 text-xs text-gray-400">
                                    @if($article->publish_year)
                                    <span><i class="fas fa-calendar mr-1"></i>{{ $article->publish_year }}</span>
                                    @endif
                                    @if($article->doi)
                                    <span><i class="fas fa-link mr-1"></i>{{ $article->doi }}</span>
                                    @endif
                                    <a href="{{ $article->url }}" target="_blank" rel="noopener" class="text-purple-500 hover:text-purple-700 ml-auto">
                                        Baca di OJS <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </article>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $articles->withQueryString()->links() }}
                </div>
                @else
                <div class="text-center py-16">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-search text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Tidak ada artikel ditemukan</h3>
                    <p class="text-gray-500 mt-1">Coba ubah kata kunci atau filter pencarian</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
</div>
