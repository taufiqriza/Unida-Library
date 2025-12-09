<div x-data="{
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
        
        document.querySelectorAll('input').forEach(input => {
            const key = input.closest('[wire\\:key]')?.getAttribute('wire:key') || '';
            if (key.includes('classification') || input.id?.includes('classification')) {
                input.value = this.selectedCode;
                input.dispatchEvent(new Event('input', { bubbles: true }));
                input.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
        
        document.querySelector('.fi-modal-close-btn')?.click();
    }
}">
    {{-- Main Classes Grid --}}
    <template x-if="search.length < 2 && results.length === 0">
        <div>
            <div class="grid grid-cols-2 gap-2 mb-4">
                <template x-for="cls in [
                    {code: '000', label: 'Karya Umum & Komputer', bg: 'bg-pink-100 dark:bg-pink-950/60', color: 'text-pink-700 dark:text-pink-300', icon: '💻'},
                    {code: '100', label: 'Filsafat & Psikologi', bg: 'bg-orange-100 dark:bg-orange-950/60', color: 'text-orange-700 dark:text-orange-300', icon: '🧠'},
                    {code: '200', label: 'Agama', bg: 'bg-emerald-100 dark:bg-emerald-950/60', color: 'text-emerald-700 dark:text-emerald-300', icon: '🕌'},
                    {code: '2X0', label: 'Islam (Umum)', bg: 'bg-teal-100 dark:bg-teal-950/60', color: 'text-teal-700 dark:text-teal-300', icon: '☪️'},
                    {code: '300', label: 'Ilmu Sosial', bg: 'bg-amber-100 dark:bg-amber-950/60', color: 'text-amber-700 dark:text-amber-300', icon: '👥'},
                    {code: '400', label: 'Bahasa', bg: 'bg-lime-100 dark:bg-lime-950/60', color: 'text-lime-700 dark:text-lime-300', icon: '🗣️'},
                    {code: '500', label: 'Sains & Matematika', bg: 'bg-cyan-100 dark:bg-cyan-950/60', color: 'text-cyan-700 dark:text-cyan-300', icon: '🔬'},
                    {code: '600', label: 'Teknologi', bg: 'bg-blue-100 dark:bg-blue-950/60', color: 'text-blue-700 dark:text-blue-300', icon: '⚙️'},
                    {code: '700', label: 'Seni & Olahraga', bg: 'bg-violet-100 dark:bg-violet-950/60', color: 'text-violet-700 dark:text-violet-300', icon: '🎨'},
                    {code: '800', label: 'Sastra', bg: 'bg-fuchsia-100 dark:bg-fuchsia-950/60', color: 'text-fuchsia-700 dark:text-fuchsia-300', icon: '📚'},
                    {code: '900', label: 'Sejarah & Geografi', bg: 'bg-slate-100 dark:bg-slate-800/80', color: 'text-slate-700 dark:text-slate-300', icon: '🌍'},
                ]" :key="cls.code">
                    <button 
                        type="button"
                        @click="selectClass(cls.code)"
                        :class="cls.bg"
                        class="flex items-center gap-3 p-3 rounded-xl border border-transparent hover:border-gray-300 dark:hover:border-gray-600 cursor-pointer text-left transition-all duration-150 hover:scale-[1.02] hover:shadow-md"
                    >
                        <span class="text-xl flex-shrink-0" x-text="cls.icon"></span>
                        <div class="flex-1 min-w-0">
                            <div :class="cls.color" class="text-lg font-extrabold leading-none" x-text="cls.code"></div>
                            <div :class="cls.color" class="text-[11px] opacity-80 truncate mt-0.5" x-text="cls.label"></div>
                        </div>
                        <svg :class="cls.color" class="w-4 h-4 opacity-50 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </template>
            </div>
            <p class="text-center text-xs text-gray-400 dark:text-gray-500">Klik kelas utama untuk melihat sub-klasifikasi, atau ketik di kotak pencarian</p>
        </div>
    </template>

    {{-- Search Box --}}
    <div class="mb-4">
        <div class="relative">
            <input 
                type="text" 
                x-model="search"
                @input.debounce.300ms="doSearch()"
                placeholder="Ketik nomor atau kata kunci (min. 2 karakter)..."
                class="w-full h-12 pl-11 pr-11 text-base border-2 border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition"
                autofocus
            >
            <div class="absolute left-0 top-0 h-12 w-11 flex items-center justify-center pointer-events-none">
                <template x-if="loading">
                    <svg class="w-5 h-5 text-primary-500 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </template>
                <template x-if="!loading">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </template>
            </div>
            <div class="absolute right-0 top-0 h-12 w-11 flex items-center justify-center">
                <button 
                    x-show="search.length > 0"
                    @click="search = ''; results = [];"
                    type="button"
                    class="w-7 h-7 flex items-center justify-center rounded-md text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Results --}}
    <template x-if="results.length > 0">
        <div>
            <div class="flex items-center justify-between px-3 py-2 mb-3 rounded-lg bg-primary-50 dark:bg-primary-900/30">
                <span class="text-sm text-primary-700 dark:text-primary-300">
                    <strong x-text="results.length"></strong> hasil ditemukan
                </span>
                <button 
                    @click="search = ''; results = [];"
                    type="button"
                    class="text-xs text-primary-600 dark:text-primary-400 hover:underline"
                >Kembali ke kelas utama</button>
            </div>
            <div class="max-h-72 overflow-y-auto border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-800/50">
                <template x-for="ddc in results" :key="ddc.code">
                    <button 
                        type="button"
                        @click="select(ddc.code, ddc.description.substring(0, 60))"
                        :class="selectedCode === ddc.code ? 'bg-primary-50 dark:bg-primary-900/40 border-l-4 border-l-primary-500' : 'border-l-4 border-l-transparent hover:bg-gray-50 dark:hover:bg-gray-700/50'"
                        class="w-full flex items-start gap-3 p-3 text-left border-b border-gray-100 dark:border-gray-700/50 last:border-b-0 transition"
                    >
                        <span 
                            :class="selectedCode === ddc.code ? 'bg-primary-500 text-white' : 'bg-primary-100 dark:bg-primary-900/50 text-primary-700 dark:text-primary-300'"
                            class="flex-shrink-0 px-2.5 py-1 font-mono font-bold text-sm rounded-lg transition"
                            x-text="ddc.code"
                        ></span>
                        <span class="flex-1 text-sm leading-relaxed text-gray-700 dark:text-gray-300" x-text="ddc.description.substring(0, 120) + (ddc.description.length > 120 ? '...' : '')"></span>
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
        <div class="py-8 text-center">
            <div class="w-14 h-14 mx-auto mb-3 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center">
                <svg class="w-7 h-7 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-gray-600 dark:text-gray-400 text-sm">Tidak ada hasil untuk "<strong x-text="search"></strong>"</p>
            <p class="text-gray-400 dark:text-gray-500 text-xs mt-1">Coba kata kunci lain</p>
        </div>
    </template>

    {{-- Footer with Selection --}}
    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between gap-4">
        <div class="flex-1 min-w-0">
            <template x-if="selectedCode">
                <div class="flex items-center gap-2 px-3 py-2.5 bg-primary-50 dark:bg-primary-900/30 border border-primary-200 dark:border-primary-800 rounded-lg">
                    <span class="px-2 py-0.5 bg-primary-500 text-white font-mono font-bold text-sm rounded" x-text="selectedCode"></span>
                    <span class="text-sm text-primary-700 dark:text-primary-300 truncate" x-text="selectedDesc"></span>
                </div>
            </template>
            <template x-if="!selectedCode">
                <span class="text-sm text-gray-400 dark:text-gray-500">Pilih klasifikasi dari daftar</span>
            </template>
        </div>
        <button 
            type="button"
            @click="apply()"
            :disabled="!selectedCode"
            :class="selectedCode ? 'bg-primary-600 hover:bg-primary-700 shadow-lg shadow-primary-500/30' : 'bg-gray-300 dark:bg-gray-700 cursor-not-allowed'"
            class="px-5 py-2.5 text-white font-semibold text-sm rounded-lg flex items-center gap-2 transition"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Gunakan
        </button>
    </div>
</div>
