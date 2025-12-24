<div class="relative" x-data="{ open: false }">
    {{-- Current Language Button --}}
    <button @click="open = !open" 
            @click.away="open = false"
            class="flex items-center gap-1 px-1.5 py-0.5 text-[10px] font-medium text-white/90 hover:text-white hover:bg-white/10 rounded transition">
        <span class="text-xs">{{ $availableLocales[$locale]['flag'] ?? '🌐' }}</span>
        <span class="hidden sm:inline uppercase">{{ $locale }}</span>
        <i class="fas fa-chevron-down text-[7px] opacity-60" :class="open ? 'rotate-180' : ''"></i>
    </button>
    
    {{-- Dropdown Menu --}}
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
         class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden z-[100]"
         style="display: none;">
        
        <div class="py-1">
            <p class="px-4 py-2 text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100">
                {{ __('opac.select_language') }}
            </p>
            
            @foreach($availableLocales as $code => $langInfo)
                <a href="{{ route('opac.set-locale', $code) }}" 
                   class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition group {{ $locale === $code ? 'bg-primary-50' : '' }}">
                    <span class="text-lg">{{ $langInfo['flag'] }}</span>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900 group-hover:text-primary-600 {{ $locale === $code ? 'text-primary-600' : '' }}">
                            {{ $langInfo['native'] }}
                        </p>
                        <p class="text-xs text-gray-500">{{ $langInfo['name'] }}</p>
                    </div>
                    @if($locale === $code)
                        <i class="fas fa-check text-primary-600 text-xs"></i>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</div>
