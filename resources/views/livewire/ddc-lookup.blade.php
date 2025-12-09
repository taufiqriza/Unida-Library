<div class="space-y-4">
    {{-- Search Input --}}
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        <input 
            type="text" 
            wire:model.live.debounce.300ms="search"
            placeholder="Cari nomor klasifikasi atau kata kunci (min. 2 karakter)..."
            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            autofocus
        >
    </div>

    {{-- Quick Access - Main Classes --}}
    @if(empty($search))
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Kelas Utama DDC</h4>
        <div class="grid grid-cols-2 gap-2">
            @foreach([
                '000' => 'Karya Umum, Komputer',
                '100' => 'Filsafat & Psikologi',
                '200' => 'Agama',
                '300' => 'Ilmu Sosial',
                '400' => 'Bahasa',
                '500' => 'Sains & Matematika',
                '600' => 'Teknologi',
                '700' => 'Seni & Olahraga',
                '800' => 'Sastra',
                '900' => 'Sejarah & Geografi',
            ] as $code => $label)
            <button 
                type="button"
                wire:click="$set('search', '{{ $code }}')"
                class="flex items-center gap-2 p-2 text-left text-sm bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-primary-500 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition"
            >
                <span class="w-10 h-10 flex items-center justify-center bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400 font-bold rounded-lg text-xs">{{ $code }}</span>
                <span class="text-gray-700 dark:text-gray-300 text-xs">{{ $label }}</span>
            </button>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Results --}}
    @if(!empty($results))
    <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden max-h-96 overflow-y-auto">
        <div class="bg-gray-50 dark:bg-gray-800 px-4 py-2 border-b border-gray-200 dark:border-gray-700">
            <span class="text-sm text-gray-600 dark:text-gray-400">{{ count($results) }} hasil ditemukan</span>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @foreach($results as $ddc)
            <button 
                type="button"
                wire:click="selectDdc('{{ $ddc['code'] }}')"
                class="w-full flex items-start gap-3 p-3 text-left hover:bg-primary-50 dark:hover:bg-primary-900/20 transition group"
            >
                <span class="flex-shrink-0 w-16 h-8 flex items-center justify-center bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400 font-mono font-bold rounded text-sm group-hover:bg-primary-200 dark:group-hover:bg-primary-800/50">
                    {{ $ddc['code'] }}
                </span>
                <span class="flex-1 text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                    {{ \Str::limit($ddc['description'], 200) }}
                </span>
                <span class="flex-shrink-0 text-primary-600 dark:text-primary-400 opacity-0 group-hover:opacity-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            </button>
            @endforeach
        </div>
    </div>
    @elseif(strlen($search) >= 2)
    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p>Tidak ada hasil untuk "{{ $search }}"</p>
        <p class="text-sm mt-1">Coba kata kunci lain</p>
    </div>
    @endif

    {{-- Help Text --}}
    <div class="text-xs text-gray-500 dark:text-gray-400 bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3">
        <strong>Tips:</strong> Ketik nomor klasifikasi (contoh: 004, 297) atau kata kunci (contoh: komputer, islam, ekonomi). Klik hasil untuk mengisi otomatis ke field klasifikasi.
    </div>
</div>
