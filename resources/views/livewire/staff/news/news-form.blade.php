<div class="space-y-5">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('staff.news.index') }}" class="w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center text-gray-600 transition">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ $isEdit ? 'Edit Berita' : 'Tulis Berita Baru' }}</h1>
                <p class="text-sm text-gray-500">{{ $isEdit ? 'Perbarui konten berita' : 'Buat berita atau pengumuman baru' }}</p>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 flex items-center gap-3">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <form wire:submit="save" class="grid lg:grid-cols-3 gap-5">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-5">
            {{-- Title & Slug --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-heading text-gray-400"></i> Judul & URL
                    </h3>
                </div>
                <div class="p-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul Berita <span class="text-red-500">*</span></label>
                        <input type="text" wire:model.live="title" placeholder="Masukkan judul berita..."
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition">
                        @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Slug URL</label>
                        <div class="flex items-center">
                            <span class="px-3 py-2.5 bg-gray-100 border border-r-0 border-gray-300 rounded-l-xl text-gray-500 text-sm">/news/</span>
                            <input type="text" wire:model="slug" placeholder="slug-url"
                                   class="flex-1 px-4 py-2.5 border border-gray-300 rounded-r-xl focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition">
                        </div>
                        @error('slug') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Excerpt --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-align-left text-gray-400"></i> Ringkasan
                    </h3>
                </div>
                <div class="p-4">
                    <textarea wire:model="excerpt" rows="2" placeholder="Ringkasan singkat berita (opsional)..."
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition resize-none"></textarea>
                    @error('excerpt') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Content --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-file-alt text-gray-400"></i> Konten <span class="text-red-500">*</span>
                    </h3>
                </div>
                <div class="p-4">
                    <textarea wire:model="content" rows="15" placeholder="Tulis konten berita di sini..."
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition font-mono text-sm"></textarea>
                    <p class="text-xs text-gray-400 mt-1">Mendukung HTML dasar untuk formatting</p>
                    @error('content') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">
            {{-- Publish --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-paper-plane text-gray-400"></i> Publikasi
                    </h3>
                </div>
                <div class="p-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select wire:model="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-3">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" wire:model="is_featured" class="w-5 h-5 rounded border-gray-300 text-rose-500 focus:ring-rose-500">
                            <span class="text-sm text-gray-700">Featured (tampil di homepage)</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" wire:model="is_pinned" class="w-5 h-5 rounded border-gray-300 text-rose-500 focus:ring-rose-500">
                            <span class="text-sm text-gray-700">Pinned (sematkan di atas)</span>
                        </label>
                    </div>
                    <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-rose-500 to-pink-600 hover:from-rose-600 hover:to-pink-700 text-white font-semibold rounded-xl shadow-lg shadow-rose-500/25 transition flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="save">
                            <i class="fas fa-save"></i> {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Berita' }}
                        </span>
                        <span wire:loading wire:target="save"><i class="fas fa-spinner fa-spin"></i> Menyimpan...</span>
                    </button>
                </div>
            </div>

            {{-- Category --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-folder text-gray-400"></i> Kategori
                    </h3>
                </div>
                <div class="p-4">
                    <select wire:model="news_category_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Featured Image --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-image text-gray-400"></i> Gambar Utama
                    </h3>
                </div>
                <div class="p-4">
                    @if($featured_image)
                    <div class="relative mb-3">
                        <img src="{{ $featured_image->temporaryUrl() }}" class="w-full h-40 object-cover rounded-xl">
                        <button type="button" wire:click="$set('featured_image', null)" class="absolute top-2 right-2 w-8 h-8 bg-red-500 text-white rounded-lg flex items-center justify-center hover:bg-red-600 transition">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    @elseif($existing_image)
                    <div class="relative mb-3">
                        <img src="{{ asset('storage/'.$existing_image) }}" class="w-full h-40 object-cover rounded-xl">
                        <span class="absolute bottom-2 left-2 px-2 py-1 bg-black/50 text-white text-xs rounded">Gambar saat ini</span>
                    </div>
                    @endif
                    <label class="block">
                        <div class="w-full px-4 py-6 border-2 border-dashed border-gray-300 rounded-xl text-center cursor-pointer hover:border-rose-500 hover:bg-rose-50/50 transition">
                            <i class="fas fa-cloud-upload-alt text-gray-400 text-2xl mb-2"></i>
                            <p class="text-sm text-gray-500">Klik untuk upload gambar</p>
                            <p class="text-xs text-gray-400">Max 2MB (JPG, PNG)</p>
                        </div>
                        <input type="file" wire:model="featured_image" accept="image/*" class="hidden">
                    </label>
                    @error('featured_image') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
    </form>
</div>
