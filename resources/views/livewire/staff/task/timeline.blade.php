@section('title', 'Gantt Timeline')

@push('styles')
<style>
    .timeline-bar { transition: all 0.2s ease; }
    .timeline-bar:hover { transform: scaleY(1.1); filter: brightness(1.1); }
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
                <span class="px-3 py-2 text-sm font-medium rounded-lg bg-gradient-to-r from-violet-600 to-purple-600 text-white shadow-sm flex items-center gap-2">
                    <i class="fas fa-chart-gantt"></i>
                    <span class="hidden sm:inline">Timeline</span>
                </span>
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
            
            {{-- Zoom Controls --}}
            <div class="bg-white rounded-xl p-1 border border-gray-200 flex shadow-sm">
                <button wire:click="$set('timelineZoom', 'day')" 
                        class="px-3 py-1.5 text-sm font-medium rounded-lg transition {{ $timelineZoom === 'day' ? 'bg-violet-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                    7D
                </button>
                <button wire:click="$set('timelineZoom', 'week')" 
                        class="px-3 py-1.5 text-sm font-medium rounded-lg transition {{ $timelineZoom === 'week' ? 'bg-violet-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                    14D
                </button>
                <button wire:click="$set('timelineZoom', 'month')" 
                        class="px-3 py-1.5 text-sm font-medium rounded-lg transition {{ $timelineZoom === 'month' ? 'bg-violet-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                    30D
                </button>
            </div>
            
            {{-- Branch Filter --}}
            <select wire:model.live="filterBranch" class="text-sm border-gray-200 rounded-xl focus:ring-violet-500 focus:border-violet-500 shadow-sm py-2">
                <option value="">Semua Cabang</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Compact Stats Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-7 gap-3">
        <div class="stat-card bg-gradient-to-r from-violet-500 to-purple-600 rounded-xl px-4 py-3 text-white flex items-center justify-between">
            <div class="w-9 h-9 bg-white/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-tasks text-sm"></i>
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold">{{ $stats['total_tasks'] }}</p>
                <p class="text-[10px] text-violet-100 uppercase tracking-wide">Total</p>
            </div>
        </div>
        <div class="stat-card bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl px-4 py-3 text-white flex items-center justify-between">
            <div class="w-9 h-9 bg-white/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-user text-sm"></i>
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold">{{ $stats['my_tasks'] }}</p>
                <p class="text-[10px] text-blue-100 uppercase tracking-wide">Saya</p>
            </div>
        </div>
        <div class="stat-card bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl px-4 py-3 text-white flex items-center justify-between">
            <div class="w-9 h-9 bg-white/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-calendar-check text-sm"></i>
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold">{{ $schedules->count() }}</p>
                <p class="text-[10px] text-emerald-100 uppercase tracking-wide">Jadwal</p>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl px-4 py-3 border border-gray-100 shadow-sm flex items-center justify-between">
            <div class="w-9 h-9 bg-amber-100 rounded-lg flex items-center justify-center text-amber-500">
                <i class="fas fa-hourglass-half text-sm"></i>
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold text-amber-600">{{ $stats['due_soon'] }}</p>
                <p class="text-[10px] text-gray-500 uppercase tracking-wide">Segera</p>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl px-4 py-3 border border-gray-100 shadow-sm flex items-center justify-between {{ $stats['overdue'] > 0 ? 'ring-1 ring-red-200' : '' }}">
            <div class="w-9 h-9 bg-red-100 rounded-lg flex items-center justify-center text-red-500">
                <i class="fas fa-exclamation-triangle text-sm"></i>
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold {{ $stats['overdue'] > 0 ? 'text-red-600' : 'text-gray-900' }}">{{ $stats['overdue'] }}</p>
                <p class="text-[10px] text-gray-500 uppercase tracking-wide">Terlambat</p>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl px-4 py-3 border border-gray-100 shadow-sm flex items-center justify-between">
            <div class="w-9 h-9 bg-gray-100 rounded-lg flex items-center justify-center text-gray-500">
                <i class="fas fa-inbox text-sm"></i>
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold text-gray-600">{{ $stats['no_date'] }}</p>
                <p class="text-[10px] text-gray-500 uppercase tracking-wide">Backlog</p>
            </div>
        </div>
        <div class="stat-card bg-gradient-to-r from-amber-500 to-orange-500 rounded-xl px-4 py-3 text-white flex items-center justify-between">
            <div class="w-9 h-9 bg-white/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-sticky-note text-sm"></i>
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold">{{ $stats['my_notes'] }}</p>
                <p class="text-[10px] text-amber-100 uppercase tracking-wide">Notes</p>
            </div>
        </div>
    </div>

    {{-- Gantt Chart --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <div class="min-w-[900px] relative">
                {{-- Date Headers --}}
                <div class="flex sticky top-0 z-10 bg-white border-b border-gray-200">
                    <div class="w-4 flex-shrink-0"></div>
                    <div class="flex-1 flex relative">
                        @for($i = 0; $i < $timelineDays; $i++)
                            @php
                                $date = $timelineStart->copy()->addDays($i);
                                $isToday = $date->isToday();
                                $isWeekend = $date->isWeekend();
                            @endphp
                            <div class="flex-1 min-w-[35px] text-center py-2 border-r border-gray-100 {{ $isWeekend ? 'bg-gray-50/50' : '' }}">
                                <p class="text-[9px] font-bold uppercase tracking-wide {{ $isToday ? 'text-red-500' : 'text-gray-400' }}">
                                    {{ $date->locale('id')->isoFormat('dd') }}
                                </p>
                                <p class="text-xs font-bold {{ $isToday ? 'text-red-500' : 'text-gray-600' }}">
                                    {{ $date->format('d') }}
                                </p>
                            </div>
                        @endfor
                    </div>
                </div>

                {{-- Gantt Rows --}}
                <div class="relative">
                    {{-- Today Marker Line --}}
                    @php
                        $todayOffset = now()->startOfDay()->diffInDays($timelineStart, false);
                        $todayLeftPercent = ($todayOffset / $timelineDays) * 100;
                    @endphp
                    @if($todayOffset >= 0 && $todayOffset < $timelineDays)
                    <div class="absolute top-0 bottom-0 w-0.5 bg-red-400 z-30 pointer-events-none" 
                         style="left: calc(16px + (100% - 16px) * {{ $todayLeftPercent / 100 }});"></div>
                    @endif
                    
                    @php
                        $tasksWithDates = $tasks->filter(fn($t) => $t->due_date || $t->start_date)->sortBy('due_date');
                        $allItems = collect();
                        
                        foreach($tasksWithDates as $task) {
                            $allItems->push([
                                'type' => 'task',
                                'item' => $task,
                                'title' => $task->title,
                                'user' => $task->assignee,
                                'start' => $task->start_date ?? $task->created_at,
                                'end' => $task->due_date ?? ($task->start_date ?? $task->created_at)->copy()->addDays(1),
                                'color' => match($task->priority) {
                                    'urgent' => 'from-rose-400 to-rose-500',
                                    'high' => 'from-orange-400 to-amber-500',
                                    'medium' => 'from-violet-400 to-purple-500',
                                    'low' => 'from-cyan-400 to-blue-500',
                                    default => 'from-blue-400 to-indigo-500'
                                },
                                'isOverdue' => $task->isOverdue(),
                            ]);
                        }
                        
                        foreach($schedules as $schedule) {
                            $typeColors = [
                                'blue' => 'from-blue-400 to-blue-500',
                                'indigo' => 'from-indigo-400 to-indigo-500',
                                'emerald' => 'from-emerald-400 to-emerald-500',
                                'violet' => 'from-violet-400 to-violet-500',
                                'amber' => 'from-amber-400 to-amber-500',
                                'cyan' => 'from-cyan-400 to-cyan-500',
                                'rose' => 'from-rose-400 to-rose-500',
                            ];
                            $typeInfo = $schedule->getTypeInfo();
                            
                            $allItems->push([
                                'type' => 'schedule',
                                'item' => $schedule,
                                'title' => $schedule->title,
                                'user' => $schedule->user,
                                'start' => $schedule->schedule_date,
                                'end' => $schedule->schedule_date->copy()->addDay(),
                                'color' => $typeColors[$typeInfo['color']] ?? 'from-gray-400 to-gray-500',
                                'isOverdue' => false,
                                'icon' => $typeInfo['icon'] ?? 'fa-calendar',
                            ]);
                        }
                        
                        $allItems = $allItems->sortBy('start');
                    @endphp
                    
                    @forelse($allItems as $index => $data)
                        @php
                            $startOffset = max(0, $data['start']->diffInDays($timelineStart, false));
                            $endOffset = min($timelineDays, $data['end']->diffInDays($timelineStart, false));
                            $duration = max(1, $endOffset - $startOffset);
                            
                            $leftPercent = ($startOffset / $timelineDays) * 100;
                            $widthPercent = ($duration / $timelineDays) * 100;
                            $isVisible = $startOffset < $timelineDays && $endOffset > 0;
                            
                            $rowBg = $index % 2 === 0 ? 'bg-white' : 'bg-gray-50/30';
                        @endphp
                        
                        @if($isVisible)
                        <div class="flex items-stretch min-h-[52px] border-b border-gray-100 {{ $rowBg }} hover:bg-blue-50/30 transition-colors">
                            <div class="w-4 flex-shrink-0"></div>
                            <div class="flex-1 relative flex items-center py-2.5">
                                {{-- Grid --}}
                                <div class="absolute inset-0 flex pointer-events-none">
                                    @for($i = 0; $i < $timelineDays; $i++)
                                        @php $date = $timelineStart->copy()->addDays($i); @endphp
                                        <div class="flex-1 border-r border-gray-100/50 {{ $date->isWeekend() ? 'bg-gray-50/30' : '' }}"></div>
                                    @endfor
                                </div>
                                
                                {{-- Bar --}}
                                <div class="timeline-bar absolute h-8 rounded-full bg-gradient-to-r {{ $data['color'] }} shadow-lg flex items-center gap-1.5 pl-1 pr-3 cursor-pointer {{ $data['isOverdue'] ? 'ring-2 ring-red-400 ring-offset-1' : '' }}"
                                     style="left: {{ $leftPercent }}%; width: {{ max(8, $widthPercent) }}%;"
                                     @if($data['type'] === 'task') wire:click="openTaskModal({{ $data['item']->id }})" @endif
                                     title="{{ $data['title'] }}">
                                    
                                    @if($data['user'])
                                        @if($data['user']->avatar)
                                            <img src="{{ asset('storage/' . $data['user']->avatar) }}" 
                                                 class="w-6 h-6 rounded-full border-2 border-white/80 object-cover flex-shrink-0">
                                        @else
                                            <div class="w-6 h-6 rounded-full bg-white/30 border-2 border-white/50 flex items-center justify-center flex-shrink-0">
                                                <span class="text-[10px] font-bold text-white">{{ strtoupper(substr($data['user']->name, 0, 1)) }}</span>
                                            </div>
                                        @endif
                                    @else
                                        <div class="w-6 h-6 rounded-full bg-white/20 border-2 border-white/30 flex items-center justify-center flex-shrink-0">
                                            <i class="fas {{ $data['type'] === 'schedule' ? ($data['icon'] ?? 'fa-calendar') : 'fa-user' }} text-white text-[8px]"></i>
                                        </div>
                                    @endif
                                    
                                    @if($widthPercent > 12)
                                        <span class="text-white text-[11px] font-semibold truncate">{{ Str::limit($data['title'], 18) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                    @empty
                        <div class="text-center py-16">
                            <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-chart-gantt text-gray-300 text-3xl"></i>
                            </div>
                            <p class="text-gray-500 font-medium">Tidak ada item di timeline</p>
                            <p class="text-gray-400 text-sm mt-1">Tambahkan due date pada tugas atau buat jadwal baru</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Legend --}}
        <div class="px-4 py-3 border-t border-gray-100 bg-gradient-to-r from-gray-50 to-white flex items-center gap-5 flex-wrap">
            <div class="flex items-center gap-1.5">
                <div class="w-0.5 h-4 bg-red-400 rounded"></div>
                <span class="text-xs text-gray-600 font-medium">Hari Ini</span>
            </div>
            <div class="h-4 w-px bg-gray-200"></div>
            <div class="flex items-center gap-1.5">
                <div class="w-5 h-3 bg-gradient-to-r from-rose-400 to-rose-500 rounded-full"></div>
                <span class="text-xs text-gray-500">Mendesak</span>
            </div>
            <div class="flex items-center gap-1.5">
                <div class="w-5 h-3 bg-gradient-to-r from-orange-400 to-amber-500 rounded-full"></div>
                <span class="text-xs text-gray-500">Tinggi</span>
            </div>
            <div class="flex items-center gap-1.5">
                <div class="w-5 h-3 bg-gradient-to-r from-violet-400 to-purple-500 rounded-full"></div>
                <span class="text-xs text-gray-500">Sedang</span>
            </div>
            <div class="flex items-center gap-1.5">
                <div class="w-5 h-3 bg-gradient-to-r from-cyan-400 to-blue-500 rounded-full"></div>
                <span class="text-xs text-gray-500">Rendah</span>
            </div>
        </div>
    </div>

    {{-- Backlog & Notes Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Backlog (Tasks Without Dates) --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-gray-100 rounded-lg flex items-center justify-center text-gray-500">
                        <i class="fas fa-inbox text-sm"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">Backlog</h3>
                        <p class="text-xs text-gray-500">Tugas tanpa tanggal deadline</p>
                    </div>
                </div>
                <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-bold rounded-lg">{{ $tasksWithoutDates->count() }}</span>
            </div>
            <div class="p-4 max-h-[300px] overflow-y-auto">
                @if($tasksWithoutDates->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($tasksWithoutDates as $task)
                    <div wire:click="openTaskModal({{ $task->id }})" 
                         class="p-3 border border-gray-100 rounded-xl hover:border-violet-200 hover:shadow-md cursor-pointer transition group">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg flex-shrink-0 flex items-center justify-center text-white shadow-sm {{
                                match($task->priority) {
                                    'urgent' => 'bg-gradient-to-br from-rose-500 to-red-600',
                                    'high' => 'bg-gradient-to-br from-orange-500 to-amber-500',
                                    'medium' => 'bg-gradient-to-br from-violet-500 to-purple-500',
                                    default => 'bg-gradient-to-br from-blue-500 to-indigo-500'
                                }
                            }}">
                                <i class="fas fa-tasks text-xs"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold text-gray-900 text-sm truncate group-hover:text-violet-600 transition">{{ $task->title }}</h4>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="px-2 py-0.5 text-[10px] font-semibold rounded uppercase"
                                          style="background: {{ $task->status?->color }}15; color: {{ $task->status?->color }}">
                                        {{ $task->status?->name ?? 'New' }}
                                    </span>
                                    @if($task->assignee)
                                    <span class="text-[10px] text-gray-400">{{ Str::limit($task->assignee->name, 10) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-check-circle text-emerald-500 text-xl"></i>
                    </div>
                    <p class="text-gray-500 text-sm">Semua tugas sudah memiliki deadline!</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Pinned Notes --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-amber-50 to-orange-50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-gradient-to-br from-amber-500 to-orange-500 rounded-lg flex items-center justify-center text-white shadow-sm">
                        <i class="fas fa-sticky-note text-sm"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">Catatan Cepat</h3>
                        <p class="text-xs text-gray-500">Notes terbaru</p>
                    </div>
                </div>
                <a href="{{ route('staff.task.notes') }}" wire:navigate 
                   class="px-2 py-1 bg-amber-100 hover:bg-amber-200 text-amber-700 text-xs font-semibold rounded-lg transition">
                    Lihat Semua
                </a>
            </div>
            <div class="p-4 space-y-3 max-h-[300px] overflow-y-auto">
                @forelse($pinnedNotes->merge($recentNotes)->unique('id')->take(5) as $note)
                @php $noteColors = $note->getColorClasses(); @endphp
                <div wire:click="openNoteModal({{ $note->id }})" 
                     class="p-3 {{ $noteColors['bg'] }} {{ $noteColors['border'] }} border rounded-xl hover:shadow-md cursor-pointer transition group">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <h4 class="font-semibold text-gray-900 text-sm truncate flex-1 group-hover:text-violet-600">{{ $note->title }}</h4>
                        @if($note->is_pinned)
                        <i class="fas fa-thumbtack text-amber-500 text-xs"></i>
                        @endif
                    </div>
                    @if($note->content)
                    <p class="text-xs text-gray-500 line-clamp-2 mb-2">{{ Str::limit($note->content, 80) }}</p>
                    @endif
                    <div class="flex items-center justify-between text-[10px] text-gray-400">
                        <span>{{ $note->updated_at->diffForHumans() }}</span>
                        @if($note->is_public)
                        <span class="px-1.5 py-0.5 bg-blue-100 text-blue-600 rounded text-[9px] font-semibold">Publik</span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-6">
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-sticky-note text-amber-500 text-xl"></i>
                    </div>
                    <p class="text-gray-500 text-sm mb-3">Belum ada catatan</p>
                    <a href="{{ route('staff.task.notes') }}" wire:navigate 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-amber-500 to-orange-500 text-white text-sm font-semibold rounded-xl shadow-sm hover:shadow-md transition">
                        <i class="fas fa-plus"></i>
                        Buat Catatan
                    </a>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Task Detail Modal (Teleport to body) --}}
    @if($showTaskModal && $selectedTask)
    @teleport('body')
    <div class="fixed inset-0 z-[99999] flex items-center justify-center p-4" x-data x-init="document.body.style.overflow = 'hidden'" x-on:remove="document.body.style.overflow = ''">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="closeTaskModal"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $selectedTask->title }}</h3>
                        <p class="text-sm text-gray-500">{{ $selectedTask->status?->name ?? 'No Status' }}</p>
                    </div>
                    <button wire:click="closeTaskModal" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                @if($selectedTask->description)
                <div class="mb-4">
                    <p class="text-sm text-gray-600">{{ $selectedTask->description }}</p>
                </div>
                @endif
                
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Prioritas:</span>
                        <span class="font-medium ml-1 capitalize">{{ $selectedTask->priority }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Deadline:</span>
                        <span class="font-medium ml-1">{{ $selectedTask->due_date?->format('d M Y') ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Assignee:</span>
                        <span class="font-medium ml-1">{{ $selectedTask->assignee?->name ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Reporter:</span>
                        <span class="font-medium ml-1">{{ $selectedTask->reporter?->name ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endteleport
    @endif

    {{-- Note Preview Modal --}}
    @if($showNoteModal && $selectedNote)
    @teleport('body')
    <div class="fixed inset-0 z-[99999] flex items-center justify-center p-4" x-data x-init="document.body.style.overflow = 'hidden'" x-on:remove="document.body.style.overflow = ''">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="closeNoteModal"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
            @php 
                $noteColor = $selectedNote->getColorClasses();
                $noteCat = $selectedNote->getCategoryInfo();
            @endphp
            
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-gray-100 {{ $noteColor['bg'] }}">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 {{ $noteColor['bg'] }} {{ $noteColor['border'] }} border rounded-xl flex items-center justify-center">
                            <i class="fas {{ $noteCat['icon'] }} {{ $noteColor['text'] }}"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ $selectedNote->title }}</h3>
                            <div class="flex items-center gap-2">
                                <span class="text-xs {{ $noteColor['text'] }} font-medium">{{ $noteCat['label'] }}</span>
                                @if($selectedNote->is_public)
                                <span class="px-1.5 py-0.5 bg-blue-100 text-blue-600 text-[10px] rounded font-semibold">Publik</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <button wire:click="closeNoteModal" class="w-8 h-8 rounded-lg hover:bg-white/50 flex items-center justify-center text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            {{-- Content --}}
            <div class="p-6">
                @if($selectedNote->content)
                <div class="prose prose-sm max-w-none">
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $selectedNote->content }}</p>
                </div>
                @else
                <div class="text-center py-4 text-gray-400">
                    <i class="fas fa-align-left text-2xl mb-2"></i>
                    <p class="text-sm">Catatan ini tidak memiliki konten</p>
                </div>
                @endif
            </div>
            
            {{-- Footer --}}
            <div class="px-6 py-3 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    @if($selectedNote->user)
                    <div class="w-6 h-6 bg-violet-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                        {{ strtoupper(substr($selectedNote->user->name, 0, 1)) }}
                    </div>
                    <span>{{ $selectedNote->user->name }}</span>
                    <span class="text-gray-300">•</span>
                    @endif
                    <span class="text-gray-400">{{ $selectedNote->updated_at->format('d M Y, H:i') }}</span>
                </div>
                <a href="{{ route('staff.task.notes') }}" wire:navigate 
                   class="px-3 py-1.5 bg-violet-100 hover:bg-violet-200 text-violet-700 text-xs font-semibold rounded-lg transition">
                    Buka di Notes
                </a>
            </div>
        </div>
    </div>
    @endteleport
    @endif
</div>
