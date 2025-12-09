<div x-data="{
    search: '',
    results: [],
    selectedCode: null,
    selectedDesc: '',
    loading: false,
    isDark: document.documentElement.classList.contains('dark'),
    
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
}" x-init="isDark = document.documentElement.classList.contains('dark')">
    {{-- Main Classes Grid --}}
    <template x-if="search.length < 2 && results.length === 0">
        <div>
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem; margin-bottom: 1rem;">
                <template x-for="cls in [
                    {code: '000', label: 'Karya Umum & Komputer', gradient: 'linear-gradient(135deg, #fce7f3 0%, #f9a8d4 100%)', color: '#9d174d', icon: '💻'},
                    {code: '100', label: 'Filsafat & Psikologi', gradient: 'linear-gradient(135deg, #ffedd5 0%, #fdba74 100%)', color: '#9a3412', icon: '🧠'},
                    {code: '200', label: 'Agama', gradient: 'linear-gradient(135deg, #d1fae5 0%, #6ee7b7 100%)', color: '#065f46', icon: '🕌'},
                    {code: '300', label: 'Ilmu Sosial', gradient: 'linear-gradient(135deg, #fef3c7 0%, #fde68a 100%)', color: '#92400e', icon: '👥'},
                    {code: '400', label: 'Bahasa', gradient: 'linear-gradient(135deg, #ecfccb 0%, #bef264 100%)', color: '#3f6212', icon: '🗣️'},
                    {code: '500', label: 'Sains & Matematika', gradient: 'linear-gradient(135deg, #cffafe 0%, #67e8f9 100%)', color: '#155e75', icon: '🔬'},
                    {code: '600', label: 'Teknologi', gradient: 'linear-gradient(135deg, #dbeafe 0%, #93c5fd 100%)', color: '#1e40af', icon: '⚙️'},
                    {code: '700', label: 'Seni & Olahraga', gradient: 'linear-gradient(135deg, #ede9fe 0%, #c4b5fd 100%)', color: '#5b21b6', icon: '🎨'},
                    {code: '800', label: 'Sastra', gradient: 'linear-gradient(135deg, #fae8ff 0%, #e879f9 100%)', color: '#86198f', icon: '📚'},
                    {code: '900', label: 'Sejarah & Geografi', gradient: 'linear-gradient(135deg, #f1f5f9 0%, #cbd5e1 100%)', color: '#334155', icon: '🌍'},
                ]" :key="cls.code">
                    <button 
                        type="button"
                        @click="selectClass(cls.code)"
                        :style="{ background: cls.gradient }"
                        style="display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem; border-radius: 0.75rem; border: none; cursor: pointer; text-align: left; transition: transform 0.15s, box-shadow 0.15s;"
                        @mouseenter="$el.style.transform = 'scale(1.02)'; $el.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)'"
                        @mouseleave="$el.style.transform = 'scale(1)'; $el.style.boxShadow = 'none'"
                    >
                        <span style="font-size: 1.5rem;" x-text="cls.icon"></span>
                        <div style="flex: 1; min-width: 0;">
                            <div :style="{ color: cls.color }" style="font-size: 1.25rem; font-weight: 800; line-height: 1;" x-text="cls.code"></div>
                            <div :style="{ color: cls.color }" style="font-size: 0.7rem; opacity: 0.8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" x-text="cls.label"></div>
                        </div>
                        <svg :style="{ color: cls.color }" style="width: 1rem; height: 1rem; opacity: 0.5;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </template>
            </div>
            <p class="text-center text-xs text-gray-400 dark:text-gray-500">Klik kelas utama untuk melihat sub-klasifikasi, atau ketik di kotak pencarian</p>
        </div>
    </template>

    {{-- Search Box --}}
    <div style="margin-bottom: 1rem;">
        <div style="position: relative;">
            <div style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); pointer-events: none;">
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
            <input 
                type="text" 
                x-model="search"
                @input.debounce.300ms="doSearch()"
                placeholder="Ketik nomor atau kata kunci (min. 2 karakter)..."
                class="w-full py-3.5 pl-10 pr-10 text-base border-2 border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition"
                autofocus
            >
            <button 
                x-show="search.length > 0"
                @click="search = ''; results = [];"
                class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
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
                    class="text-xs text-primary-600 dark:text-primary-400 hover:underline"
                >Kembali ke kelas utama</button>
            </div>
            <div class="max-h-72 overflow-y-auto border border-gray-200 dark:border-gray-600 rounded-xl">
                <template x-for="ddc in results" :key="ddc.code">
                    <button 
                        type="button"
                        @click="select(ddc.code, ddc.description.substring(0, 60))"
                        :class="selectedCode === ddc.code ? 'bg-primary-50 dark:bg-primary-900/40 border-l-4 border-l-primary-500' : 'border-l-4 border-l-transparent hover:bg-gray-50 dark:hover:bg-gray-700/50'"
                        class="w-full flex items-start gap-3 p-3 text-left border-b border-gray-100 dark:border-gray-700 last:border-b-0 transition"
                    >
                        <span 
                            :class="selectedCode === ddc.code ? 'bg-primary-500 text-white' : 'bg-primary-100 dark:bg-primary-900/50 text-primary-700 dark:text-primary-300'"
                            class="flex-shrink-0 px-2.5 py-1 font-mono font-bold text-sm rounded-lg transition"
                            x-text="ddc.code"
                        ></span>
                        <span class="flex-1 text-sm leading-relaxed text-gray-700 dark:text-gray-200" x-text="ddc.description.substring(0, 120) + (ddc.description.length > 120 ? '...' : '')"></span>
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
            <div class="w-14 h-14 mx-auto mb-3 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-gray-600 dark:text-gray-300 text-sm">Tidak ada hasil untuk "<strong x-text="search"></strong>"</p>
            <p class="text-gray-400 dark:text-gray-500 text-xs mt-1">Coba kata kunci lain</p>
        </div>
    </template>

    {{-- Footer with Selection --}}
    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600 flex items-center justify-between gap-4">
        <div class="flex-1 min-w-0">
            <template x-if="selectedCode">
                <div class="flex items-center gap-2 px-3 py-2.5 bg-primary-50 dark:bg-primary-900/30 border border-primary-200 dark:border-primary-700 rounded-lg">
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
            :class="selectedCode ? 'bg-primary-600 hover:bg-primary-700 shadow-lg shadow-primary-500/30' : 'bg-gray-300 dark:bg-gray-600 cursor-not-allowed'"
            class="px-5 py-2.5 text-white font-semibold text-sm rounded-lg flex items-center gap-2 transition"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Gunakan
        </button>
    </div>
</div>
