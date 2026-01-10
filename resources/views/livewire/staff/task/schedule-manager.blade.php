@section('title', 'Jadwal Staff')

<div class="space-y-4">
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 via-teal-500 to-cyan-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-emerald-500/30">
                <i class="fas fa-calendar-alt text-2xl"></i>
            </div>
            <div>
                <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Tugas & Jadwal</h1>
                <p class="text-sm text-gray-500 flex items-center gap-2">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                    Manajemen piket & penugasan
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            {{-- Unified 3-Tab Navigation --}}
            <div class="bg-white rounded-xl p-1 border border-gray-200 flex shadow-sm">
                <a href="{{ route('staff.task.index') }}" 
                   class="px-3 py-2 text-sm font-medium rounded-lg transition flex items-center gap-2 text-gray-600 hover:bg-gray-100">
                    <i class="fas fa-columns"></i>
                    <span class="hidden sm:inline">Kanban</span>
                </a>
                <a href="{{ route('staff.task.index') }}?view=timeline" 
                   class="px-3 py-2 text-sm font-medium rounded-lg transition flex items-center gap-2 text-gray-600 hover:bg-gray-100">
                    <i class="fas fa-stream"></i>
                    <span class="hidden sm:inline">Timeline</span>
                </a>
                <span class="px-3 py-2 text-sm font-medium rounded-lg bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-sm flex items-center gap-2">
                    <i class="fas fa-calendar-alt"></i>
                    <span class="hidden sm:inline">Jadwal</span>
                </span>
            </div>
            
            {{-- View Toggle (Week/Month) --}}
            <div class="bg-white rounded-xl p-1 border border-gray-200 flex shadow-sm">
                <button wire:click="$set('viewMode', 'week')" 
                        class="px-3 py-1.5 text-sm font-medium rounded-lg transition {{ $viewMode === 'week' ? 'bg-emerald-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="fas fa-calendar-week mr-1"></i>Minggu
                </button>
                <button wire:click="$set('viewMode', 'month')" 
                        class="px-3 py-1.5 text-sm font-medium rounded-lg transition {{ $viewMode === 'month' ? 'bg-emerald-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="fas fa-calendar mr-1"></i>Bulan
                </button>
            </div>
            
            @if($isAdmin)
            <button wire:click="openCreateModal()" 
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-medium rounded-xl shadow-lg shadow-emerald-500/25 transition text-sm">
                <i class="fas fa-plus"></i>
                <span class="hidden sm:inline">Buat Jadwal</span>
            </button>
            @endif
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl px-4 py-3 text-white flex items-center justify-between">
            <div class="w-9 h-9 bg-white/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-calendar-check text-sm"></i>
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold">{{ $stats['total_this_period'] }}</p>
                <p class="text-[10px] text-emerald-100 uppercase tracking-wide">Periode Ini</p>
            </div>
        </div>
        <div class="bg-white rounded-xl px-4 py-3 border border-gray-100 shadow-sm flex items-center justify-between">
            <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center text-blue-500">
                <i class="fas fa-user text-sm"></i>
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold text-gray-900">{{ $stats['my_schedules'] }}</p>
                <p class="text-[10px] text-gray-500 uppercase tracking-wide">Jadwal Saya</p>
            </div>
        </div>
        <div class="bg-white rounded-xl px-4 py-3 border border-gray-100 shadow-sm flex items-center justify-between">
            <div class="w-9 h-9 bg-amber-100 rounded-lg flex items-center justify-center text-amber-500">
                <i class="fas fa-clock text-sm"></i>
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold text-gray-900">{{ $stats['today_schedules'] }}</p>
                <p class="text-[10px] text-gray-500 uppercase tracking-wide">Hari Ini</p>
            </div>
        </div>
        <div class="bg-white rounded-xl px-4 py-3 border border-gray-100 shadow-sm flex items-center justify-between">
            <div class="w-9 h-9 bg-violet-100 rounded-lg flex items-center justify-center text-violet-500">
                <i class="fas fa-exchange-alt text-sm"></i>
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_swaps'] }}</p>
                <p class="text-[10px] text-gray-500 uppercase tracking-wide">Tukar Jadwal</p>
            </div>
        </div>
    </div>

    {{-- Filters & Navigation --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                {{-- Navigation --}}
                <div class="flex items-center gap-1">
                    <button wire:click="previousPeriod" class="w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded-lg flex items-center justify-center transition">
                        <i class="fas fa-chevron-left text-gray-600 text-sm"></i>
                    </button>
                    <button wire:click="goToday" class="px-3 py-1.5 text-sm font-medium text-emerald-600 hover:bg-emerald-50 rounded-lg transition">
                        Hari Ini
                    </button>
                    <button wire:click="nextPeriod" class="w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded-lg flex items-center justify-center transition">
                        <i class="fas fa-chevron-right text-gray-600 text-sm"></i>
                    </button>
                </div>
                
                <div class="h-6 w-px bg-gray-200"></div>
                
                <h2 class="font-bold text-gray-900">
                    @if($viewMode === 'week')
                        {{ $startDate->format('d M') }} - {{ $endDate->format('d M Y') }}
                    @else
                        {{ $currentDate->locale('id')->isoFormat('MMMM Y') }}
                    @endif
                </h2>
            </div>
            
            <div class="flex items-center gap-2">
                @if($isSuperAdmin)
                <select wire:model.live="filterBranch" class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm">
                    <option value="">Semua Cabang</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
                @endif
                
                <select wire:model.live="filterUser" class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm">
                    <option value="">Semua Staff</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
                
                <select wire:model.live="filterType" class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm">
                    <option value="">Semua Tipe</option>
                    @foreach($types as $key => $type)
                        <option value="{{ $key }}">{{ $type['label'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Calendar Grid --}}
    @if($viewMode === 'week')
        {{-- Week View --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            {{-- Days Header --}}
            <div class="grid grid-cols-7 border-b border-gray-100">
                @foreach(['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $index => $day)
                    <div class="px-3 py-2 text-center border-r border-gray-100 last:border-r-0 {{ $index >= 5 ? 'bg-gray-50' : '' }}">
                        <span class="text-xs font-semibold text-gray-500 uppercase">{{ $day }}</span>
                    </div>
                @endforeach
            </div>
            
            {{-- Week Days --}}
            <div class="grid grid-cols-7 min-h-[400px]">
                @foreach($calendarDays as $day)
                    <div class="border-r border-b border-gray-100 last:border-r-0 p-2 {{ !$day['isCurrentMonth'] ? 'bg-gray-50/50' : '' }} {{ $day['isToday'] ? 'bg-emerald-50' : '' }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-bold {{ $day['isToday'] ? 'w-7 h-7 bg-emerald-600 text-white rounded-full flex items-center justify-center' : 'text-gray-900' }}">
                                {{ $day['date']->format('d') }}
                            </span>
                            @if($isAdmin)
                            <button wire:click="openCreateModal('{{ $day['date']->format('Y-m-d') }}')" 
                                    class="w-5 h-5 text-gray-300 hover:text-emerald-600 hover:bg-emerald-50 rounded transition flex items-center justify-center opacity-0 group-hover:opacity-100">
                                <i class="fas fa-plus text-xs"></i>
                            </button>
                            @endif
                        </div>
                        
                        <div class="space-y-1 max-h-[200px] overflow-y-auto">
                            @foreach($day['schedules'] as $schedule)
                                @php $typeInfo = $schedule->getTypeInfo(); @endphp
                                <div wire:click="openEditModal({{ $schedule->id }})" 
                                     class="px-2 py-1.5 rounded-lg text-xs cursor-pointer hover:opacity-80 transition border-l-2"
                                     style="background-color: {{ $this->getColorBg($typeInfo['color']) }}; border-color: {{ $this->getColorBorder($typeInfo['color']) }}">
                                    <div class="flex items-center gap-1">
                                        <i class="fas {{ $typeInfo['icon'] }} text-[10px]" style="color: {{ $this->getColorText($typeInfo['color']) }}"></i>
                                        <span class="font-medium truncate text-gray-800">{{ Str::limit($schedule->title, 15) }}</span>
                                    </div>
                                    @if($schedule->user)
                                    <p class="text-[10px] text-gray-500 truncate mt-0.5">
                                        <i class="fas fa-user mr-0.5"></i>{{ $schedule->user->name }}
                                        @if($schedule->getTimeRange())
                                            • {{ $schedule->getTimeRange() }}
                                        @endif
                                    </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        {{-- Month View --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            {{-- Days Header --}}
            <div class="grid grid-cols-7 border-b border-gray-100">
                @foreach(['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $index => $day)
                    <div class="px-3 py-2 text-center border-r border-gray-100 last:border-r-0 {{ $index >= 5 ? 'bg-gray-50' : '' }}">
                        <span class="text-xs font-semibold text-gray-500 uppercase">{{ $day }}</span>
                    </div>
                @endforeach
            </div>
            
            {{-- Calendar Grid --}}
            <div class="grid grid-cols-7">
                {{-- Padding for first week --}}
                @php
                    $firstDayOfMonth = $currentDate->copy()->startOfMonth();
                    $startPadding = $firstDayOfMonth->dayOfWeekIso - 1;
                @endphp
                @for($i = 0; $i < $startPadding; $i++)
                    <div class="border-r border-b border-gray-100 bg-gray-50/30 min-h-[80px]"></div>
                @endfor
                
                @foreach($calendarDays as $day)
                    @if($day['isCurrentMonth'])
                    <div class="border-r border-b border-gray-100 p-1.5 min-h-[80px] group {{ $day['isToday'] ? 'bg-emerald-50' : '' }}" wire:key="day-{{ $day['date']->format('Y-m-d') }}">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold {{ $day['isToday'] ? 'w-5 h-5 bg-emerald-600 text-white rounded-full flex items-center justify-center' : 'text-gray-700' }}">
                                {{ $day['date']->format('d') }}
                            </span>
                            @if($day['schedules']->count() > 0)
                                <span class="px-1 py-0.5 bg-gray-100 text-gray-500 text-[9px] rounded font-medium">
                                    {{ $day['schedules']->count() }}
                                </span>
                            @endif
                        </div>
                        
                        <div class="space-y-0.5">
                            @foreach($day['schedules']->take(3) as $schedule)
                                @php $typeInfo = $schedule->getTypeInfo(); @endphp
                                <div wire:click="openEditModal({{ $schedule->id }})" 
                                     class="px-1 py-0.5 rounded text-[9px] cursor-pointer hover:opacity-80 transition truncate"
                                     style="background-color: {{ $this->getColorBg($typeInfo['color']) }}; color: {{ $this->getColorText($typeInfo['color']) }}">
                                    <i class="fas {{ $typeInfo['icon'] }} mr-0.5"></i>{{ Str::limit($schedule->title, 10) }}
                                </div>
                            @endforeach
                            @if($day['schedules']->count() > 3)
                                <p class="text-[9px] text-gray-400 text-center">+{{ $day['schedules']->count() - 3 }} lagi</p>
                            @endif
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    {{-- Type Legend --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-3"><i class="fas fa-palette mr-1"></i>Tipe Jadwal</h3>
        <div class="flex flex-wrap gap-3">
            @foreach($types as $key => $type)
                <div class="flex items-center gap-2 text-sm">
                    <div class="w-6 h-6 rounded flex items-center justify-center" style="background-color: {{ $this->getColorBg($type['color']) }}">
                        <i class="fas {{ $type['icon'] }} text-xs" style="color: {{ $this->getColorText($type['color']) }}"></i>
                    </div>
                    <span class="text-gray-600">{{ $type['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Create/Edit Modal - Teleported to body for proper z-index --}}
    <template x-teleport="body">
        <div style="position: relative; z-index: 9999;">
            @if($showModal)
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100">
                <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto"
                     @click.away="$wire.closeModal()">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-emerald-50 to-teal-50">
                <h2 class="text-lg font-bold text-gray-900">
                    <i class="fas {{ $editingId ? 'fa-edit' : 'fa-plus-circle' }} text-emerald-600 mr-2"></i>
                    {{ $editingId ? 'Edit Jadwal' : 'Buat Jadwal Baru' }}
                </h2>
                <button wire:click="closeModal" class="w-8 h-8 text-gray-400 hover:text-gray-600 hover:bg-white rounded-lg transition flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form wire:submit="save" class="p-5 space-y-4">
                {{-- Staff --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Staff <span class="text-red-500">*</span></label>
                    <select wire:model="form.user_id" class="w-full px-3 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-400">
                        <option value="">Pilih staff...</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    @error('form.user_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                
                {{-- Title --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Judul <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="form.title" class="w-full px-3 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-400" placeholder="Contoh: Piket Sirkulasi">
                    @error('form.title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                
                {{-- Type & Location --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Tipe <span class="text-red-500">*</span></label>
                        <select wire:model="form.type" class="w-full px-3 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-400">
                            @foreach($types as $key => $type)
                                <option value="{{ $key }}">{{ $type['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Lokasi</label>
                        <select wire:model="form.location" class="w-full px-3 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-400">
                            <option value="">Tidak spesifik...</option>
                            @foreach($locations as $key => $loc)
                                <option value="{{ $key }}">{{ $loc['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                {{-- Date & Shift --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal <span class="text-red-500">*</span></label>
                        <input type="date" wire:model="form.schedule_date" class="w-full px-3 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-400">
                        @error('form.schedule_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Shift</label>
                        <select wire:model="form.shift" class="w-full px-3 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-400">
                            <option value="">Pilih shift...</option>
                            @foreach($shifts as $key => $shift)
                                <option value="{{ $key }}">{{ $shift['label'] }} ({{ $shift['time'] }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                {{-- Custom Time (if no shift) --}}
                <div class="grid grid-cols-2 gap-3" x-show="!$wire.form.shift" x-cloak>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Jam Mulai</label>
                        <input type="time" wire:model="form.start_time" class="w-full px-3 py-2 border border-gray-200 rounded-xl">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Jam Selesai</label>
                        <input type="time" wire:model="form.end_time" class="w-full px-3 py-2 border border-gray-200 rounded-xl">
                    </div>
                </div>
                
                {{-- Recurring --}}
                <div class="bg-gray-50 rounded-xl p-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="form.is_recurring" class="w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                        <span class="text-sm font-medium text-gray-700"><i class="fas fa-repeat mr-1"></i>Jadwal Berulang</span>
                    </label>
                    
                    @if($form['is_recurring'])
                    <div class="mt-3 space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Pola Pengulangan</label>
                            <select wire:model="form.recurrence_pattern" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                <option value="daily">Setiap Hari</option>
                                <option value="weekly">Setiap Minggu</option>
                                <option value="biweekly">Setiap 2 Minggu</option>
                                <option value="monthly">Setiap Bulan</option>
                            </select>
                        </div>
                        
                        @if(in_array($form['recurrence_pattern'], ['weekly', 'biweekly']))
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Hari</label>
                            <div class="flex gap-1">
                                @foreach(['1' => 'Sen', '2' => 'Sel', '3' => 'Rab', '4' => 'Kam', '5' => 'Jum', '6' => 'Sab', '7' => 'Min'] as $val => $label)
                                    <label class="flex-1">
                                        <input type="checkbox" wire:model="form.recurrence_days" value="{{ $val }}" class="hidden peer">
                                        <div class="text-center py-1.5 text-xs font-medium rounded border border-gray-200 cursor-pointer peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:border-emerald-600 transition">
                                            {{ $label }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Berakhir Pada</label>
                            <input type="date" wire:model="form.recurrence_end_date" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                        </div>
                    </div>
                    @endif
                </div>
                
                {{-- Notes --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Catatan</label>
                    <textarea wire:model="form.notes" rows="2" class="w-full px-3 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-400" placeholder="Catatan tambahan..."></textarea>
                </div>
                
                {{-- Actions --}}
                <div class="flex gap-3 pt-3 border-t border-gray-100">
                    @if($editingId)
                    <button type="button" wire:click="deleteSchedule({{ $editingId }})" wire:confirm="Hapus jadwal ini?" class="px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 font-medium rounded-xl transition">
                        <i class="fas fa-trash mr-1"></i>Hapus
                    </button>
                    @endif
                    <div class="flex-1"></div>
                    <button type="button" wire:click="closeModal" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-medium rounded-xl shadow-lg shadow-emerald-500/25 transition">
                        <i class="fas fa-check mr-1"></i>{{ $editingId ? 'Simpan' : 'Buat Jadwal' }}
                    </button>
                </div>
            </form>
                </div>
            </div>
            @endif
        </div>
    </template>
</div>

