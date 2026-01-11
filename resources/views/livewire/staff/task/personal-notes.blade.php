@section('title', 'Notes')

@push('styles')
<style>
    .note-card { transition: all 0.2s ease; }
    .note-card:hover { transform: translateY(-2px); box-shadow: 0 8px 20px -4px rgba(0,0,0,0.12); }
    .stat-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .stat-card:hover { transform: translateY(-1px); }
</style>
@endpush

<div class="space-y-5">
    {{-- Header - Unified across all tabs --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-gradient-to-br from-violet-500 via-purple-500 to-fuchsia-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-purple-500/30">
                <i class="fas fa-clipboard-list text-2xl"></i>
            </div>
            <div>
                <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Tugas & Jadwal</h1>
                <p class="text-sm text-gray-500 flex items-center gap-2">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                    Kolaborasi pekerjaan perpustakaan
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
            {{-- Unified 4-Tab Navigation with wire:navigate --}}
            <div class="bg-white rounded-xl p-1 border border-gray-200 flex shadow-sm">
                <a href="{{ route('staff.task.index') }}" wire:navigate
                   class="px-3 py-2 text-sm font-medium rounded-lg transition flex items-center gap-2 text-gray-600 hover:bg-gray-100">
                    <i class="fas fa-columns"></i>
                    <span class="hidden sm:inline">Kanban</span>
                </a>
                <a href="{{ route('staff.task.timeline') }}" wire:navigate
                   class="px-3 py-2 text-sm font-medium rounded-lg transition flex items-center gap-2 text-gray-600 hover:bg-gray-100">
                    <i class="fas fa-chart-gantt"></i>
                    <span class="hidden sm:inline">Timeline</span>
                </a>
                <a href="{{ route('staff.task.schedule') }}" wire:navigate
                   class="px-3 py-2 text-sm font-medium rounded-lg transition flex items-center gap-2 text-gray-600 hover:bg-gray-100">
                    <i class="fas fa-calendar-alt"></i>
                    <span class="hidden sm:inline">Jadwal</span>
                </a>
                <span class="px-3 py-2 text-sm font-medium rounded-lg bg-gradient-to-r from-violet-600 to-purple-600 text-white shadow-sm flex items-center gap-2">
                    <i class="fas fa-sticky-note"></i>
                    <span class="hidden sm:inline">Notes</span>
                </span>
            </div>
            
            {{-- Personal/Public Toggle --}}
            <div class="bg-white rounded-xl p-1 border border-gray-200 flex shadow-sm">
                <button wire:click="$set('viewMode', 'personal')" 
                        class="px-3 py-1.5 text-sm font-medium rounded-lg transition flex items-center gap-1.5 {{ $viewMode === 'personal' ? 'bg-violet-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="fas fa-lock text-xs"></i>Pribadi
                </button>
                <button wire:click="$set('viewMode', 'public')" 
                        class="px-3 py-1.5 text-sm font-medium rounded-lg transition flex items-center gap-1.5 {{ $viewMode === 'public' ? 'bg-violet-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="fas fa-globe text-xs"></i>Team
                </button>
            </div>
            
            {{-- Search --}}
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="searchQuery" 
                       placeholder="Cari catatan..." 
                       class="pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm w-48 focus:w-64 transition-all focus:ring-2 focus:ring-violet-500/20 focus:border-violet-400 shadow-sm">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            </div>
            
            <button wire:click="openCreateModal" 
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 text-white font-medium rounded-xl shadow-lg shadow-purple-500/25 transition text-sm">
                <i class="fas fa-plus"></i>
                <span class="hidden sm:inline">Buat Catatan</span>
            </button>
        </div>
    </div>

    {{-- Compact Stats Cards - Same 5-column grid as others --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
        <div class="stat-card bg-gradient-to-r from-violet-500 to-purple-600 rounded-xl px-4 py-3 text-white flex items-center justify-between">
            <div class="w-9 h-9 bg-white/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-sticky-note text-sm"></i>
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold">{{ $notes->count() }}</p>
                <p class="text-[10px] text-violet-100 uppercase tracking-wide">Total</p>
            </div>
        </div>
        <div class="stat-card bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl px-4 py-3 text-white flex items-center justify-between">
            <div class="w-9 h-9 bg-white/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-{{ $viewMode === 'personal' ? 'lock' : 'globe' }} text-sm"></i>
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold">{{ $viewMode === 'personal' ? 'Saya' : 'Team' }}</p>
                <p class="text-[10px] text-blue-100 uppercase tracking-wide">Mode</p>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl px-4 py-3 border border-gray-100 shadow-sm flex items-center justify-between">
            <div class="w-9 h-9 bg-amber-100 rounded-lg flex items-center justify-center text-amber-500">
                <i class="fas fa-thumbtack text-sm"></i>
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold text-gray-900">{{ $notes->where('is_pinned', true)->count() }}</p>
                <p class="text-[10px] text-gray-500 uppercase tracking-wide">Disematkan</p>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl px-4 py-3 border border-gray-100 shadow-sm flex items-center justify-between">
            <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center text-blue-500">
                <i class="fas fa-briefcase text-sm"></i>
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold text-gray-900">{{ $notes->where('category', 'work')->count() }}</p>
                <p class="text-[10px] text-gray-500 uppercase tracking-wide">Kerja</p>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl px-4 py-3 border border-gray-100 shadow-sm flex items-center justify-between">
            <div class="w-9 h-9 bg-yellow-100 rounded-lg flex items-center justify-center text-yellow-600">
                <i class="fas fa-lightbulb text-sm"></i>
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold text-gray-900">{{ $notes->where('category', 'ideas')->count() }}</p>
                <p class="text-[10px] text-gray-500 uppercase tracking-wide">Ide</p>
            </div>
        </div>
    </div>

    {{-- Filter by Category --}}
    <div class="flex items-center gap-2 overflow-x-auto pb-1">
        <button wire:click="$set('filterCategory', '')" 
                class="px-3 py-1.5 text-sm font-medium rounded-lg transition whitespace-nowrap {{ !$filterCategory ? 'bg-violet-100 text-violet-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            Semua
        </button>
        @foreach($categories as $key => $cat)
        <button wire:click="$set('filterCategory', '{{ $key }}')" 
                class="px-3 py-1.5 text-sm font-medium rounded-lg transition flex items-center gap-1.5 whitespace-nowrap {{ $filterCategory === $key ? 'bg-violet-100 text-violet-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            <i class="fas {{ $cat['icon'] }} text-xs"></i>{{ $cat['label'] }}
        </button>
        @endforeach
    </div>

    {{-- Notes Grid --}}
    @if($notes->count() > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach($notes as $note)
            @php $colorClasses = $note->getColorClasses(); @endphp
            <div class="note-card group relative {{ $colorClasses['bg'] }} {{ $colorClasses['border'] }} border rounded-2xl p-4">
                {{-- Pin Badge --}}
                @if($note->is_pinned)
                <div class="absolute -top-2 -right-2 w-6 h-6 bg-amber-400 rounded-full flex items-center justify-center shadow-lg">
                    <i class="fas fa-thumbtack text-white text-[10px]"></i>
                </div>
                @endif
                
                {{-- Public Badge --}}
                @if($note->is_public)
                <div class="absolute -top-2 {{ $note->is_pinned ? '-left-2' : '-right-2' }} px-2 py-0.5 bg-blue-500 text-white text-[9px] font-bold rounded-full flex items-center gap-1 shadow-lg">
                    <i class="fas fa-globe text-[8px]"></i>Publik
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
                    
                    {{-- Actions (only for owner) --}}
                    @if($note->user_id === auth()->id())
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
                    @endif
                </div>
                
                {{-- Content --}}
                <h3 class="font-bold text-gray-900 mb-2 line-clamp-2">{{ $note->title }}</h3>
                @if($note->content)
                <p class="text-sm text-gray-600 line-clamp-4 mb-3">{!! nl2br(e(Str::limit($note->content, 200))) !!}</p>
                @endif
                
                {{-- Footer --}}
                <div class="flex items-center justify-between text-[10px] text-gray-400 pt-2 border-t {{ $colorClasses['border'] }}">
                    <div class="flex items-center gap-1">
                        @if($viewMode === 'public' && $note->user)
                        <span class="font-medium text-gray-500">{{ Str::limit($note->user->name, 15) }}</span>
                        <span>•</span>
                        @endif
                        <span>{{ $note->updated_at->diffForHumans() }}</span>
                    </div>
                    @if($note->content)
                    <span>{{ Str::wordCount($note->content) }} kata</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-20 bg-white rounded-2xl border border-gray-100">
        <div class="w-24 h-24 bg-gradient-to-br from-violet-100 to-purple-100 rounded-3xl flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-sticky-note text-violet-300 text-4xl"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Catatan</h3>
        <p class="text-gray-500 mb-6">{{ $viewMode === 'personal' ? 'Mulai tulis catatan pribadi Anda' : 'Belum ada catatan publik dari tim' }}</p>
        <button wire:click="openCreateModal" 
                class="px-6 py-3 bg-gradient-to-r from-violet-600 to-purple-600 text-white rounded-xl font-semibold hover:from-violet-700 hover:to-purple-700 shadow-lg shadow-purple-500/25 transition-all">
            <i class="fas fa-plus mr-2"></i>Buat Catatan Pertama
        </button>
    </div>
    @endif

    {{-- Modal (Teleport to body) --}}
    @if($showModal)
    @teleport('body')
    <div class="fixed inset-0 z-[99999] flex items-center justify-center p-4"
         x-data="{ show: false }"
         x-init="$nextTick(() => show = true); document.body.style.overflow = 'hidden'"
         x-on:remove="document.body.style.overflow = ''">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-gradient-to-br from-gray-900/60 via-gray-900/50 to-purple-900/40 backdrop-blur-md"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             wire:click="closeModal"></div>
        
        {{-- Modal Container --}}
        <div class="relative w-full max-w-xl"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0">
            
            <div class="relative bg-white rounded-3xl shadow-2xl overflow-hidden ring-1 ring-black/5">
                {{-- Decorative Background --}}
                <div class="absolute inset-0 bg-gradient-to-br from-violet-500/5 via-transparent to-purple-500/5 pointer-events-none"></div>
                
                {{-- Modal Header --}}
                <div class="relative px-6 py-5 border-b border-gray-100/80 bg-gradient-to-r from-violet-50/80 via-purple-50/60 to-fuchsia-50/40">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="relative">
                                <div class="w-12 h-12 bg-gradient-to-br from-violet-500 via-purple-500 to-fuchsia-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-purple-500/30">
                                    <i class="fas {{ $editMode ? 'fa-pen-fancy' : 'fa-plus' }} text-lg"></i>
                                </div>
                                <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-white rounded-lg shadow-sm flex items-center justify-center">
                                    <i class="fas fa-sticky-note text-violet-500 text-[10px]"></i>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ $editMode ? 'Edit Catatan' : 'Buat Catatan Baru' }}</h3>
                                <p class="text-sm text-gray-500">{{ $editMode ? 'Perbarui catatan Anda' : 'Tambahkan catatan untuk tim' }}</p>
                            </div>
                        </div>
                        <button wire:click="closeModal" 
                                class="w-10 h-10 rounded-xl bg-white/80 hover:bg-white border border-gray-200 flex items-center justify-center text-gray-400 hover:text-gray-600 transition-all hover:shadow-md hover:scale-105 active:scale-95">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                
                {{-- Modal Body --}}
                <div class="relative p-6 space-y-5 max-h-[60vh] overflow-y-auto">
                    {{-- Title Input --}}
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                            <i class="fas fa-heading text-violet-500 text-xs"></i>
                            Judul Catatan
                            <span class="text-red-400">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" wire:model="title" 
                                   class="w-full px-4 py-3 bg-gray-50/50 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-violet-500/10 focus:border-violet-400 focus:bg-white transition-all placeholder:text-gray-400"
                                   placeholder="Masukkan judul catatan yang menarik...">
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-300">
                                <i class="fas fa-pencil-alt text-sm"></i>
                            </div>
                        </div>
                        @error('title') 
                            <p class="text-red-500 text-xs flex items-center gap-1">
                                <i class="fas fa-exclamation-circle"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>
                    
                    {{-- Content Textarea --}}
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                            <i class="fas fa-align-left text-violet-500 text-xs"></i>
                            Konten
                        </label>
                        <textarea wire:model="content" rows="4"
                                  class="w-full px-4 py-3 bg-gray-50/50 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-violet-500/10 focus:border-violet-400 focus:bg-white transition-all placeholder:text-gray-400 resize-none"
                                  placeholder="Tulis isi catatan Anda di sini..."></textarea>
                    </div>
                    
                    {{-- Category & Color in Cards --}}
                    <div class="grid grid-cols-2 gap-4">
                        {{-- Category Select --}}
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                                <i class="fas fa-folder text-violet-500 text-xs"></i>
                                Kategori
                            </label>
                            <div class="relative">
                                <select wire:model="category" 
                                        class="w-full px-4 py-3 bg-gray-50/50 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-violet-500/10 focus:border-violet-400 focus:bg-white transition-all appearance-none cursor-pointer">
                                    @foreach($categories as $key => $cat)
                                        <option value="{{ $key }}">{{ $cat['label'] }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Color Picker --}}
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                                <i class="fas fa-palette text-violet-500 text-xs"></i>
                                Warna
                            </label>
                            <div class="flex items-center gap-2 p-3 bg-gray-50/50 rounded-xl border-2 border-gray-200">
                                @foreach($colors as $key => $clr)
                                    <button type="button" wire:click="$set('color', '{{ $key }}')"
                                            class="relative w-8 h-8 rounded-full {{ $clr['bg'] }} border-2 {{ $color === $key ? $clr['border'] : 'border-transparent' }} transition-all duration-200 hover:scale-110 {{ $color === $key ? 'scale-110 shadow-lg' : '' }}">
                                        @if($color === $key)
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <i class="fas fa-check {{ $clr['text'] }} text-xs"></i>
                                            </div>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    {{-- Public Toggle Card --}}
                    <div class="p-4 bg-gradient-to-r from-gray-50 via-violet-50/30 to-gray-50 rounded-2xl border border-gray-200/80">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl {{ $is_public ? 'bg-gradient-to-br from-blue-500 to-indigo-500' : 'bg-gray-200' }} flex items-center justify-center transition-all duration-300">
                                    <i class="fas {{ $is_public ? 'fa-globe' : 'fa-lock' }} text-white text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $is_public ? 'Catatan Publik' : 'Catatan Pribadi' }}</p>
                                    <p class="text-xs text-gray-500">{{ $is_public ? 'Semua anggota tim dapat melihat' : 'Hanya Anda yang dapat melihat' }}</p>
                                </div>
                            </div>
                            <button type="button" wire:click="$toggle('is_public')" 
                                    class="relative w-14 h-7 rounded-full transition-all duration-300 {{ $is_public ? 'bg-gradient-to-r from-blue-500 to-indigo-500 shadow-lg shadow-blue-500/30' : 'bg-gray-300' }}">
                                <span class="absolute top-0.5 w-6 h-6 bg-white rounded-full shadow-md transition-all duration-300 {{ $is_public ? 'left-[calc(100%-26px)]' : 'left-0.5' }}"></span>
                            </button>
                        </div>
                    </div>
                </div>
                
                {{-- Modal Footer --}}
                <div class="relative px-6 py-4 border-t border-gray-100 bg-gradient-to-r from-gray-50/80 to-white flex items-center justify-between gap-4">
                    <button wire:click="closeModal" 
                            class="px-5 py-2.5 text-gray-600 hover:text-gray-900 font-medium rounded-xl hover:bg-gray-100 transition-all flex items-center gap-2">
                        <i class="fas fa-arrow-left text-xs"></i>
                        Batal
                    </button>
                    <button wire:click="save" 
                            class="px-6 py-2.5 bg-gradient-to-r from-violet-600 via-purple-600 to-fuchsia-600 hover:from-violet-700 hover:via-purple-700 hover:to-fuchsia-700 text-white rounded-xl font-semibold shadow-lg shadow-purple-500/30 hover:shadow-xl hover:shadow-purple-500/40 transition-all flex items-center gap-2 hover:scale-[1.02] active:scale-[0.98]">
                        <i class="fas {{ $editMode ? 'fa-save' : 'fa-plus-circle' }}"></i>
                        {{ $editMode ? 'Simpan Perubahan' : 'Buat Catatan' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endteleport
    @endif
</div>
