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
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-gradient-to-br from-violet-500 via-purple-500 to-fuchsia-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-purple-500/30">
                <i class="fas fa-chart-gantt text-2xl"></i>
            </div>
            <div>
                <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Tugas & Jadwal</h1>
                <p class="text-sm text-gray-500 flex items-center gap-2">
                    <span class="w-2 h-2 bg-violet-500 rounded-full animate-pulse"></span>
                    Tampilan Gantt Timeline
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
                <span class="px-3 py-2 text-sm font-medium rounded-lg bg-gradient-to-r from-violet-600 to-purple-600 text-white shadow-sm flex items-center gap-2">
                    <i class="fas fa-chart-gantt"></i>
                    <span class="hidden sm:inline">Timeline</span>
                </span>
                <a href="{{ route('staff.task.schedule') }}" 
                   class="px-3 py-2 text-sm font-medium rounded-lg transition flex items-center gap-2 text-gray-600 hover:bg-gray-100">
                    <i class="fas fa-calendar-alt"></i>
                    <span class="hidden sm:inline">Jadwal</span>
                </a>
                <a href="{{ route('staff.task.notes') }}" 
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
            <select wire:model.live="filterBranch" class="text-sm border-gray-200 rounded-xl focus:ring-violet-500 focus:border-violet-500 shadow-sm">
                <option value="">Semua Cabang</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="stat-card bg-gradient-to-r from-violet-500 to-purple-600 rounded-xl px-4 py-3 text-white flex items-center justify-between">
            <div class="w-9 h-9 bg-white/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-tasks text-sm"></i>
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold">{{ $tasks->count() }}</p>
                <p class="text-[10px] text-violet-100 uppercase tracking-wide">Tugas</p>
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
                <i class="fas fa-clock text-sm"></i>
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold text-gray-900">{{ $timelineDays }}</p>
                <p class="text-[10px] text-gray-500 uppercase tracking-wide">Hari</p>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl px-4 py-3 border border-gray-100 shadow-sm flex items-center justify-between">
            <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center text-blue-500">
                <i class="fas fa-calendar text-sm"></i>
            </div>
            <div class="text-right">
                <p class="text-lg font-bold text-gray-900">{{ $timelineStart->format('d') }}-{{ $timelineEnd->format('d M') }}</p>
                <p class="text-[10px] text-gray-500 uppercase tracking-wide">Periode</p>
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
                    <span class="font-medium ml-1">{{ $selectedTask->creator?->name ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endteleport
@endif
