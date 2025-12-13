<div wire:poll.3s="refreshData" class="staff-chat-widget">
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
    <div class="fixed bottom-6 right-6 w-96 h-[32rem] bg-white rounded-2xl shadow-2xl flex flex-col overflow-hidden z-[9999] animate-slideUp">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-blue-700 via-blue-800 to-indigo-900 px-4 py-3 flex items-center justify-between flex-shrink-0">
            @if($activeChat && $activeChatUser)
            {{-- Chat Header --}}
            <div class="flex items-center gap-3">
                <button wire:click="closeChat" class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition">
                    <i class="fas fa-arrow-left text-white text-sm"></i>
                </button>
                <div class="relative">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($activeChatUser->name) }}&background=random" class="w-9 h-9 rounded-full">
                    <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2 border-blue-700 {{ $activeChatUser->is_online ? 'bg-emerald-400' : 'bg-gray-400' }}"></span>
                </div>
                <div>
                    <p class="text-white font-semibold text-sm">{{ $activeChatUser->name }}</p>
                    <p class="text-blue-200 text-xs">{{ $activeChatUser->branch?->name ?? 'Admin' }}</p>
                </div>
            </div>
            @elseif($selectedBranch)
            {{-- Branch Contacts Header --}}
            <div class="flex items-center gap-3">
                <button wire:click="backToContacts" class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition">
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
                <button wire:click="toggle" class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition">
                    <i class="fas fa-minus text-white text-sm"></i>
                </button>
                <button wire:click="toggle" class="w-8 h-8 bg-white/20 hover:bg-red-500/80 rounded-lg flex items-center justify-center transition">
                    <i class="fas fa-xmark text-white text-sm"></i>
                </button>
            </div>
        </div>

        @if($activeChat && $activeChatUser)
        {{-- Chat Room --}}
        <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-gradient-to-b from-slate-50 to-white chat-messages" id="chatMessages">
            @forelse($messages as $msg)
            <div class="flex {{ $msg['sender_id'] === auth()->id() ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-[80%]">
                    @if($msg['attachment'])
                    <div class="mb-1">
                        @if($msg['attachment_type'] === 'image')
                        <img src="{{ asset('storage/' . $msg['attachment']) }}" class="rounded-xl max-w-full cursor-pointer hover:opacity-90 transition" onclick="window.open(this.src)">
                        @else
                        <a href="{{ asset('storage/' . $msg['attachment']) }}" target="_blank" class="flex items-center gap-2 px-3 py-2 bg-gray-100 rounded-xl hover:bg-gray-200 transition">
                            <i class="fas fa-file text-gray-500"></i>
                            <span class="text-sm text-gray-700">Attachment</span>
                        </a>
                        @endif
                    </div>
                    @endif
                    @if($msg['message'])
                    <div class="px-4 py-2.5 rounded-2xl {{ $msg['sender_id'] === auth()->id() ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-br-md' : 'bg-white shadow-sm border border-gray-100 text-gray-800 rounded-bl-md' }}">
                        <p class="text-sm whitespace-pre-wrap break-words">{!! $this->formatMessage($msg['message']) !!}</p>
                    </div>
                    @endif
                    <p class="text-[10px] text-gray-400 mt-1 {{ $msg['sender_id'] === auth()->id() ? 'text-right' : '' }}">
                        {{ \Carbon\Carbon::parse($msg['created_at'])->format('H:i') }}
                        @if($msg['sender_id'] === auth()->id())
                        <i class="fas {{ $msg['read_at'] ? 'fa-check-double text-blue-400' : 'fa-check' }} ml-1"></i>
                        @endif
                    </p>
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center h-full text-gray-400">
                <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mb-3">
                    <i class="fas fa-comments text-2xl text-blue-300"></i>
                </div>
                <p class="text-sm">Belum ada pesan</p>
                <p class="text-xs">Mulai percakapan dengan {{ $activeChatUser->name }}</p>
            </div>
            @endforelse
        </div>

        {{-- Input Area --}}
        <div class="p-3 border-t border-gray-100 bg-white flex-shrink-0">
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
            <form wire:submit="sendMessage" class="flex items-end gap-2">
                <div class="flex-1 relative">
                    <textarea wire:model="message" 
                              placeholder="Ketik pesan..." 
                              rows="1"
                              class="w-full px-4 py-2.5 bg-gray-100 border-0 rounded-xl text-sm resize-none focus:ring-2 focus:ring-blue-500/30 focus:bg-white transition"
                              onkeydown="if(event.key === 'Enter' && !event.shiftKey) { event.preventDefault(); $wire.sendMessage(); }"></textarea>
                </div>
                <label class="w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center cursor-pointer transition">
                    <i class="fas fa-image text-gray-500"></i>
                    <input type="file" wire:model="attachment" class="hidden" accept="image/*,.pdf,.doc,.docx">
                </label>
                <button type="submit" class="w-10 h-10 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-500/30 transition">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>

        @elseif($selectedBranch)
        {{-- Branch Contacts List --}}
        <div class="flex-1 overflow-y-auto">
            <div class="p-3 border-b border-gray-100">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input wire:model.live.debounce.300ms="searchContact" type="text" placeholder="Cari staff..." class="w-full pl-9 pr-4 py-2 bg-gray-100 border-0 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/30">
                </div>
            </div>
            @forelse($this->contacts as $user)
            <button wire:click="openChat({{ $user->id }})" class="w-full px-4 py-3 flex items-center gap-3 hover:bg-blue-50 transition border-b border-gray-50">
                <div class="relative flex-shrink-0">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random" class="w-11 h-11 rounded-full">
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

        {{-- Content --}}
        <div class="flex-1 overflow-y-auto">
            @if($activeTab === 'conversations')
            {{-- Conversations List --}}
            @forelse($this->conversations as $conv)
            <button wire:click="openChat({{ $conv['user']->id }})" class="w-full px-4 py-3 flex items-center gap-3 hover:bg-gray-50 transition border-b border-gray-50">
                <div class="relative flex-shrink-0">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($conv['user']->name) }}&background=random" class="w-11 h-11 rounded-full">
                    <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full border-2 border-white {{ $conv['user']->is_online ? 'bg-emerald-400' : 'bg-gray-300' }}"></span>
                </div>
                <div class="flex-1 min-w-0 text-left">
                    <div class="flex items-center justify-between">
                        <p class="font-semibold text-gray-900 text-sm truncate">{{ $conv['user']->name }}</p>
                        <span class="text-[10px] text-gray-400">{{ $conv['latest']->created_at->diffForHumans(short: true) }}</span>
                    </div>
                    <div class="flex items-center justify-between mt-0.5">
                        <p class="text-xs text-gray-500 truncate pr-2">
                            @if($conv['latest']->sender_id === auth()->id())<span class="text-gray-400">Anda: </span>@endif
                            {{ $conv['latest']->attachment ? '📎 Attachment' : Str::limit($conv['latest']->message, 30) }}
                        </p>
                        @if($conv['unread'] > 0)
                        <span class="w-5 h-5 bg-blue-600 rounded-full text-[10px] text-white font-bold flex items-center justify-center flex-shrink-0">{{ $conv['unread'] }}</span>
                        @endif
                    </div>
                    <p class="text-[10px] text-gray-400 mt-0.5">
                        <i class="fas fa-building mr-1"></i>{{ $conv['user']->branch?->name ?? 'Admin' }}
                    </p>
                </div>
            </button>
            @empty
            <div class="flex flex-col items-center justify-center h-full text-gray-400 p-6">
                <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mb-3">
                    <i class="fas fa-inbox text-2xl text-blue-300"></i>
                </div>
                <p class="text-sm font-medium">Belum ada percakapan</p>
                <p class="text-xs text-center mt-1">Pilih cabang untuk memulai chat</p>
                <button wire:click="setTab('branches')" class="mt-3 px-4 py-2 bg-blue-100 text-blue-700 rounded-lg text-sm font-medium hover:bg-blue-200 transition">
                    <i class="fas fa-building mr-1"></i>Lihat Cabang
                </button>
            </div>
            @endforelse

            @elseif($activeTab === 'branches')
            {{-- Branches Grid --}}
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
            {{-- All Contacts List --}}
            <div class="p-3 border-b border-gray-100">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input wire:model.live.debounce.300ms="searchContact" type="text" placeholder="Cari staff..." class="w-full pl-9 pr-4 py-2 bg-gray-100 border-0 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/30">
                </div>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($this->contacts as $branchName => $users)
                <div class="py-2">
                    <div class="px-4 py-2 bg-gradient-to-r from-slate-50 to-white sticky top-0">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-building text-blue-400"></i>{{ $branchName }}
                            <span class="text-gray-400 font-normal">({{ $users->count() }})</span>
                        </p>
                    </div>
                    @foreach($users as $user)
                    <button wire:click="openChat({{ $user->id }})" class="w-full px-4 py-2.5 flex items-center gap-3 hover:bg-blue-50 transition">
                        <div class="relative flex-shrink-0">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random" class="w-10 h-10 rounded-full">
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
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('scrollToBottom', () => {
                setTimeout(() => {
                    const el = document.getElementById('chatMessages');
                    if (el) el.scrollTop = el.scrollHeight;
                }, 100);
            });
        });
    </script>
</div>
