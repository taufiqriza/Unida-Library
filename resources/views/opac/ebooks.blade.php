<x-opac.layout title="E-Book">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900"><i class="fas fa-file-pdf text-orange-500 mr-2"></i>Koleksi E-Book</h1>
        </div>

        <!-- Search -->
        <div class="bg-white rounded-xl p-4 shadow-lg shadow-gray-200/50 mb-6">
            <form action="{{ route('opac.ebooks') }}" method="GET" class="flex gap-3">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari e-book..." class="flex-1 px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-blue-500">
                <button type="submit" class="px-6 py-2 gradient-blue text-white rounded-lg"><i class="fas fa-search"></i></button>
            </form>
        </div>

        @if($books->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($books as $book)
            <div class="bg-white rounded-xl p-4 shadow-lg shadow-gray-200/50 hover:shadow-xl transition">
                <div class="flex gap-4">
                    <div class="w-20 h-28 bg-gradient-to-br from-orange-100 to-orange-50 rounded-lg flex-shrink-0 flex items-center justify-center overflow-hidden">
                        @if($book->cover_image)
                            <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                        @else
                            <i class="fas fa-file-pdf text-2xl text-orange-400"></i>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 line-clamp-2">{{ $book->title }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ $book->authors->pluck('name')->implode(', ') ?: '-' }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $book->publish_year }}</p>
                        @if($book->file_path)
                        <a href="{{ asset('storage/' . $book->file_path) }}" target="_blank" class="inline-flex items-center gap-1 mt-2 px-3 py-1 bg-orange-100 text-orange-600 rounded-lg text-sm hover:bg-orange-200 transition">
                            <i class="fas fa-download"></i> Unduh
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $books->links() }}</div>
        @else
        <div class="text-center py-12 bg-white rounded-xl">
            <i class="fas fa-file-pdf text-4xl text-gray-300 mb-4"></i>
            <p class="text-gray-500">Tidak ada e-book ditemukan</p>
        </div>
        @endif
    </div>
</x-opac.layout>
