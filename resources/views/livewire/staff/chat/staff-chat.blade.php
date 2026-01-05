<div wire:poll.5s="refreshMessages" class="staff-chat-widget" 
     x-data="{ 
        sending: false, 
        deleteId: null,
        showDeleteModal: false 
     }"
     @confirm-delete.window="deleteId = $event.detail.id; showDeleteModal = true">
    
    {{-- Delete Confirmation Modal --}}
    <div x-show="showDeleteModal" x-cloak
         class="fixed inset-0 z-[10000] flex items-center justify-center bg-black/50"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="bg-white rounded-2xl shadow-2xl p-6 w-80 mx-4" @click.away="showDeleteModal = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <div class="text-center">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-trash text-red-500 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Hapus Pesan?</h3>
                <p class="text-sm text-gray-500 mb-6">Pesan yang dihapus tidak dapat dikembalikan.</p>
                <div class="flex gap-3">
                    <button @click="showDeleteModal = false" class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium transition">
                        Batal
                    </button>
                    <button @click="$wire.deleteMessage(deleteId); showDeleteModal = false" class="flex-1 px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl font-medium transition">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Forward Modal --}}
    @if($showForwardModal && $forwardingMessage)
    <div class="fixed inset-0 z-[10000] flex items-center justify-center bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-[420px] max-w-[95vw] mx-4 overflow-hidden" @click.away="$wire.cancelForward()">
            {{-- Header --}}
            <div class="px-5 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                            <i class="fas fa-share text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg">Teruskan Pesan</h3>
                            <p class="text-xs text-white/70">Pilih tujuan pengiriman</p>
                        </div>
                    </div>
                    <button wire:click="cancelForward" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-white/20 transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            {{-- Message Preview --}}
            <div class="px-5 py-3 bg-gray-50 border-b">
                <p class="text-[10px] text-gray-400 uppercase tracking-wide mb-2">Pesan yang diteruskan</p>
                <div class="flex items-start gap-3">
                    @if($forwardingMessage->sender && $forwardingMessage->sender->photo)
                        <img src="{{ asset('storage/' . $forwardingMessage->sender->photo) }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                    @else
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                            {{ strtoupper(substr($forwardingMessage->sender->name ?? 'U', 0, 1)) }}
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-700">{{ $forwardingMessage->sender->name ?? 'Unknown' }}</p>
                        <p class="text-sm text-gray-600 line-clamp-2">{{ $forwardingMessage->message ?: ($forwardingMessage->attachment ? '📎 Attachment' : '') }}</p>
                    </div>
                </div>
            </div>
            
            {{-- Room List --}}
            <div class="p-4">
                <div class="relative mb-3">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" placeholder="Cari percakapan..." class="w-full pl-9 pr-4 py-2 bg-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <p class="text-[10px] text-gray-400 uppercase tracking-wide mb-2">Percakapan Terbaru</p>
                <div class="max-h-64 overflow-y-auto space-y-1 -mx-1 px-1">
                    @foreach($this->forwardRooms as $room)
                    @php
                        $otherMember = $room->members->firstWhere('user_id', '!=', auth()->id());
                        $otherUser = $otherMember?->user;
                        $roomName = $room->type === 'direct' && $otherUser ? $otherUser->name : $room->name;
                        $roomPhoto = $room->type === 'direct' && $otherUser ? $otherUser->photo : null;
                    @endphp
                    <button wire:click="forwardTo({{ $room->id }})" 
                            class="w-full flex items-center gap-3 p-2.5 hover:bg-blue-50 rounded-xl transition text-left group">
                        @if($roomPhoto)
                            <img src="{{ asset('storage/' . $roomPhoto) }}" class="w-11 h-11 rounded-full object-cover flex-shrink-0">
                        @else
                            <div class="w-11 h-11 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0
                                {{ $room->type === 'direct' ? 'bg-gradient-to-br from-blue-500 to-indigo-600' : ($room->type === 'branch' ? 'bg-gradient-to-br from-emerald-500 to-teal-600' : 'bg-gradient-to-br from-violet-500 to-purple-600') }}">
                                @if($room->type === 'direct')
                                    {{ strtoupper(substr($roomName, 0, 1)) }}
                                @else
                                    <i class="fas {{ $room->type === 'branch' ? 'fa-building' : 'fa-users' }} text-sm"></i>
                                @endif
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 truncate">{{ $roomName }}</p>
                            <p class="text-xs text-gray-500">{{ $room->type === 'direct' ? 'Personal' : ($room->type === 'branch' ? 'Cabang' : 'Grup') }}</p>
                        </div>
                        <div class="opacity-0 group-hover:opacity-100 transition">
                            <i class="fas fa-paper-plane text-blue-500"></i>
                        </div>
                    </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Floating Button - raised on mobile to avoid bottom nav --}}
    <button wire:click="toggle" 
            class="fixed bottom-24 lg:bottom-6 right-4 lg:right-6 w-14 h-14 bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 rounded-full shadow-lg shadow-blue-500/30 flex items-center justify-center text-white hover:scale-110 transition-all duration-300 z-[9998]"
            style="{{ $isOpen ? 'display: none;' : '' }}">
        <i class="fas fa-comments text-xl"></i>
        @if($this->unreadCount > 0)
        <span class="absolute -top-1 -right-1 w-6 h-6 bg-red-500 rounded-full text-xs font-bold flex items-center justify-center animate-pulse">
            {{ $this->unreadCount > 99 ? '99+' : $this->unreadCount }}
        </span>
        @endif
    </button>

    {{-- Chat Panel --}}
    @if($isOpen)
    <div class="fixed inset-4 sm:inset-auto sm:bottom-4 sm:right-4 bg-white rounded-2xl shadow-2xl flex flex-col overflow-hidden z-[9999] animate-slideUp transition-all duration-300
        {{ $isExpanded ? 'sm:w-[36rem] sm:h-[44rem]' : 'sm:w-[420px] sm:h-[600px]' }}"
        style="max-height: calc(100vh - 2rem);">
        
        {{-- Header --}}
        <div class="bg-gradient-to-r from-blue-700 via-blue-800 to-indigo-900 px-4 py-3 flex items-center justify-between flex-shrink-0">
            @if($activeView === 'chat' && $activeRoom)
                {{-- Room Chat Header --}}
                <div class="flex items-center gap-3">
                    <button wire:click="closeChat" class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition">
                        <i class="fas fa-arrow-left text-white text-sm"></i>
                    </button>
                    
                    @if($activeRoom->type === 'support')
                        {{-- Support Chat Header --}}
                        @php $member = $activeRoom->member; @endphp
                        @if($member)
                        <div class="relative">
                            @if($member->photo)
                                <img src="{{ Storage::url($member->photo) }}" class="w-9 h-9 rounded-full object-cover">
                            @else
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center text-white text-sm font-bold">
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                </div>
                            @endif
                            <span class="absolute -bottom-0.5 -right-0.5 w-4 h-4 bg-orange-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-headset text-white text-[8px]"></i>
                            </span>
                        </div>
                        <div>
                            <p class="text-white font-semibold text-sm">{{ $member->name }}</p>
                            <p class="text-blue-200 text-xs">
                                {{ $member->member_id ?? 'Member' }}
                                @if($member->branch) • {{ $member->branch->name }} @endif
                            </p>
                        </div>
                        @endif
                    @elseif($activeRoom->isGroup())
                        {{-- Group Avatar --}}
                        <div class="w-9 h-9 rounded-full {{ $activeRoom->getColorClass() }} flex items-center justify-center text-white">
                            <i class="fas {{ $activeRoom->getIconClass() }}"></i>
                        </div>
                        <div>
                            <p class="text-white font-semibold text-sm">{{ $activeRoom->name }}</p>
                            <p class="text-blue-200 text-xs">{{ $activeRoom->members->count() }} anggota</p>
                        </div>
                    @else
                        {{-- Direct Chat Avatar --}}
                        @php $otherUser = $activeRoom->getOtherUser(auth()->id()); @endphp
                        @if($otherUser)
                        <div class="relative">
                            @if($otherUser->photo)
                                <img src="{{ $otherUser->getAvatarUrl(80) }}" class="w-9 h-9 rounded-full object-cover">
                            @else
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-sm font-bold">
                                    {{ $otherUser->getInitials() }}
                                </div>
                            @endif
                            <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2 border-blue-700 {{ $otherUser->isReallyOnline() ? 'bg-emerald-400' : 'bg-gray-400' }}"></span>
                        </div>
                        <div>
                            <p class="text-white font-semibold text-sm">{{ $otherUser->name }}</p>
                            <p class="text-blue-200 text-xs flex items-center gap-1">
                                @if($otherUser->isReallyOnline())
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                <span class="text-emerald-300">Online</span>
                                @else
                                <span>{{ $otherUser->getOnlineStatusText() }}</span>
                                @endif
                            </p>
                        </div>
                        @endif
                    @endif
                </div>
            @elseif($selectedBranch)
                {{-- Branch Contacts Header --}}
                <div class="flex items-center gap-3">
                    <button wire:click="backToList" class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition">
                        <i class="fas fa-arrow-left text-white text-sm"></i>
                    </button>
                    <div>
                        <p class="text-white font-bold text-sm">{{ $this->branches->find($selectedBranch)?->name ?? 'Staff' }}</p>
                        <p class="text-blue-200 text-xs">Pilih kontak</p>
                    </div>
                </div>
            @else
                {{-- Main Header --}}
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-comments text-white"></i>
                    </div>
                    <div>
                        <p class="text-white font-bold text-sm">Staff Chat</p>
                        <p class="text-blue-200 text-xs">Komunikasi antar cabang</p>
                    </div>
                </div>
            @endif
            
            <div class="flex items-center gap-1">
                @if($isExpanded)
                    {{-- Expanded: Show all buttons --}}
                    @if($activeView === 'chat' && $activeRoomId)
                    <button wire:click="toggleMessageSearch" class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition {{ $showMessageSearch ? 'bg-white/40' : '' }}" title="Cari Pesan">
                        <i class="fas fa-search text-white text-sm"></i>
                    </button>
                    @endif
                    <button wire:click="toggleSound" class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition" title="{{ $soundEnabled ? 'Matikan Suara' : 'Nyalakan Suara' }}">
                        <i class="fas {{ $soundEnabled ? 'fa-volume-up' : 'fa-volume-mute' }} text-white text-sm"></i>
                    </button>
                    <button wire:click="$toggle('isExpanded')" class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition" title="Kecilkan">
                        <i class="fas fa-compress-alt text-white text-sm"></i>
                    </button>
                    <button wire:click="toggle" class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition" title="Tutup">
                        <i class="fas fa-minus text-white text-sm"></i>
                    </button>
                    <button wire:click="toggle" class="w-8 h-8 bg-white/20 hover:bg-red-500/80 rounded-lg flex items-center justify-center transition">
                        <i class="fas fa-xmark text-white text-sm"></i>
                    </button>
                @else
                    {{-- Compact: dropdown + close --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition" title="Menu">
                            <i class="fas fa-ellipsis-v text-white text-sm"></i>
                        </button>
                        {{-- Dropdown Menu --}}
                        <div x-show="open" @click.away="open = false" x-transition
                             class="absolute right-0 top-10 w-44 bg-white rounded-xl shadow-xl border overflow-hidden z-50">
                            @if($activeView === 'chat' && $activeRoomId)
                            <button wire:click="toggleMessageSearch" @click="open = false" class="w-full px-3 py-2.5 flex items-center gap-2 hover:bg-gray-50 text-gray-700 text-sm">
                                <i class="fas fa-search w-4"></i> Cari Pesan
                            </button>
                            @endif
                            <button wire:click="toggleSound" @click="open = false" class="w-full px-3 py-2.5 flex items-center gap-2 hover:bg-gray-50 text-gray-700 text-sm">
                                <i class="fas {{ $soundEnabled ? 'fa-volume-up' : 'fa-volume-mute' }} w-4"></i>
                                {{ $soundEnabled ? 'Matikan Suara' : 'Nyalakan Suara' }}
                            </button>
                            <button wire:click="$toggle('isExpanded')" @click="open = false" class="w-full px-3 py-2.5 flex items-center gap-2 hover:bg-gray-50 text-gray-700 text-sm border-t">
                                <i class="fas fa-expand-alt w-4"></i> Perbesar
                            </button>
                        </div>
                    </div>
                    <button wire:click="toggle" class="w-8 h-8 bg-white/20 hover:bg-red-500/80 rounded-lg flex items-center justify-center transition">
                        <i class="fas fa-xmark text-white text-sm"></i>
                    </button>
                @endif
            </div>
        </div>

        @if($activeView === 'chat' && $activeRoom)
        {{-- =================================================== --}}
        {{-- CHAT VIEW --}}
        {{-- =================================================== --}}

        {{-- Message Search Panel --}}
        @if($showMessageSearch)
        <div class="p-2 border-b border-gray-200 bg-gray-50 flex-shrink-0">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input wire:model.live.debounce.300ms="messageSearchQuery" 
                       wire:keyup="searchMessages"
                       type="text" 
                       placeholder="Cari dalam percakapan..." 
                       class="w-full pl-9 pr-10 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/30"
                       autofocus>
                <button wire:click="clearMessageSearch" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            @if(count($searchResults) > 0)
            <div class="mt-2 max-h-40 overflow-y-auto rounded-lg bg-white border border-gray-200 divide-y">
                @foreach($searchResults as $result)
                <div class="p-2 hover:bg-blue-50 cursor-pointer text-left">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-xs font-medium text-gray-800">{{ $result['sender']['name'] ?? 'Unknown' }}</span>
                        <span class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($result['created_at'])->setTimezone('Asia/Jakarta')->format('d M H:i') }}</span>
                    </div>
                    <p class="text-xs text-gray-600 mt-0.5 line-clamp-2">{!! preg_replace('/(' . preg_quote($messageSearchQuery, '/') . ')/i', '<mark class="bg-yellow-200">$1</mark>', e($result['message'])) !!}</p>
                </div>
                @endforeach
            </div>
            @elseif(strlen($messageSearchQuery) >= 2)
            <p class="mt-2 text-xs text-gray-400 text-center py-2">Tidak ditemukan hasil untuk "{{ $messageSearchQuery }}"</p>
            @endif
        </div>
        @endif
        
        {{-- Member Info Panel (Support Chat) --}}
        @if($activeRoom && $activeRoom->type === 'support' && $activeRoom->member)
        @php $member = $activeRoom->member; @endphp
        <div class="bg-gradient-to-r from-orange-50 to-amber-50 border-b border-orange-200 px-4 py-2.5">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="px-2.5 py-1 bg-orange-100 text-orange-700 text-[10px] font-bold rounded-full whitespace-nowrap">
                        {{ ['unggah' => 'Unggah', 'plagiasi' => 'Plagiasi', 'bebas' => 'Bebas Pustaka', 'pinjam' => 'Peminjaman', 'lainnya' => 'Lainnya'][$activeRoom->topic] ?? $activeRoom->topic }}
                    </span>
                    <div class="text-xs text-gray-600 truncate">
                        <span class="font-semibold">{{ $member->member_id ?? '-' }}</span>
                        <span class="text-gray-400 mx-1">•</span>
                        <span>{{ $member->branch->name ?? '-' }}</span>
                    </div>
                    @if($activeRoom->status === 'resolved')
                    <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-bold rounded-full">
                        <i class="fas fa-check mr-0.5"></i>Selesai
                    </span>
                    @endif
                </div>
                @if($activeRoom->status !== 'resolved')
                <button wire:click="markSupportResolved" class="px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-[10px] font-bold rounded-lg transition whitespace-nowrap">
                    <i class="fas fa-check mr-1"></i>Selesai
                </button>
                @else
                <button wire:click="reopenSupport" class="px-3 py-1.5 bg-orange-500 hover:bg-orange-600 text-white text-[10px] font-bold rounded-lg transition whitespace-nowrap">
                    <i class="fas fa-redo mr-1"></i>Buka
                </button>
                @endif
            </div>
        </div>
        @endif
        
        {{-- Pinned Messages --}}
        @if(count($pinnedMessages) > 0)
        <div class="px-4 py-2 bg-amber-50 border-b border-amber-100" x-data="{ expanded: false }">
            <div class="flex items-center justify-between cursor-pointer" @click="expanded = !expanded">
                <div class="flex items-center gap-2 text-amber-700">
                    <i class="fas fa-thumbtack text-xs"></i>
                    <span class="text-xs font-medium">{{ count($pinnedMessages) }} pesan disematkan</span>
                </div>
                <i class="fas fa-chevron-down text-amber-500 text-xs transition-transform" :class="expanded && 'rotate-180'"></i>
            </div>
            <div x-show="expanded" x-collapse class="mt-2 space-y-1">
                @foreach($pinnedMessages as $pinned)
                <div class="flex items-center justify-between p-2 bg-white rounded-lg text-xs cursor-pointer hover:bg-amber-50" 
                     onclick="document.querySelector('[data-message-id=\'{{ $pinned['id'] }}\']')?.scrollIntoView({behavior:'smooth',block:'center'})">
                    <div class="flex-1 min-w-0">
                        <span class="font-medium text-gray-700">{{ $pinned['sender']['name'] ?? 'Unknown' }}:</span>
                        <span class="text-gray-600 truncate">{{ Str::limit($pinned['message'], 50) }}</span>
                    </div>
                    <button wire:click.stop="togglePin({{ $pinned['id'] }})" class="ml-2 text-gray-400 hover:text-red-500" title="Lepas pin">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Messages Area --}}
        <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-gradient-to-b from-slate-50 to-white chat-messages" id="chatMessages">
            @forelse($messages as $msg)
                <div wire:key="msg-{{ $msg['id'] }}" data-message-id="{{ $msg['id'] }}">
                @if($msg['is_deleted'] ?? false)
                    {{-- Deleted Message --}}
                    <div class="flex {{ ($msg['sender_id'] === auth()->id()) ? 'justify-end' : 'justify-start' }}">
                        <div class="px-4 py-2 bg-gray-100 rounded-2xl border border-dashed border-gray-300">
                            <p class="text-sm text-gray-400 italic"><i class="fas fa-ban mr-1"></i> Pesan telah dihapus</p>
                        </div>
                    </div>
                @elseif($msg['type'] === 'system')
                    {{-- System Message --}}
                    <div class="flex justify-center">
                        <span class="px-3 py-1 bg-gray-100 text-gray-500 text-xs rounded-full">
                            {{ $msg['message'] }}
                        </span>
                    </div>
                @elseif($msg['type'] === 'bot')
                    {{-- Bot Message (Very Compact) --}}
                    <div class="flex justify-start gap-1 my-0.5">
                        <div class="w-5 h-5 bg-violet-400 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-robot text-white text-[8px]"></i>
                        </div>
                        <div class="max-w-[85%] bg-violet-50 border border-violet-100 rounded-lg px-2 py-1">
                            <div class="text-[11px] text-gray-600 leading-tight">{!! preg_replace('/\*\*(.+?)\*\*/', '<b>$1</b>', e(Str::limit($msg['message'], 150))) !!}</div>
                        </div>
                    </div>
                @else
                    {{-- Regular Message --}}
                    @php
                        $isSupportChat = $activeRoom->type === 'support';
                        $isMemberMessage = $isSupportChat && is_null($msg['sender_id']);
                        $isOwnMessage = $msg['sender_id'] === auth()->id();
                        $isGroupChat = $activeRoom->isGroup();
                        $showSenderInfo = ($isGroupChat || $isSupportChat) && !$isOwnMessage && !$isMemberMessage;
                    @endphp
                    <div class="flex {{ ($isOwnMessage || ($isSupportChat && !$isMemberMessage)) ? 'justify-end' : 'justify-start' }} items-center gap-1"
                         x-data="{ showActions: false }" 
                         @mouseenter="showActions = true" 
                         @mouseleave="showActions = false"
                         @click="showActions = !showActions"
                         @click.away="showActions = false">
                        
                        {{-- Sender Avatar (for groups/support, not own message) --}}
                        @if($showSenderInfo)
                        <div class="flex-shrink-0 self-end mb-5">
                            @if(isset($msg['sender']['photo']) && $msg['sender']['photo'])
                                <img src="{{ asset('storage/' . $msg['sender']['photo']) }}" 
                                     class="w-7 h-7 rounded-full object-cover" 
                                     alt="{{ $msg['sender']['name'] ?? '' }}">
                            @else
                                @php
                                    $name = $msg['sender']['name'] ?? 'U';
                                    $initials = strtoupper(substr($name, 0, 1));
                                    if (str_contains($name, ' ')) {
                                        $parts = explode(' ', $name);
                                        $initials = strtoupper(substr($parts[0], 0, 1) . substr($parts[1] ?? '', 0, 1));
                                    }
                                    $colors = ['3b82f6', '10b981', 'f59e0b', 'ef4444', '8b5cf6', 'ec4899'];
                                    $colorIndex = ($msg['sender']['id'] ?? 0) % count($colors);
                                    $bgColor = $colors[$colorIndex];
                                @endphp
                                <div class="w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-bold" 
                                     style="background: linear-gradient(135deg, #{{ $bgColor }} 0%, #{{ $bgColor }}cc 100%);">
                                    {{ $initials }}
                                </div>
                            @endif
                        </div>
                        @endif

                        {{-- Action buttons for own message (3-dot menu) --}}
                        @if($isOwnMessage)
                        <div class="relative" x-show="showActions" x-transition.opacity x-cloak x-data="{ menuOpen: false }">
                            <button @click.stop="menuOpen = !menuOpen" class="w-7 h-7 flex items-center justify-center hover:bg-gray-100 rounded-full">
                                <i class="fas fa-ellipsis-v text-gray-400 text-xs"></i>
                            </button>
                            <div x-show="menuOpen" @click.away="menuOpen = false" x-transition
                                 class="absolute right-0 bottom-full mb-1 w-32 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-50">
                                <button wire:click.stop="toggleReaction({{ $msg['id'] }}, '👍')" @click="menuOpen = false" class="w-full px-3 py-1.5 text-left text-xs hover:bg-gray-50 flex items-center gap-2">
                                    <span>👍</span> <span class="text-gray-700">Suka</span>
                                </button>
                                <button wire:click.stop="replyToMessage({{ $msg['id'] }})" @click="menuOpen = false" class="w-full px-3 py-1.5 text-left text-xs hover:bg-gray-50 flex items-center gap-2">
                                    <i class="fas fa-reply text-gray-400 w-3"></i> <span class="text-gray-700">Balas</span>
                                </button>
                                <button wire:click.stop="openForwardModal({{ $msg['id'] }})" @click="menuOpen = false" class="w-full px-3 py-1.5 text-left text-xs hover:bg-gray-50 flex items-center gap-2">
                                    <i class="fas fa-share text-gray-400 w-3"></i> <span class="text-gray-700">Teruskan</span>
                                </button>
                                <button wire:click.stop="togglePin({{ $msg['id'] }})" @click="menuOpen = false" class="w-full px-3 py-1.5 text-left text-xs hover:bg-gray-50 flex items-center gap-2">
                                    <i class="fas fa-thumbtack {{ ($msg['is_pinned'] ?? false) ? 'text-amber-500' : 'text-gray-400' }} w-3"></i> 
                                    <span class="text-gray-700">{{ ($msg['is_pinned'] ?? false) ? 'Lepas' : 'Pin' }}</span>
                                </button>
                                <hr class="my-1">
                                <button @click.stop="menuOpen = false; $dispatch('confirm-delete', { id: {{ $msg['id'] }} })" class="w-full px-3 py-1.5 text-left text-xs hover:bg-red-50 flex items-center gap-2 text-red-600">
                                    <i class="fas fa-trash w-3"></i> <span>Hapus</span>
                                </button>
                            </div>
                        </div>
                        @endif

                        <div class="max-w-[70%]">
                            {{-- Reply reference --}}
                            @if(isset($msg['reply_to']) && $msg['reply_to'])
                            <div class="mb-1 px-2 py-1 bg-gray-100 border-l-2 border-blue-400 rounded text-xs text-gray-600 cursor-pointer hover:bg-gray-200" onclick="document.querySelector('[data-message-id=\'{{ $msg['reply_to']['id'] }}\']')?.scrollIntoView({behavior:'smooth',block:'center'})">
                                <span class="font-semibold text-blue-600">{{ $msg['reply_to']['sender']['name'] ?? 'Unknown' }}</span>
                                <p class="truncate">{{ $msg['reply_to']['attachment_type'] ? '📎 Attachment' : Str::limit($msg['reply_to']['message'], 50) }}</p>
                            </div>
                            @endif
                            
                            {{-- Sender Name (for groups/support) --}}
                            @if($showSenderInfo)
                                <p class="text-[10px] mb-1 ml-1 flex items-center gap-1.5">
                                    <span class="font-semibold text-gray-700">{{ $msg['sender']['name'] ?? 'Unknown' }}</span>
                                    @if(isset($msg['sender']['branch']['name']))
                                    <span class="px-1.5 py-0.5 bg-blue-50 text-blue-600 rounded text-[9px]">{{ $msg['sender']['branch']['name'] }}</span>
                                    @endif
                                </p>
                            @elseif($isMemberMessage)
                                <p class="text-[10px] mb-1 ml-1">
                                    <span class="font-semibold text-orange-600">{{ $activeRoom->member->name ?? 'Member' }}</span>
                                </p>
                            @endif
                            
                            {{-- Attachment --}}
                            @if($msg['attachment'])
                            <div class="mb-1">
                                @if($msg['attachment_type'] === 'image')
                                <img src="{{ asset('storage/' . $msg['attachment']) }}" 
                                     class="rounded-xl max-w-full cursor-pointer hover:opacity-90 transition" 
                                     onclick="openGlobalImage(this.src)"
                                     alt="Image">
                                @else
                                <a href="{{ asset('storage/' . $msg['attachment']) }}" 
                                   target="_blank" 
                                   class="flex items-center gap-2 px-3 py-2 bg-gray-100 rounded-xl hover:bg-gray-200 transition">
                                    <i class="fas fa-file text-gray-500"></i>
                                    <span class="text-sm text-gray-700 truncate">{{ $msg['attachment_name'] ?? 'Attachment' }}</span>
                                </a>
                                @endif
                            </div>
                            @endif

                            {{-- Voice Note --}}
                            @if($msg['voice_path'])
                            <div class="mb-1 flex items-center gap-2 px-3 py-2 {{ $msg['sender_id'] === auth()->id() ? 'bg-blue-500/20' : 'bg-gray-100' }} rounded-xl">
                                <audio controls class="h-8 max-w-[200px]" preload="metadata">
                                    <source src="{{ asset('storage/' . $msg['voice_path']) }}" type="{{ str_ends_with($msg['voice_path'], '.mp4') ? 'audio/mp4' : 'audio/webm' }}">
                                    Your browser does not support audio.
                                </audio>
                                <span class="text-xs text-gray-500">{{ gmdate('i:s', $msg['voice_duration'] ?? 0) }}</span>
                            </div>
                            @endif
                            
                            {{-- Forwarded indicator --}}
                            @if(isset($msg['forwarded_from']) && $msg['forwarded_from'])
                            <div class="text-[10px] text-gray-400 mb-1 flex items-center gap-1">
                                <i class="fas fa-share text-[8px]"></i>
                                <span>Diteruskan dari {{ $msg['forwarded_from']['sender']['name'] ?? 'Unknown' }}</span>
                            </div>
                            @endif
                            
                            {{-- Message Bubble --}}
                            @if($msg['message'])
                            <div class="px-4 py-2.5 rounded-2xl {{ $msg['sender_id'] === auth()->id() 
                                ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-br-md' 
                                : 'bg-white shadow-sm border border-gray-100 text-gray-800 rounded-bl-md' }}">
                                @php
                                    // Highlight @mentions
                                    $formattedMsg = $this->formatMessage($msg['message']);
                                    $formattedMsg = preg_replace('/@(\w+)/', '<span class="bg-blue-100 text-blue-700 px-1 rounded">@$1</span>', $formattedMsg);
                                @endphp
                                <p class="text-sm whitespace-pre-wrap break-words">{!! $formattedMsg !!}</p>
                            </div>
                            @endif
                            
                            {{-- Reactions --}}
                            @if(!empty($msg['reactions_grouped']))
                            <div class="flex flex-wrap gap-1 mt-1">
                                @foreach($msg['reactions_grouped'] as $emoji => $data)
                                <button wire:click.stop="toggleReaction({{ $msg['id'] }}, '{{ $emoji }}')" 
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs {{ in_array(auth()->id(), $data['user_ids']) ? 'bg-blue-100 border border-blue-300' : 'bg-gray-100 hover:bg-gray-200' }}"
                                        title="{{ implode(', ', $data['users']) }}">
                                    <span>{{ $emoji }}</span>
                                    <span class="text-gray-600">{{ $data['count'] }}</span>
                                </button>
                                @endforeach
                            </div>
                            @endif

                            {{-- Task Card --}}
                            @if(isset($msg['task']) && $msg['task'])
                            <div class="mt-1 p-2.5 bg-gradient-to-br from-slate-50 to-white rounded-xl border border-gray-200 shadow-sm min-w-[220px]">
                                <div class="flex items-start gap-2">
                                    <div class="w-9 h-9 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-tasks text-white text-xs"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-[10px] text-gray-400 font-mono">TASK-{{ $msg['task']['id'] }}</span>
                                            @if(isset($msg['task']['status']))
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-medium" 
                                                  style="background: {{ $msg['task']['status']['color'] ?? '#e5e7eb' }}20; color: {{ $msg['task']['status']['color'] ?? '#6b7280' }}">
                                                {{ $msg['task']['status']['name'] ?? 'No Status' }}
                                            </span>
                                            @endif
                                        </div>
                                        <p class="text-xs font-medium text-gray-800 mt-0.5 line-clamp-2">{{ $msg['task']['title'] }}</p>
                                        <div class="flex items-center gap-2 mt-1 text-[10px] text-gray-500">
                                            <span><i class="fas fa-user mr-0.5"></i>{{ $msg['task']['assignee']['name'] ?? 'Unassigned' }}</span>
                                            @if(isset($msg['task']['due_date']) && $msg['task']['due_date'])
                                            <span><i class="fas fa-calendar mr-0.5"></i>{{ \Carbon\Carbon::parse($msg['task']['due_date'])->setTimezone('Asia/Jakarta')->format('d M') }}</span>
                                            @endif
                                        </div>
                                        <button wire:click="openTaskModal({{ $msg['task']['id'] }})" 
                                           class="inline-flex items-center gap-1 mt-1.5 px-2 py-1 bg-blue-50 text-blue-600 rounded text-[10px] font-medium hover:bg-blue-100 transition">
                                            <i class="fas fa-external-link-alt"></i> Lihat Detail
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{-- Book Card --}}
                            @if(isset($msg['book']) && $msg['book'])
                            <div class="mt-1 p-2.5 bg-gradient-to-br from-green-50 to-white rounded-xl border border-green-200 shadow-sm min-w-[220px]">
                                <div class="flex items-start gap-2">
                                    @if(isset($msg['book']['image']) && $msg['book']['image'])
                                    @php
                                        $coverPath = str_starts_with($msg['book']['image'], 'covers/') ? $msg['book']['image'] : 'covers/' . $msg['book']['image'];
                                    @endphp
                                    <img src="{{ asset('storage/' . $coverPath) }}" class="w-10 h-14 object-cover rounded shadow flex-shrink-0">
                                    @else
                                    <div class="w-10 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-book text-white text-sm"></i>
                                    </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-[10px] text-green-600 font-medium"><i class="fas fa-book mr-1"></i>BUKU</span>
                                        </div>
                                        <p class="text-xs font-medium text-gray-800 line-clamp-2">{{ $msg['book']['title'] }}</p>
                                        <p class="text-[10px] text-gray-500 mt-0.5">
                                            @if(isset($msg['book']['authors']) && count($msg['book']['authors']) > 0)
                                                {{ collect($msg['book']['authors'])->pluck('name')->implode('; ') }}
                                            @else
                                                Unknown Author
                                            @endif
                                        </p>
                                        @if(isset($msg['book']['isbn']) && $msg['book']['isbn'])
                                        <p class="text-[10px] text-gray-400 mt-0.5">ISBN: {{ $msg['book']['isbn'] }}</p>
                                        @endif
                                        <a href="/staff/biblio?book={{ $msg['book']['id'] }}" 
                                           class="inline-flex items-center gap-1 mt-1.5 px-2 py-1 bg-green-50 text-green-600 rounded text-[10px] font-medium hover:bg-green-100 transition">
                                            <i class="fas fa-external-link-alt"></i> Lihat Katalog
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            {{-- Time & Read Status --}}
                            <div class="flex items-center justify-end gap-1 mt-1">
                                <span class="text-[10px] text-gray-400">
                                    {{ \Carbon\Carbon::parse($msg['created_at'])->setTimezone('Asia/Jakarta')->format('H:i') }}
                                </span>
                                @if($msg['sender_id'] === auth()->id())
                                    @php
                                        $readsCount = $msg['reads_count'] ?? 0;
                                        $totalRecipients = $msg['total_recipients'] ?? 1;
                                        $allRead = $readsCount >= $totalRecipients && $totalRecipients > 0;
                                    @endphp
                                    @if($allRead)
                                        {{-- Double check blue - all read --}}
                                        <svg class="w-4 h-4 text-blue-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <path d="M2 12l5 5L18 6M8 12l5 5L24 6" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    @else
                                        {{-- Double check gray - sent/delivered --}}
                                        <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <path d="M2 12l5 5L18 6M8 12l5 5L24 6" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    @endif
                                @endif
                            </div>
                        </div>

                        {{-- Action buttons for others' messages (3-dot menu) --}}
                        @if(!$isOwnMessage)
                        <div class="relative" x-show="showActions" x-transition.opacity x-cloak x-data="{ menuOpen: false }">
                            <button @click.stop="menuOpen = !menuOpen" class="w-7 h-7 flex items-center justify-center hover:bg-gray-100 rounded-full">
                                <i class="fas fa-ellipsis-v text-gray-400 text-xs"></i>
                            </button>
                            <div x-show="menuOpen" @click.away="menuOpen = false" x-transition
                                 class="absolute right-0 bottom-full mb-1 w-32 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-50">
                                <button wire:click.stop="toggleReaction({{ $msg['id'] }}, '👍')" @click="menuOpen = false" class="w-full px-3 py-1.5 text-left text-xs hover:bg-gray-50 flex items-center gap-2">
                                    <span>👍</span> <span class="text-gray-700">Suka</span>
                                </button>
                                <button wire:click.stop="replyToMessage({{ $msg['id'] }})" @click="menuOpen = false" class="w-full px-3 py-1.5 text-left text-xs hover:bg-gray-50 flex items-center gap-2">
                                    <i class="fas fa-reply text-gray-400 w-3"></i> <span class="text-gray-700">Balas</span>
                                </button>
                                <button wire:click.stop="openForwardModal({{ $msg['id'] }})" @click="menuOpen = false" class="w-full px-3 py-1.5 text-left text-xs hover:bg-gray-50 flex items-center gap-2">
                                    <i class="fas fa-share text-gray-400 w-3"></i> <span class="text-gray-700">Teruskan</span>
                                </button>
                                <button wire:click.stop="togglePin({{ $msg['id'] }})" @click="menuOpen = false" class="w-full px-3 py-1.5 text-left text-xs hover:bg-gray-50 flex items-center gap-2">
                                    <i class="fas fa-thumbtack {{ ($msg['is_pinned'] ?? false) ? 'text-amber-500' : 'text-gray-400' }} w-3"></i> 
                                    <span class="text-gray-700">{{ ($msg['is_pinned'] ?? false) ? 'Lepas' : 'Pin' }}</span>
                                </button>
                            </div>
                        </div>
                        @endif
                    </div>
                @endif
                </div>
            @empty
                <div class="flex flex-col items-center justify-center h-full text-gray-400">
                    <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mb-3">
                        <i class="fas fa-comments text-2xl text-blue-300"></i>
                    </div>
                    <p class="text-sm">Belum ada pesan</p>
                    <p class="text-xs">Mulai percakapan!</p>
                </div>
            @endforelse
        </div>

        {{-- Input Area --}}
        <div class="p-3 border-t border-gray-100 bg-white flex-shrink-0">
            {{-- Attachment Preview --}}
            @if($attachment)
            <div class="mb-2 p-2 bg-gray-50 rounded-lg flex items-center justify-between">
                <div class="flex items-center gap-2">
                    @if(str_starts_with($attachment->getMimeType(), 'image/'))
                    <img src="{{ $attachment->temporaryUrl() }}" class="w-10 h-10 rounded object-cover">
                    @else
                    <div class="w-10 h-10 bg-gray-200 rounded flex items-center justify-center">
                        <i class="fas fa-file text-gray-500"></i>
                    </div>
                    @endif
                    <span class="text-xs text-gray-600 truncate max-w-[200px]">{{ $attachment->getClientOriginalName() }}</span>
                </div>
                <button wire:click="removeAttachment" class="text-red-500 hover:text-red-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @endif

            {{-- Selected Task Preview --}}
            @if($this->selectedTask)
            <div class="mb-2 p-2 bg-blue-50 rounded-lg border border-blue-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 flex-1 min-w-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-tasks text-blue-600 text-xs"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-gray-800 truncate">{{ $this->selectedTask->title }}</p>
                            <p class="text-[10px] text-gray-500">
                                <span class="px-1.5 py-0.5 rounded" style="background: {{ $this->selectedTask->status?->color ?? '#e5e7eb' }}20; color: {{ $this->selectedTask->status?->color ?? '#6b7280' }}">
                                    {{ $this->selectedTask->status?->name ?? 'No Status' }}
                                </span>
                                • {{ $this->selectedTask->assignee?->name ?? 'Unassigned' }}
                            </p>
                        </div>
                    </div>
                    <button wire:click="removeTask" class="text-red-500 hover:text-red-600 p-1">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            @endif

            {{-- Selected Book Preview --}}
            @if($this->selectedBook)
            <div class="mb-2 p-2 bg-green-50 rounded-lg border border-green-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 flex-1 min-w-0">
                        @if($this->selectedBook->cover_url)
                        <img src="{{ $this->selectedBook->cover_url }}" class="w-8 h-10 object-cover rounded shadow">
                        @else
                        <div class="w-8 h-10 bg-green-100 rounded flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-book text-green-600 text-xs"></i>
                        </div>
                        @endif
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-gray-800 truncate">{{ $this->selectedBook->title }}</p>
                            <p class="text-[10px] text-gray-500 truncate">{{ $this->selectedBook->author_names ?: 'Unknown Author' }}</p>
                        </div>
                    </div>
                    <button wire:click="removeBook" class="text-red-500 hover:text-red-600 p-1">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            @endif

            {{-- Reply Preview --}}
            @if($replyTo)
            <div class="mb-2 p-2 bg-blue-50 rounded-lg border-l-4 border-blue-500 flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-blue-600 font-medium">
                        <i class="fas fa-reply mr-1"></i> Membalas {{ $replyTo['sender']['name'] ?? 'Unknown' }}
                    </p>
                    <p class="text-xs text-gray-600 truncate">
                        {{ $replyTo['attachment_type'] ? '📎 Attachment' : Str::limit($replyTo['message'], 60) }}
                    </p>
                </div>
                <button wire:click="cancelReply" class="text-gray-400 hover:text-gray-600 p-1">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @endif
            
            <div id="voiceBarAnchor" class="relative"></div>
            
            {{-- Typing indicator --}}
            @if(count($typingUsers) > 0)
            <div class="flex items-center gap-2 px-3 py-2 text-gray-500 text-xs">
                <div class="flex gap-1">
                    <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                    <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                    <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                </div>
                <span>
                    @if(count($typingUsers) === 1)
                        {{ $typingUsers[0] }} sedang mengetik...
                    @elseif(count($typingUsers) === 2)
                        {{ $typingUsers[0] }} dan {{ $typingUsers[1] }} sedang mengetik...
                    @else
                        {{ count($typingUsers) }} orang sedang mengetik...
                    @endif
                </span>
            </div>
            @endif
            
            {{-- Sending indicator --}}
            <div wire:loading.flex wire:target="sendMessage" class="items-center gap-2 px-3 py-2 bg-blue-50 text-blue-600 text-xs rounded-lg mb-2">
                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Mengirim pesan...</span>
            </div>
            
            <form wire:submit="sendMessage" class="flex items-center gap-2">
                {{-- Emoji Picker --}}
                <div class="relative" x-data="{ showEmoji: false }">
                    <button type="button" @click="showEmoji = !showEmoji" class="w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded-lg flex items-center justify-center transition" title="Emoji">
                        <i class="fas fa-smile text-gray-500 text-xs"></i>
                    </button>
                    <div x-show="showEmoji" @click.away="showEmoji = false" x-transition
                         class="absolute bottom-10 left-0 bg-white rounded-xl shadow-xl border p-2 z-50 w-72 max-h-64 overflow-y-auto">
                        @php
                        $emojis = [
                            'Wajah' => ['😀','😃','😄','😁','😆','😅','🤣','😂','🙂','😊','😇','🥰','😍','🤩','😘','😗','😚','😋','😛','😜','🤪','😝','🤑','🤗','🤭','🤫','🤔','🤐','🤨','😐','😑','😶','😏','😒','🙄','😬','🤥','😌','😔','😪','🤤','😴','😷','🤒','🤕','🤢','🤮','🥴','😵','🤯','🤠','🥳','😎','🤓','🧐','😕','😟','🙁','☹️','😮','😯','😲','😳','🥺','😦','😧','😨','😰','😥','😢','😭','😱','😖','😣','😞','😓','😩','😫','🥱','😤','😡','😠','🤬','😈','👿','💀','☠️','💩','🤡','👹','👺','👻','👽','👾','🤖'],
                            'Gestur' => ['👋','🤚','🖐️','✋','🖖','👌','🤌','🤏','✌️','🤞','🤟','🤘','🤙','👈','👉','👆','🖕','👇','☝️','👍','👎','✊','👊','🤛','🤜','👏','🙌','👐','🤲','🤝','🙏','✍️','💅','🤳','💪','🦾','🦿','🦵','🦶','👂','🦻','👃','🧠','🫀','🫁','🦷','🦴','👀','👁️','👅','👄'],
                            'Simbol' => ['❤️','🧡','💛','💚','💙','💜','🖤','🤍','🤎','💔','❣️','💕','💞','💓','💗','💖','💘','💝','💟','☮️','✝️','☪️','🕉️','☸️','✡️','🔯','🕎','☯️','☦️','🛐','⛎','♈','♉','♊','♋','♌','♍','♎','♏','♐','♑','♒','♓','🆔','⚛️','🉑','☢️','☣️','📴','📳','🈶','🈚','🈸','🈺','🈷️','✴️','🆚','💮','🉐','㊙️','㊗️','🈴','🈵','🈹','🈲','🅰️','🅱️','🆎','🆑','🅾️','🆘','❌','⭕','🛑','⛔','📛','🚫','💯','💢','♨️','🚷','🚯','🚳','🚱','🔞','📵','🚭','❗','❕','❓','❔','‼️','⁉️','🔅','🔆','〽️','⚠️','🚸','🔱','⚜️','🔰','♻️','✅','🈯','💹','❇️','✳️','❎','🌐','💠','Ⓜ️','🌀','💤','🏧','🚾','♿','🅿️','🛗','🈳','🈂️','🛂','🛃','🛄','🛅','🚹','🚺','🚼','⚧️','🚻','🚮','🎦','📶','🈁','🔣','ℹ️','🔤','🔡','🔠','🆖','🆗','🆙','🆒','🆕','🆓','0️⃣','1️⃣','2️⃣','3️⃣','4️⃣','5️⃣','6️⃣','7️⃣','8️⃣','9️⃣','🔟','🔢','#️⃣','*️⃣','⏏️','▶️','⏸️','⏯️','⏹️','⏺️','⏭️','⏮️','⏩','⏪','⏫','⏬','◀️','🔼','🔽','➡️','⬅️','⬆️','⬇️','↗️','↘️','↙️','↖️','↕️','↔️','↪️','↩️','⤴️','⤵️','🔀','🔁','🔂','🔄','🔃','🎵','🎶','➕','➖','➗','✖️','🟰','♾️','💲','💱','™️','©️','®️','👁️‍🗨️','🔚','🔙','🔛','🔝','🔜','〰️','➰','➿','✔️','☑️','🔘','🔴','🟠','🟡','🟢','🔵','🟣','⚫','⚪','🟤','🔺','🔻','🔸','🔹','🔶','🔷','🔳','🔲','▪️','▫️','◾','◽','◼️','◻️','🟥','🟧','🟨','🟩','🟦','🟪','⬛','⬜','🟫','🔈','🔇','🔉','🔊','🔔','🔕','📣','📢','💬','💭','🗯️','♠️','♣️','♥️','♦️','🃏','🎴','🀄','🕐','🕑','🕒','🕓','🕔','🕕','🕖','🕗','🕘','🕙','🕚','🕛','🕜','🕝','🕞','🕟','🕠','🕡','🕢','🕣','🕤','🕥','🕦','🕧'],
                            'Objek' => ['📚','📖','📕','📗','📘','📙','📓','📔','📒','📃','📜','📄','📰','🗞️','📑','🔖','🏷️','💰','🪙','💴','💵','💶','💷','💸','💳','🧾','💹','✉️','📧','📨','📩','📤','📥','📦','📫','📪','📬','📭','📮','🗳️','✏️','✒️','🖋️','🖊️','🖌️','🖍️','📝','💼','📁','📂','🗂️','📅','📆','🗒️','🗓️','📇','📈','📉','📊','📋','📌','📍','📎','🖇️','📏','📐','✂️','🗃️','🗄️','🗑️','🔒','🔓','🔏','🔐','🔑','🗝️','🔨','🪓','⛏️','⚒️','🛠️','🗡️','⚔️','🔫','🪃','🏹','🛡️','🪚','🔧','🪛','🔩','⚙️','🗜️','⚖️','🦯','🔗','⛓️','🪝','🧰','🧲','🪜','⚗️','🧪','🧫','🧬','🔬','🔭','📡','💉','🩸','💊','🩹','🩼','🩺','🩻','🚪','🛗','🪞','🪟','🛏️','🛋️','🪑','🚽','🪠','🚿','🛁','🪤','🪒','🧴','🧷','🧹','🧺','🧻','🪣','🧼','🫧','🪥','🧽','🧯','🛒','🚬','⚰️','🪦','⚱️','🗿','🪧','🏧'],
                            'Alam' => ['🌍','🌎','🌏','🌐','🗺️','🧭','🏔️','⛰️','🌋','🗻','🏕️','🏖️','🏜️','🏝️','🏞️','🏟️','🏛️','🏗️','🧱','🪨','🪵','🛖','🏘️','🏚️','🏠','🏡','🏢','🏣','🏤','🏥','🏦','🏨','🏩','🏪','🏫','🏬','🏭','🏯','🏰','💒','🗼','🗽','⛪','🕌','🛕','🕍','⛩️','🕋','⛲','⛺','🌁','🌃','🏙️','🌄','🌅','🌆','🌇','🌉','♨️','🎠','🛝','🎡','🎢','💈','🎪','🚂','🚃','🚄','🚅','🚆','🚇','🚈','🚉','🚊','🚝','🚞','🚋','🚌','🚍','🚎','🚐','🚑','🚒','🚓','🚔','🚕','🚖','🚗','🚘','🚙','🛻','🚚','🚛','🚜','🏎️','🏍️','🛵','🦽','🦼','🛺','🚲','🛴','🛹','🛼','🚏','🛣️','🛤️','🛢️','⛽','🛞','🚨','🚥','🚦','🛑','🚧','⚓','🛟','⛵','🛶','🚤','🛳️','⛴️','🛥️','🚢','✈️','🛩️','🛫','🛬','🪂','💺','🚁','🚟','🚠','🚡','🛰️','🚀','🛸','🌙','🌚','🌛','🌜','🌡️','☀️','🌝','🌞','🪐','⭐','🌟','🌠','🌌','☁️','⛅','⛈️','🌤️','🌥️','🌦️','🌧️','🌨️','🌩️','🌪️','🌫️','🌬️','🌀','🌈','🌂','☂️','☔','⛱️','⚡','❄️','☃️','⛄','☄️','🔥','💧','🌊'],
                        ];
                        @endphp
                        @foreach($emojis as $category => $list)
                        <div class="mb-2">
                            <div class="text-xs text-gray-400 mb-1">{{ $category }}</div>
                            <div class="grid grid-cols-8 gap-1">
                                @foreach($list as $emoji)
                                <button type="button" @click="$wire.set('message', ($wire.get('message') || '') + '{{ $emoji }}'); showEmoji = false"
                                        class="w-6 h-6 hover:bg-gray-100 rounded flex items-center justify-center text-sm">{{ $emoji }}</button>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="flex-1 relative" x-data="{ typingTimeout: null }">
                    <textarea wire:model="message" 
                              id="chatMessageInput"
                              placeholder="Ketik pesan..." 
                              rows="1"
                              class="w-full px-4 py-2.5 bg-gray-100 border-0 rounded-xl text-sm resize-none focus:ring-2 focus:ring-blue-500/30 focus:bg-white transition"
                              @input="clearTimeout(typingTimeout); $wire.startTyping(); typingTimeout = setTimeout(() => $wire.stopTyping(), 3000)"
                              @blur="$wire.stopTyping()"
                              onkeydown="if(event.key === 'Enter' && !event.shiftKey) { event.preventDefault(); @this.stopTyping(); @this.sendMessage(); }"></textarea>
                </div>
                
                <div class="flex items-center gap-1 flex-shrink-0">
                    {{-- More Options Dropdown --}}
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open" class="w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded-lg flex items-center justify-center transition" title="Lainnya">
                            <i class="fas fa-ellipsis-v text-gray-500 text-xs"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition
                             class="absolute bottom-10 right-0 bg-white rounded-xl shadow-xl border py-1 z-50 w-40">
                            <label class="w-full px-3 py-2 flex items-center gap-2 hover:bg-gray-50 cursor-pointer text-sm text-gray-700">
                                <i class="fas fa-image text-blue-500 w-4"></i> Gambar
                                <input type="file" wire:model="attachment" class="hidden" accept="image/*,.pdf,.doc,.docx" @change="open = false">
                            </label>
                            <button type="button" wire:click="openTaskPicker" @click="open = false" class="w-full px-3 py-2 flex items-center gap-2 hover:bg-gray-50 text-sm text-gray-700 text-left">
                                <i class="fas fa-clipboard-list text-violet-500 w-4"></i> Task
                            </button>
                            <button type="button" wire:click="openBookPicker" @click="open = false" class="w-full px-3 py-2 flex items-center gap-2 hover:bg-gray-50 text-sm text-gray-700 text-left">
                                <i class="fas fa-book text-green-500 w-4"></i> Buku
                            </button>
                        </div>
                    </div>

                    {{-- Voice Recorder --}}
                    <div x-data x-init="window.VoiceRecorder.init($wire)" class="flex items-center gap-1">
                        <button type="button" onclick="VoiceRecorder.start()" class="w-8 h-8 bg-gray-100 hover:bg-gray-200 text-gray-500 rounded-lg flex items-center justify-center transition" title="Voice Note">
                            <i class="fas fa-microphone text-xs"></i>
                        </button>
                        <button type="submit" class="w-8 h-8 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 rounded-lg flex items-center justify-center text-white shadow-lg shadow-blue-500/30 transition">
                            <i class="fas fa-paper-plane text-xs"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Task Picker Modal --}}
        @if($showTaskPicker)
        <div class="absolute inset-0 bg-black/30 z-50 flex items-end rounded-2xl overflow-hidden">
            <div class="bg-white w-full rounded-t-xl max-h-[70%] flex flex-col animate-slideUp">
                <div class="p-3 border-b flex items-center justify-between">
                    <h3 class="font-semibold text-sm">Pilih Task</h3>
                    <button wire:click="closeTaskPicker" class="w-8 h-8 hover:bg-gray-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-times text-gray-500"></i>
                    </button>
                </div>
                <div class="p-2 border-b">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input wire:model.live.debounce.300ms="taskSearch" 
                               type="text" 
                               placeholder="Cari task..." 
                               class="w-full pl-9 pr-4 py-2 bg-gray-100 border-0 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/30">
                    </div>
                </div>
                <div class="flex-1 overflow-y-auto">
                    @forelse($this->availableTasks as $task)
                    <button wire:click="attachTask({{ $task->id }})" 
                            class="w-full p-3 flex items-center gap-3 hover:bg-blue-50 transition border-b border-gray-50 text-left">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-tasks text-white text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-sm text-gray-800 truncate">{{ $task->title }}</p>
                            <p class="text-xs text-gray-500 flex items-center gap-2 mt-0.5">
                                <span class="px-1.5 py-0.5 rounded" style="background: {{ $task->status?->color ?? '#e5e7eb' }}20; color: {{ $task->status?->color ?? '#6b7280' }}">
                                    {{ $task->status?->name ?? 'No Status' }}
                                </span>
                                <span>{{ $task->assignee?->name ?? 'Unassigned' }}</span>
                            </p>
                        </div>
                        <i class="fas fa-plus text-blue-400"></i>
                    </button>
                    @empty
                    <div class="p-6 text-center text-gray-400">
                        <i class="fas fa-tasks text-2xl mb-2"></i>
                        <p class="text-sm">Tidak ada task ditemukan</p>
                    </div>
                    @endforelse
            </div>
        </div>
        @endif

        {{-- Book Picker Modal --}}
        @if($showBookPicker)
        <div class="absolute inset-0 bg-black/30 z-50 flex items-end rounded-2xl overflow-hidden">
            <div class="bg-white w-full rounded-t-xl max-h-[70%] flex flex-col animate-slideUp">
                <div class="p-3 border-b flex items-center justify-between">
                    <h3 class="font-semibold text-sm"><i class="fas fa-book text-green-600 mr-2"></i>Pilih Buku</h3>
                    <button wire:click="closeBookPicker" class="w-8 h-8 hover:bg-gray-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-times text-gray-500"></i>
                    </button>
                </div>
                <div class="p-2 border-b">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input wire:model.live.debounce.300ms="bookSearch" 
                               type="text" 
                               placeholder="Cari judul, ISBN, atau pengarang..." 
                               class="w-full pl-9 pr-4 py-2 bg-gray-100 border-0 rounded-lg text-sm focus:ring-2 focus:ring-green-500/30">
                    </div>
                </div>
                <div class="flex-1 overflow-y-auto">
                    @forelse($this->availableBooks as $book)
                    <button wire:click="attachBook({{ $book->id }})" 
                            class="w-full p-3 flex items-center gap-3 hover:bg-green-50 transition border-b border-gray-50 text-left">
                        @if($book->cover_url)
                        <img src="{{ $book->cover_url }}" class="w-10 h-14 object-cover rounded shadow">
                        @else
                        <div class="w-10 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-book text-white text-sm"></i>
                        </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-sm text-gray-800 truncate">{{ $book->title }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $book->author_names ?: 'Unknown Author' }}</p>
                            <p class="text-[10px] text-gray-400 flex items-center gap-2 mt-0.5">
                                @if($book->isbn)
                                <span>ISBN: {{ $book->isbn }}</span>
                                @endif
                                <span class="px-1.5 py-0.5 bg-green-100 text-green-700 rounded">{{ $book->items_count }} eksemplar</span>
                            </p>
                        </div>
                        <i class="fas fa-plus text-green-400"></i>
                    </button>
                    @empty
                    <div class="p-6 text-center text-gray-400">
                        <i class="fas fa-book text-2xl mb-2"></i>
                        <p class="text-sm">Tidak ada buku ditemukan</p>
                        <p class="text-xs mt-1">Coba kata kunci lain</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
        @endif
        @elseif($selectedBranch)
        {{-- =================================================== --}}
        {{-- BRANCH CONTACTS VIEW --}}
        {{-- =================================================== --}}
        <div class="flex-1 overflow-y-auto">
            <div class="p-3 border-b border-gray-100">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input wire:model.live.debounce.300ms="searchQuery" type="text" placeholder="Cari staff..." class="w-full pl-9 pr-4 py-2 bg-gray-100 border-0 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/30">
                </div>
            </div>
            @forelse($this->branchContacts as $user)
            <button wire:click="openDirectChat({{ $user->id }})" class="w-full px-4 py-3 flex items-center gap-3 hover:bg-blue-50 transition border-b border-gray-50">
                <div class="relative flex-shrink-0">
                    @if($user->photo)
                        <img src="{{ $user->getAvatarUrl(100) }}" class="w-11 h-11 rounded-full object-cover">
                    @else
                        <div class="w-11 h-11 rounded-full flex items-center justify-center text-white text-base font-bold" style="background: linear-gradient(135deg, #{{ $user->getAvatarColor() }} 0%, #{{ $user->getAvatarColor() }}dd 100%);">
                            {{ $user->getInitials() }}
                        </div>
                    @endif
                    <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full border-2 border-white {{ $user->isReallyOnline() ? 'bg-emerald-400' : 'bg-gray-300' }}"></span>
                </div>
                <div class="flex-1 min-w-0 text-left">
                    <p class="font-semibold text-gray-900 text-sm">{{ $user->name }}</p>
                    <p class="text-xs text-gray-500 flex items-center gap-1.5 mt-0.5">
                        @php
                            $roleColor = match($user->role) {
                                'super_admin', 'superadmin' => 'bg-red-100 text-red-700',
                                'admin' => 'bg-blue-100 text-blue-700',
                                default => 'bg-emerald-100 text-emerald-700'
                            };
                            $roleLabel = match($user->role) {
                                'super_admin', 'superadmin' => 'Super Admin',
                                'admin' => 'Admin',
                                'librarian', 'pustakawan' => 'Pustakawan',
                                default => ucfirst($user->role)
                            };
                        @endphp
                        <span class="px-1.5 py-0.5 {{ $roleColor }} rounded text-[10px] font-medium">{{ $roleLabel }}</span>
                        @if($user->isReallyOnline())
                        <span class="text-emerald-500 text-[10px]">● Online</span>
                        @else
                        <span class="text-[10px] text-gray-400">{{ $user->getOnlineStatusText() }}</span>
                        @endif
                    </p>
                </div>
                <i class="fas fa-comment text-blue-400"></i>
            </button>
            @empty
            <div class="p-8 text-center text-gray-400">
                <i class="fas fa-user-slash text-3xl mb-2"></i>
                <p class="text-sm">Tidak ada staff di cabang ini</p>
            </div>
            @endforelse
        </div>

        @else
        {{-- =================================================== --}}
        {{-- MAIN LIST VIEW WITH TABS --}}
        {{-- =================================================== --}}
        
        {{-- Tabs --}}
        <div class="flex border-b border-gray-100 flex-shrink-0">
            <button wire:click="setTab('conversations')" class="flex-1 py-2.5 text-xs font-medium transition {{ $activeTab === 'conversations' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
                <i class="fas fa-message mr-1"></i>Chat
            </button>
            <button wire:click="setTab('branches')" class="flex-1 py-2.5 text-xs font-medium transition {{ $activeTab === 'branches' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
                <i class="fas fa-building mr-1"></i>Cabang
            </button>
            <button wire:click="setTab('contacts')" class="flex-1 py-2.5 text-xs font-medium transition {{ $activeTab === 'contacts' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
                <i class="fas fa-users mr-1"></i>Semua
            </button>
        </div>

        {{-- Tab Content --}}
        <div class="flex-1 overflow-y-auto">
            @if($activeTab === 'conversations')
            {{-- =================================================== --}}
            {{-- CONVERSATIONS TAB with Sub-tabs (Grup + Personal) --}}
            {{-- =================================================== --}}
            
            {{-- Pill Switcher for Personal / Grup / Support --}}
            <div class="p-2 sticky top-0 bg-white z-20">
                <div class="flex bg-gray-100 rounded-lg p-1">
                    <button wire:click="$set('chatSubTab', 'personal')" 
                            class="flex-1 py-1.5 text-xs font-medium transition-all duration-200 flex items-center justify-center gap-1.5 rounded-md {{ ($chatSubTab ?? 'personal') === 'personal' ? 'bg-white shadow-sm text-green-600' : 'text-gray-500 hover:text-gray-700' }}">
                        <i class="fas fa-user {{ ($chatSubTab ?? 'personal') === 'personal' ? 'text-green-500' : 'text-gray-400' }}"></i>
                        <span class="hidden sm:inline">Personal</span>
                        @if($this->rooms['directs']->sum('unread_count') > 0)
                        <span class="w-4 h-4 bg-red-500 rounded-full text-[9px] text-white font-bold flex items-center justify-center">
                            {{ $this->rooms['directs']->sum('unread_count') > 9 ? '9+' : $this->rooms['directs']->sum('unread_count') }}
                        </span>
                        @endif
                    </button>
                    <button wire:click="$set('chatSubTab', 'groups')" 
                            class="flex-1 py-1.5 text-xs font-medium transition-all duration-200 flex items-center justify-center gap-1.5 rounded-md {{ ($chatSubTab ?? 'personal') === 'groups' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
                        <i class="fas fa-users {{ ($chatSubTab ?? 'personal') === 'groups' ? 'text-blue-500' : 'text-gray-400' }}"></i>
                        <span class="hidden sm:inline">Grup</span>
                        @if($this->rooms['groups']->sum('unread_count') > 0)
                        <span class="w-4 h-4 bg-red-500 rounded-full text-[9px] text-white font-bold flex items-center justify-center">
                            {{ $this->rooms['groups']->sum('unread_count') > 9 ? '9+' : $this->rooms['groups']->sum('unread_count') }}
                        </span>
                        @endif
                    </button>
                    <button wire:click="$set('chatSubTab', 'support')" 
                            class="flex-1 py-1.5 text-xs font-medium transition-all duration-200 flex items-center justify-center gap-1.5 rounded-md {{ ($chatSubTab ?? 'personal') === 'support' ? 'bg-white shadow-sm text-orange-600' : 'text-gray-500 hover:text-gray-700' }}">
                        <i class="fas fa-headset {{ ($chatSubTab ?? 'personal') === 'support' ? 'text-orange-500' : 'text-gray-400' }}"></i>
                        <span class="hidden sm:inline">Support</span>
                        @if(($this->rooms['support'] ?? collect())->sum('unread_count') > 0)
                        <span class="w-4 h-4 bg-red-500 rounded-full text-[9px] text-white font-bold flex items-center justify-center">
                            {{ ($this->rooms['support'] ?? collect())->sum('unread_count') > 9 ? '9+' : ($this->rooms['support'] ?? collect())->sum('unread_count') }}
                        </span>
                        @endif
                    </button>
                </div>
            </div>
            
            {{-- Sub-tab Content --}}
            @if(($chatSubTab ?? 'personal') === 'support')
            {{-- Support (Member) Chats --}}
            @php $canAccessSupport = in_array(auth()->user()->role, ['super_admin', 'admin', 'librarian']); @endphp
            @forelse(($this->rooms['support'] ?? collect()) as $room)
            @php $member = $room->member; @endphp
            @if($member)
            <div @if($canAccessSupport) wire:click="openRoom({{ $room->id }})" @endif
                    class="w-full px-4 py-3 flex items-center gap-3 transition border-b border-gray-50 {{ $room->status === 'resolved' ? 'opacity-60' : '' }} {{ $canAccessSupport ? 'hover:bg-gray-50 cursor-pointer' : 'cursor-default' }}">
                <div class="relative flex-shrink-0">
                    @if($member->photo)
                        <img src="{{ Storage::url($member->photo) }}" class="w-11 h-11 rounded-full object-cover">
                    @else
                        <div class="w-11 h-11 rounded-full flex items-center justify-center text-white text-base font-bold bg-gradient-to-br from-orange-500 to-red-600">
                            {{ strtoupper(substr($member->name, 0, 1)) }}
                        </div>
                    @endif
                    @if($room->status === 'resolved')
                        <div class="absolute -bottom-0.5 -right-0.5 w-4 h-4 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-white text-[8px]"></i>
                        </div>
                    @endif
                </div>
                <div class="flex-1 min-w-0 text-left">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-gray-800 text-sm truncate">{{ $member->name }}</span>
                        @if($room->latestMessage)
                        <span class="text-[10px] text-gray-400">{{ $room->latestMessage->created_at->shortRelativeDiffForHumans() }}</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="px-1.5 py-0.5 bg-orange-100 text-orange-600 text-[9px] rounded font-medium">
                            {{ ['unggah_mandiri' => 'Unggah', 'plagiasi' => 'Plagiasi', 'bebas_pustaka' => 'Bebas Pustaka', 'peminjaman' => 'Peminjaman', 'lainnya' => 'Lainnya'][$room->topic] ?? $room->topic }}
                        </span>
                        @if($member->member_id)
                        <span class="text-[10px] text-gray-400">{{ $member->member_id }}</span>
                        @endif
                    </div>
                    @if($room->latestMessage)
                    <p class="text-xs text-gray-500 truncate mt-0.5">{{ Str::limit($room->latestMessage->message, 30) }}</p>
                    @endif
                </div>
                @if($room->unread_count > 0)
                <span class="w-5 h-5 bg-orange-500 rounded-full text-[10px] text-white font-bold flex items-center justify-center flex-shrink-0">
                    {{ $room->unread_count > 9 ? '9+' : $room->unread_count }}
                </span>
                @endif
            </div>
            @endif
            @empty
            <div class="p-8 text-center text-gray-400">
                <i class="fas fa-headset text-3xl mb-2"></i>
                <p class="text-sm">Belum ada chat support</p>
            </div>
            @endforelse
            @if(!$canAccessSupport && ($this->rooms['support'] ?? collect())->count() > 0)
            <div class="px-4 py-2 bg-amber-50 border-t border-amber-100">
                <p class="text-[10px] text-amber-600"><i class="fas fa-info-circle mr-1"></i>Hanya Admin/Pustakawan yang dapat membalas</p>
            </div>
            @endif
            @elseif(($chatSubTab ?? 'personal') === 'personal')
            {{-- Personal (Direct Messages) List --}}
            @forelse($this->rooms['directs'] as $room)
            @php $otherUser = $room->other_user; @endphp
            @if($otherUser)
            <button wire:click="openRoom({{ $room->id }})" 
                    class="w-full px-4 py-3 flex items-center gap-3 hover:bg-gray-50 transition border-b border-gray-50">
                <div class="relative flex-shrink-0">
                    @if($otherUser->photo)
                        <img src="{{ $otherUser->getAvatarUrl(100) }}" class="w-11 h-11 rounded-full object-cover">
                    @else
                        <div class="w-11 h-11 rounded-full flex items-center justify-center text-white text-base font-bold" 
                             style="background: linear-gradient(135deg, #{{ $otherUser->getAvatarColor() }} 0%, #{{ $otherUser->getAvatarColor() }}dd 100%);">
                            {{ $otherUser->getInitials() }}
                        </div>
                    @endif
                    <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full border-2 border-white {{ $otherUser->isReallyOnline() ? 'bg-emerald-400' : 'bg-gray-300' }}"></span>
                </div>
                <div class="flex-1 min-w-0 text-left">
                    <div class="flex items-center justify-between">
                        <p class="font-semibold text-gray-900 text-sm truncate">{{ $otherUser->name }}</p>
                        @if($room->latestMessage)
                        <span class="text-[10px] text-gray-400">
                            {{ $room->latestMessage->created_at->diffForHumans(short: true) }}
                        </span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between mt-0.5">
                        <p class="text-xs text-gray-500 truncate pr-2">
                            @if($room->latestMessage)
                                @if($room->latestMessage->sender_id === auth()->id())
                                    <span class="text-gray-400">Anda: </span>
                                @endif
                                {{ $room->latestMessage->attachment ? '📎 File' : Str::limit($room->latestMessage->message, 30) }}
                            @else
                                <span class="italic text-gray-400">Mulai chat</span>
                            @endif
                        </p>
                        @if($room->unread_count > 0)
                        <span class="w-5 h-5 bg-green-600 rounded-full text-[10px] text-white font-bold flex items-center justify-center flex-shrink-0">
                            {{ $room->unread_count }}
                        </span>
                        @endif
                    </div>
                    <p class="text-[10px] text-gray-400 mt-0.5 flex items-center gap-2">
                        @php
                            $roleLabels = [
                                'super_admin' => ['label' => 'Super Admin', 'color' => 'bg-purple-100 text-purple-700'],
                                'admin' => ['label' => 'Admin', 'color' => 'bg-blue-100 text-blue-700'],
                                'librarian' => ['label' => 'Pustakawan', 'color' => 'bg-amber-100 text-amber-700'],
                                'staff' => ['label' => 'Staff', 'color' => 'bg-gray-100 text-gray-700'],
                            ];
                            $roleInfo = $roleLabels[$otherUser->role] ?? ['label' => ucfirst($otherUser->role ?? 'Staff'), 'color' => 'bg-gray-100 text-gray-700'];
                        @endphp
                        <span class="px-1.5 py-0.5 rounded {{ $roleInfo['color'] }} text-[9px] font-medium">{{ $roleInfo['label'] }}</span>
                        @if($otherUser->branch)
                        <span><i class="fas fa-building mr-0.5"></i>{{ $otherUser->branch->name }}</span>
                        @else
                        <span><i class="fas fa-globe mr-0.5"></i>Pusat</span>
                        @endif
                    </p>
                </div>
            </button>
            @endif
            @empty
            <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                <div class="w-14 h-14 bg-green-50 rounded-full flex items-center justify-center mb-3">
                    <i class="fas fa-user text-xl text-green-300"></i>
                </div>
                <p class="text-sm font-medium">Belum ada chat personal</p>
                <p class="text-xs text-center mt-1">Pilih kontak di tab Cabang atau Semua</p>
                <button wire:click="setTab('branches')" class="mt-3 px-4 py-2 bg-green-100 text-green-700 rounded-lg text-sm font-medium hover:bg-green-200 transition">
                    <i class="fas fa-building mr-1"></i>Lihat Cabang
                </button>
            </div>
            @endforelse
            
            @else
            {{-- Groups List --}}
            @forelse($this->rooms['groups'] as $room)
            <button wire:click="openRoom({{ $room->id }})" 
                    class="w-full px-4 py-3 flex items-center gap-3 hover:bg-blue-50 transition border-b border-gray-50">
                {{-- Group Avatar with colored bg --}}
                <div class="w-11 h-11 rounded-full bg-gradient-to-br {{ $room->isGlobal() ? 'from-blue-500 to-indigo-600' : 'from-green-500 to-emerald-600' }} flex items-center justify-center flex-shrink-0 shadow-lg">
                    <i class="fas {{ $room->getIconClass() }} text-white text-lg"></i>
                </div>
                
                {{-- Info --}}
                <div class="flex-1 min-w-0 text-left">
                    <div class="flex items-center justify-between">
                        <p class="font-semibold text-gray-900 text-sm truncate">{{ $room->name }}</p>
                        @if($room->latestMessage)
                        <span class="text-[10px] text-gray-400">
                            {{ $room->latestMessage->created_at->diffForHumans(short: true) }}
                        </span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between mt-0.5">
                        <p class="text-xs text-gray-500 truncate pr-2">
                            @if($room->latestMessage)
                                @if($room->latestMessage->sender_id === auth()->id())
                                    <span class="text-gray-400">Anda: </span>
                                @else
                                    <span class="text-gray-400">{{ Str::before($room->latestMessage->sender->name ?? '', ' ') }}: </span>
                                @endif
                                {{ $room->latestMessage->attachment ? '📎 File' : Str::limit($room->latestMessage->message, 25) }}
                            @else
                                <span class="italic text-gray-400">Belum ada pesan</span>
                            @endif
                        </p>
                        @if($room->unread_count > 0)
                        <span class="w-5 h-5 bg-blue-600 rounded-full text-[10px] text-white font-bold flex items-center justify-center flex-shrink-0">
                            {{ $room->unread_count > 99 ? '99+' : $room->unread_count }}
                        </span>
                        @endif
                    </div>
                    <p class="text-[10px] text-gray-400 mt-0.5">
                        <i class="fas fa-users mr-1"></i>{{ $room->members_count }} anggota
                    </p>
                </div>
            </button>
            @empty
            <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                <div class="w-14 h-14 bg-blue-50 rounded-full flex items-center justify-center mb-3">
                    <i class="fas fa-users text-xl text-blue-300"></i>
                </div>
                <p class="text-sm font-medium">Belum ada grup</p>
                <p class="text-xs text-center mt-1">Sinkronisasi grup dengan</p>
                <code class="text-[10px] bg-gray-100 px-2 py-1 rounded mt-1">php artisan chat:sync-rooms</code>
            </div>
            @endforelse
            @endif

            @elseif($activeTab === 'branches')
            {{-- =================================================== --}}
            {{-- BRANCHES TAB - Premium Compact Design --}}
            {{-- =================================================== --}}
            <div class="p-3 space-y-2">
                {{-- Search Branch --}}
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input wire:model.live.debounce.300ms="branchSearch" type="text" placeholder="Cari cabang..." 
                           class="w-full pl-8 pr-4 py-2 bg-gray-100 border-0 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/30">
                </div>

                {{-- Featured Branch Cards with Nav Arrows --}}
                <div x-data="{ 
                    scroll: null,
                    scrollLeft() { this.scroll.scrollBy({ left: -200, behavior: 'smooth' }) },
                    scrollRight() { this.scroll.scrollBy({ left: 200, behavior: 'smooth' }) }
                }" class="relative group/slider">
                    {{-- Left Arrow --}}
                    <button @click="scrollLeft()" class="absolute left-0 top-1/2 -translate-y-1/2 z-10 w-7 h-7 bg-white/90 hover:bg-white shadow-lg rounded-full flex items-center justify-center opacity-0 group-hover/slider:opacity-100 transition-opacity border">
                        <i class="fas fa-chevron-left text-gray-600 text-xs"></i>
                    </button>
                    
                    {{-- Cards Container --}}
                    <div x-ref="scroll" x-init="scroll = $refs.scroll" 
                         class="flex gap-2 overflow-x-auto pb-1 px-1 scrollbar-hide scroll-smooth" style="-webkit-overflow-scrolling: touch;">
                        @forelse($this->branches as $index => $branch)
                        @php
                            $gradients = [
                                'from-blue-500 to-indigo-600',
                                'from-emerald-500 to-teal-600',
                                'from-violet-500 to-purple-600',
                                'from-rose-500 to-pink-600',
                                'from-amber-500 to-orange-600',
                                'from-cyan-500 to-blue-600',
                            ];
                            $gradient = $gradients[$index % count($gradients)];
                            $onlineCount = $branch->users->filter(fn($u) => $u->isReallyOnline())->count();
                        @endphp
                        <button wire:click="selectBranch({{ $branch->id }})" 
                                class="flex-shrink-0 w-24 group">
                            <div class="bg-gradient-to-br {{ $gradient }} rounded-xl p-2.5 h-24 flex flex-col justify-between shadow-md group-hover:shadow-lg group-hover:scale-[1.03] transition-all duration-200">
                                {{-- Icon --}}
                                <div class="w-7 h-7 bg-white/25 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-building text-white text-xs"></i>
                                </div>
                                {{-- Info --}}
                                <div>
                                    <p class="font-semibold text-white text-[11px] leading-tight line-clamp-2">{{ $branch->name }}</p>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <span class="text-white/80 text-[9px]">
                                            <i class="fas fa-users"></i> {{ $branch->users_count }}
                                        </span>
                                        @if($onlineCount > 0)
                                        <span class="text-emerald-300 text-[9px] flex items-center gap-0.5">
                                            <span class="w-1 h-1 rounded-full bg-emerald-400"></span>{{ $onlineCount }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </button>
                        @empty
                        <div class="flex-1 text-center py-4 text-gray-400 text-sm">
                            <i class="fas fa-building-circle-xmark text-xl mb-1"></i>
                            <p>Cabang tidak ditemukan</p>
                        </div>
                        @endforelse
                    </div>
                    
                    {{-- Right Arrow --}}
                    <button @click="scrollRight()" class="absolute right-0 top-1/2 -translate-y-1/2 z-10 w-7 h-7 bg-white/90 hover:bg-white shadow-lg rounded-full flex items-center justify-center opacity-0 group-hover/slider:opacity-100 transition-opacity border">
                        <i class="fas fa-chevron-right text-gray-600 text-xs"></i>
                    </button>
                </div>
                
                {{-- Quick Access List --}}
                <div class="bg-gradient-to-br from-slate-50 to-white rounded-xl border border-gray-100 overflow-hidden">
                    <div class="px-3 py-1.5 border-b border-gray-100 bg-slate-50/50 flex items-center justify-between">
                        <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Akses Cepat</p>
                        <span class="text-[10px] text-gray-400">{{ $this->branches->count() }} cabang</span>
                    </div>
                    <div class="divide-y divide-gray-50 max-h-36 overflow-y-auto">
                        @foreach($this->branches->take(5) as $branch)
                        <button wire:click="selectBranch({{ $branch->id }})" 
                                class="w-full px-3 py-2 flex items-center gap-2 hover:bg-blue-50/50 transition group">
                            <div class="w-7 h-7 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                                <i class="fas fa-building text-white text-[10px]"></i>
                            </div>
                            <div class="flex-1 min-w-0 text-left">
                                <p class="text-xs font-medium text-gray-800 truncate">{{ $branch->name }}</p>
                            </div>
                            <span class="text-[10px] text-gray-400">{{ $branch->users_count }}</span>
                            <i class="fas fa-chevron-right text-gray-300 text-[10px] group-hover:text-blue-500 transition"></i>
                        </button>
                        @endforeach
                    </div>
                </div>
                
                {{-- Super Admin / No Branch --}}
                @php
                    $noBranchCount = \App\Models\User::whereNull('branch_id')
                        ->where('id', '!=', auth()->id())
                        ->whereIn('role', ['super_admin', 'admin', 'librarian', 'staff'])
                        ->count();
                @endphp
                @if($noBranchCount > 0)
                <button wire:click="$set('activeTab', 'contacts')" 
                        class="w-full p-2.5 bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200/50 rounded-xl hover:border-amber-300 hover:shadow transition-all group flex items-center gap-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-amber-400 to-orange-500 rounded-lg flex items-center justify-center shadow">
                        <i class="fas fa-crown text-white text-xs"></i>
                    </div>
                    <div class="flex-1 text-left">
                        <p class="font-medium text-gray-800 text-xs">Super Admin & Pusat</p>
                        <p class="text-[10px] text-amber-600">{{ $noBranchCount }} staff</p>
                    </div>
                    <i class="fas fa-chevron-right text-amber-400 text-xs"></i>
                </button>
                @endif
            </div>

            @else
            {{-- =================================================== --}}
            {{-- ALL CONTACTS TAB --}}
            {{-- =================================================== --}}
            <div class="p-3 border-b border-gray-100">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input wire:model.live.debounce.300ms="searchQuery" type="text" placeholder="Cari staff..." class="w-full pl-9 pr-4 py-2 bg-gray-100 border-0 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/30">
                </div>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($this->allContacts as $branchName => $users)
                <div class="py-2">
                    <div class="px-4 py-2 bg-gradient-to-r from-slate-50 to-white sticky top-0">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-building text-blue-400"></i>{{ $branchName }}
                            <span class="text-gray-400 font-normal">({{ $users->count() }})</span>
                        </p>
                    </div>
                    @foreach($users as $user)
                    <button wire:click="openDirectChat({{ $user->id }})" class="w-full px-4 py-2.5 flex items-center gap-3 hover:bg-blue-50 transition">
                        <div class="relative flex-shrink-0">
                            @if($user->photo)
                                <img src="{{ $user->getAvatarUrl(80) }}" class="w-10 h-10 rounded-full object-cover">
                            @else
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold" style="background: linear-gradient(135deg, #{{ $user->getAvatarColor() }} 0%, #{{ $user->getAvatarColor() }}dd 100%);">
                                    {{ $user->getInitials() }}
                                </div>
                            @endif
                            <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2 border-white {{ $user->isReallyOnline() ? 'bg-emerald-400' : 'bg-gray-300' }}"></span>
                        </div>
                        <div class="flex-1 min-w-0 text-left">
                            <p class="font-medium text-gray-900 text-sm">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500 flex items-center gap-1">
                                @php
                                    $roleColor = match($user->role) {
                                        'super_admin', 'superadmin' => 'bg-red-100 text-red-700',
                                        'admin' => 'bg-blue-100 text-blue-700',
                                        default => 'bg-emerald-100 text-emerald-700'
                                    };
                                    $roleLabel = match($user->role) {
                                        'super_admin', 'superadmin' => 'Super Admin',
                                        'admin' => 'Admin',
                                        'librarian', 'pustakawan' => 'Pustakawan',
                                        default => ucfirst($user->role)
                                    };
                                @endphp
                                <span class="px-1.5 py-0.5 {{ $roleColor }} rounded text-[10px] font-medium">{{ $roleLabel }}</span>
                                @if($user->isReallyOnline())
                                <span class="text-emerald-500 text-[10px]">● Online</span>
                                @else
                                <span class="text-[10px] text-gray-400">{{ $user->getOnlineStatusText() }}</span>
                                @endif
                            </p>
                        </div>
                        <i class="fas fa-chevron-right text-gray-300 text-xs"></i>
                    </button>
                    @endforeach
                </div>
                @empty
                <div class="p-6 text-center text-gray-400">
                    <i class="fas fa-user-slash text-2xl mb-2"></i>
                    <p class="text-sm">Tidak ada kontak ditemukan</p>
                </div>
                @endforelse
            </div>
            @endif
        </div>
        @endif
    </div>
    @endif

    <style>
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .animate-slideUp { animation: slideUp 0.25s ease-out; }
        .chat-messages::-webkit-scrollbar { width: 4px; }
        .chat-messages::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }
    </style>

    <script>
        // Sound notification using Web Audio API
        let audioContext = null;
        
        function playNotificationSound() {
            if (!audioContext) {
                audioContext = new (window.AudioContext || window.webkitAudioContext)();
            }
            
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.value = 800;
            oscillator.type = 'sine';
            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.3);
        }
        
        document.addEventListener('livewire:initialized', () => {
            // Track for new message detection
            let lastMessageId = 0;
            let isInitialized = false;
            
            Livewire.on('scrollToBottom', () => {
                setTimeout(() => {
                    const el = document.getElementById('chatMessages');
                    if (el) el.scrollTop = el.scrollHeight;
                }, 100);
            });
            
            // Initialize when chat opens - scroll to bottom once
            const initChat = () => {
                const chatEl = document.getElementById('chatMessages');
                if (chatEl && !isInitialized) {
                    isInitialized = true;
                    const messageEls = chatEl.querySelectorAll('[data-message-id]');
                    if (messageEls.length > 0) {
                        lastMessageId = parseInt(messageEls[messageEls.length - 1].dataset.messageId) || 0;
                    }
                    chatEl.scrollTop = chatEl.scrollHeight;
                }
                if (!chatEl) {
                    isInitialized = false; // Reset when chat closes
                }
            };
            
            setInterval(initChat, 300);
            
            // Only scroll on NEW messages (higher ID than before)
            Livewire.hook('message.processed', () => {
                setTimeout(() => {
                    const chatEl = document.getElementById('chatMessages');
                    if (!chatEl) return;
                    
                    const messageEls = chatEl.querySelectorAll('[data-message-id]');
                    if (messageEls.length > 0) {
                        const newestId = parseInt(messageEls[messageEls.length - 1].dataset.messageId) || 0;
                        if (newestId > lastMessageId && lastMessageId > 0) {
                            chatEl.scrollTop = chatEl.scrollHeight;
                        }
                        lastMessageId = newestId;
                    }
                }, 100);
            });
            
            // Paste image from clipboard
            document.addEventListener('paste', (e) => {
                const chatInput = document.getElementById('chatMessageInput');
                if (!chatInput || document.activeElement !== chatInput) return;
                
                const items = e.clipboardData?.items;
                if (!items) return;
                
                for (const item of items) {
                    if (item.type.startsWith('image/')) {
                        e.preventDefault();
                        const file = item.getAsFile();
                        if (file) {
                            // Upload via Livewire
                            const input = document.createElement('input');
                            input.type = 'file';
                            const dt = new DataTransfer();
                            dt.items.add(file);
                            input.files = dt.files;
                            
                            @this.upload('attachment', file, 
                                () => console.log('Image pasted successfully'),
                                () => console.error('Failed to paste image')
                            );
                        }
                        break;
                    }
                }
            });
            
            // Listen for new message sound
            Livewire.on('playNewMessageSound', () => {
                playNotificationSound();
            });

            // Focus input when replying
            Livewire.on('focusInput', () => {
                setTimeout(() => {
                    document.getElementById('chatMessageInput')?.focus();
                }, 100);
            });

            // Check for open_chat query param (from notification click)
            const urlParams = new URLSearchParams(window.location.search);
            const openChatRoom = urlParams.get('open_chat');
            if (openChatRoom) {
                // Dispatch to Livewire component to open the chat room
                setTimeout(() => {
                    Livewire.dispatch('openChatRoom', { roomId: parseInt(openChatRoom) });
                    // Clean up URL
                    window.history.replaceState({}, '', window.location.pathname);
                }, 500);
            }
        });
    </script>

    {{-- Task Detail Modal --}}
    @if($viewingTaskId && $this->viewingTask)
    <div class="fixed inset-0 bg-black/50 z-[10000] flex items-center justify-center p-4" wire:click.self="closeTaskModal">
        <div class="bg-white rounded-2xl w-full max-w-md max-h-[80vh] overflow-hidden shadow-2xl animate-slideUp">
            {{-- Modal Header --}}
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-tasks text-white"></i>
                    </div>
                    <div>
                        <p class="text-white font-bold text-sm">Task Detail</p>
                        <p class="text-blue-200 text-xs">TASK-{{ $this->viewingTask->id }}</p>
                    </div>
                </div>
                <button wire:click="closeTaskModal" class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition">
                    <i class="fas fa-times text-white"></i>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-4 overflow-y-auto max-h-[60vh]">
                {{-- Status Badge --}}
                <div class="flex items-center justify-between mb-3">
                    <span class="px-3 py-1 rounded-full text-sm font-medium" 
                          style="background: {{ $this->viewingTask->status?->color ?? '#e5e7eb' }}20; color: {{ $this->viewingTask->status?->color ?? '#6b7280' }}">
                        {{ $this->viewingTask->status?->name ?? 'No Status' }}
                    </span>
                    @if($this->viewingTask->priority)
                    <span class="px-2 py-1 rounded text-xs font-medium
                        {{ $this->viewingTask->priority === 'high' ? 'bg-red-100 text-red-700' : '' }}
                        {{ $this->viewingTask->priority === 'medium' ? 'bg-amber-100 text-amber-700' : '' }}
                        {{ $this->viewingTask->priority === 'low' ? 'bg-green-100 text-green-700' : '' }}
                    ">
                        {{ ucfirst($this->viewingTask->priority) }} Priority
                    </span>
                    @endif
                </div>

                {{-- Title --}}
                <h3 class="text-lg font-bold text-gray-800 mb-3">{{ $this->viewingTask->title }}</h3>

                {{-- Description --}}
                @if($this->viewingTask->description)
                <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $this->viewingTask->description }}</p>
                </div>
                @endif

                {{-- Details Grid --}}
                <div class="grid grid-cols-2 gap-3 text-sm">
                    {{-- Assignee --}}
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-[10px] text-gray-400 uppercase font-medium mb-1">Ditugaskan ke</p>
                        <p class="font-medium text-gray-800">{{ $this->viewingTask->assignee?->name ?? 'Unassigned' }}</p>
                    </div>
                    
                    {{-- Reporter --}}
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-[10px] text-gray-400 uppercase font-medium mb-1">Dilaporkan oleh</p>
                        <p class="font-medium text-gray-800">{{ $this->viewingTask->reporter?->name ?? '-' }}</p>
                    </div>

                    {{-- Due Date --}}
                    @if($this->viewingTask->due_date)
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-[10px] text-gray-400 uppercase font-medium mb-1">Deadline</p>
                        <p class="font-medium {{ $this->viewingTask->isOverdue() ? 'text-red-600' : 'text-gray-800' }}">
                            {{ $this->viewingTask->due_date->format('d M Y') }}
                        </p>
                    </div>
                    @endif

                    {{-- Branch --}}
                    @if($this->viewingTask->branch)
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-[10px] text-gray-400 uppercase font-medium mb-1">Cabang</p>
                        <p class="font-medium text-gray-800">{{ $this->viewingTask->branch->name }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="px-4 py-3 border-t bg-gray-50 flex items-center justify-between">
                <a href="/staff/task?task={{ $this->viewingTask->id }}" 
                   class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                    <i class="fas fa-external-link-alt"></i> Buka di Kanban
                </a>
                <button wire:click="closeTaskModal" class="px-4 py-2 text-gray-600 hover:text-gray-800 text-sm font-medium transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
