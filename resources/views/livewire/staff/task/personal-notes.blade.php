<div class="min-h-screen bg-gradient-to-br from-slate-50 via-gray-50 to-amber-50/30">
    {{-- Shared Navigation Header --}}
    <div class="bg-white/80 backdrop-blur-sm border-b border-gray-100 sticky top-0 z-30">
        <div class="max-w-[1600px] mx-auto px-4 py-3">
            <div class="flex items-center justify-between">
                {{-- Title --}}
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-amber-500/25">
                        <i class="fas fa-sticky-note text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-gray-900">Personal Notes</h1>
                        <p class="text-xs text-gray-500">Catatan pribadi Anda</p>
                    </div>
                </div>
                
                {{-- 4-Tab Navigation --}}
                <div class="flex items-center bg-gray-100/80 rounded-xl p-1">
                    <a href="{{ route('staff.task.index') }}" 
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-all text-gray-500 hover:text-gray-700">
                        <i class="fas fa-columns mr-1.5"></i>Kanban
                    </a>
                    <a href="{{ route('staff.task.timeline') }}" 
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-all text-gray-500 hover:text-gray-700">
                        <i class="fas fa-chart-gantt mr-1.5"></i>Timeline
                    </a>
                    <a href="{{ route('staff.task.schedule') }}" 
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-all text-gray-500 hover:text-gray-700">
                        <i class="fas fa-calendar-alt mr-1.5"></i>Jadwal
                    </a>
                    <a href="{{ route('staff.task.notes') }}" 
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-all bg-white text-amber-600 shadow-sm">
                        <i class="fas fa-sticky-note mr-1.5"></i>Notes
                    </a>
                </div>
                
                {{-- Actions --}}
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.300ms="searchQuery" 
                               placeholder="Cari catatan..."
                               class="pl-9 pr-4 py-2 text-sm border-gray-200 rounded-xl focus:ring-amber-500 focus:border-amber-500 w-56">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    </div>
                    <select wire:model.live="filterCategory" class="text-sm border-gray-200 rounded-lg focus:ring-amber-500 focus:border-amber-500">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $key => $cat)
                            <option value="{{ $key }}">{{ $cat['label'] }}</option>
                        @endforeach
                    </select>
                    <button wire:click="openCreateModal" 
                            class="px-4 py-2 bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-xl font-semibold text-sm hover:from-amber-600 hover:to-orange-600 shadow-lg shadow-amber-500/25 transition-all flex items-center gap-2">
                        <i class="fas fa-plus"></i>
                        <span>Catatan Baru</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Notes Grid --}}
    <div class="max-w-[1600px] mx-auto p-4">
        @if($notes->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($notes as $note)
                @php $colorClasses = $note->getColorClasses(); @endphp
                <div class="group relative {{ $colorClasses['bg'] }} {{ $colorClasses['border'] }} border rounded-2xl p-4 hover:shadow-lg transition-all duration-300 hover:-translate-y-0.5">
                    {{-- Pin Badge --}}
                    @if($note->is_pinned)
                    <div class="absolute -top-2 -right-2 w-6 h-6 bg-amber-400 rounded-full flex items-center justify-center shadow-lg">
                        <i class="fas fa-thumbtack text-white text-[10px]"></i>
                    </div>
                    @endif
                    
                    {{-- Header --}}
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-2">
                            @php $catInfo = $note->getCategoryInfo(); @endphp
                            <div class="w-7 h-7 rounded-lg {{ $colorClasses['bg'] }} {{ $colorClasses['border'] }} border flex items-center justify-center">
                                <i class="fas {{ $catInfo['icon'] }} {{ $colorClasses['text'] }} text-xs"></i>
                            </div>
                            <span class="text-[10px] font-semibold uppercase tracking-wider {{ $colorClasses['text'] }}">{{ $catInfo['label'] }}</span>
                        </div>
                        
                        {{-- Actions --}}
                        <div class="opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1">
                            <button wire:click="togglePin({{ $note->id }})" 
                                    class="w-7 h-7 rounded-lg hover:bg-white/80 flex items-center justify-center transition {{ $note->is_pinned ? 'text-amber-500' : 'text-gray-400' }}">
                                <i class="fas fa-thumbtack text-xs"></i>
                            </button>
                            <button wire:click="openEditModal({{ $note->id }})" 
                                    class="w-7 h-7 rounded-lg hover:bg-white/80 flex items-center justify-center text-gray-400 hover:text-blue-500 transition">
                                <i class="fas fa-pen text-xs"></i>
                            </button>
                            <button wire:click="delete({{ $note->id }})" 
                                    wire:confirm="Yakin hapus catatan ini?"
                                    class="w-7 h-7 rounded-lg hover:bg-white/80 flex items-center justify-center text-gray-400 hover:text-red-500 transition">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </div>
                    </div>
                    
                    {{-- Content --}}
                    <h3 class="font-bold text-gray-900 mb-2 line-clamp-2">{{ $note->title }}</h3>
                    @if($note->content)
                    <p class="text-sm text-gray-600 line-clamp-4 mb-3">{!! nl2br(e(Str::limit($note->content, 200))) !!}</p>
                    @endif
                    
                    {{-- Footer --}}
                    <div class="flex items-center justify-between text-[10px] text-gray-400 pt-2 border-t {{ $colorClasses['border'] }}">
                        <span>{{ $note->updated_at->diffForHumans() }}</span>
                        @if($note->content)
                        <span>{{ Str::wordCount($note->content) }} kata</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-20">
            <div class="w-24 h-24 bg-gradient-to-br from-amber-100 to-orange-100 rounded-3xl flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-sticky-note text-amber-300 text-4xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Catatan</h3>
            <p class="text-gray-500 mb-6">Mulai tulis catatan pribadi Anda</p>
            <button wire:click="openCreateModal" 
                    class="px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-xl font-semibold hover:from-amber-600 hover:to-orange-600 shadow-lg shadow-amber-500/25 transition-all">
                <i class="fas fa-plus mr-2"></i>Buat Catatan Pertama
            </button>
        </div>
        @endif
    </div>

    {{-- Modal --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         x-data x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden" @click.away="$wire.closeModal()">
            {{-- Modal Header --}}
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-amber-50 to-orange-50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center text-white">
                        <i class="fas {{ $editMode ? 'fa-pen' : 'fa-plus' }}"></i>
                    </div>
                    <h3 class="font-bold text-gray-900">{{ $editMode ? 'Edit Catatan' : 'Catatan Baru' }}</h3>
                </div>
                <button wire:click="closeModal" class="w-8 h-8 rounded-lg hover:bg-white/80 flex items-center justify-center text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            {{-- Modal Body --}}
            <div class="p-6 space-y-4">
                {{-- Title --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Judul</label>
                    <input type="text" wire:model="title" 
                           class="w-full border-gray-200 rounded-xl focus:ring-amber-500 focus:border-amber-500"
                           placeholder="Judul catatan...">
                    @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                
                {{-- Content --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Konten</label>
                    <textarea wire:model="content" rows="5"
                              class="w-full border-gray-200 rounded-xl focus:ring-amber-500 focus:border-amber-500"
                              placeholder="Tulis catatan Anda..."></textarea>
                </div>
                
                {{-- Category & Color --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori</label>
                        <select wire:model="category" class="w-full border-gray-200 rounded-xl focus:ring-amber-500 focus:border-amber-500">
                            @foreach($categories as $key => $cat)
                                <option value="{{ $key }}">{{ $cat['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Warna</label>
                        <div class="flex gap-2">
                            @foreach($colors as $key => $clr)
                                <button type="button" wire:click="$set('color', '{{ $key }}')"
                                        class="w-7 h-7 rounded-full {{ $clr['bg'] }} {{ $clr['border'] }} border-2 transition-transform {{ $color === $key ? 'ring-2 ring-offset-2 ring-amber-400 scale-110' : 'hover:scale-105' }}">
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Modal Footer --}}
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3">
                <button wire:click="closeModal" class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium transition">
                    Batal
                </button>
                <button wire:click="save" 
                        class="px-6 py-2 bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-xl font-semibold hover:from-amber-600 hover:to-orange-600 shadow-lg shadow-amber-500/25 transition-all">
                    {{ $editMode ? 'Simpan Perubahan' : 'Buat Catatan' }}
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
