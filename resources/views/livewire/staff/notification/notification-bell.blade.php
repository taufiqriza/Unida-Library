<div class="relative" x-data @click.outside="$wire.closeDropdown()" wire:poll.10s="updateCount">
    {{-- Bell Button --}}
    <button wire:click="toggleDropdown" 
            type="button"
            class="relative flex items-center justify-center w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition-all duration-200 group">
        <i class="fas fa-bell text-lg group-hover:scale-110 transition-transform"></i>
        
        {{-- Badge Counter --}}
        @if($unreadCount > 0)
            <span class="absolute -top-1 -right-1 flex items-center justify-center min-w-[20px] h-5 px-1.5 text-[10px] font-bold text-white bg-gradient-to-r from-red-500 to-pink-500 rounded-full shadow-lg animate-pulse">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown --}}
    @if($showDropdown)
    <div class="absolute right-0 mt-2 w-96 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden z-50"
         style="max-height: 80vh;"
         @click.stop>
        
        {{-- Header --}}
        <div class="px-4 py-3 bg-gradient-to-r from-violet-600 to-purple-600 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fas fa-bell"></i>
                    <h3 class="font-bold">Notifikasi</h3>
                    @if($unreadCount > 0)
                        <span class="px-2 py-0.5 bg-white/20 rounded-full text-xs font-semibold">
                            {{ $unreadCount }} baru
                        </span>
                    @endif
                </div>
                @if($unreadCount > 0)
                    <button wire:click="markAllAsRead" 
                            class="text-xs px-2 py-1 bg-white/20 hover:bg-white/30 rounded-lg transition">
                        <i class="fas fa-check-double mr-1"></i>Tandai Dibaca
                    </button>
                @endif
            </div>
        </div>

        {{-- Notification List --}}
        <div class="overflow-y-auto" style="max-height: 400px;">
            @forelse($notifications as $notification)
                <div wire:key="notif-{{ $notification->id }}"
                     wire:click="clickNotification('{{ $notification->id }}')"
                     class="relative flex gap-3 p-4 border-b border-gray-50 hover:bg-gray-50 cursor-pointer transition-all group {{ !$notification->isRead() ? 'bg-violet-50/50' : '' }}">
                    
                    {{-- Unread Indicator --}}
                    @if(!$notification->isRead())
                        <div class="absolute left-1 top-1/2 -translate-y-1/2 w-2 h-2 rounded-full bg-violet-500"></div>
                    @endif
                    
                    {{-- Icon --}}
                    <div class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center {{ $notification->getColorClass() }}">
                        <i class="fas {{ $notification->getIconClass() }}"></i>
                    </div>
                    
                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <p class="font-semibold text-gray-900 text-sm truncate {{ !$notification->isRead() ? 'font-bold' : '' }}">
                                {{ $notification->title }}
                            </p>
                            <span class="text-[10px] text-gray-400 whitespace-nowrap">{{ $notification->getTimeAgo() }}</span>
                        </div>
                        <p class="text-xs text-gray-500 line-clamp-2 mt-0.5">{{ $notification->body }}</p>
                        
                        {{-- Action Label --}}
                        @if($notification->action_label)
                            <span class="inline-flex items-center gap-1 text-[10px] text-violet-600 font-medium mt-1">
                                {{ $notification->action_label }}
                                <i class="fas fa-arrow-right text-[8px]"></i>
                            </span>
                        @endif
                    </div>
                    
                    {{-- Dismiss Button --}}
                    <button wire:click.stop="dismissNotification('{{ $notification->id }}')" 
                            class="opacity-0 group-hover:opacity-100 text-gray-400 hover:text-red-500 transition p-1">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
            @empty
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-bell-slash text-gray-300 text-2xl"></i>
                    </div>
                    <p class="text-gray-500 font-medium">Tidak ada notifikasi</p>
                    <p class="text-gray-400 text-xs mt-1">Semua sudah tertangani! 🎉</p>
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        <div class="px-4 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
            <a href="{{ route('staff.notification.index') }}" 
               wire:navigate
               class="flex items-center gap-2 text-sm text-violet-600 hover:text-violet-700 font-semibold transition">
                <span>Lihat Semua</span>
                <i class="fas fa-arrow-right text-xs"></i>
            </a>
            <a href="{{ route('staff.notification.settings') }}" 
               wire:navigate
               class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-200 rounded-lg transition"
               title="Pengaturan Notifikasi">
                <i class="fas fa-cog"></i>
            </a>
        </div>
    </div>
    @endif
</div>
