<div class="space-y-5">
    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('staff.elibrary.index', ['activeTab' => 'ethesis']) }}" class="w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center text-gray-600 transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $isEdit ? 'Edit E-Thesis' : 'Tambah E-Thesis' }}</h1>
            <p class="text-sm text-gray-500">{{ $isEdit ? 'Perbarui data e-thesis' : 'Upload karya ilmiah baru' }}</p>
        </div>
    </div>

    <form wire:submit="save" class="grid lg:grid-cols-3 gap-5">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="title" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Penulis <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="author" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('author') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NIM <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="nim" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('nim') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis <span class="text-red-500">*</span></label>
                        <select wire:model="type" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="skripsi">Skripsi</option>
                            <option value="tesis">Tesis</option>
                            <option value="disertasi">Disertasi</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Program Studi <span class="text-red-500">*</span></label>
                        <select wire:model="department_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Pilih --</option>
                            @foreach($departments as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('department_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tahun <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="year" maxlength="4" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('year') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pembimbing 1</label>
                        <input type="text" wire:model="advisor1" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pembimbing 2</label>
                        <input type="text" wire:model="advisor2" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Abstrak</label>
                    <textarea wire:model="abstract" rows="5" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kata Kunci</label>
                    <input type="text" wire:model="keywords" placeholder="pisahkan dengan koma" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">
            {{-- Publish --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-4">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" wire:model="is_public" class="w-5 h-5 rounded border-gray-300 text-blue-500 focus:ring-blue-500">
                    <span class="text-sm text-gray-700">Tampilkan di OPAC</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" wire:model="is_fulltext_public" class="w-5 h-5 rounded border-gray-300 text-blue-500 focus:ring-blue-500">
                    <span class="text-sm text-gray-700">Fulltext publik</span>
                </label>
                <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-blue-500 to-cyan-600 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/25 transition flex items-center justify-center gap-2">
                    <span wire:loading.remove wire:target="save"><i class="fas fa-save"></i> Simpan</span>
                    <span wire:loading wire:target="save"><i class="fas fa-spinner fa-spin"></i></span>
                </button>
            </div>

            {{-- Cover --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">Cover</label>
                @if($cover_path)
                <img src="{{ $cover_path->temporaryUrl() }}" class="w-full h-40 object-cover rounded-xl mb-2">
                @elseif($existing_cover)
                <img src="{{ asset('storage/'.$existing_cover) }}" class="w-full h-40 object-cover rounded-xl mb-2">
                @endif
                <input type="file" wire:model="cover_path" accept="image/*" class="w-full text-sm">
            </div>

            {{-- File --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">File PDF</label>
                @if($existing_file)
                <p class="text-xs text-gray-500 mb-2"><i class="fas fa-file-pdf mr-1"></i>{{ basename($existing_file) }}</p>
                @endif
                <input type="file" wire:model="file_path" accept=".pdf" class="w-full text-sm">
                <p class="text-xs text-gray-400 mt-1">Max 50MB (PDF)</p>
            </div>
        </div>
    </form>
</div>
