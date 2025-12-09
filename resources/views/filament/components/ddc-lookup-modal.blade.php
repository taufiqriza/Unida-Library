<div 
    x-data="{
        search: '',
        results: [],
        selectedCode: null,
        selectedDesc: '',
        loading: false,
        
        async doSearch() {
            if (this.search.length < 2) {
                this.results = [];
                return;
            }
            this.loading = true;
            try {
                const res = await fetch('/api/ddc/search?q=' + encodeURIComponent(this.search) + '&limit=25');
                this.results = await res.json();
            } catch (e) {
                this.results = [];
            }
            this.loading = false;
        },
        
        selectClass(code) {
            this.search = code;
            this.doSearch();
        },
        
        select(code, desc) {
            this.selectedCode = code;
            this.selectedDesc = desc;
        },
        
        apply() {
            if (!this.selectedCode) return;
            
            // Find classification input and set value
            document.querySelectorAll('input').forEach(input => {
                const key = input.closest('[wire\\:key]')?.getAttribute('wire:key') || '';
                if (key.includes('classification') || input.id?.includes('classification')) {
                    input.value = this.selectedCode;
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
            
            // Close modal
            document.querySelector('.fi-modal-close-btn')?.click();
        }
    }"
    x-init="$refs.searchInput?.focus()"
>
    {{-- Search --}}
    <div class="relative mb-4">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <template x-if="loading">
                <svg class="animate-spin h-5 w-5 text-primary-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </template>
            <template x-if="!loading">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </template>
        </div>
        <input 
            type="text" 
            x-model="search"
            x-ref="searchInput"
            @input.debounce.300ms="doSearch()"
            placeholder="Ketik nomor atau kata kunci (min. 2 karakter)..."
            class="w-full pl-10 pr-10 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
        >
        <button 
            x-show="search.length > 0"
            @click="search = ''; results = [];"
            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Content --}}
    <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden" style="max-height: 350px; overflow-y: auto;">
        {{-- Main Classes --}}
        <template x-if="search.length < 2 && results.length === 0">
            <div class="p-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Kelas Utama DDC</p>
                <div class="grid grid-cols-2 gap-2">
                    <template x-for="cls in [
                        {code: '000', label: 'Karya Umum & Komputer', bg: 'bg-rose-500'},
                        {code: '100', label: 'Filsafat & Psikologi', bg: 'bg-orange-500'},
                        {code: '200', label: 'Agama', bg: 'bg-emerald-500'},
                        {code: '300', label: 'Ilmu Sosial', bg: 'bg-yellow-500'},
                        {code: '400', label: 'Bahasa', bg: 'bg-lime-500'},
                        {code: '500', label: 'Sains & Matematika', bg: 'bg-cyan-500'},
                        {code: '600', label: 'Teknologi', bg: 'bg-blue-500'},
                        {code: '700', label: 'Seni & Olahraga', bg: 'bg-violet-500'},
                        {code: '800', label: 'Sastra', bg: 'bg-fuchsia-500'},
                        {code: '900', label: 'Sejarah & Geografi', bg: 'bg-slate-500'},
                    ]" :key="cls.code">
                        <button 
                            type="button"
                            @click="selectClass(cls.code)"
                            class="flex items-center gap-2 p-2 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-primary-500 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition text-left"
                        >
                            <span :class="cls.bg" class="text-white font-bold text-xs w-9 h-9 rounded flex items-center justify-center" x-text="cls.code"></span>
                            <span class="text-xs text-gray-700 dark:text-gray-300 flex-1" x-text="cls.label"></span>
                        </button>
                    </template>
                </div>
            </div>
        </template>

        {{-- Results --}}
        <template x-if="results.length > 0">
            <div>
                <div class="bg-gray-50 dark:bg-gray-800 px-3 py-2 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-10">
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        <span class="font-semibold text-primary-600" x-text="results.length"></span> hasil
                    </span>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    <template x-for="ddc in results" :key="ddc.code">
                        <button 
                            type="button"
                            @click="select(ddc.code, ddc.description.substring(0, 60))"
                            :class="selectedCode === ddc.code ? 'bg-primary-50 dark:bg-primary-900/30 border-l-4 border-primary-500' : 'border-l-4 border-transparent hover:bg-gray-50 dark:hover:bg-gray-800'"
                            class="w-full flex items-start gap-3 p-3 text-left transition"
                        >
                            <span class="flex-shrink-0 px-2 py-1 bg-primary-100 dark:bg-primary-900/50 text-primary-700 dark:text-primary-300 font-mono font-bold rounded text-sm" x-text="ddc.code"></span>
                            <span class="flex-1 text-sm text-gray-700 dark:text-gray-300 leading-relaxed" x-text="ddc.description.substring(0, 100) + (ddc.description.length > 100 ? '...' : '')"></span>
                            <template x-if="selectedCode === ddc.code">
                                <span class="flex-shrink-0 w-5 h-5 bg-primary-500 rounded-full flex items-center justify-center">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </span>
                            </template>
                        </button>
                    </template>
                </div>
            </div>
        </template>

        {{-- No Results --}}
        <template x-if="search.length >= 2 && results.length === 0 && !loading">
            <div class="p-6 text-center">
                <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-gray-500 dark:text-gray-400 text-sm">Tidak ada hasil untuk "<span x-text="search"></span>"</p>
            </div>
        </template>
    </div>

    {{-- Footer --}}
    <div class="mt-4 flex items-center justify-between gap-4">
        <div class="flex-1 min-w-0">
            <template x-if="selectedCode">
                <div class="flex items-center gap-2 text-sm">
                    <span class="text-gray-500">Dipilih:</span>
                    <span class="px-2 py-0.5 bg-primary-100 text-primary-700 font-mono font-bold rounded" x-text="selectedCode"></span>
                    <span class="text-gray-600 dark:text-gray-400 truncate" x-text="selectedDesc"></span>
                </div>
            </template>
            <template x-if="!selectedCode">
                <span class="text-sm text-gray-400">Pilih klasifikasi dari daftar</span>
            </template>
        </div>
        <button 
            type="button"
            @click="apply()"
            :disabled="!selectedCode"
            :class="selectedCode ? 'bg-primary-600 hover:bg-primary-700' : 'bg-gray-300 cursor-not-allowed'"
            class="px-4 py-2 text-white font-medium rounded-lg transition flex items-center gap-2"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Gunakan
        </button>
    </div>
</div>
