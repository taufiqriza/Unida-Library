@section('title', 'Tugas Tim')

@push('styles')
<style>
    .kanban-column { min-height: 450px; }
    .task-card { transition: all 0.2s ease; cursor: pointer; user-select: none; }
    .task-card:hover { transform: translateY(-2px); box-shadow: 0 8px 20px -4px rgba(0,0,0,0.12); }
    .task-card.sortable-ghost { opacity: 0.4; transform: rotate(2deg); }
    .task-card.sortable-drag { transform: rotate(2deg) scale(1.02); box-shadow: 0 15px 35px -5px rgba(0,0,0,0.2); cursor: grabbing; }
    .task-card.sortable-chosen { box-shadow: 0 10px 25px -5px rgba(139, 92, 246, 0.3); }
    .stat-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .stat-card:hover { transform: translateY(-1px); }
    .category-badge { font-size: 10px; padding: 3px 8px; border-radius: 6px; font-weight: 600; }
    .editable-field { cursor: pointer; transition: all 0.2s; border-radius: 6px; padding: 2px 4px; margin: -2px -4px; }
    .editable-field:hover { background: #f3f4f6; }
    .activity-dot::before { content: ''; position: absolute; left: 11px; top: 24px; bottom: -8px; width: 2px; background: #e5e7eb; }
    .activity-item:last-child .activity-dot::before { display: none; }
    .status-pill { transition: all 0.2s; }
    .status-pill:hover { transform: scale(1.05); }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    document.addEventListener('livewire:navigated', initSortable);
    document.addEventListener('DOMContentLoaded', initSortable);
    document.addEventListener('livewire:init', () => {
        Livewire.hook('morph.updated', () => {
            setTimeout(initSortable, 100);
        });
    });
    
    function initSortable() {
        document.querySelectorAll('.kanban-column').forEach(column => {
            if (column.sortableInstance) {
                column.sortableInstance.destroy();
            }
            column.sortableInstance = new Sortable(column, {
                group: 'tasks',
                animation: 200,
                ghostClass: 'sortable-ghost',
                dragClass: 'sortable-drag',
                chosenClass: 'sortable-chosen',
                draggable: '.task-card',
                filter: '.quick-add-form, input, button, a',
                preventOnFilter: false,
                delay: 200,
                delayOnTouchOnly: false,
                onEnd: function(evt) {
                    const taskId = evt.item.dataset.taskId;
                    const newStatusId = evt.to.dataset.statusId;
                    if (taskId && newStatusId && evt.from !== evt.to) {
                        Livewire.find(evt.item.closest('[wire\\:id]').getAttribute('wire:id')).moveTask(parseInt(taskId), parseInt(newStatusId));
                    }
                }
            });
        });
    }
</script>
@endpush

<div class="space-y-5" x-data="{ showQuickAdd: null }">
    {{-- Header --}}
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

        <div class="flex items-center gap-3">
            {{-- Unified 4-Tab Navigation --}}
            <div class="bg-white rounded-xl p-1 border border-gray-200 flex shadow-sm">
                <a href="{{ route('staff.task.index') }}" wire:navigate
                   class="px-3 py-2 text-sm font-medium rounded-lg transition flex items-center gap-2 {{ $viewMode === 'kanban' ? 'bg-gradient-to-r from-violet-600 to-purple-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
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
                <a href="{{ route('staff.task.notes') }}" wire:navigate
                   class="px-3 py-2 text-sm font-medium rounded-lg transition flex items-center gap-2 text-gray-600 hover:bg-gray-100">
                    <i class="fas fa-sticky-note"></i>
                    <span class="hidden sm:inline">Notes</span>
                </a>
            </div>
            
            {{-- Search --}}
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="searchQuery" 
                       placeholder="Cari tugas..." 
                       class="pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm w-48 focus:w-64 transition-all focus:ring-2 focus:ring-violet-500/20 focus:border-violet-400 shadow-sm">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            </div>
            
            <a href="{{ route('staff.task.create') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 text-white font-medium rounded-xl shadow-lg shadow-purple-500/25 transition text-sm">
                <i class="fas fa-plus"></i>
                <span class="hidden sm:inline">Buat Tugas</span>
            </a>
        </div>
    </div>

    {{-- Compact Stats Cards - Icon Left, Value Right --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
        <div class="stat-card bg-gradient-to-r from-violet-500 to-purple-600 rounded-xl px-4 py-3 text-white flex items-center justify-between">
            <div class="w-9 h-9 bg-white/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-clipboard-list text-sm"></i>
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold">{{ $stats['total'] }}</p>
                <p class="text-[10px] text-violet-100 uppercase tracking-wide">Total</p>
            </div>
        </div>
        <div class="stat-card bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl px-4 py-3 text-white flex items-center justify-between">
            <div class="w-9 h-9 bg-white/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-user text-sm"></i>
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold">{{ $stats['my_tasks'] }}</p>
                <p class="text-[10px] text-blue-100 uppercase tracking-wide">Tugas Saya</p>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl px-4 py-3 border border-gray-100 shadow-sm flex items-center justify-between">
            <div class="w-9 h-9 bg-red-100 rounded-lg flex items-center justify-center text-red-500">
                <i class="fas fa-exclamation-triangle text-sm"></i>
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold text-red-600">{{ $stats['overdue'] }}</p>
                <p class="text-[10px] text-gray-500 uppercase tracking-wide">Terlambat</p>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl px-4 py-3 border border-gray-100 shadow-sm flex items-center justify-between">
            <div class="w-9 h-9 bg-amber-100 rounded-lg flex items-center justify-center text-amber-500">
                <i class="fas fa-clock text-sm"></i>
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold text-gray-900">{{ $stats['due_soon'] }}</p>
                <p class="text-[10px] text-gray-500 uppercase tracking-wide">Segera</p>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl px-4 py-3 border border-gray-100 shadow-sm flex items-center justify-between">
            <div class="w-9 h-9 bg-emerald-100 rounded-lg flex items-center justify-center text-emerald-500">
                <i class="fas fa-check-circle text-sm"></i>
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold text-emerald-600">{{ $stats['completed_today'] ?? 0 }}</p>
                <p class="text-[10px] text-gray-500 uppercase tracking-wide">Selesai</p>
            </div>
        </div>
    </div>

    {{-- Today's Schedule Widget (Integration) --}}
    @if($todaySchedules->count() > 0)
    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-xl border border-emerald-200 p-4">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center text-white">
                    <i class="fas fa-calendar-day text-sm"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 text-sm">Jadwal Hari Ini</h3>
                    <p class="text-[10px] text-gray-500">{{ now()->locale('id')->isoFormat('dddd, D MMMM') }}</p>
                </div>
            </div>
            <a href="{{ route('staff.task.schedule') }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium flex items-center gap-1">
                Lihat Semua <i class="fas fa-arrow-right text-[10px]"></i>
            </a>
        </div>
        <div class="flex gap-2 overflow-x-auto pb-1">
            @foreach($todaySchedules as $schedule)
                @php 
                    $typeInfo = $schedule->getTypeInfo();
                    $colorMap = [
                        'blue' => 'bg-blue-100 text-blue-700 border-blue-200',
                        'indigo' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
                        'emerald' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                        'violet' => 'bg-violet-100 text-violet-700 border-violet-200',
                        'amber' => 'bg-amber-100 text-amber-700 border-amber-200',
                        'cyan' => 'bg-cyan-100 text-cyan-700 border-cyan-200',
                        'rose' => 'bg-rose-100 text-rose-700 border-rose-200',
                        'gray' => 'bg-gray-100 text-gray-700 border-gray-200',
                    ];
                    $colorClass = $colorMap[$typeInfo['color']] ?? $colorMap['gray'];
                @endphp
                <div class="flex-shrink-0 px-3 py-2 rounded-lg border {{ $colorClass }} min-w-[140px]">
                    <div class="flex items-center gap-1.5 mb-1">
                        <i class="fas {{ $typeInfo['icon'] }} text-xs"></i>
                        <span class="font-semibold text-xs truncate">{{ Str::limit($schedule->title, 15) }}</span>
                    </div>
                    <p class="text-[10px] opacity-75">
                        @if($schedule->getTimeRange())
                            <i class="fas fa-clock mr-0.5"></i>{{ $schedule->getTimeRange() }}
                        @else
                            <i class="fas fa-tag mr-0.5"></i>{{ $typeInfo['label'] }}
                        @endif
                    </p>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3">
        <div class="flex flex-wrap items-center gap-3">
            {{-- Branch Filter (Super Admin Only) --}}
            @if($isSuperAdmin)
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 bg-violet-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-building text-violet-600 text-xs"></i>
                </div>
                <select wire:model.live="filterBranch" class="px-3 py-2 bg-violet-50 border border-violet-200 rounded-lg text-sm focus:ring-2 focus:ring-violet-500/20 font-medium text-violet-700">
                    <option value="">Semua Cabang</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="h-6 w-px bg-gray-200"></div>
            @endif
            
            <select wire:model.live="filterAssignee" class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-violet-500/20">
                <option value="">Semua PIC</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="filterPriority" class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-violet-500/20">
                <option value="">Semua Prioritas</option>
                <option value="urgent">Mendesak</option>
                <option value="high">Tinggi</option>
                <option value="medium">Sedang</option>
                <option value="low">Rendah</option>
            </select>
            <select wire:model.live="filterType" class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-violet-500/20">
                <option value="">Semua Kategori</option>
                <option value="general">Tugas Umum</option>
                <option value="collection">Pengembangan Koleksi</option>
                <option value="service">Pelayanan</option>
                <option value="admin">Administrasi</option>
                <option value="event">Kegiatan</option>
            </select>
            @if($filterAssignee || $filterPriority || $filterType || $searchQuery || ($isSuperAdmin && $filterBranch))
                <button wire:click="$set('filterAssignee', ''); $set('filterPriority', ''); $set('filterType', ''); $set('searchQuery', ''); $set('filterBranch', '')" 
                        class="px-3 py-2 text-gray-500 hover:text-red-600 hover:bg-red-50 text-sm flex items-center gap-1 rounded-lg transition">
                    <i class="fas fa-times"></i>Reset
                </button>
            @endif
        </div>
    </div>

    {{-- Kanban Board with Scroll Navigation --}}
    @if($viewMode === 'kanban')
    <div class="relative" 
         x-data="{ 
             canScrollLeft: false, 
             canScrollRight: true,
             checkScroll() {
                 const el = this.$refs.kanbanScroll;
                 this.canScrollLeft = el.scrollLeft > 10;
                 this.canScrollRight = el.scrollLeft < (el.scrollWidth - el.clientWidth - 10);
             },
             scrollLeft() {
                 this.$refs.kanbanScroll.scrollBy({ left: -320, behavior: 'smooth' });
             },
             scrollRight() {
                 this.$refs.kanbanScroll.scrollBy({ left: 320, behavior: 'smooth' });
             }
         }"
         x-init="$nextTick(() => checkScroll())">
        
        {{-- Scroll Left Button --}}
        <button x-show="canScrollLeft" 
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-x-2"
                x-transition:enter-end="opacity-100 translate-x-0"
                @click="scrollLeft()"
                class="absolute left-0 top-1/2 -translate-y-1/2 z-10 w-10 h-10 bg-white/90 hover:bg-white shadow-lg rounded-full flex items-center justify-center text-gray-600 hover:text-violet-600 transition border border-gray-100 backdrop-blur-sm">
            <i class="fas fa-chevron-left"></i>
        </button>

        {{-- Scroll Right Button --}}
        <button x-show="canScrollRight" 
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-x-2"
                x-transition:enter-end="opacity-100 translate-x-0"
                @click="scrollRight()"
                class="absolute right-0 top-1/2 -translate-y-1/2 z-10 w-10 h-10 bg-white/90 hover:bg-white shadow-lg rounded-full flex items-center justify-center text-gray-600 hover:text-violet-600 transition border border-gray-100 backdrop-blur-sm">
            <i class="fas fa-chevron-right"></i>
        </button>

        {{-- Scroll Indicator --}}
        <div x-show="canScrollRight" class="absolute right-0 top-0 bottom-4 w-16 bg-gradient-to-l from-slate-50 to-transparent pointer-events-none z-[5] hidden lg:block"></div>
        <div x-show="canScrollLeft" class="absolute left-0 top-0 bottom-4 w-16 bg-gradient-to-r from-slate-50 to-transparent pointer-events-none z-[5] hidden lg:block"></div>

        {{-- Kanban Scrollable Container --}}
        <div class="flex gap-4 overflow-x-auto pb-4 -mx-4 px-4 lg:mx-0 lg:px-0 scroll-smooth scrollbar-hide"
             x-ref="kanbanScroll"
             @scroll="checkScroll()">
        @foreach($statuses as $status)
            <div class="flex-shrink-0 w-72 lg:w-80">
                {{-- Column Header --}}
                <div class="flex items-center justify-between mb-3 px-1">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full shadow-sm" style="background-color: {{ $status->color }}"></div>
                        <h3 class="font-bold text-gray-700 text-sm">{{ $status->name }}</h3>
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs font-bold rounded-full">
                            {{ $tasksByStatus[$status->id]->count() }}
                        </span>
                    </div>
                    <button @click="showQuickAdd = {{ $status->id }}" 
                            class="w-7 h-7 text-gray-400 hover:text-violet-600 hover:bg-violet-50 rounded-lg transition flex items-center justify-center">
                        <i class="fas fa-plus text-xs"></i>
                    </button>
                </div>

                {{-- Column Body --}}
                <div class="kanban-column bg-gray-50/80 rounded-xl p-2 space-y-2" data-status-id="{{ $status->id }}">
                    {{-- Quick Add Card --}}
                    <div x-show="showQuickAdd === {{ $status->id }}" x-cloak 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="quick-add-form bg-white rounded-xl p-3 shadow-lg border-2 border-violet-300">
                        <input wire:model="newTaskTitle" 
                               type="text" 
                               placeholder="Judul tugas..."
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm mb-2 focus:ring-2 focus:ring-violet-500/20 focus:border-violet-400"
                               @keydown.enter="$wire.createQuickTask({{ $status->id }}); showQuickAdd = null"
                               @keydown.escape="showQuickAdd = null"
                               x-ref="quickAddInput{{ $status->id }}"
                               x-init="$watch('showQuickAdd', value => { if(value === {{ $status->id }}) $nextTick(() => $refs.quickAddInput{{ $status->id }}.focus()) })">
                        <div class="flex gap-2">
                            <button wire:click="createQuickTask({{ $status->id }})" 
                                    @click="showQuickAdd = null"
                                    class="flex-1 px-3 py-2 bg-violet-600 hover:bg-violet-700 text-white text-xs font-medium rounded-lg transition">
                                <i class="fas fa-check mr-1"></i>Simpan
                            </button>
                            <button @click="showQuickAdd = null" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-medium rounded-lg transition">
                                Batal
                            </button>
                        </div>
                    </div>

                    {{-- Task Cards --}}
                    @forelse($tasksByStatus[$status->id] as $task)
                        <div wire:click="openTaskModal({{ $task->id }})" 
                             wire:key="task-{{ $task->id }}"
                             data-task-id="{{ $task->id }}"
                             class="task-card bg-white rounded-xl p-3 shadow-sm border border-gray-100 {{ $task->isOverdue() ? 'border-l-4 border-l-red-500' : '' }}">
                            
                            {{-- Header: Category & Priority --}}
                            <div class="flex items-center justify-between gap-2 mb-2">
                                @php
                                    $categoryStyles = [
                                        'collection' => ['bg-emerald-100 text-emerald-700', 'fa-books', 'Koleksi'],
                                        'service' => ['bg-blue-100 text-blue-700', 'fa-hands-helping', 'Pelayanan'],
                                        'admin' => ['bg-amber-100 text-amber-700', 'fa-file-alt', 'Administrasi'],
                                        'event' => ['bg-pink-100 text-pink-700', 'fa-calendar-star', 'Kegiatan'],
                                        'general' => ['bg-gray-100 text-gray-600', 'fa-clipboard-check', 'Umum'],
                                        'task' => ['bg-gray-100 text-gray-600', 'fa-clipboard-check', 'Umum'],
                                    ];
                                    $cat = $categoryStyles[$task->type] ?? $categoryStyles['general'];
                                @endphp
                                <span class="category-badge {{ $cat[0] }} flex items-center gap-1">
                                    <i class="fas {{ $cat[1] }} text-[8px]"></i>{{ $cat[2] }}
                                </span>
                                @php
                                    $priorityStyles = [
                                        'urgent' => ['bg-red-100 text-red-700', 'Mendesak'],
                                        'high' => ['bg-orange-100 text-orange-700', 'Tinggi'],
                                        'medium' => ['bg-yellow-100 text-yellow-700', 'Sedang'],
                                        'low' => ['bg-green-100 text-green-700', 'Rendah'],
                                    ];
                                    $pri = $priorityStyles[$task->priority] ?? $priorityStyles['medium'];
                                @endphp
                                <span class="category-badge {{ $pri[0] }}">
                                    {{ $pri[1] }}
                                </span>
                            </div>

                            {{-- Title --}}
                            <p class="font-semibold text-gray-900 text-sm line-clamp-2 mb-3">{{ $task->title }}</p>

                            {{-- Footer --}}
                            <div class="flex items-center justify-between text-xs text-gray-400 pt-2 border-t border-gray-50">
                                <div class="flex items-center gap-2">
                                    @if($task->due_date)
                                        <span class="flex items-center gap-1 {{ $task->isOverdue() ? 'text-red-500 font-medium' : '' }}">
                                            <i class="fas fa-calendar-alt text-[10px]"></i>{{ $task->due_date->format('d M') }}
                                        </span>
                                    @endif
                                    @if($task->comments_count ?? $task->comments->count() > 0)
                                        <span class="flex items-center gap-1 text-gray-400">
                                            <i class="fas fa-comment text-[10px]"></i>{{ $task->comments_count ?? $task->comments->count() }}
                                        </span>
                                    @endif
                                    @if($isSuperAdmin && $task->branch)
                                        <span class="text-[10px] text-indigo-500">
                                            <i class="fas fa-building text-[8px]"></i> {{ $task->branch->code ?? Str::limit($task->branch->name, 8) }}
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-1">
                                    @if($task->assignee)
                                        @if($task->assignee->photo)
                                            <img src="{{ asset('storage/' . $task->assignee->photo) }}" 
                                                 class="w-6 h-6 rounded-full object-cover border-2 border-white shadow-sm" 
                                                 title="PIC: {{ $task->assignee->name }}">
                                        @else
                                            <div class="w-6 h-6 bg-gradient-to-br from-violet-400 to-purple-500 rounded-full flex items-center justify-center text-white text-[10px] font-bold shadow-sm border-2 border-white" title="PIC: {{ $task->assignee->name }}">
                                                {{ strtoupper(substr($task->assignee->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    @else
                                        <div class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center text-gray-400 text-[10px]" title="Belum ada PIC">
                                            <i class="fas fa-user-plus"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                                <i class="fas fa-inbox text-gray-300"></i>
                            </div>
                            <p class="text-gray-400 text-xs">Tidak ada tugas</p>
                            <button @click="showQuickAdd = {{ $status->id }}" class="text-violet-600 text-xs mt-1 hover:underline">
                                <i class="fas fa-plus mr-1"></i>Tambah
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
        </div>
    </div>
    @endif

    {{-- Timeline View --}}
    @if($viewMode === 'timeline')
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        {{-- Timeline Header --}}
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-violet-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-stream text-violet-600 text-sm"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 text-sm">Timeline Tugas</h3>
                    <p class="text-xs text-gray-500">{{ $timelineStart->format('d M') }} - {{ $timelineEnd->format('d M Y') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                {{-- Zoom Controls --}}
                <div class="flex bg-gray-100 rounded-lg p-0.5">
                    <button wire:click="$set('timelineZoom', 'day')" 
                            class="px-2 py-1 text-xs font-medium rounded-md transition {{ $timelineZoom === 'day' ? 'bg-white text-violet-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        7 Hari
                    </button>
                    <button wire:click="$set('timelineZoom', 'week')" 
                            class="px-2 py-1 text-xs font-medium rounded-md transition {{ $timelineZoom === 'week' ? 'bg-white text-violet-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        14 Hari
                    </button>
                    <button wire:click="$set('timelineZoom', 'month')" 
                            class="px-2 py-1 text-xs font-medium rounded-md transition {{ $timelineZoom === 'month' ? 'bg-white text-violet-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        30 Hari
                    </button>
                </div>
            </div>
        </div>

        {{-- Timeline Body --}}
        <div class="overflow-x-auto">
            <div class="min-w-[800px]">
                {{-- Date Headers --}}
                <div class="flex border-b border-gray-100">
                    <div class="w-48 flex-shrink-0 px-3 py-2 bg-gray-50 border-r border-gray-100">
                        <span class="text-xs font-semibold text-gray-500">TUGAS</span>
                    </div>
                    <div class="flex-1 flex">
                        @for($i = 0; $i < $timelineDays; $i++)
                            @php
                                $date = $timelineStart->copy()->addDays($i);
                                $isToday = $date->isToday();
                                $isWeekend = $date->isWeekend();
                            @endphp
                            <div class="flex-1 min-w-[40px] text-center py-2 border-r border-gray-50 {{ $isToday ? 'bg-violet-50' : ($isWeekend ? 'bg-gray-50/50' : '') }}">
                                <p class="text-[10px] font-semibold {{ $isToday ? 'text-violet-600' : 'text-gray-400' }}">
                                    {{ $date->locale('id')->isoFormat('ddd') }}
                                </p>
                                <p class="text-xs font-bold {{ $isToday ? 'text-violet-600' : 'text-gray-700' }}">
                                    {{ $date->format('d') }}
                                </p>
                            </div>
                        @endfor
                    </div>
                </div>

                {{-- Task Rows --}}
                <div class="divide-y divide-gray-50">
                    @php
                        $tasksWithDates = $tasks->filter(fn($t) => $t->due_date || $t->start_date)->sortBy('due_date');
                    @endphp
                    
                    @forelse($tasksWithDates as $task)
                        @php
                            $taskStart = $task->start_date ?? $task->created_at;
                            $taskEnd = $task->due_date ?? $taskStart->copy()->addDays(1);
                            
                            // Calculate position
                            $startOffset = max(0, $taskStart->diffInDays($timelineStart, false));
                            $endOffset = min($timelineDays, $taskEnd->diffInDays($timelineStart, false) + 1);
                            $duration = max(1, $endOffset - $startOffset);
                            
                            $leftPercent = ($startOffset / $timelineDays) * 100;
                            $widthPercent = ($duration / $timelineDays) * 100;
                            
                            // Priority colors
                            $barColors = [
                                'urgent' => 'bg-gradient-to-r from-red-500 to-red-600',
                                'high' => 'bg-gradient-to-r from-orange-500 to-orange-600',
                                'medium' => 'bg-gradient-to-r from-blue-500 to-indigo-600',
                                'low' => 'bg-gradient-to-r from-emerald-500 to-teal-600',
                            ];
                            $barColor = $barColors[$task->priority] ?? $barColors['medium'];
                            
                            // Check if task is visible in timeline
                            $isVisible = $startOffset < $timelineDays && $endOffset > 0;
                        @endphp
                        
                        @if($isVisible)
                        <div class="flex hover:bg-gray-50/50 transition-colors">
                            {{-- Task Name --}}
                            <div class="w-48 flex-shrink-0 px-3 py-3 border-r border-gray-100 flex items-center gap-2">
                                @php
                                    $priorityStyles = [
                                        'urgent' => 'bg-red-100 text-red-600',
                                        'high' => 'bg-orange-100 text-orange-600',
                                        'medium' => 'bg-blue-100 text-blue-600',
                                        'low' => 'bg-emerald-100 text-emerald-600',
                                    ];
                                @endphp
                                <div class="w-2 h-2 rounded-full flex-shrink-0 {{ $priorityStyles[$task->priority] ?? 'bg-gray-100' }}"></div>
                                <div class="min-w-0 flex-1">
                                    <p wire:click="openTaskModal({{ $task->id }})" 
                                       class="text-xs font-medium text-gray-900 truncate cursor-pointer hover:text-violet-600 transition">
                                        {{ Str::limit($task->title, 22) }}
                                    </p>
                                    <p class="text-[10px] text-gray-400 truncate">
                                        {{ $task->assignee?->name ?? 'Belum ditugaskan' }}
                                    </p>
                                </div>
                            </div>
                            
                            {{-- Timeline Bar Area --}}
                            <div class="flex-1 relative py-2">
                                {{-- Grid Lines --}}
                                <div class="absolute inset-0 flex">
                                    @for($i = 0; $i < $timelineDays; $i++)
                                        @php $date = $timelineStart->copy()->addDays($i); @endphp
                                        <div class="flex-1 border-r border-gray-50 {{ $date->isToday() ? 'bg-violet-50/30' : '' }}"></div>
                                    @endfor
                                </div>
                                
                                {{-- Task Bar --}}
                                <div class="absolute top-1/2 -translate-y-1/2 h-6 rounded-full shadow-sm flex items-center px-2 text-white text-[10px] font-semibold {{ $barColor }} {{ $task->isOverdue() ? 'ring-2 ring-red-300 ring-offset-1' : '' }}"
                                     style="left: {{ $leftPercent }}%; width: {{ max(3, $widthPercent) }}%;"
                                     title="{{ $task->title }} ({{ $taskStart->format('d M') }} - {{ $taskEnd->format('d M') }})">
                                    @if($widthPercent > 10)
                                        <span class="truncate">{{ Str::limit($task->title, 15) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                    @empty
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-calendar-times text-gray-300 text-2xl"></i>
                            </div>
                            <p class="text-gray-500 text-sm font-medium">Tidak ada tugas dengan tanggal</p>
                            <p class="text-gray-400 text-xs mt-1">Tambahkan due date pada tugas untuk melihatnya di timeline</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Legend --}}
        <div class="px-4 py-3 border-t border-gray-100 bg-gray-50/50 flex items-center gap-4 flex-wrap">
            <span class="text-xs text-gray-500 font-medium">Prioritas:</span>
            <div class="flex items-center gap-1">
                <div class="w-3 h-3 bg-gradient-to-r from-red-500 to-red-600 rounded"></div>
                <span class="text-xs text-gray-600">Mendesak</span>
            </div>
            <div class="flex items-center gap-1">
                <div class="w-3 h-3 bg-gradient-to-r from-orange-500 to-orange-600 rounded"></div>
                <span class="text-xs text-gray-600">Tinggi</span>
            </div>
            <div class="flex items-center gap-1">
                <div class="w-3 h-3 bg-gradient-to-r from-blue-500 to-indigo-600 rounded"></div>
                <span class="text-xs text-gray-600">Sedang</span>
            </div>
            <div class="flex items-center gap-1">
                <div class="w-3 h-3 bg-gradient-to-r from-emerald-500 to-teal-600 rounded"></div>
                <span class="text-xs text-gray-600">Rendah</span>
            </div>
        </div>
    </div>
    @endif

    {{-- Modals teleported to body for proper z-index --}}
    <template x-teleport="body">
        <div style="position: relative; z-index: 99999;">
            {{-- Task Detail Modal --}}
            @if($showTaskModal && $selectedTask)
            <div class="fixed inset-0 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
                 x-data="{ activeTab: 'details' }"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100">
                <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col" 
                     @click.away="$wire.closeTaskModal()">
            
            {{-- Modal Header --}}
            <div class="p-4 border-b border-gray-100 flex items-center justify-between flex-shrink-0" 
                 style="background: linear-gradient(135deg, {{ $selectedTask->status->color }}15, {{ $selectedTask->status->color }}05);">
                <div class="flex items-center gap-3">
                    @php
                        $cat = $categoryStyles[$selectedTask->type] ?? $categoryStyles['general'];
                    @endphp
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $cat[0] }}">
                        <i class="fas {{ $cat[1] }}"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-gray-400 text-sm font-mono">#{{ $selectedTask->id }}</span>
                            <span class="px-2 py-0.5 rounded-lg text-xs font-semibold text-white" style="background-color: {{ $selectedTask->status->color }}">
                                {{ $selectedTask->status->name }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2 mt-1 text-xs text-gray-500">
                            <span class="flex items-center gap-1">
                                <i class="fas fa-user-edit text-[10px]"></i>{{ $selectedTask->reporter?->name ?? 'Unknown' }}
                            </span>
                            @if($isSuperAdmin && $selectedTask->branch)
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-building text-[10px]"></i>{{ $selectedTask->branch->name }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-1">
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="w-8 h-8 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition flex items-center justify-center">
                            <i class="fas fa-ellipsis-v text-sm"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 top-full mt-1 w-44 bg-white rounded-xl shadow-xl border border-gray-100 py-1 z-10">
                            <a href="{{ route('staff.task.edit', $selectedTask->id) }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-edit text-gray-400 w-4"></i> Edit
                            </a>
                            <button wire:click="duplicateTask({{ $selectedTask->id }})" class="w-full flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-copy text-gray-400 w-4"></i> Duplikat
                            </button>
                            <hr class="my-1">
                            <button wire:click="confirmDelete({{ $selectedTask->id }})" class="w-full flex items-center gap-2 px-3 py-2 text-sm text-red-600 hover:bg-red-50">
                                <i class="fas fa-trash text-red-400 w-4"></i> Hapus
                            </button>
                        </div>
                    </div>
                    <button wire:click="closeTaskModal" class="w-8 h-8 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition flex items-center justify-center">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            {{-- Tabs --}}
            <div class="flex border-b border-gray-100 px-4 flex-shrink-0 bg-gray-50/50">
                <button @click="activeTab = 'details'" 
                        class="px-3 py-2.5 text-sm font-medium border-b-2 transition flex items-center gap-1.5"
                        :class="activeTab === 'details' ? 'text-violet-600 border-violet-600' : 'text-gray-500 border-transparent hover:text-gray-700'">
                    <i class="fas fa-info-circle text-xs"></i> Detail
                </button>
                <button @click="activeTab = 'comments'" 
                        class="px-3 py-2.5 text-sm font-medium border-b-2 transition flex items-center gap-1.5"
                        :class="activeTab === 'comments' ? 'text-violet-600 border-violet-600' : 'text-gray-500 border-transparent hover:text-gray-700'">
                    <i class="fas fa-comments text-xs"></i> Diskusi
                    @if($selectedTask->comments->count() > 0)
                        <span class="px-1.5 bg-violet-100 text-violet-700 text-[10px] font-bold rounded-full">{{ $selectedTask->comments->count() }}</span>
                    @endif
                </button>
                <button @click="activeTab = 'activity'" 
                        class="px-3 py-2.5 text-sm font-medium border-b-2 transition flex items-center gap-1.5"
                        :class="activeTab === 'activity' ? 'text-violet-600 border-violet-600' : 'text-gray-500 border-transparent hover:text-gray-700'">
                    <i class="fas fa-history text-xs"></i> Riwayat
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="flex-1 overflow-y-auto">
                {{-- Details Tab --}}
                <div x-show="activeTab === 'details'" class="p-5 space-y-4">
                    {{-- Title --}}
                    <div>
                        @if($editingField === 'title')
                            <div class="flex gap-2">
                                <input type="text" wire:model="editTitle" 
                                       class="flex-1 px-3 py-2 border-2 border-violet-300 rounded-lg text-lg font-bold focus:ring-2 focus:ring-violet-500/20"
                                       @keydown.enter="$wire.saveField('title')"
                                       @keydown.escape="$wire.cancelEdit()">
                                <button wire:click="saveField('title')" class="px-3 bg-violet-600 text-white rounded-lg"><i class="fas fa-check"></i></button>
                                <button wire:click="cancelEdit" class="px-3 bg-gray-200 text-gray-600 rounded-lg"><i class="fas fa-times"></i></button>
                            </div>
                        @else
                            <h2 wire:click="startEdit('title')" class="text-lg font-bold text-gray-900 editable-field cursor-pointer group">
                                {{ $selectedTask->title }}
                                <i class="fas fa-pencil-alt text-gray-300 text-xs opacity-0 group-hover:opacity-100 transition ml-1"></i>
                            </h2>
                        @endif
                    </div>

                    {{-- Description --}}
                    <div class="bg-gray-50 rounded-xl p-3">
                        <label class="text-xs text-gray-500 font-semibold mb-1 block"><i class="fas fa-align-left mr-1"></i>Deskripsi</label>
                        @if($editingField === 'description')
                            <div class="space-y-2">
                                <textarea wire:model="editDescription" rows="3" class="w-full px-3 py-2 border-2 border-violet-300 rounded-lg focus:ring-2 focus:ring-violet-500/20 bg-white text-sm"></textarea>
                                <div class="flex gap-2">
                                    <button wire:click="saveField('description')" class="px-3 py-1.5 bg-violet-600 text-white text-xs font-medium rounded-lg">Simpan</button>
                                    <button wire:click="cancelEdit" class="px-3 py-1.5 bg-gray-200 text-gray-600 text-xs font-medium rounded-lg">Batal</button>
                                </div>
                            </div>
                        @else
                            <div wire:click="startEdit('description')" class="editable-field group min-h-[40px] cursor-pointer text-sm">
                                @if($selectedTask->description)
                                    <div class="text-gray-600">{!! nl2br(e($selectedTask->description)) !!}</div>
                                @else
                                    <p class="text-gray-400 italic text-xs"><i class="fas fa-plus-circle mr-1"></i>Tambah deskripsi...</p>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Checklist --}}
                    <div class="bg-gray-50 rounded-xl p-3">
                        @php
                            $totalChecklist = $selectedTask->checklists->count();
                            $completedChecklist = $selectedTask->checklists->where('is_completed', true)->count();
                            $progress = $totalChecklist > 0 ? round(($completedChecklist / $totalChecklist) * 100) : 0;
                        @endphp
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs text-gray-500 font-semibold"><i class="fas fa-list-check mr-1"></i>Poin-poin</label>
                            @if($totalChecklist > 0)
                                <span class="text-xs font-bold {{ $progress == 100 ? 'text-emerald-600' : 'text-gray-500' }}">
                                    {{ $completedChecklist }}/{{ $totalChecklist }} ({{ $progress }}%)
                                </span>
                            @endif
                        </div>

                        {{-- Progress Bar --}}
                        @if($totalChecklist > 0)
                            <div class="w-full h-1.5 bg-gray-200 rounded-full mb-3 overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500 {{ $progress == 100 ? 'bg-emerald-500' : 'bg-violet-500' }}" 
                                     style="width: {{ $progress }}%"></div>
                            </div>
                        @endif

                        {{-- Checklist Items --}}
                        <div class="space-y-1.5 mb-3">
                            @forelse($selectedTask->checklists as $item)
                                <div class="flex items-center gap-2 group bg-white rounded-lg p-2 border border-gray-100 hover:border-violet-200 transition">
                                    <button wire:click="toggleChecklistItem({{ $item->id }})" 
                                            class="w-5 h-5 rounded border-2 flex items-center justify-center transition flex-shrink-0 {{ $item->is_completed ? 'bg-emerald-500 border-emerald-500' : 'border-gray-300 hover:border-violet-400' }}">
                                        @if($item->is_completed)
                                            <i class="fas fa-check text-white text-[10px]"></i>
                                        @endif
                                    </button>
                                    <span class="flex-1 text-sm {{ $item->is_completed ? 'text-gray-400 line-through' : 'text-gray-700' }}">
                                        {{ $item->content }}
                                    </span>
                                    <button wire:click="deleteChecklistItem({{ $item->id }})" 
                                            class="w-6 h-6 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded transition opacity-0 group-hover:opacity-100 flex items-center justify-center">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </div>
                            @empty
                                <p class="text-gray-400 text-xs italic py-2">Belum ada poin-poin</p>
                            @endforelse
                        </div>

                        {{-- Add New Item --}}
                        <div class="flex gap-2">
                            <input type="text" wire:model="newChecklistItem" 
                                   wire:keydown.enter="addChecklistItem"
                                   placeholder="Tambah poin baru..." 
                                   class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-violet-500/20 focus:border-violet-400 bg-white">
                            <button wire:click="addChecklistItem" 
                                    class="px-3 py-2 bg-violet-600 hover:bg-violet-700 text-white rounded-lg transition">
                                <i class="fas fa-plus text-sm"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Meta Grid --}}
                    <div class="grid grid-cols-2 gap-3">
                        {{-- Status --}}
                        <div class="p-3 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-500 mb-2 font-semibold"><i class="fas fa-tasks mr-1"></i>Status</p>
                            <div class="flex flex-wrap gap-1">
                                @foreach($statuses as $status)
                                    <button wire:click="moveTask({{ $selectedTask->id }}, {{ $status->id }})" 
                                            class="status-pill px-2 py-1 rounded-lg text-[10px] font-semibold transition {{ $status->id == $selectedTask->status_id ? 'text-white shadow-sm' : '' }}"
                                            style="background-color: {{ $status->id == $selectedTask->status_id ? $status->color : $status->color . '20' }}; color: {{ $status->id == $selectedTask->status_id ? 'white' : $status->color }}">
                                        {{ $status->name }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Priority --}}
                        <div class="p-3 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-500 mb-2 font-semibold"><i class="fas fa-flag mr-1"></i>Prioritas</p>
                            <div class="flex gap-1">
                                @foreach(['low' => ['bg-green-100 text-green-700', 'Rendah'], 'medium' => ['bg-yellow-100 text-yellow-700', 'Sedang'], 'high' => ['bg-orange-100 text-orange-700', 'Tinggi'], 'urgent' => ['bg-red-100 text-red-700', 'Mendesak']] as $p => $styles)
                                    <button wire:click="$set('editPriority', '{{ $p }}'); $wire.saveField('priority')" 
                                            class="px-2 py-1 rounded-lg text-[10px] font-semibold transition {{ $selectedTask->priority === $p ? $styles[0] . ' ring-1 ring-offset-1' : 'bg-gray-100 text-gray-400 hover:bg-gray-200' }}">
                                        {{ $styles[1] }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- PIC --}}
                        <div class="p-3 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-500 mb-2 font-semibold"><i class="fas fa-user-check mr-1"></i>PIC (Penanggung Jawab)</p>
                            @if($editingField === 'assigned_to')
                                <select wire:model="editAssignedTo" wire:change="saveField('assigned_to')" class="w-full px-2 py-1 text-sm border border-gray-200 rounded-lg bg-white">
                                    <option value="">Belum ditugaskan</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            @else
                                <div wire:click="startEdit('assigned_to')" class="flex items-center gap-2 cursor-pointer group">
                                    @if($selectedTask->assignee)
                                        <div class="w-6 h-6 bg-gradient-to-br from-violet-400 to-purple-500 rounded-full flex items-center justify-center text-white text-[10px] font-bold">
                                            {{ strtoupper(substr($selectedTask->assignee->name, 0, 1)) }}
                                        </div>
                                        <span class="font-medium text-gray-900 text-sm group-hover:text-violet-600">{{ $selectedTask->assignee->name }}</span>
                                    @else
                                        <span class="text-gray-400 text-sm group-hover:text-violet-600">Belum ditugaskan</span>
                                    @endif
                                    <i class="fas fa-pencil-alt text-gray-300 text-[10px] opacity-0 group-hover:opacity-100 transition ml-auto"></i>
                                </div>
                            @endif
                        </div>

                        {{-- Deadline --}}
                        <div class="p-3 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-500 mb-2 font-semibold"><i class="fas fa-calendar-alt mr-1"></i>Tenggat Waktu</p>
                            @if($editingField === 'due_date')
                                <input type="date" wire:model="editDueDate" wire:change="saveField('due_date')" class="w-full px-2 py-1 text-sm border border-gray-200 rounded-lg bg-white">
                            @else
                                <p wire:click="startEdit('due_date')" class="font-medium text-sm cursor-pointer group {{ $selectedTask->isOverdue() ? 'text-red-600' : 'text-gray-900' }}">
                                    {{ $selectedTask->due_date?->format('d M Y') ?? 'Belum diatur' }}
                                    @if($selectedTask->isOverdue())
                                        <span class="text-red-500 text-xs">(Terlambat!)</span>
                                    @endif
                                    <i class="fas fa-pencil-alt text-gray-300 text-[10px] opacity-0 group-hover:opacity-100 transition ml-1"></i>
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Quick Assign --}}
                    @if(!$selectedTask->assigned_to || $selectedTask->assigned_to !== auth()->id())
                        <button wire:click="assignTaskToMe({{ $selectedTask->id }})" 
                                class="w-full px-4 py-2.5 bg-violet-50 hover:bg-violet-100 text-violet-700 text-sm font-semibold rounded-xl transition flex items-center justify-center gap-2 border border-violet-200">
                            <i class="fas fa-hand-pointer"></i> Ambil Tugas Ini
                        </button>
                    @endif
                </div>

                {{-- Comments Tab --}}
                <div x-show="activeTab === 'comments'" class="p-5">
                    <div class="mb-4 bg-gray-50 rounded-xl p-3">
                        <div class="flex gap-2">
                            <div class="w-8 h-8 bg-gradient-to-br from-violet-400 to-purple-500 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="flex-1">
                                <textarea wire:model="newComment" rows="2" placeholder="Tulis komentar atau update..." 
                                          class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-violet-500/20 focus:border-violet-400 resize-none bg-white"></textarea>
                                <div class="flex justify-end mt-2">
                                    <button wire:click="addComment" class="px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white text-sm font-medium rounded-lg transition flex items-center gap-1">
                                        <i class="fas fa-paper-plane text-xs"></i> Kirim
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @forelse($selectedTask->comments as $comment)
                            <div class="flex gap-2 group">
                                <div class="w-8 h-8 bg-gradient-to-br from-gray-400 to-gray-500 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                    {{ strtoupper(substr($comment->user->name ?? 'U', 0, 1)) }}
                                </div>
                                <div class="flex-1 bg-white rounded-lg p-3 border border-gray-100 shadow-sm">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="font-semibold text-gray-900 text-sm">{{ $comment->user->name ?? 'Unknown' }}</span>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                            @if($comment->user_id === auth()->id())
                                                <button wire:click="deleteComment({{ $comment->id }})" class="text-gray-300 hover:text-red-500 opacity-0 group-hover:opacity-100 transition">
                                                    <i class="fas fa-trash text-[10px]"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    <p class="text-gray-600 text-sm">{{ $comment->content }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-2">
                                    <i class="fas fa-comments text-gray-300 text-xl"></i>
                                </div>
                                <p class="text-gray-400 text-sm">Belum ada diskusi</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Activity Tab --}}
                <div x-show="activeTab === 'activity'" class="p-5">
                    <div class="space-y-1">
                        @forelse($selectedTask->activities ?? [] as $activity)
                            <div class="activity-item flex gap-3 relative py-2">
                                <div class="activity-dot w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 relative z-10 shadow-sm
                                    @switch($activity->action)
                                        @case('created') bg-green-100 @break
                                        @case('status_changed') bg-blue-100 @break
                                        @case('assigned') bg-purple-100 @break
                                        @default bg-gray-100
                                    @endswitch">
                                    @switch($activity->action)
                                        @case('created') <i class="fas fa-plus text-green-500 text-[10px]"></i> @break
                                        @case('status_changed') <i class="fas fa-exchange-alt text-blue-500 text-[10px]"></i> @break
                                        @case('assigned') <i class="fas fa-user-check text-purple-500 text-[10px]"></i> @break
                                        @default <i class="fas fa-edit text-gray-400 text-[10px]"></i>
                                    @endswitch
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-600">
                                        <span class="font-semibold text-gray-900">{{ $activity->user->name ?? 'System' }}</span>
                                        @switch($activity->action)
                                            @case('created') membuat tugas ini @break
                                            @case('status_changed') mengubah status ke <span class="font-medium">{{ $activity->new_value }}</span> @break
                                            @case('assigned') menugaskan ke <span class="font-medium">{{ $activity->new_value }}</span> @break
                                            @default melakukan perubahan
                                        @endswitch
                                    </p>
                                    <span class="text-[10px] text-gray-400">{{ $activity->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-2">
                                    <i class="fas fa-history text-gray-300 text-xl"></i>
                                </div>
                                <p class="text-gray-400 text-sm">Belum ada riwayat</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                </div>
            </div>
            @endif

            {{-- Delete Confirmation Modal --}}
            @if($showDeleteConfirm)
            <div class="fixed inset-0 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
                <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-5 text-center">
                    <div class="w-14 h-14 bg-red-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-trash text-red-500 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Hapus Tugas?</h3>
                    <p class="text-gray-500 text-sm mb-4">Tugas ini akan dihapus permanen.</p>
                    <div class="flex gap-2">
                        <button wire:click="cancelDelete" class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition">Batal</button>
                        <button wire:click="deleteTask" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-xl transition"><i class="fas fa-trash mr-1"></i>Hapus</button>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </template>
</div>
