<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" 
            class="relative w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300"
            :class="$store.darkMode ? 'bg-slate-700 hover:bg-slate-600 text-emerald-400' : 'bg-slate-100 hover:bg-slate-200 text-emerald-600'"
            title="Staff Online">
        <i class="fas fa-users text-lg"></i>
        @if($this->onlineCount > 0)
        <span class="absolute -top-1 -right-1 w-5 h-5 bg-emerald-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">
            {{ $this->onlineCount }}
        </span>
        @endif
    </button>

    {{-- Dropdown --}}
    <div x-show="open" x-cloak @click.away="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="absolute right-0 mt-2 w-72 rounded-xl shadow-xl border overflow-hidden z-50"
         :class="$store.darkMode ? 'bg-slate-800 border-slate-700' : 'bg-white border-slate-200'">
        
        <div class="px-4 py-3 border-b" :class="$store.darkMode ? 'border-slate-700' : 'border-slate-100'">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold text-sm" :class="$store.darkMode ? 'text-slate-100' : 'text-slate-900'">
                    <i class="fas fa-circle text-emerald-500 text-[8px] mr-2"></i>Status Staff
                </h3>
                <span class="text-xs px-2 py-0.5 rounded-full" :class="$store.darkMode ? 'bg-emerald-500/20 text-emerald-400' : 'bg-emerald-100 text-emerald-700'">
                    {{ $this->onlineCount }} online
                </span>
            </div>
        </div>

        <div class="max-h-80 overflow-y-auto">
            @forelse($this->onlineStaff as $staff)
            <div class="px-4 py-2.5 flex items-center gap-3 border-b last:border-0" :class="$store.darkMode ? 'border-slate-700 hover:bg-slate-700/50' : 'border-slate-50 hover:bg-slate-50'">
                <div class="relative">
                    <div class="w-9 h-9 rounded-full overflow-hidden border-2" :class="$store.darkMode ? 'border-slate-600' : 'border-slate-200'">
                        <img src="{{ $staff->getAvatarUrl(50) }}" class="w-full h-full object-cover">
                    </div>
                    <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2 {{ $staff->isReallyOnline() ? 'bg-emerald-500' : 'bg-slate-400' }}" :class="$store.darkMode ? 'border-slate-800' : 'border-white'"></span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium truncate" :class="$store.darkMode ? 'text-slate-200' : 'text-slate-900'">{{ $staff->name }}</p>
                    <p class="text-[10px] truncate" :class="$store.darkMode ? 'text-slate-400' : 'text-slate-500'">
                        <span class="{{ $staff->role === 'super_admin' ? 'text-red-500' : ($staff->role === 'admin' ? 'text-amber-600' : 'text-blue-500') }}">{{ $staff->role === 'super_admin' ? 'SA' : ($staff->role === 'admin' ? 'Admin' : 'Staff') }}</span>
                        @if($staff->branch)<span class="mx-1">·</span>{{ $staff->branch->name }}@endif
                        <span class="mx-1">·</span>
                        <span class="{{ $staff->isReallyOnline() ? 'text-emerald-500' : '' }}">{{ $staff->getOnlineStatusText() }}</span>
                    </p>
                </div>
            </div>
            @empty
            <div class="px-4 py-8 text-center">
                <i class="fas fa-user-slash text-2xl mb-2" :class="$store.darkMode ? 'text-slate-600' : 'text-slate-300'"></i>
                <p class="text-sm" :class="$store.darkMode ? 'text-slate-500' : 'text-slate-400'">Tidak ada staff</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
