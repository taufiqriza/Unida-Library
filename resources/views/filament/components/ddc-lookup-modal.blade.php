<div 
    x-data="{
        search: '',
        results: [],
        selectedCode: null,
        selectedDesc: '',
        loading: false,
        mainClasses: [
            { code: '000', label: 'Karya Umum & Komputer', color: 'from-rose-500 to-pink-600', icon: '💻' },
            { code: '100', label: 'Filsafat & Psikologi', color: 'from-orange-500 to-amber-600', icon: '🧠' },
            { code: '200', label: 'Agama', color: 'from-emerald-500 to-teal-600', icon: '🕌' },
            { code: '300', label: 'Ilmu Sosial', color: 'from-yellow-500 to-orange-500', icon: '👥' },
            { code: '400', label: 'Bahasa', color: 'from-lime-500 to-green-600', icon: '🗣️' },
            { code: '500', label: 'Sains & Matematika', color: 'from-cyan-500 to-blue-600', icon: '🔬' },
            { code: '600', label: 'Teknologi', color: 'from-blue-500 to-indigo-600', icon: '⚙️' },
            { code: '700', label: 'Seni & Olahraga', color: 'from-violet-500 to-purple-600', icon: '🎨' },
            { code: '800', label: 'Sastra', color: 'from-fuchsia-500 to-pink-600', icon: '📚' },
            { code: '900', label: 'Sejarah & Geografi', color: 'from-slate-500 to-gray-600', icon: '🌍' },
        ],
        async searchDdc() {
            if (this.search.length < 2) {
                this.results = [];
                return;
            }
            this.loading = true;
            try {
                const response = await fetch(`/api/ddc/search?q=${encodeURIComponent(this.search)}&limit=25`);
                this.results = await response.json();
            } catch (e) {
                console.error(e);
                this.results = [];
            }
            this.loading = false;
        },
        selectClass(code) {
            this.search = code;
            this.searchDdc();
        },
        selectResult(code, desc) {
            this.selectedCode = code;
            this.selectedDesc = desc;
        },
        applySelection() {
            if (this.selectedCode) {
                const allInputs = document.querySelectorAll('input');
                let targetInput = null;
                
                allInputs.forEach(inp => {
                    const wrapper = inp.closest('[wire\\:key]');
                    const wrapperKey = wrapper?.getAttribute('wire:key') || '';
                    const inputId = inp.id || '';
                    const inputName = inp.name || '';
                    
                    if (wrapperKey.includes('classification') || 
                        inputId.includes('classification') || 
                        inputName.includes('classification')) {
                        targetInput = inp;
                    }
                });
                
                if (targetInput) {
                    targetInput.value = this.selectedCode;
                    targetInput.dispatchEvent(new Event('input', { bubbles: true }));
                    targetInput.dispatchEvent(new Event('change', { bubbles: true }));
                    if (targetInput._x_model) {
                        targetInput._x_model.set(this.selectedCode);
                    }
                }
                
                this.$dispatch('close-modal', { id: 'searchDdc' });
                setTimeout(() => {
                    const closeBtn = document.querySelector('.fi-modal-close-btn');
                    if (closeBtn) closeBtn.click();
                }, 100);
            }
        }
    }"
    x-init="$nextTick(() => $refs.searchInput?.focus())"
    class="-m-6"
