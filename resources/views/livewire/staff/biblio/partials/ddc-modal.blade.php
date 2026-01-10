{{-- Smart DDC Modal with AI Recommendations --}}
<template x-teleport="body">
    <div x-data="smartDdcModal()" x-show="open" x-cloak @open-ddc-modal.window="openModal()" @keydown.escape.window="open = false" style="position: fixed; inset: 0; z-index: 99999;">
        <div x-show="open" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="position: fixed; inset: 0; background: rgba(0,0,0,0.75);" @click="open = false"></div>
        
        <div style="position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; padding: 1rem; pointer-events: none;">
            <div x-show="open" x-transition class="bg-white rounded-2xl shadow-2xl w-full max-w-6xl max-h-[92vh] overflow-hidden flex flex-col" style="pointer-events: auto;">
                
                {{-- Header --}}
                <div class="px-5 py-4 bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-700 text-white flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 bg-white/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-brain text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold">Smart DDC Classification</h3>
                            <p class="text-indigo-200 text-xs">AI-Powered • e-DDC Edition 23</p>
                        </div>
                    </div>
                    <button @click="open = false" class="w-9 h-9 flex items-center justify-center rounded-lg hover:bg-white/20 transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- AI Recommendation Panel --}}
                <div x-show="aiLoading || aiRecommendations.length > 0" class="border-b border-gray-200 bg-gradient-to-r from-violet-50 via-purple-50 to-indigo-50">
                    <div class="px-5 py-4">
                        {{-- Title being analyzed --}}
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-7 h-7 bg-gradient-to-br from-violet-500 to-purple-600 rounded-lg flex items-center justify-center shadow-sm">
                                <i class="fas fa-magic text-white text-xs"></i>
                            </div>
                            <span class="font-semibold text-gray-800 text-sm">Rekomendasi AI</span>
                            <span x-show="aiSource === 'ai'" class="px-2 py-0.5 bg-violet-100 text-violet-700 text-[10px] font-medium rounded-full">🤖 Groq AI</span>
                            <span x-show="aiSource === 'keyword'" class="px-2 py-0.5 bg-blue-100 text-blue-700 text-[10px] font-medium rounded-full">📚 Keyword</span>
                        </div>
                        
                        {{-- Book title --}}
                        <div x-show="bookTitle" class="mb-3 p-2.5 bg-white/80 rounded-lg border border-purple-100">
                            <div class="text-[10px] text-gray-500 uppercase tracking-wide mb-0.5">Judul Buku</div>
                            <div class="text-sm text-gray-800 font-medium line-clamp-2" x-text="bookTitle"></div>
                        </div>
                        
                        {{-- Loading --}}
                        <div x-show="aiLoading" class="flex items-center gap-3 py-3">
                            <div class="w-5 h-5 border-2 border-purple-500 border-t-transparent rounded-full animate-spin"></div>
                            <span class="text-gray-600 text-sm">Menganalisis dengan AI...</span>
                        </div>
                        
                        {{-- Summary --}}
                        <div x-show="aiSummary && !aiLoading" class="mb-3 p-3 rounded-xl border" :class="{
                            'bg-emerald-50 border-emerald-200': aiSummary?.status === 'confident',
                            'bg-blue-50 border-blue-200': aiSummary?.status === 'suggested',
                            'bg-amber-50 border-amber-200': aiSummary?.status === 'review',
                            'bg-gray-50 border-gray-200': aiSummary?.status === 'no_match'
                        }">
                            <div class="flex items-start gap-2">
                                <i class="fas mt-0.5 text-sm" :class="{
                                    'fa-check-circle text-emerald-600': aiSummary?.status === 'confident',
                                    'fa-lightbulb text-blue-600': aiSummary?.status === 'suggested',
                                    'fa-exclamation-triangle text-amber-600': aiSummary?.status === 'review',
                                    'fa-info-circle text-gray-500': aiSummary?.status === 'no_match'
                                }"></i>
                                <div class="flex-1">
                                    <p class="text-sm font-medium" :class="{
                                        'text-emerald-800': aiSummary?.status === 'confident',
                                        'text-blue-800': aiSummary?.status === 'suggested',
                                        'text-amber-800': aiSummary?.status === 'review',
                                        'text-gray-700': aiSummary?.status === 'no_match'
                                    }" x-text="aiSummary?.message"></p>
                                    <div x-show="aiKeywords.length > 0" class="mt-1.5 flex flex-wrap gap-1">
                                        <template x-for="kw in aiKeywords.slice(0, 8)" :key="kw">
                                            <span class="px-2 py-0.5 bg-white/80 text-gray-600 text-[11px] rounded-md border border-gray-200" x-text="kw"></span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Recommendation Cards --}}
                        <div x-show="aiRecommendations.length > 0 && !aiLoading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                            <template x-for="(rec, idx) in aiRecommendations" :key="rec.code">
                                <button @click="select(rec.code, rec.description)" type="button"
                                    class="p-3 rounded-xl border-2 text-left transition-all group"
                                    :class="selectedCode === rec.code 
                                        ? 'bg-gradient-to-br from-indigo-500 to-purple-600 border-transparent text-white shadow-lg' 
                                        : 'bg-white border-gray-200 hover:border-purple-300 hover:shadow-md'">
                                    <div class="flex items-start justify-between gap-2 mb-1.5">
                                        <span class="font-mono font-bold text-base" :class="selectedCode === rec.code ? 'text-white' : 'text-indigo-600'" x-text="rec.code"></span>
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold flex-shrink-0"
                                            :class="{
                                                'bg-emerald-400 text-white': rec.confidence === 'high' && selectedCode !== rec.code,
                                                'bg-blue-400 text-white': rec.confidence === 'medium' && selectedCode !== rec.code,
                                                'bg-gray-400 text-white': rec.confidence === 'low' && selectedCode !== rec.code,
                                                'bg-white/30 text-white': selectedCode === rec.code
                                            }">
                                            <span x-text="rec.confidence === 'high' ? '✓ Tinggi' : rec.confidence === 'medium' ? '~ Sedang' : '? Rendah'"></span>
                                        </span>
                                    </div>
                                    <div class="text-xs mb-1" :class="selectedCode === rec.code ? 'text-indigo-100' : 'text-gray-700'" x-text="rec.description"></div>
                                    <div x-show="rec.reason" class="text-[10px] italic" :class="selectedCode === rec.code ? 'text-indigo-200' : 'text-gray-500'" x-text="rec.reason"></div>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Search Bar --}}
                <div class="px-5 py-3 bg-gray-50 border-b border-gray-200">
                    <div class="flex gap-2">
                        <div class="flex-1 relative">
                            <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="text" x-model="search" @input.debounce.300ms="doSearch()" 
                                   class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 bg-white" 
                                   placeholder="Cari kode DDC atau deskripsi...">
                        </div>
                        <button @click="showFavorites = !showFavorites" 
                                :class="showFavorites ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 border border-gray-300 hover:border-indigo-400'"
                                class="px-4 py-2.5 rounded-xl font-medium transition flex items-center gap-2 text-sm">
                            <i class="fas fa-star" :class="showFavorites ? '' : 'text-yellow-500'"></i>
                        </button>
                    </div>
                </div>

                {{-- Content --}}
                <div class="flex flex-1 min-h-0">
                    {{-- Left: Main Classes --}}
                    <div class="w-64 border-r border-gray-200 bg-gray-50/50 overflow-y-auto">
                        <div x-show="!showFavorites" class="p-3 space-y-1">
                            <template x-for="cls in mainClasses" :key="cls.code">
                                <button type="button" @click="selectClass(cls.code)" 
                                        :style="{ background: cls.bg }" 
                                        class="w-full p-2.5 rounded-lg text-left hover:shadow transition flex items-center gap-2">
                                    <span class="text-base" x-text="cls.icon"></span>
                                    <div class="flex-1 min-w-0">
                                        <div class="font-bold text-sm" :style="{ color: cls.color }" x-text="cls.code"></div>
                                        <div class="text-[11px] truncate" :style="{ color: cls.color }" x-text="cls.label"></div>
                                    </div>
                                </button>
                            </template>
                        </div>
                        
                        <div x-show="showFavorites" class="p-3">
                            <div x-show="favorites.length === 0" class="text-center py-8 text-gray-400 text-sm">
                                <i class="fas fa-star text-2xl mb-2"></i>
                                <p>Belum ada favorit</p>
                            </div>
                            <div class="space-y-1">
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

                    {{-- Right: Results --}}
                    <div class="flex-1 overflow-y-auto">
                        <div x-show="results.length > 0" class="p-3 space-y-1.5">
                            <template x-for="ddc in results" :key="ddc.code">
                                <div :class="selectedCode === ddc.code ? 'bg-indigo-50 border-indigo-400 shadow-sm' : 'bg-white border-gray-200 hover:border-indigo-300'" 
                                     class="border rounded-xl p-3 transition cursor-pointer"
                                     @click="select(ddc.code, ddc.description)">
                                    <div class="flex items-start gap-3">
                                        <span :class="selectedCode === ddc.code ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-indigo-600'" 
                                              class="px-2 py-1 rounded-lg font-mono font-bold text-sm flex-shrink-0" x-text="ddc.code"></span>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-gray-900 text-sm leading-relaxed" x-html="highlightSearch(getMainDescription(ddc.description), search)"></div>
                                            <div x-show="getAdditionalInfo(ddc.description)" class="text-xs text-gray-500 mt-1 line-clamp-2" x-html="highlightSearch(getAdditionalInfo(ddc.description), search)"></div>
                                        </div>
                                        <div class="flex items-center gap-1 flex-shrink-0">
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

                        <div x-show="loading" class="flex items-center justify-center py-16">
                            <div class="w-6 h-6 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                        </div>

                        <div x-show="search.length >= 1 && results.length === 0 && !loading" class="text-center py-16 text-gray-400">
                            <i class="fas fa-search text-3xl mb-2"></i>
                            <p>Tidak ditemukan</p>
                        </div>

                        <div x-show="search.length < 1 && results.length === 0 && !showFavorites && aiRecommendations.length === 0 && !aiLoading" class="text-center py-16 text-gray-400">
                            <i class="fas fa-layer-group text-3xl mb-2"></i>
                            <p>Pilih kelas utama atau ketik pencarian</p>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="border-t border-gray-200 bg-gray-50">
                    <div x-show="selectedCode" class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-white/20 rounded-lg flex items-center justify-center font-mono font-bold text-sm" x-text="selectedCode"></div>
                            <div class="flex-1 min-w-0">
                                <div class="text-[10px] text-indigo-200 uppercase">Terpilih</div>
                                <div class="text-sm truncate" x-text="getMainDescription(selectedDesc)"></div>
                            </div>
                            <button @click="selectedCode = null; selectedDesc = ''" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-white/20">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="px-4 py-3 flex items-center justify-between">
                        <div class="text-xs text-gray-500">
                            <span x-show="!selectedCode"><i class="fas fa-info-circle mr-1"></i> Pilih klasifikasi DDC</span>
                            <span x-show="selectedCode" class="text-emerald-600 font-medium"><i class="fas fa-check-circle mr-1"></i> Siap digunakan</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="open = false" class="px-4 py-2 text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 text-sm font-medium">
                                Batal
                            </button>
                            <button type="button" @click="apply()" :disabled="!selectedCode" 
                                    :class="selectedCode ? 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm' : 'bg-gray-200 text-gray-400 cursor-not-allowed'" 
                                    class="px-5 py-2 font-semibold rounded-lg transition text-sm">
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
        
        bookTitle: '',
        aiRecommendations: [],
        aiSummary: null,
        aiKeywords: [],
        aiLoading: false,
        aiSource: '',
        
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
            // Try multiple selectors to find title
            const titleEl = document.querySelector('textarea[wire\\:model="title"]') 
                         || document.querySelector('textarea[wire\\:model\\.live="title"]')
                         || document.querySelector('textarea[wire\\:model\\.defer="title"]');
            
            if (titleEl && titleEl.value && titleEl.value.length >= 5) {
                this.bookTitle = titleEl.value;
                this.getAiRecommendations();
            } else {
                // Fallback: try to get from Livewire component
                try {
                    const title = @this.get('title');
                    if (title && title.length >= 5) {
                        this.bookTitle = title;
                        this.getAiRecommendations();
                    }
                } catch(e) {}
            }
        },
        
        async getAiRecommendations() {
            if (!this.bookTitle || this.bookTitle.length < 5) return;
            
            this.aiLoading = true;
            this.aiRecommendations = [];
            this.aiSummary = null;
            this.aiKeywords = [];
            
            try {
                const res = await fetch('/api/ddc/recommend?title=' + encodeURIComponent(this.bookTitle));
                const data = await res.json();
                this.aiRecommendations = data.recommendations || [];
                this.aiSummary = data.summary || null;
                this.aiKeywords = data.keywords_found || [];
                this.aiSource = data.source || 'keyword';
            } catch (e) {
                console.error('AI error:', e);
            }
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
                const res = await fetch('/api/ddc/search?q=' + encodeURIComponent(this.search) + '&limit=300');
                this.results = await res.json() || [];
            } catch (e) { this.results = []; }
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
        },
        
        getMainDescription(desc) {
            if (!desc) return '';
            return (desc.split(/\s{2,}/)[0] || desc).substring(0, 120);
        },
        
        getAdditionalInfo(desc) {
            if (!desc) return '';
            const parts = desc.split(/\s{2,}/);
            return parts.slice(1).filter(p => !p.toLowerCase().includes('lihat juga')).join(' ').substring(0, 150);
        },
        
        toggleFavorite(ddc) {
            const idx = this.favorites.findIndex(f => f.code === ddc.code);
            if (idx >= 0) this.favorites.splice(idx, 1);
            else {
                this.favorites.push({ code: ddc.code, description: this.getMainDescription(ddc.description) });
                if (this.favorites.length > 20) this.favorites = this.favorites.slice(-20);
            }
            localStorage.setItem('ddc_favorites', JSON.stringify(this.favorites));
        },
        
        isFavorite(code) {
            return this.favorites.some(f => f.code === code);
        },
        
        // IMPROVED: Multi-word phrase highlighting
        highlightSearch(text, searchTerm) {
            if (!text || !searchTerm || searchTerm.length < 1) return text;
            
            const escapeRegex = (str) => str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            let highlighted = text;
            
            // Get words from search term
            const words = searchTerm.toLowerCase().trim().split(/\s+/).filter(w => w.length >= 2);
            if (words.length === 0) return text;
            
            // Strategy 1: Try full phrase match first
            if (searchTerm.trim().length >= 3) {
                const phraseRegex = new RegExp(`(${escapeRegex(searchTerm.trim())})`, 'gi');
                if (phraseRegex.test(text)) {
                    return text.replace(phraseRegex, '<mark class="bg-yellow-300 px-0.5 rounded font-semibold">$1</mark>');
                }
            }
            
            // Strategy 2: Try consecutive 2-word combinations
            if (words.length >= 2) {
                for (let i = 0; i < words.length - 1; i++) {
                    const phrase = words[i] + '\\s+' + words[i + 1];
                    const phraseRegex = new RegExp(`(${phrase})`, 'gi');
                    highlighted = highlighted.replace(phraseRegex, '<mark class="bg-yellow-300 px-0.5 rounded font-semibold">$1</mark>');
                }
            }
            
            // Strategy 3: Highlight individual words (different color)
            words.forEach(word => {
                const wordRegex = new RegExp(`(?<![\\w>])(${escapeRegex(word)})(?![\\w<])`, 'gi');
                highlighted = highlighted.replace(wordRegex, (match, p1) => {
                    // Don't double-highlight
                    if (highlighted.indexOf(`<mark`) > -1 && highlighted.indexOf(p1) < highlighted.indexOf(`<mark`)) {
                        return match;
                    }
                    return `<mark class="bg-amber-200/70 px-0.5 rounded">${p1}</mark>`;
                });
            });
            
            return highlighted;
        }
    }
}
</script>
