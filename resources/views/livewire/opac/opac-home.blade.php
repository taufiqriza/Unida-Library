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
            <form id="searchForm" action="{{ route('opac.search') }}" method="GET" class="max-w-2xl mx-auto">
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
                    <a href="{{ route('opac.search') }}?q=islam" class="px-3 py-1 bg-white/10 hover:bg-white/20 text-white text-xs rounded-full transition">Islam</a>
                    <a href="{{ route('opac.search') }}?q=ekonomi" class="px-3 py-1 bg-white/10 hover:bg-white/20 text-white text-xs rounded-full transition">Ekonomi</a>
                    <a href="{{ route('opac.search') }}?q=pendidikan" class="px-3 py-1 bg-white/10 hover:bg-white/20 text-white text-xs rounded-full transition">Pendidikan</a>
                    <a href="{{ route('opac.search') }}?q=hukum" class="px-3 py-1 bg-white/10 hover:bg-white/20 text-white text-xs rounded-full transition hidden sm:inline-block">Hukum</a>
                </div>
            </form>
        </div>
    </section>

    <!-- Stats Bar - Individual Cards -->
    <section class="max-w-7xl mx-auto px-3 lg:px-4 -mt-6 lg:-mt-10 relative z-10">
        <div class="grid grid-cols-3 md:grid-cols-5 gap-2 lg:gap-3">
            <div class="bg-white rounded-xl p-3 lg:p-4 shadow-lg flex items-center gap-3">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-book text-blue-600 text-sm lg:text-lg"></i>
                </div>
                <div>
                    <div class="text-lg lg:text-2xl font-bold text-gray-900">{{ number_format($stats['books']) }}</div>
                    <div class="text-[10px] lg:text-xs text-gray-500">{{ __('opac.homepage.book_titles') }}</div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-3 lg:p-4 shadow-lg flex items-center gap-3">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-graduation-cap text-purple-600 text-sm lg:text-lg"></i>
                </div>
                <div>
                    <div class="text-lg lg:text-2xl font-bold text-gray-900">{{ number_format($stats['etheses']) }}</div>
                    <div class="text-[10px] lg:text-xs text-gray-500">E-Thesis</div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-3 lg:p-4 shadow-lg flex items-center gap-3">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-book-quran text-emerald-600 text-sm lg:text-lg"></i>
                </div>
                <div>
                    <div class="text-lg lg:text-2xl font-bold text-gray-900">8,425</div>
                    <div class="text-[10px] lg:text-xs text-gray-500">{{ __('opac.homepage.shamela_books') }}</div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-3 lg:p-4 shadow-lg hidden md:flex items-center gap-3">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-newspaper text-red-600 text-sm lg:text-lg"></i>
                </div>
                <div>
                    <div class="text-lg lg:text-2xl font-bold text-gray-900">{{ number_format($stats['journals']) }}</div>
                    <div class="text-[10px] lg:text-xs text-gray-500">{{ __('opac.homepage.unida_journals') }}</div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-3 lg:p-4 shadow-lg hidden md:flex items-center gap-3">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-file-pdf text-orange-600 text-sm lg:text-lg"></i>
                </div>
                <div>
                    <div class="text-lg lg:text-2xl font-bold text-gray-900">{{ number_format($stats['ebooks']) }}</div>
                    <div class="text-[10px] lg:text-xs text-gray-500">E-Book</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Welcome & Services Section -->
    <section class="max-w-7xl mx-auto px-3 lg:px-4 py-8 lg:py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Welcome Message -->
            <div class="lg:col-span-2">
                <div class="bg-gradient-to-br from-slate-50 to-white rounded-2xl p-6 lg:p-8 border border-gray-100 h-full">
                    <div class="flex items-start gap-4 mb-6">
                        <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center shadow-lg border border-gray-100 flex-shrink-0 p-2">
                            <img src="{{ asset('storage/logo-portal.png') }}" alt="Logo" class="w-full h-full object-contain">
                        </div>
                        <div>
                            <h2 class="text-xl lg:text-2xl font-bold text-gray-900 mb-1">{{ __('opac.welcome_to_library') }}</h2>
                            <p class="text-gray-500 text-sm">{{ __('opac.university') }}</p>
                        </div>
                    </div>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        {{ __('opac.homepage.welcome_description') }}
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('opac.page', 'visi-misi') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-100 text-blue-700 rounded-lg text-sm font-medium hover:bg-blue-200 transition">
                            <i class="fas fa-bullseye"></i> {{ __('opac.home_menu.vision_mission') }}
                        </a>
                        <a href="{{ route('opac.page', 'jam-layanan') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-100 text-emerald-700 rounded-lg text-sm font-medium hover:bg-emerald-200 transition">
                            <i class="fas fa-clock"></i> {{ __('opac.home_menu.hours') }}
                        </a>
                        <a href="{{ route('opac.page', 'fasilitas') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-100 text-purple-700 rounded-lg text-sm font-medium hover:bg-purple-200 transition">
                            <i class="fas fa-building"></i> {{ __('opac.home_menu.facilities') }}
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
            </div>
        </div>
    </section>

    <!-- Premium Digital Collections -->
    <section class="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 py-10 lg:py-14 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -mr-48 -mt-48"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/5 rounded-full -ml-32 -mb-32"></div>
        <div class="max-w-7xl mx-auto px-3 lg:px-4 relative">
            <div class="text-center mb-8">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 rounded-full text-white text-sm font-medium mb-4">
                    <i class="fas fa-crown text-amber-300"></i> {{ __('opac.homepage.premium_collection') }}
                </div>
                <h2 class="text-2xl lg:text-3xl font-bold text-white mb-2">{{ __('opac.homepage.exclusive_digital') }}</h2>
                <p class="text-blue-200">{{ __('opac.homepage.access_digital') }}</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
                <!-- Maktabah Shamela -->
                <a href="{{ route('opac.shamela.index') }}" class="group relative bg-gradient-to-br from-emerald-600 to-teal-700 rounded-2xl p-6 overflow-hidden hover:shadow-2xl hover:shadow-emerald-500/30 transition-all hover:-translate-y-1">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                    <div class="relative">
                        <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition">
                            <i class="fas fa-book-quran text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">{{ __('opac.homepage.shamela_title') }}</h3>
                        <p class="text-emerald-200 text-sm mb-4">{{ __('opac.homepage.shamela_desc') }}</p>
                        <div class="flex items-center justify-between">
                            <span class="px-3 py-1 bg-white/20 rounded-full text-white text-xs font-medium">{{ __('opac.homepage.shamela_count') }}</span>
                            <i class="fas fa-arrow-right text-white/70 group-hover:translate-x-2 transition"></i>
                        </div>
                    </div>
                </a>
                
                <!-- Universitaria -->
                <a href="{{ route('opac.universitaria.index') }}" class="group relative bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl p-6 overflow-hidden hover:shadow-2xl hover:shadow-amber-500/30 transition-all hover:-translate-y-1">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                    <div class="absolute top-3 right-3">
                        <span class="px-2 py-1 bg-white/25 rounded-full text-[10px] font-bold text-white"><i class="fas fa-crown mr-1"></i>PREMIUM</span>
                    </div>
                    <div class="relative">
                        <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition">
                            <i class="fas fa-landmark text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">{{ __('opac.homepage.universitaria_title') }}</h3>
                        <p class="text-amber-100 text-sm mb-4">{{ __('opac.homepage.universitaria_desc') }}</p>
                        <div class="flex items-center justify-between">
                            <span class="px-3 py-1 bg-white/20 rounded-full text-white text-xs font-medium">{{ __('opac.homepage.heritage') }}</span>
                            <i class="fas fa-arrow-right text-white/70 group-hover:translate-x-2 transition"></i>
                        </div>
                    </div>
                </a>
                
                <!-- Database Jurnal -->
                <a href="{{ route('opac.database-access') }}" class="group relative bg-gradient-to-br from-indigo-600 to-blue-700 rounded-2xl p-6 overflow-hidden hover:shadow-2xl hover:shadow-indigo-500/30 transition-all hover:-translate-y-1">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                    <div class="relative">
                        <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition">
                            <i class="fas fa-database text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">{{ __('opac.homepage.journal_database') }}</h3>
                        <p class="text-indigo-200 text-sm mb-4">{{ __('opac.homepage.journal_desc') }}</p>
                        <div class="flex items-center justify-between">
                            <span class="px-3 py-1 bg-white/20 rounded-full text-white text-xs font-medium">{{ __('opac.homepage.journal_count') }}</span>
                            <i class="fas fa-arrow-right text-white/70 group-hover:translate-x-2 transition"></i>
                        </div>
                    </div>
                </a>
                
                <!-- E-Resources -->
                <a href="{{ route('opac.page', 'e-resources') }}" class="group relative bg-gradient-to-br from-violet-600 to-purple-700 rounded-2xl p-6 overflow-hidden hover:shadow-2xl hover:shadow-violet-500/30 transition-all hover:-translate-y-1">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                    <div class="relative">
                        <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition">
                            <i class="fas fa-globe text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">{{ __('opac.homepage.eresources_title') }}</h3>
                        <p class="text-violet-200 text-sm mb-4">{{ __('opac.homepage.eresources_desc') }}</p>
                        <div class="flex items-center justify-between">
                            <span class="px-3 py-1 bg-white/20 rounded-full text-white text-xs font-medium">{{ __('opac.homepage.free_access') }}</span>
                            <i class="fas fa-arrow-right text-white/70 group-hover:translate-x-2 transition"></i>
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
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-book text-2xl text-blue-200"></i>
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
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-book text-2xl text-amber-200"></i>
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
    <section class="max-w-7xl mx-auto px-4 py-8 lg:py-10">
        <!-- Header -->
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-3">
                <div class="w-1 h-8 bg-blue-600 rounded-full"></div>
                <h2 class="text-xl lg:text-2xl font-bold text-gray-900">{{ __('opac.homepage.news_announcements') }}</h2>
            </div>
            <a href="{{ route('opac.news.index') }}" class="inline-flex items-center gap-2 text-sm text-blue-600 hover:text-blue-700 font-medium group">
                <span>{{ __('opac.view_all') }}</span>
                <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
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
            
            <!-- News Cards - 5 visible -->
            <div id="newsScroll" class="flex gap-4 overflow-x-auto pb-2 scroll-smooth" style="-ms-overflow-style: none; scrollbar-width: none;">
                <style>#newsScroll::-webkit-scrollbar { display: none; }</style>
                
                @foreach($news as $index => $item)
                <a href="{{ route('opac.news.show', $item['slug']) }}" 
                   class="flex-shrink-0 w-[calc(100%-1rem)] sm:w-[calc(50%-0.5rem)] lg:w-[calc(20%-0.8rem)] group">
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden h-full">
                        <!-- Image -->
                        <div class="relative aspect-[16/10] overflow-hidden bg-gray-100">
                            @if($item['image'])
                                <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center">
                                    <i class="fas fa-newspaper text-3xl text-blue-300"></i>
                                </div>
                            @endif
                            
                            <!-- Category Badge -->
                            @if(isset($item['category']) && $item['category'])
                            <div class="absolute top-2 left-2">
                                <span class="px-2 py-1 bg-blue-600 text-white text-[10px] font-medium rounded">
                                    <i class="fas fa-tag mr-1"></i>{{ strtoupper($item['category']) }}
                                </span>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Content -->
                        <div class="p-3">
                            <!-- Date -->
                            <div class="flex items-center gap-3 text-[11px] text-gray-400 mb-2">
                                <span><i class="far fa-calendar mr-1"></i>{{ $item['published_at'] }}</span>
                            </div>
                            
                            <!-- Title -->
                            <h3 class="font-semibold text-gray-900 text-sm leading-snug line-clamp-2 group-hover:text-blue-600 transition mb-2">
                                {{ $item['title'] }}
                            </h3>
                            
                            <!-- Excerpt -->
                            @if(isset($item['excerpt']) && $item['excerpt'])
                            <p class="text-gray-500 text-xs line-clamp-2 mb-3">{{ Str::limit(strip_tags($item['excerpt']), 80) }}</p>
                            @endif
                            
                            <!-- Read More Button -->
                            <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                                <span class="inline-flex items-center gap-1.5 text-blue-600 text-xs font-medium group-hover:gap-2 transition-all">
                                    {{ __('opac.read_more') }} <i class="fas fa-arrow-right text-[10px]"></i>
                                </span>
                            </div>
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
                .animate-scroll {
                    animation: scroll 30s linear infinite;
                }
                .animate-scroll:hover {
                    animation-play-state: paused;
                }
            </style>
        </div>
        @endif
        
        <!-- Contact Bar -->
        <div class="max-w-7xl mx-auto px-4 mt-8">
            <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 rounded-2xl p-5 lg:p-6 relative overflow-hidden">
                <!-- Decorative -->
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
                
                <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <!-- Info -->
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-headset text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-white font-bold">{{ __('opac.homepage.need_help') }}</h3>
                            <p class="text-blue-200 text-sm">{{ __('opac.homepage.contact_desc') }}</p>
                        </div>
                    </div>
                    
                    <!-- Contact Buttons -->
                    <div class="flex flex-wrap items-center gap-3">
                        <a href="mailto:library@unida.gontor.ac.id" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl text-white text-sm transition group">
                            <i class="fas fa-envelope"></i>
                            <span class="hidden sm:inline">library@unida.gontor.ac.id</span>
                            <span class="sm:hidden">Email</span>
                        </a>
                        <a href="https://wa.me/6285183053934" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-500 hover:bg-emerald-400 rounded-xl text-white text-sm font-medium transition group">
                            <i class="fab fa-whatsapp text-lg"></i>
                            <span>0851-8305-3934</span>
                        </a>
                        <a href="https://www.instagram.com/libraryunidagontor" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-400 hover:to-pink-400 rounded-xl text-white text-sm font-medium transition">
                            <i class="fab fa-instagram text-lg"></i>
                            <span class="hidden sm:inline">@libraryunidagontor</span>
                            <span class="sm:hidden">Instagram</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Advanced Search Modal Component -->
    <x-opac.advanced-search-modal />
</div>
