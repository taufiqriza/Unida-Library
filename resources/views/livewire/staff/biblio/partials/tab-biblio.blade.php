{{-- Tab Bibliografi --}}
@php
$config = ['gradient' => 'from-blue-500 to-indigo-600', 'bg' => 'blue', 'icon' => 'fa-book'];
@endphp

@if($books->count() > 0)
    {{-- Bulk Actions --}}
    @if(count($selectedItems) > 0)
    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-between">
        <span class="text-sm text-blue-700"><i class="fas fa-check-circle mr-2"></i>{{ count($selectedItems) }} buku dipilih</span>
        <div class="flex gap-2">
            <button wire:click="clearSelection" class="px-3 py-1.5 text-xs text-gray-600 hover:text-gray-800">Batal</button>
            <button wire:click="confirmBulkDelete" class="px-3 py-1.5 text-xs bg-red-500 text-white rounded-lg hover:bg-red-600"><i class="fas fa-trash mr-1"></i>Hapus</button>
        </div>
    </div>
    @endif

    @if($viewMode === 'list')
    <div class="overflow-x-auto" style="overflow-y: visible;">
        <table class="w-full text-sm" style="overflow: visible;">
            <thead class="bg-gradient-to-r {{ $config['gradient'] }} text-white">
                <tr>
                    <th class="px-3 py-3 text-center w-10"><input type="checkbox" wire:model.live="selectAll" class="rounded border-white/50 text-blue-600"></th>
                    <th class="px-4 py-3 text-left font-medium w-20">Cover</th>
                    <th class="px-4 py-3 text-left font-medium">Judul & Info</th>
                    <th class="px-4 py-3 text-center font-medium w-16">Tahun</th>
                    <th class="px-4 py-3 text-left font-medium w-36">Media</th>
                    <th class="px-4 py-3 text-left font-medium w-40">Input By</th>
                    <th class="px-4 py-3 text-center font-medium w-24">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50" style="overflow: visible;">
                @foreach($books as $book)
                <tr class="hover:bg-blue-50/30 transition {{ in_array((string)$book->id, $selectedItems) ? 'bg-blue-50' : '' }}" style="overflow: visible; {{ $coverSearchBookId === $book->id ? 'position: relative; z-index: 9999;' : '' }}">
                    <td class="px-3 py-3 text-center"><input type="checkbox" wire:click="toggleBookSelection({{ $book->id }})" {{ in_array((string)$book->id, $selectedItems) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600"></td>
                    <td class="px-4 py-3" style="overflow: visible; position: relative; z-index: 1;">
                        <div class="relative w-14 h-20">
                            <div class="w-full h-full bg-gradient-to-br from-slate-100 to-slate-200 rounded-lg overflow-hidden shadow-sm">
                                @if($book->image)
                                <img src="{{ $book->cover_url }}" class="w-full h-full object-cover" alt="">
                                @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fas fa-book text-slate-300 text-lg"></i>
                                </div>
                                @endif
                            </div>
                            <div class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-gradient-to-r {{ $config['gradient'] }} rounded-full flex items-center justify-center text-white text-[10px] font-bold border-2 border-white shadow">{{ $book->items_count }}</div>
                            @if(!$book->image)
                            <button 
                                wire:click="openCoverSearch({{ $book->id }})" 
                                wire:loading.attr="disabled" 
                                class="absolute -bottom-1.5 -left-2 -right-2 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white text-[8px] font-semibold py-0.5 flex items-center justify-center gap-0.5 rounded-md shadow cursor-pointer whitespace-nowrap">
                                <span wire:loading.remove wire:target="openCoverSearch({{ $book->id }})"><i class="fas fa-camera text-[7px]"></i> Cari Cover</span>
                                <span wire:loading wire:target="openCoverSearch({{ $book->id }})"><i class="fas fa-spinner fa-spin text-[7px]"></i> Mencari...</span>
                            </button>
                            @endif
                            
                            {{-- Cover Search Popup --}}
                            @if($coverSearchBookId === $book->id)
                            <div class="absolute top-full left-0 mt-2 w-[480px] bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden" style="z-index: 99999; position: absolute;" wire:click.outside="closeCoverSearch">
                                <div class="px-3 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 flex items-center justify-between">
                                    <span class="text-white text-xs font-semibold"><i class="fas fa-image mr-1"></i> Pilih Cover</span>
                                    <button wire:click="closeCoverSearch" class="text-white/80 hover:text-white"><i class="fas fa-times text-xs"></i></button>
                                </div>
                                <div class="p-3">
                                    @if(count($coverSearchResults) > 0)
                                    <div class="flex gap-3 overflow-x-auto pb-1">
                                        @foreach($coverSearchResults as $cover)
                                        <div wire:click="applyCover('{{ $cover['url'] }}')" class="flex-shrink-0 w-24 cursor-pointer group">
                                            <div class="relative w-24 h-32 rounded-lg overflow-hidden border-2 border-transparent group-hover:border-blue-500 group-hover:shadow-lg transition shadow-sm bg-gray-100">
                                                <img src="{{ $cover['url'] }}" class="w-full h-full object-cover" onerror="this.parentElement.parentElement.style.display='none'">
                                                <span class="absolute top-0.5 left-0.5 px-1 py-px text-[7px] font-medium rounded {{ $cover['source'] === 'Google' ? 'bg-blue-600' : ($cover['source'] === 'Internal' ? 'bg-emerald-600' : 'bg-orange-500') }} text-white inline-flex items-center gap-0.5 leading-none">
                                                    <i class="{{ $cover['source'] === 'Google' ? 'fab fa-google' : ($cover['source'] === 'Internal' ? 'fas fa-database' : 'fas fa-book-open') }} text-[6px]"></i>{{ $cover['source'] === 'Google' ? 'Books' : ($cover['source'] === 'Internal' ? 'Lokal' : 'OL') }}
                                                </span>
                                            </div>
                                            <p class="text-[9px] text-gray-600 text-center mt-1.5 line-clamp-2 leading-tight">{{ Str::limit($cover['title'] ?: '-', 35) }}</p>
                                        </div>
                                        @endforeach
                                    </div>
                                    @else
                                    <div class="py-4 text-center">
                                        <i class="fas fa-spinner fa-spin text-blue-500 mb-2"></i>
                                        <p class="text-xs text-gray-500">Mencari...</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif
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
                        <p class="text-xs text-gray-700 truncate">{{ $book->user?->name ?? $book->branch?->name ?? '-' }}</p>
                        @if($book->updated_at && $book->updated_at->gt($book->created_at->addMinutes(1)))
                        <p class="text-[10px] text-amber-500"><i class="fas fa-edit mr-0.5"></i>{{ $book->updated_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }}</p>
                        @else
                        <p class="text-[10px] text-gray-400">{{ $book->created_at?->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }}</p>
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
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 p-4" style="overflow: visible;">
        @foreach($books as $book)
        <div class="bg-gray-50 rounded-xl group relative" style="overflow: visible; {{ $coverSearchBookId === $book->id ? 'z-index: 9999;' : '' }}">
            <div class="absolute -top-1.5 -right-1.5 z-10 px-2 py-0.5 bg-gradient-to-r {{ $config['gradient'] }} rounded-full text-white text-xs font-bold shadow border-2 border-white">{{ $book->items_count }}</div>
            @if(!$book->image)
            <button 
                wire:click="openCoverSearch({{ $book->id }})" 
                wire:loading.attr="disabled" 
                class="absolute -bottom-1.5 left-1 right-1 z-10 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white text-[10px] font-semibold py-0.5 flex items-center justify-center gap-1 rounded-md shadow cursor-pointer">
                <span wire:loading.remove wire:target="openCoverSearch({{ $book->id }})"><i class="fas fa-camera text-[9px]"></i> Cari Cover</span>
                <span wire:loading wire:target="openCoverSearch({{ $book->id }})"><i class="fas fa-spinner fa-spin text-[9px]"></i> Mencari...</span>
            </button>
            @endif
            <div class="relative aspect-[3/4] bg-gradient-to-br from-slate-100 to-slate-200 rounded-t-xl overflow-hidden">
                @if($book->image)
                <img src="{{ $book->cover_url }}" class="w-full h-full object-cover" alt="">
                @else
                <div class="w-full h-full flex items-center justify-center">
                    <i class="fas fa-book text-slate-300 text-4xl"></i>
                </div>
                @endif
                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                    <button wire:click="quickView({{ $book->id }})" class="p-2 bg-white rounded-lg text-emerald-600 hover:bg-emerald-50"><i class="fas fa-eye"></i></button>
                    <a href="{{ route('staff.biblio.edit', $book->id) }}" class="p-2 bg-white rounded-lg text-blue-600 hover:bg-blue-50"><i class="fas fa-edit"></i></a>
                    <button wire:click="confirmDelete({{ $book->id }}, 'book')" class="p-2 bg-white rounded-lg text-red-600 hover:bg-red-50"><i class="fas fa-trash"></i></button>
                </div>
            </div>
            {{-- Cover Search Popup for Grid --}}
            @if($coverSearchBookId === $book->id)
            <div class="absolute top-full left-0 mt-2 w-[480px] bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden" style="z-index: 99999;" wire:click.outside="closeCoverSearch">
                <div class="px-3 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 flex items-center justify-between">
                    <span class="text-white text-xs font-semibold"><i class="fas fa-image mr-1"></i> Pilih Cover</span>
                    <button wire:click="closeCoverSearch" class="text-white/80 hover:text-white"><i class="fas fa-times text-xs"></i></button>
                </div>
                <div class="p-3">
                    @if(count($coverSearchResults) > 0)
                    <div class="flex gap-3 overflow-x-auto pb-1">
                        @foreach($coverSearchResults as $cover)
                        <div wire:click="applyCover('{{ $cover['url'] }}')" class="flex-shrink-0 w-24 cursor-pointer group/cover">
                            <div class="relative w-24 h-32 rounded-lg overflow-hidden border-2 border-transparent group-hover/cover:border-blue-500 group-hover/cover:shadow-lg transition shadow-sm bg-gray-100">
                                <img src="{{ $cover['url'] }}" class="w-full h-full object-cover" onerror="this.parentElement.parentElement.style.display='none'">
                                <span class="absolute top-0.5 left-0.5 px-1 py-px text-[7px] font-medium rounded {{ $cover['source'] === 'Google' ? 'bg-blue-600' : ($cover['source'] === 'Internal' ? 'bg-emerald-600' : 'bg-orange-500') }} text-white inline-flex items-center gap-0.5 leading-none">
                                    <i class="{{ $cover['source'] === 'Google' ? 'fab fa-google' : ($cover['source'] === 'Internal' ? 'fas fa-database' : 'fas fa-book-open') }} text-[6px]"></i>{{ $cover['source'] === 'Google' ? 'Books' : ($cover['source'] === 'Internal' ? 'Lokal' : 'OL') }}
                                </span>
                            </div>
                            <p class="text-[9px] text-gray-600 text-center mt-1.5 line-clamp-2 leading-tight">{{ Str::limit($cover['title'] ?: '-', 35) }}</p>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="py-4 text-center">
                        <i class="fas fa-spinner fa-spin text-blue-500 mb-2"></i>
                        <p class="text-xs text-gray-500">Mencari...</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
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
