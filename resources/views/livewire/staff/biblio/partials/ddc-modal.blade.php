{{-- Enhanced DDC Lookup Modal - Teleported to body for highest z-index --}}
<template x-teleport="body">
    <div x-data="enhancedDdcModal()" x-show="open" x-cloak @open-ddc-modal.window="open = true" @keydown.escape.window="open = false" style="position: fixed; inset: 0; z-index: 99999;">
        {{-- Backdrop --}}
        <div x-show="open" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="position: fixed; inset: 0; background: rgba(0,0,0,0.7);" @click="open = false"></div>
        
        {{-- Modal Content - Enhanced Size --}}
        <div style="position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; padding: 1rem; pointer-events: none;">
            <div x-show="open" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="bg-white rounded-2xl shadow-2xl w-full max-w-6xl max-h-[90vh] overflow-hidden" style="pointer-events: auto;">
                
                {{-- Header --}}
                <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-indigo-600 via-purple-600 to-blue-700 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold flex items-center gap-3">
                                <i class="fas fa-layer-group text-2xl"></i> 
                                <div>
                                    <div>e-DDC Edition 23 Lookup</div>
                                    <div class="text-sm text-indigo-200 font-normal">Dewey Decimal Classification System</div>
                                </div>
                            </h3>
                        </div>
                        <button @click="open = false" class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-white/20 text-white transition-colors">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                </div>

                {{-- Search Section --}}
                <div class="p-6 border-b border-gray-100 bg-gray-50">
                    <div class="flex gap-4">
                        <div class="flex-1 relative">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" x-model="search" @input.debounce.300ms="doSearch()" 
                                   class="w-full pl-12 pr-4 py-4 text-base border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white" 
                                   placeholder="Cari nomor klasifikasi (contoh: 297) atau deskripsi (contoh: islam, teknologi)..." 
                                   autofocus>
                        </div>
                        <button @click="showFavorites = !showFavorites" 
                                :class="showFavorites ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 border border-gray-200'"
                                class="px-6 py-4 rounded-xl font-medium transition-colors flex items-center gap-2">
                            <i class="fas fa-star"></i>
                            <span class="hidden sm:inline">Favorit</span>
                        </button>
                    </div>
                    
                    {{-- Search Stats --}}
                    <div x-show="results.length > 0" class="mt-3 flex items-center justify-between text-sm text-gray-600">
                        <span><span x-text="results.length"></span> hasil ditemukan</span>
                        <span x-show="search.length >= 2">untuk "<span x-text="search" class="font-medium"></span>"</span>
                    </div>
                </div>

                {{-- Content Area --}}
                <div class="flex h-[500px]">
                    {{-- Left Panel: Main Classes / Favorites --}}
                    <div class="w-80 border-r border-gray-200 bg-gray-50 overflow-y-auto">
                        {{-- Main Classes --}}
                        <div x-show="!showFavorites" class="p-4">
                            <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
                                <i class="fas fa-th-large text-indigo-600"></i>
                                Kelas Utama DDC
                            </h4>
                            <div class="space-y-2">
                                <template x-for="cls in mainClasses" :key="cls.code">
                                    <button type="button" @click="selectClass(cls.code)" 
                                            :style="{ background: cls.bg }" 
                                            class="w-full p-4 rounded-xl text-left hover:shadow-md transition-all flex items-center gap-3 group">
                                        <span class="text-2xl" x-text="cls.icon"></span>
                                        <div class="flex-1">
                                            <div class="font-bold text-lg" :style="{ color: cls.color }" x-text="cls.code"></div>
                                            <div class="text-sm opacity-90" :style="{ color: cls.color }" x-text="cls.label"></div>
                                        </div>
                                        <i class="fas fa-chevron-right opacity-0 group-hover:opacity-100 transition-opacity" :style="{ color: cls.color }"></i>
                                    </button>
                                </template>
                            </div>
                        </div>

                        {{-- Favorites --}}
                        <div x-show="showFavorites" class="p-4">
                            <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
                                <i class="fas fa-star text-yellow-500"></i>
                                DDC Favorit
                            </h4>
                            <div x-show="favorites.length === 0" class="text-center py-8 text-gray-500">
                                <i class="fas fa-star text-3xl text-gray-300 mb-2"></i>
                                <p class="text-sm">Belum ada DDC favorit</p>
                                <p class="text-xs">Klik ⭐ untuk menambah favorit</p>
                            </div>
                            <div class="space-y-2">
                                <template x-for="fav in favorites" :key="fav.code">
                                    <button type="button" @click="select(fav.code, fav.description)" 
                                            class="w-full p-3 bg-white rounded-lg border border-gray-200 text-left hover:border-indigo-300 transition-colors">
                                        <div class="font-mono font-bold text-indigo-600" x-text="fav.code"></div>
                                        <div class="text-sm text-gray-600 truncate" x-text="fav.description.substring(0, 50)"></div>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Right Panel: Search Results --}}
                    <div class="flex-1 overflow-y-auto">
                        {{-- Results --}}
                        <div x-show="results.length > 0" class="p-4">
                            <div class="space-y-3">
                                <template x-for="ddc in results" :key="ddc.code">
                                    <div :class="selectedCode === ddc.code ? 'bg-indigo-50 border-indigo-300 shadow-md' : 'bg-white border-gray-200 hover:border-indigo-200'" 
                                         class="border rounded-xl p-4 transition-all cursor-pointer group"
                                         @click="select(ddc.code, ddc.description)">
                                        
                                        <div class="flex items-start gap-4">
                                            {{-- DDC Code --}}
                                            <div class="flex-shrink-0">
                                                <span :class="selectedCode === ddc.code ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-indigo-600 group-hover:bg-indigo-100'" 
                                                      class="px-3 py-2 rounded-lg font-mono font-bold text-lg transition-colors" 
                                                      x-text="ddc.code"></span>
                                            </div>
                                            
                                            {{-- Description --}}
                                            <div class="flex-1 min-w-0">
                                                <div class="text-gray-900 font-medium leading-relaxed" x-text="getMainDescription(ddc.description)"></div>
                                                <div x-show="getAdditionalInfo(ddc.description)" class="text-sm text-gray-600 mt-2 leading-relaxed" x-text="getAdditionalInfo(ddc.description)"></div>
                                                <div x-show="getCrossReferences(ddc.description)" class="text-xs text-blue-600 mt-2 italic" x-text="getCrossReferences(ddc.description)"></div>
                                            </div>
                                            
                                            {{-- Actions --}}
                                            <div class="flex items-center gap-2">
                                                <button @click.stop="toggleFavorite(ddc)" 
                                                        :class="isFavorite(ddc.code) ? 'text-yellow-500' : 'text-gray-300 hover:text-yellow-500'"
                                                        class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-yellow-50 transition-colors">
                                                    <i class="fas fa-star"></i>
                                                </button>
                                                <i x-show="selectedCode === ddc.code" class="fas fa-check-circle text-indigo-600 text-xl"></i>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Loading --}}
                        <div x-show="loading" class="flex items-center justify-center py-16">
                            <div class="text-center">
                                <i class="fas fa-spinner fa-spin text-3xl text-indigo-600 mb-3"></i>
                                <p class="text-gray-600">Mencari klasifikasi...</p>
                            </div>
                        </div>

                        {{-- No Results --}}
                        <div x-show="search.length >= 2 && results.length === 0 && !loading" class="text-center py-16">
                            <i class="fas fa-search text-5xl text-gray-300 mb-4"></i>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">Tidak ditemukan</h4>
                            <p class="text-gray-600 mb-4">Coba kata kunci lain atau gunakan kelas utama di sebelah kiri</p>
                            <button @click="search = ''; results = []" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                Reset Pencarian
                            </button>
                        </div>

                        {{-- Initial State --}}
                        <div x-show="search.length < 2 && results.length === 0 && !showFavorites" class="text-center py-16">
                            <i class="fas fa-layer-group text-5xl text-indigo-300 mb-4"></i>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">e-DDC Edition 23</h4>
                            <p class="text-gray-600 mb-4">Pilih kelas utama di sebelah kiri atau mulai mengetik untuk mencari</p>
                            <div class="text-sm text-gray-500">
                                <span class="font-medium">4,715</span> klasifikasi tersedia
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="p-6 border-t border-gray-100 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div x-show="selectedCode" class="flex items-center gap-3">
                                <span class="px-4 py-2 bg-indigo-600 text-white font-mono font-bold text-lg rounded-lg" x-text="selectedCode"></span>
                                <div class="max-w-md">
                                    <div class="font-medium text-gray-900" x-text="getMainDescription(selectedDesc)"></div>
                                    <div class="text-sm text-gray-600" x-text="selectedCode ? 'Siap digunakan' : ''"></div>
                                </div>
                            </div>
                            <div x-show="!selectedCode" class="text-gray-500 italic">Pilih klasifikasi DDC</div>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <button type="button" @click="open = false" 
                                    class="px-6 py-3 text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                                Batal
                            </button>
                            <button type="button" @click="apply()" :disabled="!selectedCode" 
                                    :class="selectedCode ? 'bg-indigo-600 hover:bg-indigo-700 text-white' : 'bg-gray-200 text-gray-400 cursor-not-allowed'" 
                                    class="px-8 py-3 font-semibold rounded-xl transition-colors flex items-center gap-2">
                                <i class="fas fa-check"></i> 
                                Gunakan Klasifikasi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
