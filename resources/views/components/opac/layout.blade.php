<!DOCTYPE html>
<html lang="{{ $locale ?? 'id' }}" dir="{{ $textDirection ?? 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'OPAC' }} - Perpustakaan UNIDA Gontor</title>
    
    {{-- Preconnect to CDNs for faster loading --}}
    <link rel="preconnect" href="https://cdn.tailwindcss.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    
    {{-- Critical CSS - prevents FOUC --}}
    <style>
        html, body { margin: 0; padding: 0; background: #eff6ff; }
        body { opacity: 0; }
        body.ready { opacity: 1; transition: opacity 0.15s; }
    </style>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50: '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe', 300: '#93c5fd', 400: '#60a5fa', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8', 800: '#1e40af', 900: '#1e3a8a' },
                        accent: { 400: '#fbbf24', 500: '#f59e0b', 600: '#d97706' }
                    }
                }
            }
        }
        // Show body when Tailwind is ready
        document.addEventListener('DOMContentLoaded', function() {
            document.body.classList.add('ready');
        });
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .pattern-bg {
            background-color: #eff6ff;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%233b82f6' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        /* Mega Menu with bridge */
        .menu-item { position: relative; }
        .menu-item .mega-menu { 
            display: none; 
            position: absolute;
            top: 100%;
            padding-top: 8px; /* Bridge/gap filler */
        }
        .menu-item .mega-menu::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 8px;
            background: transparent;
        }
        .menu-item:hover .mega-menu { display: block; }
        /* Dropdown Menu with bridge */
        .dropdown-item { position: relative; }
        .dropdown-item .dropdown-menu { 
            display: none;
            position: absolute;
            top: 100%;
            padding-top: 8px;
        }
        .dropdown-item .dropdown-menu::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 8px;
            background: transparent;
        }
        .dropdown-item:hover .dropdown-menu { display: block; }
        .sidebar-overlay { background: rgba(0,0,0,0.5); }
        [x-cloak] { display: none !important; }
        
        /* RTL Support for Mega Menu - Center below nav */
        [dir="rtl"] .mega-menu {
            left: 50% !important;
            right: auto !important;
            transform: translateX(-50%) !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
            padding-top: 16px !important; /* Larger bridge for RTL */
        }
        [dir="rtl"] .mega-menu::before {
            height: 16px !important; /* Match padding-top */
        }
        [dir="rtl"] .menu-item {
            position: relative !important; /* Keep relative for hover detection */
        }
        /* Keep direction for flex children */
        [dir="rtl"] .mega-menu .flex {
            direction: rtl;
        }
        /* Fix input autofill background */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus,
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px white inset !important;
            box-shadow: 0 0 0 30px white inset !important;
            -webkit-text-fill-color: #374151 !important;
        }
        /* Remove default input styling */
        input[type="text"],
        input[type="search"] {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            box-shadow: none !important;
        }
    </style>
    
    {{-- Google Analytics GA4 --}}
    @php
        $gaEnabled = \App\Models\Setting::get('ga_enabled', false);
        $gaMeasurementId = \App\Models\Setting::get('ga_measurement_id', '');
    @endphp
    @if($gaEnabled && $gaMeasurementId)
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaMeasurementId }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ $gaMeasurementId }}');
    </script>
    @endif
    
    @livewireStyles
