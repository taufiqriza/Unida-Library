<x-opac.layout :title="$book->title">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Breadcrumb -->
        <nav class="text-sm text-gray-500 mb-6">
            <a href="{{ route('opac.home') }}" class="hover:text-blue-600">Beranda</a>
            <span class="mx-2">/</span>
            <a href="{{ route('opac.catalog') }}" class="hover:text-blue-600">Katalog</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900">{{ Str::limit($book->title, 50) }}</span>
        </nav>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Cover -->
            <div class="md:col-span-1">
                <div class="bg-white rounded-xl p-4 shadow-lg shadow-gray-200/50">
                    <div class="aspect-[3/4] bg-gradient-to-br from-blue-100 to-blue-50 rounded-lg flex items-center justify-center overflow-hidden">
                        @if($book->cover_url)
                            <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                        @else
                            <i class="fas fa-book text-6xl text-blue-300"></i>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Detail -->
            <div class="md:col-span-2">
                <div class="bg-white rounded-xl p-6 shadow-lg shadow-gray-200/50">
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $book->title }}</h1>
                    <p class="text-gray-600 mb-4">{{ $book->author_names ?: '-' }}</p>

                    <div class="grid grid-cols-2 gap-4 text-sm mb-6">
                        <div><span class="text-gray-500">ISBN:</span> <span class="text-gray-900">{{ $book->isbn ?? '-' }}</span></div>
                        <div><span class="text-gray-500">Penerbit:</span> <span class="text-gray-900">{{ $book->publisher?->name ?? '-' }}</span></div>
                        <div><span class="text-gray-500">Tahun:</span> <span class="text-gray-900">{{ $book->publish_year ?? '-' }}</span></div>
                        <div><span class="text-gray-500">Subjek:</span> <span class="text-gray-900">{{ $book->subjects->pluck('name')->implode(', ') ?: '-' }}</span></div>
                        <div><span class="text-gray-500">Bahasa:</span> <span class="text-gray-900">{{ $book->language ?? '-' }}</span></div>
                        <div><span class="text-gray-500">Halaman:</span> <span class="text-gray-900">{{ $book->collation ?? '-' }}</span></div>
                        <div><span class="text-gray-500">No. Panggil:</span> <span class="text-gray-900">{{ $book->call_number ?? '-' }}</span></div>
                        <div><span class="text-gray-500">Edisi:</span> <span class="text-gray-900">{{ $book->edition ?? '-' }}</span></div>
                    </div>

                    @if($book->abstract)
                    <div class="border-t border-gray-100 pt-4">
                        <h3 class="font-semibold text-gray-900 mb-2">Abstrak</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $book->abstract }}</p>
                    </div>
                    @endif
                </div>

                <!-- Availability -->
                <div class="bg-white rounded-xl p-6 shadow-lg shadow-gray-200/50 mt-4">
                    <h3 class="font-semibold text-gray-900 mb-4"><i class="fas fa-map-marker-alt text-blue-500 mr-2"></i>Ketersediaan</h3>
                    @if($book->items->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-gray-600">No. Barcode</th>
                                    <th class="px-3 py-2 text-left text-gray-600">Lokasi</th>
                                    <th class="px-3 py-2 text-left text-gray-600">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($book->items as $item)
                                <tr>
                                    <td class="px-3 py-2 text-gray-900">{{ $item->barcode }}</td>
                                    <td class="px-3 py-2 text-gray-600">{{ $item->branch?->name ?? '-' }}</td>
                                    <td class="px-3 py-2">
                                        @if($item->status === 'available')
                                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">Tersedia</span>
                                        @elseif($item->status === 'borrowed')
                                            <span class="px-2 py-1 bg-orange-100 text-orange-700 rounded-full text-xs">Dipinjam</span>
                                        @else
                                            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs">{{ ucfirst($item->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-sm text-gray-500">Tidak ada eksemplar tersedia</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Related Books -->
        @if($relatedBooks->count() > 0)
        <div class="mt-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Buku Terkait</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($relatedBooks as $related)
                <a href="{{ route('opac.catalog.show', $related->id) }}" class="bg-white rounded-xl overflow-hidden shadow-lg shadow-gray-200/50 hover:shadow-xl transition group">
                    <div class="aspect-[3/4] bg-gradient-to-br from-blue-100 to-blue-50 flex items-center justify-center">
                        @if($related->cover_url)
                            <img src="{{ $related->cover_url }}" alt="{{ $related->title }}" class="w-full h-full object-cover">
                        @else
                            <i class="fas fa-book text-3xl text-blue-300"></i>
                        @endif
                    </div>
                    <div class="p-3">
                        <h3 class="font-semibold text-gray-900 text-sm line-clamp-2 group-hover:text-blue-600">{{ $related->title }}</h3>
                        <p class="text-xs text-gray-500 mt-1">{{ $related->author_names ?: '-' }}</p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</x-opac.layout>
