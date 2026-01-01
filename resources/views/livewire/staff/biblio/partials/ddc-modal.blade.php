{{-- DDC Lookup Modal - Teleported to body for highest z-index --}}
<template x-teleport="body">
    <div x-data="ddcModal()" x-show="open" x-cloak @open-ddc-modal.window="open = true" @keydown.escape.window="open = false" style="position: fixed; inset: 0; z-index: 99999;">
        {{-- Backdrop --}}
        <div x-show="open" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="position: fixed; inset: 0; background: rgba(0,0,0,0.7);" @click="open = false"></div>
        
        {{-- Modal Content --}}
        <div style="position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; padding: 1rem; pointer-events: none;">
            <div x-show="open" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[85vh] overflow-hidden" style="pointer-events: auto;">
                
                {{-- Header --}}
                <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-purple-50 to-white">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-book text-purple-500"></i> DDC Lookup
                    </h3>
                    <button @click="open = false" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- Search --}}
                <div class="p-4 border-b border-gray-100">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" x-model="search" @input.debounce.300ms="doSearch()" class="w-full pl-10 pr-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500" placeholder="Cari nomor atau deskripsi klasifikasi..." autofocus>
                    </div>
                </div>

                {{-- Content --}}
                <div class="p-4 overflow-y-auto" style="max-height: 400px;">
                    {{-- Main Classes --}}
                    <template x-if="search.length < 2 && results.length === 0">
                        <div class="grid grid-cols-2 gap-2">
                            <template x-for="cls in mainClasses" :key="cls.code">
                                <button type="button" @click="selectClass(cls.code)" :style="{ background: cls.bg }" class="p-3 rounded-xl text-left hover:shadow-md transition flex items-center gap-3">
                                    <span class="text-2xl" x-text="cls.icon"></span>
                                    <div>
                                        <div class="font-bold" :style="{ color: cls.color }" x-text="cls.code"></div>
                                        <div class="text-xs opacity-80" :style="{ color: cls.color }" x-text="cls.label"></div>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </template>

                    {{-- Results --}}
                    <template x-if="results.length > 0">
                        <div class="space-y-1">
                            <div class="text-xs text-gray-500 mb-2"><span x-text="results.length"></span> hasil ditemukan</div>
                            <template x-for="ddc in results" :key="ddc.code">
                                <button type="button" @click="select(ddc.code, ddc.description)" :class="selectedCode === ddc.code ? 'bg-purple-50 border-purple-300' : 'bg-white border-gray-200 hover:bg-gray-50'" class="w-full p-3 rounded-xl border text-left flex items-start gap-3 transition">
                                    <span :class="selectedCode === ddc.code ? 'bg-purple-600 text-white' : 'bg-gray-100 text-purple-600'" class="px-2 py-1 rounded-lg font-mono font-bold text-sm" x-text="ddc.code"></span>
                                    <span class="text-sm text-gray-700 flex-1" x-text="ddc.description.substring(0, 100)"></span>
                                    <i x-show="selectedCode === ddc.code" class="fas fa-check-circle text-purple-600"></i>
                                </button>
                            </template>
                        </div>
                    </template>

                    {{-- Loading --}}
                    <div x-show="loading" class="flex items-center justify-center py-8">
                        <i class="fas fa-spinner fa-spin text-2xl text-purple-600"></i>
                    </div>

                    {{-- No Results --}}
                    <template x-if="search.length >= 2 && results.length === 0 && !loading">
                        <div class="text-center py-8">
                            <i class="fas fa-search text-4xl text-gray-300 mb-2"></i>
                            <p class="text-sm text-gray-500">Tidak ditemukan</p>
                        </div>
                    </template>
                </div>

                {{-- Footer --}}
                <div class="p-4 border-t border-gray-100 flex items-center justify-between bg-gray-50">
                    <div x-show="selectedCode" class="flex items-center gap-2">
                        <span class="px-3 py-1 bg-purple-600 text-white font-mono font-bold text-sm rounded-lg" x-text="selectedCode"></span>
                        <span class="text-sm text-gray-600 truncate max-w-[200px]" x-text="selectedDesc"></span>
                    </div>
                    <div x-show="!selectedCode" class="text-sm text-gray-400 italic">Pilih klasifikasi</div>
                    <button type="button" @click="apply()" :disabled="!selectedCode" :class="selectedCode ? 'bg-purple-600 hover:bg-purple-700 text-white' : 'bg-gray-200 text-gray-400 cursor-not-allowed'" class="px-5 py-2.5 text-sm font-semibold rounded-xl transition">
                        <i class="fas fa-check mr-1"></i> Gunakan
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
function ddcModal() {
    return {
        open: false,
        search: '',
        results: [],
        selectedCode: null,
        selectedDesc: '',
        loading: false,
        mainClasses: [
            {code: '000', label: 'Karya Umum', bg: '#fce7f3', color: '#9d174d', icon: '💻'},
            {code: '100', label: 'Filsafat', bg: '#ffedd5', color: '#9a3412', icon: '🧠'},
            {code: '200', label: 'Agama', bg: '#d1fae5', color: '#065f46', icon: '🕌'},
            {code: '2X', label: 'Islam', bg: '#ccfbf1', color: '#115e59', icon: '☪️'},
            {code: '300', label: 'Ilmu Sosial', bg: '#fef3c7', color: '#92400e', icon: '👥'},
            {code: '400', label: 'Bahasa', bg: '#ecfccb', color: '#3f6212', icon: '🗣️'},
            {code: '500', label: 'Sains', bg: '#cffafe', color: '#155e75', icon: '🔬'},
            {code: '600', label: 'Teknologi', bg: '#dbeafe', color: '#1e40af', icon: '⚙️'},
            {code: '700', label: 'Seni', bg: '#ede9fe', color: '#5b21b6', icon: '🎨'},
            {code: '800', label: 'Sastra', bg: '#fae8ff', color: '#86198f', icon: '📚'},
            {code: '900', label: 'Sejarah', bg: '#f1f5f9', color: '#334155', icon: '🌍'},
        ],
        selectClass(code) {
            this.search = code;
            this.doSearch();
        },
        async doSearch() {
            if (this.search.length < 2) { this.results = []; return; }
            this.loading = true;
            try {
                const res = await fetch('/api/ddc/search?q=' + encodeURIComponent(this.search) + '&limit=30');
                this.results = await res.json();
            } catch (e) { this.results = []; }
            this.loading = false;
        },
        select(code, desc) {
            this.selectedCode = code;
            this.selectedDesc = desc.substring(0, 80);
        },
        apply() {
            if (!this.selectedCode) return;
            @this.set('classification', this.selectedCode);
            this.open = false;
            this.search = '';
            this.results = [];
            this.selectedCode = null;
        }
    }
}
</script>
