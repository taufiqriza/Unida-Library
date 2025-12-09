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
            const res = await fetch('/api/ddc/search?q=' + encodeURIComponent(this.search) + '&limit=50');
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
    {{-- Search Box --}}
    <div style="margin-bottom: 1rem;">
        <div style="position: relative;">
            <div style="position: absolute; left: 0.875rem; top: 50%; transform: translateY(-50%); pointer-events: none;">
                <template x-if="loading">
                    <svg class="ddc-search-icon ddc-spin" fill="none" viewBox="0 0 24 24">
                        <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </template>
                <template x-if="!loading">
                    <svg class="ddc-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </template>
            </div>
            <input 
                type="text" 
                x-model="search"
                @input.debounce.300ms="doSearch()"
                placeholder="Cari nomor klasifikasi atau deskripsi..."
                class="ddc-search-input"
                autofocus
            >
            <button 
                x-show="search.length > 0"
                @click="search = ''; results = [];"
                type="button"
                class="ddc-search-clear"
            >
                <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Main Classes Grid - Original Style --}}
    <template x-if="search.length < 2 && results.length === 0">
        <div>
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem; margin-bottom: 0.5rem;">
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
            {{-- 2X Islam - Centered --}}
            <div style="display: flex; justify-content: center; margin-bottom: 0.75rem;">
                <button 
                    type="button"
                    @click="selectClass('2X')"
                    style="display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem 2rem; border-radius: 0.75rem; border: none; cursor: pointer; text-align: left; transition: transform 0.15s, box-shadow 0.15s; background: linear-gradient(135deg, #ccfbf1 0%, #5eead4 100%);"
                    @mouseenter="$el.style.transform = 'scale(1.02)'; $el.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)'"
                    @mouseleave="$el.style.transform = 'scale(1)'; $el.style.boxShadow = 'none'"
                >
                    <span style="font-size: 1.5rem;">☪️</span>
                    <div>
                        <div style="font-size: 1.25rem; font-weight: 800; line-height: 1; color: #115e59;">2X</div>
                        <div style="font-size: 0.7rem; opacity: 0.8; color: #115e59;">Islam</div>
                    </div>
                    <svg style="width: 1rem; height: 1rem; opacity: 0.5; color: #115e59;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
            <p style="text-align: center; font-size: 0.75rem; color: #9ca3af;">Klik kelas utama atau ketik di kotak pencarian</p>
        </div>
    </template>

    {{-- Results List - Fixed Height with Scroll --}}
    <template x-if="results.length > 0">
        <div>
            {{-- Results Header --}}
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.625rem 0.875rem; margin-bottom: 0.5rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 0.625rem; color: white;">
                <span style="font-size: 0.875rem; font-weight: 600;">
                    <span x-text="results.length"></span> hasil ditemukan
                </span>
                <button 
                    @click="search = ''; results = [];"
                    type="button"
                    style="font-size: 0.75rem; color: rgba(255,255,255,0.9); background: rgba(255,255,255,0.2); border: none; padding: 0.25rem 0.625rem; border-radius: 0.375rem; cursor: pointer;"
                    onmouseover="this.style.background='rgba(255,255,255,0.3)'"
                    onmouseout="this.style.background='rgba(255,255,255,0.2)'"
                >← Kembali</button>
            </div>
            
            {{-- Scrollable Results --}}
            <div style="max-height: 280px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 0.75rem; background: #fafafa;" class="ddc-results-scroll">
                <template x-for="ddc in results" :key="ddc.code">
                    <button 
                        type="button"
                        @click="select(ddc.code, ddc.description.substring(0, 80))"
                        :class="selectedCode === ddc.code ? 'ddc-item-selected' : 'ddc-item'"
                        class="ddc-result-item"
                    >
                        <span :class="selectedCode === ddc.code ? 'ddc-code-selected' : 'ddc-code'" x-text="ddc.code"></span>
                        <span class="ddc-desc" x-text="ddc.description.substring(0, 100) + (ddc.description.length > 100 ? '...' : '')"></span>
                        <svg x-show="selectedCode === ddc.code" class="ddc-check" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </template>
            </div>
        </div>
    </template>

    {{-- No Results --}}
    <template x-if="search.length >= 2 && results.length === 0 && !loading">
        <div style="padding: 2.5rem 1rem; text-align: center;">
            <div style="width: 3.5rem; height: 3.5rem; margin: 0 auto 0.75rem; background: #f3f4f6; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <svg style="width: 1.75rem; height: 1.75rem; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p style="font-size: 0.875rem; color: #6b7280; font-weight: 500;">Tidak ada hasil untuk "<span x-text="search"></span>"</p>
            <button 
                @click="search = ''; results = [];"
                type="button"
                style="margin-top: 0.75rem; font-size: 0.8125rem; color: #667eea; background: none; border: none; cursor: pointer;"
                onmouseover="this.style.textDecoration='underline'"
                onmouseout="this.style.textDecoration='none'"
            >Kembali ke kelas utama</button>
        </div>
    </template>

    {{-- Footer --}}
    <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb; display: flex; align-items: center; gap: 0.75rem;">
        <div style="flex: 1; min-width: 0;">
            <template x-if="selectedCode">
                <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.625rem 0.875rem; background: linear-gradient(135deg, rgba(102,126,234,0.1) 0%, rgba(118,75,162,0.1) 100%); border: 1px solid rgba(102,126,234,0.3); border-radius: 0.625rem;">
                    <span style="padding: 0.25rem 0.5rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-family: monospace; font-weight: 700; font-size: 0.875rem; border-radius: 0.375rem; box-shadow: 0 2px 6px rgba(102,126,234,0.3);" x-text="selectedCode"></span>
                    <span style="font-size: 0.875rem; color: #4b5563; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" x-text="selectedDesc"></span>
                </div>
            </template>
            <template x-if="!selectedCode">
                <span style="font-size: 0.875rem; color: #9ca3af; font-style: italic;">Pilih klasifikasi dari daftar</span>
            </template>
        </div>
        <button 
            type="button"
            @click="apply()"
            x-bind:disabled="!selectedCode"
            x-bind:class="selectedCode ? 'ddc-btn-active' : 'ddc-btn-disabled'"
            class="ddc-btn-gunakan"
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
            <span>Gunakan</span>
        </button>
    </div>

    <style>
        @keyframes spin { to { transform: rotate(360deg); } }
        .ddc-spin { animation: spin 1s linear infinite; }
        
        /* Search Input */
        .ddc-search-input {
            width: 100%;
            height: 3rem;
            padding-left: 2.75rem;
            padding-right: 2.75rem;
            font-size: 0.9375rem;
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
            background: white;
            color: #111827;
            outline: none;
            transition: all 0.2s;
        }
        .ddc-search-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
        }
        .ddc-search-input::placeholder { color: #9ca3af; }
        .ddc-search-icon { width: 1.25rem; height: 1.25rem; color: #9ca3af; }
        .ddc-search-clear {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            padding: 0.25rem;
            color: #9ca3af;
            background: none;
            border: none;
            cursor: pointer;
        }
        .ddc-search-clear:hover { color: #6b7280; }
        
        .dark .ddc-search-input {
            background: #374151;
            border-color: #4b5563;
            color: #f3f4f6;
        }
        .dark .ddc-search-input:focus {
            border-color: #818cf8;
            box-shadow: 0 0 0 3px rgba(129,140,248,0.15);
        }
        .dark .ddc-search-input::placeholder { color: #9ca3af; }
        .dark .ddc-search-icon { color: #9ca3af; }
        .dark .ddc-search-clear { color: #9ca3af; }
        .dark .ddc-search-clear:hover { color: #d1d5db; }
        
        /* Results scroll */
        .ddc-results-scroll::-webkit-scrollbar { width: 6px; }
        .ddc-results-scroll::-webkit-scrollbar-track { background: #f3f4f6; border-radius: 3px; }
        .ddc-results-scroll::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 3px; }
        .ddc-results-scroll::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
        
        .ddc-result-item {
            width: 100%;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 0.875rem 1rem;
            text-align: left;
            border: none;
            border-bottom: 1px solid #f3f4f6;
            cursor: pointer;
            transition: all 0.15s;
        }
        .ddc-item { background: white; border-left: 4px solid transparent; }
        .ddc-item:hover { background: #f9fafb; }
        .ddc-item-selected { 
            background: linear-gradient(135deg, rgba(102,126,234,0.08) 0%, rgba(118,75,162,0.08) 100%); 
            border-left: 4px solid #667eea; 
        }
        
        .ddc-code {
            flex-shrink: 0;
            min-width: 70px;
            padding: 0.375rem 0.75rem;
            background: #f3f4f6;
            color: #667eea;
            font-family: ui-monospace, monospace;
            font-weight: 700;
            font-size: 0.8125rem;
            border-radius: 0.5rem;
            text-align: center;
        }
        .ddc-code-selected {
            flex-shrink: 0;
            min-width: 70px;
            padding: 0.375rem 0.75rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-family: ui-monospace, monospace;
            font-weight: 700;
            font-size: 0.8125rem;
            border-radius: 0.5rem;
            text-align: center;
            box-shadow: 0 2px 8px rgba(102,126,234,0.3);
        }
        
        .ddc-desc {
            flex: 1;
            font-size: 0.875rem;
            line-height: 1.5;
            color: #374151;
            padding-top: 0.125rem;
        }
        
        .ddc-check {
            flex-shrink: 0;
            width: 1.25rem;
            height: 1.25rem;
            color: #667eea;
            margin-top: 0.125rem;
        }
        
        .ddc-btn-gunakan {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.625rem 1.5rem;
            font-weight: 600;
            font-size: 0.875rem;
            border: none;
            border-radius: 0.625rem;
            transition: all 0.2s ease;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .ddc-btn-gunakan svg {
            width: 1rem;
            height: 1rem;
            flex-shrink: 0;
        }
        .ddc-btn-active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 14px rgba(102,126,234,0.4);
            cursor: pointer;
        }
        .ddc-btn-active:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102,126,234,0.5);
        }
        .ddc-btn-disabled {
            background: #e5e7eb;
            color: #9ca3af;
            cursor: not-allowed;
        }
        
        /* Dark mode */
        .dark .ddc-results-scroll { background: #1f2937 !important; border-color: #374151 !important; }
        .dark .ddc-result-item { border-color: #374151 !important; }
        .dark .ddc-item { background: #1f2937 !important; }
        .dark .ddc-item:hover { background: #374151 !important; }
        .dark .ddc-item-selected { background: rgba(102,126,234,0.15) !important; }
        .dark .ddc-code { background: #374151 !important; color: #a5b4fc !important; }
        .dark .ddc-desc { color: #d1d5db !important; }
        .dark .ddc-btn-disabled { background: #374151; color: #6b7280; }
    </style>
</div>
