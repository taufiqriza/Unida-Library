{{-- Tab Bibliografi --}}
@php
$config = ['gradient' => 'from-blue-500 to-indigo-600', 'bg' => 'blue', 'icon' => 'fa-book'];
@endphp

@if($books->count() > 0)
    @if($viewMode === 'list')
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gradient-to-r {{ $config['gradient'] }} text-white">
                <tr>
                    <th class="px-4 py-3 text-left font-medium w-20">Cover</th>
                    <th class="px-4 py-3 text-left font-medium">Judul & Info</th>
                    <th class="px-4 py-3 text-center font-medium w-16">Tahun</th>
                    <th class="px-4 py-3 text-left font-medium w-36">Media</th>
                    <th class="px-4 py-3 text-left font-medium w-40">Input By</th>
                    <th class="px-4 py-3 text-center font-medium w-24">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($books as $book)
                <tr class="hover:bg-blue-50/30 transition">
                    <td class="px-4 py-3">
                        <div class="relative w-14 h-20">
                            <div class="w-full h-full bg-gray-100 rounded-lg overflow-hidden">
                                @if($book->image)
                                <img src="{{ asset('storage/' . $book->image) }}" class="w-full h-full object-cover" alt="">
                                @else
                                <div class="w-full h-full flex items-center justify-center text-gray-300"><i class="fas fa-book"></i></div>
                                @endif
                            </div>
                            <div class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-gradient-to-r {{ $config['gradient'] }} rounded-full flex items-center justify-center text-white text-[10px] font-bold border-2 border-white shadow">{{ $book->items_count }}</div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('staff.biblio.edit', $book->id) }}" class="font-semibold text-gray-900 hover:text-blue-600 transition line-clamp-1">{{ $book->title }}</a>
                        <div class="flex flex-wrap gap-1 mt-1">
                            @foreach($book->authors->take(2) as $author)
                            <span class="text-xs text-purple-600 bg-purple-50 px-1.5 py-0.5 rounded">{{ $author->name }}</span>
                            @endforeach
                        </div>
                        <div class="flex flex-wrap items-center gap-3 mt-1.5 text-xs text-gray-500">
                            <span><i class="fas fa-barcode mr-1"></i>{{ $book->isbn ?: '-' }}</span>
                            <span class="font-mono bg-gray-100 px-1.5 py-0.5 rounded">{{ $book->call_number ?: '-' }}</span>
                            @if($book->publisher)<span><i class="fas fa-building mr-1"></i>{{ $book->publisher->name }}</span>@endif
                        </div>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-sm font-medium text-gray-700">{{ $book->publish_year ?: '-' }}</span>
                    </td>
                    <td class="px-4 py-3">
                        @if($book->mediaType)
                        <span class="text-xs px-2 py-1 bg-violet-100 text-violet-700 rounded-full">{{ $book->mediaType->name }}</span>
                        @else
                        <span class="text-xs text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($book->user)
                        <p class="text-xs text-gray-700 truncate">{{ $book->user->name }}</p>
                        <p class="text-[10px] text-gray-400">{{ $book->created_at?->format('d M Y') }}</p>
                        @else
                        <p class="text-xs text-gray-500 truncate">{{ $book->branch?->name ?? '-' }}</p>
                        <p class="text-[10px] text-gray-400">{{ $book->created_at?->format('d M Y') }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-1">
                            <button wire:click="quickView({{ $book->id }})" class="p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition" title="Lihat"><i class="fas fa-eye"></i></button>
                            <a href="{{ route('staff.biblio.edit', $book->id) }}" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit"><i class="fas fa-edit"></i></a>
                            <button wire:click="confirmDelete({{ $book->id }}, 'book')" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus"><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 p-4">
        @foreach($books as $book)
        <div class="bg-gray-50 rounded-xl group relative">
            <div class="absolute -top-1.5 -right-1.5 z-10 px-2 py-0.5 bg-gradient-to-r {{ $config['gradient'] }} rounded-full text-white text-xs font-bold shadow border-2 border-white">{{ $book->items_count }}</div>
            <div class="relative aspect-[3/4] bg-gray-200 rounded-t-xl overflow-hidden">
                @if($book->image)
                <img src="{{ asset('storage/' . $book->image) }}" class="w-full h-full object-cover" alt="">
                @else
                <div class="w-full h-full flex items-center justify-center text-gray-300"><i class="fas fa-book text-4xl"></i></div>
                @endif
                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                    <button wire:click="quickView({{ $book->id }})" class="p-2 bg-white rounded-lg text-emerald-600 hover:bg-emerald-50"><i class="fas fa-eye"></i></button>
                    <a href="{{ route('staff.biblio.edit', $book->id) }}" class="p-2 bg-white rounded-lg text-blue-600 hover:bg-blue-50"><i class="fas fa-edit"></i></a>
                    <button wire:click="confirmDelete({{ $book->id }}, 'book')" class="p-2 bg-white rounded-lg text-red-600 hover:bg-red-50"><i class="fas fa-trash"></i></button>
                </div>
            </div>
            <div class="p-3">
                <a href="{{ route('staff.biblio.edit', $book->id) }}" class="font-medium text-gray-900 text-sm line-clamp-2 hover:text-blue-600">{{ $book->title }}</a>
                <p class="text-xs font-mono text-gray-400 mt-1 truncate">{{ $book->call_number ?: '-' }}</p>
            </div>
        </div>
        @endforeach
    </div>
    @endif
    <div class="p-4 border-t border-gray-100 bg-gray-50">{{ $books->links() }}</div>
@else
<div class="flex flex-col items-center justify-center py-16 text-center px-4">
    <div class="w-20 h-20 bg-gradient-to-br {{ $config['gradient'] }} rounded-2xl flex items-center justify-center mb-4 shadow-lg">
        <i class="fas {{ $config['icon'] }} text-3xl text-white"></i>
    </div>
    <h3 class="text-lg font-bold text-gray-900 mb-1">Belum Ada Data Bibliografi</h3>
    <p class="text-gray-500 text-sm mb-4">{{ $search ? 'Tidak ditemukan buku yang sesuai.' : 'Mulai dengan menambahkan buku pertama.' }}</p>
    @if(!$search)
    <a href="{{ route('staff.biblio.create') }}" class="px-5 py-2.5 bg-gradient-to-r {{ $config['gradient'] }} text-white text-sm font-medium rounded-xl hover:shadow-lg transition shadow-md">
        <i class="fas fa-plus mr-2"></i>Tambah Buku
    </a>
    @endif
</div>
@endif
