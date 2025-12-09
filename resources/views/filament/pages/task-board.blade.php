<x-filament-panels::page>
    <div class="space-y-4">
        {{-- Filter --}}
        <div class="flex items-center gap-4">
            <div class="w-64">
                <select wire:model.live="projectId" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm">
                    <option value="">Semua Proyek</option>
                    @foreach(\App\Models\Project::where('status', 'active')->get() as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>
            <a href="{{ \App\Filament\Resources\TaskResource::getUrl('create') }}" class="fi-btn fi-btn-size-md fi-btn-color-primary">
                <x-heroicon-o-plus class="w-4 h-4 mr-1" />
                Task Baru
            </a>
        </div>

        {{-- Kanban Board --}}
        <div class="flex gap-4 overflow-x-auto pb-4" x-data="kanban()">
            @foreach($statuses as $status)
                <div class="flex-shrink-0 w-72 bg-gray-100 dark:bg-gray-800 rounded-xl p-3"
                     x-on:drop="onDrop($event, {{ $status['id'] }})"
                     x-on:dragover.prevent
                     x-on:dragenter.prevent>
                    {{-- Column Header --}}
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full" style="background-color: {{ $status['color'] }}"></span>
                            <h3 class="font-semibold text-sm text-gray-700 dark:text-gray-300">{{ $status['name'] }}</h3>
                        </div>
                        <span class="text-xs bg-gray-200 dark:bg-gray-700 px-2 py-1 rounded-full">
                            {{ count($tasks[$status['id']] ?? []) }}
                        </span>
                    </div>

                    {{-- Tasks --}}
                    <div class="space-y-2 min-h-[200px]">
                        @foreach($tasks[$status['id']] ?? [] as $task)
                            <div class="bg-white dark:bg-gray-900 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700 cursor-move hover:shadow-md transition-shadow"
                                 draggable="true"
                                 x-on:dragstart="onDragStart($event, {{ $task['id'] }})"
                                 x-on:dragend="onDragEnd($event)">
                                {{-- Task Type & ID --}}
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs text-gray-500">
                                        @switch($task['type'])
                                            @case('bug') 🐛 @break
                                            @case('feature') ✨ @break
                                            @case('improvement') 📈 @break
                                            @default 📋
                                        @endswitch
                                        #{{ $task['id'] }}
                                    </span>
                                    <span class="text-xs px-2 py-0.5 rounded-full
                                        @switch($task['priority'])
                                            @case('urgent') bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300 @break
                                            @case('high') bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300 @break
                                            @case('medium') bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300 @break
                                            @default bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300
                                        @endswitch">
                                        {{ ucfirst($task['priority']) }}
                                    </span>
                                </div>

                                {{-- Title --}}
                                <a href="{{ \App\Filament\Resources\TaskResource::getUrl('view', ['record' => $task['id']]) }}"
                                   class="font-medium text-sm text-gray-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400 line-clamp-2">
                                    {{ $task['title'] }}
                                </a>

                                {{-- Project --}}
                                @if($task['project'])
                                    <p class="text-xs text-gray-500 mt-1">{{ $task['project']['name'] }}</p>
                                @endif

                                {{-- Footer --}}
                                <div class="flex items-center justify-between mt-3 pt-2 border-t border-gray-100 dark:border-gray-800">
                                    {{-- Assignee --}}
                                    <div class="flex items-center gap-1">
                                        @if($task['assignee'])
                                            <div class="w-6 h-6 rounded-full bg-primary-500 flex items-center justify-center text-white text-xs">
                                                {{ strtoupper(substr($task['assignee']['name'], 0, 1)) }}
                                            </div>
                                            <span class="text-xs text-gray-600 dark:text-gray-400">{{ $task['assignee']['name'] }}</span>
                                        @else
                                            <span class="text-xs text-gray-400">Unassigned</span>
                                        @endif
                                    </div>

                                    {{-- Due Date --}}
                                    @if($task['due_date'])
                                        @php
                                            $dueDate = \Carbon\Carbon::parse($task['due_date']);
                                            $isOverdue = $dueDate->isPast() && !($task['status']['is_done'] ?? false);
                                        @endphp
                                        <span class="text-xs {{ $isOverdue ? 'text-red-600 dark:text-red-400' : 'text-gray-500' }}">
                                            @if($isOverdue)
                                                <x-heroicon-o-exclamation-triangle class="w-3 h-3 inline" />
                                            @endif
                                            {{ $dueDate->format('d M') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        function kanban() {
            return {
                draggedTaskId: null,
                onDragStart(event, taskId) {
                    this.draggedTaskId = taskId;
                    event.target.classList.add('opacity-50');
                },
                onDragEnd(event) {
                    event.target.classList.remove('opacity-50');
                },
                onDrop(event, statusId) {
                    if (this.draggedTaskId) {
                        @this.call('moveTask', this.draggedTaskId, statusId);
                        this.draggedTaskId = null;
                    }
                }
            }
        }
    </script>
</x-filament-panels::page>
