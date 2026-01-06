@section('title', $editMode ? 'Edit Kelas' : 'Buat Kelas Baru')

<div class="max-w-4xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('staff.elearning.index') }}" wire:navigate class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center text-gray-600 hover:bg-gray-200 transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $editMode ? 'Edit Kelas' : 'Buat Kelas Baru' }}</h1>
            <p class="text-sm text-gray-500">{{ $editMode ? 'Perbarui informasi kelas' : 'Isi informasi untuk membuat kelas baru' }}</p>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">
        {{-- Basic Info --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-info-circle text-violet-500"></i>
                Informasi Dasar
            </h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Kelas <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="title" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500" placeholder="Contoh: Pelatihan Cek Plagiasi Turnitin">
                    @error('title') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea wire:model="description" rows="4" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500" placeholder="Jelaskan tentang kelas ini..."></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <select wire:model="category_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cabang</label>
                        @if($this->isSuperAdmin())
                        <select wire:model="branch_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500">
                            <option value="">Semua Cabang (Global)</option>
                            @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        @else
                        <input type="text" value="{{ $branches->firstWhere('id', $branch_id)?->name ?? 'Cabang Anda' }}" disabled class="w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-gray-600">
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Level</label>
                        <select wire:model="level" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500">
                            <option value="beginner">Pemula</option>
                            <option value="intermediate">Menengah</option>
                            <option value="advanced">Lanjutan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Durasi (Jam)</label>
                        <input type="number" wire:model="duration_hours" min="1" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500" placeholder="Contoh: 8">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Maks. Peserta</label>
                        <input type="number" wire:model="max_participants" min="1" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500" placeholder="Kosongkan jika tidak terbatas">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Thumbnail</label>
                    <div class="flex items-start gap-4">
                        @if($existing_thumbnail || $thumbnail)
                        <div class="w-32 h-20 rounded-xl overflow-hidden bg-gray-100">
                            @if($thumbnail)
                            <img src="{{ $thumbnail->temporaryUrl() }}" class="w-full h-full object-cover">
                            @else
                            <img src="{{ Storage::url($existing_thumbnail) }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        @endif
                        <div class="flex-1">
                            <input type="file" wire:model="thumbnail" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
                            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maks 2MB. Rasio 16:9 disarankan.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Schedule --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-calendar-alt text-violet-500"></i>
                Jadwal & Lokasi
            </h2>
            
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                        <input type="date" wire:model="start_date" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                        <input type="date" wire:model="end_date" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Waktu</label>
                        <input type="time" wire:model="schedule_time" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hari</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach(['monday' => 'Sen', 'tuesday' => 'Sel', 'wednesday' => 'Rab', 'thursday' => 'Kam', 'friday' => 'Jum', 'saturday' => 'Sab', 'sunday' => 'Min'] as $day => $label)
                            <label class="inline-flex items-center">
                                <input type="checkbox" wire:model="schedule_days" value="{{ $day }}" class="rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                                <span class="ml-1 text-sm text-gray-700">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model.live="is_online" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-violet-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-violet-600"></div>
                    </label>
                    <span class="text-sm font-medium text-gray-700">Kelas Online</span>
                </div>

                @if($is_online)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Link Meeting</label>
                    <input type="url" wire:model="meeting_link" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500" placeholder="https://meet.google.com/xxx atau https://zoom.us/j/xxx">
                </div>
                @else
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                    <input type="text" wire:model="location" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500" placeholder="Contoh: Ruang Seminar Lt. 2 Perpustakaan Pusat">
                </div>
                @endif
            </div>
        </div>

        {{-- Settings --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-cog text-violet-500"></i>
                Pengaturan
            </h2>
            
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select wire:model="status" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500">
                            <option value="draft">Draft</option>
                            <option value="published">Dipublikasi</option>
                            <option value="archived">Diarsipkan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Kelulusan (%)</label>
                        <input type="number" wire:model="passing_score" min="0" max="100" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500">
                    </div>
                </div>

                <div class="flex flex-wrap gap-6">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="requires_approval" class="rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                        <span class="text-sm text-gray-700">Pendaftaran perlu persetujuan</span>
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="has_certificate" class="rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                        <span class="text-sm text-gray-700">Terbitkan sertifikat</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('staff.elearning.index') }}" wire:navigate class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-violet-600 to-purple-600 text-white rounded-xl font-semibold shadow-lg shadow-violet-500/25 hover:shadow-violet-500/40 transition">
                <span wire:loading.remove wire:target="save">
                    <i class="fas fa-save mr-2"></i>{{ $editMode ? 'Simpan Perubahan' : 'Buat Kelas' }}
                </span>
                <span wire:loading wire:target="save">
                    <i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...
                </span>
            </button>
        </div>
    </form>
</div>