>
    {{-- Header with Gradient --}}
    <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 px-6 py-5">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-white">DDC Lookup</h2>
                <p class="text-white/80 text-sm">Dewey Decimal Classification - Cari nomor klasifikasi</p>
            </div>
        </div>
    </div>

    {{-- Search Box --}}
    <div class="px-6 py-4 bg-gradient-to-b from-gray-50 to-white dark:from-gray-800 dark:to-gray-900 border-b border-gray-200 dark:border-gray-700">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <template x-if="loading">
                    <svg class="animate-spin h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
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
                @input.debounce.300ms="searchDdc()"
                placeholder="Ketik nomor klasifikasi atau kata kunci..."
                class="w-full pl-12 pr-12 py-4 text-lg border-2 border-gray-200 dark:border-gray-600 rounded-2xl focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 dark:bg-gray-800 dark:text-white transition-all shadow-sm"
            >
            <button 
                x-show="search.length > 0"
                @click="search = ''; results = [];"
                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Content Area --}}
    <div class="max-h-[50vh] overflow-y-auto bg-white dark:bg-gray-900">
        {{-- Main Classes Grid --}}
        <template x-if="search.length < 2 && results.length === 0">
            <div class="p-6">
                <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 mb-4 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900/50 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                    </span>
                    Kelas Utama DDC
                </h4>
                <div class="grid grid-cols-2 gap-3">
                    <template x-for="cls in mainClasses" :key="cls.code">
                        <button 
                            type="button"
                            @click="selectClass(cls.code)"
                            class="group relative overflow-hidden rounded-2xl p-4 text-left transition-all duration-300 hover:scale-[1.02] hover:shadow-xl"
                        >
                            {{-- Background Gradient --}}
                            <div :class="'absolute inset-0 bg-gradient-to-br ' + cls.color + ' opacity-90 group-hover:opacity-100 transition-opacity'"></div>
                            
                            {{-- Content --}}
                            <div class="relative flex items-center gap-3">
                                <span class="text-3xl" x-text="cls.icon"></span>
                                <div class="flex-1 min-w-0">
                                    <span class="block text-2xl font-black text-white/90" x-text="cls.code"></span>
                                    <span class="block text-sm font-medium text-white/80 truncate" x-text="cls.label"></span>
                                </div>
                                <svg class="w-5 h-5 text-white/60 group-hover:text-white group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </button>
                    </template>
                </div>
            </div>
        </template>

        {{-- Search Results --}}
        <template x-if="results.length > 0">
            <div>
                <div class="px-6 py-3 bg-gray-50 dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 sticky top-0">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">
                        <span class="text-indigo-600 dark:text-indigo-400 font-bold" x-text="results.length"></span> hasil ditemukan
                    </span>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    <template x-for="ddc in results" :key="ddc.id">
                        <button 
                            type="button"
                            @click="selectResult(ddc.code, ddc.description)"
                            :class="selectedCode === ddc.code ? 'bg-indigo-50 dark:bg-indigo-900/30 border-l-4 border-indigo-500' : 'border-l-4 border-transparent hover:bg-gray-50 dark:hover:bg-gray-800'"
                            class="w-full flex items-start gap-4 p-4 text-left transition-all"
                        >
                            <span 
                                :class="selectedCode === ddc.code ? 'from-indigo-500 to-purple-600 scale-110' : 'from-gray-400 to-gray-500'"
                                class="flex-shrink-0 w-16 h-12 flex items-center justify-center bg-gradient-to-br text-white font-mono font-bold rounded-xl text-sm shadow-lg transition-all"
                                x-text="ddc.code"
                            ></span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed" x-text="ddc.description.substring(0, 180) + (ddc.description.length > 180 ? '...' : '')"></p>
                            </div>
                            <template x-if="selectedCode === ddc.code">
                                <span class="flex-shrink-0 w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
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
            <div class="p-12 text-center">
                <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-gray-600 dark:text-gray-400 font-semibold text-lg">Tidak ada hasil</p>
                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Coba kata kunci lain untuk "<span class="text-indigo-500" x-text="search"></span>"</p>
            </div>
        </template>
    </div>

    {{-- Footer --}}
    <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 border-t border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between gap-4">
            {{-- Selected Preview --}}
            <div class="flex-1 min-w-0">
                <template x-if="selectedCode">
                    <div class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-xl border-2 border-indigo-200 dark:border-indigo-800 shadow-sm">
                        <span class="px-3 py-1.5 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-mono font-bold rounded-lg shadow" x-text="selectedCode"></span>
                        <span class="text-sm text-gray-600 dark:text-gray-400 truncate" x-text="selectedDesc.substring(0, 50) + '...'"></span>
                    </div>
                </template>
                <template x-if="!selectedCode">
                    <div class="flex items-center gap-2 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm">Pilih klasifikasi dari daftar</span>
                    </div>
                </template>
            </div>
            
            {{-- Action Button --}}
            <button 
                type="button"
                @click="applySelection()"
                :disabled="!selectedCode"
                :class="selectedCode ? 'from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 shadow-lg shadow-indigo-500/30' : 'from-gray-300 to-gray-400 cursor-not-allowed'"
                class="px-6 py-3 text-sm font-bold text-white bg-gradient-to-r rounded-xl transition-all flex items-center gap-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Gunakan
            </button>
        </div>
    </div>
</div>