</head>
<body class="pattern-bg min-h-screen" x-data="{ sidebarOpen: false, searchOpen: false }">

    <!-- Top Bar -->
    <div class="bg-primary-800 text-white text-xs hidden lg:block">
        <div class="max-w-7xl mx-auto px-4 py-2 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="tel:085183053934" class="flex items-center gap-1 hover:text-primary-200">
                    <i class="fas fa-phone"></i> 0851-8305-3934
                </a>
                <a href="mailto:library@unida.gontor.ac.id" class="flex items-center gap-1 hover:text-primary-200">
                    <i class="fas fa-envelope"></i> library@unida.gontor.ac.id
                </a>
            </div>
            <div class="flex items-center gap-4">
                {{-- Language Switcher --}}
                <x-opac.language-switcher />
                
                <span class="flex items-center gap-1">
                    <i class="fas fa-id-card"></i> {{ __('opac.npp') }}: 350210D2014338
                </span>
                <div class="flex items-center gap-2">
                    <a href="https://youtube.com/@perpustakaanunidagontor" target="_blank" class="hover:text-red-400"><i class="fab fa-youtube"></i></a>
                    <a href="https://instagram.com/perpustakaanunida" target="_blank" class="hover:text-pink-400"><i class="fab fa-instagram"></i></a>
                    <a href="https://tiktok.com/@perpustakaanunida" target="_blank" class="hover:text-primary-200"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="bg-gradient-to-r from-primary-600 to-primary-800 sticky top-0 z-50 shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <a href="{{ route('opac.home') }}" class="flex items-center lg:flex-none flex-1 lg:flex-initial justify-center lg:justify-start">
                    <img src="{{ url('storage/logo.png') }}" alt="UNIDA Library" class="h-10 w-auto">
                </a>

                <!-- Desktop Navigation -->
                <nav class="hidden lg:flex items-center gap-1">
                    <!-- HOME Menu -->
                    <div class="menu-item">
                        <button class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white hover:bg-white/10 rounded-lg transition">
                            <i class="fas fa-home text-xs"></i> {{ __('opac.menu.home') }} <i class="fas fa-chevron-down text-[10px] ml-1"></i>
                        </button>
                        <div class="mega-menu left-0 w-[700px] -ml-20">
                            <div class="bg-white rounded-xl shadow-2xl p-6">
                            <div class="grid grid-cols-3 gap-6">
                                <!-- Profil -->
                                <div>
                                    <h3 class="font-bold text-gray-900 mb-3 text-sm">{{ __('opac.home_menu.profile') }}</h3>
                                    <div class="space-y-2">
                                        <a href="{{ route('opac.page', 'visi-misi') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                            <div class="w-9 h-9 bg-primary-100 rounded-lg flex items-center justify-center"><i class="fas fa-bullseye text-primary-600"></i></div>
                                            <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">{{ __('opac.home_menu.vision_mission') }}</p><p class="text-xs text-gray-500">{{ __('opac.tagline') }}</p></div>
                                        </a>
                                        <a href="{{ route('opac.page', 'sejarah') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                            <div class="w-9 h-9 bg-amber-100 rounded-lg flex items-center justify-center"><i class="fas fa-landmark text-amber-600"></i></div>
                                            <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">{{ __('opac.home_menu.history') }}</p><p class="text-xs text-gray-500">{{ __('opac.home_menu.history') }}</p></div>
                                        </a>
                                        <a href="{{ route('opac.page', 'struktur-organisasi') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                            <div class="w-9 h-9 bg-emerald-100 rounded-lg flex items-center justify-center"><i class="fas fa-sitemap text-emerald-600"></i></div>
                                            <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">{{ __('opac.home_menu.organization') }}</p><p class="text-xs text-gray-500">{{ __('opac.home_menu.organization') }}</p></div>
                                        </a>
                                        <a href="{{ route('opac.page', 'mou') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                            <div class="w-9 h-9 bg-purple-100 rounded-lg flex items-center justify-center"><i class="fas fa-handshake text-purple-600"></i></div>
                                            <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">{{ __('opac.home_menu.mou') }}</p><p class="text-xs text-gray-500">{{ __('opac.home_menu.mou') }}</p></div>
                                        </a>
                                    </div>
                                </div>
                                <!-- Guidelines -->
                                <div>
                                    <h3 class="font-bold text-gray-900 mb-3 text-sm">{{ __('opac.home_menu.guidelines') }}</h3>
                                    <div class="space-y-2">
                                        <a href="{{ route('opac.page', 'tata-tertib') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                            <div class="w-9 h-9 bg-red-100 rounded-lg flex items-center justify-center"><i class="fas fa-clipboard-list text-red-600"></i></div>
                                            <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">{{ __('opac.home_menu.rules') }}</p><p class="text-xs text-gray-500">{{ __('opac.home_menu.rules') }}</p></div>
                                        </a>
                                        <a href="{{ route('opac.page', 'jam-layanan') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                            <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center"><i class="fas fa-clock text-green-600"></i></div>
                                            <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">{{ __('opac.home_menu.hours') }}</p><p class="text-xs text-gray-500">{{ __('opac.home_menu.hours') }}</p></div>
                                        </a>
                                        <a href="{{ route('opac.page', 'fasilitas') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                            <div class="w-9 h-9 bg-cyan-100 rounded-lg flex items-center justify-center"><i class="fas fa-building text-cyan-600"></i></div>
                                            <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">{{ __('opac.home_menu.facilities') }}</p><p class="text-xs text-gray-500">{{ __('opac.home_menu.facilities') }}</p></div>
                                        </a>
                                        <a href="{{ route('opac.page', 'karir') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                            <div class="w-9 h-9 bg-orange-100 rounded-lg flex items-center justify-center"><i class="fas fa-user-tie text-orange-600"></i></div>
                                            <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">{{ __('opac.home_menu.career') }}</p><p class="text-xs text-gray-500">{{ __('opac.home_menu.career') }}</p></div>
                                        </a>
                                    </div>
                                </div>
                                <!-- Video -->
                                <div>
                                    <h3 class="font-bold text-gray-900 mb-3 text-sm">{{ __('opac.home_menu.video_profile') }}</h3>
                                    <div class="aspect-video bg-gray-100 rounded-xl overflow-hidden">
                                        <iframe class="w-full h-full" src="https://www.youtube.com/embed/YOUR_VIDEO_ID" frameborder="0" allowfullscreen></iframe>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2 text-center">{{ __('opac.home_menu.video_profile') }}</p>
                                </div>
                            </div>
                        </div></div>
                    </div>

                    <!-- E-RESOURCES Menu - Restructured -->
                    <div class="menu-item">
                        <button class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white hover:bg-white/10 rounded-lg transition">
                            <i class="fas fa-layer-group text-xs"></i> {{ __('opac.menu.collection') }} <i class="fas fa-chevron-down text-[10px] ml-1"></i>
                        </button>
                        <div class="mega-menu left-0 w-[920px] -ml-40">
                            <div class="bg-white rounded-xl shadow-2xl overflow-hidden">
                                {{-- Header --}}
                                <div class="bg-gradient-to-r from-primary-600 to-blue-600 px-6 py-4 flex items-center justify-between">
                                    <div class="flex items-center gap-3 text-white">
                                        <i class="fas fa-search text-lg"></i>
                                        <span class="font-bold text-base">{{ __('opac.collection_menu.browse') }}</span>
                                    </div>
                                    <a href="{{ route('opac.search') }}" class="text-sm text-blue-200 hover:text-white transition">
                                        {{ __('opac.collection_menu.search_global') }} <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                </div>
                                
                                <div class="p-6">
                                    <div class="grid gap-5" style="grid-template-columns: 150px 1fr 1fr 1fr;">
                                        
                                        {{-- Column 1: Cari di Global Search (narrow) --}}
                                        <div class="flex flex-col">
                                            <h3 class="font-bold text-gray-900 mb-4 text-sm uppercase tracking-wide flex items-center gap-2">
                                                <span class="w-6 h-6 bg-primary-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                    <i class="fas fa-search text-primary-600 text-xs"></i>
                                                </span>
                                                <span>{{ __('opac.collection_menu.find') }}</span>
                                            </h3>
                                            <div class="space-y-1 flex-1">
                                                <a href="{{ route('opac.search') }}?type=book" class="flex items-center gap-2 px-2 py-2 rounded-lg hover:bg-gray-50 group">
                                                    <div class="w-7 h-7 bg-primary-100 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fas fa-book text-primary-600 text-xs"></i></div>
                                                    <span class="text-sm font-medium text-gray-800 group-hover:text-primary-600">{{ __('opac.collection_menu.book') }}</span>
                                                </a>
                                                <a href="{{ route('opac.search') }}?type=ebook" class="flex items-center gap-2 px-2 py-2 rounded-lg hover:bg-gray-50 group">
                                                    <div class="w-7 h-7 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fas fa-file-pdf text-orange-600 text-xs"></i></div>
                                                    <span class="text-sm font-medium text-gray-800 group-hover:text-primary-600">{{ __('opac.collection_menu.ebook') }}</span>
                                                </a>
                                                <a href="{{ route('opac.search') }}?type=ethesis" class="flex items-center gap-2 px-2 py-2 rounded-lg hover:bg-gray-50 group">
                                                    <div class="w-7 h-7 bg-pink-100 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fas fa-graduation-cap text-pink-600 text-xs"></i></div>
                                                    <span class="text-sm font-medium text-gray-800 group-hover:text-primary-600">{{ __('opac.collection_menu.ethesis') }}</span>
                                                </a>
                                                <a href="{{ route('opac.search') }}?type=shamela" class="flex items-center gap-2 px-2 py-2 rounded-lg hover:bg-gray-50 group">
                                                    <div class="w-7 h-7 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fas fa-book-quran text-emerald-600 text-xs"></i></div>
                                                    <span class="text-sm font-medium text-gray-800 group-hover:text-primary-600">{{ __('opac.collection_menu.shamela') }}</span>
                                                </a>
                                                <a href="{{ route('opac.search') }}?type=external" class="flex items-center gap-2 px-2 py-2 rounded-lg hover:bg-gray-50 group">
                                                    <div class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fas fa-globe text-blue-600 text-xs"></i></div>
                                                    <span class="text-sm font-medium text-gray-800 group-hover:text-primary-600">{{ __('opac.collection_menu.external') }}</span>
                                                </a>
                                            </div>
                                        </div>
                                        
                                        {{-- Column 2: Koleksi Khusus (Premium) --}}
                                        <div class="flex flex-col">
                                            <h3 class="font-bold text-gray-900 mb-4 text-sm uppercase tracking-wide flex items-center gap-2">
                                                <span class="w-6 h-6 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                    <i class="fas fa-crown text-amber-600 text-xs"></i>
                                                </span>
                                                <span>{{ __('opac.collection_menu.special') }}</span>
                                            </h3>
                                            <div class="space-y-2 flex-1">
                                                <a href="{{ route('opac.shamela.index') }}" class="block p-3 rounded-xl bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-100 hover:shadow-md transition">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fas fa-book-quran text-white text-base"></i></div>
                                                        <div class="min-w-0">
                                                            <p class="text-sm font-bold text-gray-900 whitespace-nowrap">{{ __('opac.collection_menu.maktabah_shamela') }}</p>
                                                            <p class="text-xs text-gray-500 whitespace-nowrap">{{ __('opac.collection_menu.shamela_desc') }}</p>
                                                        </div>
                                                    </div>
                                                </a>
                                                <a href="{{ route('opac.universitaria.index') }}" class="block p-3 rounded-xl bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-100 hover:shadow-md transition">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-600 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fas fa-landmark text-white text-base"></i></div>
                                                        <div class="min-w-0">
                                                            <p class="text-sm font-bold text-gray-900 whitespace-nowrap">{{ __('opac.collection_menu.universitaria') }}</p>
                                                            <p class="text-xs text-gray-500 whitespace-nowrap">{{ __('opac.collection_menu.universitaria_desc') }}</p>
                                                        </div>
                                                    </div>
                                                </a>
                                                <a href="{{ route('opac.page', 'digilib-apps') }}" class="block p-3 rounded-xl bg-gray-50 border border-gray-100 hover:shadow-md transition">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fas fa-mobile-alt text-teal-600 text-base"></i></div>
                                                        <div class="min-w-0">
                                                            <p class="text-sm font-bold text-gray-900">{{ __('opac.collection_menu.digilib') }}</p>
                                                            <p class="text-xs text-gray-500">{{ __('opac.collection_menu.digilib_desc') }}</p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        
                                        {{-- Column 3: Jurnal Ilmiah --}}
                                        <div class="flex flex-col">
                                            <h3 class="font-bold text-gray-900 mb-4 text-sm uppercase tracking-wide flex items-center gap-2">
                                                <span class="w-6 h-6 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                    <i class="fas fa-journal-whills text-red-600 text-xs"></i>
                                                </span>
                                                <span>{{ __('opac.collection_menu.journal') }}</span>
                                            </h3>
                                            <div class="space-y-3 flex-1">
                                                {{-- Jurnal UNIDA --}}
                                                <div class="p-3 bg-gray-50 rounded-xl">
                                                    <p class="text-xs font-bold text-gray-500 mb-2">{{ __('opac.collection_menu.ejournal') }}</p>
                                                    <a href="{{ route('opac.journals.index') }}" class="flex items-center gap-2 py-1.5 rounded hover:bg-white group">
                                                        <i class="fas fa-newspaper text-red-500 text-sm"></i>
                                                        <span class="text-sm text-gray-700 group-hover:text-primary-600">{{ __('opac.collection_menu.ejournal') }}</span>
                                                    </a>
                                                    <a href="https://ejournal.unida.gontor.ac.id" target="_blank" class="flex items-center gap-2 py-1.5 rounded hover:bg-white group">
                                                        <i class="fas fa-external-link-alt text-gray-400 text-sm"></i>
                                                        <span class="text-sm text-gray-700 group-hover:text-primary-600">{{ __('opac.collection_menu.ojs_portal') }}</span>
                                                    </a>
                                                </div>
                                                {{-- Jurnal Konsorsium --}}
                                                <div class="p-3 bg-gradient-to-br from-indigo-50 to-blue-50 rounded-xl border border-indigo-100">
                                                    <p class="text-xs font-bold text-indigo-600 mb-2 flex items-center gap-1">
                                                        <i class="fas fa-key text-xs"></i> {{ __('opac.collection_menu.consortium') }}
                                                    </p>
                                                    <a href="{{ route('opac.database-access') }}" class="flex items-center gap-2 py-1.5 rounded hover:bg-white group">
                                                        <i class="fas fa-database text-indigo-500 text-sm"></i>
                                                        <span class="text-sm text-gray-700 group-hover:text-primary-600">{{ __('opac.collection_menu.gale') }}</span>
                                                        <span class="ml-auto px-1.5 py-0.5 bg-amber-100 text-amber-700 text-[10px] font-bold rounded">120K+</span>
                                                    </a>
                                                    <a href="{{ route('opac.database-access') }}" class="flex items-center gap-2 py-1.5 rounded hover:bg-white group">
                                                        <i class="fas fa-database text-blue-500 text-sm"></i>
                                                        <span class="text-sm text-gray-700 group-hover:text-primary-600">{{ __('opac.collection_menu.proquest') }}</span>
                                                        <span class="ml-auto px-1.5 py-0.5 bg-amber-100 text-amber-700 text-[10px] font-bold rounded">90K+</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        {{-- Column 4: Sumber Terbuka --}}
                                        <div class="flex flex-col">
                                            <h3 class="font-bold text-gray-900 mb-4 text-sm uppercase tracking-wide flex items-center gap-2">
                                                <span class="w-6 h-6 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                    <i class="fas fa-unlock text-green-600 text-xs"></i>
                                                </span>
                                                <span>{{ __('opac.collection_menu.open_access') }}</span>
                                            </h3>
                                            <div class="space-y-1">
                                                <a href="https://repo.unida.gontor.ac.id" target="_blank" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-50 group">
                                                    <i class="fas fa-database text-indigo-500 text-sm w-5"></i>
                                                    <span class="text-sm text-gray-700 group-hover:text-primary-600">{{ __('opac.collection_menu.repository') }}</span>
                                                    <i class="fas fa-external-link-alt text-gray-300 text-xs ml-auto"></i>
                                                </a>
                                                <a href="https://ipusnas.id" target="_blank" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-50 group">
                                                    <i class="fas fa-book-reader text-blue-500 text-sm w-5"></i>
                                                    <span class="text-sm text-gray-700 group-hover:text-primary-600">{{ __('opac.collection_menu.ipusnas') }}</span>
                                                    <i class="fas fa-external-link-alt text-gray-300 text-xs ml-auto"></i>
                                                </a>
                                                <a href="https://openlibrary.org" target="_blank" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-50 group">
                                                    <i class="fas fa-globe text-teal-500 text-sm w-5"></i>
                                                    <span class="text-sm text-gray-700 group-hover:text-primary-600">{{ __('opac.collection_menu.openlibrary') }}</span>
                                                    <i class="fas fa-external-link-alt text-gray-300 text-xs ml-auto"></i>
                                                </a>
                                                <a href="https://www.pdfdrive.com" target="_blank" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-50 group">
                                                    <i class="fas fa-file-pdf text-rose-500 text-sm w-5"></i>
                                                    <span class="text-sm text-gray-700 group-hover:text-primary-600">{{ __('opac.collection_menu.pdfdrive') }}</span>
                                                    <i class="fas fa-external-link-alt text-gray-300 text-xs ml-auto"></i>
                                                </a>
                                            </div>
                                            {{-- Premium CTA --}}
                                            <a href="{{ route('opac.page', 'e-resources') }}" class="mt-3 group relative overflow-hidden flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-primary-600 via-blue-600 to-indigo-600 text-white text-sm font-bold rounded-xl shadow-lg hover:shadow-xl hover:scale-[1.02] transition-all">
                                                <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                                                <i class="fas fa-th-large text-sm"></i>
                                                <span>{{ __('opac.collection_menu.all_eresources') }}</span>
                                                <i class="fas fa-arrow-right text-xs opacity-70 group-hover:translate-x-1 transition-transform"></i>
                                            </a>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DISCOVER Menu -->
                    <div class="menu-item">
                        <button class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white hover:bg-white/10 rounded-lg transition">
                            <i class="fas fa-compass text-xs"></i> {{ __('opac.menu.discover') }} <i class="fas fa-chevron-down text-[10px] ml-1"></i>
                        </button>
                        <div class="mega-menu left-0 w-[700px] -ml-48">
                            <div class="bg-white rounded-xl shadow-2xl p-6">
                                <div class="grid grid-cols-3 gap-6">
                                <!-- New Experience -->
                                <div>
                                    <h3 class="font-bold text-gray-900 mb-3 text-sm">{{ __('opac.discover_menu.new_experience') }}</h3>
                                    <div class="space-y-2">
                                        <a href="{{ route('opac.page', 'event-library') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                            <div class="w-9 h-9 bg-pink-100 rounded-lg flex items-center justify-center"><i class="fas fa-calendar-star text-pink-600"></i></div>
                                            <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">{{ __('opac.discover_menu.event_library') }}</p><p class="text-xs text-gray-500">{{ __('opac.discover_menu.event_library') }}</p></div>
                                        </a>
                                        <a href="{{ route('opac.page', 'virtual-tour') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                            <div class="w-9 h-9 bg-cyan-100 rounded-lg flex items-center justify-center"><i class="fas fa-vr-cardboard text-cyan-600"></i></div>
                                            <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">{{ __('opac.discover_menu.virtual_tour') }}</p><p class="text-xs text-gray-500">{{ __('opac.discover_menu.virtual_tour') }}</p></div>
                                        </a>
                                        <a href="{{ route('opac.page', 'e-learning') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                            <div class="w-9 h-9 bg-violet-100 rounded-lg flex items-center justify-center"><i class="fas fa-laptop text-violet-600"></i></div>
                                            <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">{{ __('opac.discover_menu.elearning') }}</p><p class="text-xs text-gray-500">{{ __('opac.discover_menu.elearning') }}</p></div>
                                        </a>
                                    </div>
                                </div>
                                <!-- Explore More -->
                                <div>
                                    <h3 class="font-bold text-gray-900 mb-3 text-sm">{{ __('opac.discover_menu.explore') }}</h3>
                                    <div class="space-y-2">
                                        <a href="{{ route('opac.page', 'prosa-kreatif') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                            <div class="w-9 h-9 bg-rose-100 rounded-lg flex items-center justify-center"><i class="fas fa-pen-fancy text-rose-600"></i></div>
                                            <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">{{ __('opac.discover_menu.creative_prose') }}</p><p class="text-xs text-gray-500">{{ __('opac.discover_menu.creative_prose') }}</p></div>
                                        </a>
                                        <a href="{{ route('opac.page', 'survey') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                            <div class="w-9 h-9 bg-lime-100 rounded-lg flex items-center justify-center"><i class="fas fa-poll text-lime-600"></i></div>
                                            <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">{{ __('opac.discover_menu.survey') }}</p><p class="text-xs text-gray-500">{{ __('opac.discover_menu.survey') }}</p></div>
                                        </a>
                                        <a href="{{ route('opac.page', 'research-tools') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                            <div class="w-9 h-9 bg-amber-100 rounded-lg flex items-center justify-center"><i class="fas fa-microscope text-amber-600"></i></div>
                                            <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">{{ __('opac.discover_menu.research_tools') }}</p><p class="text-xs text-gray-500">{{ __('opac.discover_menu.research_tools') }}</p></div>
                                        </a>
                                    </div>
                                </div>
                                <!-- Book Recommendation -->
                                <div>
                                    <h3 class="font-bold text-gray-900 mb-3 text-sm">{{ __('opac.discover_menu.recommendation') }}</h3>
                                    <a href="{{ route('opac.search') . '?type=book' }}?sort=latest" class="block bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl p-4 hover:shadow-lg transition">
                                        <div class="flex gap-3">
                                            <div class="w-16 h-20 bg-gradient-to-br from-primary-400 to-primary-600 rounded-lg flex items-center justify-center shadow-lg">
                                                <i class="fas fa-book text-white text-xl"></i>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-xs text-gray-500">{{ __('opac.discover_menu.latest_collection') }}</p>
                                                <p class="font-bold text-gray-900 text-sm">{{ __('opac.discover_menu.view_new_books') }}</p>
                                                <span class="inline-block mt-2 px-2 py-0.5 bg-accent-400 text-xs font-medium rounded">{{ __('opac.new') }}</span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div></div>
                    </div>

                    <!-- GUIDE Menu -->
                    <div class="menu-item">
                        <button class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white hover:bg-white/10 rounded-lg transition">
                            <i class="fas fa-book-open text-xs"></i> {{ __('opac.menu.guide') }} <i class="fas fa-chevron-down text-[10px] ml-1"></i>
                        </button>
                        <div class="mega-menu left-0 w-[550px] -ml-48">
                            <div class="bg-white rounded-xl shadow-2xl p-6">
                                <div class="grid grid-cols-2 gap-6">
                                    <!-- Panduan Pengguna -->
                                    <div>
                                        <h3 class="font-bold text-gray-900 mb-3 text-sm">{{ __('opac.guide_menu.user_guide') }}</h3>
                                        <div class="space-y-2">
                                            <a href="{{ route('opac.page', 'panduan-opac') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                                <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center"><i class="fas fa-search text-blue-600"></i></div>
                                                <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">{{ __('opac.guide_menu.opac_guide') }}</p><p class="text-xs text-gray-500">{{ __('opac.guide_menu.opac_guide_desc') }}</p></div>
                                            </a>
                                            <a href="{{ route('opac.panduan.thesis') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                                <div class="w-9 h-9 bg-emerald-100 rounded-lg flex items-center justify-center"><i class="fas fa-upload text-emerald-600"></i></div>
                                                <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">{{ __('opac.guide_menu.thesis_upload') }}</p><p class="text-xs text-gray-500">{{ __('opac.guide_menu.thesis_upload_desc') }}</p></div>
                                            </a>
                                            <a href="{{ route('opac.panduan.plagiarism') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                                <div class="w-9 h-9 bg-teal-100 rounded-lg flex items-center justify-center"><i class="fas fa-shield-alt text-teal-600"></i></div>
                                                <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">{{ __('opac.guide_menu.plagiarism') }}</p><p class="text-xs text-gray-500">{{ __('opac.guide_menu.plagiarism_desc') }}</p></div>
                                            </a>
                                            <a href="{{ route('opac.page', 'panduan-ospek') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                                <div class="w-9 h-9 bg-orange-100 rounded-lg flex items-center justify-center"><i class="fas fa-user-graduate text-orange-600"></i></div>
                                                <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">{{ __('opac.guide_menu.ospek_guide') }}</p><p class="text-xs text-gray-500">{{ __('opac.guide_menu.ospek_guide_desc') }}</p></div>
                                            </a>
                                        </div>
                                    </div>
                                    <!-- Materi & Akademik -->
                                    <div>
                                        <h3 class="font-bold text-gray-900 mb-3 text-sm">{{ __('opac.guide_menu.academic') }}</h3>
                                        <div class="space-y-2">
                                            <a href="{{ route('opac.page', 'panduan-akademik') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                                <div class="w-9 h-9 bg-purple-100 rounded-lg flex items-center justify-center"><i class="fas fa-graduation-cap text-purple-600"></i></div>
                                                <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">{{ __('opac.guide_menu.academic_guide') }}</p><p class="text-xs text-gray-500">{{ __('opac.guide_menu.academic_guide_desc') }}</p></div>
                                            </a>
                                            <a href="{{ route('opac.page', 'materi-perpustakaan') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                                <div class="w-9 h-9 bg-pink-100 rounded-lg flex items-center justify-center"><i class="fas fa-chalkboard-teacher text-pink-600"></i></div>
                                                <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">{{ __('opac.guide_menu.library_materials') }}</p><p class="text-xs text-gray-500">{{ __('opac.guide_menu.library_materials_desc') }}</p></div>
                                            </a>
                                            <a href="{{ route('opac.page', 'download-eddc') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                                <div class="w-9 h-9 bg-indigo-100 rounded-lg flex items-center justify-center"><i class="fas fa-download text-indigo-600"></i></div>
                                                <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">{{ __('opac.guide_menu.download_eddc') }}</p><p class="text-xs text-gray-500">{{ __('opac.guide_menu.download_eddc_desc') }}</p></div>
                                            </a>
                                        </div>
                                        <div class="mt-3 bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl p-3 text-white">
                                            <p class="text-xs text-primary-100 mb-2">{{ __('opac.guide_menu.need_help') }}</p>
                                            <a href="https://wa.me/6285183053934" target="_blank" class="text-xs font-medium hover:underline flex items-center gap-1">
                                                <i class="fab fa-whatsapp"></i> {{ __('opac.guide_menu.chat_whatsapp') }} →
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- NEWS Link -->
                    <a href="{{ route('opac.news.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white hover:bg-white/10 rounded-lg transition">
                        <i class="fas fa-newspaper text-xs"></i> {{ __('opac.menu.news') }}
                    </a>
                </nav>

                <!-- Right Side -->
                <div class="flex items-center gap-2">
                    <!-- Search Button with Card Style -->
                    <button 
                        @click="searchOpen = true" 
                        class="hidden lg:flex items-center gap-2 px-3 py-1 text-white bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-xl text-sm font-medium transition shadow-sm h-10 border border-white/10"
                    >
                        <i class="fas fa-search"></i>
                        <span>{{ __('opac.search') }}</span>
                    </button>
                    
                    <!-- Auth Buttons Desktop -->
                    @auth('member')
                        <div class="hidden lg:block relative" x-data="{ openProfile: false }">
                            <!-- Trigger Button -->
                            <button @click="openProfile = !openProfile" @click.away="openProfile = false" class="flex items-center gap-2.5 px-1.5 py-1 pr-3 text-left text-white bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-xl shadow-sm transition group border border-white/10 h-10">
                                <div class="w-7 h-7 bg-gradient-to-br from-white/20 to-white/5 rounded-lg flex items-center justify-center border border-white/20 text-white shadow-inner overflow-hidden shrink-0">
                                     @if(auth('member')->user()->photo)
                                        <img src="{{ asset('storage/' . auth('member')->user()->photo) }}" class="w-full h-full object-cover">
                                     @else
                                        <i class="fas fa-user text-[10px]"></i>
                                     @endif
                                </div>
                                <div class="leading-none flex flex-col justify-center">
                                    <p class="text-[9px] font-medium text-primary-200 -mb-0.5">{{ __('opac.auth.greeting') }},</p>
                                    <p class="text-xs font-bold text-white max-w-[100px] truncate">{{ Str::words(auth('member')->user()->name, 2, '') }}</p>
                                </div>
                                <i class="fas fa-chevron-down text-[10px] text-primary-200 group-hover:text-white transition-transform duration-200 ml-0.5" :class="openProfile ? 'rotate-180' : ''"></i>
                            </button>

                            <!-- Dropdown Modal -->
                            <div x-show="openProfile" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 translate-y-2"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 translate-y-2"
                                 class="absolute right-0 top-full mt-3 w-80 bg-white rounded-2xl shadow-2xl border border-gray-100 ring-1 ring-black/5 overflow-hidden z-50 origin-top-right md:w-96"
                                 style="display: none;">
                                 
                                 <!-- Header Profil -->
                                 <div class="p-5 bg-gradient-to-br from-primary-600 to-primary-800 relative overflow-hidden text-white">
                                     <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4xKSIvPjwvc3ZnPg==')] opacity-30"></div>
                                     <div class="relative flex items-center gap-4">
                                         <div class="w-16 h-16 bg-white p-1 rounded-2xl shadow-lg rotate-3 shrink-0">
                                             @if(auth('member')->user()->photo)
                                                 <img src="{{ asset('storage/' . auth('member')->user()->photo) }}" class="w-full h-full object-cover rounded-xl">
                                             @else
                                                 <div class="w-full h-full bg-primary-50 rounded-xl flex items-center justify-center text-primary-500 text-2xl"><i class="fas fa-user"></i></div>
                                             @endif
                                         </div>
                                         <div class="min-w-0">
                                             <h4 class="font-bold text-lg truncate">{{ auth('member')->user()->name }}</h4>
                                             <p class="text-xs text-primary-200 font-mono mb-2">{{ auth('member')->user()->member_id }}</p>
                                             <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-white/10 border border-white/20 text-[10px] font-medium text-white backdrop-blur-md">
                                                 <i class="fab fa-google"></i> {{ __('opac.auth.connected') }}
                                             </div>
                                         </div>
                                     </div>
                                 </div>

                                 <!-- Menu Navigasi (Clean Grid 2x2) -->
                                 @if(auth('member')->user()->profile_completed)
                                 <div class="p-4 grid grid-cols-2 gap-3 bg-gray-50/50 border-b border-gray-100">
                                     <a href="{{ route('opac.member.dashboard') }}" class="p-3 bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-blue-200 transition group flex flex-col items-center gap-2 text-center">
                                         <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-110 transition shadow-inner"><i class="fas fa-tachometer-alt"></i></div>
                                         <span class="text-xs font-bold text-gray-600 group-hover:text-blue-600">{{ __('opac.member.dashboard') }}</span>
                                     </a>
                                     <a href="{{ route('opac.member.plagiarism.index') }}" class="p-3 bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-teal-200 transition group flex flex-col items-center gap-2 text-center">
                                         <div class="w-10 h-10 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center group-hover:scale-110 transition shadow-inner"><i class="fas fa-shield-alt"></i></div>
                                         <span class="text-xs font-bold text-gray-600 group-hover:text-teal-600">{{ __('opac.member.plagiarism') }}</span>
                                     </a>
                                     <a href="{{ route('opac.member.submissions') }}" class="p-3 bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-purple-200 transition group flex flex-col items-center gap-2 text-center">
                                         <div class="w-10 h-10 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center group-hover:scale-110 transition shadow-inner"><i class="fas fa-upload"></i></div>
                                         <span class="text-xs font-bold text-gray-600 group-hover:text-purple-600">{{ __('opac.member.submissions') }}</span>
                                     </a>
                                     <a href="{{ route('opac.member.settings') }}" class="p-3 bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-gray-300 transition group flex flex-col items-center gap-2 text-center">
                                         <div class="w-10 h-10 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center group-hover:scale-110 transition shadow-inner"><i class="fas fa-cog"></i></div>
                                         <span class="text-xs font-bold text-gray-600 group-hover:text-gray-900">{{ __('opac.member.settings') }}</span>
                                     </a>
                                 </div>
                                 @else
                                 <div class="p-4 bg-yellow-50 border-b border-yellow-100 text-center">
                                     <p class="text-xs font-medium text-yellow-700">{{ __('opac.auth.profile_incomplete') }}</p>
                                     <a href="{{ route('member.complete-profile') }}" class="text-xs font-bold text-yellow-800 underline mt-1 block">{{ __('opac.auth.complete_now') }}</a>
                                 </div>
                                 @endif
                                 
                                 <!-- Footer Logout -->
                                 <div class="p-3 bg-gray-50 border-t border-gray-100">
                                     <a href="{{ route('opac.logout') }}" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-white text-gray-700 font-semibold text-sm rounded-xl border border-gray-200 hover:bg-red-50 hover:text-red-600 hover:border-red-100 transition shadow-sm group">
                                         <i class="fas fa-sign-out-alt group-hover:-translate-x-0.5 transition-transform"></i> {{ __('opac.logout') }}
                                     </a>
                                 </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="hidden lg:flex items-center gap-2 px-4 py-2 text-sm font-medium text-primary-700 bg-white hover:bg-gray-50 rounded-xl shadow-sm transition">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>{{ __('opac.login') }}</span>
                        </a>
                    @endauth

                </div>
            </div>
        </div>
    </header>

    <!-- Mobile Sidebar -->
    <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-[60] lg:hidden">
        <div class="sidebar-overlay absolute inset-0 backdrop-blur-sm" @click="sidebarOpen = false"></div>
        <div class="absolute right-0 top-0 h-full w-[85%] max-w-sm bg-white shadow-2xl overflow-y-auto" 
             x-show="sidebarOpen" 
             x-transition:enter="transform transition ease-out duration-300"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in duration-200"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full">
            
            <!-- Sidebar Header with Logo -->
            <div class="bg-gradient-to-r from-primary-700 to-primary-900 p-4">
                <div class="flex items-center justify-between">
                    <img src="{{ url('storage/logo.png') }}" alt="UNIDA Library" class="h-9 w-auto">
                    <button @click="sidebarOpen = false" class="w-9 h-9 bg-white/20 hover:bg-white/30 rounded-xl flex items-center justify-center text-white transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- User Info -->
            @auth('member')
            <div class="p-4 bg-gradient-to-r from-primary-50 to-blue-50 border-b border-primary-100">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl flex items-center justify-center shadow-lg shadow-primary-500/30">
                        <i class="fas fa-user text-white text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-900">{{ auth('member')->user()->name }}</p>
                        <a href="{{ route('opac.member.dashboard') }}" class="text-xs text-primary-600 hover:underline flex items-center gap-1">
                            <i class="fas fa-tachometer-alt"></i> {{ __('opac.member.dashboard') }}
                        </a>
                    </div>
                    <a href="{{ route('opac.member.dashboard') }}" class="w-9 h-9 bg-white rounded-lg flex items-center justify-center text-primary-600 shadow-sm hover:shadow transition">
                        <i class="fas fa-chevron-right text-sm"></i>
                    </a>
                </div>
            </div>
            @else
            <div class="p-4 bg-gradient-to-r from-gray-50 to-slate-50 border-b">
                <p class="text-xs text-gray-500 mb-3 text-center">{{ __('opac.auth.full_access') }}</p>
                <div class="flex gap-2">
                    <a href="{{ route('login') }}" class="flex-1 py-2.5 text-center text-sm font-semibold text-primary-600 bg-white border-2 border-primary-600 rounded-xl hover:bg-primary-50 transition">{{ __('opac.login') }}</a>
                    <a href="{{ route('opac.register') }}" class="flex-1 py-2.5 text-center text-sm font-semibold text-white bg-gradient-to-r from-primary-600 to-primary-700 rounded-xl shadow-lg shadow-primary-500/30 hover:shadow-xl transition">{{ __('opac.register') }}</a>
                </div>
            </div>
            @endauth

            <!-- Search -->
            <div class="p-4 border-b">
                <form action="{{ route('opac.search') }}" method="GET">
                    <div class="flex bg-gray-100 rounded-xl overflow-hidden border-2 border-transparent focus-within:border-primary-300 focus-within:bg-white transition">
                        <input type="text" name="q" placeholder="{{ __('opac.search_placeholder') }}" class="flex-1 px-4 py-2.5 bg-transparent text-sm focus:outline-none">
                        <button class="px-4 text-primary-600 hover:text-primary-700"><i class="fas fa-search"></i></button>
                    </div>
                </form>
            </div>

            <!-- Menu Items -->
            <div class="p-4 space-y-2" x-data="{ openMenu: null }">
                <!-- HOME -->
                <div class="rounded-xl overflow-hidden" :class="openMenu === 'home' ? 'bg-primary-50 ring-1 ring-primary-100' : ''">
                    <button @click="openMenu = openMenu === 'home' ? null : 'home'" class="w-full flex items-center justify-between p-3 hover:bg-gray-50 transition" :class="openMenu === 'home' ? 'bg-primary-50 hover:bg-primary-50' : ''">
                        <span class="flex items-center gap-3">
                            <span class="w-9 h-9 rounded-lg flex items-center justify-center" :class="openMenu === 'home' ? 'bg-primary-600 text-white' : 'bg-primary-100 text-primary-600'">
                                <i class="fas fa-home"></i>
                            </span>
                            <span class="font-semibold text-gray-900">{{ __('opac.menu.home') }}</span>
                        </span>
                        <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="openMenu === 'home' ? 'rotate-180 text-primary-600' : ''"></i>
                    </button>
                    <div x-show="openMenu === 'home'" x-collapse class="px-3 pb-3">
                        <div class="pl-12 space-y-1 border-l-2 border-primary-200 ml-1.5">
                            <p class="text-[10px] font-bold text-primary-600 uppercase tracking-wider px-3 py-1.5">{{ __('opac.home_menu.profile') }}</p>
                            <a href="{{ route('opac.page', 'visi-misi') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-primary-600 hover:bg-white rounded-lg transition"><i class="fas fa-bullseye text-xs text-gray-400"></i> Visi & Misi</a>
                            <a href="{{ route('opac.page', 'sejarah') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-primary-600 hover:bg-white rounded-lg transition"><i class="fas fa-landmark text-xs text-gray-400"></i> Sejarah</a>
                            <a href="{{ route('opac.page', 'struktur-organisasi') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-primary-600 hover:bg-white rounded-lg transition"><i class="fas fa-sitemap text-xs text-gray-400"></i> Struktur Organisasi</a>
                            <a href="{{ route('opac.page', 'mou') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-primary-600 hover:bg-white rounded-lg transition"><i class="fas fa-handshake text-xs text-gray-400"></i> MoU</a>
                            <p class="text-[10px] font-bold text-primary-600 uppercase tracking-wider px-3 py-1.5 mt-2">{{ __('opac.home_menu.guidelines') }}</p>
                            <a href="{{ route('opac.page', 'tata-tertib') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-primary-600 hover:bg-white rounded-lg transition"><i class="fas fa-clipboard-list text-xs text-gray-400"></i> Tata Tertib</a>
                            <a href="{{ route('opac.page', 'jam-layanan') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-primary-600 hover:bg-white rounded-lg transition"><i class="fas fa-clock text-xs text-gray-400"></i> Jam Layanan</a>
                            <a href="{{ route('opac.page', 'fasilitas') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-primary-600 hover:bg-white rounded-lg transition"><i class="fas fa-building text-xs text-gray-400"></i> Fasilitas</a>
                            <a href="{{ route('opac.page', 'karir') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-primary-600 hover:bg-white rounded-lg transition"><i class="fas fa-user-tie text-xs text-gray-400"></i> Karir</a>
                        </div>
                    </div>
                </div>

                <!-- KOLEKSI (E-RESOURCES) -->
                <div class="rounded-xl overflow-hidden" :class="openMenu === 'eresources' ? 'bg-indigo-50 ring-1 ring-indigo-100' : ''">
                    <button @click="openMenu = openMenu === 'eresources' ? null : 'eresources'" class="w-full flex items-center justify-between p-3 hover:bg-gray-50 transition" :class="openMenu === 'eresources' ? 'bg-indigo-50 hover:bg-indigo-50' : ''">
                        <span class="flex items-center gap-3">
                            <span class="w-9 h-9 rounded-lg flex items-center justify-center" :class="openMenu === 'eresources' ? 'bg-indigo-600 text-white' : 'bg-indigo-100 text-indigo-600'">
                                <i class="fas fa-layer-group"></i>
                            </span>
                            <span class="font-semibold text-gray-900">{{ __('opac.menu.collection') }}</span>
                        </span>
                        <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="openMenu === 'eresources' ? 'rotate-180 text-indigo-600' : ''"></i>
                    </button>
                    <div x-show="openMenu === 'eresources'" x-collapse class="px-3 pb-3">
                        <div class="pl-12 space-y-1 border-l-2 border-indigo-200 ml-1.5">
                            {{-- Cari Koleksi --}}
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider px-3 py-1.5">🔍 {{ __('opac.collection_menu.find') }}</p>
                            <a href="{{ route('opac.search') }}?type=book" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-indigo-600 hover:bg-white rounded-lg transition"><i class="fas fa-book text-xs text-primary-500"></i> {{ __('opac.collection_menu.book') }}</a>
                            <a href="{{ route('opac.search') }}?type=ebook" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-indigo-600 hover:bg-white rounded-lg transition"><i class="fas fa-file-pdf text-xs text-orange-500"></i> {{ __('opac.collection_menu.ebook') }}</a>
                            <a href="{{ route('opac.search') }}?type=ethesis" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-indigo-600 hover:bg-white rounded-lg transition"><i class="fas fa-graduation-cap text-xs text-pink-500"></i> {{ __('opac.collection_menu.ethesis') }}</a>
                            
                            {{-- Koleksi Khusus --}}
                            <p class="text-[10px] font-bold text-amber-600 uppercase tracking-wider px-3 py-1.5 mt-2">👑 {{ __('opac.collection_menu.special') }}</p>
                            <a href="{{ route('opac.shamela.index') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-emerald-600 hover:bg-white rounded-lg transition bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-100">
                                <i class="fas fa-book-quran text-xs text-emerald-500"></i> {{ __('opac.collection_menu.maktabah_shamela') }} 
                                <span class="ml-auto px-1.5 py-0.5 bg-emerald-500 text-white text-[8px] font-bold rounded">8K+</span>
                            </a>
                            <a href="{{ route('opac.universitaria.index') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-amber-600 hover:bg-white rounded-lg transition bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-100">
                                <i class="fas fa-landmark text-xs text-amber-500"></i> {{ __('opac.collection_menu.universitaria') }} 
                                <span class="ml-auto px-1.5 py-0.5 bg-amber-500 text-white text-[8px] font-bold rounded">{{ __('opac.homepage.heritage') }}</span>
                            </a>
                            
                            {{-- Jurnal --}}
                            <p class="text-[10px] font-bold text-red-600 uppercase tracking-wider px-3 py-1.5 mt-2">📰 {{ __('opac.collection_menu.journal') }}</p>
                            <a href="{{ route('opac.journals.index') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-indigo-600 hover:bg-white rounded-lg transition"><i class="fas fa-newspaper text-xs text-red-500"></i> {{ __('opac.collection_menu.ejournal') }}</a>
                            <a href="{{ route('opac.database-access') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-indigo-600 hover:bg-white rounded-lg transition bg-gradient-to-r from-indigo-50 to-blue-50 border border-indigo-100">
                                <i class="fas fa-database text-xs text-indigo-500"></i> {{ __('opac.collection_menu.consortium') }} 
                                <span class="ml-auto px-1.5 py-0.5 bg-amber-400 text-amber-900 text-[8px] font-bold rounded">PRO</span>
                            </a>
                            
                            {{-- Sumber Terbuka --}}
                            <p class="text-[10px] font-bold text-green-600 uppercase tracking-wider px-3 py-1.5 mt-2">🌐 {{ __('opac.collection_menu.open_access') }}</p>
                            <a href="https://repo.unida.gontor.ac.id" target="_blank" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-indigo-600 hover:bg-white rounded-lg transition"><i class="fas fa-database text-xs text-indigo-400"></i> {{ __('opac.collection_menu.repository') }} <i class="fas fa-external-link-alt text-[10px] text-gray-300 ml-auto"></i></a>
                            <a href="https://ipusnas.id" target="_blank" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-indigo-600 hover:bg-white rounded-lg transition"><i class="fas fa-book-reader text-xs text-blue-400"></i> {{ __('opac.collection_menu.ipusnas') }} <i class="fas fa-external-link-alt text-[10px] text-gray-300 ml-auto"></i></a>
                            <a href="{{ route('opac.page', 'e-resources') }}" class="flex items-center justify-center gap-2 mt-2 px-3 py-2 bg-gradient-to-r from-primary-500 to-blue-600 text-white text-sm font-medium rounded-lg">
                                <i class="fas fa-th-large"></i> {{ __('opac.collection_menu.all_eresources') }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- DISCOVER -->
                <div class="rounded-xl overflow-hidden" :class="openMenu === 'discover' ? 'bg-emerald-50 ring-1 ring-emerald-100' : ''">
                    <button @click="openMenu = openMenu === 'discover' ? null : 'discover'" class="w-full flex items-center justify-between p-3 hover:bg-gray-50 transition" :class="openMenu === 'discover' ? 'bg-emerald-50 hover:bg-emerald-50' : ''">
                        <span class="flex items-center gap-3">
                            <span class="w-9 h-9 rounded-lg flex items-center justify-center" :class="openMenu === 'discover' ? 'bg-emerald-600 text-white' : 'bg-emerald-100 text-emerald-600'">
                                <i class="fas fa-compass"></i>
                            </span>
                            <span class="font-semibold text-gray-900">{{ __('opac.menu.discover') }}</span>
                        </span>
                        <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="openMenu === 'discover' ? 'rotate-180 text-emerald-600' : ''"></i>
                    </button>
                    <div x-show="openMenu === 'discover'" x-collapse class="px-3 pb-3">
                        <div class="pl-12 space-y-1 border-l-2 border-emerald-200 ml-1.5">
                            <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider px-3 py-1.5">{{ __('opac.discover_menu.new_experience') }}</p>
                            <a href="{{ route('opac.page', 'event-library') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-emerald-600 hover:bg-white rounded-lg transition"><i class="fas fa-calendar-star text-xs text-gray-400"></i> Event Library</a>
                            <a href="{{ route('opac.page', 'virtual-tour') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-emerald-600 hover:bg-white rounded-lg transition"><i class="fas fa-vr-cardboard text-xs text-gray-400"></i> Virtual Tour</a>
                            <a href="{{ route('opac.page', 'e-learning') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-emerald-600 hover:bg-white rounded-lg transition"><i class="fas fa-laptop text-xs text-gray-400"></i> E-Learning</a>
                            <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider px-3 py-1.5 mt-2">{{ __('opac.discover_menu.explore') }}</p>
                            <a href="{{ route('opac.page', 'prosa-kreatif') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-emerald-600 hover:bg-white rounded-lg transition"><i class="fas fa-pen-fancy text-xs text-gray-400"></i> Prosa Kreatif</a>
                            <a href="{{ route('opac.page', 'survey') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-emerald-600 hover:bg-white rounded-lg transition"><i class="fas fa-poll text-xs text-gray-400"></i> Experience Survey</a>
                            <a href="{{ route('opac.page', 'research-tools') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-emerald-600 hover:bg-white rounded-lg transition"><i class="fas fa-microscope text-xs text-gray-400"></i> Research Tools</a>
                        </div>
                    </div>
                </div>

                <!-- GUIDE -->
                <div class="rounded-xl overflow-hidden" :class="openMenu === 'guide' ? 'bg-orange-50 ring-1 ring-orange-100' : ''">
                    <button @click="openMenu = openMenu === 'guide' ? null : 'guide'" class="w-full flex items-center justify-between p-3 hover:bg-gray-50 transition" :class="openMenu === 'guide' ? 'bg-orange-50 hover:bg-orange-50' : ''">
                        <span class="flex items-center gap-3">
                            <span class="w-9 h-9 rounded-lg flex items-center justify-center" :class="openMenu === 'guide' ? 'bg-orange-600 text-white' : 'bg-orange-100 text-orange-600'">
                                <i class="fas fa-book-open"></i>
                            </span>
                            <span class="font-semibold text-gray-900">{{ __('opac.menu.guide') }}</span>
                        </span>
                        <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="openMenu === 'guide' ? 'rotate-180 text-orange-600' : ''"></i>
                    </button>
                    <div x-show="openMenu === 'guide'" x-collapse class="px-3 pb-3">
                        <div class="pl-12 space-y-1 border-l-2 border-orange-200 ml-1.5">
                            <p class="text-[10px] font-bold text-orange-600 uppercase tracking-wider px-3 py-1.5">{{ __('opac.guide_menu.user_guide') }}</p>
                            <a href="{{ route('opac.page', 'panduan-opac') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-orange-600 hover:bg-white rounded-lg transition"><i class="fas fa-search text-xs text-gray-400"></i> Panduan OPAC</a>
                            <a href="{{ route('opac.panduan.thesis') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-orange-600 hover:bg-white rounded-lg transition"><i class="fas fa-upload text-xs text-gray-400"></i> Unggah Tugas Akhir</a>
                            <a href="{{ route('opac.panduan.plagiarism') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-teal-600 hover:bg-white rounded-lg transition"><i class="fas fa-shield-alt text-xs text-gray-400"></i> Cek Plagiasi</a>
                            <a href="{{ route('opac.page', 'panduan-ospek') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-orange-600 hover:bg-white rounded-lg transition"><i class="fas fa-user-graduate text-xs text-gray-400"></i> Panduan Ospek</a>
                            <p class="text-[10px] font-bold text-orange-600 uppercase tracking-wider px-3 py-1.5 mt-2">{{ __('opac.guide_menu.academic') }}</p>
                            <a href="{{ route('opac.page', 'panduan-akademik') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-orange-600 hover:bg-white rounded-lg transition"><i class="fas fa-graduation-cap text-xs text-gray-400"></i> Panduan Akademik</a>
                            <a href="{{ route('opac.page', 'materi-perpustakaan') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-orange-600 hover:bg-white rounded-lg transition"><i class="fas fa-chalkboard-teacher text-xs text-gray-400"></i> Materi Perpustakaan</a>
                            <a href="{{ route('opac.page', 'download-eddc') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-orange-600 hover:bg-white rounded-lg transition"><i class="fas fa-download text-xs text-gray-400"></i> Download E-DDC 23</a>
                        </div>
                    </div>
                </div>

                <!-- NEWS -->
                <a href="{{ route('opac.news.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-pink-50 transition group">
                    <span class="w-9 h-9 bg-pink-100 group-hover:bg-pink-600 rounded-lg flex items-center justify-center text-pink-600 group-hover:text-white transition">
                        <i class="fas fa-newspaper"></i>
                    </span>
                    <span class="font-semibold text-gray-900">{{ __('opac.menu.news') }}</span>
                </a>
            </div>

            <!-- Quick Access -->
            <div class="p-4 border-t">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3">{{ __('opac.quick_access') }}</p>
                <div class="grid grid-cols-3 gap-2">
                    <a href="{{ route('opac.search') . '?type=book' }}" class="flex flex-col items-center gap-1 p-3 bg-gray-50 hover:bg-primary-50 rounded-xl transition group">
                        <i class="fas fa-search text-gray-400 group-hover:text-primary-600"></i>
                        <span class="text-[10px] text-gray-600 group-hover:text-primary-600">{{ __('opac.catalog') }}</span>
                    </a>
                    <a href="{{ route('opac.search') . '?type=ebook' }}" class="flex flex-col items-center gap-1 p-3 bg-gray-50 hover:bg-orange-50 rounded-xl transition group">
                        <i class="fas fa-book text-gray-400 group-hover:text-orange-600"></i>
                        <span class="text-[10px] text-gray-600 group-hover:text-orange-600">E-Book</span>
                    </a>
                    <a href="{{ route('opac.search') . '?type=ethesis' }}" class="flex flex-col items-center gap-1 p-3 bg-gray-50 hover:bg-pink-50 rounded-xl transition group">
                        <i class="fas fa-graduation-cap text-gray-400 group-hover:text-pink-600"></i>
                        <span class="text-[10px] text-gray-600 group-hover:text-pink-600">E-Thesis</span>
                    </a>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="p-4 border-t bg-gradient-to-br from-gray-50 to-slate-100">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3">{{ __('opac.contact_us') }}</p>
                <div class="space-y-2">
                    <a href="tel:085183053934" class="flex items-center gap-3 p-2.5 bg-white rounded-xl text-sm text-gray-600 hover:text-primary-600 shadow-sm hover:shadow transition">
                        <span class="w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center"><i class="fas fa-phone text-primary-600 text-xs"></i></span>
                        0851-8305-3934
                    </a>
                    <a href="mailto:library@unida.gontor.ac.id" class="flex items-center gap-3 p-2.5 bg-white rounded-xl text-sm text-gray-600 hover:text-primary-600 shadow-sm hover:shadow transition">
                        <span class="w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center"><i class="fas fa-envelope text-primary-600 text-xs"></i></span>
                        library@unida.gontor.ac.id
                    </a>
                    <a href="https://wa.me/6285183053934" target="_blank" class="flex items-center gap-3 p-2.5 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl text-sm text-white shadow-lg shadow-green-500/30 hover:shadow-xl transition">
                        <span class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center"><i class="fab fa-whatsapp text-lg"></i></span>
                        Chat WhatsApp
                    </a>
                </div>
                <!-- Social Media -->
                <div class="flex items-center justify-center gap-3 mt-4 pt-4 border-t border-gray-200">
                    <a href="https://youtube.com/@perpustakaanunidagontor" target="_blank" class="w-9 h-9 bg-white rounded-lg flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 shadow-sm transition"><i class="fab fa-youtube"></i></a>
                    <a href="https://instagram.com/perpustakaanunida" target="_blank" class="w-9 h-9 bg-white rounded-lg flex items-center justify-center text-gray-400 hover:text-pink-500 hover:bg-pink-50 shadow-sm transition"><i class="fab fa-instagram"></i></a>
                    <a href="https://tiktok.com/@perpustakaanunida" target="_blank" class="w-9 h-9 bg-white rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-900 hover:bg-gray-100 shadow-sm transition"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
        </div>
    </div>

    <main class="pb-20 lg:pb-0">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-primary-700 to-primary-900 mt-12 hidden lg:block">
        <div class="max-w-7xl mx-auto px-4 py-10">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <img src="{{ url('storage/logo.png') }}" alt="UNIDA Library" class="h-12 w-auto brightness-0 invert">
                    </div>
                    <p class="text-sm text-primary-200">{{ __('opac.footer.about') }}</p>
                    <div class="flex gap-3 mt-4">
                        <a href="https://youtube.com/@perpustakaanunidagontor" target="_blank" class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center text-white hover:bg-red-500 transition"><i class="fab fa-youtube"></i></a>
                        <a href="https://instagram.com/perpustakaanunida" target="_blank" class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center text-white hover:bg-pink-500 transition"><i class="fab fa-instagram"></i></a>
                        <a href="https://tiktok.com/@perpustakaanunida" target="_blank" class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center text-white hover:bg-gray-700 transition"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-4">{{ __('opac.footer.eresources') }}</h4>
                    <ul class="space-y-2 text-sm text-primary-200">
                        <li><a href="{{ route('opac.search') . '?type=book' }}" class="hover:text-white transition"><i class="fas fa-search mr-2"></i>{{ __('opac.footer.opac') }}</a></li>
                        <li><a href="{{ route('opac.search') . '?type=ebook' }}" class="hover:text-white transition"><i class="fas fa-file-pdf mr-2"></i>{{ __('opac.collection_menu.ebook') }}</a></li>
                        <li><a href="{{ route('opac.search') . '?type=ethesis' }}" class="hover:text-white transition"><i class="fas fa-graduation-cap mr-2"></i>{{ __('opac.collection_menu.ethesis') }}</a></li>
                        <li><a href="https://repo.unida.gontor.ac.id" target="_blank" class="hover:text-white transition"><i class="fas fa-database mr-2"></i>{{ __('opac.footer.repository') }}</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-4">{{ __('opac.home_menu.hours') }}</h4>
                    <ul class="space-y-2 text-sm text-primary-200">
                        <li class="flex items-start gap-2"><i class="fas fa-clock mt-1"></i><span>{{ __('opac.footer.weekday') }}<br>08.00 - 16.00 WIB</span></li>
                        <li class="flex items-start gap-2"><i class="fas fa-clock mt-1"></i><span>{{ __('opac.footer.saturday') }}<br>08.00 - 12.00 WIB</span></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-4">{{ __('opac.footer.contact') }}</h4>
                    <ul class="space-y-2 text-sm text-primary-200">
                        <li><a href="mailto:library@unida.gontor.ac.id" class="hover:text-white transition flex items-center gap-2"><i class="fas fa-envelope"></i>library@unida.gontor.ac.id</a></li>
                        <li><a href="https://wa.me/6285183053934" target="_blank" class="hover:text-white transition flex items-center gap-2"><i class="fab fa-whatsapp"></i>0851-8305-3934</a></li>
                        <li><a href="https://library.unida.gontor.ac.id" target="_blank" class="hover:text-white transition flex items-center gap-2"><i class="fas fa-globe"></i>library.unida.gontor.ac.id</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-white/20 mt-8 pt-6 text-center text-sm text-primary-300">
                &copy; {{ date('Y') }} {{ __('opac.site_name') }}. {{ __('opac.footer.all_rights') }}.
            </div>
        </div>
    </footer>

    <!-- Search Modal - Works on both mobile and desktop -->
    <div x-show="searchOpen" x-cloak class="fixed inset-0 z-[70]" @keydown.escape.window="searchOpen = false">
        <div class="absolute inset-0 bg-gradient-to-br from-primary-900/90 to-indigo-900/90 backdrop-blur-md" @click="searchOpen = false"></div>
        <div class="absolute inset-x-4 top-16 lg:top-24 lg:inset-x-auto lg:left-1/2 lg:-translate-x-1/2 lg:w-full lg:max-w-2xl" 
             x-show="searchOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4">
            
            <!-- Close button floating -->
            <button @click="searchOpen = false" class="absolute -top-2 -right-2 lg:top-0 lg:-right-12 w-10 h-10 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center text-white transition z-10">
                <i class="fas fa-times"></i>
            </button>

            <!-- Search container -->
            <div class="text-center mb-6">
                <h3 class="text-white text-xl lg:text-2xl font-bold mb-2">{{ __('opac.collection_menu.search_global') }}</h3>
                <p class="text-primary-200 text-sm">{{ __('opac.search_placeholder') }}</p>
            </div>

            <form action="{{ route('opac.search') }}" method="GET">
                <!-- Search input - Rounded Full Style -->
                <div class="relative group">
                    <div class="absolute -inset-1 bg-gradient-to-r from-primary-400 via-white to-primary-400 rounded-full opacity-30 group-hover:opacity-50 blur-lg transition duration-500"></div>
                    <div class="relative flex items-center bg-white rounded-full shadow-2xl overflow-hidden">
                        <div class="pl-5 pr-2">
                            <i class="fas fa-search text-gray-400 text-lg"></i>
                        </div>
                        <input type="text" name="q" placeholder="Ketik judul, pengarang, ISBN, atau kata kunci..." 
                               class="flex-1 px-2 py-4 lg:py-5 text-gray-700 text-sm lg:text-base focus:outline-none bg-transparent"
                               x-ref="searchInput"
                               @keydown.enter="$el.form.submit()">
                        <button type="submit" class="m-1.5 px-6 lg:px-8 py-2.5 lg:py-3 bg-gradient-to-r from-primary-600 to-indigo-600 hover:from-primary-700 hover:to-indigo-700 text-white font-semibold rounded-full transition-all duration-300 shadow-lg flex items-center gap-2">
                            <span class="hidden sm:inline">{{ __('opac.search') }}</span>
                            <i class="fas fa-arrow-right text-sm"></i>
                        </button>
                    </div>
                </div>

                <!-- Quick search categories -->
                <div class="mt-6 flex flex-wrap items-center justify-center gap-2">
                    <span class="text-primary-200 text-xs">Filter:</span>
                    <a href="{{ route('opac.search') }}?type=book" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-xs font-medium rounded-full transition flex items-center gap-1.5">
                        <i class="fas fa-book text-[10px]"></i> {{ __('opac.homepage.books') }}
                    </a>
                    <a href="{{ route('opac.search') }}?type=ebook" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-xs font-medium rounded-full transition flex items-center gap-1.5">
                        <i class="fas fa-file-pdf text-[10px]"></i> E-Book
                    </a>
                    <a href="{{ route('opac.search') }}?type=ethesis" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-xs font-medium rounded-full transition flex items-center gap-1.5">
                        <i class="fas fa-graduation-cap text-[10px]"></i> E-Thesis
                    </a>
                    <a href="{{ route('opac.journals.index') }}" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-xs font-medium rounded-full transition flex items-center gap-1.5">
                        <i class="fas fa-file-lines text-[10px]"></i> {{ __('opac.homepage.journals') }}
                    </a>
                    <a href="{{ route('opac.news.index') }}" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-xs font-medium rounded-full transition flex items-center gap-1.5">
                        <i class="fas fa-newspaper text-[10px]"></i> {{ __('opac.menu.news') }}
                    </a>
                </div>

                <!-- Popular searches -->
                <div class="mt-4 flex flex-wrap items-center justify-center gap-2">
                    <span class="text-primary-300 text-xs">{{ __('opac.popular') }}:</span>
                    <a href="{{ route('opac.search') }}?q=islam" class="px-3 py-1 bg-white/5 hover:bg-white/10 text-primary-200 text-xs rounded-full transition">Islam</a>
                    <a href="{{ route('opac.search') }}?q=ekonomi" class="px-3 py-1 bg-white/5 hover:bg-white/10 text-primary-200 text-xs rounded-full transition">Ekonomi</a>
                    <a href="{{ route('opac.search') }}?q=pendidikan" class="px-3 py-1 bg-white/5 hover:bg-white/10 text-primary-200 text-xs rounded-full transition">Pendidikan</a>
                </div>

                <p class="mt-6 text-xs text-primary-300 text-center">
                    Tekan <kbd class="px-2 py-1 bg-white/10 rounded text-white">Esc</kbd> atau klik di luar untuk menutup
                </p>
            </form>
        </div>
    </div>

    <!-- Mobile Bottom Nav -->
    <div class="fixed bottom-0 left-0 right-0 bg-gradient-to-r from-primary-800 to-primary-900 lg:hidden z-50 safe-area-bottom shadow-[0_-4px_20px_rgba(0,0,0,0.25)]">
        <div class="flex items-center justify-around py-2">
            <a href="{{ route('opac.home') }}" class="flex flex-col items-center py-1.5 px-3 {{ request()->routeIs('opac.home') ? 'text-white' : 'text-primary-300' }} hover:text-white transition">
                <span class="w-10 h-10 rounded-xl flex items-center justify-center {{ request()->routeIs('opac.home') ? 'bg-white/20' : '' }}">
                    <i class="fas fa-home text-lg"></i>
                </span>
                <span class="text-[10px] mt-0.5 font-medium">{{ __('opac.menu.home') }}</span>
            </a>
            <a href="{{ route('opac.search') }}?type=ebook" class="flex flex-col items-center py-1.5 px-3 {{ request('type') == 'ebook' ? 'text-white' : 'text-primary-300' }} hover:text-white transition">
                <span class="w-10 h-10 rounded-xl flex items-center justify-center {{ request('type') == 'ebook' ? 'bg-white/20' : '' }}">
                    <i class="fas fa-book text-lg"></i>
                </span>
                <span class="text-[10px] mt-0.5 font-medium">E-Book</span>
            </a>
            <button @click="searchOpen = true; $nextTick(() => $refs.searchInput?.focus())" class="flex flex-col items-center -mt-5">
                <span class="w-14 h-14 bg-gradient-to-br from-accent-400 to-accent-500 text-gray-900 rounded-2xl shadow-lg shadow-accent-500/40 flex items-center justify-center ring-4 ring-primary-800">
                    <i class="fas fa-search text-xl"></i>
                </span>
                <span class="text-[10px] mt-1 font-medium text-white">{{ __('opac.search') }}</span>
            </button>
            <a href="{{ route('opac.search') }}?type=ethesis" class="flex flex-col items-center py-1.5 px-3 {{ request('type') == 'ethesis' ? 'text-white' : 'text-primary-300' }} hover:text-white transition">
                <span class="w-10 h-10 rounded-xl flex items-center justify-center {{ request('type') == 'ethesis' ? 'bg-white/20' : '' }}">
                    <i class="fas fa-graduation-cap text-lg"></i>
                </span>
                <span class="text-[10px] mt-0.5 font-medium">E-Thesis</span>
            </a>
            <button @click="sidebarOpen = true" class="flex flex-col items-center py-1.5 px-3 text-primary-300 hover:text-white transition">
                <span class="w-10 h-10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-bars text-lg"></i>
                </span>
                <span class="text-[10px] mt-0.5 font-medium">Menu</span>
            </button>
        </div>
    </div>

    {{-- Floating Widgets: WhatsApp & Scroll to Top --}}
    @include('partials.floating-widgets')

    <script src="{{ url('livewire/livewire.js') }}" data-csrf="{{ csrf_token() }}" data-update-uri="{{ url('livewire/update') }}"></script>
</body>
</html>
