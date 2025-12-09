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
            <p style="text-align: center; font-size: 0.75rem; color: #9ca3af;">Klik kelas utama untuk melihat sub-klasifikasi, atau ketik di kotak pencarian</p>
        </div>
    </template>

    {{-- Search Box --}}
    <div style="margin-bottom: 1rem;">
        <div style="position: relative;">
            <div style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); pointer-events: none;">
                <template x-if="loading">
                    <svg style="width: 1.25rem; height: 1.25rem; color: #6366f1; animation: spin 1s linear infinite;" fill="none" viewBox="0 0 24 24">
                        <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </template>
                <template x-if="!loading">
                    <svg style="width: 1.25rem; height: 1.25rem; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </template>
            </div>
            <input 
                type="text" 
                x-model="search"
                @input.debounce.300ms="doSearch()"
                placeholder="Ketik nomor atau kata kunci (min. 2 karakter)..."
                style="width: 100%; padding: 0.875rem 2.5rem 0.875rem 2.5rem; border: 2px solid #e5e7eb; border-radius: 0.75rem; font-size: 0.9375rem; outline: none; transition: border-color 0.2s, box-shadow 0.2s;"
                class="dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                @focus="$el.style.borderColor = '#6366f1'; $el.style.boxShadow = '0 0 0 3px rgba(99, 102, 241, 0.1)'"
                @blur="$el.style.borderColor = '#e5e7eb'; $el.style.boxShadow = 'none'"
                autofocus
            >
            <button 
                x-show="search.length > 0"
                @click="search = ''; results = [];"
                style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #9ca3af; padding: 0.25rem;"
                @mouseenter="$el.style.color = '#6b7280'"
                @mouseleave="$el.style.color = '#9ca3af'"
            >
                <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Results --}}
    <template x-if="results.length > 0">
        <div>
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.5rem 0.75rem; background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border-radius: 0.5rem; margin-bottom: 0.75rem;">
                <span style="font-size: 0.8125rem; color: #0369a1;">
                    <strong x-text="results.length"></strong> hasil ditemukan
                </span>
                <button 
                    @click="search = ''; results = [];"
                    style="font-size: 0.75rem; color: #0284c7; background: none; border: none; cursor: pointer; text-decoration: underline;"
                >Kembali ke kelas utama</button>
            </div>
            <div style="max-height: 280px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 0.75rem;" class="dark:border-gray-600">
                <template x-for="ddc in results" :key="ddc.code">
                    <button 
                        type="button"
                        @click="select(ddc.code, ddc.description.substring(0, 60))"
                        :style="{ 
                            background: selectedCode === ddc.code ? 'linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%)' : 'transparent',
                            borderLeft: selectedCode === ddc.code ? '4px solid #6366f1' : '4px solid transparent'
                        }"
                        style="width: 100%; display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.75rem; text-align: left; border: none; border-bottom: 1px solid #f3f4f6; cursor: pointer; transition: background 0.15s;"
                        class="dark:border-gray-700"
                        @mouseenter="if (selectedCode !== ddc.code) $el.style.background = '#f9fafb'"
                        @mouseleave="if (selectedCode !== ddc.code) $el.style.background = 'transparent'"
                    >
                        <span 
                            :style="{ background: selectedCode === ddc.code ? 'linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%)' : 'linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%)', color: selectedCode === ddc.code ? 'white' : '#4338ca' }"
                            style="flex-shrink: 0; padding: 0.375rem 0.625rem; font-family: monospace; font-weight: 700; font-size: 0.875rem; border-radius: 0.5rem; transition: all 0.15s;"
                            x-text="ddc.code"
                        ></span>
                        <span style="flex: 1; font-size: 0.8125rem; line-height: 1.4;" class="text-gray-700 dark:text-gray-300" x-text="ddc.description.substring(0, 120) + (ddc.description.length > 120 ? '...' : '')"></span>
                        <template x-if="selectedCode === ddc.code">
                            <span style="flex-shrink: 0; width: 1.25rem; height: 1.25rem; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border-radius: 9999px; display: flex; align-items: center; justify-content: center;">
                                <svg style="width: 0.75rem; height: 0.75rem; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        <div style="padding: 2rem; text-align: center;">
            <div style="width: 3.5rem; height: 3.5rem; margin: 0 auto 0.75rem; background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); border-radius: 9999px; display: flex; align-items: center; justify-content: center;">
                <svg style="width: 1.75rem; height: 1.75rem; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p style="color: #6b7280; font-size: 0.875rem;">Tidak ada hasil untuk "<strong x-text="search"></strong>"</p>
            <p style="color: #9ca3af; font-size: 0.75rem; margin-top: 0.25rem;">Coba kata kunci lain</p>
        </div>
    </template>

    {{-- Footer with Selection --}}
    <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between; gap: 1rem;" class="dark:border-gray-600">
        <div style="flex: 1; min-width: 0;">
            <template x-if="selectedCode">
                <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.625rem 0.75rem; background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%); border-radius: 0.5rem; border: 1px solid #c7d2fe;">
                    <span style="padding: 0.25rem 0.5rem; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; font-family: monospace; font-weight: 700; font-size: 0.875rem; border-radius: 0.375rem;" x-text="selectedCode"></span>
                    <span style="font-size: 0.8125rem; color: #4338ca; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" x-text="selectedDesc"></span>
                </div>
            </template>
            <template x-if="!selectedCode">
                <span style="font-size: 0.8125rem; color: #9ca3af;">Pilih klasifikasi dari daftar</span>
            </template>
        </div>
        <button 
            type="button"
            @click="apply()"
            :disabled="!selectedCode"
            :style="{ 
                background: selectedCode ? 'linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%)' : '#d1d5db',
                cursor: selectedCode ? 'pointer' : 'not-allowed',
                boxShadow: selectedCode ? '0 4px 12px rgba(99, 102, 241, 0.3)' : 'none'
            }"
            style="padding: 0.625rem 1.25rem; color: white; font-weight: 600; font-size: 0.875rem; border: none; border-radius: 0.5rem; display: flex; align-items: center; gap: 0.5rem; transition: all 0.2s;"
            @mouseenter="if (selectedCode) { $el.style.transform = 'translateY(-1px)'; $el.style.boxShadow = '0 6px 16px rgba(99, 102, 241, 0.4)' }"
            @mouseleave="if (selectedCode) { $el.style.transform = 'translateY(0)'; $el.style.boxShadow = '0 4px 12px rgba(99, 102, 241, 0.3)' }"
        >
            <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Gunakan
        </button>
    </div>
</div>

<style>
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
