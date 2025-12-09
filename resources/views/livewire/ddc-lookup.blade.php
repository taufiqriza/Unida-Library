<div class="ddc-lookup-container">
    {{-- Search Box --}}
    <div class="mb-4">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                @if($search && strlen($search) >= 2)
                <div wire:loading.flex wire:target="search">
                    <svg class="animate-spin h-5 w-5 text-primary-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                @endif
                <svg wire:loading.remove wire:target="search" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search"
                placeholder="Ketik nomor klasifikasi atau kata kunci (min. 2 karakter)..."
                class="w-full pl-12 pr-10 py-3 text-base border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                autofocus
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

    {{-- Content --}}
    <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden" style="max-height: 400px; overflow-y: auto;">
        @if(strlen($search) < 2)
        {{-- Main Classes --}}
        <div class="p-4">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Kelas Utama DDC - Klik untuk melihat sub-kelas</p>
            <div class="grid grid-cols-2 gap-2">
                @foreach([
                    ['000', 'Karya Umum & Komputer', 'bg-rose-500'],
                    ['100', 'Filsafat & Psikologi', 'bg-orange-500'],
                    ['200', 'Agama', 'bg-emerald-500'],
                    ['300', 'Ilmu Sosial', 'bg-yellow-500'],
                    ['400', 'Bahasa', 'bg-lime-500'],
                    ['500', 'Sains & Matematika', 'bg-cyan-500'],
                    ['600', 'Teknologi', 'bg-blue-500'],
                    ['700', 'Seni & Olahraga', 'bg-violet-500'],
                    ['800', 'Sastra', 'bg-fuchsia-500'],
                    ['900', 'Sejarah & Geografi', 'bg-slate-500'],
                ] as $class)
                <button 
                    type="button"
                    wire:click="searchByClass('{{ $class[0] }}')"
                    class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-primary-500 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition text-left"
                >
                    <span class="{{ $class[2] }} text-white font-bold text-sm w-10 h-10 rounded-lg flex items-center justify-center">{{ $class[0] }}</span>
                    <span class="text-sm text-gray-700 dark:text-gray-300 flex-1">{{ $class[1] }}</span>
                </button>
                @endforeach
            </div>
        </div>
        @elseif(count($results) > 0)
        {{-- Results --}}
        <div class="bg-gray-50 dark:bg-gray-800 px-4 py-2 border-b border-gray-200 dark:border-gray-700 sticky top-0">
            <span class="text-sm text-gray-600 dark:text-gray-400">
                <span class="font-semibold text-primary-600">{{ count($results) }}</span> hasil ditemukan
            </span>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @foreach($results as $ddc)
            <button 
                type="button"
                wire:click="selectResult('{{ $ddc['code'] }}', '{{ addslashes(Str::limit($ddc['description'], 80)) }}')"
                class="w-full flex items-start gap-3 p-3 text-left hover:bg-primary-50 dark:hover:bg-primary-900/20 transition {{ $selectedCode === $ddc['code'] ? 'bg-primary-50 dark:bg-primary-900/30 ring-2 ring-primary-500 ring-inset' : '' }}"
            >
                <span class="flex-shrink-0 px-2 py-1 bg-primary-100 dark:bg-primary-900/50 text-primary-700 dark:text-primary-300 font-mono font-bold rounded text-sm">
                    {{ $ddc['code'] }}
                </span>
                <span class="flex-1 text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                    {{ Str::limit($ddc['description'], 120) }}
                </span>
                @if($selectedCode === $ddc['code'])
                <span class="flex-shrink-0 w-5 h-5 bg-primary-500 rounded-full flex items-center justify-center">
                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </span>
                @endif
            </button>
            @endforeach
        </div>
        @else
        {{-- No Results --}}
        <div class="p-8 text-center">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-gray-500 dark:text-gray-400">Tidak ada hasil untuk "{{ $search }}"</p>
        </div>
        @endif
    </div>

    {{-- Footer --}}
    <div class="mt-4 flex items-center justify-between">
        <div class="flex-1">
            @if($selectedCode)
            <div class="flex items-center gap-2 text-sm">
                <span class="text-gray-500">Dipilih:</span>
                <span class="px-2 py-1 bg-primary-100 text-primary-700 font-mono font-bold rounded">{{ $selectedCode }}</span>
                <span class="text-gray-600 truncate max-w-xs">{{ $selectedDesc }}</span>
            </div>
            @else
            <span class="text-sm text-gray-400">Pilih klasifikasi dari daftar</span>
            @endif
        </div>
        <button 
            type="button"
            wire:click="applySelection"
            @if(!$selectedCode) disabled @endif
            class="px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Gunakan
        </button>
    </div>
</div>
