<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Staff Portal</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                            950: '#172554',
                        },
                        danger: {
                            50: '#fef2f2',
                            500: '#ef4444',
                            600: '#dc2626',
                        },
                        success: {
                            50: '#f0fdf4',
                            500: '#22c55e',
                            600: '#16a34a',
                        },
                        warning: {
                            50: '#fffbeb',
                            500: '#f59e0b',
                            600: '#d97706',
                        },
                    }
                }
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    {{-- Alpine.js is included in @filamentScripts - do not load CDN version --}}

    @php
        $user = auth()->user();
        $branch = $user->branch;
        $currentDate = now()->locale('id')->isoFormat('dddd, D MMMM Y');
        $navItems = [
            ['label' => 'Dashboard', 'icon' => 'fa-house', 'route' => 'staff.dashboard', 'patterns' => ['staff.dashboard*']],
            ['label' => 'Tasks', 'icon' => 'fa-clipboard-list', 'route' => 'staff.task.index', 'patterns' => ['staff.task*']],
            ['label' => 'Sirkulasi', 'icon' => 'fa-arrows-rotate', 'route' => 'staff.circulation.index', 'patterns' => ['staff.circulation*']],
            ['label' => 'Katalog', 'icon' => 'fa-book', 'route' => 'staff.biblio.index', 'patterns' => ['staff.biblio*']],
            ['label' => 'E-Library', 'icon' => 'fa-cloud', 'route' => 'staff.elibrary.index', 'patterns' => ['staff.elibrary*']],
            ['label' => 'Anggota', 'icon' => 'fa-users', 'route' => 'staff.member.index', 'patterns' => ['staff.member*']],
            ['label' => 'Berita', 'icon' => 'fa-newspaper', 'route' => 'staff.news.index', 'patterns' => ['staff.news*']],
            ['label' => 'Stock Opname', 'icon' => 'fa-clipboard-check', 'route' => 'staff.stock-opname.index', 'patterns' => ['staff.stock-opname*']],
            ['label' => 'Statistik', 'icon' => 'fa-chart-pie', 'route' => 'staff.statistics.index', 'patterns' => ['staff.statistics*']],
            ['label' => 'Analytics', 'icon' => 'fa-chart-line', 'route' => 'staff.analytics.index', 'patterns' => ['staff.analytics*']],
            ['label' => 'Profil', 'icon' => 'fa-user-circle', 'route' => 'staff.profile', 'patterns' => ['staff.profile*']],
        ];
        // Add Control menu for admin only
        if (in_array($user->role, ['super_admin', 'admin'])) {
            array_splice($navItems, 9, 0, [['label' => 'Pengaturan', 'icon' => 'fa-cog', 'route' => 'staff.control.index', 'patterns' => ['staff.control*']]]);
        }
    @endphp

    <link rel="stylesheet" href="{{ asset('css/staff-portal.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.default.min.css" rel="stylesheet">
    @stack('styles')

    <style>
        [x-cloak] { display: none !important; }
        
        .staff-sidebar { width: 14rem; }
        html.sidebar-collapsed .staff-sidebar { width: 5rem; }
        .main-content-wrapper { margin-left: 14rem; }
        html.sidebar-collapsed .main-content-wrapper { margin-left: 5rem; }
        
        @media (max-width: 1023px) {
            .staff-sidebar { display: none !important; }
            .main-content-wrapper { margin-left: 0 !important; }
        }
        
        /* Ensure modals with high z-index appear above everything */
        .fixed[style*="z-index: 9999"],
        .fixed[style*="z-index: 10000"] {
            position: fixed !important;
        }
    </style>

    <script>
        (function() {
            const collapsed = localStorage.getItem('staffSidebarCollapsed') === 'true';
            if (collapsed) document.documentElement.classList.add('sidebar-collapsed');
        })();
    </script>
    @livewireStyles
    @filamentStyles
