{{-- Smart DDC Modal with AI Recommendations --}}
<template x-teleport="body">
    <div x-data="smartDdcModal()" x-show="open" x-cloak @open-ddc-modal.window="openModal()" @keydown.escape.window="open = false" style="position: fixed; inset: 0; z-index: 99999;">
        {{-- Backdrop --}}
        <div x-show="open" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="position: fixed; inset: 0; background: rgba(0,0,0,0.7);" @click="open = false"></div>
        
        {{-- Modal Content --}}
        <div style="position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; padding: 1rem; pointer-events: none;">
            <div x-show="open" x-transition class="bg-white rounded-3xl shadow-2xl w-full max-w-7xl max-h-[90vh] overflow-hidden border border-gray-200 flex flex-col" style="pointer-events: auto;">
                
                {{-- Header --}}
                <div class="p-5 bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center">
                                <i class="fas fa-brain text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold">Smart DDC Classification</h3>
                                <div class="text-blue-200 text-sm">AI-Powered • 4,715 Klasifikasi</div>
                            </div>
                        </div>
                        <button @click="open = false" class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-white/20 text-white transition">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                </div>

                {{-- AI Recommendation Panel (shows when title exists) --}}
                <div x-show="aiRecommendations.length > 0 || aiLoading" class="border-b border-gray-200">
                    <div class="p-4 bg-gradient-to-r from-purple-50 to-indigo-50">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-8 h-8 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-magic text-white text-sm"></i>
                            </span>
                            <h4 class="font-semibold text-gray-900">Rekomendasi AI berdasarkan Judul</h4>
                            <span x-show="bookTitle" class="text-xs text-gray-500 truncate max-w-md">"<span x-text="bookTitle.substring(0, 60)"></span>..."</span>
                        </div>
                        
                        {{-- Loading --}}
                        <div x-show="aiLoading" class="flex items-center gap-3 py-4">
                            <i class="fas fa-spinner fa-spin text-purple-600"></i>
                            <span class="text-gray-600 text-sm">Menganalisis judul...</span>
                        </div>
                        
                        {{-- Summary --}}
                        <div x-show="aiSummary && !aiLoading" class="mb-3 p-3 rounded-xl" :class="{
                            'bg-green-100 border border-green-200': aiSummary?.status === 'confident',
                            'bg-blue-100 border border-blue-200': aiSummary?.status === 'suggested',
                            'bg-amber-100 border border-amber-200': aiSummary?.status === 'review',
                            'bg-gray-100 border border-gray-200': aiSummary?.status === 'no_match'
                        }">
                            <div class="flex items-start gap-2">
                                <i class="fas mt-0.5" :class="{
                                    'fa-check-circle text-green-600': aiSummary?.status === 'confident',
                                    'fa-lightbulb text-blue-600': aiSummary?.status === 'suggested',
                                    'fa-exclamation-triangle text-amber-600': aiSummary?.status === 'review',
                                    'fa-info-circle text-gray-600': aiSummary?.status === 'no_match'
                                }"></i>
                                <div>
                                    <p class="text-sm font-medium" :class="{
                                        'text-green-800': aiSummary?.status === 'confident',
                                        'text-blue-800': aiSummary?.status === 'suggested',
                                        'text-amber-800': aiSummary?.status === 'review',
                                        'text-gray-800': aiSummary?.status === 'no_match'
                                    }" x-text="aiSummary?.message"></p>
                                    <p x-show="aiKeywords.length > 0" class="text-xs mt-1 text-gray-600">
                                        Kata kunci: <span x-text="aiKeywords.join(', ')"></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Recommendations --}}
                        <div x-show="aiRecommendations.length > 0 && !aiLoading" class="flex flex-wrap gap-2">
                            <template x-for="rec in aiRecommendations" :key="rec.code">
                                <button @click="select(rec.code, rec.description)" type="button"
                                    class="group flex items-center gap-2 px-3 py-2 rounded-xl border-2 transition-all"
                                    :class="selectedCode === rec.code 
                                        ? 'bg-purple-600 border-purple-600 text-white' 
                                        : 'bg-white border-gray-200 hover:border-purple-300 hover:bg-purple-50'">
                                    <span class="font-mono font-bold" :class="selectedCode === rec.code ? 'text-white' : 'text-purple-600'" x-text="rec.code"></span>
                                    <span class="text-sm truncate max-w-[200px]" :class="selectedCode === rec.code ? 'text-purple-100' : 'text-gray-600'" x-text="rec.description"></span>
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-medium"
                                        :class="{
                                            'bg-green-500 text-white': rec.confidence === 'high',
                                            'bg-blue-500 text-white': rec.confidence === 'medium',
                                            'bg-gray-400 text-white': rec.confidence === 'low'
                                        }"
                                        x-text="rec.confidence === 'high' ? '✓ Tinggi' : rec.confidence === 'medium' ? '~ Sedang' : '? Rendah'">
                                    </span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Search Section --}}
                <div class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-blue-100">
                    <div class="flex gap-3">
                        <div class="flex-1 relative">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-blue-400"></i>
                            <input type="text" x-model="search" @input.debounce.300ms="doSearch()" 
                                   class="w-full pl-11 pr-4 py-3 text-sm border-2 border-blue-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white" 
                                   placeholder="Cari kode DDC atau deskripsi...">
                        </div>
                        <button @click="showFavorites = !showFavorites" 
                                :class="showFavorites ? 'bg-blue-600 text-white' : 'bg-white text-blue-600 border-2 border-blue-200'"
                                class="px-4 py-3 rounded-xl font-medium transition flex items-center gap-2">
                            <i class="fas fa-star"></i>
                            <span class="hidden sm:inline">Favorit</span>
                        </button>
                    </div>
                    <div x-show="results.length > 0" class="mt-2 text-xs text-blue-600">
                        <span x-text="results.length"></span> hasil ditemukan
                    </div>
                </div>

                {{-- Content Area --}}
                <div class="flex flex-1 min-h-0">
                    {{-- Left Panel: Main Classes --}}
                    <div class="w-72 border-r border-gray-200 bg-gray-50 overflow-y-auto">
                        <div x-show="!showFavorites" class="p-3">
                            <h4 class="font-semibold text-gray-900 mb-2 text-sm flex items-center gap-2">
                                <i class="fas fa-th-large text-indigo-600"></i> Kelas Utama
                            </h4>
                            <div class="space-y-1.5">
                                <template x-for="cls in mainClasses" :key="cls.code">
                                    <button type="button" @click="selectClass(cls.code)" 
                                            :style="{ background: cls.bg }" 
                                            class="w-full p-3 rounded-lg text-left hover:shadow transition flex items-center gap-2 group">
                                        <span class="text-lg" x-text="cls.icon"></span>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-bold text-sm" :style="{ color: cls.color }" x-text="cls.code"></div>
                                            <div class="text-xs truncate" :style="{ color: cls.color }" x-text="cls.label"></div>
                                        </div>
                                    </button>
                                </template>
                            </div>
                        </div>
                        
                        {{-- Favorites --}}
                        <div x-show="showFavorites" class="p-3">
                            <h4 class="font-semibold text-gray-900 mb-2 text-sm flex items-center gap-2">
                                <i class="fas fa-star text-yellow-500"></i> Favorit
                            </h4>
                            <div x-show="favorites.length === 0" class="text-center py-6 text-gray-400 text-sm">
                                <i class="fas fa-star text-2xl mb-2"></i>
                                <p>Belum ada favorit</p>
                            </div>
                            <div class="space-y-1.5">
                                <template x-for="fav in favorites" :key="fav.code">
                                    <button type="button" @click="select(fav.code, fav.description)" 
                                            class="w-full p-2 bg-white rounded-lg border border-gray-200 text-left hover:border-indigo-300 transition">
                                        <div class="font-mono font-bold text-indigo-600 text-sm" x-text="fav.code"></div>
                                        <div class="text-xs text-gray-600 truncate" x-text="fav.description"></div>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Right Panel: Results --}}
                    <div class="flex-1 overflow-y-auto">
                        <div x-show="results.length > 0" class="p-3 space-y-2">
                            <template x-for="ddc in results" :key="ddc.code">
                                <div :class="selectedCode === ddc.code ? 'bg-indigo-50 border-indigo-300' : 'bg-white border-gray-200 hover:border-indigo-200'" 
                                     class="border rounded-xl p-3 transition cursor-pointer group"
                                     @click="select(ddc.code, ddc.description)">
                                    <div class="flex items-start gap-3">
                                        <span :class="selectedCode === ddc.code ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-indigo-600'" 
                                              class="px-2 py-1 rounded-lg font-mono font-bold text-sm" x-text="ddc.code"></span>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-gray-900 text-sm" x-html="highlightSearch(getMainDescription(ddc.description), search)"></div>
                                            <div x-show="getAdditionalInfo(ddc.description)" class="text-xs text-gray-500 mt-1 line-clamp-2" x-text="getAdditionalInfo(ddc.description)"></div>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <button @click.stop="toggleFavorite(ddc)" 
                                                    :class="isFavorite(ddc.code) ? 'text-yellow-500' : 'text-gray-300 hover:text-yellow-500'"
                                                    class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-yellow-50 transition">
                                                <i class="fas fa-star text-sm"></i>
                                            </button>
                                            <i x-show="selectedCode === ddc.code" class="fas fa-check-circle text-indigo-600"></i>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Loading --}}
                        <div x-show="loading" class="flex items-center justify-center py-12">
                            <i class="fas fa-spinner fa-spin text-2xl text-indigo-600"></i>
                        </div>

                        {{-- No Results --}}
                        <div x-show="search.length >= 1 && results.length === 0 && !loading" class="text-center py-12">
                            <i class="fas fa-search text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-600">Tidak ditemukan</p>
                        </div>

                        {{-- Initial State --}}
                        <div x-show="search.length < 1 && results.length === 0 && !showFavorites && aiRecommendations.length === 0" class="text-center py-12">
                            <i class="fas fa-layer-group text-4xl text-indigo-300 mb-3"></i>
                            <p class="text-gray-600">Pilih kelas utama atau mulai mengetik</p>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="bg-gray-50 border-t border-gray-200">
                    <div x-show="selectedCode" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                                <span class="font-mono font-bold text-sm" x-text="selectedCode"></span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-xs text-blue-200">Terpilih</div>
                                <div class="text-sm truncate" x-text="getMainDescription(selectedDesc)"></div>
                            </div>
                            <button @click="selectedCode = null; selectedDesc = ''" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/20">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="px-4 py-3 flex items-center justify-between">
                        <div x-show="!selectedCode" class="text-sm text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i> Pilih klasifikasi DDC
                        </div>
                        <div x-show="selectedCode" class="text-sm text-green-600 font-medium">
                            <i class="fas fa-check-circle mr-1"></i> Siap digunakan
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="open = false" class="px-4 py-2 text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 text-sm font-medium">
                                Batal
                            </button>
                            <button type="button" @click="apply()" :disabled="!selectedCode" 
                                    :class="selectedCode ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-gray-200 text-gray-400 cursor-not-allowed'" 
                                    class="px-6 py-2 font-semibold rounded-xl transition text-sm">
                                <i class="fas fa-check mr-1"></i> Gunakan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
