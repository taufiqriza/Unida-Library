<x-opac.layout title="E-Thesis">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900"><i class="fas fa-graduation-cap text-pink-500 mr-2"></i>Koleksi E-Thesis</h1>
        </div>

        <!-- Search -->
        <div class="bg-white rounded-xl p-4 shadow-lg shadow-gray-200/50 mb-6">
            <form action="{{ route('opac.etheses') }}" method="GET" class="flex gap-3">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul atau penulis..." class="flex-1 px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-blue-500">
                <button type="submit" class="px-6 py-2 gradient-blue text-white rounded-lg"><i class="fas fa-search"></i></button>
            </form>
        </div>

        @if($theses->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($theses as $thesis)
            <div class="bg-white rounded-xl p-4 shadow-lg shadow-gray-200/50 hover:shadow-xl transition">
                <div class="flex gap-4">
                    <div class="w-16 h-20 bg-gradient-to-br from-pink-100 to-pink-50 rounded-lg flex-shrink-0 flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-xl text-pink-400"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="inline-block px-2 py-0.5 bg-pink-100 text-pink-600 rounded text-xs mb-1">{{ $thesis->getTypeLabel() }}</span>
                        <h3 class="font-semibold text-gray-900 line-clamp-2">{{ $thesis->title }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ $thesis->author }}</p>
                        <div class="flex items-center gap-3 mt-2 text-xs text-gray-400">
                            <span><i class="fas fa-calendar mr-1"></i>{{ $thesis->year }}</span>
                            @if($thesis->department)
                            <span><i class="fas fa-building mr-1"></i>{{ $thesis->department->name }}</span>
                            @endif
                        </div>
                        @if($thesis->is_fulltext_public && $thesis->file_path)
                        <a href="{{ asset('storage/' . $thesis->file_path) }}" target="_blank" class="inline-flex items-center gap-1 mt-2 px-3 py-1 bg-pink-100 text-pink-600 rounded-lg text-sm hover:bg-pink-200 transition">
                            <i class="fas fa-eye"></i> Lihat
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $theses->links() }}</div>
        @else
        <div class="text-center py-12 bg-white rounded-xl">
            <i class="fas fa-graduation-cap text-4xl text-gray-300 mb-4"></i>
            <p class="text-gray-500">Tidak ada e-thesis ditemukan</p>
        </div>
        @endif
    </div>
</x-opac.layout>
