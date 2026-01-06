@section('title', $course->title)

<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
        <div class="flex items-start gap-4">
            <a href="{{ route('staff.elearning.index') }}" wire:navigate class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center text-gray-600 hover:bg-gray-200 transition mt-1">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <div class="flex items-center gap-2 mb-1">
                    @if($course->category)
                    <span class="text-xs font-medium text-violet-600 bg-violet-50 px-2 py-0.5 rounded">{{ $course->category->name }}</span>
                    @endif
                    <span class="px-2 py-0.5 text-xs font-semibold rounded {{ $course->status === 'published' ? 'bg-green-100 text-green-700' : ($course->status === 'draft' ? 'bg-gray-100 text-gray-700' : 'bg-red-100 text-red-700') }}">
                        {{ $course->status === 'published' ? 'Aktif' : ($course->status === 'draft' ? 'Draft' : 'Arsip') }}
                    </span>
                </div>
                <h1 class="text-xl lg:text-2xl font-bold text-gray-900">{{ $course->title }}</h1>
                <p class="text-sm text-gray-500 mt-1">Instruktur: {{ $course->instructor->name }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('staff.elearning.edit', $course->id) }}" wire:navigate class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition">
                <i class="fas fa-edit mr-1"></i> Edit
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-violet-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-layer-group text-violet-600"></i>
                </div>
                <div>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['total_modules'] }}</p>
                    <p class="text-xs text-gray-500">Modul</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-alt text-blue-600"></i>
                </div>
                <div>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['total_materials'] }}</p>
                    <p class="text-xs text-gray-500">Materi</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-green-600"></i>
                </div>
                <div>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['total_enrollments'] }}</p>
                    <p class="text-xs text-gray-500">Peserta</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-amber-600"></i>
                </div>
                <div>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['pending_enrollments'] }}</p>
                    <p class="text-xs text-gray-500">Menunggu</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-award text-emerald-600"></i>
                </div>
                <div>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['completed_enrollments'] }}</p>
                    <p class="text-xs text-gray-500">Lulus</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-2 border-b border-gray-200 overflow-x-auto">
        @foreach(['overview' => 'Ringkasan', 'modules' => 'Modul & Materi', 'participants' => 'Peserta', 'certificates' => 'Sertifikat'] as $key => $label)
        <button wire:click="$set('tab', '{{ $key }}')" class="px-5 py-3 font-semibold text-sm transition border-b-2 whitespace-nowrap {{ $tab === $key ? 'text-violet-600 border-violet-600' : 'text-gray-500 border-transparent hover:text-gray-700' }}">
            {{ $label }}
        </button>
        @endforeach
    </div>

    @include('livewire.staff.elearning.partials.course-' . $tab)

    {{-- Module Modal --}}
    @if($showModuleModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" wire:click.self="$set('showModuleModal', false)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900">{{ $editingModuleId ? 'Edit Modul' : 'Tambah Modul' }}</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Modul</label>
                    <input type="text" wire:model="moduleTitle" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500">
                    @error('moduleTitle') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea wire:model="moduleDescription" rows="3" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500"></textarea>
                </div>
            </div>
            <div class="p-6 border-t border-gray-100 flex justify-end gap-3">
                <button wire:click="$set('showModuleModal', false)" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200">Batal</button>
                <button wire:click="saveModule" class="px-4 py-2 bg-violet-600 text-white rounded-xl font-semibold hover:bg-violet-700">Simpan</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Material Modal --}}
    @if($showMaterialModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" wire:click.self="$set('showMaterialModal', false)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900">{{ $editingMaterialId ? 'Edit Materi' : 'Tambah Materi' }}</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Materi</label>
                    <input type="text" wire:model="materialTitle" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                    <select wire:model.live="materialType" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500">
                        <option value="text">Teks/Artikel</option>
                        <option value="video">Video</option>
                        <option value="document">Dokumen</option>
                        <option value="link">Link Eksternal</option>
                        <option value="quiz">Quiz</option>
                    </select>
                </div>
                @if($materialType === 'text')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konten</label>
                    <textarea wire:model="materialContent" rows="6" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500"></textarea>
                </div>
                @elseif($materialType === 'video')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">URL Video (YouTube/Vimeo)</label>
                    <input type="url" wire:model="materialVideoUrl" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500" placeholder="https://youtube.com/watch?v=...">
                </div>
                @elseif($materialType === 'document')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload File</label>
                    <input type="file" wire:model="materialFile" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700">
                    <p class="text-xs text-gray-500 mt-1">PDF, DOC, PPT, XLS. Maks 50MB</p>
                </div>
                @elseif($materialType === 'link')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">URL</label>
                    <input type="url" wire:model="materialExternalLink" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500">
                </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Durasi (menit)</label>
                    <input type="number" wire:model="materialDuration" min="1" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500">
                </div>
            </div>
            <div class="p-6 border-t border-gray-100 flex justify-end gap-3">
                <button wire:click="$set('showMaterialModal', false)" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200">Batal</button>
                <button wire:click="saveMaterial" class="px-4 py-2 bg-violet-600 text-white rounded-xl font-semibold hover:bg-violet-700">Simpan</button>
            </div>
        </div>
    </div>
    @endif
</div>
