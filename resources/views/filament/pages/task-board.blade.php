<x-filament-panels::page>
    <style>
        /* Consistent with Dashboard Styles */
        .gradient-card {
            border-radius: 12px;
            padding: 0.875rem;
            color: white;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 8px 30px -10px rgba(0,0,0,0.25);
        }
        @media (min-width: 640px) {
            .gradient-card { border-radius: 16px; padding: 1.25rem; }
        }
        .gradient-card:hover { transform: translateY(-3px); }
        .gradient-card::before {
            content: '';
            position: absolute;
            top: -15px;
            right: -15px;
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
        }
        @media (min-width: 640px) {
            .gradient-card::before { width: 60px; height: 60px; }
        }
        .gradient-blue { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .gradient-green { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
        .gradient-orange { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .gradient-purple { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .gradient-red { background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%); }
        .gradient-indigo { background: linear-gradient(135deg, #5f72bd 0%, #9b23ea 100%); }
        .gradient-amber { background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%); }
        
        .stat-icon {
            width: 2.25rem; height: 2.25rem;
            background: rgba(255,255,255,0.2);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        @media (min-width: 640px) {
            .stat-icon { width: 2.75rem; height: 2.75rem; border-radius: 10px; }
        }
        .stat-icon svg { width: 1rem; height: 1rem; }
        @media (min-width: 640px) {
            .stat-icon svg { width: 1.25rem; height: 1.25rem; }
        }
        .stat-value { font-size: 1.25rem; font-weight: 800; line-height: 1; }
        @media (min-width: 640px) {
            .stat-value { font-size: 1.5rem; }
        }
        .stat-label { font-size: 0.65rem; opacity: 0.9; margin-top: 0.125rem; }
        @media (min-width: 640px) {
            .stat-label { font-size: 0.75rem; }
        }
        
        .section-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }
        @media (min-width: 640px) {
            .section-card { border-radius: 16px; }
        }
        .dark .section-card { background: rgb(31 41 55); }
        
        .section-header {
            padding: 0.625rem 0.875rem;
            color: white;
            font-weight: 600;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .section-header svg { width: 1rem; height: 1rem; }
        
        /* Kanban Specific Styles */
        .kanban-container {
            display: flex;
            gap: 1rem;
            overflow-x: auto;
            padding-bottom: 1rem;
            min-height: 500px;
        }
        @media (min-width: 640px) {
            .kanban-container { gap: 1.25rem; }
        }
        
        .kanban-column {
            flex-shrink: 0;
            width: 280px;
            display: flex;
            flex-direction: column;
        }
        @media (min-width: 640px) {
            .kanban-column { width: 320px; }
        }
        
        .column-header {
            background: white;
            border-radius: 12px 12px 0 0;
            padding: 0.75rem 1rem;
            border: 1px solid rgba(0,0,0,0.08);
            border-bottom: none;
        }
        .dark .column-header { 
            background: rgb(31 41 55); 
            border-color: rgba(255,255,255,0.08);
        }
        
        .column-body {
            flex: 1;
            background: rgb(249 250 251);
            border-radius: 0 0 12px 12px;
            padding: 0.75rem;
            border: 1px solid rgba(0,0,0,0.08);
            border-top: none;
            min-height: 400px;
            transition: all 0.2s ease;
        }
        .dark .column-body { 
            background: rgb(17 24 39); 
            border-color: rgba(255,255,255,0.08);
        }
        .column-body.drag-over {
            background: rgb(239 246 255);
            border-color: rgb(59 130 246);
        }
        .dark .column-body.drag-over {
            background: rgba(59, 130, 246, 0.1);
        }
        
        .task-card {
            background: white;
            border-radius: 10px;
            padding: 0.875rem;
            margin-bottom: 0.625rem;
            border: 1px solid rgba(0,0,0,0.08);
            cursor: grab;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .dark .task-card { 
            background: rgb(31 41 55); 
            border-color: rgba(255,255,255,0.08);
        }
        .task-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .task-card:active { cursor: grabbing; }
        .task-card.dragging {
            opacity: 0.5;
            transform: rotate(2deg);
        }
        
        .priority-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.125rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.625rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }
        .priority-urgent { background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%); color: white; }
        .priority-high { background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%); color: white; }
        .priority-medium { background: rgb(219 234 254); color: rgb(37 99 235); }
        .priority-low { background: rgb(243 244 246); color: rgb(107 114 128); }
        .dark .priority-medium { background: rgba(59, 130, 246, 0.2); }
        .dark .priority-low { background: rgba(107, 114, 128, 0.2); }
        
        .toolbar-card {
            background: white;
            border-radius: 12px;
            padding: 0.875rem;
            border: 1px solid rgba(0,0,0,0.08);
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        }
        @media (min-width: 640px) {
            .toolbar-card { padding: 1rem; }
        }
        .dark .toolbar-card { 
            background: rgb(31 41 55); 
            border-color: rgba(255,255,255,0.08);
        }
        
        /* Custom scrollbar */
        .kanban-container::-webkit-scrollbar { height: 6px; }
        .kanban-container::-webkit-scrollbar-track { background: transparent; }
        .kanban-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        .kanban-container::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .dark .kanban-container::-webkit-scrollbar-thumb { background: #475569; }
        
        [x-cloak] { display: none !important; }
    </style>

    @php
        $totalTasks = collect($tasks)->flatten(1)->count();
        $completedTasks = collect($statuses)->where('is_done', true)->pluck('id')->flatMap(fn($id) => $tasks[$id] ?? [])->count();
        $overdueTasks = collect($tasks)->flatten(1)->filter(fn($t) => isset($t['due_date']) && \Carbon\Carbon::parse($t['due_date'])->isPast() && !($t['status']['is_done'] ?? false))->count();
        $inProgressTasks = collect($tasks)->flatten(1)->filter(fn($t) => in_array($t['status']['slug'] ?? '', ['in_progress', 'review']))->count();
    @endphp

    {{-- Stats Row --}}
    <div class="flex gap-2 sm:gap-4 mb-4">
        <div class="gradient-card gradient-blue flex-1 !p-2 sm:!p-4">
            <div class="flex items-center gap-2 sm:gap-3">
                <div class="stat-icon !w-7 !h-7 sm:!w-11 sm:!h-11"><x-heroicon-o-clipboard-document-list class="text-white !w-4 !h-4 sm:!w-5 sm:!h-5" /></div>
                <div><div class="stat-value">{{ $totalTasks }}</div><div class="stat-label">Total Task</div></div>
            </div>
        </div>
        <div class="gradient-card gradient-amber flex-1 !p-2 sm:!p-4">
            <div class="flex items-center gap-2 sm:gap-3">
                <div class="stat-icon !w-7 !h-7 sm:!w-11 sm:!h-11"><x-heroicon-o-arrow-path class="text-white !w-4 !h-4 sm:!w-5 sm:!h-5" /></div>
                <div><div class="stat-value">{{ $inProgressTasks }}</div><div class="stat-label">In Progress</div></div>
            </div>
        </div>
        <div class="gradient-card gradient-green flex-1 !p-2 sm:!p-4">
            <div class="flex items-center gap-2 sm:gap-3">
                <div class="stat-icon !w-7 !h-7 sm:!w-11 sm:!h-11"><x-heroicon-o-check-badge class="text-white !w-4 !h-4 sm:!w-5 sm:!h-5" /></div>
                <div><div class="stat-value">{{ $completedTasks }}</div><div class="stat-label">Selesai</div></div>
            </div>
        </div>
        <div class="gradient-card gradient-red flex-1 !p-2 sm:!p-4">
            <div class="flex items-center gap-2 sm:gap-3">
                <div class="stat-icon !w-7 !h-7 sm:!w-11 sm:!h-11"><x-heroicon-o-exclamation-triangle class="text-white !w-4 !h-4 sm:!w-5 sm:!h-5" /></div>
                <div><div class="stat-value">{{ $overdueTasks }}</div><div class="stat-label">Overdue</div></div>
            </div>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="toolbar-card mb-4" x-data="{ searchQuery: '', priorityFilter: '' }">
        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
            {{-- Search --}}
            <div class="relative flex-1 max-w-xs">
                <x-heroicon-o-magnifying-glass class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                <input type="text" 
                       x-model="searchQuery"
                       placeholder="Cari task..." 
                       class="w-full pl-9 pr-3 py-2 text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            
            {{-- Project Filter --}}
            <div class="relative">
                <x-heroicon-o-folder class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" />
                <select wire:model.live="projectId" 
                        class="appearance-none pl-9 pr-8 py-2 text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 focus:ring-2 focus:ring-primary-500 cursor-pointer">
                    <option value="">Semua Proyek</option>
                    @foreach(\App\Models\Project::where('status', 'active')->get() as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>
            
            {{-- Priority Filter Buttons --}}
            <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-800 rounded-lg p-1">
                <button @click="priorityFilter = ''" 
                        :class="priorityFilter === '' ? 'bg-white dark:bg-gray-700 shadow-sm' : 'hover:bg-gray-200 dark:hover:bg-gray-700'"
                        class="px-3 py-1.5 text-xs font-medium rounded-md transition">
                    All
                </button>
                <button @click="priorityFilter = 'urgent'" 
                        :class="priorityFilter === 'urgent' ? 'bg-red-500 text-white shadow-sm' : 'text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30'"
                        class="px-3 py-1.5 text-xs font-medium rounded-md transition">
                    🔥 Urgent
                </button>
                <button @click="priorityFilter = 'high'" 
                        :class="priorityFilter === 'high' ? 'bg-orange-500 text-white shadow-sm' : 'text-orange-600 hover:bg-orange-50 dark:hover:bg-orange-900/30'"
                        class="px-3 py-1.5 text-xs font-medium rounded-md transition">
                    High
                </button>
            </div>
            
            {{-- Actions --}}
            <div class="flex items-center gap-2 sm:ml-auto">
                <button wire:click="loadBoard" class="p-2 rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 transition" title="Refresh">
                    <x-heroicon-o-arrow-path class="w-4 h-4 text-gray-500" />
                </button>
                <a href="{{ \App\Filament\Resources\TaskResource::getUrl('create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-lg transition"
                   style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <x-heroicon-o-plus class="w-4 h-4" />
                    <span class="hidden sm:inline">Task Baru</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Kanban Board --}}
    <div class="kanban-container" x-data="kanbanBoard()">
        @foreach($statuses as $status)
            <div class="kanban-column">
                {{-- Column Header --}}
                <div class="column-header">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $status['color'] }};"></span>
                            <span class="font-semibold text-sm text-gray-800 dark:text-gray-200">{{ $status['name'] }}</span>
                        </div>
                        <span class="text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 px-2 py-0.5 rounded-full">
                            {{ count($tasks[$status['id']] ?? []) }}
                        </span>
                    </div>
                </div>
                
                {{-- Column Body --}}
                <div class="column-body"
                     :class="dragOverColumn === {{ $status['id'] }} && 'drag-over'"
                     x-on:drop="onDrop($event, {{ $status['id'] }})"
                     x-on:dragover.prevent="dragOverColumn = {{ $status['id'] }}"
                     x-on:dragleave="dragOverColumn = null"
                     x-on:dragenter.prevent>
                    
                    @forelse($tasks[$status['id']] ?? [] as $task)
                        <div class="task-card"
                             draggable="true"
                             x-on:dragstart="onDragStart($event, {{ $task['id'] }})"
                             x-on:dragend="onDragEnd($event)">
                            
                            {{-- Header: Type + Priority --}}
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-base">
                                        @switch($task['type'] ?? 'task')
                                            @case('bug') 🐛 @break
                                            @case('feature') ✨ @break
                                            @case('improvement') 📈 @break
                                            @case('documentation') 📝 @break
                                            @default 📋
                                        @endswitch
                                    </span>
                                    <span class="text-xs text-gray-400 font-mono">#{{ $task['id'] }}</span>
                                </div>
                                <span class="priority-badge priority-{{ $task['priority'] ?? 'low' }}">
                                    {{ ucfirst($task['priority'] ?? 'low') }}
                                </span>
                            </div>
                            
                            {{-- Title --}}
                            <a href="{{ \App\Filament\Resources\TaskResource::getUrl('view', ['record' => $task['id']]) }}"
                               class="block font-semibold text-sm text-gray-800 dark:text-gray-100 hover:text-primary-600 dark:hover:text-primary-400 transition line-clamp-2 mb-2">
                                {{ $task['title'] }}
                            </a>
                            
                            {{-- Project --}}
                            @if($task['project'] ?? null)
                                <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 mb-2">
                                    <x-heroicon-o-folder class="w-3 h-3" />
                                    <span class="truncate">{{ $task['project']['name'] }}</span>
                                </div>
                            @endif
                            
                            {{-- Footer: Assignee + Due Date --}}
                            <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-700">
                                {{-- Assignee --}}
                                <div class="flex items-center gap-1.5">
                                    @if($task['assignee'] ?? null)
                                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-white text-xs font-bold" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                            {{ strtoupper(substr($task['assignee']['name'], 0, 1)) }}
                                        </div>
                                        <span class="text-xs text-gray-600 dark:text-gray-400 truncate max-w-[70px]">{{ $task['assignee']['name'] }}</span>
                                    @else
                                        <div class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                            <x-heroicon-o-user class="w-3 h-3 text-gray-400" />
                                        </div>
                                        <span class="text-xs text-gray-400 italic">-</span>
                                    @endif
                                </div>
                                
                                {{-- Due Date --}}
                                @if($task['due_date'] ?? null)
                                    @php
                                        $dueDate = \Carbon\Carbon::parse($task['due_date']);
                                        $isOverdue = $dueDate->isPast() && !($task['status']['is_done'] ?? false);
                                        $isDueSoon = !$isOverdue && $dueDate->isBetween(now(), now()->addDays(2));
                                    @endphp
                                    <span class="flex items-center gap-1 text-xs px-2 py-0.5 rounded-full {{ $isOverdue ? 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400' : ($isDueSoon ? 'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400') }}">
                                        @if($isOverdue)
                                            <x-heroicon-o-exclamation-triangle class="w-3 h-3" />
                                        @else
                                            <x-heroicon-o-calendar class="w-3 h-3" />
                                        @endif
                                        {{ $dueDate->format('d M') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-xl flex items-center justify-center mb-3">
                                <x-heroicon-o-inbox class="w-6 h-6 text-gray-400" />
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Tidak ada task</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

    <script>
        function kanbanBoard() {
            return {
                draggedTaskId: null,
                dragOverColumn: null,
                
                onDragStart(event, taskId) {
                    this.draggedTaskId = taskId;
                    event.target.classList.add('dragging');
                    event.dataTransfer.effectAllowed = 'move';
                },
                
                onDragEnd(event) {
                    event.target.classList.remove('dragging');
                    this.dragOverColumn = null;
                },
                
                onDrop(event, statusId) {
                    if (this.draggedTaskId) {
                        @this.call('moveTask', this.draggedTaskId, statusId);
                        this.draggedTaskId = null;
                        this.dragOverColumn = null;
                    }
                }
            }
        }
    </script>
</x-filament-panels::page>
