<div 
    x-data="{
        search: '',
        results: [],
        selectedCode: null,
        selectedDesc: '',
        loading: false,
        mainClasses: [
            { code: '000', label: 'Karya Umum, Komputer', color: 'bg-red-500' },
            { code: '100', label: 'Filsafat & Psikologi', color: 'bg-orange-500' },
            { code: '200', label: 'Agama', color: 'bg-amber-500' },
            { code: '300', label: 'Ilmu Sosial', color: 'bg-yellow-500' },
            { code: '400', label: 'Bahasa', color: 'bg-lime-500' },
            { code: '500', label: 'Sains & Matematika', color: 'bg-green-500' },
            { code: '600', label: 'Teknologi', color: 'bg-teal-500' },
            { code: '700', label: 'Seni & Olahraga', color: 'bg-cyan-500' },
            { code: '800', label: 'Sastra', color: 'bg-blue-500' },
            { code: '900', label: 'Sejarah & Geografi', color: 'bg-purple-500' },
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
                // Find the classification input by looking for the specific field
                const allInputs = document.querySelectorAll('input');
                let targetInput = null;
                
                allInputs.forEach(inp => {
                    // Check various ways to identify the classification input
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
                    // Set the value
                    targetInput.value = this.selectedCode;
                    
                    // Trigger events for Livewire/Alpine to pick up the change
                    targetInput.dispatchEvent(new Event('input', { bubbles: true }));
                    targetInput.dispatchEvent(new Event('change', { bubbles: true }));
                    
                    // Also try to update via Alpine if available
                    if (targetInput._x_model) {
                        targetInput._x_model.set(this.selectedCode);
                    }
                }
                
                // Close the modal using Filament's close mechanism
                this.$dispatch('close-modal', { id: 'searchDdc' });
                
                // Fallback: click the close button
                setTimeout(() => {
                    const closeBtn = document.querySelector('.fi-modal-close-btn');
                    if (closeBtn) closeBtn.click();
                }, 100);
            }
        }
    }"
    x-init="$nextTick(() => $refs.searchInput?.focus())"
    class="space-y-4"
>
    {{-- Search Box --}}
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <template x-if="loading">
                <svg class="animate-spin h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24">
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
            placeholder="Ketik nomor klasifikasi atau kata kunci (min. 2 karakter)..."
            class="w-full pl-12 pr-10 py-3.5 text-base border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition"
        >
        <button 
            x-show="search.length > 0"
            @click="search = ''; results = [];"
            class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Content Area --}}
    <div class="max-h-[45vh] overflow-y-auto rounded-xl border border-gray-200 dark:border-gray-700">
        {{-- Main Classes (when no search) --}}
        <template x-if="search.length < 2 && results.length === 0">
            <div class="p-4">
                <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    Kelas Utama DDC - Klik untuk melihat sub-kelas
                </h4>
                <div class="grid grid-cols-2 gap-2">
                    <template x-for="cls in mainClasses" :key="cls.code">
                        <button 
                            type="button"
                            @click="selectClass(cls.code)"
                            class="flex items-center gap-3 p-3 text-left bg-gray-50 dark:bg-gray-700/50 rounded-xl border-2 border-transparent hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition group"
                        >
                            <span 
                                :class="cls.color"
                                class="w-12 h-12 flex items-center justify-center text-white font-bold rounded-xl text-sm shadow-lg group-hover:scale-110 transition"
                                x-text="cls.code"
                            ></span>
                            <span class="flex-1 text-sm font-medium text-gray-700 dark:text-gray-300" x-text="cls.label"></span>
                            <svg class="w-5 h-5 text-gray-300 group-hover:text-blue-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </template>
                </div>
            </div>
        </template>

        {{-- Search Results --}}
        <template x-if="results.length > 0">
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                <template x-for="ddc in results" :key="ddc.id">
                    <button 
                        type="button"
                        @click="selectResult(ddc.code, ddc.description)"
                        :class="selectedCode === ddc.code ? 'bg-blue-50 dark:bg-blue-900/30 ring-2 ring-blue-500 ring-inset' : ''"
                        class="w-full flex items-start gap-4 p-4 text-left hover:bg-blue-50 dark:hover:bg-blue-900/20 transition group"
                    >
                        <span class="flex-shrink-0 w-16 h-10 flex items-center justify-center bg-gradient-to-br from-blue-500 to-indigo-600 text-white font-mono font-bold rounded-lg text-sm shadow-md group-hover:shadow-lg transition" x-text="ddc.code"></span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed" x-text="ddc.description.substring(0, 150) + (ddc.description.length > 150 ? '...' : '')"></p>
                        </div>
                        <template x-if="selectedCode === ddc.code">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </span>
                        </template>
                    </button>
                </template>
            </div>
        </template>

        {{-- No Results --}}
        <template x-if="search.length >= 2 && results.length === 0 && !loading">
            <div class="p-8 text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-gray-500 dark:text-gray-400 font-medium">Tidak ada hasil untuk "<span x-text="search"></span>"</p>
                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Coba kata kunci lain atau nomor klasifikasi</p>
            </div>
        </template>
    </div>

    {{-- Selected Preview & Actions --}}
    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
        <div class="flex-1 min-w-0">
            <template x-if="selectedCode">
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1.5 bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 font-mono font-bold rounded-lg" x-text="selectedCode"></span>
                    <span class="text-sm text-gray-600 dark:text-gray-400 truncate" x-text="selectedDesc.substring(0, 60) + '...'"></span>
                </div>
            </template>
            <template x-if="!selectedCode">
                <span class="text-sm text-gray-500 dark:text-gray-400">Pilih klasifikasi dari daftar di atas</span>
            </template>
        </div>
        <button 
            type="button"
            @click="applySelection()"
            :disabled="!selectedCode"
            class="ml-4 px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 shadow-lg shadow-blue-500/25"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Gunakan Klasifikasi
        </button>
    </div>

    {{-- Tips --}}
    <div class="text-xs text-gray-500 dark:text-gray-400 bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 flex items-start gap-2">
        <svg class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span><strong>Tips:</strong> Ketik nomor (004, 297) atau kata kunci (komputer, islam, ekonomi). Klik kelas utama untuk melihat sub-klasifikasi.</span>
    </div>
</div>