function smartDdcModal() {
    return {
        open: false,
        search: '',
        results: [],
        selectedCode: null,
        selectedDesc: '',
        loading: false,
        showFavorites: false,
        favorites: JSON.parse(localStorage.getItem('ddc_favorites') || '[]'),
        
        // AI Recommendation
        bookTitle: '',
        aiRecommendations: [],
        aiSummary: null,
        aiKeywords: [],
        aiLoading: false,
        
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
        
        openModal() {
            this.open = true;
            // Get title from Livewire component
            const titleEl = document.querySelector('textarea[wire\\:model="title"]');
            if (titleEl && titleEl.value.length >= 5) {
                this.bookTitle = titleEl.value;
                this.getAiRecommendations();
            }
        },
        
        async getAiRecommendations() {
            if (!this.bookTitle || this.bookTitle.length < 5) return;
            
            this.aiLoading = true;
            try {
                const res = await fetch('/api/ddc/recommend?title=' + encodeURIComponent(this.bookTitle));
                const data = await res.json();
                this.aiRecommendations = data.recommendations || [];
                this.aiSummary = data.summary || null;
                this.aiKeywords = data.keywords_found || [];
            } catch (e) {
                console.error('AI recommendation error:', e);
            }
            this.aiLoading = false;
        },
        
        selectClass(code) {
            const searchMap = {
                '000': '0', '100': '1', '200': '2', '300': '3', '400': '4',
                '500': '5', '600': '6', '700': '7', '800': '8', '900': '9', '2X': '2X'
            };
            this.search = searchMap[code] || code;
            this.doSearch();
        },
        
        async doSearch() {
            if (!this.search || this.search.length < 1) { 
                this.results = []; 
                return; 
            }
            this.loading = true;
            try {
                const res = await fetch('/api/ddc/search?q=' + encodeURIComponent(this.search) + '&limit=300');
                this.results = await res.json() || [];
            } catch (e) { 
                this.results = []; 
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
        
        getMainDescription(description) {
            if (!description) return '';
            const parts = description.split(/\s{2,}/);
            return parts[0] || description.substring(0, 100);
        },
        
        getAdditionalInfo(description) {
            if (!description) return '';
            const parts = description.split(/\s{2,}/);
            return parts.slice(1).filter(p => !p.toLowerCase().includes('lihat juga')).join(' ').substring(0, 150);
        },
        
        toggleFavorite(ddc) {
            const index = this.favorites.findIndex(f => f.code === ddc.code);
            if (index >= 0) {
                this.favorites.splice(index, 1);
            } else {
                this.favorites.push({ code: ddc.code, description: this.getMainDescription(ddc.description) });
                if (this.favorites.length > 20) this.favorites = this.favorites.slice(-20);
            }
            localStorage.setItem('ddc_favorites', JSON.stringify(this.favorites));
        },
        
        isFavorite(code) {
            return this.favorites.some(f => f.code === code);
        },
        
        highlightSearch(text, searchTerm) {
            if (!text || !searchTerm || searchTerm.length < 2) return text;
            const terms = searchTerm.toLowerCase().split(/\s+/).filter(t => t.length >= 2);
            let highlighted = text;
            terms.forEach(term => {
                const regex = new RegExp(`(${term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
                highlighted = highlighted.replace(regex, '<mark class="bg-yellow-200 px-0.5 rounded">$1</mark>');
            });
            return highlighted;
        }
    }
}
</script>
