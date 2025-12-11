@push('styles')
<style>
    .stat-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 12px 24px -8px rgba(0, 0, 0, 0.12); }
    .book-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .book-card:hover { transform: translateY(-2px); box-shadow: 0 12px 24px -8px rgba(0, 0, 0, 0.12); }
    .modal-backdrop { animation: fadeIn 0.2s ease-out; }
    .modal-content { animation: slideUp 0.3s ease-out; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endpush

<div class="space-y-5">
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 via-indigo-500 to-purple-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-500/25">
                <i class="fas fa-book-open text-xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Katalog Bibliografi</h1>
                <p class="text-sm text-gray-500">{{ $userBranch->name ?? 'Cabang' }} • {{ $stats['total_books'] }} judul</p>
            </div>
        </div>

        <a href="{{ route('staff.biblio.create') }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-medium rounded-xl shadow-lg shadow-blue-500/25 transition text-sm">
            <i class="fas fa-plus"></i>
            <span>Tambah Buku</span>
        </a>
    </div>

    {{-- Compact Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        {{-- Total Judul --}}
        <div class="stat-card bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl p-4 text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-book text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold">{{ number_format($stats['total_books']) }}</p>
                    <p class="text-xs text-blue-100">Total Judul</p>
                </div>
            </div>
        </div>

        {{-- Total Eksemplar --}}
        <div class="stat-card bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl p-4 text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-copy text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold">{{ number_format($stats['total_items']) }}</p>
                    <p class="text-xs text-emerald-100">Eksemplar</p>
                </div>
            </div>
        </div>

        {{-- Baru Minggu Ini --}}
        <div class="stat-card bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-star text-white text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['recent_additions']) }}</p>
                    <p class="text-xs text-gray-500">Baru (7 hari)</p>
                </div>
            </div>
        </div>

        {{-- Tanpa Eksemplar --}}
        <div class="stat-card bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-red-400 to-rose-500 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-white text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['books_without_items']) }}</p>
                    <p class="text-xs text-gray-500">Tanpa Item</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Search & View Toggle --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input wire:model.live.debounce.300ms="search" 
                       type="text" 
                       placeholder="Cari judul, ISBN, penulis, atau nomor panggil..."
                       class="w-full pl-9 pr-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 rounded-lg text-sm">
            </div>
            
            {{-- View Toggle --}}
            <div class="flex items-center bg-gray-100 rounded-lg p-1">
                <button wire:click="setViewMode('list')" 
                        class="px-3 py-1.5 rounded-md text-sm font-medium transition {{ $viewMode === 'list' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                    <i class="fas fa-list"></i>
                </button>
                <button wire:click="setViewMode('grid')" 
                        class="px-3 py-1.5 rounded-md text-sm font-medium transition {{ $viewMode === 'grid' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                    <i class="fas fa-th-large"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Results Info --}}
    @if($books->total() > 0)
    <div class="text-sm text-gray-500">
        Menampilkan <span class="font-medium text-gray-700">{{ $books->firstItem() }}-{{ $books->lastItem() }}</span> 
        dari <span class="font-medium text-gray-700">{{ $books->total() }}</span> judul
    </div>
    @endif

    {{-- Book List/Grid --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        @if($books->count() > 0)
            @if($viewMode === 'list')
                {{-- List View --}}
                <div class="divide-y divide-gray-100">
                    @foreach($books as $book)
                    <div class="book-card p-4 hover:bg-blue-50/30 flex gap-4">
                        {{-- Cover --}}
                        <div class="relative w-14 h-20 flex-shrink-0 bg-gray-100 rounded-lg overflow-hidden">
                            @if($book->image)
                                <img src="{{ asset('storage/' . $book->image) }}" class="w-full h-full object-cover" alt="">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-300">
                                    <i class="fas fa-book text-xl"></i>
                                </div>
                            @endif
                            {{-- Item Badge --}}
                            <div class="absolute -top-1 -right-1 w-5 h-5 bg-blue-600 rounded-full flex items-center justify-center text-white text-[10px] font-bold border-2 border-white">
                                {{ $book->items->count() }}
                            </div>
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('staff.biblio.edit', $book->id) }}" 
                               class="font-semibold text-gray-900 hover:text-blue-600 transition block truncate">
                                {{ $book->title }}
                            </a>
                            
                            {{-- Authors --}}
                            <div class="flex flex-wrap gap-1 mt-1">
                                @foreach($book->authors->take(2) as $author)
                                    <span class="text-xs text-purple-600 bg-purple-50 px-1.5 py-0.5 rounded">{{ $author->name }}</span>
                                @endforeach
                            </div>

                            {{-- Metadata --}}
                            <div class="flex flex-wrap items-center gap-3 mt-2 text-xs text-gray-500">
                                <span title="ISBN"><i class="fas fa-barcode mr-1"></i>{{ $book->isbn ?: '-' }}</span>
                                <span title="Call Number" class="font-mono bg-gray-100 px-1.5 py-0.5 rounded">{{ $book->call_number ?: '-' }}</span>
                                <span title="Tahun"><i class="fas fa-calendar mr-1"></i>{{ $book->publish_year ?: '-' }}</span>
                                <span title="Input oleh"><i class="fas fa-user mr-1"></i>{{ $book->user->name ?? '-' }}</span>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-1">
                            <a href="{{ route('staff.biblio.show', $book->id) }}" 
                               class="p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition" 
                               title="Lihat Eksemplar">
                                <i class="fas fa-boxes-stacked"></i>
                            </a>
                            <a href="{{ route('staff.biblio.edit', $book->id) }}" 
                               class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" 
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button wire:click="confirmDelete({{ $book->id }})" 
                                    class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" 
                                    title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                {{-- Grid View --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 p-4">
                    @foreach($books as $book)
                    <div class="book-card bg-gray-50 rounded-xl overflow-hidden group">
                        {{-- Cover --}}
                        <div class="relative aspect-[3/4] bg-gray-200">
                            @if($book->image)
                                <img src="{{ asset('storage/' . $book->image) }}" class="w-full h-full object-cover" alt="">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-300">
                                    <i class="fas fa-book text-4xl"></i>
                                </div>
                            @endif
                            {{-- Item Badge --}}
                            <div class="absolute top-2 right-2 px-2 py-0.5 bg-blue-600 rounded-full text-white text-xs font-bold">
                                {{ $book->items->count() }} eks
                            </div>
                            {{-- Hover Actions --}}
                            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                                <a href="{{ route('staff.biblio.show', $book->id) }}" class="p-2 bg-white rounded-lg text-emerald-600 hover:bg-emerald-50">
                                    <i class="fas fa-boxes-stacked"></i>
                                </a>
                                <a href="{{ route('staff.biblio.edit', $book->id) }}" class="p-2 bg-white rounded-lg text-blue-600 hover:bg-blue-50">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button wire:click="confirmDelete({{ $book->id }})" class="p-2 bg-white rounded-lg text-red-600 hover:bg-red-50">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        {{-- Info --}}
                        <div class="p-3">
                            <a href="{{ route('staff.biblio.edit', $book->id) }}" class="font-medium text-gray-900 text-sm line-clamp-2 hover:text-blue-600">
                                {{ $book->title }}
                            </a>
                            <p class="text-xs text-gray-500 mt-1">{{ $book->publish_year }}</p>
                            <p class="text-xs font-mono text-gray-400 mt-1 truncate">{{ $book->call_number ?: '-' }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif

            {{-- Pagination --}}
            <div class="p-4 border-t border-gray-100 bg-gray-50">
                {{ $books->links() }}
            </div>
        @else
            {{-- Empty State --}}
            <div class="flex flex-col items-center justify-center py-16 text-center px-4">
                <div class="w-16 h-16 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-book-open text-2xl text-blue-500"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">Belum Ada Data</h3>
                <p class="text-gray-500 text-sm mb-4">
                    @if($search)
                        Tidak ditemukan buku yang sesuai pencarian.
                    @else
                        Mulai dengan menambahkan buku pertama.
                    @endif
                </p>
                @if($search)
                    <button wire:click="$set('search', '')" class="text-blue-600 hover:underline text-sm">Bersihkan pencarian</button>
                @else
                    <a href="{{ route('staff.biblio.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm">Tambah Buku</a>
                @endif
            </div>
        @endif
    </div>

    {{-- Delete Confirmation Modal --}}
    @if($deleteConfirmId)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-backdrop" style="background: rgba(0,0,0,0.5);">
        <div class="modal-content bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-trash text-2xl text-red-500"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Hapus Buku?</h3>
                <p class="text-gray-500 text-sm mb-6">Data yang sudah dihapus tidak dapat dikembalikan.</p>
                <div class="flex gap-3">
                    <button wire:click="cancelDelete" class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition">
                        Batal
                    </button>
                    <button wire:click="delete" class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-xl transition">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
