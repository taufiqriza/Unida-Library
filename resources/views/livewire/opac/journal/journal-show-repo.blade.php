<div>
<x-opac.layout title="{{ $article->title }} - Repository">
    <div class="min-h-screen bg-gray-50">
        {{-- Header --}}
        <div class="bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-800 text-white">
            <div class="max-w-5xl mx-auto px-4 py-8">
                {{-- Breadcrumb --}}
                <nav class="flex items-center gap-2 text-sm text-indigo-200 mb-6">
                    <a href="{{ route('opac.home') }}" class="hover:text-white">Beranda</a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <a href="{{ route('opac.search') }}?type=journal" class="hover:text-white">Artikel</a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <span class="text-white">Detail</span>
                </nav>

                {{-- Badge --}}
                <div class="flex items-center gap-3 mb-4">
                    <span class="px-3 py-1 bg-white/20 rounded-full text-sm font-medium">
                        <i class="fas fa-university mr-1"></i> Repository UNIDA
                    </span>
                </div>

                {{-- Title --}}
                <h1 class="text-2xl lg:text-3xl font-bold leading-tight mb-4">
                    {{ $article->title }}
                </h1>

                {{-- Authors --}}
                @if($article->authors)
                <div class="flex items-center gap-2 text-indigo-100">
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
                    {{-- Abstract --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-file-alt text-indigo-500"></i> Abstrak
                        </h2>
                        @if($article->abstract)
                            <p class="text-gray-700 leading-relaxed text-justify">
                                {{ $article->abstract }}
                            </p>
                        @else
                            <p class="text-gray-500 italic">Abstrak tidak tersedia</p>
                        @endif
                    </div>

                    {{-- Keywords --}}
                    @if($article->keywords && count($article->keywords) > 0)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-tags text-indigo-500"></i> Kata Kunci
                        </h2>
                        <div class="flex flex-wrap gap-2">
                            @foreach($article->keywords as $keyword)
                                <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm">
                                    {{ $keyword }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Action Button --}}
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ $article->external_url ?? $article->url }}" target="_blank" rel="noopener" 
                           class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-6 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition shadow-lg shadow-indigo-600/30">
                            <i class="fas fa-external-link-alt"></i>
                            Buka di Repository UNIDA
                        </a>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="space-y-6">
                    {{-- Article Info --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-bold text-gray-900 mb-4">Informasi Artikel</h3>
                        <dl class="space-y-3 text-sm">
                            @if($article->journal_name)
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Jurnal</dt>
                                <dd class="font-medium text-gray-900 text-right">{{ $article->journal_name }}</dd>
                            </div>
                            @endif
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
                            <div class="flex justify-between pt-2 border-t">
                                <dt class="text-gray-500">Dilihat</dt>
                                <dd class="font-medium text-gray-900">{{ number_format($article->views ?? 0) }}x</dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Source Info --}}
                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl p-6 border border-indigo-100">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center">
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

                    {{-- Related Articles --}}
                    @if($related->count() > 0)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-bold text-gray-900 mb-4">Artikel Lainnya</h3>
                        <div class="space-y-3">
                            @foreach($related as $rel)
                            <a href="{{ route('opac.journals.show', $rel) }}" class="block group">
                                <h4 class="text-sm font-medium text-gray-900 group-hover:text-indigo-600 line-clamp-2">
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
