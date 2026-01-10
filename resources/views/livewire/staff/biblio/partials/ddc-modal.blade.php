{{-- Smart DDC Modal with AI Recommendations --}}
<template x-teleport="body">
    <div x-data="smartDdcModal()" x-show="open" x-cloak @open-ddc-modal.window="openModal()" @keydown.escape.window="open = false" style="position: fixed; inset: 0; z-index: 99999;">
        <div x-show="open" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="position: fixed; inset: 0; background: rgba(0,0,0,0.6);" @click="open = false"></div>
        
        <div style="position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; padding: 1rem; pointer-events: none;">
            <div x-show="open" x-transition class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-hidden flex flex-col" style="pointer-events: auto;">
                
                {{-- Header --}}
                <div class="px-5 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white flex items-center justify-between flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-layer-group text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold">DDC Classification</h3>
                            <p class="text-blue-200 text-xs">e-DDC Edition 23 • 4,715 Klasifikasi</p>
                        </div>
                    </div>
                    <button @click="open = false" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/20 transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- Scrollable Content Area --}}
                <div class="flex-1 overflow-y-auto">
                    {{-- AI Recommendation Panel --}}
                    <div x-show="aiLoading || aiRecommendations.length > 0" class="border-b border-gray-200 bg-blue-50/50">
                        <div class="px-4 py-3">
                            <div class="flex items-center gap-2 mb-2">
                                <i class="fas fa-magic text-blue-600 text-sm"></i>
                                <span class="font-medium text-gray-800 text-sm">Rekomendasi AI</span>
                                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-[10px] font-medium rounded">Local AI</span>
                                <span x-show="bookTitle" class="text-xs text-gray-500 truncate max-w-xs ml-auto">"<span x-text="bookTitle.substring(0, 40)"></span>..."</span>
                            </div>
                            
                            {{-- Loading --}}
                            <div x-show="aiLoading" class="flex items-center gap-2 py-2 text-sm text-gray-600">
                                <div class="w-4 h-4 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                                <span>Menganalisis judul...</span>
                            </div>
                            
                            {{-- Summary --}}
                            <div x-show="aiSummary && !aiLoading" class="mb-2 p-2 rounded-lg text-sm" :class="{
                                'bg-green-50 text-green-800': aiSummary?.status === 'confident',
                                'bg-blue-50 text-blue-800': aiSummary?.status === 'suggested',
                                'bg-gray-100 text-gray-700': aiSummary?.status !== 'confident' && aiSummary?.status !== 'suggested'
                            }">
                                <i class="fas fa-lightbulb mr-1"></i>
                                <span x-text="aiSummary?.message?.replace('🤖 AI merekomendasikan', 'Rekomendasi:').replace('📚 ', '')"></span>
                            </div>
                            
                            {{-- Compact Recommendation Pills --}}
                            <div x-show="aiRecommendations.length > 0 && !aiLoading" class="flex flex-wrap gap-1.5">
                                <template x-for="rec in aiRecommendations" :key="rec.code">
                                    <button @click="select(rec.code, rec.description)" type="button"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-sm border transition"
                                        :class="selectedCode === rec.code 
                                            ? 'bg-blue-600 border-blue-600 text-white' 
                                            : 'bg-white border-gray-200 hover:border-blue-400 text-gray-700'">
                                        <span class="font-mono font-bold" x-text="rec.code"></span>
                                        <span class="text-xs truncate max-w-[120px]" x-text="rec.description.split('/')[0].substring(0, 20)"></span>
                                        <span class="w-1.5 h-1.5 rounded-full flex-shrink-0"
                                            :class="{
                                                'bg-green-400': rec.confidence === 'high',
                                                'bg-yellow-400': rec.confidence === 'medium',
                                                'bg-gray-400': rec.confidence === 'low'
                                            }"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Search Bar --}}
                    <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex-shrink-0">
                        <div class="flex gap-2">
                            <div class="flex-1 relative">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                <input type="text" x-model="search" @input.debounce.300ms="doSearch()" 
                                       class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 bg-white" 
                                       placeholder="Cari kode DDC atau deskripsi...">
                            </div>
                            <button @click="showFavorites = !showFavorites" 
                                    :class="showFavorites ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 border border-gray-300'"
                                    class="px-3 py-2 rounded-lg transition">
                                <i class="fas fa-star" :class="showFavorites ? '' : 'text-yellow-500'"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Main Content --}}
                    <div class="flex min-h-[300px]">
                        {{-- Left: Main Classes --}}
                        <div class="w-56 border-r border-gray-200 bg-gray-50/50 overflow-y-auto flex-shrink-0">
                            <div x-show="!showFavorites" class="p-2 space-y-1">
                                <template x-for="cls in mainClasses" :key="cls.code">
                                    <button type="button" @click="selectClass(cls.code)" 
                                            class="w-full p-2 rounded-lg text-left hover:bg-blue-50 transition flex items-center gap-2 text-sm">
                                        <span x-text="cls.icon"></span>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-bold text-blue-700" x-text="cls.code"></div>
                                            <div class="text-xs text-gray-600 truncate" x-text="cls.label"></div>
                                        </div>
                                    </button>
                                </template>
                            </div>
                            
                            <div x-show="showFavorites" class="p-2">
                                <div x-show="favorites.length === 0" class="text-center py-6 text-gray-400 text-sm">
                                    <i class="fas fa-star text-xl mb-1"></i>
                                    <p>Belum ada favorit</p>
                                </div>
                                <div class="space-y-1">
                                    <template x-for="fav in favorites" :key="fav.code">
                                        <button type="button" @click="select(fav.code, fav.description)" 
                                                class="w-full p-2 bg-white rounded-lg border border-gray-200 text-left hover:border-blue-400 transition text-sm">
                                            <div class="font-mono font-bold text-blue-600" x-text="fav.code"></div>
                                            <div class="text-xs text-gray-600 truncate" x-text="fav.description"></div>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        {{-- Right: Results --}}
                        <div class="flex-1 overflow-y-auto">
                            <div x-show="results.length > 0" class="p-2 space-y-1">
                                <template x-for="ddc in results" :key="ddc.code">
                                    <div :class="selectedCode === ddc.code ? 'bg-blue-50 border-blue-400' : 'bg-white border-gray-200 hover:border-blue-300'" 
                                         class="border rounded-lg p-2.5 transition cursor-pointer"
                                         @click="select(ddc.code, ddc.description)">
                                        <div class="flex items-start gap-2">
                                            <span :class="selectedCode === ddc.code ? 'bg-blue-600 text-white' : 'bg-gray-100 text-blue-600'" 
                                                  class="px-2 py-0.5 rounded font-mono font-bold text-sm flex-shrink-0" x-text="ddc.code"></span>
                                            <div class="flex-1 min-w-0 text-sm">
                                                <div class="text-gray-800" x-html="highlightSearch(getMainDescription(ddc.description), search)"></div>
                                            </div>
                                            <div class="flex items-center gap-1 flex-shrink-0">
                                                <button @click.stop="toggleFavorite(ddc)" 
                                                        :class="isFavorite(ddc.code) ? 'text-yellow-500' : 'text-gray-300 hover:text-yellow-500'"
                                                        class="w-6 h-6 flex items-center justify-center">
                                                    <i class="fas fa-star text-xs"></i>
                                                </button>
                                                <i x-show="selectedCode === ddc.code" class="fas fa-check-circle text-blue-600 text-sm"></i>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div x-show="loading" class="flex items-center justify-center py-12">
                                <div class="w-5 h-5 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                            </div>

                            <div x-show="search.length >= 1 && results.length === 0 && !loading" class="text-center py-12 text-gray-400">
                                <i class="fas fa-search text-2xl mb-2"></i>
                                <p class="text-sm">Tidak ditemukan</p>
                            </div>

                            <div x-show="search.length < 1 && results.length === 0 && !showFavorites && aiRecommendations.length === 0 && !aiLoading" class="text-center py-12 text-gray-400">
                                <i class="fas fa-layer-group text-2xl mb-2"></i>
                                <p class="text-sm">Pilih kelas utama atau ketik pencarian</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="border-t border-gray-200 bg-gray-50 flex-shrink-0">
                    <div x-show="selectedCode" class="px-4 py-2 bg-blue-600 text-white">
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold" x-text="selectedCode"></span>
                            <span class="text-blue-200">—</span>
                            <span class="text-sm truncate" x-text="getMainDescription(selectedDesc)"></span>
                            <button @click="selectedCode = null; selectedDesc = ''" class="ml-auto w-6 h-6 flex items-center justify-center rounded hover:bg-white/20">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="px-4 py-2.5 flex items-center justify-between">
                        <div class="text-xs text-gray-500">
                            <span x-show="!selectedCode">Pilih klasifikasi DDC</span>
                            <span x-show="selectedCode" class="text-green-600"><i class="fas fa-check mr-1"></i>Siap digunakan</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="open = false" class="px-3 py-1.5 text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 text-sm">
                                Batal
                            </button>
                            <button type="button" @click="apply()" :disabled="!selectedCode" 
                                    :class="selectedCode ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-gray-200 text-gray-400 cursor-not-allowed'" 
                                    class="px-4 py-1.5 font-medium rounded-lg transition text-sm">
                                Gunakan
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
        open: false, search: '', results: [], selectedCode: null, selectedDesc: '', loading: false,
        showFavorites: false, favorites: JSON.parse(localStorage.getItem('ddc_favorites') || '[]'),
        bookTitle: '', aiRecommendations: [], aiSummary: null, aiKeywords: [], aiLoading: false,
        
        mainClasses: [
            {code: '000', label: 'Karya Umum', icon: '💻'},
            {code: '100', label: 'Filsafat', icon: '🧠'},
            {code: '200', label: 'Agama', icon: '🕌'},
            {code: '2X', label: 'Islam', icon: '☪️'},
            {code: '300', label: 'Ilmu Sosial', icon: '👥'},
            {code: '400', label: 'Bahasa', icon: '🗣️'},
            {code: '500', label: 'Sains', icon: '🔬'},
            {code: '600', label: 'Teknologi', icon: '⚙️'},
            {code: '700', label: 'Seni', icon: '🎨'},
            {code: '800', label: 'Sastra', icon: '📚'},
            {code: '900', label: 'Sejarah', icon: '🌍'},
        ],
        
        openModal() {
            this.open = true;
            const titleEl = document.querySelector('textarea[wire\\:model="title"]') 
                         || document.querySelector('textarea[wire\\:model\\.live="title"]');
            if (titleEl && titleEl.value && titleEl.value.length >= 5) {
                this.bookTitle = titleEl.value;
                this.getAiRecommendations();
            } else {
                try {
                    const title = @this.get('title');
                    if (title && title.length >= 5) { this.bookTitle = title; this.getAiRecommendations(); }
                } catch(e) {}
            }
        },
        
        async getAiRecommendations() {
            if (!this.bookTitle || this.bookTitle.length < 5) return;
            this.aiLoading = true;
            this.aiRecommendations = []; this.aiSummary = null;
            try {
                const res = await fetch('/api/ddc/recommend?title=' + encodeURIComponent(this.bookTitle));
                const data = await res.json();
                this.aiRecommendations = data.recommendations || [];
                this.aiSummary = data.summary || null;
            } catch (e) {}
            this.aiLoading = false;
        },
        
        selectClass(code) {
            const map = {'000':'0','100':'1','200':'2','300':'3','400':'4','500':'5','600':'6','700':'7','800':'8','900':'9','2X':'2X'};
            this.search = map[code] || code;
            this.doSearch();
        },
        
        async doSearch() {
            if (!this.search || this.search.length < 1) { this.results = []; return; }
            this.loading = true;
            try {
                const res = await fetch('/api/ddc/search?q=' + encodeURIComponent(this.search) + '&limit=200');
                this.results = await res.json() || [];
            } catch (e) { this.results = []; }
            this.loading = false;
        },
        
        select(code, desc) { this.selectedCode = code; this.selectedDesc = desc; },
        apply() { if (!this.selectedCode) return; @this.set('classification', this.selectedCode); this.open = false; },
        getMainDescription(desc) { return desc ? (desc.split(/\s{2,}/)[0] || desc).substring(0, 80) : ''; },
        
        toggleFavorite(ddc) {
            const idx = this.favorites.findIndex(f => f.code === ddc.code);
            if (idx >= 0) this.favorites.splice(idx, 1);
            else { this.favorites.push({ code: ddc.code, description: this.getMainDescription(ddc.description) }); }
            if (this.favorites.length > 20) this.favorites = this.favorites.slice(-20);
            localStorage.setItem('ddc_favorites', JSON.stringify(this.favorites));
        },
        
        isFavorite(code) { return this.favorites.some(f => f.code === code); },
        
        highlightSearch(text, term) {
            if (!text || !term || term.length < 2) return text;
            const words = term.toLowerCase().split(/\s+/).filter(w => w.length >= 2);
            let result = text;
            words.forEach(word => {
                const regex = new RegExp(`(${word.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
                result = result.replace(regex, '<mark class="bg-yellow-200 rounded px-0.5">$1</mark>');
            });
            return result;
        }
    }
}
</script>
