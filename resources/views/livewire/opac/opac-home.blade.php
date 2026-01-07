<div>
    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 text-white py-8 lg:py-16 relative overflow-hidden">
        <!-- Decorative elements -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full -ml-24 -mb-24"></div>
        
        <div class="max-w-7xl mx-auto px-4 text-center relative z-10">
            <h1 class="text-2xl lg:text-4xl font-bold mb-2">{{ __('opac.site_name') }}</h1>
            <p class="text-blue-200 text-sm lg:text-base mb-6">{{ __('opac.university') }}</p>
            
            <!-- Search Box - Powerful Rounded Full Design -->
            <form id="searchForm" action="{{ route('opac.search') }}" method="GET" class="max-w-2xl mx-auto" onsubmit="return handleSearchSubmit(this)">
                <div class="relative group">
                    <!-- Glow effect -->
                    <div class="absolute -inset-1 bg-gradient-to-r from-blue-400 via-white to-blue-400 rounded-full opacity-30 group-hover:opacity-50 blur-lg transition duration-500"></div>
                    
                    <!-- Search input container -->
                    <div class="relative flex items-center bg-white rounded-full shadow-2xl shadow-blue-900/30 overflow-hidden">
                        <div class="pl-5 pr-2">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input 
                            type="search" 
                            name="q" 
                            id="searchInput"
                            placeholder="{{ __('opac.homepage.search_placeholder') }}" 
                            class="flex-1 px-2 py-4 lg:py-5 text-gray-700 text-sm lg:text-base focus:outline-none bg-transparent"
                            autocomplete="off"
                        >
                        <!-- Pill Switcher -->
                        <div class="m-1.5 flex items-center bg-gray-100 rounded-full p-1 gap-1">
                            <button type="submit" class="px-5 lg:px-6 py-2.5 lg:py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-full transition-all duration-300 shadow-lg shadow-blue-600/30 text-sm flex items-center gap-2">
                                <i class="fas fa-search"></i>
                                <span class="hidden sm:inline">{{ __('opac.search') }}</span>
                            </button>
                            <button type="button" id="advancedBtn" onclick="openAdvancedSearch()" class="w-10 h-10 lg:w-11 lg:h-11 flex items-center justify-center text-gray-500 hover:text-blue-600 hover:bg-white rounded-full transition-all duration-300">
                                <i class="fas fa-sliders-h"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Quick search tags -->
                <div class="mt-4 flex flex-wrap items-center justify-center gap-2">
                    <span class="text-blue-200 text-xs">{{ __('opac.homepage.popular_tags') }}:</span>
                    <a href="{{ route('opac.search') }}?q=islam" onclick="SearchSplash?.show('islam')" class="px-3 py-1 bg-white/10 hover:bg-white/20 text-white text-xs rounded-full transition">Islam</a>
                    <a href="{{ route('opac.search') }}?q=ekonomi" onclick="SearchSplash?.show('ekonomi')" class="px-3 py-1 bg-white/10 hover:bg-white/20 text-white text-xs rounded-full transition">Ekonomi</a>
                    <a href="{{ route('opac.search') }}?q=pendidikan" onclick="SearchSplash?.show('pendidikan')" class="px-3 py-1 bg-white/10 hover:bg-white/20 text-white text-xs rounded-full transition">Pendidikan</a>
                    <a href="{{ route('opac.search') }}?q=hukum" onclick="SearchSplash?.show('hukum')" class="px-3 py-1 bg-white/10 hover:bg-white/20 text-white text-xs rounded-full transition hidden sm:inline-block">Hukum</a>
                </div>
            </form>
        </div>
    </section>

    <!-- Stats Bar - Horizontal Scroll on Mobile -->
    <section class="max-w-7xl mx-auto px-2 lg:px-4 -mt-5 lg:-mt-10 relative z-10">
        <!-- Mobile: Horizontal Scroll | Desktop: Grid -->
        <div class="flex lg:grid lg:grid-cols-6 gap-2 lg:gap-3 overflow-x-auto pb-1 lg:pb-0 scrollbar-hide" style="-webkit-overflow-scrolling: touch;">
            <!-- Buku -->
            <div class="bg-white rounded-xl p-2.5 lg:p-4 shadow-sm border border-gray-100 flex items-center gap-2 lg:gap-3 flex-shrink-0 w-[105px] lg:w-auto">
                <div class="w-9 h-9 lg:w-11 lg:h-11 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-book text-blue-600 text-xs lg:text-base"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-sm lg:text-xl font-bold text-gray-900"><span class="lg:hidden">{{ $stats['books'] >= 1000 ? round($stats['books']/1000) . 'K' : $stats['books'] }}</span><span class="hidden lg:inline">{{ number_format($stats['books']) }}</span></div>
                    <div class="text-[8px] lg:text-[10px] text-gray-500">{{ __('opac.stats.books') }}</div>
                </div>
            </div>
            <!-- E-Thesis -->
            <div class="bg-white rounded-xl p-2.5 lg:p-4 shadow-sm border border-gray-100 flex items-center gap-2 lg:gap-3 flex-shrink-0 w-[105px] lg:w-auto">
                <div class="w-9 h-9 lg:w-11 lg:h-11 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-graduation-cap text-purple-600 text-xs lg:text-base"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-sm lg:text-xl font-bold text-gray-900"><span class="lg:hidden">{{ $stats['etheses'] >= 1000 ? round($stats['etheses']/1000) . 'K' : $stats['etheses'] }}</span><span class="hidden lg:inline">{{ number_format($stats['etheses']) }}</span></div>
                    <div class="text-[8px] lg:text-[10px] text-gray-500">{{ __('opac.stats.ethesis') }}</div>
                </div>
            </div>
            <!-- Shamela -->
            <div class="bg-white rounded-xl p-2.5 lg:p-4 shadow-sm border border-gray-100 flex items-center gap-2 lg:gap-3 flex-shrink-0 w-[105px] lg:w-auto">
                <div class="w-9 h-9 lg:w-11 lg:h-11 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-book-quran text-emerald-600 text-xs lg:text-base"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-sm lg:text-xl font-bold text-gray-900"><span class="lg:hidden">8.4K</span><span class="hidden lg:inline">8,400</span></div>
                    <div class="text-[8px] lg:text-[10px] text-gray-500">{{ __('opac.stats.shamela') }}</div>
                </div>
            </div>
            <!-- E-Book -->
            <div class="bg-white rounded-xl p-2.5 lg:p-4 shadow-sm border border-gray-100 flex items-center gap-2 lg:gap-3 flex-shrink-0 w-[105px] lg:w-auto">
                <div class="w-9 h-9 lg:w-11 lg:h-11 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-file-pdf text-orange-600 text-xs lg:text-base"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-sm lg:text-xl font-bold text-gray-900"><span class="lg:hidden">{{ $stats['ebooks'] >= 1000 ? round($stats['ebooks']/1000) . 'K' : $stats['ebooks'] }}</span><span class="hidden lg:inline">{{ number_format($stats['ebooks']) }}</span></div>
                    <div class="text-[8px] lg:text-[10px] text-gray-500">{{ __('opac.stats.ebook') }}</div>
                </div>
            </div>
            <!-- Jurnal -->
            <div class="bg-white rounded-xl p-2.5 lg:p-4 shadow-sm border border-gray-100 flex items-center gap-2 lg:gap-3 flex-shrink-0 w-[105px] lg:w-auto">
                <div class="w-9 h-9 lg:w-11 lg:h-11 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-newspaper text-red-600 text-xs lg:text-base"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-sm lg:text-xl font-bold text-gray-900"><span class="lg:hidden">{{ $stats['journals'] >= 1000 ? round($stats['journals']/1000) . 'K' : $stats['journals'] }}</span><span class="hidden lg:inline">{{ number_format($stats['journals']) }}</span></div>
                    <div class="text-[8px] lg:text-[10px] text-gray-500">{{ __('opac.stats.journals') }}</div>
                </div>
            </div>
            <!-- Anggota -->
            <div class="bg-white rounded-xl p-2.5 lg:p-4 shadow-sm border border-gray-100 flex items-center gap-2 lg:gap-3 flex-shrink-0 w-[105px] lg:w-auto">
                <div class="w-9 h-9 lg:w-11 lg:h-11 bg-cyan-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-users text-cyan-600 text-xs lg:text-base"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-sm lg:text-xl font-bold text-gray-900"><span class="lg:hidden">{{ ($stats['members'] ?? 12500) >= 1000 ? round(($stats['members'] ?? 12500)/1000) . 'K' : ($stats['members'] ?? 12500) }}</span><span class="hidden lg:inline">{{ number_format($stats['members'] ?? 12500) }}</span></div>
                    <div class="text-[8px] lg:text-[10px] text-gray-500">{{ __('opac.stats.members') }}</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Welcome & Services Section -->
    <section class="max-w-7xl mx-auto px-3 lg:px-4 py-8 lg:py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Welcome Message -->
            <div class="lg:col-span-2">
                <div class="bg-gradient-to-br from-slate-50 to-white rounded-2xl p-4 lg:p-8 border border-gray-100 h-full">
                    <div class="flex items-center gap-3 lg:gap-4 mb-4 lg:mb-6">
                        <div class="w-12 h-12 lg:w-16 lg:h-16 bg-white rounded-xl lg:rounded-2xl flex items-center justify-center shadow-lg border border-gray-100 flex-shrink-0 p-1.5 lg:p-2">
                            <img src="{{ asset('storage/logo-portal.png') }}" alt="Logo" class="w-full h-full object-contain">
                        </div>
                        <div>
                            <h2 class="text-lg lg:text-2xl font-bold text-gray-900 mb-0.5 lg:mb-1">{{ __('opac.welcome_to_library') }}</h2>
                            <p class="text-gray-500 text-xs lg:text-sm">{{ __('opac.university') }}</p>
                        </div>
                    </div>
                    <p class="text-gray-600 leading-relaxed mb-4 lg:mb-6 text-sm lg:text-base hidden sm:block">
                        {{ __('opac.homepage.welcome_description') }}
                    </p>
                    <p class="text-gray-600 text-sm mb-4 sm:hidden line-clamp-2">
                        {{ Str::limit(__('opac.homepage.welcome_description'), 80) }}
                    </p>
                    <div class="flex flex-wrap gap-2 lg:gap-3">
                        <a href="{{ route('opac.page', 'visi-misi') }}" class="inline-flex items-center gap-1.5 lg:gap-2 px-3 lg:px-4 py-1.5 lg:py-2 bg-blue-100 text-blue-700 rounded-lg text-xs lg:text-sm font-medium hover:bg-blue-200 transition">
                            <i class="fas fa-bullseye text-[10px] lg:text-xs"></i> {{ __('opac.home_menu.vision_mission') }}
                        </a>
                        <a href="{{ route('opac.page', 'jam-layanan') }}" class="inline-flex items-center gap-1.5 lg:gap-2 px-3 lg:px-4 py-1.5 lg:py-2 bg-emerald-100 text-emerald-700 rounded-lg text-xs lg:text-sm font-medium hover:bg-emerald-200 transition">
                            <i class="fas fa-clock text-[10px] lg:text-xs"></i> {{ __('opac.home_menu.hours') }}
                        </a>
                        <a href="{{ route('opac.page', 'fasilitas') }}" class="inline-flex items-center gap-1.5 lg:gap-2 px-3 lg:px-4 py-1.5 lg:py-2 bg-purple-100 text-purple-700 rounded-lg text-xs lg:text-sm font-medium hover:bg-purple-200 transition">
                            <i class="fas fa-building text-[10px] lg:text-xs"></i> {{ __('opac.home_menu.facilities') }}
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Quick Services -->
            <div class="space-y-3">
                <h3 class="font-bold text-gray-900 flex items-center gap-2 mb-4">
                    <i class="fas fa-bolt text-amber-500"></i> {{ __('opac.homepage.quick_services') }}
                </h3>
                <a href="{{ route('opac.panduan.thesis') }}" class="block bg-gradient-to-r from-purple-500 to-indigo-600 rounded-xl p-4 text-white hover:shadow-lg hover:shadow-purple-200 transition group">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-cloud-upload-alt text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-sm">{{ __('opac.homepage.thesis_upload') }}</h4>
                            <p class="text-purple-200 text-xs">{{ __('opac.homepage.thesis_upload_desc') }}</p>
                        </div>
                        <i class="fas fa-chevron-right text-purple-200 group-hover:translate-x-1 transition"></i>
                    </div>
                </a>
                <a href="{{ route('opac.panduan.plagiarism') }}" class="block bg-gradient-to-r from-teal-500 to-emerald-600 rounded-xl p-4 text-white hover:shadow-lg hover:shadow-teal-200 transition group">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-shield-alt text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-sm">{{ __('opac.homepage.plagiarism_check') }}</h4>
                            <p class="text-teal-200 text-xs">{{ __('opac.homepage.plagiarism_desc') }}</p>
                        </div>
                        <i class="fas fa-chevron-right text-teal-200 group-hover:translate-x-1 transition"></i>
                    </div>
                </a>
                <a href="{{ route('opac.panduan.member') }}" class="block bg-gradient-to-r from-blue-500 to-cyan-600 rounded-xl p-4 text-white hover:shadow-lg hover:shadow-blue-200 transition group">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-id-card text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-sm">{{ __('opac.homepage.member_guide') }}</h4>
                            <p class="text-blue-200 text-xs">{{ __('opac.homepage.member_guide_desc') }}</p>
                        </div>
                        <i class="fas fa-chevron-right text-blue-200 group-hover:translate-x-1 transition"></i>
                    </div>
                </a>
                <a href="{{ route('opac.page', 'e-learning') }}" class="block bg-gradient-to-r from-amber-500 to-orange-600 rounded-xl p-4 text-white hover:shadow-lg hover:shadow-amber-200 transition group">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-graduation-cap text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-sm">E-Learning</h4>
                            <p class="text-amber-200 text-xs">Kelas & workshop literasi</p>
                        </div>
                        <i class="fas fa-chevron-right text-amber-200 group-hover:translate-x-1 transition"></i>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- Premium Digital Collections -->
    <section class="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 py-6 lg:py-14 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -mr-48 -mt-48"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/5 rounded-full -ml-32 -mb-32"></div>
        <div class="max-w-7xl mx-auto px-2 lg:px-4 relative">
            <div class="text-center mb-4 lg:mb-8">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 lg:px-4 lg:py-2 bg-white/20 rounded-full text-white text-xs lg:text-sm font-medium mb-2 lg:mb-4">
                    <i class="fas fa-crown text-amber-300 text-xs"></i> {{ __('opac.homepage.premium_collection') }}
                </div>
                <h2 class="text-lg lg:text-3xl font-bold text-white mb-1 lg:mb-2">{{ __('opac.homepage.exclusive_digital') }}</h2>
                <p class="text-blue-200 text-xs lg:text-base hidden sm:block">{{ __('opac.homepage.access_digital') }}</p>
            </div>
            
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 lg:gap-6">
                <!-- Maktabah Shamela -->
                <a href="{{ route('opac.shamela.index') }}" class="group relative bg-gradient-to-br from-emerald-600 to-teal-700 rounded-xl lg:rounded-2xl p-3 lg:p-6 overflow-hidden hover:shadow-2xl transition-all">
                    <div class="absolute top-0 right-0 w-20 lg:w-32 h-20 lg:h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                    <div class="relative">
                        <div class="w-10 h-10 lg:w-14 lg:h-14 bg-white/20 rounded-lg lg:rounded-xl flex items-center justify-center mb-2 lg:mb-4">
                            <i class="fas fa-book-quran text-white text-base lg:text-2xl"></i>
                        </div>
                        <h3 class="text-sm lg:text-xl font-bold text-white mb-1 lg:mb-2">Shamela</h3>
                        <p class="text-emerald-200 text-[10px] lg:text-sm mb-2 lg:mb-4 hidden sm:block">{{ __('opac.homepage.shamela_desc') }}</p>
                        <div class="flex items-center justify-between">
                            <span class="px-2 py-0.5 lg:px-3 lg:py-1 bg-white/20 rounded-full text-white text-[9px] lg:text-xs font-medium">8.4K</span>
                            <i class="fas fa-arrow-right text-white/70 text-xs lg:text-base"></i>
                        </div>
                    </div>
                </a>
                
                <!-- Universitaria -->
                <a href="{{ route('opac.universitaria.index') }}" class="group relative bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl lg:rounded-2xl p-3 lg:p-6 overflow-hidden hover:shadow-2xl transition-all">
                    <div class="absolute top-0 right-0 w-20 lg:w-32 h-20 lg:h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                    <div class="absolute top-2 right-2 lg:top-3 lg:right-3">
                        <span class="px-1.5 py-0.5 lg:px-2 lg:py-1 bg-white/25 rounded-full text-[8px] lg:text-[10px] font-bold text-white"><i class="fas fa-crown"></i></span>
                    </div>
                    <div class="relative">
                        <div class="w-10 h-10 lg:w-14 lg:h-14 bg-white/20 rounded-lg lg:rounded-xl flex items-center justify-center mb-2 lg:mb-4">
                            <i class="fas fa-landmark text-white text-base lg:text-2xl"></i>
                        </div>
                        <h3 class="text-sm lg:text-xl font-bold text-white mb-1 lg:mb-2">Universitaria</h3>
                        <p class="text-amber-100 text-[10px] lg:text-sm mb-2 lg:mb-4 hidden sm:block">{{ __('opac.homepage.universitaria_desc') }}</p>
                        <div class="flex items-center justify-between">
                            <span class="px-2 py-0.5 lg:px-3 lg:py-1 bg-white/20 rounded-full text-white text-[9px] lg:text-xs font-medium">Heritage</span>
                            <i class="fas fa-arrow-right text-white/70 text-xs lg:text-base"></i>
                        </div>
                    </div>
                </a>
                
                <!-- Database Jurnal -->
                <a href="{{ route('opac.database-access') }}" class="group relative bg-gradient-to-br from-indigo-600 to-blue-700 rounded-xl lg:rounded-2xl p-3 lg:p-6 overflow-hidden hover:shadow-2xl transition-all">
                    <div class="absolute top-0 right-0 w-20 lg:w-32 h-20 lg:h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                    <div class="relative">
                        <div class="w-10 h-10 lg:w-14 lg:h-14 bg-white/20 rounded-lg lg:rounded-xl flex items-center justify-center mb-2 lg:mb-4">
                            <i class="fas fa-database text-white text-base lg:text-2xl"></i>
                        </div>
                        <h3 class="text-sm lg:text-xl font-bold text-white mb-1 lg:mb-2">Database</h3>
                        <p class="text-indigo-200 text-[10px] lg:text-sm mb-2 lg:mb-4 hidden sm:block">{{ __('opac.homepage.journal_desc') }}</p>
                        <div class="flex items-center justify-between">
                            <span class="px-2 py-0.5 lg:px-3 lg:py-1 bg-white/20 rounded-full text-white text-[9px] lg:text-xs font-medium">10+</span>
                            <i class="fas fa-arrow-right text-white/70 text-xs lg:text-base"></i>
                        </div>
                    </div>
                </a>
                
                <!-- E-Resources -->
                <a href="{{ route('opac.page', 'e-resources') }}" class="group relative bg-gradient-to-br from-violet-600 to-purple-700 rounded-xl lg:rounded-2xl p-3 lg:p-6 overflow-hidden hover:shadow-2xl transition-all">
                    <div class="absolute top-0 right-0 w-20 lg:w-32 h-20 lg:h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                    <div class="relative">
                        <div class="w-10 h-10 lg:w-14 lg:h-14 bg-white/20 rounded-lg lg:rounded-xl flex items-center justify-center mb-2 lg:mb-4">
                            <i class="fas fa-globe text-white text-base lg:text-2xl"></i>
                        </div>
                        <h3 class="text-sm lg:text-xl font-bold text-white mb-1 lg:mb-2">E-Resources</h3>
                        <p class="text-violet-200 text-[10px] lg:text-sm mb-2 lg:mb-4 hidden sm:block">{{ __('opac.homepage.eresources_desc') }}</p>
                        <div class="flex items-center justify-between">
                            <span class="px-2 py-0.5 lg:px-3 lg:py-1 bg-white/20 rounded-full text-white text-[9px] lg:text-xs font-medium">Free</span>
                            <i class="fas fa-arrow-right text-white/70 text-xs lg:text-base"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- Discovery Section with Tabs -->
    <section class="max-w-7xl mx-auto px-4 py-10 lg:py-14" x-data="{ activeTab: 'books' }">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xl lg:text-2xl font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-sparkles text-blue-500"></i> {{ __('opac.homepage.explore_collection') }}
                </h2>
                <p class="text-gray-500 text-sm mt-1">{{ __('opac.homepage.discover_latest') }}</p>
            </div>
            
            <!-- Tab Navigation -->
            <div class="flex items-center gap-1 bg-gray-100 rounded-xl p-1 overflow-x-auto">
                <button @click="activeTab = 'books'" :class="activeTab === 'books' ? 'bg-white shadow text-blue-600' : 'text-gray-600 hover:text-gray-900'" class="px-4 py-2 rounded-lg text-sm font-medium transition whitespace-nowrap">
                    <i class="fas fa-book mr-1.5"></i>{{ __('opac.homepage.books') }}
                </button>
                <button @click="activeTab = 'ethesis'" :class="activeTab === 'ethesis' ? 'bg-white shadow text-purple-600' : 'text-gray-600 hover:text-gray-900'" class="px-4 py-2 rounded-lg text-sm font-medium transition whitespace-nowrap">
                    <i class="fas fa-graduation-cap mr-1.5"></i>{{ __('opac.homepage.ethesis') }}
                </button>
                <button @click="activeTab = 'ebooks'" :class="activeTab === 'ebooks' ? 'bg-white shadow text-orange-600' : 'text-gray-600 hover:text-gray-900'" class="px-4 py-2 rounded-lg text-sm font-medium transition whitespace-nowrap">
                    <i class="fas fa-file-pdf mr-1.5"></i>{{ __('opac.homepage.ebook') }}
                </button>
                <button @click="activeTab = 'manuscripts'" :class="activeTab === 'manuscripts' ? 'bg-white shadow text-amber-600' : 'text-gray-600 hover:text-gray-900'" class="px-4 py-2 rounded-lg text-sm font-medium transition whitespace-nowrap">
                    <i class="fas fa-scroll mr-1.5"></i>Naskah Nusantara
                </button>
                <button @click="activeTab = 'journals'" :class="activeTab === 'journals' ? 'bg-white shadow text-emerald-600' : 'text-gray-600 hover:text-gray-900'" class="px-4 py-2 rounded-lg text-sm font-medium transition whitespace-nowrap">
                    <i class="fas fa-newspaper mr-1.5"></i>{{ __('opac.homepage.journals') }}
                </button>
            </div>
        </div>

        <!-- Books Tab -->
        <div x-show="activeTab === 'books'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
            @if($newBooks->count() > 0)
            <div class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-6 lg:grid-cols-8 gap-2 lg:gap-3">
                @foreach($newBooks->take(8) as $index => $book)
                <a href="{{ route('opac.catalog.show', $book['id']) }}" class="group">
                    <div class="aspect-[2/3] rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 group-hover:-translate-y-1 relative bg-gradient-to-br from-blue-50 to-slate-50">
                        @if($book['cover'])
                            <img src="{{ $book['cover'] }}" alt="{{ $book['title'] }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-blue-600 via-indigo-500 to-purple-600 flex flex-col items-center justify-center p-2 text-center">
                                <i class="fas fa-book text-lg text-white/70 mb-1"></i>
                                <p class="text-white font-bold text-[8px] leading-tight line-clamp-3">{{ Str::limit($book['title'], 35) }}</p>
                            </div>
                        @endif
                        @if($index < 3)
                        <div class="absolute top-1.5 left-1.5 bg-gradient-to-r from-blue-500 to-indigo-500 text-white text-[7px] px-1.5 py-0.5 rounded-full font-semibold uppercase tracking-wide shadow">
                            <i class="fas fa-certificate text-[6px] mr-0.5"></i>Baru
                        </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-2">
                            <h3 class="text-white text-[10px] lg:text-xs font-medium line-clamp-2 leading-tight">{{ $book['title'] }}</h3>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            <div class="text-center mt-6">
                <a href="{{ route('opac.search') }}?type=book&sort=latest" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-medium transition">
                    {{ __('opac.homepage.view_all_books') }} <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            @else
            <div class="text-center py-12 text-gray-400">
                <i class="fas fa-book text-4xl mb-3"></i>
                <p>{{ __('opac.homepage.no_books') }}</p>
            </div>
            @endif
        </div>

        <!-- E-Thesis Tab -->
        <div x-show="activeTab === 'ethesis'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
            @if($latestEtheses->count() > 0)
            <div class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-6 lg:grid-cols-8 gap-2 lg:gap-3">
                @foreach($latestEtheses->take(8) as $thesis)
                <a href="{{ route('opac.ethesis.show', $thesis->id) }}" class="group">
                    <div class="aspect-[2/3] rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 group-hover:-translate-y-1 relative bg-gradient-to-br from-purple-100 to-indigo-100">
                        <div class="w-full h-full flex flex-col items-center justify-center p-3 text-center">
                            <i class="fas fa-graduation-cap text-3xl text-purple-300 mb-2"></i>
                            <span class="text-[9px] text-purple-400 uppercase tracking-wide font-medium">Thesis</span>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-purple-900/90 via-purple-900/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-2">
                            <h3 class="text-white text-[10px] lg:text-xs font-medium line-clamp-3 leading-tight">{{ $thesis->title }}</h3>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            <div class="text-center mt-6">
                <a href="{{ route('opac.search') }}?type=ethesis" class="inline-flex items-center gap-2 px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-sm font-medium transition">
                    {{ __('opac.homepage.view_all_ethesis') }} <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            @else
            <div class="text-center py-12 text-gray-400">
                <i class="fas fa-graduation-cap text-4xl mb-3"></i>
                <p>{{ __('opac.homepage.no_ethesis') }}</p>
            </div>
            @endif
        </div>

        <!-- E-Books Tab -->
        <div x-show="activeTab === 'ebooks'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
            @if($latestEbooks->count() > 0)
            <div class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-6 lg:grid-cols-8 gap-2 lg:gap-3">
                @foreach($latestEbooks->take(8) as $ebook)
                <a href="{{ route('opac.ebook.show', $ebook->id) }}" class="group">
                    <div class="aspect-[2/3] rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 group-hover:-translate-y-1 relative bg-gradient-to-br from-orange-100 to-red-100">
                        <div class="w-full h-full flex flex-col items-center justify-center p-3 text-center">
                            <i class="fas fa-file-pdf text-3xl text-orange-300 mb-2"></i>
                            <span class="text-[9px] text-orange-400 uppercase tracking-wide font-medium">E-Book</span>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-orange-900/90 via-orange-900/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-2">
                            <h3 class="text-white text-[10px] lg:text-xs font-medium line-clamp-3 leading-tight">{{ $ebook->title }}</h3>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            <div class="text-center mt-6">
                <a href="{{ route('opac.search') }}?type=ebook" class="inline-flex items-center gap-2 px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-sm font-medium transition">
                    {{ __('opac.homepage.view_all_ebook') }} <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            @else
            <div class="text-center py-12 text-gray-400">
                <i class="fas fa-file-pdf text-4xl mb-3"></i>
                <p>{{ __('opac.homepage.no_ebook') }}</p>
            </div>
            @endif
        </div>

        <!-- Journals Tab -->
        <div x-show="activeTab === 'journals'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
            @if($latestJournals->count() > 0)
            <div class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-6 lg:grid-cols-8 gap-2 lg:gap-3">
                @foreach($latestJournals->take(8) as $journal)
                <a href="{{ route('opac.journals.show', $journal->id) }}" class="group">
                    <div class="aspect-[2/3] rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 group-hover:-translate-y-1 relative bg-gradient-to-br from-emerald-100 to-teal-100">
                        <div class="w-full h-full flex flex-col items-center justify-center p-3 text-center">
                            <i class="fas fa-file-lines text-3xl text-emerald-300 mb-2"></i>
                            <span class="text-[9px] text-emerald-400 uppercase tracking-wide font-medium">Journal</span>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-emerald-900/90 via-emerald-900/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-2">
                            <h3 class="text-white text-[10px] lg:text-xs font-medium line-clamp-3 leading-tight">{{ $journal->title }}</h3>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            <div class="text-center mt-6">
                <a href="{{ route('opac.journals.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-medium transition">
                    {{ __('opac.homepage.view_all_journals') }} <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            @else
            <div class="text-center py-12 text-gray-400">
                <i class="fas fa-newspaper text-4xl mb-3"></i>
                <p>{{ __('opac.homepage.no_journals') }}</p>
            </div>
            @endif
        </div>

        <!-- Manuscripts Tab -->
        <div x-show="activeTab === 'manuscripts'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
            @if($featuredManuscripts->count() > 0)
            <div class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-6 lg:grid-cols-8 gap-2 lg:gap-3">
                @foreach($featuredManuscripts->take(8) as $manuscript)
                <a href="{{ $manuscript['url'] }}" target="_blank" class="group">
                    <div class="aspect-[2/3] rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 group-hover:-translate-y-1 relative bg-gradient-to-br from-amber-100 to-orange-100">
                        <img 
                            src="{{ $manuscript['cover'] }}" 
                            alt="{{ $manuscript['title'] }}"
                            class="w-full h-full object-cover"
                            loading="lazy"
                            onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjI2NyIgdmlld0JveD0iMCAwIDIwMCAyNjciIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIyMDAiIGhlaWdodD0iMjY3IiBmaWxsPSIjRkVGM0UyIi8+CjxwYXRoIGQ9Ik0xMDAgMTMzLjVDMTE4LjIyNSAxMzMuNSAxMzMgMTE4LjcyNSAxMzMgMTAwLjVDMTMzIDgyLjI3NDYgMTE4LjIyNSA2Ny41IDEwMCA2Ny41QzgxLjc3NDYgNjcuNSA2NyA4Mi4yNzQ2IDY3IDEwMC41QzY3IDExOC43MjUgODEuNzc0NiAxMzMuNSAxMDAgMTMzLjVaIiBmaWxsPSIjRjU5RTBCIi8+CjxwYXRoIGQ9Ik0xNjcgMjAwLjVDMTY3IDE2My4zNTUgMTM3LjE0NSAxMzMuNSAxMDAgMTMzLjVDNjIuODU1IDEzMy41IDMzIDE2My4zNTUgMzMgMjAwLjVIMTY3WiIgZmlsbD0iI0Y1OUUwQiIvPgo8L3N2Zz4K'"
                        >
                        <!-- Type Badge -->
                        @if($manuscript['type'])
                            <div class="absolute top-1.5 left-1.5 bg-gradient-to-r from-amber-500 to-orange-500 text-white text-[7px] px-1.5 py-0.5 rounded-full font-semibold uppercase tracking-wide shadow">
                                <i class="fas fa-scroll text-[6px] mr-0.5"></i>{{ $manuscript['type'] }}
                            </div>
                        @endif
                        <!-- External Link -->
                        <div class="absolute top-1.5 right-1.5 w-6 h-6 bg-black/50 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <i class="fas fa-external-link-alt text-white text-[8px]"></i>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-2">
                            <h3 class="text-white text-[10px] lg:text-xs font-medium line-clamp-2 leading-tight">{{ $manuscript['title'] }}</h3>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            <div class="text-center mt-6">
                <a href="{{ route('opac.khastara.browse') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-sm font-medium transition">
                    Jelajahi Naskah Nusantara <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            @else
            <div class="text-center py-12 text-gray-400">
                <i class="fas fa-scroll text-4xl mb-3"></i>
                <p>Koleksi naskah sedang dimuat...</p>
            </div>
            @endif
        </div>
    </section>

    <!-- Recommended Books -->
    @if($popularBooks->count() > 0)
    <section class="bg-gradient-to-b from-amber-50 to-white py-8 lg:py-10">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg lg:text-xl font-bold text-gray-900"><i class="fas fa-star text-amber-500 mr-2"></i>{{ __('opac.homepage.recommendations') }}</h2>
                <a href="{{ route('opac.search') . '?type=book' }}" class="text-sm text-amber-600 hover:text-amber-700">{{ __('opac.view_all') }} <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
            <div class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-6 lg:grid-cols-8 gap-2 lg:gap-3">
                @foreach($popularBooks->take(8) as $index => $book)
                <a href="{{ route('opac.catalog.show', $book['id']) }}" class="group">
                    <div class="aspect-[2/3] rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 group-hover:-translate-y-1 relative bg-gradient-to-br from-amber-50 to-slate-50">
                        @if($book['cover'])
                            <img src="{{ $book['cover'] }}" alt="{{ $book['title'] }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-amber-500 via-orange-500 to-red-500 flex flex-col items-center justify-center p-2 text-center">
                                <i class="fas fa-book text-lg text-white/70 mb-1"></i>
                                <p class="text-white font-bold text-[8px] leading-tight line-clamp-3">{{ Str::limit($book['title'], 35) }}</p>
                            </div>
                        @endif
                        @if($index < 3)
                        <div class="absolute top-1.5 left-1.5 w-5 h-5 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full flex items-center justify-center shadow-lg">
                            <span class="text-white text-[9px] font-bold">{{ $index + 1 }}</span>
                        </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-2">
                            <h3 class="text-white text-[10px] lg:text-xs font-medium line-clamp-2 leading-tight">{{ $book['title'] }}</h3>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- News & Events -->
    @if(count($news) > 0)
    <section class="max-w-7xl mx-auto px-2 lg:px-4 py-6 lg:py-10">
        <!-- Header -->
        <div class="flex items-center justify-between mb-4 lg:mb-5">
            <div class="flex items-center gap-2 lg:gap-3">
                <div class="w-1 h-6 lg:h-8 bg-blue-600 rounded-full"></div>
                <h2 class="text-base lg:text-2xl font-bold text-gray-900">{{ __('opac.homepage.news_announcements') }}</h2>
            </div>
            <a href="{{ route('opac.news.index') }}" class="inline-flex items-center gap-1 lg:gap-2 text-xs lg:text-sm text-blue-600 hover:text-blue-700 font-medium group">
                <span>{{ __('opac.view_all') }}</span>
                <i class="fas fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>
        
        <!-- Horizontal Scroll Container -->
        <div class="relative group/scroll">
            <!-- Navigation Arrows -->
            <button onclick="document.getElementById('newsScroll').scrollBy({left: -280, behavior: 'smooth'})" 
                    class="absolute -left-3 top-1/2 -translate-y-1/2 z-10 w-10 h-10 bg-white shadow-lg rounded-full items-center justify-center text-gray-600 hover:text-blue-600 hover:shadow-xl transition-all hidden lg:flex opacity-0 group-hover/scroll:opacity-100">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button onclick="document.getElementById('newsScroll').scrollBy({left: 280, behavior: 'smooth'})" 
                    class="absolute -right-3 top-1/2 -translate-y-1/2 z-10 w-10 h-10 bg-white shadow-lg rounded-full items-center justify-center text-gray-600 hover:text-blue-600 hover:shadow-xl transition-all hidden lg:flex opacity-0 group-hover/scroll:opacity-100">
                <i class="fas fa-chevron-right"></i>
            </button>
            
            <!-- News Cards -->
            <div id="newsScroll" class="flex gap-2 lg:gap-4 overflow-x-auto pb-2 scroll-smooth" style="-ms-overflow-style: none; scrollbar-width: none;">
                <style>#newsScroll::-webkit-scrollbar { display: none; }</style>
                
                @foreach($news as $index => $item)
                <a href="{{ route('opac.news.show', $item['slug']) }}" 
                   class="flex-shrink-0 w-[160px] sm:w-[200px] lg:w-[calc(20%-0.8rem)] group">
                    <div class="bg-white rounded-lg lg:rounded-xl border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden h-full">
                        <!-- Image -->
                        <div class="relative aspect-[16/10] overflow-hidden bg-gray-100">
                            @if($item['image'])
                                <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center">
                                    <i class="fas fa-newspaper text-xl lg:text-3xl text-blue-300"></i>
                                </div>
                            @endif
                            
                            <!-- Category Badge -->
                            @if(isset($item['category']) && $item['category'])
                            <div class="absolute top-1.5 left-1.5 lg:top-2 lg:left-2">
                                <span class="px-1.5 py-0.5 lg:px-2 lg:py-1 bg-blue-600 text-white text-[8px] lg:text-[10px] font-medium rounded">
                                    {{ strtoupper(Str::limit($item['category'], 6, '')) }}
                                </span>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Content -->
                        <div class="p-2 lg:p-3">
                            <!-- Date -->
                            <div class="text-[9px] lg:text-[11px] text-gray-400 mb-1 lg:mb-2">
                                <i class="far fa-calendar mr-0.5"></i>{{ $item['published_at'] }}
                            </div>
                            
                            <!-- Title -->
                            <h3 class="font-semibold text-gray-900 text-xs lg:text-sm leading-snug line-clamp-2 group-hover:text-blue-600 transition">
                                {{ $item['title'] }}
                            </h3>
                            
                            <!-- Excerpt - Hidden on mobile -->
                            @if(isset($item['excerpt']) && $item['excerpt'])
                            <p class="text-gray-500 text-xs line-clamp-2 mb-3 hidden lg:block">{{ Str::limit(strip_tags($item['excerpt']), 60) }}</p>
                            @endif
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Library Branches & Contact -->
    <section class="bg-gradient-to-b from-white to-slate-50 py-8 lg:py-12 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4">
            <!-- Section Header -->
            <div class="text-center mb-6">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 rounded-full text-blue-600 text-sm font-medium mb-3">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>{{ __('opac.homepage.library_network') }}</span>
                </div>
                <h2 class="text-xl lg:text-2xl font-bold text-gray-900">{{ __('opac.homepage.visit_branches') }}</h2>
                <p class="text-gray-500 text-sm mt-1">{{ __('opac.homepage.branch_locations', ['count' => count($branches)]) }}</p>
            </div>
        </div>
        
        <!-- Auto Scroll Branches -->
        @if(count($branches) > 0)
        <div class="relative">
            <!-- Gradient Fade -->
            <div class="absolute left-0 top-0 bottom-0 w-16 bg-gradient-to-r from-white to-transparent z-10 pointer-events-none"></div>
            <div class="absolute right-0 top-0 bottom-0 w-16 bg-gradient-to-l from-slate-50 to-transparent z-10 pointer-events-none"></div>
            
            <!-- Scrolling Container -->
            <div class="flex animate-scroll hover:pause-animation">
                @php $icons = ['fa-building', 'fa-landmark', 'fa-university', 'fa-school', 'fa-book-reader', 'fa-graduation-cap']; @endphp
                @php $colors = ['from-blue-500 to-indigo-600', 'from-emerald-500 to-teal-600', 'from-purple-500 to-violet-600', 'from-amber-500 to-orange-600', 'from-rose-500 to-pink-600', 'from-cyan-500 to-blue-600']; @endphp
                
                <!-- First set of cards -->
                @foreach($branches as $index => $branch)
                <div class="flex-shrink-0 w-48 mx-2">
                    <div class="bg-white rounded-xl p-4 shadow-sm hover:shadow-lg border border-gray-100 transition-all duration-300 hover:-translate-y-1 group h-full">
                        <div class="w-10 h-10 bg-gradient-to-br {{ $colors[$index % count($colors)] }} rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <i class="fas {{ $icons[$index % count($icons)] }} text-white text-sm"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900 text-sm leading-tight mb-1">{{ $branch['name'] }}</h4>
                        @if($branch['address'])
                        <p class="text-gray-400 text-[10px] line-clamp-2"><i class="fas fa-map-pin mr-1"></i>{{ $branch['address'] }}</p>
                        @else
                        <p class="text-gray-400 text-[10px]"><i class="fas fa-check-circle mr-1 text-emerald-400"></i>{{ __('opac.available') }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
                
                <!-- Duplicate for seamless loop -->
                @foreach($branches as $index => $branch)
                <div class="flex-shrink-0 w-48 mx-2">
                    <div class="bg-white rounded-xl p-4 shadow-sm hover:shadow-lg border border-gray-100 transition-all duration-300 hover:-translate-y-1 group h-full">
                        <div class="w-10 h-10 bg-gradient-to-br {{ $colors[$index % count($colors)] }} rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <i class="fas {{ $icons[$index % count($icons)] }} text-white text-sm"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900 text-sm leading-tight mb-1">{{ $branch['name'] }}</h4>
                        @if($branch['address'])
                        <p class="text-gray-400 text-[10px] line-clamp-2"><i class="fas fa-map-pin mr-1"></i>{{ $branch['address'] }}</p>
                        @else
                        <p class="text-gray-400 text-[10px]"><i class="fas fa-check-circle mr-1 text-emerald-400"></i>{{ __('opac.available') }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            
            <style>
                @keyframes scroll {
                    0% { transform: translateX(0); }
                    100% { transform: translateX(-50%); }
                }
                @keyframes scroll-rtl {
                    0% { transform: translateX(0); }
                    100% { transform: translateX(50%); }
                }
                .animate-scroll {
                    animation: scroll 30s linear infinite;
                }
                [dir="rtl"] .animate-scroll {
                    animation: scroll-rtl 30s linear infinite;
                }
                .animate-scroll:hover {
                    animation-play-state: paused;
                }
            </style>
        </div>
        @endif
        
        <!-- Contact Bar -->
        <div class="max-w-7xl mx-auto px-2 lg:px-4 mt-6 lg:mt-8">
            <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 rounded-xl lg:rounded-2xl p-3 lg:p-6 relative overflow-hidden">
                <!-- Decorative -->
                <div class="absolute top-0 right-0 w-20 lg:w-32 h-20 lg:h-32 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                
                <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 lg:gap-4">
                    <!-- Info -->
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 lg:w-12 lg:h-12 bg-white/20 rounded-lg lg:rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-headset text-white text-base lg:text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-white font-bold text-sm lg:text-base">{{ __('opac.homepage.need_help') }}</h3>
                            <p class="text-blue-200 text-[10px] lg:text-sm hidden sm:block">{{ __('opac.homepage.contact_desc') }}</p>
                        </div>
                    </div>
                    
                    <!-- Contact Buttons -->
                    <div class="flex flex-wrap items-center gap-2 lg:gap-3">
                        <a href="mailto:library@unida.gontor.ac.id" class="inline-flex items-center gap-1.5 px-3 py-2 lg:px-4 lg:py-2.5 bg-white/10 hover:bg-white/20 border border-white/20 rounded-lg lg:rounded-xl text-white text-xs lg:text-sm transition">
                            <i class="fas fa-envelope text-xs"></i>
                            <span class="hidden sm:inline">library@unida.gontor.ac.id</span>
                            <span class="sm:hidden">Email</span>
                        </a>
                        <a href="https://wa.me/6285183053934" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-2 lg:px-4 lg:py-2.5 bg-emerald-500 hover:bg-emerald-400 rounded-lg lg:rounded-xl text-white text-xs lg:text-sm font-medium transition">
                            <i class="fab fa-whatsapp"></i>
                            <span>WhatsApp</span>
                        </a>
                        <a href="https://www.instagram.com/libraryunidagontor" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-2 lg:px-4 lg:py-2.5 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg lg:rounded-xl text-white text-xs lg:text-sm font-medium transition">
                            <i class="fab fa-instagram"></i>
                            <span class="hidden sm:inline">@libraryunidagontor</span>
                            <span class="sm:hidden">IG</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Advanced Search Modal Component -->
    <x-opac.advanced-search-modal />
    
    <script>
    function handleSearchSubmit(form) {
        const query = form.querySelector('input[name="q"]').value.trim();
        if (query && window.SearchSplash) {
            SearchSplash.show(query);
        }
        return true;
    }
    </script>
</div>
