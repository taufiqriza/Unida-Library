@section('title', 'Detail Bibliografi')

<div class="space-y-5">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <a href="{{ route('staff.biblio.index') }}" wire:navigate class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('staff.biblio.edit', $book) }}" wire:navigate class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 text-white text-sm font-semibold rounded-xl hover:bg-amber-600 transition">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex gap-5">
                    <div class="w-32 h-44 bg-gradient-to-br from-slate-100 to-slate-200 rounded-xl flex items-center justify-center flex-shrink-0 overflow-hidden">
                        @if($book->image)
                            <img src="{{ asset('storage/'.$book->image) }}" class="w-full h-full object-cover">
                        @else
                            <i class="fas fa-book text-slate-300 text-4xl"></i>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <h1 class="text-xl font-bold text-gray-900 mb-2">{{ $book->title }}</h1>
                        <p class="text-gray-600 mb-4">{{ $book->authors->pluck('name')->implode('; ') ?: 'Penulis tidak diketahui' }}</p>
                        
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500">ISBN</p>
                                <p class="font-semibold text-gray-900 font-mono">{{ $book->isbn ?: '-' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">No. Panggil</p>
                                <p class="font-semibold text-gray-900 font-mono">{{ $book->call_number ?: '-' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Penerbit</p>
                                <p class="font-semibold text-gray-900">{{ $book->publisher?->name ?: '-' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Tahun Terbit</p>
                                <p class="font-semibold text-gray-900">{{ $book->publish_year ?: '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-layer-group text-blue-500"></i> Eksemplar
                        </h3>
                        <p class="text-xs text-gray-500">{{ $book->items->count() }} eksemplar tersedia</p>
                    </div>
                    <button wire:click="openAddModal" 
                            class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-500 text-white text-xs font-semibold rounded-lg hover:bg-blue-600 transition">
                        <i class="fas fa-plus"></i> Tambah
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="text-left text-xs text-gray-500 uppercase tracking-wider bg-gray-50">
                                <th class="px-5 py-3 font-semibold">Barcode</th>
                                <th class="px-5 py-3 font-semibold">Lokasi</th>
                                <th class="px-5 py-3 font-semibold">Status</th>
                                <th class="px-5 py-3 font-semibold text-center">Cetak</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($book->items as $item)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-5 py-3 font-mono text-sm font-semibold text-gray-900">{{ $item->barcode }}</td>
                                <td class="px-5 py-3 text-sm text-gray-600">{{ $item->location?->name ?: '-' }}</td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full {{ $item->isAvailable() ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $item->isAvailable() ? 'Tersedia' : 'Dipinjam' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-center gap-1">
                                        <a href="{{ route('print.barcode', $item) }}" target="_blank" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-600 hover:bg-gray-100 transition" title="Cetak Barcode">
                                            <i class="fas fa-barcode"></i>
                                        </a>
                                        <a href="{{ route('print.label', $item) }}" target="_blank" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-600 hover:bg-gray-100 transition" title="Cetak Label">
                                            <i class="fas fa-tag"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-5 py-8 text-center text-gray-400">Belum ada eksemplar</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-5">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="font-bold text-gray-900 mb-4">Subjek</h3>
                <div class="flex flex-wrap gap-2">
                    @forelse($book->subjects as $subject)
                        <span class="px-3 py-1 bg-blue-50 text-blue-700 text-xs font-medium rounded-full">{{ $subject->name }}</span>
                    @empty
                        <p class="text-sm text-gray-400">Tidak ada subjek</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="font-bold text-gray-900 mb-4">Info Lainnya</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Klasifikasi</span>
                        <span class="font-medium text-gray-900">{{ $book->classification ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">GMD</span>
                        <span class="font-medium text-gray-900">{{ $book->mediaType?->name ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Bahasa</span>
                        <span class="font-medium text-gray-900">{{ $book->language ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Edisi</span>
                        <span class="font-medium text-gray-900">{{ $book->edition ?: '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Item Modal -->
    @if($showAddModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" wire:click.self="closeAddModal">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden" @click.stop>
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-900">Tambah Eksemplar</h3>
                <button wire:click="closeAddModal" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-5">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Eksemplar</label>
                    <input type="number" wire:model="addQty" min="1" max="50" 
                           class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('addQty') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <button wire:click="addItems" wire:loading.attr="disabled" 
                        class="w-full py-2.5 bg-blue-500 text-white font-semibold rounded-xl hover:bg-blue-600 transition disabled:opacity-50 flex items-center justify-center gap-2">
                    <span wire:loading wire:target="addItems"><i class="fas fa-spinner fa-spin"></i></span>
                    Tambah Eksemplar
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
