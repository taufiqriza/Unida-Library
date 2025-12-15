@section('title', 'Pusat Notifikasi')

<div class="space-y-5">
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-gradient-to-br from-violet-500 via-purple-500 to-fuchsia-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-violet-500/25">
                <i class="fas fa-bell text-xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Pusat Notifikasi</h1>
                <p class="text-sm text-gray-500">{{ $stats['unread'] }} belum dibaca dari {{ $stats['total'] }} total</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if($stats['unread'] > 0)
                <button wire:click="markAllAsRead" 
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-violet-100 hover:bg-violet-200 text-violet-700 font-medium rounded-xl transition text-sm">
                    <i class="fas fa-check-double"></i>
                    <span>Tandai Semua Dibaca</span>
                </button>
            @endif
            <a href="{{ route('staff.notification.settings') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition text-sm">
                <i class="fas fa-cog"></i>
                <span>Pengaturan</span>
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    <p class="text-xs text-gray-500 font-medium">Total Notifikasi</p>
                </div>
                <div class="w-10 h-10 bg-violet-100 rounded-xl flex items-center justify-center text-violet-600">
                    <i class="fas fa-bell"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-2xl font-bold text-violet-600">{{ $stats['unread'] }}</p>
                    <p class="text-xs text-gray-500 font-medium">Belum Dibaca</p>
                </div>
                <div class="w-10 h-10 bg-violet-100 rounded-xl flex items-center justify-center text-violet-600">
                    <i class="fas fa-envelope"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-2xl font-bold text-emerald-600">{{ $stats['today'] }}</p>
                    <p class="text-xs text-gray-500 font-medium">Hari Ini</p>
                </div>
                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-600">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            {{-- Status Filter --}}
            <div class="flex items-center gap-2">
                @foreach(['all' => 'Semua', 'unread' => 'Belum Dibaca', 'read' => 'Sudah Dibaca'] as $value => $label)
                    <button wire:click="setFilter('{{ $value }}')" 
                            class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $filter === $value ? 'bg-violet-600 text-white shadow-lg shadow-violet-500/25' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            {{-- Category Filter --}}
            <div class="flex items-center gap-2">
                <button wire:click="setCategory('')" 
                        class="px-3 py-2 rounded-lg text-sm font-medium transition {{ $category === '' ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Semua Kategori
                </button>
                @foreach($categories as $key => $cat)
                    <button wire:click="setCategory('{{ $key }}')" 
                            class="px-3 py-2 rounded-lg text-sm font-medium transition flex items-center gap-1.5 {{ $category === $key ? 'bg-' . $cat['color'] . '-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        <i class="fas {{ $cat['icon'] }} text-xs"></i>
                        <span>{{ $cat['label'] }}</span>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Search --}}
        <div class="mt-4">
            <div class="relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" wire:model.live.debounce.300ms="search" 
                       placeholder="Cari notifikasi..." 
                       class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500/20 focus:border-violet-400 text-sm">
            </div>
        </div>
    </div>

    {{-- Notification List --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        @forelse($notifications as $notification)
            <div wire:key="notif-{{ $notification->id }}"
                 class="relative flex gap-4 p-4 border-b border-gray-50 hover:bg-gray-50 transition-all group {{ !$notification->isRead() ? 'bg-violet-50/30' : '' }}">
                
                {{-- Unread Indicator --}}
                @if(!$notification->isRead())
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-violet-500 to-purple-500"></div>
                @endif
                
                {{-- Icon --}}
                <div class="flex-shrink-0 w-12 h-12 rounded-xl flex items-center justify-center {{ $notification->getColorClass() }}">
                    <i class="fas {{ $notification->getIconClass() }} text-lg"></i>
                </div>
                
                {{-- Content --}}
                <div class="flex-1 min-w-0 cursor-pointer" wire:click="clickNotification('{{ $notification->id }}')">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="font-semibold text-gray-900 {{ !$notification->isRead() ? 'font-bold' : '' }}">
                                {{ $notification->title }}
                            </p>
                            <p class="text-sm text-gray-500 mt-0.5">{{ $notification->body }}</p>
                            
                            @if($notification->action_label)
                                <span class="inline-flex items-center gap-1 text-xs text-violet-600 font-semibold mt-2 hover:underline">
                                    {{ $notification->action_label }}
                                    <i class="fas fa-arrow-right text-[10px]"></i>
                                </span>
                            @endif
                        </div>
                        
                        <div class="text-right flex-shrink-0">
                            <p class="text-xs text-gray-400">{{ $notification->getTimeAgo() }}</p>
                            <span class="inline-block mt-1 px-2 py-0.5 bg-{{ $categories[$notification->category]['color'] ?? 'gray' }}-100 text-{{ $categories[$notification->category]['color'] ?? 'gray' }}-700 rounded text-[10px] font-semibold">
                                {{ $notification->getCategoryLabel() }}
                            </span>
                        </div>
                    </div>
                </div>
                
                {{-- Actions --}}
                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition">
                    @if(!$notification->isRead())
                        <button wire:click="markAsRead('{{ $notification->id }}')" 
                                class="w-8 h-8 rounded-lg bg-green-100 hover:bg-green-200 text-green-600 flex items-center justify-center transition" 
                                title="Tandai Dibaca">
                            <i class="fas fa-check text-xs"></i>
                        </button>
                    @endif
                    <button wire:click="deleteNotification('{{ $notification->id }}')" 
                            class="w-8 h-8 rounded-lg bg-red-100 hover:bg-red-200 text-red-600 flex items-center justify-center transition" 
                            title="Hapus">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="p-12 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-bell-slash text-gray-300 text-3xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Tidak Ada Notifikasi</h3>
                <p class="text-gray-500 text-sm">
                    @if($filter !== 'all' || $category || $search)
                        Tidak ada notifikasi yang sesuai dengan filter.
                    @else
                        Anda belum memiliki notifikasi apapun.
                    @endif
                </p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($notifications->hasPages())
        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
