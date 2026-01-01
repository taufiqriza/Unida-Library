<div class="w-full max-w-6xl mx-auto px-4 sm:px-6">
    {{-- Progress Header --}}
    <div class="sticky top-0 z-30 -mx-4 sm:-mx-6 px-4 sm:px-6 py-4 mb-6">
        <div class="bg-white/95 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <a href="{{ route('staff.biblio.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-600 transition">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-lg font-bold text-gray-900">{{ $isEdit ? 'Edit Bibliografi' : 'Tambah Bibliografi' }}</h1>
                        <p class="text-xs text-gray-500">{{ $this->completionPercentage }}% selesai</p>
                    </div>
                </div>
                <button wire:click="save" wire:loading.attr="disabled" class="px-5 py-2.5 bg-primary-600 text-white text-sm font-semibold rounded-xl hover:bg-primary-700 disabled:opacity-50 transition flex items-center gap-2">
                    <span wire:loading.remove wire:target="save"><i class="fas fa-save mr-1"></i> Simpan</span>
                    <span wire:loading wire:target="save"><i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...</span>
                </button>
            </div>
            
            {{-- Step Indicators --}}
            <div class="relative flex items-center justify-between px-2">
                <div class="absolute top-5 left-10 right-10 h-0.5 bg-gray-200 rounded-full"></div>
                <div class="absolute top-5 left-10 h-0.5 bg-primary-500 rounded-full transition-all" style="width: calc({{ ($step - 1) * 33.33 }}% - {{ $step > 1 ? '0px' : '0px' }}); max-width: calc(100% - 5rem);"></div>
                
                @foreach([
                    ['step' => 1, 'label' => 'Info Utama', 'icon' => 'fa-info-circle'],
                    ['step' => 2, 'label' => 'Penulis', 'icon' => 'fa-users'],
                    ['step' => 3, 'label' => 'Penerbitan', 'icon' => 'fa-building'],
                    ['step' => 4, 'label' => 'Detail', 'icon' => 'fa-file-alt'],
                ] as $s)
                    <button wire:click="goToStep({{ $s['step'] }})" type="button" class="relative z-10 flex flex-col items-center {{ $step > $s['step'] ? 'cursor-pointer' : 'cursor-default' }}" @if($step < $s['step']) disabled @endif>
                        <div @class([
                            'w-10 h-10 rounded-full flex items-center justify-center transition-all',
                            'bg-primary-600 text-white shadow-lg' => $step === $s['step'],
                            'bg-primary-500 text-white' => $step > $s['step'],
                            'bg-gray-100 text-gray-400' => $step < $s['step'],
                        ])>
                            @if($step > $s['step'])
                                <i class="fas fa-check"></i>
                            @else
                                <i class="fas {{ $s['icon'] }}"></i>
                            @endif
                        </div>
                        <span @class(['text-xs mt-2 font-medium', 'text-primary-600' => $step >= $s['step'], 'text-gray-400' => $step < $s['step']])>{{ $s['label'] }}</span>
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        
        {{-- Step 1: Info Utama --}}
        @if($step === 1)
        <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-primary-50 to-white">
            <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-info-circle text-primary-500"></i> Informasi Utama
            </h2>
        </div>
        <div class="p-5 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Perpustakaan <span class="text-red-500">*</span></label>
                    <select wire:model.live="branch_id" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500" {{ auth()->user()->role !== 'super_admin' ? 'disabled' : '' }}>
                        @foreach($branches as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('branch_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Lokasi Rak</label>
                    <select wire:model="location_id" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">-- Pilih Lokasi --</option>
                        @foreach($locations as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">GMD (Tipe Media)</label>
                    <select wire:model="media_type_id" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">-- Pilih GMD --</option>
                        @foreach($mediaTypes as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Judul <span class="text-red-500">*</span></label>
                <textarea wire:model="title" rows="2" class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Masukkan judul bibliografi"></textarea>
                @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Edisi</label>
                    <input type="text" wire:model="edition" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="cth: Ed. 2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Content Type</label>
                    <select wire:model="content_type_id" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">-- Pilih --</option>
                        @foreach($contentTypes as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                @if(!$isEdit)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah Eksemplar</label>
                    <input type="number" wire:model="item_qty" min="1" max="100" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Step 2: Penulis & Subjek --}}
        @if($step === 2)
        <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-primary-50 to-white">
            <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-users text-primary-500"></i> Penulis & Subjek
            </h2>
        </div>
        <div class="p-5 space-y-5">
            {{-- Authors --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2"><i class="fas fa-user-edit text-gray-400 mr-1"></i> Penulis</label>
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <input type="text" wire:model.live.debounce.300ms="authorSearch" @focus="open = true" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Ketik nama penulis...">
                            <div wire:loading wire:target="authorSearch" class="absolute right-3 top-1/2 -translate-y-1/2">
                                <i class="fas fa-spinner fa-spin text-gray-400"></i>
                            </div>
                        </div>
                        @if(strlen($authorSearch) >= 2)
                        <button type="button" wire:click="createAuthor" class="px-4 py-2.5 bg-primary-100 text-primary-700 text-sm font-medium rounded-xl hover:bg-primary-200 transition flex items-center gap-1.5 whitespace-nowrap">
                            <i class="fas fa-plus"></i> Tambah Baru
                        </button>
                        @endif
                    </div>
                    
                    @if(count($authorResults) > 0)
                    <div x-show="open" x-cloak class="absolute z-40 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-48 overflow-y-auto">
                        @foreach($authorResults as $author)
                            <button type="button" wire:click="addAuthor({{ $author['id'] }}, '{{ addslashes($author['name']) }}')" @click="open = false" class="w-full px-4 py-2.5 text-left text-sm hover:bg-primary-50 flex items-center gap-2 border-b border-gray-50 last:border-0">
                                <span class="w-7 h-7 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center text-xs font-medium">{{ strtoupper(substr($author['name'], 0, 1)) }}</span>
                                <span>{{ $author['name'] }}</span>
                            </button>
                        @endforeach
                    </div>
                    @endif
                </div>
                <p class="text-xs text-gray-400 mt-1.5">Cari penulis atau klik "Tambah Baru"</p>
                
                @if(count($selectedAuthors) > 0)
                <div class="flex flex-wrap gap-2 mt-3">
                    @foreach($selectedAuthors as $author)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary-100 text-primary-700 text-sm rounded-lg">
                            <i class="fas fa-user text-xs"></i> {{ $author['name'] }}
                            <button type="button" wire:click="removeAuthor({{ $author['id'] }})" class="hover:text-primary-900 ml-1">
                                <i class="fas fa-times"></i>
                            </button>
                        </span>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Subjects --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2"><i class="fas fa-tags text-gray-400 mr-1"></i> Subjek/Topik</label>
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <input type="text" wire:model.live.debounce.300ms="subjectSearch" @focus="open = true" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500" placeholder="Ketik subjek/topik...">
                            <div wire:loading wire:target="subjectSearch" class="absolute right-3 top-1/2 -translate-y-1/2">
                                <i class="fas fa-spinner fa-spin text-gray-400"></i>
                            </div>
                        </div>
                        @if(strlen($subjectSearch) >= 2)
                        <button type="button" wire:click="createSubject" class="px-4 py-2.5 bg-amber-100 text-amber-700 text-sm font-medium rounded-xl hover:bg-amber-200 transition flex items-center gap-1.5 whitespace-nowrap">
                            <i class="fas fa-plus"></i> Tambah Baru
                        </button>
                        @endif
                    </div>
                    
                    @if(count($subjectResults) > 0)
                    <div x-show="open" x-cloak class="absolute z-40 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-48 overflow-y-auto">
                        @foreach($subjectResults as $subject)
                            <button type="button" wire:click="addSubject({{ $subject['id'] }}, '{{ addslashes($subject['name']) }}')" @click="open = false" class="w-full px-4 py-2.5 text-left text-sm hover:bg-amber-50 flex items-center gap-2 border-b border-gray-50 last:border-0">
                                <span class="w-7 h-7 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center text-xs font-medium">#</span>
                                <span>{{ $subject['name'] }}</span>
                            </button>
                        @endforeach
                    </div>
                    @endif
                </div>
                <p class="text-xs text-gray-400 mt-1.5">Cari subjek atau klik "Tambah Baru"</p>
                
                @if(count($selectedSubjects) > 0)
                <div class="flex flex-wrap gap-2 mt-3">
                    @foreach($selectedSubjects as $subject)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-100 text-amber-700 text-sm rounded-lg">
                            <i class="fas fa-tag text-xs"></i> {{ $subject['name'] }}
                            <button type="button" wire:click="removeSubject({{ $subject['id'] }})" class="hover:text-amber-900 ml-1">
                                <i class="fas fa-times"></i>
                            </button>
                        </span>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Step 3: Penerbitan & Klasifikasi --}}
        @if($step === 3)
        @include('livewire.staff.biblio.partials.step3-publishing')
        @endif

        {{-- Step 4: Detail & File --}}
        @if($step === 4)
        @include('livewire.staff.biblio.partials.step4-detail')
        @endif

        {{-- Navigation --}}
        <div class="p-5 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
            <div>
                @if($step > 1)
                    <button wire:click="previousStep" type="button" class="px-4 py-2.5 text-gray-600 hover:text-gray-900 text-sm font-medium rounded-xl hover:bg-gray-200 bg-gray-100 transition flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </button>
                @endif
            </div>
            <div class="flex items-center gap-2">
                @if($step < $totalSteps)
                    <button wire:click="nextStep" type="button" class="px-5 py-2.5 bg-primary-600 text-white text-sm font-semibold rounded-xl hover:bg-primary-700 transition flex items-center gap-2">
                        Lanjut <i class="fas fa-arrow-right"></i>
                    </button>
                @else
                    <button wire:click="save" type="button" class="px-5 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 transition flex items-center gap-2">
                        <i class="fas fa-check"></i> Simpan Bibliografi
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Loading Overlay --}}
    <div wire:loading.flex wire:target="save,nextStep,previousStep" class="fixed inset-0 bg-black/50 items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-6 flex items-center gap-4 shadow-xl">
            <i class="fas fa-spinner fa-spin text-2xl text-primary-600"></i>
            <span class="text-gray-900 font-medium">Memproses...</span>
        </div>
    </div>

    {{-- DDC Modal via teleport --}}
    @include('livewire.staff.biblio.partials.ddc-modal')
</div>

@script
<script>
$wire.on('showSuccess', ({ title, message }) => {
    Swal.fire({
        icon: 'success',
        title: title,
        text: message,
        timer: 2000,
        showConfirmButton: false,
        timerProgressBar: true
    });
});
</script>
@endscript
