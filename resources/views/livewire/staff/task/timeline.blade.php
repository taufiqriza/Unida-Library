<div class="min-h-screen bg-gradient-to-br from-slate-50 via-gray-50 to-violet-50">
    {{-- Shared Navigation Header --}}
    <div class="bg-white/80 backdrop-blur-sm border-b border-gray-100 sticky top-0 z-30">
        <div class="max-w-[1600px] mx-auto px-4 py-3">
            <div class="flex items-center justify-between">
                {{-- Title --}}
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-violet-500 to-purple-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-violet-500/25">
                        <i class="fas fa-chart-gantt text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-gray-900">Gantt Timeline</h1>
                        <p class="text-xs text-gray-500">{{ $timelineStart->format('d M') }} - {{ $timelineEnd->format('d M Y') }}</p>
                    </div>
                </div>
                
                {{-- 4-Tab Navigation --}}
                <div class="flex items-center bg-gray-100/80 rounded-xl p-1">
                    <a href="{{ route('staff.task.index') }}" 
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-all text-gray-500 hover:text-gray-700">
                        <i class="fas fa-columns mr-1.5"></i>Kanban
                    </a>
                    <a href="{{ route('staff.task.timeline') }}" 
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-all bg-white text-violet-600 shadow-sm">
                        <i class="fas fa-chart-gantt mr-1.5"></i>Timeline
                    </a>
                    <a href="{{ route('staff.task.schedule') }}" 
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-all text-gray-500 hover:text-gray-700">
                        <i class="fas fa-calendar-alt mr-1.5"></i>Jadwal
                    </a>
                    <a href="{{ route('staff.task.notes') }}" 
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-all text-gray-500 hover:text-gray-700">
                        <i class="fas fa-sticky-note mr-1.5"></i>Notes
                    </a>
                </div>
                
                {{-- Zoom Controls --}}
                <div class="flex items-center gap-3">
                    <select wire:model.live="filterBranch" class="text-sm border-gray-200 rounded-lg focus:ring-violet-500 focus:border-violet-500">
                        <option value="">Semua Cabang</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    <div class="flex bg-gray-100 rounded-xl p-1">
                        <button wire:click="$set('timelineZoom', 'day')" 
                                class="px-3 py-1.5 text-xs font-semibold rounded-lg transition {{ $timelineZoom === 'day' ? 'bg-white text-violet-600 shadow' : 'text-gray-500 hover:text-gray-700' }}">
                            7D
                        </button>
                        <button wire:click="$set('timelineZoom', 'week')" 
                                class="px-3 py-1.5 text-xs font-semibold rounded-lg transition {{ $timelineZoom === 'week' ? 'bg-white text-violet-600 shadow' : 'text-gray-500 hover:text-gray-700' }}">
                            14D
                        </button>
                        <button wire:click="$set('timelineZoom', 'month')" 
                                class="px-3 py-1.5 text-xs font-semibold rounded-lg transition {{ $timelineZoom === 'month' ? 'bg-white text-violet-600 shadow' : 'text-gray-500 hover:text-gray-700' }}">
                            30D
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Gantt Chart Body --}}
    <div class="max-w-[1600px] mx-auto p-4">
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
                                    <div class="absolute h-8 rounded-full bg-gradient-to-r {{ $data['color'] }} shadow-lg flex items-center gap-1.5 pl-1 pr-3 cursor-pointer hover:scale-[1.02] hover:shadow-xl transition-all {{ $data['isOverdue'] ? 'ring-2 ring-red-400 ring-offset-1' : '' }}"
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
</div>
