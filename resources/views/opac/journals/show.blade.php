<x-opac.layout title="{{ $article->title }} - Jurnal">
    <div class="min-h-screen bg-gray-50">
        {{-- Header --}}
        <div class="bg-gradient-to-br from-purple-600 via-purple-700 to-indigo-800 text-white">
            <div class="max-w-5xl mx-auto px-4 py-8">
                {{-- Breadcrumb --}}
                <nav class="flex items-center gap-2 text-sm text-purple-200 mb-6">
                    <a href="{{ route('opac.home') }}" class="hover:text-white">Beranda</a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <a href="{{ route('opac.journals.index') }}" class="hover:text-white">Jurnal</a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <span class="text-white">Detail Artikel</span>
                </nav>

                {{-- Journal Badge --}}
                <div class="flex items-center gap-3 mb-4">
                    <span class="px-3 py-1 bg-white/20 rounded-full text-sm font-medium">
                        {{ $article->source?->name ?? $article->journal_name }}
                    </span>
                    @if($article->source?->sinta_rank)
                        <span class="px-2 py-1 bg-yellow-500 text-yellow-900 rounded-full text-xs font-bold">
                            SINTA {{ $article->source->sinta_rank }}
                        </span>
                    @endif
                </div>

                {{-- Title --}}
                <h1 class="text-2xl lg:text-3xl font-bold leading-tight mb-4">
                    {{ $article->title }}
                </h1>

                {{-- Authors --}}
                @if($article->authors)
                <div class="flex items-center gap-2 text-purple-100">
                    <i class="fas fa-users"></i>
                    <span>{{ $article->authors_string }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Content --}}
        <div class="max-w-5xl mx-auto px-4 py-8">
            <div class="grid lg:grid-cols-3 gap-8">
                {{-- Main Content --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Cover Image --}}
                    @if($article->cover_url)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <img src="{{ $article->cover_url }}" alt="{{ $article->title }}" class="w-full max-h-96 object-contain bg-gray-50">
                    </div>
                    @endif

                    {{-- Abstract --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-file-alt text-purple-500"></i> Abstrak
                        </h2>
                        @if($article->abstract)
                            <p class="text-gray-700 leading-relaxed text-justify">
                                {{ $article->abstract }}
                            </p>
                        @else
                            <p class="text-gray-500 italic">Abstrak tidak tersedia</p>
                        @endif

                        @if($article->abstract_en)
                            <hr class="my-4">
                            <h3 class="font-semibold text-gray-800 mb-2">Abstract (English)</h3>
                            <p class="text-gray-700 leading-relaxed text-justify">
                                {{ $article->abstract_en }}
                            </p>
                        @endif
                    </div>

                    {{-- Keywords --}}
                    @if($article->keywords && count($article->keywords) > 0)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-tags text-purple-500"></i> Kata Kunci
                        </h2>
                        <div class="flex flex-wrap gap-2">
                            @foreach($article->keywords as $keyword)
                                <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm">
                                    {{ $keyword }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Action Buttons --}}
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ $article->url }}" target="_blank" rel="noopener" 
                           class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-6 py-3 bg-purple-600 text-white rounded-xl font-semibold hover:bg-purple-700 transition shadow-lg shadow-purple-600/30">
                            <i class="fas fa-external-link-alt"></i>
                            Buka di Open Journal
                        </a>
                        @if($article->pdf_url)
                        <a href="{{ $article->pdf_url }}" target="_blank" rel="noopener"
                           class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-6 py-3 bg-red-600 text-white rounded-xl font-semibold hover:bg-red-700 transition">
                            <i class="fas fa-file-pdf"></i>
                            Download PDF
                        </a>
                        @endif
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="space-y-6">
                    {{-- Article Info --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-bold text-gray-900 mb-4">Informasi Artikel</h3>
                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Jurnal</dt>
                                <dd class="font-medium text-gray-900">{{ $article->journal_name }}</dd>
                            </div>
                            @if($article->volume)
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Volume</dt>
                                <dd class="font-medium text-gray-900">{{ $article->volume }}</dd>
                            </div>
                            @endif
                            @if($article->issue)
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Nomor</dt>
                                <dd class="font-medium text-gray-900">{{ $article->issue }}</dd>
                            </div>
                            @endif
                            @if($article->pages)
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Halaman</dt>
                                <dd class="font-medium text-gray-900">{{ $article->pages }}</dd>
                            </div>
                            @endif
                            @if($article->publish_year)
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Tahun</dt>
                                <dd class="font-medium text-gray-900">{{ $article->publish_year }}</dd>
                            </div>
                            @endif
                            @if($article->published_at)
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Tanggal Terbit</dt>
                                <dd class="font-medium text-gray-900">{{ $article->published_at->format('d M Y') }}</dd>
                            </div>
                            @endif
                            @if($article->doi)
                            <div class="pt-2 border-t">
                                <dt class="text-gray-500 mb-1">DOI</dt>
                                <dd>
                                    <a href="https://doi.org/{{ $article->doi }}" target="_blank" class="text-blue-600 hover:underline text-xs break-all">
                                        {{ $article->doi }}
                                    </a>
                                </dd>
                            </div>
                            @endif
                            <div class="flex justify-between pt-2 border-t">
                                <dt class="text-gray-500">Dilihat</dt>
                                <dd class="font-medium text-gray-900">{{ number_format($article->views) }}x</dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Journal Info --}}
                    @if($article->source)
                    <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl p-6 border border-purple-100">
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
                           class="mt-4 block text-center px-4 py-2 bg-white text-purple-600 rounded-lg text-sm font-medium hover:bg-purple-600 hover:text-white transition border border-purple-200">
                            Lihat Semua Artikel
                        </a>
                    </div>
                    @endif

                    {{-- Related Articles --}}
                    @if($related->count() > 0)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-bold text-gray-900 mb-4">Artikel Terkait</h3>
                        <div class="space-y-3">
                            @foreach($related as $rel)
                            <a href="{{ route('opac.journals.show', $rel) }}" class="block group">
                                <h4 class="text-sm font-medium text-gray-900 group-hover:text-purple-600 line-clamp-2">
                                    {{ $rel->title }}
                                </h4>
                                <p class="text-xs text-gray-500 mt-1">{{ $rel->publish_year }}</p>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-opac.layout>
