{{-- Smart DDC Modal with AI Recommendations --}}
<template x-teleport="body">
    <div x-data="smartDdcModal()" x-show="open" x-cloak @open-ddc-modal.window="openModal()" @keydown.escape.window="open = false" style="position: fixed; inset: 0; z-index: 99999;">
        <div x-show="open" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="position: fixed; inset: 0; background: rgba(0,0,0,0.6);" @click="open = false"></div>
        
        <div style="position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; padding: 1rem; pointer-events: none;">
            <div x-show="open" x-transition class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[88vh] overflow-hidden flex flex-col" style="pointer-events: auto;">
                
                {{-- Header --}}
                <div class="px-4 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white flex items-center justify-between flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-white/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-brain text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold">Smart DDC Classification</h3>
                            <p class="text-blue-200 text-xs">AI-Powered • e-DDC Edition 23</p>
                        </div>
                    </div>
                    <button @click="open = false" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/20 transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- Scrollable Content --}}
                <div class="flex-1 overflow-y-auto min-h-0">
                    {{-- AI Recommendation Panel --}}
                    <div x-show="aiLoading || aiRecommendations.length > 0" class="border-b border-gray-200 bg-gradient-to-r from-violet-50 via-purple-50 to-indigo-50">
                        <div class="px-4 py-3">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-5 h-5 bg-gradient-to-br from-violet-500 to-purple-600 rounded flex items-center justify-center">
                                    <i class="fas fa-magic text-white text-[10px]"></i>
                                </div>
                                <span class="font-semibold text-gray-800 text-sm">Rekomendasi AI</span>
                                <span class="px-1.5 py-0.5 bg-blue-100 text-blue-700 text-[10px] font-medium rounded">Local AI</span>
                            </div>
                            
                            {{-- Book Title --}}
                            <div x-show="bookTitle" class="mb-2 p-2 bg-white/80 rounded-lg border border-purple-100">
                                <div class="text-[10px] text-gray-500 uppercase tracking-wide">Judul Buku</div>
                                <div class="text-sm text-gray-800 font-medium" x-text="bookTitle"></div>
                            </div>
                            
                            <div x-show="aiLoading" class="flex items-center gap-2 py-2 text-sm text-gray-600">
                                <div class="w-4 h-4 border-2 border-purple-500 border-t-transparent rounded-full animate-spin"></div>
                                <span>Menganalisis judul...</span>
                            </div>
                            
                            {{-- Summary --}}
                            <div x-show="aiSummary && !aiLoading" class="mb-2 p-2 rounded-lg border text-xs" :class="{
                                'bg-emerald-50 border-emerald-200 text-emerald-800': aiSummary?.status === 'confident',
                                'bg-blue-50 border-blue-200 text-blue-800': aiSummary?.status === 'suggested',
                                'bg-gray-50 border-gray-200 text-gray-700': aiSummary?.status !== 'confident' && aiSummary?.status !== 'suggested'
                            }">
                                <div class="flex items-start gap-2">
                                    <i class="fas fa-lightbulb mt-0.5"></i>
                                    <div>
                                        <span x-text="aiSummary?.message?.replace('🤖 AI merekomendasikan', 'Rekomendasi:')"></span>
                                        <div x-show="aiKeywords?.length > 0" class="mt-1 flex flex-wrap gap-1">
                                            <template x-for="kw in aiKeywords.slice(0, 6)" :key="kw">
                                                <span class="px-1.5 py-0.5 bg-white text-gray-600 text-[10px] rounded border border-gray-200" x-text="kw"></span>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div x-show="aiRecommendations.length > 0 && !aiLoading" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-1.5">
                                <template x-for="rec in aiRecommendations" :key="rec.code">
                                    <button @click="select(rec.code, rec.description)" type="button"
                                        class="p-2 rounded-lg border text-left transition-all"
                                        :class="selectedCode === rec.code 
                                            ? 'bg-gradient-to-br from-indigo-500 to-purple-600 border-transparent text-white shadow' 
                                            : 'bg-white border-gray-200 hover:border-purple-300 hover:shadow-sm'">
                                        <div class="flex items-center justify-between gap-1 mb-0.5">
                                            <span class="font-mono font-bold text-sm" :class="selectedCode === rec.code ? 'text-white' : 'text-indigo-600'" x-text="rec.code"></span>
                                            <span class="w-2 h-2 rounded-full" :class="{'bg-emerald-400': rec.confidence === 'high', 'bg-yellow-400': rec.confidence === 'medium', 'bg-gray-400': rec.confidence === 'low'}"></span>
                                        </div>
                                        <div class="text-[10px] line-clamp-2" :class="selectedCode === rec.code ? 'text-indigo-100' : 'text-gray-600'" x-text="rec.description.split('/')[0]"></div>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Search Bar --}}
                    <div class="px-4 py-2 bg-gray-50 border-b border-gray-200">
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
                    <div class="flex min-h-[280px]">
                        {{-- Left: Main Classes --}}
                        <div class="w-56 border-r border-gray-200 bg-gray-50/50 overflow-y-auto flex-shrink-0">
                            <div x-show="!showFavorites" class="p-2 space-y-1">
                                <template x-for="cls in mainClasses" :key="cls.code">
                                    <button type="button" @click="selectClass(cls.code)" 
                                            :style="{ background: cls.bg }" 
                                            class="w-full p-2 rounded-lg text-left hover:shadow transition flex items-center gap-2">
                                        <span class="text-sm" x-text="cls.icon"></span>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-bold text-sm" :style="{ color: cls.color }" x-text="cls.code"></div>
                                            <div class="text-[10px] truncate" :style="{ color: cls.color }" x-text="cls.label"></div>
                                        </div>
                                    </button>
                                </template>
                            </div>
                            
                            <div x-show="showFavorites" class="p-2">
                                <div x-show="favorites.length === 0" class="text-center py-6 text-gray-400 text-sm">
                                    <i class="fas fa-star text-lg mb-1"></i>
                                    <p class="text-xs">Belum ada favorit</p>
                                </div>
                                <div class="space-y-1">
                                    <template x-for="fav in favorites" :key="fav.code">
                                        <button type="button" @click="select(fav.code, fav.description)" 
                                                class="w-full p-2 bg-white rounded-lg border border-gray-200 text-left hover:border-blue-300 transition text-sm">
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
                                                  class="px-1.5 py-0.5 rounded font-mono font-bold text-sm flex-shrink-0" x-text="ddc.code"></span>
                                            <div class="flex-1 min-w-0">
                                                <div class="text-sm text-gray-800" x-html="highlightSearch(getMainDescription(ddc.description), search)"></div>
                                                <div x-show="getAdditionalInfo(ddc.description)" class="text-xs text-gray-500 mt-0.5 line-clamp-1" x-html="highlightSearch(getAdditionalInfo(ddc.description), search)"></div>
                                            </div>
                                            <div class="flex items-center gap-1 flex-shrink-0">
                                                <button @click.stop="toggleFavorite(ddc)" :class="isFavorite(ddc.code) ? 'text-yellow-500' : 'text-gray-300 hover:text-yellow-500'" class="w-5 h-5 flex items-center justify-center">
                                                    <i class="fas fa-star text-xs"></i>
                                                </button>
                                                <i x-show="selectedCode === ddc.code" class="fas fa-check-circle text-blue-600 text-sm"></i>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div x-show="loading" class="flex items-center justify-center py-10">
                                <div class="w-5 h-5 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                            </div>

                            <div x-show="search.length >= 1 && results.length === 0 && !loading" class="text-center py-10 text-gray-400">
                                <i class="fas fa-search text-xl mb-1"></i>
                                <p class="text-sm">Tidak ditemukan</p>
                            </div>

                            <div x-show="search.length < 1 && results.length === 0 && !showFavorites && aiRecommendations.length === 0 && !aiLoading" class="text-center py-10 text-gray-400">
                                <i class="fas fa-layer-group text-xl mb-1"></i>
                                <p class="text-sm">Pilih kelas utama atau ketik pencarian</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="border-t border-gray-200 bg-gray-50 flex-shrink-0">
                    <div x-show="selectedCode" class="px-4 py-1.5 bg-blue-600 text-white text-sm">
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold" x-text="selectedCode"></span>
                            <span class="text-blue-300">—</span>
                            <span class="truncate flex-1" x-text="getMainDescription(selectedDesc)"></span>
                            <button @click="selectedCode = null; selectedDesc = ''" class="w-5 h-5 flex items-center justify-center rounded hover:bg-white/20">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="px-4 py-2 flex items-center justify-between">
                        <div class="text-xs text-gray-500">
                            <span x-show="!selectedCode">Pilih klasifikasi DDC</span>
                            <span x-show="selectedCode" class="text-green-600"><i class="fas fa-check mr-1"></i>Siap</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="open = false" class="px-3 py-1.5 text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 text-sm">Batal</button>
                            <button type="button" @click="apply()" :disabled="!selectedCode" 
                                    :class="selectedCode ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-gray-200 text-gray-400 cursor-not-allowed'" 
                                    class="px-4 py-1.5 font-medium rounded-lg transition text-sm">Gunakan</button>
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
            const titleEl = document.querySelector('textarea[wire\\:model="title"]') || document.querySelector('textarea[wire\\:model\\.live="title"]');
            if (titleEl?.value?.length >= 5) { this.bookTitle = titleEl.value; this.getAiRecommendations(); }
            else { try { const t = @this.get('title'); if (t?.length >= 5) { this.bookTitle = t; this.getAiRecommendations(); } } catch(e) {} }
        },
        
        async getAiRecommendations() {
            if (!this.bookTitle || this.bookTitle.length < 5) return;
            this.aiLoading = true; this.aiRecommendations = []; this.aiSummary = null;
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
            this.search = map[code] || code; this.doSearch();
        },
        
        async doSearch() {
            if (!this.search || this.search.length < 1) { this.results = []; return; }
            this.loading = true;
            try { this.results = await (await fetch('/api/ddc/search?q=' + encodeURIComponent(this.search) + '&limit=200')).json() || []; }
            catch (e) { this.results = []; }
            this.loading = false;
        },
        
        select(code, desc) { this.selectedCode = code; this.selectedDesc = desc; },
        apply() { if (this.selectedCode) { @this.set('classification', this.selectedCode); this.open = false; } },
        getMainDescription(d) { return d ? (d.split(/\s{2,}/)[0] || d).substring(0, 100) : ''; },
        getAdditionalInfo(d) { if (!d) return ''; const p = d.split(/\s{2,}/); return p.slice(1).filter(x => !x.toLowerCase().includes('lihat juga')).join(' ').substring(0, 100); },
        
        toggleFavorite(ddc) {
            const i = this.favorites.findIndex(f => f.code === ddc.code);
            if (i >= 0) this.favorites.splice(i, 1);
            else { this.favorites.push({ code: ddc.code, description: this.getMainDescription(ddc.description) }); if (this.favorites.length > 20) this.favorites = this.favorites.slice(-20); }
            localStorage.setItem('ddc_favorites', JSON.stringify(this.favorites));
        },
        
        isFavorite(code) { return this.favorites.some(f => f.code === code); },
        
        highlightSearch(text, term) {
            if (!text || !term || term.length < 2) return text;
            const escape = s => s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            const words = term.toLowerCase().trim().split(/\s+/).filter(w => w.length >= 2);
            if (!words.length) return text;
            
            let result = text;
            
            // Try full phrase first
            if (term.trim().length >= 3) {
                const pr = new RegExp(`(${escape(term.trim())})`, 'gi');
                if (pr.test(text)) return text.replace(pr, '<mark class="bg-yellow-300 px-0.5 rounded font-medium">$1</mark>');
            }
            
            // Try 2-word combinations
            for (let i = 0; i < words.length - 1; i++) {
                const twoWord = new RegExp(`(${escape(words[i])}\\s+${escape(words[i+1])})`, 'gi');
                result = result.replace(twoWord, '<mark class="bg-yellow-300 px-0.5 rounded font-medium">$1</mark>');
            }
            
            // Single words
            words.forEach(w => {
                if (!result.toLowerCase().includes(`>${w}<`)) {
                    result = result.replace(new RegExp(`(${escape(w)})`, 'gi'), '<mark class="bg-yellow-200 px-0.5 rounded">$1</mark>');
                }
            });
            
            return result;
        }
    }
}
</script>
