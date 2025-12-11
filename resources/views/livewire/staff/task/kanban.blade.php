@push('styles')
<style>
    .kanban-column { min-height: 500px; }
    .task-card { transition: all 0.2s ease; cursor: pointer; }
    .task-card:hover { transform: translateY(-2px); box-shadow: 0 8px 16px -4px rgba(0,0,0,0.1); }
    .stat-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .stat-card:hover { transform: translateY(-2px); }
    .priority-badge { font-size: 10px; padding: 2px 6px; border-radius: 4px; font-weight: 600; }
    .type-icon { width: 18px; height: 18px; display: inline-flex; align-items: center; justify-content: center; }
</style>
@endpush

<div class="space-y-5" x-data="{ showQuickAdd: null }">
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-gradient-to-br from-violet-500 via-purple-500 to-fuchsia-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-purple-500/25">
                <i class="fas fa-tasks text-xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Task Board</h1>
                <p class="text-sm text-gray-500">Kelola tugas dengan Kanban</p>
            </div>
        </div>

        <a href="{{ route('staff.task.create') }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 text-white font-medium rounded-xl shadow-lg shadow-purple-500/25 transition text-sm">
            <i class="fas fa-plus"></i>
            <span>Buat Task</span>
        </a>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
        <div class="stat-card bg-gradient-to-r from-violet-500 to-purple-600 rounded-xl p-4 text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold">{{ $stats['total'] }}</p>
                    <p class="text-xs text-violet-100">Total Task</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl p-4 text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-check"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold">{{ $stats['my_tasks'] }}</p>
                    <p class="text-xs text-blue-100">Task Saya</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-red-400 to-rose-500 rounded-lg flex items-center justify-center text-white">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['overdue'] }}</p>
                    <p class="text-xs text-gray-500">Overdue</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-lg flex items-center justify-center text-white">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['due_soon'] }}</p>
                    <p class="text-xs text-gray-500">Due Soon</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3">
        <div class="flex flex-wrap items-center gap-3">
            <select wire:model.live="filterAssignee" class="px-3 py-2 bg-gray-50 border-transparent rounded-lg text-sm">
                <option value="">Semua Assignee</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="filterPriority" class="px-3 py-2 bg-gray-50 border-transparent rounded-lg text-sm">
                <option value="">Semua Prioritas</option>
                <option value="urgent">🔴 Urgent</option>
                <option value="high">🟠 High</option>
                <option value="medium">🟡 Medium</option>
                <option value="low">🟢 Low</option>
            </select>
            <select wire:model.live="filterType" class="px-3 py-2 bg-gray-50 border-transparent rounded-lg text-sm">
                <option value="">Semua Tipe</option>
                <option value="task">📋 Task</option>
                <option value="bug">🐛 Bug</option>
                <option value="feature">✨ Feature</option>
                <option value="improvement">📈 Improvement</option>
            </select>
            @if($filterAssignee || $filterPriority || $filterType)
                <button wire:click="$set('filterAssignee', ''); $set('filterPriority', ''); $set('filterType', '')" 
                        class="px-3 py-2 text-gray-500 hover:text-gray-700 text-sm">
                    <i class="fas fa-times mr-1"></i>Reset
                </button>
            @endif
        </div>
    </div>

    {{-- Kanban Board --}}
    <div class="flex gap-4 overflow-x-auto pb-4">
        @foreach($statuses as $status)
            <div class="flex-shrink-0 w-72">
                {{-- Column Header --}}
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full" style="background-color: {{ $status->color }}"></div>
                        <h3 class="font-bold text-gray-700">{{ $status->name }}</h3>
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">
                            {{ $tasksByStatus[$status->id]->count() }}
                        </span>
                    </div>
                    <button @click="showQuickAdd = {{ $status->id }}" 
                            class="p-1 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded transition">
                        <i class="fas fa-plus text-sm"></i>
                    </button>
                </div>

                {{-- Column Body --}}
                <div class="kanban-column bg-gray-50 rounded-xl p-2 space-y-2">
                    {{-- Quick Add Card --}}
                    <div x-show="showQuickAdd === {{ $status->id }}" x-cloak class="bg-white rounded-lg p-3 shadow-sm border border-violet-200">
                        <input wire:model="newTaskTitle" 
                               type="text" 
                               placeholder="Judul task..."
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm mb-2"
                               @keydown.enter="$wire.createQuickTask({{ $status->id }})"
                               @keydown.escape="showQuickAdd = null">
                        <div class="flex gap-2">
                            <button wire:click="createQuickTask({{ $status->id }})" 
                                    @click="showQuickAdd = null"
                                    class="flex-1 px-3 py-1.5 bg-violet-600 text-white text-xs font-medium rounded-lg">
                                Simpan
                            </button>
                            <button @click="showQuickAdd = null" class="px-3 py-1.5 bg-gray-100 text-gray-600 text-xs font-medium rounded-lg">
                                Batal
                            </button>
                        </div>
                    </div>

                    {{-- Task Cards --}}
                    @forelse($tasksByStatus[$status->id] as $task)
                        <div wire:click="openTaskModal({{ $task->id }})" 
                             class="task-card bg-white rounded-lg p-3 shadow-sm border border-gray-100 {{ $task->isOverdue() ? 'border-l-4 border-l-red-500' : '' }}">
                            {{-- Type & Priority --}}
                            <div class="flex items-center justify-between mb-2">
                                <span class="type-icon text-sm">
                                    @switch($task->type)
                                        @case('bug') 🐛 @break
                                        @case('feature') ✨ @break
                                        @case('improvement') 📈 @break
                                        @default 📋
                                    @endswitch
                                </span>
                                <span class="priority-badge 
                                    @switch($task->priority)
                                        @case('urgent') bg-red-100 text-red-700 @break
                                        @case('high') bg-orange-100 text-orange-700 @break
                                        @case('medium') bg-yellow-100 text-yellow-700 @break
                                        @default bg-green-100 text-green-700
                                    @endswitch
                                ">{{ ucfirst($task->priority) }}</span>
                            </div>

                            {{-- Title --}}
                            <p class="font-medium text-gray-900 text-sm line-clamp-2 mb-2">{{ $task->title }}</p>

                            {{-- Project Badge --}}
                            @if($task->project)
                                <span class="inline-block px-2 py-0.5 bg-violet-100 text-violet-700 text-[10px] font-medium rounded mb-2">
                                    {{ $task->project->name }}
                                </span>
                            @endif

                            {{-- Footer --}}
                            <div class="flex items-center justify-between text-xs text-gray-400">
                                <span>#{{ $task->id }}</span>
                                <div class="flex items-center gap-2">
                                    @if($task->due_date)
                                        <span class="{{ $task->isOverdue() ? 'text-red-500 font-medium' : '' }}">
                                            <i class="fas fa-calendar mr-1"></i>{{ $task->due_date->format('d M') }}
                                        </span>
                                    @endif
                                    @if($task->assignee)
                                        <div class="w-5 h-5 bg-gradient-to-br from-violet-400 to-purple-500 rounded-full flex items-center justify-center text-white text-[10px] font-bold" title="{{ $task->assignee->name }}">
                                            {{ strtoupper(substr($task->assignee->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-inbox text-gray-300 text-2xl mb-2"></i>
                            <p class="text-gray-400 text-xs">Tidak ada task</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

    {{-- Task Detail Modal --}}
    @if($showTaskModal && $selectedTask)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background: rgba(0,0,0,0.5);">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden" @click.away="$wire.closeTaskModal()">
            {{-- Modal Header --}}
            <div class="p-4 border-b border-gray-100 flex items-center justify-between" style="background-color: {{ $selectedTask->status->color }}20;">
                <div class="flex items-center gap-2">
                    <span class="text-lg">
                        @switch($selectedTask->type)
                            @case('bug') 🐛 @break
                            @case('feature') ✨ @break
                            @case('improvement') 📈 @break
                            @default 📋
                        @endswitch
                    </span>
                    <span class="text-gray-500 text-sm font-mono">#{{ $selectedTask->id }}</span>
                </div>
                <button wire:click="closeTaskModal" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-6 overflow-y-auto max-h-[60vh]">
                <h2 class="text-xl font-bold text-gray-900 mb-4">{{ $selectedTask->title }}</h2>

                @if($selectedTask->description)
                    <div class="prose prose-sm max-w-none text-gray-600 mb-6">
                        {!! $selectedTask->description !!}
                    </div>
                @endif

                {{-- Meta Info --}}
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Status</p>
                        <span class="px-2 py-1 rounded text-xs font-medium text-white" style="background-color: {{ $selectedTask->status->color }}">
                            {{ $selectedTask->status->name }}
                        </span>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Prioritas</p>
                        <span class="priority-badge 
                            @switch($selectedTask->priority)
                                @case('urgent') bg-red-100 text-red-700 @break
                                @case('high') bg-orange-100 text-orange-700 @break
                                @case('medium') bg-yellow-100 text-yellow-700 @break
                                @default bg-green-100 text-green-700
                            @endswitch
                        ">{{ ucfirst($selectedTask->priority) }}</span>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Assignee</p>
                        <p class="font-medium text-gray-900 text-sm">{{ $selectedTask->assignee?->name ?? 'Unassigned' }}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Deadline</p>
                        <p class="font-medium text-sm {{ $selectedTask->isOverdue() ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $selectedTask->due_date?->format('d M Y') ?? '-' }}
                            @if($selectedTask->isOverdue())
                                <span class="text-red-500 text-xs">(Overdue)</span>
                            @endif
                        </p>
                    </div>
                    @if($selectedTask->project)
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Proyek</p>
                        <p class="font-medium text-gray-900 text-sm">{{ $selectedTask->project->name }}</p>
                    </div>
                    @endif
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Reporter</p>
                        <p class="font-medium text-gray-900 text-sm">{{ $selectedTask->reporter?->name ?? '-' }}</p>
                    </div>
                </div>

                {{-- Move Status Buttons --}}
                <div class="border-t border-gray-100 pt-4">
                    <p class="text-xs text-gray-500 mb-2">Pindah ke:</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($statuses as $status)
                            @if($status->id !== $selectedTask->status_id)
                                <button wire:click="moveTask({{ $selectedTask->id }}, {{ $status->id }}); closeTaskModal()" 
                                        class="px-3 py-1.5 rounded-lg text-xs font-medium border transition hover:shadow-sm"
                                        style="border-color: {{ $status->color }}; color: {{ $status->color }};">
                                    {{ $status->name }}
                                </button>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="p-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
                @if(!$selectedTask->assigned_to || $selectedTask->assigned_to !== auth()->id())
                    <button wire:click="assignTaskToMe({{ $selectedTask->id }})" 
                            class="px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white text-sm font-medium rounded-lg transition">
                        <i class="fas fa-user-plus mr-1"></i> Assign ke Saya
                    </button>
                @else
                    <span class="text-sm text-gray-500">Task ini sudah diassign ke Anda</span>
                @endif
                <a href="{{ route('staff.task.edit', $selectedTask->id) }}" 
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                    <i class="fas fa-edit mr-1"></i> Edit Lengkap
                </a>
            </div>
        </div>
    </div>
    @endif
</div>
