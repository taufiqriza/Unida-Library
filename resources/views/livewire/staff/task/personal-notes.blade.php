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
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-gradient-to-br from-amber-400 via-orange-500 to-rose-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-orange-500/30">
                <i class="fas fa-sticky-note text-2xl"></i>
            </div>
            <div>
                <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Tugas & Jadwal</h1>
                <p class="text-sm text-gray-500 flex items-center gap-2">
                    <span class="w-2 h-2 bg-amber-500 rounded-full animate-pulse"></span>
                    Catatan untuk tim
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            {{-- Unified 4-Tab Navigation --}}
            <div class="bg-white rounded-xl p-1 border border-gray-200 flex shadow-sm">
                <a href="{{ route('staff.task.index') }}" 
                   class="px-3 py-2 text-sm font-medium rounded-lg transition flex items-center gap-2 text-gray-600 hover:bg-gray-100">
                    <i class="fas fa-columns"></i>
                    <span class="hidden sm:inline">Kanban</span>
                </a>
                <a href="{{ route('staff.task.timeline') }}" 
                   class="px-3 py-2 text-sm font-medium rounded-lg transition flex items-center gap-2 text-gray-600 hover:bg-gray-100">
                    <i class="fas fa-chart-gantt"></i>
                    <span class="hidden sm:inline">Timeline</span>
                </a>
                <a href="{{ route('staff.task.schedule') }}" 
                   class="px-3 py-2 text-sm font-medium rounded-lg transition flex items-center gap-2 text-gray-600 hover:bg-gray-100">
                    <i class="fas fa-calendar-alt"></i>
                    <span class="hidden sm:inline">Jadwal</span>
                </a>
                <span class="px-3 py-2 text-sm font-medium rounded-lg bg-gradient-to-r from-amber-500 to-orange-500 text-white shadow-sm flex items-center gap-2">
                    <i class="fas fa-sticky-note"></i>
                    <span class="hidden sm:inline">Notes</span>
                </span>
            </div>
            
            {{-- Personal/Public Toggle --}}
            <div class="bg-white rounded-xl p-1 border border-gray-200 flex shadow-sm">
                <button wire:click="$set('viewMode', 'personal')" 
                        class="px-3 py-1.5 text-sm font-medium rounded-lg transition flex items-center gap-1.5 {{ $viewMode === 'personal' ? 'bg-amber-500 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="fas fa-lock text-xs"></i>Catatan Saya
                </button>
                <button wire:click="$set('viewMode', 'public')" 
                        class="px-3 py-1.5 text-sm font-medium rounded-lg transition flex items-center gap-1.5 {{ $viewMode === 'public' ? 'bg-amber-500 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="fas fa-globe text-xs"></i>Publik Tim
                </button>
            </div>
            
            {{-- Search --}}
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="searchQuery" 
                       placeholder="Cari catatan..." 
                       class="pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm w-48 focus:w-64 transition-all focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 shadow-sm">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            </div>
            
            <button wire:click="openCreateModal" 
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-medium rounded-xl shadow-lg shadow-orange-500/25 transition text-sm">
                <i class="fas fa-plus"></i>
                <span class="hidden sm:inline">Catatan Baru</span>
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="stat-card bg-gradient-to-r from-amber-400 to-orange-500 rounded-xl px-4 py-3 text-white flex items-center justify-between">
            <div class="w-9 h-9 bg-white/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-sticky-note text-sm"></i>
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold">{{ $notes->count() }}</p>
                <p class="text-[10px] text-amber-100 uppercase tracking-wide">{{ $viewMode === 'personal' ? 'Catatan Saya' : 'Catatan Publik' }}</p>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl px-4 py-3 border border-gray-100 shadow-sm flex items-center justify-between">
            <div class="w-9 h-9 bg-violet-100 rounded-lg flex items-center justify-center text-violet-500">
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
    <div class="flex items-center gap-2">
        <button wire:click="$set('filterCategory', '')" 
                class="px-3 py-1.5 text-sm font-medium rounded-lg transition {{ !$filterCategory ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            Semua
        </button>
        @foreach($categories as $key => $cat)
        <button wire:click="$set('filterCategory', '{{ $key }}')" 
                class="px-3 py-1.5 text-sm font-medium rounded-lg transition flex items-center gap-1.5 {{ $filterCategory === $key ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
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
        <div class="w-24 h-24 bg-gradient-to-br from-amber-100 to-orange-100 rounded-3xl flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-sticky-note text-amber-300 text-4xl"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Catatan</h3>
        <p class="text-gray-500 mb-6">{{ $viewMode === 'personal' ? 'Mulai tulis catatan pribadi Anda' : 'Belum ada catatan publik dari tim' }}</p>
        <button wire:click="openCreateModal" 
                class="px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-xl font-semibold hover:from-amber-600 hover:to-orange-600 shadow-lg shadow-orange-500/25 transition-all">
            <i class="fas fa-plus mr-2"></i>Buat Catatan Pertama
        </button>
    </div>
    @endif
</div>

{{-- Modal (Teleport to body) --}}
@if($showModal)
@teleport('body')
<div class="fixed inset-0 z-[99999] flex items-center justify-center p-4" x-data x-init="document.body.style.overflow = 'hidden'" x-on:remove="document.body.style.overflow = ''">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="closeModal"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
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
            
            {{-- Public Toggle --}}
            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                <button type="button" wire:click="$toggle('is_public')" 
                        class="relative w-12 h-6 rounded-full transition-colors {{ $is_public ? 'bg-blue-500' : 'bg-gray-300' }}">
                    <span class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow-sm transition-transform {{ $is_public ? 'translate-x-6' : '' }}"></span>
                </button>
                <div>
                    <p class="text-sm font-medium text-gray-700">Catatan Publik</p>
                    <p class="text-xs text-gray-500">{{ $is_public ? 'Catatan dapat dilihat semua anggota tim' : 'Hanya Anda yang dapat melihat catatan ini' }}</p>
                </div>
            </div>
        </div>
        
        {{-- Modal Footer --}}
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3">
            <button wire:click="closeModal" class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium transition">
                Batal
            </button>
            <button wire:click="save" 
                    class="px-6 py-2 bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-xl font-semibold hover:from-amber-600 hover:to-orange-600 shadow-lg shadow-orange-500/25 transition-all">
                {{ $editMode ? 'Simpan Perubahan' : 'Buat Catatan' }}
            </button>
        </div>
    </div>
</div>
@endteleport
@endif
