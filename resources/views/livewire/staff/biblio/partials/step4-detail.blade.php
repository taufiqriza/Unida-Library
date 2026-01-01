<div class="p-5 border-b border-gray-100 bg-gradient-to-r from-primary-50 to-white">
    <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
        <i class="fas fa-file-alt text-primary-500"></i> Detail & Pengaturan
    </h2>
</div>
<div class="p-5 space-y-5">
    {{-- Cover & Settings Row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        {{-- Cover Upload --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2"><i class="fas fa-image text-gray-400 mr-1"></i> Gambar Sampul</label>
            <label class="block cursor-pointer">
                <div class="w-full aspect-[2/3] border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center overflow-hidden bg-gray-50 hover:bg-gray-100 hover:border-primary-400 transition relative group">
                    @if($cover_image)
                        <img src="{{ $cover_image->temporaryUrl() }}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                            <span class="text-white text-sm font-medium"><i class="fas fa-camera mr-1"></i> Ganti</span>
                        </div>
                    @elseif($isEdit && $book?->image)
                        <img src="{{ $book->cover_url }}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                            <span class="text-white text-sm font-medium"><i class="fas fa-camera mr-1"></i> Ganti</span>
                        </div>
                    @else
                        <div class="text-center p-4">
                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-300 mb-2"></i>
                            <p class="text-xs text-gray-500">Klik untuk upload</p>
                            <p class="text-xs text-gray-400">JPG/PNG, max 2MB</p>
                        </div>
                    @endif
                    <input type="file" wire:model="cover_image" accept="image/*" class="hidden">
                </div>
            </label>
            @error('cover_image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            <div wire:loading wire:target="cover_image" class="mt-2 text-xs text-primary-600 flex items-center gap-1">
                <i class="fas fa-spinner fa-spin"></i> Mengupload...
            </div>
        </div>

        {{-- OPAC Settings --}}
        <div class="md:col-span-2 space-y-4">
            <div>
                <h3 class="text-sm font-semibold text-gray-800 mb-3"><i class="fas fa-cog text-gray-400 mr-1"></i> Pengaturan OPAC</h3>
                <div class="space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition">
                        <input type="checkbox" wire:model="is_opac_visible" class="w-5 h-5 text-primary-600 rounded">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Tampilkan di OPAC</p>
                            <p class="text-xs text-gray-500">Bibliografi akan muncul di pencarian publik</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition">
                        <input type="checkbox" wire:model="promoted" class="w-5 h-5 text-primary-600 rounded">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Promosikan</p>
                            <p class="text-xs text-gray-500">Tampilkan di halaman utama OPAC</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Abstract --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Abstrak/Ringkasan</label>
                <textarea wire:model="abstract" rows="3" class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Ringkasan isi buku..."></textarea>
            </div>
        </div>
    </div>

    {{-- Notes --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Catatan</label>
        <textarea wire:model="notes" rows="2" class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Catatan tambahan..."></textarea>
    </div>

    {{-- Summary --}}
    <div class="pt-4 border-t border-gray-100">
        <h3 class="text-sm font-semibold text-gray-800 mb-3"><i class="fas fa-list-alt text-gray-400 mr-1"></i> Ringkasan Data</h3>
        <div class="bg-gray-50 rounded-xl p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2 text-sm">
                <div class="flex justify-between py-1.5 border-b border-gray-100">
                    <span class="text-gray-500">Judul</span>
                    <span class="font-medium text-gray-900 text-right max-w-[60%] truncate">{{ $title ?: '-' }}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-gray-100">
                    <span class="text-gray-500">Penulis</span>
                    <span class="font-medium text-gray-900">{{ count($selectedAuthors) > 0 ? collect($selectedAuthors)->pluck('name')->implode(', ') : '-' }}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-gray-100">
                    <span class="text-gray-500">Subjek</span>
                    <span class="font-medium text-gray-900">{{ count($selectedSubjects) > 0 ? collect($selectedSubjects)->pluck('name')->take(2)->implode(', ') . (count($selectedSubjects) > 2 ? '...' : '') : '-' }}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-gray-100">
                    <span class="text-gray-500">Tahun Terbit</span>
                    <span class="font-medium text-gray-900">{{ $publish_year ?: '-' }}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-gray-100">
                    <span class="text-gray-500">ISBN</span>
                    <span class="font-medium text-gray-900">{{ $isbn ?: '-' }}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-gray-100">
                    <span class="text-gray-500">No. Klasifikasi</span>
                    <span class="font-medium text-gray-900">{{ $classification ?: '-' }}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-gray-100">
                    <span class="text-gray-500">No. Panggil</span>
                    <span class="font-medium text-primary-600">{{ $call_number ?: '-' }}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-gray-100">
                    <span class="text-gray-500">Bahasa</span>
                    <span class="font-medium text-gray-900">{{ $languages[$language] ?? '-' }}</span>
                </div>
                @if(!$isEdit)
                <div class="flex justify-between py-1.5 border-b border-gray-100">
                    <span class="text-gray-500">Eksemplar</span>
                    <span class="font-medium text-emerald-600">{{ $item_qty }} item akan dibuat</span>
                </div>
                @endif
                <div class="flex justify-between py-1.5">
                    <span class="text-gray-500">Status OPAC</span>
                    <span class="font-medium {{ $is_opac_visible ? 'text-emerald-600' : 'text-gray-400' }}">
                        <i class="fas {{ $is_opac_visible ? 'fa-eye' : 'fa-eye-slash' }} mr-1"></i>
                        {{ $is_opac_visible ? 'Tampil' : 'Tersembunyi' }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
