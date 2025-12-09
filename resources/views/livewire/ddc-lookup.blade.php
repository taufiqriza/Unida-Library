<div>
    @if($isOpen)
    {{-- Modal Backdrop --}}
    <div 
        class="fixed inset-0 z-50 overflow-y-auto"
        x-data="{ }"
        x-init="$nextTick(() => $refs.searchInput?.focus())"
    >
        {{-- Overlay --}}
        <div 
            class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"
            wire:click="closeModal"
        ></div>

        {{-- Modal Content --}}
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-3xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl transform transition-all">
                
                {{-- Header --}}
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-t-2xl px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-white">DDC Lookup</h2>
                                <p class="text-blue-100 text-sm">Dewey Decimal Classification</p>
                            </div>
                        </div>
                        <button 
                            wire:click="closeModal"
                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 hover:bg-white/20 text-white transition"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Search Box --}}
                <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="search"
                            x-ref="searchInput"
                            placeholder="Ketik nomor klasifikasi atau kata kunci..."
                            class="w-full pl-12 pr-4 py-3.5 text-base border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition"
                        >
                        @if($search)
                        <button 
                            wire:click="$set('search', '')"
                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                        @endif
                    </div>
                </div>

                {{-- Content Area --}}
                <div class="max-h-[50vh] overflow-y-auto">
                    @if(empty($search) || strlen($search) < 2)
                    {{-- Main Classes Grid --}}
                    <div class="p-4">
                        <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                            </svg>
                            Kelas Utama DDC
                        </h4>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach([
                                ['000', 'Karya Umum, Komputer', 'bg-red-500'],
                                ['100', 'Filsafat & Psikologi', 'bg-orange-500'],
                                ['200', 'Agama', 'bg-amber-500'],
                                ['300', 'Ilmu Sosial', 'bg-yellow-500'],
                                ['400', 'Bahasa', 'bg-lime-500'],
                                ['500', 'Sains & Matematika', 'bg-green-500'],
                                ['600', 'Teknologi', 'bg-teal-500'],
                                ['700', 'Seni & Olahraga', 'bg-cyan-500'],
                                ['800', 'Sastra', 'bg-blue-500'],
                                ['900', 'Sejarah & Geografi', 'bg-purple-500'],
                            ] as $class)
                            <button 
                                type="button"
                                wire:click="searchByClass('{{ $class[0] }}')"
                                class="flex items-center gap-3 p-3 text-left bg-gray-50 dark:bg-gray-700/50 rounded-xl border-2 border-transparent hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition group"
                            >
                                <span class="w-12 h-12 flex items-center justify-center {{ $class[2] }} text-white font-bold rounded-xl text-sm shadow-lg group-hover:scale-110 transition">
                                    {{ $class[0] }}
                                </span>
                                <span class="flex-1 text-sm font-medium text-gray-700 dark:text-gray-300">{{ $class[1] }}</span>
                                <svg class="w-5 h-5 text-gray-300 group-hover:text-blue-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @elseif(count($results) > 0)
                    {{-- Search Results --}}
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($results as $ddc)
                        <button 
                            type="button"
                            wire:click="selectDdc('{{ $ddc['code'] }}', '{{ addslashes(\Str::limit($ddc['description'], 100)) }}')"
                            class="w-full flex items-start gap-4 p-4 text-left hover:bg-blue-50 dark:hover:bg-blue-900/20 transition group {{ $selectedCode === $ddc['code'] ? 'bg-blue-50 dark:bg-blue-900/30 ring-2 ring-blue-500 ring-inset' : '' }}"
                        >
                            <span class="flex-shrink-0 w-16 h-10 flex items-center justify-center bg-gradient-to-br from-blue-500 to-indigo-600 text-white font-mono font-bold rounded-lg text-sm shadow-md group-hover:shadow-lg transition">
                                {{ $ddc['code'] }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                    {{ \Str::limit($ddc['description'], 150) }}
                                </p>
                            </div>
                            @if($selectedCode === $ddc['code'])
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </span>
                            @endif
                        </button>
                        @endforeach
                    </div>
                    @else
                    {{-- No Results --}}
                    <div class="p-8 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 font-medium">Tidak ada hasil untuk "{{ $search }}"</p>
                        <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Coba kata kunci lain atau nomor klasifikasi</p>
                    </div>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-b-2xl border-t border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            @if($selectedCode)
                            <span class="flex items-center gap-2">
                                <span class="font-medium text-blue-600 dark:text-blue-400">{{ $selectedCode }}</span>
                                <span class="text-gray-400">dipilih</span>
                            </span>
                            @else
                            <span>Pilih klasifikasi untuk mengisi field</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <button 
                                type="button"
                                wire:click="closeModal"
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-500 transition"
                            >
                                Batal
                            </button>
                            <button 
                                type="button"
                                wire:click="confirmSelection"
                                @if(!$selectedCode) disabled @endif
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Gunakan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