</head>
<body x-data="staffPortal()" class="antialiased bg-slate-50 font-['Inter']">

    {{-- Mobile Header --}}
    <header class="mobile-header lg:hidden fixed top-0 left-0 right-0 h-16 flex items-center justify-between px-4 z-50 bg-gradient-to-r from-blue-700 via-blue-800 to-indigo-900 shadow-xl">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-book-open text-white text-sm"></i>
            </div>
            <div>
                <p class="text-white text-sm font-semibold">Staff Portal</p>
                <p class="text-blue-200 text-[10px]">{{ $branch?->name ?? 'Perpustakaan' }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-9 h-9 rounded-full bg-white/20 overflow-hidden border-2 border-white/30">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random" class="w-full h-full object-cover">
            </div>
        </div>
    </header>

    <div class="lg:flex lg:min-h-screen w-full lg:pt-0">
        {{-- Desktop Sidebar - FIXED --}}
        <aside class="staff-sidebar hidden lg:flex lg:flex-col lg:fixed lg:top-0 lg:left-0 lg:h-screen bg-gradient-to-b from-blue-700 via-blue-800 to-indigo-900 text-white/80 shadow-xl transition-all duration-300 z-40"
               :class="sidebarCollapsed ? 'lg:w-20' : 'lg:w-56'"
               x-cloak>
            
            {{-- Logo --}}
            <div class="p-4 border-b border-white/10 min-h-[72px] flex items-center transition-all duration-300">
                <div class="flex items-center gap-3 transition-all duration-300"
                     :class="sidebarCollapsed ? 'opacity-0 absolute pointer-events-none' : 'opacity-100 relative'">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg overflow-hidden">
                        <img src="{{ asset('storage/logo-portal.png') }}" alt="Logo" class="w-8 h-8 object-contain">
                    </div>
                    <div class="leading-tight whitespace-nowrap">
                        <p class="text-sm font-bold text-white">UNIDA LIBRARY</p>
                        <p class="text-[10px] text-blue-200">Admin Portal</p>
                    </div>
                </div>
                <div class="transition-all duration-300 mx-auto"
                     :class="sidebarCollapsed ? 'opacity-100 relative' : 'opacity-0 absolute pointer-events-none'">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-lg overflow-hidden">
                        <img src="{{ asset('storage/logo-portal.png') }}" alt="Logo" class="w-8 h-8 object-contain">
                    </div>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
                @foreach($navItems as $item)
                    @php $active = request()->routeIs($item['patterns']); @endphp
                    <a href="{{ route($item['route']) }}" wire:navigate title="{{ $item['label'] }}"
                       class="group relative flex items-center rounded-xl transition-all duration-200 {{ $active ? 'bg-white/15 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}"
                       :class="sidebarCollapsed ? 'justify-center p-3' : 'gap-3 px-3 py-2.5'">
                        @if($active)
                            <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-white rounded-r-full"></div>
                        @endif
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center {{ $active ? 'bg-white/20 text-white shadow-lg' : 'bg-white/10 text-blue-200 group-hover:text-white' }} flex-shrink-0 transition-all">
                            <i class="fas {{ $item['icon'] }} text-sm"></i>
                        </div>
                        <span class="text-sm font-medium transition-all duration-300"
                              :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            {{-- User Info --}}
            <div class="border-t border-white/10 p-4" x-data="{ showLogoutModal: false }">
                <div class="bg-white/10 rounded-xl p-3 mb-3 transition-all duration-300"
                     :class="sidebarCollapsed ? 'opacity-0 absolute pointer-events-none' : 'opacity-100 relative'">
                    <p class="text-xs text-blue-200">{{ ucfirst($user->role) }}</p>
                    <p class="text-sm font-semibold text-white truncate">{{ $user->name }}</p>
                </div>
                <button @click="showLogoutModal = true" 
                        type="button"
                        class="w-full flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-white/10 hover:bg-red-500/80 border border-white/20 hover:border-red-500 text-white transition-all">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="text-sm font-medium transition-all duration-300"
                          :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">Logout</span>
                </button>

                {{-- Logout Confirmation Modal - Teleported to body --}}
                <template x-teleport="body">
                    <div x-show="showLogoutModal" 
                         x-cloak
                         class="fixed inset-0 z-[99999] flex items-center justify-center p-4"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0">
                        
                        {{-- Backdrop --}}
                        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showLogoutModal = false"></div>
                        
                        {{-- Modal Content --}}
                        <div class="relative bg-white rounded-2xl shadow-2xl max-w-sm w-full overflow-hidden"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                             @click.stop>
                            
                            {{-- Icon Header --}}
                            <div class="pt-8 pb-4 flex justify-center">
                                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-red-100 to-red-50 flex items-center justify-center">
                                    <div class="w-14 h-14 rounded-full bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center shadow-lg shadow-red-500/30">
                                        <i class="fas fa-sign-out-alt text-white text-2xl"></i>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Content --}}
                            <div class="px-6 pb-6 text-center">
                                <h3 class="text-xl font-bold text-gray-900 mb-2">Keluar dari Portal?</h3>
                                <p class="text-gray-500 text-sm mb-6">
                                    Anda akan keluar dari akun <span class="font-semibold text-gray-700">{{ $user->name }}</span>. 
                                    Pastikan semua pekerjaan sudah tersimpan.
                                </p>
                                
                                {{-- Actions --}}
                                <div class="flex gap-3">
                                    <button @click="showLogoutModal = false" 
                                            type="button"
                                            class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition">
                                        Batal
                                    </button>
                                    <a href="{{ route('staff.logout') }}" 
                                       class="flex-1 px-4 py-3 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold rounded-xl shadow-lg shadow-red-500/25 transition flex items-center justify-center gap-2">
                                        <i class="fas fa-sign-out-alt"></i>
                                        <span>Ya, Keluar</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </aside>

        {{-- Desktop Header - FIXED --}}
        <header class="desktop-header hidden lg:flex items-center justify-between px-8 bg-white border-b border-slate-200 shadow-sm fixed top-0 z-30 h-[72px]"
                :class="sidebarCollapsed ? 'left-20 w-[calc(100vw-80px)]' : 'left-56 w-[calc(100vw-224px)]'">
            <div class="flex items-center gap-4">
                <button @click="toggleSidebar()" class="w-9 h-9 rounded-lg border border-slate-200 text-slate-500 hover:text-slate-900 hover:border-slate-400 flex items-center justify-center transition-all">
                    <i class="fas" :class="sidebarCollapsed ? 'fa-angles-right' : 'fa-angles-left'"></i>
                </button>
                <div class="h-8 w-px bg-slate-200"></div>
                <div>
                    <h2 class="text-lg font-semibold leading-tight text-slate-900">@yield('title', 'Dashboard')</h2>
                    <p class="text-xs text-slate-500">{{ $currentDate }}</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                @include('staff.components.portal-switcher')
                @include('staff.components.quick-actions')
                
                {{-- Notification Bell --}}
                @livewire('staff.notification.notification-bell')
                
                <div class="h-8 w-px bg-slate-200"></div>
                <div class="text-right">
                    <p class="text-xs text-slate-400">{{ $branch?->name ?? 'Semua Cabang' }}</p>
                    <p class="text-sm font-semibold text-slate-900">{{ $user->name }}</p>
                </div>
                <div class="w-10 h-10 rounded-full border-2 border-slate-200 overflow-hidden">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random" class="w-full h-full object-cover">
                </div>
            </div>
        </header>

        {{-- Main Content Wrapper --}}
        <div class="flex-1 flex flex-col min-h-screen bg-slate-50/70 lg:pt-0 lg:pb-0 main-content-wrapper overflow-x-hidden"
             :class="sidebarCollapsed ? 'lg:ml-20' : 'lg:ml-56'">
            <main class="flex-1 w-full max-w-full px-4 pt-20 pb-24 sm:px-6 lg:px-8 lg:pt-[88px] lg:pb-8 overflow-x-hidden">
                {{-- Queue Status Alert for Admins --}}
                <x-queue-status-alert />
                
                @yield('content')
            </main>
        </div>
    </div>

    @stack('modals')

    {{-- Staff Chat Widget --}}
    @livewire('staff.chat.staff-chat')

    {{-- Mobile Bottom Nav --}}
    <nav class="mobile-nav lg:hidden fixed bottom-0 left-0 right-0 h-16 bg-gradient-to-r from-blue-700 to-indigo-800 flex items-center justify-around px-2 z-50 shadow-[0_-4px_20px_rgba(0,0,0,0.15)]">
        @foreach(array_slice($navItems, 0, 5) as $item)
            @php $active = request()->routeIs($item['patterns']); @endphp
            <a href="{{ route($item['route']) }}" wire:navigate class="flex flex-col items-center gap-1 px-3 py-2 rounded-xl transition-all {{ $active ? 'text-amber-400' : 'text-white/70' }}">
                <i class="fas {{ $item['icon'] }} text-lg"></i>
                <span class="text-[10px] font-medium">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <script>
        function staffPortal() {
            return {
                sidebarCollapsed: localStorage.getItem('staffSidebarCollapsed') === 'true',
                toggleSidebar() {
                    this.sidebarCollapsed = !this.sidebarCollapsed;
                    localStorage.setItem('staffSidebarCollapsed', this.sidebarCollapsed);
                    document.documentElement.classList.toggle('sidebar-collapsed', this.sidebarCollapsed);
                }
            }
        }
    </script>
    @stack('scripts')
    @livewireScripts
    @filamentScripts

    {{-- Filament Notifications --}}
    @livewire('notifications')
</body>
</html>