function enhancedDdcModal() {
    return {
        open: false,
        search: '',
        results: [],
        selectedCode: null,
        selectedDesc: '',
        loading: false,
        showFavorites: false,
        favorites: JSON.parse(localStorage.getItem('ddc_favorites') || '[]'),
        mainClasses: [
            {code: '000', label: 'Karya Umum', bg: 'linear-gradient(135deg, #fce7f3 0%, #f3e8ff 100%)', color: '#9d174d', icon: '💻'},
            {code: '100', label: 'Filsafat', bg: 'linear-gradient(135deg, #ffedd5 0%, #fef3c7 100%)', color: '#9a3412', icon: '🧠'},
            {code: '200', label: 'Agama', bg: 'linear-gradient(135deg, #d1fae5 0%, #ecfccb 100%)', color: '#065f46', icon: '🕌'},
            {code: '2X', label: 'Islam', bg: 'linear-gradient(135deg, #ccfbf1 0%, #a7f3d0 100%)', color: '#115e59', icon: '☪️'},
            {code: '300', label: 'Ilmu Sosial', bg: 'linear-gradient(135deg, #fef3c7 0%, #fde68a 100%)', color: '#92400e', icon: '👥'},
            {code: '400', label: 'Bahasa', bg: 'linear-gradient(135deg, #ecfccb 0%, #d9f99d 100%)', color: '#3f6212', icon: '🗣️'},
            {code: '500', label: 'Sains', bg: 'linear-gradient(135deg, #cffafe 0%, #a5f3fc 100%)', color: '#155e75', icon: '🔬'},
            {code: '600', label: 'Teknologi', bg: 'linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%)', color: '#1e40af', icon: '⚙️'},
            {code: '700', label: 'Seni', bg: 'linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%)', color: '#5b21b6', icon: '🎨'},
            {code: '800', label: 'Sastra', bg: 'linear-gradient(135deg, #fae8ff 0%, #f3e8ff 100%)', color: '#86198f', icon: '📚'},
            {code: '900', label: 'Sejarah', bg: 'linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%)', color: '#334155', icon: '🌍'},
        ],
        
        selectClass(code) {
            this.search = code;
            this.doSearch();
        },
        
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
                console.error('DDC Search Error:', e);
            }
            this.loading = false;
        },
        
        select(code, desc) {
            this.selectedCode = code;
            this.selectedDesc = desc;
        },
        
        apply() {
            if (!this.selectedCode) return;
            @this.set('classification', this.selectedCode);
            this.open = false;
            this.search = '';
            this.results = [];
            this.selectedCode = null;
            this.selectedDesc = '';
        },
        
        // Enhanced description parsing
        getMainDescription(description) {
            if (!description) return '';
            // Extract main description before additional info
            const parts = description.split(/\s{2,}/);
            return parts[0] || description.substring(0, 100);
        },
        
        getAdditionalInfo(description) {
            if (!description) return '';
            // Extract additional information (between main desc and cross-references)
            const parts = description.split(/\s{2,}/);
            const filtered = parts.slice(1).filter(part => 
                !part.toLowerCase().includes('lihat juga') && 
                !part.toLowerCase().includes('kelaskan di') &&
                part.length > 10
            );
            return filtered.join(' ').substring(0, 200);
        },
        
        getCrossReferences(description) {
            if (!description) return '';
            // Extract cross-references (Lihat juga...)
            const match = description.match(/Lihat juga[^.]*\./g);
            return match ? match.join(' ') : '';
        },
        
        // Favorites functionality
        toggleFavorite(ddc) {
            const index = this.favorites.findIndex(f => f.code === ddc.code);
            if (index >= 0) {
                this.favorites.splice(index, 1);
            } else {
                this.favorites.push({
                    code: ddc.code,
                    description: this.getMainDescription(ddc.description)
                });
                // Keep only last 20 favorites
                if (this.favorites.length > 20) {
                    this.favorites = this.favorites.slice(-20);
                }
            }
            localStorage.setItem('ddc_favorites', JSON.stringify(this.favorites));
        },
        
        isFavorite(code) {
            return this.favorites.some(f => f.code === code);
        },
        
        // Initialize
        init() {
            // Load favorites from localStorage
            this.favorites = JSON.parse(localStorage.getItem('ddc_favorites') || '[]');
        }
    }
}
</script>
