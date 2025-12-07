<x-opac.layout title="Berita">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6"><i class="fas fa-newspaper text-emerald-500 mr-2"></i>Berita & Pengumuman</h1>

        @if($news->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($news as $item)
            <a href="{{ route('opac.news.show', $item->slug) }}" class="bg-white rounded-xl overflow-hidden shadow-lg shadow-gray-200/50 hover:shadow-xl transition group">
                <div class="aspect-video bg-gradient-to-br from-emerald-100 to-emerald-50 flex items-center justify-center">
                    @if($item->image_url)
                        <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                    @else
                        <i class="fas fa-newspaper text-4xl text-emerald-300"></i>
                    @endif
                </div>
                <div class="p-4">
                    <p class="text-xs text-gray-400 mb-2">{{ $item->published_at?->format('d M Y') }}</p>
                    <h3 class="font-semibold text-gray-900 line-clamp-2 group-hover:text-blue-600">{{ $item->title }}</h3>
                    <p class="text-sm text-gray-500 mt-2 line-clamp-2">{{ $item->excerpt }}</p>
                </div>
            </a>
            @endforeach
        </div>
        <div class="mt-6">{{ $news->links() }}</div>
        @else
        <div class="text-center py-12 bg-white rounded-xl">
            <i class="fas fa-newspaper text-4xl text-gray-300 mb-4"></i>
            <p class="text-gray-500">Belum ada berita</p>
        </div>
        @endif
    </div>
</x-opac.layout>
