<div>
    {{-- Floating Button --}}
    <div class="fixed bottom-6 right-6 z-50" x-data="{ showOptions: false }">
        @if(!$isOpen)
            {{-- Main Button --}}
            <div class="relative">
                {{-- Options Popup --}}
                <div x-show="showOptions" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-2"
                     @click.away="showOptions = false"
                     class="absolute bottom-16 right-0 bg-white rounded-xl shadow-2xl border p-2 w-56">
                    <p class="text-xs text-gray-500 px-3 py-2 font-medium">Hubungi Kami</p>
                    
                    {{-- WhatsApp Option --}}
                    <a href="https://wa.me/6285156789012" target="_blank"
                       class="flex items-center gap-3 px-3 py-2.5 hover:bg-gray-50 rounded-lg transition">
                        <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fab fa-whatsapp text-white text-lg"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800 text-sm">WhatsApp</p>
                            <p class="text-xs text-gray-500">Chat langsung</p>
                        </div>
                    </a>
                    
                    {{-- System Chat Option --}}
                    <button wire:click="openChat" @click="showOptions = false"
                            class="w-full flex items-center gap-3 px-3 py-2.5 hover:bg-gray-50 rounded-lg transition">
                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-comments text-white text-lg"></i>
                        </div>
                        <div class="text-left">
                            <p class="font-medium text-gray-800 text-sm">Chat System</p>
                            <p class="text-xs text-gray-500">
                                @auth
                                    Terhubung dengan staff
                                @else
                                    Login diperlukan
                                @endauth
                            </p>
                        </div>
                    </button>
                </div>
                
                {{-- Floating Button --}}
                <button @click="showOptions = !showOptions"
                        class="w-14 h-14 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-full shadow-lg flex items-center justify-center transition-all hover:scale-105">
                    <i class="fas fa-comment-dots text-xl"></i>
                    @if($this->unreadCount > 0)
                        <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">
                            {{ $this->unreadCount > 9 ? '9+' : $this->unreadCount }}
                        </span>
                    @endif
                </button>
            </div>
        @endif
    </div>

    {{-- Chat Window --}}
    @if($isOpen)
    <div class="fixed bottom-6 right-6 z-50 w-80 sm:w-96 h-[500px] bg-white rounded-2xl shadow-2xl border flex flex-col overflow-hidden"
         x-data="{ scrollToBottom() { $nextTick(() => { const el = document.getElementById('supportMessages'); if(el) el.scrollTop = el.scrollHeight; }); } }"
         x-init="scrollToBottom()"
         @message-sent.window="scrollToBottom()">
        
        {{-- Header --}}
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-headset text-lg"></i>
                </div>
                <div>
                    <h3 class="font-semibold">Layanan Perpustakaan</h3>
                    <p class="text-xs text-blue-100">
                        @if($room && $room->topic)
                            {{ $topics[$room->topic]['label'] ?? 'Support' }}
                        @else
                            Online
                        @endif
                    </p>
                </div>
            </div>
            <button wire:click="closeChat" class="w-8 h-8 hover:bg-white/20 rounded-full flex items-center justify-center transition">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Topic Selector --}}
        @if($showTopicSelector)
        <div class="flex-1 p-4 overflow-y-auto">
            <p class="text-gray-600 text-sm mb-4">Pilih topik pertanyaan Anda:</p>
            <div class="space-y-2">
                @foreach($topics as $key => $topic)
                <button wire:click="selectTopic('{{ $key }}')"
                        class="w-full flex items-center gap-3 p-3 bg-gray-50 hover:bg-blue-50 border hover:border-blue-200 rounded-xl transition">
                    <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                        <i class="fas {{ $topic['icon'] }}"></i>
                    </div>
                    <span class="font-medium text-gray-700">{{ $topic['label'] }}</span>
                </button>
                @endforeach
            </div>
        </div>
        @else
        
        {{-- Messages --}}
        <div id="supportMessages" class="flex-1 p-4 overflow-y-auto space-y-3" wire:poll.5s="loadMessages">
            @forelse($messages as $msg)
                @if($msg['type'] === 'system')
                    {{-- System Message --}}
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-3 text-sm text-blue-800 whitespace-pre-line">
                        {{ $msg['message'] }}
                    </div>
                @elseif($msg['sender_id'] === auth()->id())
                    {{-- My Message --}}
                    <div class="flex justify-end">
                        <div class="max-w-[80%]">
                            @if(!empty($msg['attachment_path']))
                                <img src="{{ Storage::url($msg['attachment_path']) }}" 
                                     class="rounded-xl mb-1 max-h-40 cursor-pointer" 
                                     onclick="window.open(this.src)">
                            @endif
                            @if($msg['message'])
                            <div class="bg-blue-500 text-white px-4 py-2 rounded-2xl rounded-br-md">
                                {{ $msg['message'] }}
                            </div>
                            @endif
                            <p class="text-[10px] text-gray-400 text-right mt-1">
                                {{ \Carbon\Carbon::parse($msg['created_at'])->format('H:i') }}
                            </p>
                        </div>
                    </div>
                @else
                    {{-- Staff Message --}}
                    <div class="flex justify-start">
                        <div class="max-w-[80%]">
                            <p class="text-xs text-gray-500 mb-1 font-medium">
                                {{ $msg['sender']['name'] ?? 'Staff' }}
                            </p>
                            @if(!empty($msg['attachment_path']))
                                <img src="{{ Storage::url($msg['attachment_path']) }}" 
                                     class="rounded-xl mb-1 max-h-40 cursor-pointer"
                                     onclick="window.open(this.src)">
                            @endif
                            @if(!empty($msg['voice_path']))
                                <audio controls class="h-10 mb-1">
                                    <source src="{{ Storage::url($msg['voice_path']) }}">
                                </audio>
                            @endif
                            @if($msg['message'])
                            <div class="bg-gray-100 text-gray-800 px-4 py-2 rounded-2xl rounded-bl-md">
                                {{ $msg['message'] }}
                            </div>
                            @endif
                            <p class="text-[10px] text-gray-400 mt-1">
                                {{ \Carbon\Carbon::parse($msg['created_at'])->format('H:i') }}
                            </p>
                        </div>
                    </div>
                @endif
            @empty
                <div class="text-center text-gray-400 py-8">
                    <i class="fas fa-comments text-4xl mb-2"></i>
                    <p class="text-sm">Mulai percakapan</p>
                </div>
            @endforelse
        </div>

        {{-- Input --}}
        <div class="p-3 border-t bg-gray-50">
            @if($image)
            <div class="mb-2 relative inline-block">
                <img src="{{ $image->temporaryUrl() }}" class="h-16 rounded-lg">
                <button wire:click="$set('image', null)" 
                        class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 text-white rounded-full text-xs">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @endif
            
            <form wire:submit="sendMessage" class="flex items-center gap-2">
                <label class="w-9 h-9 bg-gray-200 hover:bg-gray-300 rounded-full flex items-center justify-center cursor-pointer transition">
                    <i class="fas fa-image text-gray-500 text-sm"></i>
                    <input type="file" wire:model="image" accept="image/*" class="hidden">
                </label>
                
                <input type="text" wire:model="newMessage" 
                       placeholder="Ketik pesan..." 
                       class="flex-1 px-4 py-2 bg-white border rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                
                <button type="submit" 
                        class="w-9 h-9 bg-blue-500 hover:bg-blue-600 text-white rounded-full flex items-center justify-center transition">
                    <i class="fas fa-paper-plane text-sm"></i>
                </button>
            </form>
        </div>
        @endif
    </div>
    @endif
</div>
