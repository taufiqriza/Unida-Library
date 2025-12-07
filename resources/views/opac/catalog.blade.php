<x-opac.layout title="Katalog">
    <div class="max-w-7xl mx-auto px-3 lg:px-4 py-4 lg:py-8">
        <!-- Search & Filter -->
        <div class="bg-white rounded-lg lg:rounded-xl p-3 lg:p-4 shadow-lg shadow-gray-200/50 mb-4">
            <form action="{{ route('opac.catalog') }}" method="GET" class="flex flex-col lg:flex-row gap-2 lg:gap-3">
                <div class="flex-1">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul, pengarang, ISBN..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-blue-500">
                </div>
                <div class="grid grid-cols-3 gap-2 lg:flex lg:gap-3">
                    <select name="branch" class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-blue-500">
                        <option value="">Semua Lokasi</option>
                        @foreach($branches as $br)
                            <option value="{{ $br->id }}" {{ request('branch') == $br->id ? 'selected' : '' }}>{{ $br->name }}</option>
                        @endforeach
                    </select>
                    <select name="subject" class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-blue-500">
                        <option value="">Semua Subjek</option>
                        @foreach($subjects as $subj)
                            <option value="{{ $subj->id }}" {{ request('subject') == $subj->id ? 'selected' : '' }}>{{ $subj->name }}</option>
                        @endforeach
                    </select>
                    <select name="sort" class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-blue-500">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Judul A-Z</option>
                        <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Terpopuler</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 gradient-blue text-white text-sm rounded-lg"><i class="fas fa-search mr-1"></i>Cari</button>
            </form>
        </div>

        <!-- Results -->
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs lg:text-sm text-gray-500">{{ $books->total() }} buku ditemukan</p>
        </div>

        @if($books->count() > 0)
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
            @foreach($books as $book)
            <a href="{{ route('opac.catalog.show', $book->id) }}" class="bg-white rounded-xl overflow-hidden shadow-lg shadow-gray-200/50 hover:shadow-xl hover:-translate-y-1 transition group">
                <div class="aspect-[3/4] bg-gradient-to-br from-blue-100 to-blue-50 flex items-center justify-center">
                    @if($book->cover_url)
                        <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                    @else
                        <i class="fas fa-book text-4xl text-blue-300"></i>
                    @endif
                </div>
                <div class="p-3">
                    <h3 class="font-semibold text-gray-900 text-sm line-clamp-2 group-hover:text-blue-600">{{ $book->title }}</h3>
                    <p class="text-xs text-gray-500 mt-1 line-clamp-1">{{ $book->author_names ?: '-' }}</p>
                    <p class="text-xs text-blue-600 mt-1"><i class="fas fa-copy mr-1"></i>{{ $book->items_count }} eksemplar</p>
                </div>
            </a>
            @endforeach
        </div>

        <div class="mt-6">{{ $books->links() }}</div>
        @else
        <div class="text-center py-12">
            <i class="fas fa-search text-4xl text-gray-300 mb-4"></i>
            <p class="text-gray-500">Tidak ada buku ditemukan</p>
        </div>
        @endif
    </div>
</x-opac.layout>
