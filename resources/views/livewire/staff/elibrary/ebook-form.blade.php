<div class="space-y-5">
    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('staff.elibrary.index', ['activeTab' => 'ebook']) }}" class="w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center text-gray-600 transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $isEdit ? 'Edit E-Book' : 'Tambah E-Book' }}</h1>
            <p class="text-sm text-gray-500">{{ $isEdit ? 'Perbarui data e-book' : 'Upload e-book baru ke perpustakaan digital' }}</p>
        </div>
    </div>

    <form wire:submit="save" class="grid lg:grid-cols-3 gap-5">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="title" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                    @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Penulis/Pengarang</label>
                        <input type="text" wire:model="sor" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Terbit</label>
                        <input type="text" wire:model="publish_year" maxlength="4" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ISBN</label>
                        <input type="text" wire:model="isbn" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bahasa</label>
                        <select wire:model="language" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                            <option value="id">Indonesia</option>
                            <option value="en">English</option>
                            <option value="ar">Arabic</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Klasifikasi</label>
                        <input type="text" wire:model="classification" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. Panggil</label>
                        <input type="text" wire:model="call_number" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Abstrak</label>
                    <textarea wire:model="abstract" rows="4" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500"></textarea>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">
            {{-- Publish --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Akses</label>
                    <select wire:model="access_type" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                        <option value="open">Open Access</option>
                        <option value="member">Member Only</option>
                        <option value="restricted">Restricted</option>
                    </select>
                </div>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" wire:model="is_active" class="w-5 h-5 rounded border-gray-300 text-violet-500 focus:ring-violet-500">
                    <span class="text-sm text-gray-700">Aktif (tampil di OPAC)</span>
                </label>
                <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-violet-500 to-purple-600 text-white font-semibold rounded-xl shadow-lg shadow-violet-500/25 transition flex items-center justify-center gap-2">
                    <span wire:loading.remove wire:target="save"><i class="fas fa-save"></i> Simpan</span>
                    <span wire:loading wire:target="save"><i class="fas fa-spinner fa-spin"></i></span>
                </button>
            </div>

            {{-- Cover --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">Cover</label>
                @if($cover_image)
                <img src="{{ $cover_image->temporaryUrl() }}" class="w-full h-40 object-cover rounded-xl mb-2">
                @elseif($existing_cover)
                <img src="{{ asset('storage/'.$existing_cover) }}" class="w-full h-40 object-cover rounded-xl mb-2">
                @endif
                <input type="file" wire:model="cover_image" accept="image/*" class="w-full text-sm">
            </div>

            {{-- File --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">File E-Book <span class="text-red-500">*</span></label>
                @if($existing_file)
                <p class="text-xs text-gray-500 mb-2"><i class="fas fa-file-pdf mr-1"></i>{{ basename($existing_file) }}</p>
                @endif
                <input type="file" wire:model="file_path" accept=".pdf,.epub" class="w-full text-sm">
                @error('file_path') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                <p class="text-xs text-gray-400 mt-1">Max 50MB (PDF, EPUB)</p>
            </div>
        </div>
    </form>
</div>
