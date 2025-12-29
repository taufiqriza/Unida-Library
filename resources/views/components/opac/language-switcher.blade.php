<div class="relative" x-data="{ open: false }">
    @php
        $flags = [
            'id' => '<svg class="w-4 h-3 rounded-sm shadow-sm" viewBox="0 0 640 480"><path fill="#e70011" d="M0 0h640v240H0z"/><path fill="#fff" d="M0 240h640v240H0z"/></svg>',
            'en' => '<svg class="w-4 h-3 rounded-sm shadow-sm" viewBox="0 0 640 480"><path fill="#012169" d="M0 0h640v480H0z"/><path fill="#FFF" d="m75 0 244 181L562 0h78v62L400 241l240 178v61h-80L320 301 81 480H0v-60l239-178L0 64V0h75z"/><path fill="#C8102E" d="m424 281 216 159v40L369 281h55zm-184 20 6 35L54 480H0l240-179zM640 0v3L391 191l2-44L590 0h50zM0 0l239 176h-60L0 42V0z"/><path fill="#FFF" d="M241 0v480h160V0H241zM0 160v160h640V160H0z"/><path fill="#C8102E" d="M0 193v96h640v-96H0zM273 0v480h96V0h-96z"/></svg>',
            'ar' => '<svg class="w-4 h-3 rounded-sm shadow-sm" viewBox="0 0 640 480"><path fill="#006c35" d="M0 0h640v480H0z"/><path fill="#fff" d="M170 220h300v40H170z"/></svg>',
        ];
    @endphp
    
    {{-- Current Language Button --}}
    <button @click="open = !open" 
            @click.away="open = false"
            class="flex items-center gap-1.5 px-2 py-1 text-[10px] font-medium text-white/90 hover:text-white hover:bg-white/10 rounded transition">
        {!! $flags[$locale] ?? '<i class="fas fa-globe text-xs"></i>' !!}
        <span class="hidden sm:inline uppercase">{{ $locale }}</span>
        <i class="fas fa-chevron-down text-[7px] opacity-60 transition-transform" :class="open ? 'rotate-180' : ''"></i>
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
                    <span class="flex-shrink-0">{!! $flags[$code] ?? '<i class="fas fa-globe text-gray-400"></i>' !!}</span>
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
