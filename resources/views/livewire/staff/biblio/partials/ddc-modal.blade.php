{{-- Enhanced DDC Lookup Modal - Teleported to body for highest z-index --}}
<template x-teleport="body">
    <div x-data="enhancedDdcModal()" x-show="open" x-cloak @open-ddc-modal.window="open = true" @keydown.escape.window="open = false" style="position: fixed; inset: 0; z-index: 99999;">
        {{-- Backdrop --}}
        <div x-show="open" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="position: fixed; inset: 0; background: rgba(0,0,0,0.7);" @click="open = false"></div>
        
        {{-- Modal Content - Enhanced Professional Layout --}}
        <div style="position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; padding: 1rem; pointer-events: none;">
            <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="bg-white rounded-3xl shadow-2xl w-full max-w-7xl max-h-[90vh] overflow-hidden border border-gray-200 flex flex-col" style="pointer-events: auto;">
                
                {{-- Header - Full Blue --}}
                <div class="p-6 bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                                <i class="fas fa-layer-group text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold">DDC - Dewey Decimal Classification</h3>
                                <div class="text-blue-100 font-medium">✅ GUNAKAN DDC INI - VALID DAN SAH</div>
                                <div class="text-blue-200 text-sm">4,715 Klasifikasi Tersedia</div>
                            </div>
                        </div>
                        <button @click="open = false" class="w-12 h-12 flex items-center justify-center rounded-xl hover:bg-white/20 text-white transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                {{-- Search Section - Enhanced --}}
                <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-blue-100">
                    <div class="flex gap-4">
                        <div class="flex-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-search text-blue-400 text-lg"></i>
                            </div>
                            <input type="text" x-model="search" @input.debounce.300ms="doSearch()" 
                                   class="w-full pl-12 pr-4 py-4 text-base border-2 border-blue-200 rounded-2xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-sm transition-all" 
                                   placeholder="Cari nomor klasifikasi (contoh: 297) atau deskripsi (contoh: islam, teknologi)..." 
                                   autofocus>
                        </div>
                        <button @click="showFavorites = !showFavorites" 
                                :class="showFavorites ? 'bg-blue-600 text-white shadow-lg' : 'bg-white text-blue-600 border-2 border-blue-200 hover:border-blue-300'"
                                class="px-6 py-4 rounded-2xl font-semibold transition-all flex items-center gap-2">
                            <i class="fas fa-star"></i>
                            <span class="hidden sm:inline">Favorit</span>
                        </button>
                    </div>
                    
                    {{-- Search Stats --}}
                    <div x-show="results.length > 0" class="mt-4 flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2 text-blue-700">
                            <i class="fas fa-info-circle"></i>
                            <span class="font-medium"><span x-text="results.length"></span> hasil ditemukan</span>
                        </div>
                        <span x-show="search.length >= 2" class="text-blue-600">untuk "<span x-text="search" class="font-semibold"></span>"</span>
                    </div>
                </div>

                {{-- Content Area --}}
                <div class="flex flex-1 min-h-0">
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
                                                <div class="text-gray-900 font-medium leading-relaxed" x-html="highlightSearch(getMainDescription(ddc.description), search)"></div>
                                                <div x-show="getAdditionalInfo(ddc.description)" class="text-sm text-gray-600 mt-2 leading-relaxed" x-html="highlightSearch(getAdditionalInfo(ddc.description), search)"></div>
                                                <div x-show="getCrossReferences(ddc.description)" class="text-xs text-blue-600 mt-2 italic cursor-pointer hover:text-blue-800 transition-colors" 
                                                     x-text="getCrossReferences(ddc.description)"
                                                     @click.stop="navigateToReference(ddc.description)"
                                                     title="Klik untuk melihat referensi terkait"></div>
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

                {{-- Enhanced Footer with Fixed Selection Preview --}}
                <div class="bg-gradient-to-r from-gray-50 to-blue-50 border-t border-gray-200">
                    {{-- Compact Selection Preview --}}
                    <div x-show="selectedCode" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                    <span class="font-mono font-bold text-sm" x-text="selectedCode"></span>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="fas fa-check-circle text-green-300 text-sm"></i>
                                    <span class="font-semibold text-sm">Terpilih</span>
                                </div>
                                <div class="text-blue-100 text-sm leading-tight line-clamp-2" x-text="getMainDescription(selectedDesc)"></div>
                            </div>
                            <div class="flex-shrink-0">
                                <button @click="selectedCode = null; selectedDesc = ''" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/20 text-white/80 hover:text-white transition-colors">
                                    <i class="fas fa-times text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Action Buttons --}}
                    <div class="px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div x-show="!selectedCode" class="flex items-center gap-2 text-gray-500">
                                    <i class="fas fa-info-circle"></i>
                                    <span class="text-sm">Pilih klasifikasi DDC untuk melanjutkan</span>
                                </div>
                                <div x-show="selectedCode" class="flex items-center gap-2 text-green-600">
                                    <i class="fas fa-check-circle"></i>
                                    <span class="text-sm font-medium">Siap digunakan</span>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-3">
                                <button type="button" @click="open = false" 
                                        class="px-6 py-3 text-gray-700 bg-white border-2 border-gray-200 rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all font-medium">
                                    <i class="fas fa-times mr-2"></i>
                                    Batal
                                </button>
                                <button type="button" @click="apply()" :disabled="!selectedCode" 
                                        :class="selectedCode ? 'bg-blue-600 hover:bg-blue-700 text-white shadow-lg hover:shadow-xl' : 'bg-gray-200 text-gray-400 cursor-not-allowed'" 
                                        class="px-8 py-3 font-bold rounded-xl transition-all flex items-center gap-2 border-2 border-transparent">
                                    <i class="fas fa-check text-lg"></i> 
                                    <span>Gunakan Klasifikasi</span>
                                </button>
                            </div>
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
            console.log('selectClass called with:', code);
            // Implement proper DDC hierarchy selection
            if (code === '2X') {
                this.search = '2X';  // Special case for Islamic classifications
            } else if (code.length === 3 && code.endsWith('00')) {
                // For main class sections (000, 100, 200, etc.)
                // Search by the main class digit to get ALL subclasses
                this.search = code.charAt(0);  // "000" -> "0", "100" -> "1", "200" -> "2"
            } else {
                this.search = code;
            }
            console.log('Search query set to:', this.search);
            this.doSearch();
        },
        
        async doSearch() {
            // Allow single character search for main classes
            if (this.search.length < 1) { 
                this.results = []; 
                return; 
            }
            
            this.loading = true;
            try {
                // Increase limit for comprehensive results
                const limit = this.search.length === 1 ? 250 : 100; // More results for main classes
                const res = await fetch('/api/ddc/search?q=' + encodeURIComponent(this.search) + '&limit=' + limit);
                const data = await res.json();
                this.results = data || [];
                console.log('DDC Search Results:', this.results.length, 'for query:', this.search);
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
        
        // Navigate to cross-reference
        navigateToReference(description) {
            const crossRefs = this.getCrossReferences(description);
            if (!crossRefs) return;
            
            // Extract DDC codes from cross-references
            const codeMatches = crossRefs.match(/\b\d{3}(?:\.\d+)?\b/g);
            if (codeMatches && codeMatches.length > 0) {
                // Use the first found code
                this.search = codeMatches[0];
                this.doSearch();
            }
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
        
        // Highlight search terms
        highlightSearch(text, searchTerm) {
            if (!text || !searchTerm || searchTerm.length < 2) return text;
            
            // Split search terms and filter out short terms
            const terms = searchTerm.toLowerCase().split(/\s+/).filter(t => t.length >= 2);
            let highlighted = text;
            
            // Sort terms by length (longest first) to avoid partial replacements
            terms.sort((a, b) => b.length - a.length);
            
            terms.forEach(term => {
                const escapedTerm = term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                // Use word boundary for better matching
                const regex = new RegExp(`\\b(${escapedTerm})`, 'gi');
                highlighted = highlighted.replace(regex, '<mark class="bg-yellow-200 px-1 rounded font-semibold">$1</mark>');
            });
            
            return highlighted;
        },
        
        // Initialize
        init() {
            // Load favorites from localStorage
            this.favorites = JSON.parse(localStorage.getItem('ddc_favorites') || '[]');
        }
    }
}
</script>
