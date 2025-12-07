<x-opac.layout :title="$news->title">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Breadcrumb -->
        <nav class="text-sm text-gray-500 mb-6">
            <a href="{{ route('opac.home') }}" class="hover:text-blue-600">Beranda</a>
            <span class="mx-2">/</span>
            <a href="{{ route('opac.news') }}" class="hover:text-blue-600">Berita</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900">{{ Str::limit($news->title, 40) }}</span>
        </nav>

        <article class="bg-white rounded-xl shadow-lg shadow-gray-200/50 overflow-hidden">
            @if($news->image_url)
            <div class="aspect-video">
                <img src="{{ $news->image_url }}" alt="{{ $news->title }}" class="w-full h-full object-cover">
            </div>
            @endif
            
            <div class="p-6 md:p-8">
                <p class="text-sm text-gray-400 mb-2">{{ $news->published_at?->format('d F Y') }}</p>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">{{ $news->title }}</h1>
                <div class="prose prose-blue max-w-none text-gray-600">
                    {!! nl2br(e($news->content)) !!}
                </div>
            </div>
        </article>

        <!-- Recent News -->
        @if($recentNews->count() > 0)
        <div class="mt-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Berita Lainnya</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($recentNews as $item)
                <a href="{{ route('opac.news.show', $item->slug) }}" class="flex gap-4 bg-white rounded-xl p-4 shadow-lg shadow-gray-200/50 hover:shadow-xl transition group">
                    <div class="w-24 h-16 bg-gradient-to-br from-emerald-100 to-emerald-50 rounded-lg flex-shrink-0 flex items-center justify-center overflow-hidden">
                        @if($item->image_url)
                            <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                        @else
                            <i class="fas fa-newspaper text-emerald-300"></i>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-400">{{ $item->published_at?->format('d M Y') }}</p>
                        <h3 class="font-semibold text-gray-900 line-clamp-2 group-hover:text-blue-600">{{ $item->title }}</h3>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</x-opac.layout>
