<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Staff Portal</title>

    {{-- Preconnect to CDNs --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://cdn.tailwindcss.com">
    <link rel="preconnect" href="https://unpkg.com">
    
    {{-- Critical CSS - prevents FOUC --}}
    <style>
        html, body { margin: 0; padding: 0; background: #f8fafc; }
        body { opacity: 0; }
        body.ready { opacity: 1; transition: opacity 0.1s; }
    </style>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js" defer></script>
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
            ['label' => 'Kehadiran', 'icon' => 'fa-fingerprint', 'route' => 'staff.attendance.index', 'patterns' => ['staff.attendance*']],
            ['label' => 'Sirkulasi', 'icon' => 'fa-arrows-rotate', 'route' => 'staff.circulation.index', 'patterns' => ['staff.circulation*']],
            ['label' => 'Katalog', 'icon' => 'fa-book', 'route' => 'staff.biblio.index', 'patterns' => ['staff.biblio*']],
            ['label' => 'E-Library', 'icon' => 'fa-cloud', 'route' => 'staff.elibrary.index', 'patterns' => ['staff.elibrary*']],
            ['label' => 'Anggota', 'icon' => 'fa-users', 'route' => 'staff.member.index', 'patterns' => ['staff.member*']],
            ['label' => 'Berita', 'icon' => 'fa-newspaper', 'route' => 'staff.news.index', 'patterns' => ['staff.news*']],
            ['label' => 'Stock Opname', 'icon' => 'fa-clipboard-check', 'route' => 'staff.stock-opname.index', 'patterns' => ['staff.stock-opname*']],
            ['label' => 'Statistik', 'icon' => 'fa-chart-pie', 'route' => 'staff.statistics.index', 'patterns' => ['staff.statistics*']],
            ['label' => 'Analytics', 'icon' => 'fa-chart-line', 'route' => 'staff.analytics.index', 'patterns' => ['staff.analytics*']],
        ];
        // Add Control menu for admin only
        if (in_array($user->role, ['super_admin', 'admin'])) {
            $navItems[] = ['label' => 'Pengaturan', 'icon' => 'fa-cog', 'route' => 'staff.control.index', 'patterns' => ['staff.control*']];
        }
        // Note: Profil removed from sidebar, now in header dropdown
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
        // Immediately sync sidebar state to prevent flash
        (function() {
            function syncSidebarState() {
                const collapsed = localStorage.getItem('staffSidebarCollapsed') === 'true';
                if (collapsed) {
                    document.documentElement.classList.add('sidebar-collapsed');
                } else {
                    document.documentElement.classList.remove('sidebar-collapsed');
                }
            }
            
            // Sync immediately on script load
            syncSidebarState();
            
            // Also sync after Livewire navigation
            document.addEventListener('livewire:navigated', syncSidebarState);
        })();
    </script>
    @livewireStyles
    @filamentStyles
</head>
<body x-data="staffPortal()" class="antialiased bg-slate-50 font-['Inter']">
<script>document.body.classList.add('ready');</script>

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
               :class="sidebarCollapsed ? 'lg:w-20' : 'lg:w-56'">
            
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
                @livewire('staff.attendance.quick-attendance')
                @include('staff.components.quick-actions')
                
                {{-- Notification Bell --}}
                @livewire('staff.notification.notification-bell')
                
                <div class="h-8 w-px bg-slate-200"></div>
                
                {{-- Profile Dropdown --}}
                <div x-data="{ profileOpen: false }" class="relative">
                    <button @click="profileOpen = !profileOpen" class="flex items-center gap-3 hover:bg-slate-50 rounded-xl px-2 py-1.5 transition">
                        <div class="text-right hidden sm:block">
                            <p class="text-xs text-slate-400">{{ $branch?->name ?? 'Semua Cabang' }}</p>
                            <p class="text-sm font-semibold text-slate-900">{{ $user->name }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-full border-2 border-slate-200 overflow-hidden">
                            <img src="{{ $user->getAvatarUrl(100) }}" class="w-full h-full object-cover">
                        </div>
                        <i class="fas fa-chevron-down text-slate-400 text-xs transition" :class="profileOpen ? 'rotate-180' : ''"></i>
                    </button>
                    
                    {{-- Dropdown Menu --}}
                    <div x-show="profileOpen" 
                         x-cloak
                         @click.away="profileOpen = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 top-full mt-2 w-72 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden z-50">
                        
                        {{-- User Info Header --}}
                        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-14 h-14 rounded-xl border-2 border-white/30 overflow-hidden shadow-lg">
                                    <img src="{{ $user->getAvatarUrl(100) }}" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-white font-bold truncate">{{ $user->name }}</p>
                                    <p class="text-blue-200 text-sm truncate">{{ $user->email }}</p>
                                    <span class="inline-block mt-1 px-2 py-0.5 bg-white/20 text-white text-xs rounded-lg">
                                        {{ \App\Models\User::getRoles()[$user->role] ?? ucfirst($user->role) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Menu Items --}}
                        <div class="py-2">
                            <a href="{{ route('staff.profile') }}" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 transition">
                                <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">Profil Saya</p>
                                    <p class="text-xs text-slate-400">Edit informasi akun</p>
                                </div>
                            </a>
                            
                            @if(in_array($user->role, ['super_admin', 'admin']))
                            <a href="{{ route('staff.control.index') }}" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 transition">
                                <div class="w-9 h-9 bg-violet-100 rounded-lg flex items-center justify-center text-violet-600">
                                    <i class="fas fa-cog"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">Pengaturan</p>
                                    <p class="text-xs text-slate-400">Kelola staff & sistem</p>
                                </div>
                            </a>
                            @endif
                        </div>
                        
                        {{-- Logout --}}
                        <div class="border-t border-slate-100 p-2">
                            <a href="{{ route('staff.logout') }}" class="w-full flex items-center gap-3 px-3 py-2.5 hover:bg-red-50 rounded-xl transition group">
                                <div class="w-9 h-9 bg-red-100 group-hover:bg-red-200 rounded-lg flex items-center justify-center text-red-600 transition">
                                    <i class="fas fa-sign-out-alt"></i>
                                </div>
                                <div class="text-left">
                                    <p class="text-sm font-semibold text-red-600">Logout</p>
                                    <p class="text-xs text-slate-400">Keluar dari sistem</p>
                                </div>
                            </a>
                        </div>
                    </div>
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

    {{-- Mobile Bottom Nav - Native App Style --}}
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-50" x-data="{ menuOpen: false }">
        {{-- Bottom Bar --}}
        <div class="relative bg-white border-t border-gray-200 shadow-[0_-4px_20px_rgba(0,0,0,0.08)]">
            {{-- Safe area for notch devices --}}
            <div class="flex items-end justify-around px-2 pt-2 pb-safe" style="padding-bottom: max(0.5rem, env(safe-area-inset-bottom));">
                
                {{-- Dashboard --}}
                @php $dashActive = request()->routeIs('staff.dashboard*'); @endphp
                <a href="{{ route('staff.dashboard') }}" class="flex flex-col items-center gap-0.5 py-1 px-3 min-w-[60px] transition-all duration-200 {{ $dashActive ? 'text-blue-600' : 'text-gray-400' }}">
                    <div class="relative">
                        <i class="fas fa-house text-xl"></i>
                        @if($dashActive)
                        <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 bg-blue-600 rounded-full"></span>
                        @endif
                    </div>
                    <span class="text-[10px] font-medium">Home</span>
                </a>
                
                {{-- Tasks --}}
                @php $taskActive = request()->routeIs('staff.task*'); @endphp
                <a href="{{ route('staff.task.index') }}" class="flex flex-col items-center gap-0.5 py-1 px-3 min-w-[60px] transition-all duration-200 {{ $taskActive ? 'text-blue-600' : 'text-gray-400' }}">
                    <div class="relative">
                        <i class="fas fa-clipboard-list text-xl"></i>
                        @if($taskActive)
                        <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 bg-blue-600 rounded-full"></span>
                        @endif
                    </div>
                    <span class="text-[10px] font-medium">Tasks</span>
                </a>
                
                {{-- Kehadiran - Special Center Button --}}
                @php $attendActive = request()->routeIs('staff.attendance*'); @endphp
                <div class="relative -mt-6">
                    <a href="{{ route('staff.attendance.index') }}" 
                       class="flex items-center justify-center w-16 h-16 rounded-full shadow-xl transition-all duration-300 active:scale-95
                              {{ $attendActive 
                                 ? 'bg-gradient-to-br from-emerald-500 to-teal-600 ring-4 ring-emerald-100' 
                                 : 'bg-gradient-to-br from-blue-600 to-indigo-700 ring-4 ring-blue-100' }}">
                        <div class="text-white text-center">
                            <i class="fas fa-fingerprint text-2xl"></i>
                        </div>
                    </a>
                    {{-- Glow effect --}}
                    <div class="absolute inset-0 rounded-full bg-blue-500 opacity-20 blur-xl -z-10 animate-pulse"></div>
                </div>
                
                {{-- Sirkulasi --}}
                @php $circActive = request()->routeIs('staff.circulation*'); @endphp
                <a href="{{ route('staff.circulation.index') }}" class="flex flex-col items-center gap-0.5 py-1 px-3 min-w-[60px] transition-all duration-200 {{ $circActive ? 'text-blue-600' : 'text-gray-400' }}">
                    <div class="relative">
                        <i class="fas fa-arrows-rotate text-xl"></i>
                        @if($circActive)
                        <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 bg-blue-600 rounded-full"></span>
                        @endif
                    </div>
                    <span class="text-[10px] font-medium">Sirkulasi</span>
                </a>
                
                {{-- Menu --}}
                <button @click="menuOpen = true" class="flex flex-col items-center gap-0.5 py-1 px-3 min-w-[60px] text-gray-400 transition-all duration-200 hover:text-blue-600">
                    <i class="fas fa-bars text-xl"></i>
                    <span class="text-[10px] font-medium">Menu</span>
                </button>
            </div>
        </div>
        
        {{-- Full Screen Menu Drawer --}}
        <div x-show="menuOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50"
             @click="menuOpen = false"
             style="display: none;">
        </div>
        
        <div x-show="menuOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full"
             class="fixed bottom-0 left-0 right-0 bg-white rounded-t-3xl shadow-2xl z-50 max-h-[85vh] overflow-hidden"
             style="display: none;">
            
            {{-- Handle Bar --}}
            <div class="flex justify-center py-3">
                <div class="w-10 h-1 bg-gray-300 rounded-full"></div>
            </div>
            
            {{-- User Profile Header --}}
            <div class="px-5 pb-4 border-b border-gray-100">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center overflow-hidden shadow-lg">
                        <img src="{{ $user->getAvatarUrl(100) }}" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-gray-900 truncate">{{ $user->name }}</p>
                        <p class="text-sm text-gray-500 truncate">{{ $user->email }}</p>
                        <span class="inline-block mt-1 px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-medium rounded-lg">
                            {{ \App\Models\User::getRoles()[$user->role] ?? ucfirst($user->role) }}
                        </span>
                    </div>
                    <button @click="menuOpen = false" class="p-2 hover:bg-gray-100 rounded-xl transition">
                        <i class="fas fa-times text-gray-400"></i>
                    </button>
                </div>
            </div>
            
            {{-- Menu Items Grid --}}
            <div class="p-4 overflow-y-auto max-h-[50vh]">
                <div class="grid grid-cols-4 gap-3">
                    @foreach($navItems as $item)
                        @php $active = request()->routeIs($item['patterns']); @endphp
                        <a href="{{ route($item['route']) }}" 
                           @click="menuOpen = false"
                           class="flex flex-col items-center gap-2 p-3 rounded-2xl transition-all duration-200 
                                  {{ $active ? 'bg-blue-50 text-blue-600' : 'hover:bg-gray-50 text-gray-600' }}">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center transition-all
                                        {{ $active ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/30' : 'bg-gray-100' }}">
                                <i class="fas {{ $item['icon'] }} text-lg"></i>
                            </div>
                            <span class="text-[11px] font-medium text-center leading-tight">{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
            
            {{-- Quick Actions --}}
            <div class="px-4 pb-4">
                <div class="flex gap-3">
                    <a href="{{ route('staff.profile') }}" @click="menuOpen = false"
                       class="flex-1 flex items-center justify-center gap-2 py-3 bg-gray-100 hover:bg-gray-200 rounded-xl transition">
                        <i class="fas fa-user text-gray-600"></i>
                        <span class="text-sm font-medium text-gray-700">Profil</span>
                    </a>
                    <a href="{{ route('staff.logout') }}" 
                       class="flex-1 flex items-center justify-center gap-2 py-3 bg-red-50 hover:bg-red-100 rounded-xl transition">
                        <i class="fas fa-sign-out-alt text-red-600"></i>
                        <span class="text-sm font-medium text-red-600">Logout</span>
                    </a>
                </div>
            </div>
            
            {{-- Safe area spacer --}}
            <div style="height: env(safe-area-inset-bottom);"></div>
        </div>
    </nav>

    <script>
        function staffPortal() {
            return {
                // Get initial state from localStorage
                sidebarCollapsed: localStorage.getItem('staffSidebarCollapsed') === 'true',
                
                init() {
                    // Sync HTML class with Alpine state on init
                    this.syncSidebarClass();
                    
                    // Watch for changes
                    this.$watch('sidebarCollapsed', () => {
                        this.syncSidebarClass();
                    });
                },
                
                syncSidebarClass() {
                    if (this.sidebarCollapsed) {
                        document.documentElement.classList.add('sidebar-collapsed');
                    } else {
                        document.documentElement.classList.remove('sidebar-collapsed');
                    }
                },
                
                toggleSidebar() {
                    this.sidebarCollapsed = !this.sidebarCollapsed;
                    localStorage.setItem('staffSidebarCollapsed', this.sidebarCollapsed);
                }
            }
        }
    </script>
    @stack('scripts')
    @livewireScripts
    
    {{-- Skip dialog, go directly to login on session expired --}}
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.hook('request', ({ fail }) => {
                fail(({ status, preventDefault }) => {
                    if (status === 419) {
                        preventDefault();
                        window.location.href = '/login';
                    }
                });
            });
        });
    </script>
    
    @filamentScripts

    {{-- Filament Notifications --}}
    @livewire('notifications')
</body>
</html>
