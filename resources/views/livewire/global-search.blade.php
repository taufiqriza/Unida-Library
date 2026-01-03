<div class="min-h-screen bg-gray-50" x-data="{ showSubjectModal: false }">
    {{-- Background Pattern --}}
    <div class="fixed inset-0 opacity-[0.03] pointer-events-none z-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23000000\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    {{-- Search Header with Gradient --}}
    <div class="relative bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 overflow-hidden z-10">
        {{-- Decorative elements --}}
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full -ml-24 -mb-24"></div>
        
        <div class="relative max-w-7xl mx-auto px-4 py-8 lg:py-12 z-10">
            {{-- Title --}}
            <div class="text-center mb-6">
                <h1 class="text-2xl lg:text-4xl font-bold text-white mb-2">{{ __('opac.global_search.title') }}</h1>
                <p class="text-blue-200 text-sm lg:text-base">{{ __('opac.global_search.subtitle') }}</p>
            </div>

            {{-- Search Box - Full Rounded Design like Homepage --}}
            <div class="max-w-3xl mx-auto">
                <div class="relative group">
                    {{-- Glow effect --}}
                    <div class="absolute -inset-1 bg-gradient-to-r from-blue-400 via-white to-blue-400 rounded-full opacity-30 group-hover:opacity-50 blur-lg transition duration-500"></div>
                    
                    {{-- Search input container --}}
                    <div class="relative flex items-center bg-white rounded-full shadow-2xl shadow-primary-900/30 overflow-hidden">
                        <div class="pl-5 pr-2">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input 
                            type="text" 
                            wire:model.live.debounce.400ms="query"
                            id="searchInput"
                            placeholder="{{ __('opac.global_search.search_placeholder') }}" 
                            class="flex-1 px-2 py-4 lg:py-5 text-gray-700 text-sm lg:text-base focus:outline-none bg-transparent"
                            autofocus
                        >
                        @if($query)
                            <button wire:click="$set('query', '')" class="px-3 text-gray-400 hover:text-gray-600 transition">
                                <i class="fas fa-times-circle text-lg"></i>
                            </button>
                        @endif
                        {{-- Pill Switcher --}}
                        <div class="m-1.5 flex items-center bg-gray-100 rounded-full p-1 gap-1">
                            <button class="px-5 lg:px-6 py-2.5 lg:py-3 bg-gradient-to-r from-primary-600 to-indigo-600 text-white font-semibold rounded-full transition-all duration-300 shadow-lg shadow-primary-600/30 text-sm flex items-center gap-2">
                                <i class="fas fa-search"></i>
                                <span class="hidden sm:inline">{{ __('opac.global_search.search') }}</span>
                            </button>
                            <button type="button" id="advancedBtn" onclick="openAdvancedSearch()" class="w-10 h-10 lg:w-11 lg:h-11 flex items-center justify-center text-gray-500 hover:text-primary-600 hover:bg-white rounded-full transition-all duration-300">
                                <i class="fas fa-sliders-h"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Resource Type Tabs - Flex wrap for all languages, no scroll --}}
            @php 
                $tabs = [
                    'all' => ['icon' => 'fa-layer-group', 'label' => __('opac.global_search.tab_all')],
                    'book' => ['icon' => 'fa-book', 'label' => __('opac.global_search.tab_book')],
                    'ebook' => ['icon' => 'fa-file-pdf', 'label' => __('opac.global_search.tab_ebook')],
                    'ethesis' => ['icon' => 'fa-graduation-cap', 'label' => __('opac.global_search.tab_ethesis')],
                    'journal' => ['icon' => 'fa-file-lines', 'label' => __('opac.global_search.tab_journal')],
                    'external' => ['icon' => 'fa-globe', 'label' => __('opac.global_search.tab_external')],
                    'shamela' => ['icon' => 'fa-book-quran', 'label' => __('opac.global_search.tab_shamela')],
                ];
                
                $formatCompact = function($num) {
                    if ($num >= 1000000) return round($num / 1000000, 1) . 'M';
                    if ($num >= 1000) return round($num / 1000, 1) . 'K';
                    return number_format($num);
                };
            @endphp
            
            <div class="flex flex-wrap items-center justify-center gap-2 mt-8 px-4">
                @foreach($tabs as $key => $tab)
                    <button 
                        wire:click="setResourceType('{{ $key }}')"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-full text-sm font-medium transition-all
                            {{ $resourceType === $key 
                                ? 'bg-white text-primary-700 shadow-lg scale-105' 
                                : 'bg-white/10 text-white hover:bg-white/20 backdrop-blur-sm' }}"
                    >
                        <i class="fas {{ $tab['icon'] }}"></i>
                        <span>{{ $tab['label'] }}</span>
                        <span class="px-2 py-0.5 text-xs rounded-full {{ $resourceType === $key ? 'bg-primary-100 text-primary-700' : 'bg-white/20' }}">
                            {{ $formatCompact($counts[$key] ?? 0) }}
                        </span>
                    </button>
                @endforeach
            </div>
        </div>
        
        {{-- E-Resources Notice - Absolute at bottom of header, no height change --}}
        <div x-data="{ showNotice: true }" x-show="showNotice" x-cloak
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute bottom-2 left-0 right-0 flex justify-center z-20 pointer-events-none">
            <div class="pointer-events-auto flex items-center gap-1.5 px-3 py-1.5 bg-white/15 backdrop-blur-md rounded-full border border-white/25 shadow-lg text-xs">
                <i class="fas fa-lightbulb text-amber-300"></i>
                <span class="text-white/90">
                    <span class="font-semibold text-white">{{ __('opac.global_search.e_resources_notice') }}</span>
                    <span class="hidden sm:inline"> — Shamela, Waqfeya, Open Library</span>
                    <a href="{{ route('opac.page', 'journal-subscription') }}" class="ml-1 text-amber-300 hover:text-amber-200 font-medium">{{ __('opac.global_search.view_more') }} →</a>
                </span>
                <button @click="showNotice = false" 
                        class="ml-1 w-5 h-5 flex items-center justify-center bg-white/20 hover:bg-red-500 text-white/80 hover:text-white rounded-full transition-all"
                        title="{{ __('opac.global_search.close') }}">
                    <i class="fas fa-times text-[10px]"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="relative z-10 max-w-7xl mx-auto px-4 py-6 lg:py-8">
        <div class="flex flex-col lg:flex-row gap-6 lg:gap-8">
            
            {{-- Sidebar Filters --}}
            <aside class="w-full lg:w-80 flex-shrink-0" x-data="{ collapsed: false }">
                {{-- Mobile Filter Toggle --}}
                <button 
                    wire:click="$toggle('showMobileFilters')"
                    class="lg:hidden w-full flex items-center justify-between p-4 bg-white rounded-2xl shadow-sm border border-gray-100 mb-4"
                >
                    <span class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-sliders-h text-primary-500"></i> 
                        {{ __('opac.global_search.filter_search') }}
                        @if($this->activeFiltersCount > 0)
                            <span class="px-2 py-0.5 text-xs bg-primary-100 text-primary-700 rounded-full">{{ $this->activeFiltersCount }}</span>
                        @endif
                    </span>
                    <i class="fas fa-chevron-{{ $showMobileFilters ? 'up' : 'down' }} text-gray-400"></i>
                </button>

                <div class="{{ $showMobileFilters ? 'block' : 'hidden lg:block' }} space-y-4">
                    {{-- Filter Card --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        {{-- Filter Header with Collapse Toggle --}}
                        <button @click="collapsed = !collapsed" class="w-full p-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100 flex items-center justify-between hover:bg-gray-50 transition">
                            <h3 class="font-bold text-gray-900 flex items-center gap-2">
                                <div class="w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-filter text-primary-600 text-sm"></i>
                                </div>
                                {{ __('opac.global_search.filters') }}
                                @if($this->activeFiltersCount > 0)
                                    <span class="px-2 py-0.5 text-xs bg-primary-100 text-primary-700 rounded-full">{{ $this->activeFiltersCount }}</span>
                                @endif
                            </h3>
                            <div class="flex items-center gap-2">
                                @if($this->activeFiltersCount > 0)
                                    <button wire:click.stop="clearAllFilters" class="text-xs text-red-500 hover:text-red-600 font-medium flex items-center gap-1 px-2 py-1 rounded hover:bg-red-50">
                                        <i class="fas fa-times"></i> {{ __('opac.global_search.reset') }}
                                    </button>
                                @endif
                                <i class="fas fa-chevron-down text-gray-400 transition-transform" :class="collapsed && 'rotate-180'"></i>
                            </div>
                        </button>

                        <div x-show="!collapsed" x-collapse class="p-4 space-y-5">
                            {{-- Branch/Location Filter (Dropdown) --}}
                            <div class="filter-section">
                                <label class="flex items-center gap-2 text-sm font-semibold text-gray-800 mb-3">
                                    <span class="w-6 h-6 bg-red-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-map-marker-alt text-red-500 text-xs"></i>
                                    </span>
                                    {{ __('opac.global_search.library_location') }}
                                </label>
                                <select wire:model.live="branchId" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white transition">
                                    <option value="">{{ __('opac.global_search.all_locations') }}</option>
                                    @foreach($this->branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <hr class="border-gray-100">

                            {{-- Collection Type (for Books) --}}
                            @if($resourceType === 'all' || $resourceType === 'book')
                            <div class="filter-section">
                                <label class="flex items-center gap-2 text-sm font-semibold text-gray-800 mb-3">
                                    <span class="w-6 h-6 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-layer-group text-blue-500 text-xs"></i>
                                    </span>
                                    {{ __('opac.global_search.collection_type') }}
                                </label>
                                <select wire:model.live="collectionTypeId" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white transition">
                                    <option value="">{{ __('opac.global_search.all_types') }}</option>
                                    @foreach($this->collectionTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            {{-- Faculty & Department (for E-Thesis) --}}
                            @if($resourceType === 'all' || $resourceType === 'ethesis')
                            <div class="filter-section">
                                <label class="flex items-center gap-2 text-sm font-semibold text-gray-800 mb-3">
                                    <span class="w-6 h-6 bg-purple-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-university text-purple-500 text-xs"></i>
                                    </span>
                                    {{ __('opac.global_search.faculty_dept') }}
                                </label>
                                <div class="space-y-2">
                                    <select wire:model.live="facultyId" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white transition">
                                        <option value="">{{ __('opac.global_search.all_faculties') }}</option>
                                        @foreach($this->faculties as $faculty)
                                            <option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                                        @endforeach
                                    </select>
                                    @if($facultyId)
                                    <select wire:model.live="departmentId" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white transition">
                                        <option value="">{{ __('opac.global_search.all_departments') }}</option>
                                        @foreach($this->departments as $dept)
                                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                        @endforeach
                                    </select>
                                    @endif
                                </div>
                            </div>

                            {{-- Thesis Type --}}
                            <div class="filter-section">
                                <label class="flex items-center gap-2 text-sm font-semibold text-gray-800 mb-3">
                                    <span class="w-6 h-6 bg-pink-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-scroll text-pink-500 text-xs"></i>
                                    </span>
                                    {{ __('opac.global_search.thesis_type') }}
                                </label>
                                <div class="flex flex-wrap gap-2">
                                    @foreach(['skripsi' => __('opac.global_search.thesis_skripsi'), 'tesis' => __('opac.global_search.thesis_tesis'), 'disertasi' => __('opac.global_search.thesis_disertasi')] as $key => $label)
                                        <button 
                                            wire:click="$set('thesisType', '{{ $thesisType === $key ? '' : $key }}')"
                                            class="px-3 py-1.5 text-xs font-medium rounded-full transition
                                                {{ $thesisType === $key 
                                                    ? 'bg-purple-600 text-white' 
                                                    : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                                        >
                                            {{ $label }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            {{-- Journal Source Filter --}}
                            @if($resourceType === 'all' || $resourceType === 'journal')
                            <div class="filter-section">
                                <label class="flex items-center gap-2 text-sm font-semibold text-gray-800 mb-3">
                                    <span class="w-6 h-6 bg-indigo-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-file-lines text-indigo-500 text-xs"></i>
                                    </span>
                                    {{ __('opac.global_search.journal_source') }}
                                </label>
                                <select wire:model.live="journalCode" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white transition">
                                    <option value="">{{ __('opac.global_search.all_journals') }}</option>
                                    @foreach($this->journalSources as $source)
                                        <option value="{{ $source->code }}">{{ $source->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            {{-- E-Book Source Filter --}}
                            @if($resourceType === 'ebook')
                            <div class="filter-section">
                                <label class="flex items-center gap-2 text-sm font-semibold text-gray-800 mb-3">
                                    <span class="w-6 h-6 bg-emerald-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-book-reader text-emerald-500 text-xs"></i>
                                    </span>
                                    {{ __('opac.global_search.ebook_source') }}
                                </label>
                                <div class="flex flex-wrap gap-2">
                                    <button 
                                        wire:click="$set('ebookSource', null)"
                                        class="px-3 py-1.5 text-xs font-medium rounded-full transition
                                            {{ !$ebookSource ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                                    >
                                        {{ __('opac.global_search.all_sources') }}
                                    </button>
                                    <button 
                                        wire:click="$set('ebookSource', 'local')"
                                        class="px-3 py-1.5 text-xs font-medium rounded-full transition flex items-center gap-1
                                            {{ $ebookSource === 'local' ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                                    >
                                        <i class="fas fa-database text-[10px]"></i> Local
                                    </button>
                                    <button 
                                        wire:click="$set('ebookSource', 'kubuku')"
                                        class="px-3 py-1.5 text-xs font-medium rounded-full transition flex items-center gap-1
                                            {{ $ebookSource === 'kubuku' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                                    >
                                        <i class="fas fa-cloud text-[10px]"></i> KUBUKU
                                    </button>
                                </div>
                            </div>
                            @endif

                            {{-- Subject Filter --}}
                            @if($resourceType === 'all' || $resourceType === 'book')
                            <div class="filter-section">
                                <label class="flex items-center gap-2 text-sm font-semibold text-gray-800 mb-3">
                                    <span class="w-6 h-6 bg-amber-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-tags text-amber-500 text-xs"></i>
                                    </span>
                                    {{ __('opac.global_search.subject') }}
                                </label>
                                
                                {{-- Selected Subjects --}}
                                @if(!empty($selectedSubjects))
                                <div class="flex flex-wrap gap-1.5 mb-3">
                                    @foreach($this->subjects->whereIn('id', $selectedSubjects) as $subject)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-100 text-amber-800 text-xs font-medium rounded-full">
                                            {{ \Str::limit($subject->name, 20) }}
                                            <button wire:click="removeSubject({{ $subject->id }})" class="hover:text-amber-900">
                                                <i class="fas fa-times text-[10px]"></i>
                                            </button>
                                        </span>
                                    @endforeach
                                </div>
                                @endif

                                {{-- Popular Subjects --}}
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($this->popularSubjects->take(8) as $subject)
                                        @if(!in_array($subject->id, $selectedSubjects))
                                        <button 
                                            wire:click="toggleSubject({{ $subject->id }})"
                                            class="px-2.5 py-1 text-xs bg-gray-100 text-gray-600 rounded-full hover:bg-amber-100 hover:text-amber-700 transition"
                                        >
                                            {{ \Str::limit($subject->name, 15) }}
                                        </button>
                                        @endif
                                    @endforeach
                                </div>
                                
                                <button 
                                    @click="showSubjectModal = true"
                                    class="mt-2 text-xs text-primary-600 hover:text-primary-700 font-medium"
                                >
                                    <i class="fas fa-plus mr-1"></i> {{ __('opac.global_search.view_all_subjects') }}
                                </button>
                            </div>
                            @endif

                            <hr class="border-gray-100">

                            {{-- Language Filter --}}
                            <div class="filter-section">
                                <label class="flex items-center gap-2 text-sm font-semibold text-gray-800 mb-3">
                                    <span class="w-6 h-6 bg-cyan-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-globe text-cyan-500 text-xs"></i>
                                    </span>
                                    {{ __('opac.global_search.language') }}
                                </label>
                                <div class="flex flex-wrap gap-2">
                                    <button 
                                        wire:click="$set('language', null)"
                                        class="px-3 py-1.5 text-xs font-medium rounded-full transition
                                            {{ !$language ? 'bg-cyan-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                                    >
                                        {{ __('opac.global_search.all_languages') }}
                                    </button>
                                    @foreach($this->languages as $code => $name)
                                        <button 
                                            wire:click="$set('language', '{{ $language === $code ? '' : $code }}')"
                                            class="px-3 py-1.5 text-xs font-medium rounded-full transition
                                                {{ $language === $code 
                                                    ? 'bg-cyan-600 text-white' 
                                                    : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                                        >
                                            {{ $name }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Year Range --}}
                            <div class="filter-section">
                                <label class="flex items-center gap-2 text-sm font-semibold text-gray-800 mb-3">
                                    <span class="w-6 h-6 bg-green-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-calendar text-green-500 text-xs"></i>
                                    </span>
                                    {{ __('opac.global_search.year_published') }}
                                </label>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <input 
                                            type="number" 
                                            wire:model.live.debounce.500ms="yearFrom" 
                                            placeholder="{{ __('opac.global_search.year_from') }}" 
                                            min="1900" 
                                            max="{{ date('Y') }}" 
                                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                        >
                                    </div>
                                    <div>
                                        <input 
                                            type="number" 
                                            wire:model.live.debounce.500ms="yearTo" 
                                            placeholder="{{ __('opac.global_search.year_to') }}" 
                                            min="1900" 
                                            max="{{ date('Y') }}" 
                                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                        >
                                    </div>
                                </div>
                                {{-- Quick Year Buttons --}}
                                <div class="flex flex-wrap gap-1.5 mt-2">
                                    @foreach([date('Y'), date('Y')-1, date('Y')-2, date('Y')-5] as $year)
                                        <button 
                                            wire:click="$set('yearFrom', {{ $year }})"
                                            class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded-lg hover:bg-green-100 hover:text-green-700 transition"
                                        >
                                            {{ $year == date('Y') ? __('opac.global_search.this_year') : ($year == date('Y')-1 ? __('opac.global_search.last_year') : $year) }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Statistics Card --}}
                    <div class="bg-gradient-to-br from-primary-600 via-primary-700 to-indigo-700 rounded-2xl p-5 text-white shadow-lg">
                        <h4 class="font-bold mb-4 flex items-center gap-2">
                            <i class="fas fa-chart-pie"></i> {{ __('opac.global_search.collection_stats') }}
                        </h4>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-2 bg-white/10 rounded-lg">
                                <span class="flex items-center gap-2 text-sm text-primary-100">
                                    <i class="fas fa-book w-4"></i> {{ __('opac.global_search.printed_books') }}
                                </span>
                                <span class="font-bold">{{ number_format($counts['book']) }}</span>
                            </div>
                            <div class="flex items-center justify-between p-2 bg-white/10 rounded-lg">
                                <span class="flex items-center gap-2 text-sm text-primary-100">
                                    <i class="fas fa-file-pdf w-4"></i> E-Book
                                </span>
                                <span class="font-bold">{{ number_format($counts['ebook']) }}</span>
                            </div>
                            <div class="flex items-center justify-between p-2 bg-white/10 rounded-lg">
                                <span class="flex items-center gap-2 text-sm text-primary-100">
                                    <i class="fas fa-graduation-cap w-4"></i> E-Thesis
                                </span>
                                <span class="font-bold">{{ number_format($counts['ethesis']) }}</span>
                            </div>
                            <div class="flex items-center justify-between p-2 bg-white/10 rounded-lg">
                                <span class="flex items-center gap-2 text-sm text-primary-100">
                                    <i class="fas fa-file-lines w-4"></i> Jurnal
                                </span>
                                <span class="font-bold">{{ number_format($counts['journal']) }}</span>
                            </div>
                            @if(($counts['external'] ?? 0) > 0)
                            <div class="flex items-center justify-between p-2 bg-white/10 rounded-lg">
                                <span class="flex items-center gap-2 text-sm text-primary-100">
                                    <i class="fas fa-globe w-4"></i> Open Library
                                </span>
                                <span class="font-bold">{{ number_format($counts['external']) }}+</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Help Card --}}
                    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4">
                        <h4 class="font-semibold text-amber-800 mb-2 flex items-center gap-2">
                            <i class="fas fa-lightbulb text-amber-500"></i> {{ __('opac.global_search.search_tips') }}
                        </h4>
                        <ul class="text-xs text-amber-700 space-y-1.5">
                            <li class="flex items-start gap-2">
                                <i class="fas fa-check text-amber-500 mt-0.5"></i>
                                <span>{{ __('opac.global_search.tip_1') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="fas fa-check text-amber-500 mt-0.5"></i>
                                <span>{{ __('opac.global_search.tip_2') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="fas fa-check text-amber-500 mt-0.5"></i>
                                <span>{{ __('opac.global_search.tip_3') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </aside>

            {{-- Results Section --}}
            <main class="flex-1 min-w-0">
                {{-- Results Header --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                    <div>
                        @if($query)
                            <h2 class="text-lg font-bold text-gray-900">
                                {{ __('opac.global_search.results_for') }} "<span class="text-primary-600">{{ $query }}</span>"
                            </h2>
                        @else
                            <h2 class="text-lg font-bold text-gray-900">{{ __('opac.global_search.all_collection') }}</h2>
                        @endif
                        <p class="text-sm text-gray-500 mt-0.5">
                            {{ number_format($this->totalResults) }} {{ __('opac.global_search.items_found') }}
                            @if($this->activeFiltersCount > 0)
                                <span class="text-primary-600">({{ $this->activeFiltersCount }} {{ __('opac.global_search.active_filters') }})</span>
                            @endif
                        </p>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        {{-- Sort --}}
                        <select wire:model.live="sortBy" class="px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 bg-white">
                            <option value="relevance">{{ __('opac.global_search.sort_relevance') }}</option>
                            <option value="newest">{{ __('opac.global_search.sort_newest') }}</option>
                            <option value="oldest">{{ __('opac.global_search.sort_oldest') }}</option>
                            <option value="title_asc">{{ __('opac.global_search.sort_title_asc') }}</option>
                            <option value="title_desc">{{ __('opac.global_search.sort_title_desc') }}</option>
                        </select>
                        
                        {{-- View Toggle --}}
                        <div class="hidden lg:flex items-center bg-gray-100 rounded-xl p-1">
                            <button 
                                wire:click="$set('viewMode', 'grid')"
                                class="p-2 rounded-lg transition {{ $viewMode === 'grid' ? 'bg-white shadow-sm text-primary-600' : 'text-gray-400 hover:text-gray-600' }}"
                            >
                                <i class="fas fa-th-large"></i>
                            </button>
                            <button 
                                wire:click="$set('viewMode', 'list')"
                                class="p-2 rounded-lg transition {{ $viewMode === 'list' ? 'bg-white shadow-sm text-primary-600' : 'text-gray-400 hover:text-gray-600' }}"
                            >
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Active Filters Pills --}}
                @if($this->activeFiltersCount > 0)
                <div class="flex flex-wrap items-center gap-2 mb-4 p-3 bg-gray-50 rounded-xl">
                    <span class="text-xs text-gray-500 font-medium">{{ __('opac.global_search.active_filters_label') }}</span>
                    
                    @if($branchId)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-100 text-red-700 text-xs font-medium rounded-full">
                            <i class="fas fa-map-marker-alt"></i>
                            {{ $this->branches->find($branchId)?->name }}
                            <button wire:click="$set('branchId', null)" class="hover:text-red-900"><i class="fas fa-times"></i></button>
                        </span>
                    @endif
                    
                    @if($collectionTypeId)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full">
                            <i class="fas fa-layer-group"></i>
                            {{ $this->collectionTypes->find($collectionTypeId)?->name }}
                            <button wire:click="$set('collectionTypeId', null)" class="hover:text-blue-900"><i class="fas fa-times"></i></button>
                        </span>
                    @endif
                    
                    @if($facultyId)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-purple-100 text-purple-700 text-xs font-medium rounded-full">
                            <i class="fas fa-university"></i>
                            {{ $this->faculties->find($facultyId)?->name }}
                            <button wire:click="$set('facultyId', null)" class="hover:text-purple-900"><i class="fas fa-times"></i></button>
                        </span>
                    @endif
                    
                    @if($language)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-cyan-100 text-cyan-700 text-xs font-medium rounded-full">
                            <i class="fas fa-globe"></i>
                            {{ $this->languages[$language] ?? $language }}
                            <button wire:click="$set('language', null)" class="hover:text-cyan-900"><i class="fas fa-times"></i></button>
                        </span>
                    @endif
                    
                    @if($yearFrom || $yearTo)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">
                            <i class="fas fa-calendar"></i>
                            {{ $yearFrom ?? '...' }} - {{ $yearTo ?? date('Y') }}
                            <button wire:click="$set('yearFrom', null); $set('yearTo', null)" class="hover:text-green-900"><i class="fas fa-times"></i></button>
                        </span>
                    @endif

                    @if($journalCode)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-indigo-100 text-indigo-700 text-xs font-medium rounded-full">
                            <i class="fas fa-file-lines"></i>
                            {{ $this->journalSources->firstWhere('code', $journalCode)?->name }}
                            <button wire:click="$set('journalCode', null)" class="hover:text-indigo-900"><i class="fas fa-times"></i></button>
                        </span>
                    @endif
                    
                    <button wire:click="clearAllFilters" class="text-xs text-gray-500 hover:text-red-600 font-medium ml-auto">
                        {{ __('opac.global_search.clear_all') }}
                    </button>
                </div>
                @endif

                {{-- Loading State --}}
                <div wire:loading.flex wire:target="query, resourceType, branchId, collectionTypeId, facultyId, departmentId, language, yearFrom, yearTo, thesisType, sortBy, journalCode" class="items-center justify-center py-16">
                    <div class="text-center">
                        <div class="w-12 h-12 border-3 border-primary-600 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
                        <p class="text-sm text-gray-500">{{ __('opac.global_search.searching') }}</p>
                    </div>
                </div>

                {{-- Results --}}
                <div wire:loading.remove wire:target="query, resourceType, branchId, collectionTypeId, facultyId, departmentId, language, yearFrom, yearTo, thesisType, sortBy, journalCode">
                    @if(count($results) > 0)
                        @if($resourceType === 'shamela')
                        {{-- Shamela List View - Special RTL Layout --}}
                        <div class="space-y-3">
                            @foreach($results as $item)
                                <a href="{{ $item['url'] }}" class="flex gap-4 bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-lg hover:border-blue-200 transition group">
                                    {{-- Cover --}}
                                    <div class="flex-shrink-0 w-20 h-28 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg overflow-hidden">
                                        @if($item['cover'])
                                            <img src="{{ $item['cover'] }}" alt="{{ $item['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-amber-600 via-orange-500 to-red-600 flex flex-col items-center justify-center p-2 text-center">
                                                <i class="fas fa-book-open text-lg text-white/70 mb-1"></i>
                                                <p class="text-white font-bold text-[9px] leading-tight line-clamp-3">{{ Str::limit($item['title'], 35) }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    {{-- Info --}}
                                    <div class="flex-1 min-w-0" dir="rtl">
                                        <h3 class="font-bold text-gray-900 text-base line-clamp-2 mb-1 group-hover:text-blue-600 transition">
                                            {{ $item['title'] }}
                                        </h3>
                                        <p class="text-sm text-gray-600 truncate mb-2">
                                            <i class="fas fa-user-edit text-blue-400 ml-1"></i>
                                            {{ $item['author'] ?? 'المؤلف غير معروف' }}
                                        </p>
                                        
                                        <div class="flex flex-wrap items-center gap-2 text-xs">
                                            @if($item['meta']['category'] ?? null)
                                            <span class="px-2 py-1 bg-indigo-50 text-indigo-600 rounded-full">
                                                <i class="fas fa-folder-open ml-1"></i>{{ $item['meta']['category'] }}
                                            </span>
                                            @endif
                                            @if($item['year'])
                                            <span class="px-2 py-1 bg-blue-50 text-blue-600 rounded-full">
                                                <i class="fas fa-calendar-alt ml-1"></i>{{ $item['year'] }}
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    {{-- Arrow --}}
                                    <div class="flex-shrink-0 flex items-center text-gray-300 group-hover:text-blue-500 transition">
                                        <i class="fas fa-chevron-left text-lg"></i>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        @elseif($viewMode === 'grid')
                        {{-- Grid View - Modern Card Design --}}
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 lg:gap-4">
                            @foreach($results as $item)
                                <a href="{{ $item['url'] }}" class="group">
                                    {{-- Card Container --}}
                                    <div class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-xl border border-gray-100/50 transition-all duration-300 hover:-translate-y-1">
                                        {{-- Cover Section --}}
                                        <div class="aspect-[2/3] bg-gradient-to-br from-slate-100 to-slate-50 relative overflow-hidden">
                                            @if($item['cover'])
                                                <img src="{{ $item['cover'] }}" alt="{{ $item['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                            @else
                                                <div class="w-full h-full bg-gradient-to-br from-amber-600 via-orange-500 to-red-600 flex flex-col items-center justify-center p-3 text-center group-hover:scale-105 transition-transform duration-500">
                                                    <i class="fas fa-book-open text-xl text-white/70 mb-2"></i>
                                                    <p class="text-white font-bold text-xs leading-tight line-clamp-4">{{ Str::limit($item['title'], 50) }}</p>
                                                </div>
                                            @endif
                                            
                                            {{-- Year Badge - Top Right --}}
                                            @if($item['year'])
                                            <div class="absolute top-2 right-2">
                                                <span class="px-2 py-0.5 text-[10px] font-bold bg-black/70 text-white rounded backdrop-blur-sm">
                                                    {{ $item['year'] }}
                                                </span>
                                            </div>
                                            @endif
                                            
                                            {{-- Type Icon - Top Left --}}
                                            <div class="absolute top-2 left-2">
                                                <span class="w-7 h-7 flex items-center justify-center rounded-lg shadow-lg
                                                    @if($item['badgeColor'] === 'blue') bg-blue-500 text-white
                                                    @elseif($item['badgeColor'] === 'orange') bg-orange-500 text-white
                                                    @elseif($item['badgeColor'] === 'purple') bg-purple-500 text-white
                                                    @elseif($item['badgeColor'] === 'indigo') bg-indigo-500 text-white
                                                    @elseif($item['badgeColor'] === 'green') bg-emerald-500 text-white
                                                    @elseif($item['badgeColor'] === 'emerald') bg-emerald-600 text-white
                                                    @elseif($item['badgeColor'] === 'cyan') bg-cyan-500 text-white
                                                    @else bg-slate-500 text-white
                                                    @endif
                                                ">
                                                    <i class="fas {{ $item['icon'] }} text-xs"></i>
                                                </span>
                                            </div>

                                            {{-- Hover Overlay --}}
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-3">
                                                <span class="text-white text-[10px] font-medium flex items-center gap-1">
                                                    <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i> {{ __('opac.global_search.view_detail') }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        {{-- Info Section --}}
                                        <div class="p-2.5">
                                            <h3 class="font-semibold text-gray-900 text-xs line-clamp-2 group-hover:text-primary-600 transition-colors leading-snug min-h-[2.5rem]">
                                                {!! $item['title_highlighted'] ?? e($item['title']) !!}
                                            </h3>
                                            <p class="text-[11px] text-gray-500 mt-1 line-clamp-1 flex items-center gap-1">
                                                <i class="fas fa-pen-nib text-gray-400 text-[9px]"></i>
                                                <span class="truncate">{!! $item['author_highlighted'] ?? e($item['author']) !!}</span>
                                            </p>
                                            @if(isset($item['meta']['branch']) && $item['meta']['branch'])
                                            <p class="text-[10px] text-emerald-600 mt-1 flex items-center gap-1">
                                                <i class="fas fa-location-dot text-[8px]"></i>
                                                <span class="truncate">{{ Str::limit($item['meta']['branch'], 20) }}</span>
                                            </p>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        @else
                        {{-- List View - Modern Design --}}
                        <div class="space-y-3">
                            @foreach($results as $item)
                                <a href="{{ $item['url'] }}" class="group flex gap-4 bg-white rounded-xl p-3 shadow-sm hover:shadow-lg border border-gray-100/50 hover:border-primary-200 transition-all duration-300">
                                    {{-- Cover --}}
                                    <div class="w-16 sm:w-20 aspect-[2/3] flex-shrink-0 bg-gradient-to-br from-slate-100 to-slate-50 rounded-lg overflow-hidden relative">
                                        @if($item['cover'])
                                            <img src="{{ $item['cover'] }}" alt="{{ $item['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-amber-600 via-orange-500 to-red-600 flex flex-col items-center justify-center p-1.5 text-center group-hover:scale-105 transition-transform duration-500">
                                                <i class="fas fa-book-open text-sm text-white/70 mb-0.5"></i>
                                                <p class="text-white font-bold text-[8px] leading-tight line-clamp-3">{{ Str::limit($item['title'], 30) }}</p>
                                            </div>
                                        @endif
                                        {{-- Type Icon Overlay --}}
                                        <div class="absolute top-1 left-1">
                                            <span class="w-5 h-5 flex items-center justify-center rounded shadow
                                                @if($item['badgeColor'] === 'blue') bg-blue-500 text-white
                                                @elseif($item['badgeColor'] === 'orange') bg-orange-500 text-white
                                                @elseif($item['badgeColor'] === 'purple') bg-purple-500 text-white
                                                @elseif($item['badgeColor'] === 'indigo') bg-indigo-500 text-white
                                                @elseif($item['badgeColor'] === 'green') bg-emerald-500 text-white
                                                @elseif($item['badgeColor'] === 'emerald') bg-emerald-600 text-white
                                                @elseif($item['badgeColor'] === 'cyan') bg-cyan-500 text-white
                                                @else bg-slate-500 text-white
                                                @endif
                                            ">
                                                <i class="fas {{ $item['icon'] }} text-[8px]"></i>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    {{-- Info --}}
                                    <div class="flex-1 min-w-0 py-0.5">
                                        <div class="flex items-start justify-between gap-2">
                                            <h3 class="font-semibold text-gray-900 text-sm group-hover:text-primary-600 transition-colors line-clamp-2 leading-snug">
                                                {!! $item['title_highlighted'] ?? e($item['title']) !!}
                                            </h3>
                                            @if($item['year'])
                                            <span class="flex-shrink-0 px-2 py-0.5 text-[10px] font-bold bg-slate-100 text-slate-600 rounded">
                                                {{ $item['year'] }}
                                            </span>
                                            @endif
                                        </div>
                                        
                                        <p class="text-xs text-gray-600 mt-1.5 flex items-center gap-1.5">
                                            <i class="fas fa-pen-nib text-gray-400 text-[10px]"></i>
                                            <span class="line-clamp-1">{!! $item['author_highlighted'] ?? e($item['author']) !!}</span>
                                        </p>
                                        
                                        @if($item['description'])
                                            <p class="text-[11px] text-gray-500 mt-1.5 line-clamp-2 hidden sm:block">{!! $item['description'] !!}</p>
                                        @endif
                                        
                                        <div class="flex items-center flex-wrap gap-x-3 gap-y-1 mt-2">
                                            @if(isset($item['meta']['branch']) && $item['meta']['branch'])
                                                <span class="text-[11px] text-emerald-600 flex items-center gap-1">
                                                    <i class="fas fa-location-dot text-[9px]"></i>{{ $item['meta']['branch'] }}
                                                </span>
                                            @endif
                                            @if(isset($item['meta']['department']))
                                                <span class="text-[11px] text-gray-500 flex items-center gap-1">
                                                    <i class="fas fa-building-columns text-[9px]"></i>{{ Str::limit($item['meta']['department'], 25) }}
                                                </span>
                                            @endif
                                            @if(isset($item['meta']['isbn']) && $item['meta']['isbn'])
                                                <span class="text-[11px] text-gray-400 flex items-center gap-1 font-mono">
                                                    <i class="fas fa-barcode text-[9px]"></i>{{ $item['meta']['isbn'] }}
                                                </span>
                                            @endif
                                            @if(isset($item['meta']['journal']))
                                                <span class="text-[11px] text-purple-600 flex items-center gap-1">
                                                    <i class="fas fa-book-open text-[9px]"></i>{{ Str::limit($item['meta']['journal'], 20) }}
                                                </span>
                                            @endif
                                            <span class="text-[11px] px-1.5 py-0.5 rounded
                                                @if($item['badgeColor'] === 'blue') bg-blue-50 text-blue-600
                                                @elseif($item['badgeColor'] === 'orange') bg-orange-50 text-orange-600
                                                @elseif($item['badgeColor'] === 'purple') bg-purple-50 text-purple-600
                                                @elseif($item['badgeColor'] === 'indigo') bg-indigo-50 text-indigo-600
                                                @elseif($item['badgeColor'] === 'green') bg-emerald-50 text-emerald-600
                                                @elseif($item['badgeColor'] === 'cyan') bg-cyan-50 text-cyan-600
                                                @else bg-slate-50 text-slate-600
                                                @endif
                                            ">
                                                {{ $item['badge'] }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    {{-- Arrow --}}
                                    <div class="hidden sm:flex items-center text-gray-300 group-hover:text-primary-500 transition-colors">
                                        <i class="fas fa-chevron-right"></i>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        @endif
                        
                        {{-- Pagination --}}
                        @if($this->totalPages > 1)
                        <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-100">
                            <div class="text-sm text-gray-500">
                                {{ __('opac.global_search.page_of', ['page' => $page, 'total' => $this->totalPages]) }}
                            </div>
                            <div class="flex items-center gap-2">
                                <button 
                                    wire:click="previousPage"
                                    {{ $page <= 1 ? 'disabled' : '' }}
                                    class="px-4 py-2 text-sm font-medium rounded-xl transition flex items-center gap-2
                                        {{ $page <= 1 
                                            ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
                                            : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50 hover:border-primary-300' }}"
                                >
                                    <i class="fas fa-chevron-left text-xs"></i>
                                    <span class="hidden sm:inline">{{ __('opac.global_search.previous') }}</span>
                                </button>
                                
                                {{-- Page Numbers --}}
                                <div class="hidden sm:flex items-center gap-1">
                                    @php
                                        $start = max(1, $page - 2);
                                        $end = min($this->totalPages, $page + 2);
                                    @endphp
                                    @if($start > 1)
                                        <button wire:click="gotoPage(1)" class="w-10 h-10 text-sm rounded-xl hover:bg-gray-100">1</button>
                                        @if($start > 2)<span class="px-1 text-gray-400">...</span>@endif
                                    @endif
                                    @for($i = $start; $i <= $end; $i++)
                                        <button 
                                            wire:click="gotoPage({{ $i }})"
                                            class="w-10 h-10 text-sm rounded-xl transition
                                                {{ $i === $page 
                                                    ? 'bg-primary-600 text-white' 
                                                    : 'hover:bg-gray-100' }}"
                                        >{{ $i }}</button>
                                    @endfor
                                    @if($end < $this->totalPages)
                                        @if($end < $this->totalPages - 1)<span class="px-1 text-gray-400">...</span>@endif
                                        <button wire:click="gotoPage({{ $this->totalPages }})" class="w-10 h-10 text-sm rounded-xl hover:bg-gray-100">{{ $this->totalPages }}</button>
                                    @endif
                                </div>
                                
                                <button 
                                    wire:click="nextPage"
                                    {{ $page >= $this->totalPages ? 'disabled' : '' }}
                                    class="px-4 py-2 text-sm font-medium rounded-xl transition flex items-center gap-2
                                        {{ $page >= $this->totalPages 
                                            ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
                                            : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50 hover:border-primary-300' }}"
                                >
                                    <span class="hidden sm:inline">{{ __('opac.global_search.next') }}</span>
                                    <i class="fas fa-chevron-right text-xs"></i>
                                </button>
                            </div>
                        </div>
                        @endif
                    @else

                        {{-- Empty State --}}
                        <div class="text-center py-16">
                            @if($resourceType === 'external' && !$query)
                                {{-- Special message for External/Open Library --}}
                                <div class="w-24 h-24 bg-gradient-to-br from-cyan-100 to-cyan-50 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <i class="fas fa-globe text-4xl text-cyan-500"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('opac.global_search.search_open_library') }}</h3>
                                <p class="text-gray-500 max-w-md mx-auto mb-6">
                                    {{ __('opac.global_search.open_library_desc') }} <span class="font-semibold text-cyan-600">{{ __('opac.global_search.open_library_count') }}</span> 
                                    {{ __('opac.global_search.open_library_source') }}
                                </p>
                                
                                {{-- Example Keywords --}}
                                <div class="mt-6 p-6 bg-gradient-to-br from-cyan-50 to-blue-50 rounded-2xl max-w-lg mx-auto border border-cyan-100">
                                    <h4 class="font-semibold text-gray-700 mb-3">
                                        <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                                        {{ __('opac.global_search.try_search') }}
                                    </h4>
                                    <div class="flex flex-wrap justify-center gap-2">
                                        @foreach(['Laravel', 'Python', 'Machine Learning', 'Islamic Finance', 'Entrepreneurship'] as $suggestion)
                                            <button 
                                                wire:click="$set('query', '{{ $suggestion }}')"
                                                class="px-4 py-2 bg-white text-cyan-700 rounded-full text-sm hover:bg-cyan-600 hover:text-white transition border border-cyan-200 shadow-sm"
                                            >
                                                {{ $suggestion }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                                
                                <p class="text-xs text-gray-400 mt-6">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    {{ __('opac.global_search.open_library_footer') }} <a href="https://openlibrary.org" target="_blank" class="text-cyan-600 hover:underline">openlibrary.org</a>
                                </p>
                            @elseif($resourceType === 'shamela' && !$query)
                                {{-- Special message for Shamela - Islamic Books --}}
                                <div class="w-24 h-24 bg-gradient-to-br from-blue-100 to-indigo-50 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <i class="fas fa-book-quran text-4xl text-blue-600"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('opac.global_search.search_shamela') }}</h3>
                                <p class="text-gray-500 max-w-md mx-auto mb-6">
                                    {{ __('opac.global_search.shamela_desc') }} <span class="font-semibold text-blue-600">{{ __('opac.global_search.shamela_count') }}</span> 
                                    {{ __('opac.global_search.shamela_source') }}
                                </p>
                                
                                {{-- Example Keywords --}}
                                <div class="mt-6 p-6 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl max-w-lg mx-auto border border-blue-100">
                                    <h4 class="font-semibold text-gray-700 mb-3">
                                        <i class="fas fa-mosque text-blue-600 mr-2"></i>
                                        {{ __('opac.global_search.try_search') }}
                                    </h4>
                                    <div class="flex flex-wrap justify-center gap-2">
                                        @foreach(['حديث', 'فقه', 'تفسير', 'سيرة', 'عقيدة', 'تاريخ'] as $suggestion)
                                            <button 
                                                wire:click="$set('query', '{{ $suggestion }}')"
                                                class="px-4 py-2 bg-white text-blue-700 rounded-full text-sm hover:bg-blue-600 hover:text-white transition border border-blue-200 shadow-sm"
                                            >
                                                {{ $suggestion }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                                
                                <p class="text-xs text-gray-400 mt-6">
                                    <i class="fas fa-database mr-1"></i>
                                    {{ __('opac.global_search.shamela_footer') }}
                                </p>
                            @else
                                {{-- Default empty state --}}
                                <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <i class="fas fa-search text-4xl text-gray-300"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('opac.global_search.no_results') }}</h3>
                                <p class="text-gray-500 max-w-md mx-auto mb-6">
                                    @if($query)
                                        {{ __('opac.global_search.no_results_for') }} "<span class="font-medium">{{ $query }}</span>". 
                                        {{ __('opac.global_search.try_other_keywords') }}
                                    @else
                                        {{ __('opac.global_search.start_search') }}
                                    @endif
                                </p>
                                @if($this->activeFiltersCount > 0)
                                    <button wire:click="clearAllFilters" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-600 text-white rounded-xl font-medium hover:bg-primary-700 transition shadow-lg shadow-primary-600/30">
                                        <i class="fas fa-redo"></i> {{ __('opac.global_search.reset_all_filters') }}
                                    </button>
                                @endif
                                
                                {{-- Suggestions --}}
                                <div class="mt-8 p-6 bg-gray-50 rounded-2xl max-w-lg mx-auto">
                                    <h4 class="font-semibold text-gray-700 mb-3">{{ __('opac.global_search.search_suggestions') }}</h4>
                                    <div class="flex flex-wrap justify-center gap-2">
                                        @foreach(['Manajemen', 'Akuntansi', 'Hukum', 'Teknik', 'Ekonomi'] as $suggestion)
                                            <button 
                                                wire:click="$set('query', '{{ $suggestion }}')"
                                                class="px-4 py-2 bg-white text-gray-600 rounded-full text-sm hover:bg-primary-50 hover:text-primary-600 transition border border-gray-200"
                                            >
                                                {{ $suggestion }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </main>
        </div>
    </div>

    {{-- Subject Modal --}}
    <div 
        x-show="showSubjectModal" 
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showSubjectModal = false"></div>
            
            <div 
                class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-auto overflow-hidden"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            >
                <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-amber-50 to-white">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-tags text-amber-500"></i> {{ __('opac.global_search.select_subject') }}
                        </h3>
                        <button @click="showSubjectModal = false" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                
                <div class="p-5 max-h-96 overflow-y-auto">
                    <div class="flex flex-wrap gap-2">
                        @foreach($this->subjects as $subject)
                            <button 
                                wire:click="toggleSubject({{ $subject->id }})"
                                class="px-3 py-1.5 text-sm rounded-full transition
                                    {{ in_array($subject->id, $selectedSubjects) 
                                        ? 'bg-amber-500 text-white' 
                                        : 'bg-gray-100 text-gray-600 hover:bg-amber-100 hover:text-amber-700' }}"
                            >
                                {{ $subject->name }}
                            </button>
                        @endforeach
                    </div>
                </div>
                
                <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3">
                    <button @click="showSubjectModal = false" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">
                        {{ __('opac.global_search.close') }}
                    </button>
                    <button @click="showSubjectModal = false" class="px-4 py-2 text-sm bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                        {{ __('opac.global_search.apply') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Advanced Search Modal Component --}}
    <x-opac.advanced-search-modal />
</div>
