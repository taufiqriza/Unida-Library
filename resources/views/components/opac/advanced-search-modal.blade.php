<!-- Advanced Search Modal Component -->
<div id="advancedSearchModal" class="fixed inset-0 z-[9999] hidden opacity-0 transition-opacity duration-300">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-gradient-to-br from-slate-900/95 via-blue-900/90 to-indigo-900/95 backdrop-blur-md" onclick="closeAdvancedSearch()"></div>
    
    <!-- Modal Container -->
    <div class="absolute inset-0 flex items-center justify-center p-4 overflow-y-auto">
        <div id="modalContent" class="relative bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl w-full max-w-xl transform scale-95 transition-all duration-300 border border-white/20 my-8">
            <!-- Decorative Elements -->
            <div class="absolute -top-20 -right-20 w-40 h-40 bg-blue-500/20 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-20 -left-20 w-40 h-40 bg-indigo-500/20 rounded-full blur-3xl"></div>
            
            <!-- Header -->
            <div class="relative px-6 lg:px-8 pt-6 lg:pt-8 pb-4 lg:pb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl lg:text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                            {{ __('opac.advanced_search.title') }}
                        </h3>
                        <p class="text-xs lg:text-sm text-gray-500 mt-1">{{ __('opac.advanced_search.subtitle') }}</p>
                    </div>
                    <button onclick="closeAdvancedSearch()" class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-100 hover:bg-red-100 text-gray-400 hover:text-red-500 transition-all duration-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <!-- Body -->
            <form action="{{ route('opac.search') }}" method="GET" class="relative px-6 lg:px-8 pb-6 lg:pb-8" onsubmit="return handleAdvancedSearch(this)">
                <!-- Kata Kunci -->
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-keyboard text-blue-500 mr-2"></i>{{ __('opac.advanced_search.keyword') }}
                    </label>
                    <div class="relative">
                        <input type="text" name="q" id="advancedQuery" class="w-full px-5 py-3.5 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200 text-gray-800 placeholder-gray-400" placeholder="{{ __('opac.advanced_search.keyword_placeholder') }}">
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                </div>

                <!-- Jenis Koleksi (type) -->
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-layer-group text-blue-500 mr-2"></i>{{ __('opac.advanced_search.collection_type') }}
                    </label>
                    <div class="grid grid-cols-3 sm:grid-cols-6 gap-2">
                        @php
                            $types = [
                                ['value' => '', 'key' => 'all', 'icon' => 'fas fa-globe'],
                                ['value' => 'book', 'key' => 'book', 'icon' => 'fas fa-book'],
                                ['value' => 'ebook', 'key' => 'ebook', 'icon' => 'fas fa-file-pdf'],
                                ['value' => 'ethesis', 'key' => 'ethesis', 'icon' => 'fas fa-graduation-cap'],
                                ['value' => 'journal', 'key' => 'journal', 'icon' => 'fas fa-newspaper'],
                                ['value' => 'news', 'key' => 'news', 'icon' => 'fas fa-bullhorn'],
                            ];
                        @endphp
                        @foreach($types as $index => $type)
                        <label class="group relative cursor-pointer">
                            <input type="radio" name="type" value="{{ $type['value'] }}" {{ $index === 0 ? 'checked' : '' }} class="peer sr-only">
                            <div class="flex flex-col items-center justify-center gap-1 p-2.5 rounded-xl border-2 border-gray-200 bg-white peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-gray-300 transition-all duration-200">
                                <i class="{{ $type['icon'] }} text-gray-400 peer-checked:text-blue-600 group-hover:text-gray-500 text-base"></i>
                                <span class="text-[10px] lg:text-xs font-medium text-gray-500 peer-checked:text-blue-700">{{ __('opac.advanced_search.' . $type['key']) }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                
                <!-- Filter Tambahan -->
                <div class="mb-5 p-4 bg-gradient-to-br from-gray-50 to-blue-50/50 rounded-2xl border border-gray-100">
                    <p class="text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-filter text-blue-500 mr-2"></i>{{ __('opac.advanced_search.additional_filter') }}
                    </p>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <!-- Cabang (branch) -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('opac.advanced_search.branch') }}</label>
                            <select name="branch" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-xl text-sm focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                                <option value="">{{ __('opac.advanced_search.all_branches') }}</option>
                                @foreach(\App\Models\Branch::orderBy('name')->get() as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Bahasa (lang) -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('opac.advanced_search.language') }}</label>
                            <select name="lang" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-xl text-sm focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                                <option value="">{{ __('opac.advanced_search.all_languages') }}</option>
                                <option value="ind">Indonesia</option>
                                <option value="eng">English</option>
                                <option value="ara">العربية</option>
                            </select>
                        </div>
                        
                        <!-- Tahun Dari (from) -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('opac.advanced_search.year_from') }}</label>
                            <input type="number" name="from" placeholder="1990" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-xl text-sm focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        </div>
                        
                        <!-- Tahun Sampai (to) -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('opac.advanced_search.year_to') }}</label>
                            <input type="number" name="to" placeholder="{{ date('Y') }}" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-xl text-sm focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        </div>
                    </div>
                </div>
                
                <!-- Buttons -->
                <div class="flex gap-3">
                    <button type="button" onclick="closeAdvancedSearch()" class="flex-1 px-5 py-3 border-2 border-gray-200 text-gray-600 font-semibold rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>{{ __('opac.advanced_search.back') }}
                    </button>
                    <button type="submit" class="flex-1 px-5 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 transition-all duration-200">
                        <i class="fas fa-search mr-2"></i>{{ __('opac.advanced_search.search_now') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openAdvancedSearch() {
        const modal = document.getElementById('advancedSearchModal');
        const content = document.getElementById('modalContent');
        const btn = document.getElementById('advancedBtn');
        const searchInput = document.getElementById('searchInput');
        
        // Rotate icon if button exists
        if (btn) {
            btn.querySelector('i').classList.add('fa-spin');
        }
        
        setTimeout(() => {
            if (btn) {
                btn.querySelector('i').classList.remove('fa-spin');
            }
            if (searchInput) {
                document.getElementById('advancedQuery').value = searchInput.value;
            }
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            // Animate in
            requestAnimationFrame(() => {
                modal.classList.remove('opacity-0');
                content.classList.remove('scale-95');
                content.classList.add('scale-100');
            });
            
            // Focus input
            setTimeout(() => document.getElementById('advancedQuery').focus(), 300);
        }, btn ? 300 : 0);
    }
    
    function closeAdvancedSearch() {
        const modal = document.getElementById('advancedSearchModal');
        const content = document.getElementById('modalContent');
        
        modal.classList.add('opacity-0');
        content.classList.remove('scale-100');
        content.classList.add('scale-95');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }, 300);
    }
    
    function handleAdvancedSearch(form) {
        const query = form.querySelector('input[name="q"]').value.trim();
        if (window.SearchSplash) {
            closeAdvancedSearch();
            SearchSplash.show(query || 'Pencarian Lanjutan');
        }
        return true;
    }
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeAdvancedSearch();
    });
</script>
