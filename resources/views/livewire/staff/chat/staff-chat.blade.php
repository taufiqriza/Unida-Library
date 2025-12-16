<div @if($isOpen) wire:poll.5s="refreshData" @endif class="staff-chat-widget">
    {{-- Floating Button --}}
    <button wire:click="toggle" 
            class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 rounded-full shadow-lg shadow-blue-500/30 flex items-center justify-center text-white hover:scale-110 transition-all duration-300 z-[9998]"
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
    <div class="fixed bottom-6 right-6 bg-white rounded-2xl shadow-2xl flex flex-col overflow-hidden z-[9999] animate-slideUp transition-all duration-300
        {{ $isExpanded ? 'w-[32rem] h-[42rem]' : 'w-96 h-[32rem]' }}">
        
        {{-- Header --}}
        <div class="bg-gradient-to-r from-blue-700 via-blue-800 to-indigo-900 px-4 py-3 flex items-center justify-between flex-shrink-0">
            @if($activeView === 'chat' && $activeRoom)
                {{-- Room Chat Header --}}
                <div class="flex items-center gap-3">
                    <button wire:click="closeChat" class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition">
                        <i class="fas fa-arrow-left text-white text-sm"></i>
                    </button>
                    
                    @if($activeRoom->isGroup())
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
                            <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2 border-blue-700 {{ $otherUser->is_online ? 'bg-emerald-400' : 'bg-gray-400' }}"></span>
                        </div>
                        <div>
                            <p class="text-white font-semibold text-sm">{{ $otherUser->name }}</p>
                            <p class="text-blue-200 text-xs">{{ $otherUser->branch?->name ?? 'Admin' }}</p>
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
                        <span class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($result['created_at'])->format('d M H:i') }}</span>
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
        
        {{-- Messages Area --}}
        <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-gradient-to-b from-slate-50 to-white chat-messages" id="chatMessages">
            @forelse($messages as $msg)
                <div wire:key="msg-{{ $msg['id'] }}">
                @if($msg['type'] === 'system')
                    {{-- System Message --}}
                    <div class="flex justify-center">
                        <span class="px-3 py-1 bg-gray-100 text-gray-500 text-xs rounded-full">
                            {{ $msg['message'] }}
                        </span>
                    </div>
                @else
                    {{-- Regular Message --}}
                    @php
                        $isOwnMessage = $msg['sender_id'] === auth()->id();
                        $isGroupChat = $activeRoom->isGroup();
                    @endphp
                    <div class="flex {{ $isOwnMessage ? 'justify-end' : 'justify-start' }} gap-2">
                        {{-- Sender Avatar (for groups, not own message) --}}
                        @if($isGroupChat && !$isOwnMessage)
                        <div class="flex-shrink-0 mt-4">
                            @if(isset($msg['sender']['photo']) && $msg['sender']['photo'])
                                <img src="{{ asset('storage/' . $msg['sender']['photo']) }}" 
                                     class="w-8 h-8 rounded-full object-cover" 
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
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold" 
                                     style="background: linear-gradient(135deg, #{{ $bgColor }} 0%, #{{ $bgColor }}cc 100%);">
                                    {{ $initials }}
                                </div>
                            @endif
                        </div>
                        @endif

                        <div class="max-w-[75%]">
                            {{-- Sender Name + Branch (for groups, not own message) --}}
                            @if($isGroupChat && !$isOwnMessage)
                                <p class="text-[10px] mb-1 ml-1 flex items-center gap-1.5">
                                    <span class="font-semibold text-gray-700">{{ $msg['sender']['name'] ?? 'Unknown' }}</span>
                                    @if(isset($msg['sender']['branch']['name']))
                                    <span class="px-1.5 py-0.5 bg-blue-50 text-blue-600 rounded text-[9px]">{{ $msg['sender']['branch']['name'] }}</span>
                                    @else
                                    <span class="px-1.5 py-0.5 bg-amber-50 text-amber-600 rounded text-[9px]">Pusat</span>
                                    @endif
                                </p>
                            @endif
                            
                            {{-- Attachment --}}
                            @if($msg['attachment'])
                            <div class="mb-1">
                                @if($msg['attachment_type'] === 'image')
                                <img src="{{ asset('storage/' . $msg['attachment']) }}" 
                                     class="rounded-xl max-w-full cursor-pointer hover:opacity-90 transition" 
                                     onclick="window.open(this.src)">
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
                            
                            {{-- Message Bubble --}}
                            @if($msg['message'])
                            <div class="px-4 py-2.5 rounded-2xl {{ $msg['sender_id'] === auth()->id() 
                                ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-br-md' 
                                : 'bg-white shadow-sm border border-gray-100 text-gray-800 rounded-bl-md' }}">
                                <p class="text-sm whitespace-pre-wrap break-words">{!! $this->formatMessage($msg['message']) !!}</p>
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
                                            <span><i class="fas fa-calendar mr-0.5"></i>{{ \Carbon\Carbon::parse($msg['task']['due_date'])->format('d M') }}</span>
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
                            
                            {{-- Time --}}
                            <p class="text-[10px] text-gray-400 mt-1 {{ $msg['sender_id'] === auth()->id() ? 'text-right' : '' }}">
                                {{ \Carbon\Carbon::parse($msg['created_at'])->format('H:i') }}
                            </p>
                        </div>
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
            
            <form wire:submit="sendMessage" class="flex items-center gap-2">
                <div class="flex-1 relative">
                    <textarea wire:model="message" 
                              placeholder="Ketik pesan..." 
                              rows="1"
                              class="w-full px-4 py-2.5 bg-gray-100 border-0 rounded-xl text-sm resize-none focus:ring-2 focus:ring-blue-500/30 focus:bg-white transition"
                              onkeydown="if(event.key === 'Enter' && !event.shiftKey) { event.preventDefault(); $wire.sendMessage(); }"></textarea>
                </div>
                <div class="flex items-center gap-1 flex-shrink-0">
                    <label class="w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded-lg flex items-center justify-center cursor-pointer transition" title="Attach File">
                        <i class="fas fa-image text-gray-500 text-xs"></i>
                        <input type="file" wire:model="attachment" class="hidden" accept="image/*,.pdf,.doc,.docx">
                    </label>
                    <button type="button" wire:click="openTaskPicker" class="w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded-lg flex items-center justify-center transition" title="Attach Task">
                        <i class="fas fa-clipboard-list text-gray-500 text-xs"></i>
                    </button>
                    <button type="button" wire:click="openBookPicker" class="w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded-lg flex items-center justify-center transition" title="Share Buku">
                        <i class="fas fa-book text-gray-500 text-xs"></i>
                    </button>
                    <button type="submit" class="w-8 h-8 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 rounded-lg flex items-center justify-center text-white shadow-lg shadow-blue-500/30 transition">
                        <i class="fas fa-paper-plane text-xs"></i>
                    </button>
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
                    <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full border-2 border-white {{ $user->is_online ? 'bg-emerald-400' : 'bg-gray-300' }}"></span>
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
                        @if($user->is_online)
                        <span class="text-emerald-500 text-[10px]">● Online</span>
                        @elseif($user->last_seen_at)
                        <span class="text-[10px]">{{ $user->last_seen_at->diffForHumans() }}</span>
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
            
            {{-- Pill Switcher for Personal / Grup --}}
            <div class="p-2 sticky top-0 bg-white z-20">
                <div class="flex bg-gray-100 rounded-lg p-1">
                    <button wire:click="$set('chatSubTab', 'personal')" 
                            class="flex-1 py-1.5 text-xs font-medium transition-all duration-200 flex items-center justify-center gap-1.5 rounded-md {{ ($chatSubTab ?? 'personal') === 'personal' ? 'bg-white shadow-sm text-green-600' : 'text-gray-500 hover:text-gray-700' }}">
                        <i class="fas fa-user {{ ($chatSubTab ?? 'personal') === 'personal' ? 'text-green-500' : 'text-gray-400' }}"></i>
                        Personal
                        @if($this->rooms['directs']->sum('unread_count') > 0)
                        <span class="w-4 h-4 bg-red-500 rounded-full text-[9px] text-white font-bold flex items-center justify-center">
                            {{ $this->rooms['directs']->sum('unread_count') > 9 ? '9+' : $this->rooms['directs']->sum('unread_count') }}
                        </span>
                        @endif
                    </button>
                    <button wire:click="$set('chatSubTab', 'groups')" 
                            class="flex-1 py-1.5 text-xs font-medium transition-all duration-200 flex items-center justify-center gap-1.5 rounded-md {{ ($chatSubTab ?? 'personal') === 'groups' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
                        <i class="fas fa-users {{ ($chatSubTab ?? 'personal') === 'groups' ? 'text-blue-500' : 'text-gray-400' }}"></i>
                        Grup
                        @if($this->rooms['groups']->sum('unread_count') > 0)
                        <span class="w-4 h-4 bg-red-500 rounded-full text-[9px] text-white font-bold flex items-center justify-center">
                            {{ $this->rooms['groups']->sum('unread_count') > 9 ? '9+' : $this->rooms['groups']->sum('unread_count') }}
                        </span>
                        @endif
                    </button>
                </div>
            </div>
            
            {{-- Sub-tab Content --}}
            @if(($chatSubTab ?? 'personal') === 'personal')
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
                    <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full border-2 border-white {{ $otherUser->is_online ? 'bg-emerald-400' : 'bg-gray-300' }}"></span>
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
            {{-- BRANCHES TAB --}}
            {{-- =================================================== --}}
            <div class="p-3">
                <div class="grid grid-cols-2 gap-2">
                    @foreach($this->branches as $branch)
                    <button wire:click="selectBranch({{ $branch->id }})" 
                            class="p-4 bg-gradient-to-br from-slate-50 to-white border border-gray-100 rounded-xl hover:border-blue-300 hover:shadow-md transition-all text-left group">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-xl flex items-center justify-center mb-2 group-hover:scale-110 transition shadow-lg shadow-blue-500/20">
                            <i class="fas fa-building text-white"></i>
                        </div>
                        <p class="font-semibold text-gray-900 text-sm line-clamp-2 leading-tight">{{ $branch->name }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-users mr-1"></i>{{ $branch->users_count }} staff
                        </p>
                    </button>
                    @endforeach
                    
                    {{-- No Branch Staff --}}
                    @php
                        $noBranchCount = \App\Models\User::whereNull('branch_id')
                            ->where('id', '!=', auth()->id())
                            ->whereIn('role', ['super_admin', 'admin', 'librarian', 'staff'])
                            ->count();
                    @endphp
                    @if($noBranchCount > 0)
                    <button wire:click="$set('activeTab', 'contacts')" 
                            class="p-4 bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-100 rounded-xl hover:border-amber-300 hover:shadow-md transition-all text-left group">
                        <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl flex items-center justify-center mb-2 group-hover:scale-110 transition shadow-lg shadow-amber-500/20">
                            <i class="fas fa-user-shield text-white"></i>
                        </div>
                        <p class="font-semibold text-gray-900 text-sm">Super Admin</p>
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-users mr-1"></i>{{ $noBranchCount }} staff
                        </p>
                    </button>
                    @endif
                </div>
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
                            <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2 border-white {{ $user->is_online ? 'bg-emerald-400' : 'bg-gray-300' }}"></span>
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
                                @if($user->is_online)
                                <span class="text-emerald-500">Online</span>
                                @elseif($user->last_seen_at)
                                <span>{{ $user->last_seen_at->diffForHumans() }}</span>
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
            Livewire.on('scrollToBottom', () => {
                // Scroll with multiple attempts to ensure it works after DOM render
                const scroll = () => {
                    const el = document.getElementById('chatMessages');
                    if (el) el.scrollTop = el.scrollHeight;
                };
                setTimeout(scroll, 50);
                setTimeout(scroll, 150);
                setTimeout(scroll, 300);
            });
            
            // Listen for new message sound
            Livewire.on('playNewMessageSound', () => {
                playNotificationSound();
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
