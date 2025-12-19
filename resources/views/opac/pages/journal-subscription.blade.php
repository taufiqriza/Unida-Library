<x-opac.layout :title="__('opac.additional_pages.journal_subscription.title')">
    <x-opac.page-header 
        :title="__('opac.additional_pages.journal_subscription.title')" 
        :subtitle="__('opac.additional_pages.journal_subscription.subtitle')"
        :breadcrumbs="[['label' => 'E-Resources'], ['label' => __('opac.additional_pages.journal_subscription.title')]]"
    />

    <section class="max-w-6xl mx-auto px-4 py-6 lg:py-10">
        <!-- Hero Stats -->
        <div class="bg-gradient-to-br from-primary-600 via-blue-600 to-indigo-700 rounded-2xl lg:rounded-3xl p-5 lg:p-8 mb-6 lg:mb-8 relative overflow-hidden">
            <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.05\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-50"></div>
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2 blur-3xl"></div>
            
            <div class="relative">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                        <i class="fas fa-globe text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl lg:text-2xl font-bold text-white">{{ __('opac.additional_pages.journal_subscription.hero_title') }}</h2>
                        <p class="text-blue-200 text-sm">{{ __('opac.additional_pages.journal_subscription.hero_subtitle') }}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4 mt-6">
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 lg:p-4 text-center">
                        <div class="text-2xl lg:text-3xl font-bold text-white">11+</div>
                        <div class="text-xs text-blue-200">{{ __('opac.additional_pages.journal_subscription.ebook_sources') }}</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 lg:p-4 text-center">
                        <div class="text-2xl lg:text-3xl font-bold text-white">3</div>
                        <div class="text-xs text-blue-200">{{ __('opac.additional_pages.journal_subscription.journal_databases') }}</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 lg:p-4 text-center">
                        <div class="text-2xl lg:text-3xl font-bold text-white">100K+</div>
                        <div class="text-xs text-blue-200">{{ __('opac.additional_pages.journal_subscription.total_collection') }}</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 lg:p-4 text-center">
                        <div class="text-2xl lg:text-3xl font-bold text-white">24/7</div>
                        <div class="text-xs text-blue-200">{{ __('opac.additional_pages.journal_subscription.online_access') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info -->
        <div class="bg-amber-50 rounded-xl p-4 border border-amber-200 mb-6 lg:mb-8">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-info-circle text-amber-600 text-sm"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 text-sm mb-1">{{ __('opac.additional_pages.journal_subscription.access_info') }}</h3>
                    <p class="text-gray-600 text-xs lg:text-sm">{{ __('opac.additional_pages.journal_subscription.access_info_desc') }}</p>
                </div>
            </div>
        </div>


        <!-- E-Book Gratis -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg flex items-center gap-2">
            <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-book text-emerald-600 text-sm"></i>
            </div>
            {{ __('opac.additional_pages.journal_subscription.free_ebooks') }}
        </h3>
        
        @php
        $ebooks = [
            [
                'name' => 'Maktabah Syamilah',
                'url' => route('opac.shamela.index'),
                'desc' => 'Perpustakaan digital Islam klasik - Database Lokal',
                'collections' => '8,425',
                'type' => 'Kitab',
                'color' => 'emerald',
                'icon' => 'fa-mosque',
                'subjects' => ['Tafsir', 'Hadits', 'Fiqih', 'Aqidah', 'Sirah', 'Bahasa Arab', 'Nahwu Sharaf', 'Tasawuf', 'Ushul Fiqih', 'Tarikh']
            ],
            [
                'name' => 'Waqfeya',
                'url' => 'https://waqfeya.net/',
                'desc' => 'Perpustakaan digital berbahasa Arab',
                'collections' => '15,000+',
                'type' => 'Kitab',
                'color' => 'teal',
                'icon' => 'fa-book-quran',
                'subjects' => ['Tafsir', 'Hadits', 'Fiqih', 'Aqidah', 'Tarikh Islam', 'Adab', 'Lughah']
            ],
            [
                'name' => 'Perpustakaan Islam Digital',
                'url' => 'https://perpustakaanislamdigital.com/',
                'desc' => 'Koleksi buku-buku Islam digital Indonesia',
                'collections' => '500+',
                'type' => 'Buku',
                'color' => 'green',
                'icon' => 'fa-book-open',
                'subjects' => ['Fiqih', 'Akhlak', 'Sirah', 'Terjemah Kitab', 'Pendidikan Islam']
            ],
            [
                'name' => 'Rumah Fiqih',
                'url' => 'https://www.rumahfiqih.com/',
                'desc' => 'Kumpulan PDF buku-buku fiqih',
                'collections' => '200+',
                'type' => 'PDF',
                'color' => 'lime',
                'icon' => 'fa-balance-scale',
                'subjects' => ['Fiqih Ibadah', 'Fiqih Muamalah', 'Fiqih Munakahat', 'Fiqih Jinayah']
            ],
            [
                'name' => 'Open Library',
                'url' => 'https://openlibrary.org/',
                'desc' => 'Internet Archive open library',
                'collections' => '4M+',
                'type' => 'Books',
                'color' => 'blue',
                'icon' => 'fa-landmark',
                'subjects' => ['Fiction', 'Science', 'History', 'Philosophy', 'Arts', 'Technology', 'Medicine']
            ],
            [
                'name' => 'Harvard DASH',
                'url' => 'https://dash.harvard.edu/',
                'desc' => 'Digital Access to Scholarship at Harvard',
                'collections' => '100K+',
                'type' => 'Papers',
                'color' => 'red',
                'icon' => 'fa-university',
                'subjects' => ['Research Papers', 'Dissertations', 'Academic Articles', 'Science', 'Humanities']
            ],
            [
                'name' => 'NYU Arabic Collections',
                'url' => 'http://dlib.nyu.edu/aco/',
                'desc' => 'Arabic digital collections from NYU',
                'collections' => '10,000+',
                'type' => 'Items',
                'color' => 'violet',
                'icon' => 'fa-scroll',
                'subjects' => ['Arabic Literature', 'Islamic Studies', 'Middle Eastern History', 'Manuscripts']
            ],
            [
                'name' => 'ManyBooks',
                'url' => 'https://manybooks.net/',
                'desc' => 'Free e-books collection',
                'collections' => '50K+',
                'type' => 'E-Books',
                'color' => 'orange',
                'icon' => 'fa-books',
                'subjects' => ['Fiction', 'Non-Fiction', 'Classics', 'Romance', 'Mystery', 'Sci-Fi']
            ],
            [
                'name' => 'Noor-Book',
                'url' => 'https://www.noor-book.com/en/',
                'desc' => 'Digital library in multiple languages',
                'collections' => '200K+',
                'type' => 'Books',
                'color' => 'amber',
                'icon' => 'fa-sun',
                'subjects' => ['Arabic', 'English', 'Islamic', 'Literature', 'Science', 'History']
            ],
            [
                'name' => 'PDF Drive',
                'url' => 'https://www.pdfdrive.com/',
                'desc' => 'Search engine for PDF files',
                'collections' => '80M+',
                'type' => 'PDFs',
                'color' => 'rose',
                'icon' => 'fa-file-pdf',
                'subjects' => ['All Categories', 'Academic', 'Business', 'Technology', 'Self-Help']
            ],
            [
                'name' => 'PDF Books World',
                'url' => 'https://www.pdfbooksworld.com/',
                'desc' => 'Classic literature and academic books',
                'collections' => '5K+',
                'type' => 'PDFs',
                'color' => 'slate',
                'icon' => 'fa-book-bookmark',
                'subjects' => ['Classic Literature', 'Philosophy', 'Science', 'History', 'Biography']
            ],
        ];
        
        $colorClasses = [
            'emerald' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'border' => 'hover:border-emerald-300', 'badge' => 'bg-emerald-500'],
            'teal' => ['bg' => 'bg-teal-100', 'text' => 'text-teal-600', 'border' => 'hover:border-teal-300', 'badge' => 'bg-teal-500'],
            'green' => ['bg' => 'bg-green-100', 'text' => 'text-green-600', 'border' => 'hover:border-green-300', 'badge' => 'bg-green-500'],
            'lime' => ['bg' => 'bg-lime-100', 'text' => 'text-lime-600', 'border' => 'hover:border-lime-300', 'badge' => 'bg-lime-500'],
            'blue' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'border' => 'hover:border-blue-300', 'badge' => 'bg-blue-500'],
            'red' => ['bg' => 'bg-red-100', 'text' => 'text-red-600', 'border' => 'hover:border-red-300', 'badge' => 'bg-red-500'],
            'violet' => ['bg' => 'bg-violet-100', 'text' => 'text-violet-600', 'border' => 'hover:border-violet-300', 'badge' => 'bg-violet-500'],
            'orange' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-600', 'border' => 'hover:border-orange-300', 'badge' => 'bg-orange-500'],
            'amber' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'border' => 'hover:border-amber-300', 'badge' => 'bg-amber-500'],
            'rose' => ['bg' => 'bg-rose-100', 'text' => 'text-rose-600', 'border' => 'hover:border-rose-300', 'badge' => 'bg-rose-500'],
            'slate' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'border' => 'hover:border-slate-300', 'badge' => 'bg-slate-500'],
        ];
        @endphp

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8 lg:mb-10">
            @foreach($ebooks as $e)
            @php $c = $colorClasses[$e['color']]; @endphp
            <div x-data="{ open: false }" class="bg-white rounded-xl shadow-sm border border-gray-100 {{ $c['border'] }} hover:shadow-lg transition-all overflow-hidden group">
                {{-- Card Header --}}
                <div class="p-4">
                    <div class="flex items-start gap-3">
                        <div class="w-12 h-12 {{ $c['bg'] }} rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                            <i class="fas {{ $e['icon'] }} {{ $c['text'] }} text-lg"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-gray-900 text-sm group-hover:text-primary-600 transition">{{ $e['name'] }}</h4>
                            <p class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ $e['desc'] }}</p>
                            
                            {{-- Stats --}}
                            <div class="flex items-center gap-2 mt-2">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 {{ $c['badge'] }} text-white text-[10px] font-bold rounded-full">
                                    <i class="fas fa-layer-group text-[8px]"></i>
                                    {{ $e['collections'] }}
                                </span>
                                <span class="text-[10px] text-gray-400">{{ $e['type'] }}</span>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Action Buttons --}}
                    <div class="flex items-center gap-2 mt-3 pt-3 border-t border-gray-100">
                        <a href="{{ $e['url'] }}" target="_blank" class="flex-1 px-3 py-2 {{ $c['bg'] }} {{ $c['text'] }} text-xs font-semibold rounded-lg hover:opacity-80 transition text-center">
                            <i class="fas fa-external-link-alt mr-1"></i> {{ __('opac.additional_pages.journal_subscription.access') }}
                        </a>
                        <button @click="open = !open" class="px-3 py-2 bg-gray-100 text-gray-600 text-xs font-medium rounded-lg hover:bg-gray-200 transition">
                            <i class="fas fa-tags mr-1"></i> 
                            <span x-text="open ? '{{ __('opac.additional_pages.journal_subscription.close') }}' : '{{ __('opac.additional_pages.journal_subscription.category') }}'"></span>
                        </button>
                    </div>
                </div>
                
                {{-- Subjects Dropdown --}}
                <div x-show="open" x-collapse class="px-4 pb-4">
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-[10px] font-semibold text-gray-500 mb-2">{{ __('opac.additional_pages.journal_subscription.categories_subjects') }}</p>
                        <div class="flex flex-wrap gap-1">
                            @foreach($e['subjects'] as $subject)
                            <span class="px-2 py-0.5 bg-white text-gray-600 text-[10px] rounded border border-gray-200">{{ $subject }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>


        <!-- Database Berlangganan - Enhanced with Portal Link -->
        <div class="mb-6 p-5 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="flex-1">
                    <h3 class="text-lg font-bold flex items-center gap-2">
                        <i class="fas fa-database"></i>
                        {{ __('opac.additional_pages.journal_subscription.subscribed_databases') }}
                    </h3>
                    <p class="text-blue-200 text-sm mt-1">{{ __('opac.additional_pages.journal_subscription.subscribed_desc') }}</p>
                </div>
                <a href="{{ route('opac.database-access') }}" 
                   class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-white text-blue-700 font-bold rounded-xl hover:bg-blue-50 transition shadow-lg">
                    <i class="fas fa-key"></i>
                    {{ __('opac.additional_pages.journal_subscription.access_portal') }}
                    <i class="fas fa-arrow-right text-sm"></i>
                </a>
            </div>
        </div>

        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg flex items-center gap-2">
            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-database text-blue-600 text-sm"></i>
            </div>
            {{ __('opac.additional_pages.journal_subscription.subscribed_databases') }}
            <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-[10px] font-bold rounded-full ml-auto">
                <i class="fas fa-key mr-1"></i> {{ __('opac.additional_pages.journal_subscription.credentials') }}
            </span>
        </h3>
        
        @php
        $databases = [
            [
                'name' => 'Gale Academic (Teknik)',
                'url' => 'https://link.gale.com/apps/SPJ.SP01?u=idfpptij',
                'desc' => 'Jurnal teknik & sains dari Gale Academic OneFile',
                'collections' => '17K+',
                'type' => 'Journals',
                'color' => 'orange',
                'icon' => 'fa-cogs',
                'subjects' => ['Engineering', 'Computer Science', 'Physics', 'Chemistry', 'Mathematics', 'Technology']
            ],
            [
                'name' => 'Gale Academic (Humaniora)',
                'url' => 'https://link.gale.com/apps/SPJ.SP02?u=fpptijwt',
                'desc' => 'Referensi humaniora dan sosial',
                'collections' => '15K+',
                'type' => 'Journals',
                'color' => 'orange',
                'icon' => 'fa-users',
                'subjects' => ['Social Sciences', 'Humanities', 'Education', 'Psychology', 'History', 'Literature']
            ],
            [
                'name' => 'ProQuest',
                'url' => 'https://www.proquest.com/login',
                'desc' => 'Academic journals and dissertations',
                'collections' => '90K+',
                'type' => 'Journals',
                'color' => 'blue',
                'icon' => 'fa-journal-whills',
                'subjects' => ['Business', 'Economics', 'Health Sciences', 'Social Sciences', 'Dissertations', 'Theses']
            ],
        ];
        @endphp

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            @foreach($databases as $d)
            @php $c = $colorClasses[$d['color']]; @endphp
            <div x-data="{ open: false }" class="bg-white rounded-xl shadow-sm border border-gray-100 {{ $c['border'] }} hover:shadow-lg transition-all overflow-hidden group">
                {{-- Card Header --}}
                <div class="p-4">
                    <div class="flex items-start gap-3">
                        <div class="w-12 h-12 {{ $c['bg'] }} rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                            <i class="fas {{ $d['icon'] }} {{ $c['text'] }} text-lg"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <h4 class="font-bold text-gray-900 text-sm group-hover:text-primary-600 transition">{{ $d['name'] }}</h4>
                            </div>
                            <p class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ $d['desc'] }}</p>
                            
                            {{-- Stats --}}
                            <div class="flex items-center gap-2 mt-2">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 {{ $c['badge'] }} text-white text-[10px] font-bold rounded-full">
                                    <i class="fas fa-layer-group text-[8px]"></i>
                                    {{ $d['collections'] }}
                                </span>
                                <span class="text-[10px] text-gray-400">{{ $d['type'] }}</span>
                                <span class="text-[10px] text-amber-600 ml-auto"><i class="fas fa-lock"></i></span>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Action Buttons --}}
                    <div class="flex items-center gap-2 mt-3 pt-3 border-t border-gray-100">
                        <a href="{{ $d['url'] }}" target="_blank" class="flex-1 px-3 py-2 {{ $c['bg'] }} {{ $c['text'] }} text-xs font-semibold rounded-lg hover:opacity-80 transition text-center">
                            <i class="fas fa-external-link-alt mr-1"></i> {{ __('opac.additional_pages.journal_subscription.access') }}
                        </a>
                        <button @click="open = !open" class="px-3 py-2 bg-gray-100 text-gray-600 text-xs font-medium rounded-lg hover:bg-gray-200 transition">
                            <i class="fas fa-tags mr-1"></i> 
                            <span x-text="open ? '{{ __('opac.additional_pages.journal_subscription.close') }}' : '{{ __('opac.additional_pages.journal_subscription.category') }}'"></span>
                        </button>
                    </div>
                </div>
                
                {{-- Subjects Dropdown --}}
                <div x-show="open" x-collapse class="px-4 pb-4">
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-[10px] font-semibold text-gray-500 mb-2">{{ __('opac.additional_pages.journal_subscription.categories_subjects') }}</p>
                        <div class="flex flex-wrap gap-1">
                            @foreach($d['subjects'] as $subject)
                            <span class="px-2 py-0.5 bg-white text-gray-600 text-[10px] rounded border border-gray-200">{{ $subject }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Credentials -->
        <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-5 lg:p-6 text-white mb-8 lg:mb-10">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-10 h-10 bg-amber-500/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-shield-alt text-amber-400"></i>
                </div>
                <div>
                    <h3 class="font-bold">{{ __('opac.additional_pages.journal_subscription.credentials_title') }}</h3>
                    <p class="text-slate-400 text-xs">{{ __('opac.additional_pages.journal_subscription.credentials_desc') }}</p>
                </div>
            </div>
            
            <div class="grid sm:grid-cols-2 gap-4">
                <!-- Gale -->
                <div class="bg-white/10 backdrop-blur rounded-xl p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 bg-orange-500/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-database text-orange-400 text-sm"></i>
                        </div>
                        <h4 class="font-semibold text-amber-400">Gale Academic</h4>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center justify-between bg-black/20 rounded-lg px-3 py-2">
                            <span class="text-slate-400 text-xs">{{ __('opac.additional_pages.journal_subscription.username') }}</span>
                            <code class="text-emerald-400 font-mono text-sm">UnivKanB</code>
                        </div>
                        <div class="flex items-center justify-between bg-black/20 rounded-lg px-3 py-2">
                            <span class="text-slate-400 text-xs">{{ __('opac.additional_pages.journal_subscription.password') }}</span>
                            <code class="text-emerald-400 font-mono text-sm">FPPTIjatim@1</code>
                        </div>
                    </div>
                </div>

                <!-- ProQuest -->
                <div class="bg-white/10 backdrop-blur rounded-xl p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 bg-blue-500/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-journal-whills text-blue-400 text-sm"></i>
                        </div>
                        <h4 class="font-semibold text-amber-400">ProQuest</h4>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center justify-between bg-black/20 rounded-lg px-3 py-2">
                            <span class="text-slate-400 text-xs">{{ __('opac.additional_pages.journal_subscription.username') }}</span>
                            <code class="text-emerald-400 font-mono text-sm">UDarussalam</code>
                        </div>
                        <div class="flex items-center justify-between bg-black/20 rounded-lg px-3 py-2">
                            <span class="text-slate-400 text-xs">{{ __('opac.additional_pages.journal_subscription.password') }}</span>
                            <code class="text-emerald-400 font-mono text-sm">FPPTIjatim@1</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Perpusnas -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg flex items-center gap-2">
            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-landmark text-purple-600 text-sm"></i>
            </div>
            {{ __('opac.additional_pages.journal_subscription.national_library') }}
            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded-full ml-auto">
                <i class="fas fa-check mr-1"></i> {{ __('opac.additional_pages.journal_subscription.free') }}
            </span>
        </h3>
        
        <div class="grid sm:grid-cols-2 gap-4 mb-8">
            <a href="https://ipusnas.id" target="_blank" class="flex items-center gap-4 bg-white rounded-xl p-4 lg:p-5 shadow-sm border border-gray-100 hover:border-blue-300 hover:shadow-lg transition group">
                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform">
                    <i class="fas fa-book-reader text-white text-xl"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-bold text-gray-900 group-hover:text-primary-600 transition">iPusnas</h4>
                    <p class="text-xs text-gray-500 mt-0.5">{{ __('opac.additional_pages.journal_subscription.digital_perpusnas') }}</p>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-[10px] font-bold rounded-full">500K+ E-Books</span>
                        <span class="text-[10px] text-emerald-600"><i class="fas fa-id-card mr-1"></i> {{ __('opac.additional_pages.journal_subscription.free_for_wni') }}</span>
                    </div>
                </div>
                <i class="fas fa-chevron-right text-gray-300 group-hover:text-blue-500 group-hover:translate-x-1 transition-all"></i>
            </a>

            <a href="https://bfrpn.perpusnas.go.id" target="_blank" class="flex items-center gap-4 bg-white rounded-xl p-4 lg:p-5 shadow-sm border border-gray-100 hover:border-green-300 hover:shadow-lg transition group">
                <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-green-500/30 group-hover:scale-110 transition-transform">
                    <i class="fas fa-star text-white text-xl"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-bold text-gray-900 group-hover:text-primary-600 transition">Bintang Pusnas</h4>
                    <p class="text-xs text-gray-500 mt-0.5">{{ __('opac.additional_pages.journal_subscription.pustaka_perpusnas') }}</p>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-bold rounded-full">{{ __('opac.additional_pages.journal_subscription.rare_collection') }}</span>
                        <span class="text-[10px] text-emerald-600"><i class="fas fa-check-circle mr-1"></i> {{ __('opac.additional_pages.journal_subscription.open_access') }}</span>
                    </div>
                </div>
                <i class="fas fa-chevron-right text-gray-300 group-hover:text-green-500 group-hover:translate-x-1 transition-all"></i>
            </a>
        </div>

        <!-- Tips -->
        <div class="bg-gradient-to-br from-primary-50 to-blue-50 rounded-xl p-4 lg:p-5 border border-primary-100">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-primary-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-lightbulb text-primary-600"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 text-sm mb-2">{{ __('opac.additional_pages.journal_subscription.tips_title') }}</h4>
                    <ul class="text-xs text-gray-600 space-y-1.5">
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle text-emerald-500 mt-0.5"></i>
                            <span>{{ __('opac.additional_pages.journal_subscription.tip_1') }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle text-emerald-500 mt-0.5"></i>
                            <span>{{ __('opac.additional_pages.journal_subscription.tip_2') }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle text-emerald-500 mt-0.5"></i>
                            <span>{{ __('opac.additional_pages.journal_subscription.tip_3') }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle text-emerald-500 mt-0.5"></i>
                            <span>{{ __('opac.additional_pages.journal_subscription.tip_4') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</x-opac.layout>
