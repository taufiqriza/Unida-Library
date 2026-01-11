{{-- Unified Task Module Header - Used across Kanban, Timeline, Jadwal, Notes --}}
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
        {{-- Unified 4-Tab Navigation with wire:navigate for seamless transitions --}}
        <div class="bg-white rounded-xl p-1 border border-gray-200 flex shadow-sm">
            <a href="{{ route('staff.task.index') }}" wire:navigate
               class="px-3 py-2 text-sm font-medium rounded-lg transition flex items-center gap-2 {{ $activeTab === 'kanban' ? 'bg-gradient-to-r from-violet-600 to-purple-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas fa-columns"></i>
                <span class="hidden sm:inline">Kanban</span>
            </a>
            <a href="{{ route('staff.task.timeline') }}" wire:navigate
               class="px-3 py-2 text-sm font-medium rounded-lg transition flex items-center gap-2 {{ $activeTab === 'timeline' ? 'bg-gradient-to-r from-violet-600 to-purple-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas fa-chart-gantt"></i>
                <span class="hidden sm:inline">Timeline</span>
            </a>
            <a href="{{ route('staff.task.schedule') }}" wire:navigate
               class="px-3 py-2 text-sm font-medium rounded-lg transition flex items-center gap-2 {{ $activeTab === 'schedule' ? 'bg-gradient-to-r from-violet-600 to-purple-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas fa-calendar-alt"></i>
                <span class="hidden sm:inline">Jadwal</span>
            </a>
            <a href="{{ route('staff.task.notes') }}" wire:navigate
               class="px-3 py-2 text-sm font-medium rounded-lg transition flex items-center gap-2 {{ $activeTab === 'notes' ? 'bg-gradient-to-r from-violet-600 to-purple-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas fa-sticky-note"></i>
                <span class="hidden sm:inline">Notes</span>
            </a>
        </div>
        
        {{ $actions ?? '' }}
    </div>
</div>
