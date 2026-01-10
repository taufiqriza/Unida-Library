<div>
    @php
        $whatsappNumber = \App\Models\Setting::get('contact_whatsapp', '6285183053934');
    @endphp
    
    {{-- Floating Button --}}
    <div class="fixed bottom-24 lg:bottom-6 right-6 z-50" x-data="{ showOptions: false }">
        @if(!$isOpen)
            <div class="relative">
                {{-- Options Popup --}}
                <div x-show="showOptions" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     @click.away="showOptions = false"
                     class="absolute bottom-16 right-0 bg-white rounded-xl shadow-2xl border p-2 w-64">
                    <p class="text-xs text-gray-500 px-3 py-2 font-medium">Hubungi Kami</p>
                    
                    <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank"
                       class="flex items-center gap-3 px-3 py-2.5 hover:bg-gray-50 rounded-lg transition">
                        <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fab fa-whatsapp text-white text-lg"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800 text-sm">WhatsApp</p>
                            <p class="text-xs text-gray-500">Chat langsung</p>
                        </div>
                    </a>
                    
                    <button wire:click="openChat" @click="showOptions = false"
                            class="w-full flex items-center gap-3 px-3 py-2.5 hover:bg-gray-50 rounded-lg transition">
                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-comments text-white text-lg"></i>
                        </div>
                        <div class="text-left">
                            <p class="font-medium text-gray-800 text-sm">Chat System</p>
                            <p class="text-xs text-gray-500">
                                @auth('member')
                                    Terhubung dengan staff
                                @else
                                    Login diperlukan
                                @endauth
                            </p>
                        </div>
                    </button>
                </div>
                
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

    {{-- Chat Window (Larger) --}}
    @if($isOpen)
    <div class="fixed bottom-4 right-4 z-50 w-[360px] sm:w-[420px] h-[600px] bg-white rounded-2xl shadow-2xl border flex flex-col overflow-hidden"
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
                    @if($room && $room->topic && !$showTopicSelector)
                    <button wire:click="$set('showTopicSelector', true)" class="text-xs text-blue-100 hover:text-white flex items-center gap-1">
                        {{ $topics[$room->topic]['label'] ?? 'Support' }}
                        <i class="fas fa-pen text-[8px]"></i>
                    </button>
                    @else
                    <p class="text-xs text-blue-100">Online</p>
                    @endif
                </div>
            </div>
            <button wire:click="closeChat" class="w-8 h-8 hover:bg-white/20 rounded-full flex items-center justify-center transition">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Topic Selector --}}
        @if($showTopicSelector)
        <div class="flex-1 p-4 overflow-y-auto">
            <p class="text-gray-600 text-sm mb-4">Pilih keperluan Anda:</p>
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
                    <div class="flex justify-center">
                        <span class="px-3 py-1 bg-gray-100 text-gray-500 text-xs rounded-full">
                            {{ $msg['message'] }}
                        </span>
                    </div>
                @elseif($msg['type'] === 'bot')
                    {{-- Bot Message --}}
                    @php
                        $isAiResponse = str_starts_with($msg['message'] ?? '', '🤖');
                    @endphp
                    <div class="flex justify-start gap-1.5">
                        <div class="w-6 h-6 {{ $isAiResponse ? 'bg-gradient-to-br from-indigo-500 to-purple-600' : 'bg-violet-500' }} rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-robot text-white text-[10px]"></i>
                        </div>
                        <div class="max-w-[85%] {{ $isAiResponse ? 'bg-gradient-to-br from-indigo-50 to-purple-50 border-indigo-100' : 'bg-violet-50 border-violet-100' }} border px-3 py-2 rounded-xl rounded-tl-sm">
                            @if($isAiResponse)
                            <p class="text-[9px] text-indigo-500 font-semibold mb-1 flex items-center gap-1">
                                <i class="fas fa-sparkles"></i> UNIDA Library AI
                            </p>
                            @endif
                            <div class="text-xs text-gray-700 leading-relaxed whitespace-pre-line">{!! preg_replace('/\*\*(.+?)\*\*/', '<strong class="' . ($isAiResponse ? 'text-indigo-700' : 'text-violet-700') . '">$1</strong>', e(ltrim($msg['message'], '🤖 '))) !!}</div>
                            <p class="text-[9px] {{ $isAiResponse ? 'text-indigo-400' : 'text-violet-400' }} mt-1">{{ \Carbon\Carbon::parse($msg['created_at'])->format('H:i') }}</p>
                        </div>
                    </div>
                @elseif(is_null($msg['sender_id']))
                    {{-- Member Message --}}
                    <div class="flex justify-end">
                        <div class="max-w-[75%]">
                            @if(!empty($msg['attachment']))
                                <img src="{{ Storage::url($msg['attachment']) }}" 
                                     class="rounded-xl mb-1 max-h-48 cursor-pointer" 
                                     onclick="window.dispatchEvent(new CustomEvent('show-image', {detail: '{{ Storage::url($msg['attachment']) }}'}))">
                            @endif
                            @if($msg['message'])
                            <div class="bg-blue-500 text-white px-4 py-2.5 rounded-2xl rounded-br-sm text-sm">
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
                    <div class="flex justify-start gap-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user-tie text-white text-xs"></i>
                        </div>
                        <div class="max-w-[75%]">
                            <p class="text-[10px] text-blue-600 mb-0.5 font-semibold flex items-center gap-1">
                                <i class="fas fa-user-tie text-[8px]"></i> {{ $msg['sender']['name'] ?? 'Pustakawan' }}
                            </p>
                            @if(!empty($msg['attachment']))
                                <img src="{{ Storage::url($msg['attachment']) }}" 
                                     class="rounded-xl mb-1 max-h-48 cursor-pointer"
                                     onclick="window.dispatchEvent(new CustomEvent('show-image', {detail: '{{ Storage::url($msg['attachment']) }}'}))">
                            @endif
                            @if(!empty($msg['voice_path']))
                                <audio controls class="h-10 mb-1 w-full">
                                    <source src="{{ Storage::url($msg['voice_path']) }}">
                                </audio>
                            @endif
                            @if($msg['message'])
                            <div class="bg-blue-50 border border-blue-100 text-gray-800 px-3 py-2 rounded-xl rounded-tl-sm text-sm">
                                {{ $msg['message'] }}
                            </div>
                            @endif
                            <p class="text-[9px] text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($msg['created_at'])->format('H:i') }}</p>
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

        {{-- Quick Questions + Input --}}
        <div class="border-t bg-gray-50">
            @if(!$connectedToStaff)
            {{-- Connect to Staff Button --}}
            <div class="px-3 pt-2">
                <button type="button" wire:click="connectToStaff" class="w-full py-2.5 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white text-sm font-semibold rounded-xl flex items-center justify-center gap-2 shadow-sm transition">
                    <i class="fas fa-headset"></i> Hubungi Pustakawan Langsung
                </button>
            </div>
            {{-- Quick Questions --}}
            <div class="px-3 pt-2 flex gap-2 overflow-x-auto scrollbar-hide">
                <button type="button" wire:click="$set('newMessage', 'unggah')" class="px-3 py-1 bg-white border border-gray-200 rounded-full text-xs text-gray-600 hover:bg-violet-50 hover:border-violet-200 whitespace-nowrap">📤 Unggah</button>
                <button type="button" wire:click="$set('newMessage', 'plagiasi')" class="px-3 py-1 bg-white border border-gray-200 rounded-full text-xs text-gray-600 hover:bg-violet-50 hover:border-violet-200 whitespace-nowrap">🔍 Plagiasi</button>
                <button type="button" wire:click="$set('newMessage', 'bebas pustaka')" class="px-3 py-1 bg-white border border-gray-200 rounded-full text-xs text-gray-600 hover:bg-violet-50 hover:border-violet-200 whitespace-nowrap">📜 Bebas Pustaka</button>
                <button type="button" wire:click="$set('newMessage', 'pinjam')" class="px-3 py-1 bg-white border border-gray-200 rounded-full text-xs text-gray-600 hover:bg-violet-50 hover:border-violet-200 whitespace-nowrap">📚 Pinjam</button>
                <button type="button" wire:click="$set('newMessage', 'jam')" class="px-3 py-1 bg-white border border-gray-200 rounded-full text-xs text-gray-600 hover:bg-violet-50 hover:border-violet-200 whitespace-nowrap">🕐 Jam</button>
            </div>
            @else
            {{-- Connected to Staff Indicator --}}
            <div class="px-3 pt-2">
                <div class="py-2 px-3 bg-green-50 border border-green-200 rounded-xl flex items-center gap-2 text-green-700 text-sm">
                    <i class="fas fa-check-circle"></i>
                    <span>Terhubung dengan pustakawan</span>
                </div>
            </div>
            @endif
            {{-- Image Preview --}}
            @if($image)
            <div class="px-3 pt-2">
                <div class="relative inline-block">
                    <img src="{{ $image->temporaryUrl() }}" class="h-16 rounded-lg">
                    <button wire:click="$set('image', null)" 
                            class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 text-white rounded-full text-xs flex items-center justify-center">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            @endif
            
            {{-- Input --}}
            <form wire:submit="sendMessage" class="p-3 flex items-center gap-2">
                <label class="w-10 h-10 bg-gray-200 hover:bg-gray-300 rounded-full flex items-center justify-center cursor-pointer transition flex-shrink-0">
                    <i class="fas fa-image text-gray-500"></i>
                    <input type="file" wire:model="image" accept="image/*" class="hidden">
                </label>
                
                <input type="text" wire:model="newMessage" 
                       placeholder="Ketik pesan..." 
                       class="flex-1 px-4 py-2.5 bg-white border rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                
                <button type="submit" 
                        class="w-10 h-10 bg-blue-500 hover:bg-blue-600 text-white rounded-full flex items-center justify-center transition flex-shrink-0">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
        @endif
        
        {{-- Image Modal --}}
        <div x-data="{ show: false, src: '' }" 
             x-on:show-image.window="src = $event.detail; show = true"
             x-show="show"
             x-cloak
             class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/90 p-4"
             @click="show = false"
             @keydown.escape.window="show = false">
            <img :src="src" class="max-w-full max-h-full rounded-lg shadow-2xl" @click.stop>
            <button @click="show = false" class="absolute top-4 right-4 w-12 h-12 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center text-white transition">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
    </div>
    @endif
</div>
