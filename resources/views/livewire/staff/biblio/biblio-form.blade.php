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
        <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-primary-50 to-white flex items-center justify-between">
            <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-info-circle text-primary-500"></i> Informasi Utama
            </h2>
            @if(!$isEdit)
            <button type="button" wire:click="openCopyModal" class="px-3 py-1.5 bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white text-xs font-medium rounded-lg transition flex items-center gap-2 shadow-sm">
                <i class="fas fa-globe"></i>
                <span>Cari Online</span>
                <div class="flex -space-x-1">
                    <span class="w-4 h-4 bg-white rounded-full flex items-center justify-center"><i class="fas fa-database text-[8px] text-emerald-500"></i></span>
                    <span class="w-4 h-4 bg-white rounded-full flex items-center justify-center"><i class="fab fa-google text-[8px] text-blue-500"></i></span>
                    <span class="w-4 h-4 bg-white rounded-full flex items-center justify-center"><i class="fas fa-book-open text-[8px] text-orange-500"></i></span>
                </div>
            </button>
            @endif
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
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Lokasi Rak <span class="text-red-500">*</span></label>
                    <select wire:model="location_id" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">-- Pilih Lokasi --</option>
                        @foreach($locations as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('location_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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
                <label class="block text-sm font-medium text-gray-700 mb-2"><i class="fas fa-user-edit text-gray-400 mr-1"></i> Penulis <span class="text-red-500">*</span></label>
                @error('selectedAuthors') <p class="text-red-500 text-xs mb-2">{{ $message }}</p> @enderror
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
                <label class="block text-sm font-medium text-gray-700 mb-2"><i class="fas fa-tags text-gray-400 mr-1"></i> Subjek</label>
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <input type="text" wire:model.live.debounce.300ms="subjectSearch" @focus="open = true" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500" placeholder="Cari subjek...">
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

    {{-- Loading Overlay - Teleported to body for highest z-index --}}
    <template x-teleport="body">
        <div wire:loading.flex wire:target="save,nextStep,previousStep" class="fixed inset-0 bg-black/50 items-center justify-center z-[9999]">
            <div class="bg-white rounded-2xl p-6 flex items-center gap-4 shadow-xl">
                <i class="fas fa-spinner fa-spin text-2xl text-primary-600"></i>
                <span class="text-gray-900 font-medium">Memproses...</span>
            </div>
        </div>
    </template>

    {{-- DDC Modal via teleport --}}
    @include('livewire.staff.biblio.partials.ddc-modal')

    {{-- Copy Catalog Modal --}}
    <template x-teleport="body">
        <div x-data="{ show: @entangle('showCopyModal') }" x-show="show" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-transition>
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden" @click.outside="show = false">
                {{-- Header --}}
                <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-blue-600 to-indigo-600">
                    <div class="flex items-center justify-between">
                        <div class="text-white">
                            <h3 class="text-lg font-bold flex items-center gap-2"><i class="fas fa-search-plus"></i> Cari Data Bibliografi</h3>
                            <p class="text-blue-100 text-sm mt-0.5">Temukan data buku untuk mempercepat proses input katalog</p>
                        </div>
                        <button @click="show = false" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/20 hover:bg-white/30 text-white transition"><i class="fas fa-times"></i></button>
                    </div>
                    {{-- Search Input --}}
                    <div class="mt-4 flex gap-2">
                        <div class="flex-1 relative">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" wire:model="copySearch" wire:keydown.enter="searchCatalog" placeholder="Ketik ISBN, judul buku, atau nama penulis..." class="w-full pl-11 pr-4 py-3 text-sm bg-white rounded-xl focus:ring-2 focus:ring-white/50 border-0">
                        </div>
                        <button wire:click="searchCatalog" wire:loading.attr="disabled" class="px-6 py-3 bg-white text-blue-600 text-sm font-semibold rounded-xl hover:bg-blue-50 transition flex items-center gap-2">
                            <span wire:loading.remove wire:target="searchCatalog">Cari</span>
                            <span wire:loading wire:target="searchCatalog"><i class="fas fa-spinner fa-spin"></i></span>
                        </button>
                    </div>
                    {{-- Source Info --}}
                    <div class="mt-3 flex items-center gap-4 text-xs text-blue-100">
                        <span class="flex items-center gap-1.5"><i class="fas fa-database"></i> Katalog UNIDA</span>
                        <span class="flex items-center gap-1.5"><i class="fab fa-google"></i> Google Books</span>
                        <span class="flex items-center gap-1.5"><i class="fas fa-globe"></i> Open Library</span>
                    </div>
                </div>

                {{-- Results --}}
                <div class="p-5 overflow-y-auto" style="max-height: calc(90vh - 180px);">
                    @if(empty($internalResults) && empty($googleResults) && empty($openLibraryResults) && !$copySearch)
                    <div class="text-center py-12">
                        <div class="w-20 h-20 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-lightbulb text-3xl text-amber-500"></i>
                        </div>
                        <h4 class="text-gray-800 font-semibold mb-2">Cari Data untuk Input Cepat</h4>
                        <p class="text-gray-500 text-sm max-w-md mx-auto">Gunakan <span class="font-medium text-blue-600">ISBN</span> untuk hasil paling akurat, atau cari berdasarkan <span class="font-medium text-blue-600">judul</span> dan <span class="font-medium text-blue-600">penulis</span></p>
                        <div class="flex justify-center gap-6 mt-6">
                            <div class="flex flex-col items-center gap-1">
                                <span class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center"><i class="fas fa-database text-emerald-600"></i></span>
                                <span class="text-[10px] text-gray-500">Katalog UNIDA</span>
                            </div>
                            <div class="flex flex-col items-center gap-1">
                                <span class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center"><i class="fab fa-google text-blue-600"></i></span>
                                <span class="text-[10px] text-gray-500">Google Books</span>
                            </div>
                            <div class="flex flex-col items-center gap-1">
                                <span class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center"><i class="fas fa-globe text-orange-600"></i></span>
                                <span class="text-[10px] text-gray-500">Open Library</span>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="space-y-6">
                        {{-- Internal Results --}}
                        @if(!empty($internalResults))
                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <span class="w-6 h-6 bg-emerald-500 rounded-lg flex items-center justify-center"><i class="fas fa-database text-white text-xs"></i></span>
                                <h4 class="font-semibold text-gray-800">Katalog Internal UNIDA Gontor</h4>
                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 text-xs font-medium rounded-full">{{ count($internalResults) }} hasil</span>
                            </div>
                            <p class="text-xs text-gray-500 mb-2 -mt-1">Data dari seluruh cabang perpustakaan Universitas Darussalam Gontor</p>
                            <div class="space-y-2">
                                @foreach($internalResults as $result)
                                <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-100 hover:border-emerald-300 transition group">
                                    <div class="flex gap-3">
                                        <div class="w-12 h-16 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0 shadow-sm">
                                            @if($result['image'])
                                            <img src="{{ asset('storage/' . (str_starts_with($result['image'], 'covers/') ? $result['image'] : 'covers/' . $result['image'])) }}" class="w-full h-full object-cover">
                                            @else
                                            <div class="w-full h-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center"><i class="fas fa-book text-white/80"></i></div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-gray-900 text-sm line-clamp-1">{{ $result['title'] }}</p>
                                            <p class="text-xs text-gray-600">{{ collect($result['authors'])->pluck('name')->implode(', ') ?: '-' }}</p>
                                            <div class="flex flex-wrap items-center gap-2 mt-1 text-xs">
                                                @if($result['isbn'])<span class="text-gray-500"><i class="fas fa-barcode mr-1"></i>{{ $result['isbn'] }}</span>@endif
                                                @if($result['classification'])<span class="text-emerald-600 font-medium">DDC: {{ $result['classification'] }}</span>@endif
                                                <span class="px-1.5 py-0.5 bg-emerald-200 text-emerald-800 rounded text-[10px]">{{ $result['branch']['name'] ?? '-' }}</span>
                                                <span class="text-gray-400">{{ $result['items_count'] }} eks</span>
                                            </div>
                                        </div>
                                        <button wire:click="copyCatalog({{ $result['id'] }})" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold rounded-lg transition self-center opacity-80 group-hover:opacity-100">
                                            <i class="fas fa-clone mr-1"></i> Salin
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- Google Books Results --}}
                        @if(!empty($googleResults))
                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <span class="w-6 h-6 bg-blue-500 rounded-lg flex items-center justify-center"><i class="fab fa-google text-white text-xs"></i></span>
                                <h4 class="font-semibold text-gray-800">Google Books</h4>
                                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-medium rounded-full">{{ count($googleResults) }} hasil</span>
                            </div>
                            <p class="text-xs text-gray-500 mb-2 -mt-1">Database buku terlengkap dari Google dengan cover & deskripsi</p>
                            <div class="space-y-2">
                                @foreach($googleResults as $index => $result)
                                <div class="p-3 bg-blue-50 rounded-xl border border-blue-100 hover:border-blue-300 transition group">
                                    <div class="flex gap-3">
                                        <div class="w-12 h-16 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0 shadow-sm">
                                            @if($result['cover'])
                                            <img src="{{ $result['cover'] }}" class="w-full h-full object-cover">
                                            @else
                                            <div class="w-full h-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center"><i class="fas fa-book text-white/80"></i></div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-gray-900 text-sm line-clamp-1">{{ $result['title'] }}</p>
                                            <p class="text-xs text-gray-600">{{ implode(', ', $result['authors']) ?: '-' }}</p>
                                            <div class="flex flex-wrap items-center gap-2 mt-1 text-xs">
                                                @if($result['isbn'])<span class="text-gray-500"><i class="fas fa-barcode mr-1"></i>{{ $result['isbn'] }}</span>@endif
                                                @if($result['publisher'])<span class="text-gray-500"><i class="fas fa-building mr-1"></i>{{ Str::limit($result['publisher'], 20) }}</span>@endif
                                                @if($result['publishedDate'])<span class="text-blue-600 font-medium">{{ substr($result['publishedDate'], 0, 4) }}</span>@endif
                                                @if($result['pageCount'])<span class="text-gray-400">{{ $result['pageCount'] }} hlm</span>@endif
                                            </div>
                                        </div>
                                        <button wire:click="useGoogleBook({{ $index }})" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold rounded-lg transition self-center opacity-80 group-hover:opacity-100">
                                            <i class="fas fa-download mr-1"></i> Gunakan
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- Open Library Results --}}
                        @if(!empty($openLibraryResults))
                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <span class="w-6 h-6 bg-orange-500 rounded-lg flex items-center justify-center"><i class="fas fa-globe text-white text-xs"></i></span>
                                <h4 class="font-semibold text-gray-800">Open Library</h4>
                                <span class="px-2 py-0.5 bg-orange-100 text-orange-700 text-xs font-medium rounded-full">{{ count($openLibraryResults) }} hasil</span>
                            </div>
                            <p class="text-xs text-gray-500 mb-2 -mt-1">Database open source dengan klasifikasi DDC</p>
                            <div class="space-y-2">
                                @foreach($openLibraryResults as $index => $result)
                                <div class="p-3 bg-orange-50 rounded-xl border border-orange-100 hover:border-orange-300 transition group">
                                    <div class="flex gap-3">
                                        <div class="w-12 h-16 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0 shadow-sm">
                                            @if($result['cover'])
                                            <img src="{{ $result['cover'] }}" class="w-full h-full object-cover">
                                            @else
                                            <div class="w-full h-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center"><i class="fas fa-book text-white/80"></i></div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-gray-900 text-sm line-clamp-1">{{ $result['title'] }}</p>
                                            <p class="text-xs text-gray-600">{{ implode(', ', $result['authors']) ?: '-' }}</p>
                                            <div class="flex flex-wrap items-center gap-2 mt-1 text-xs">
                                                @if($result['isbn'])<span class="text-gray-500"><i class="fas fa-barcode mr-1"></i>{{ $result['isbn'] }}</span>@endif
                                                @if($result['ddc'])<span class="text-orange-600 font-medium">DDC: {{ $result['ddc'] }}</span>@endif
                                                @if($result['publishYear'])<span class="text-gray-500">{{ $result['publishYear'] }}</span>@endif
                                            </div>
                                        </div>
                                        <button wire:click="useOpenLibrary({{ $index }})" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold rounded-lg transition self-center opacity-80 group-hover:opacity-100">
                                            <i class="fas fa-download mr-1"></i> Gunakan
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- No Results --}}
                        @if(empty($internalResults) && empty($googleResults) && empty($openLibraryResults) && $copySearch)
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-search text-2xl text-gray-400"></i>
                            </div>
                            <h4 class="text-gray-600 font-medium mb-1">Tidak ditemukan hasil</h4>
                            <p class="text-gray-400 text-sm">Coba kata kunci lain atau input data secara manual</p>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </template>
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
