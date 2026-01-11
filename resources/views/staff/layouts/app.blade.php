<!DOCTYPE html>
<html lang="id">
<head>
    {{-- CRITICAL: Sync state BEFORE anything renders --}}
    <script>
        (function() {
            var c = localStorage.getItem('staffSidebarCollapsed') === 'true';
            var d = localStorage.getItem('staffPortalDarkMode') === 'true';
            if (c) document.documentElement.classList.add('sidebar-collapsed');
            if (d) document.documentElement.classList.add('dark');
        })();
    </script>
    <x-google-analytics />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Staff Portal</title>
    
    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
    {{-- Preconnect to CDNs --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://cdn.tailwindcss.com">
    <link rel="preconnect" href="https://unpkg.com">
    
    {{-- Critical CSS - prevents FOUC + Dark Mode Variables --}}
    <style>
        /* CSS Variables for theming */
        :root {
            --bg-primary: #f8fafc;
            --bg-secondary: #ffffff;
            --bg-tertiary: #f1f5f9;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            --border-color: #e2e8f0;
            --shadow-color: rgba(0, 0, 0, 0.1);
        }
        
        html.dark {
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --bg-tertiary: #334155;
            --text-primary: #f1f5f9;
            --text-secondary: #cbd5e1;
            --text-muted: #64748b;
            --border-color: #334155;
            --shadow-color: rgba(0, 0, 0, 0.4);
        }
        
        html, body { margin: 0; padding: 0; background: var(--bg-primary); color: var(--text-primary); }
        html.dark { color-scheme: dark; }
        body { opacity: 0; transition: opacity 0.15s, background-color 0.3s, color 0.3s; }
        body.ready { opacity: 1; }
        
        /* Critical: Sidebar curves - prevent FOUC */
        @media (min-width: 1024px) {
            .staff-sidebar::before, .staff-sidebar::after {
                content: '';
                position: absolute;
                right: -32px;
                width: 32px;
                height: 32px;
                pointer-events: none;
                transition: background 0.3s;
            }
            .staff-sidebar::before { top: 0; background: radial-gradient(circle at 100% 100%, transparent 32px, #1d4ed8 32px); }
            .staff-sidebar::after { bottom: 0; background: radial-gradient(circle at 100% 0%, transparent 32px, #312e81 32px); }
            html.dark .staff-sidebar::before { background: radial-gradient(circle at 100% 100%, transparent 32px, #1e293b 32px) !important; }
            html.dark .staff-sidebar::after { background: radial-gradient(circle at 100% 0%, transparent 32px, #020617 32px) !important; }
        }
        
        /* Custom Scrollbar - Overlay style (tidak mengambil ruang) */
        html { overflow-y: scroll; }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.3); border-radius: 10px; border: 2px solid transparent; background-clip: padding-box; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(148, 163, 184, 0.5); border: 2px solid transparent; background-clip: padding-box; }
        html.dark ::-webkit-scrollbar-thumb { background: rgba(100, 116, 139, 0.3); border: 2px solid transparent; background-clip: padding-box; }
        html.dark ::-webkit-scrollbar-thumb:hover { background: rgba(100, 116, 139, 0.5); border: 2px solid transparent; background-clip: padding-box; }
        @supports (overflow: overlay) { html { overflow-y: overlay; } }
        * { scrollbar-width: thin; scrollbar-color: rgba(148, 163, 184, 0.3) transparent; }
        html.dark * { scrollbar-color: rgba(100, 116, 139, 0.3) transparent; }
    </style>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js" defer></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
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
        $isStaffOnly = $user->role === 'staff';
        $isAdminOrAbove = in_array($user->role, ['super_admin', 'admin', 'librarian']);
        
        // E-Library pending counts
        $pendingSubmissions = \App\Models\ThesisSubmission::where('status', 'submitted')->count();
        $pendingPlagiarism = \App\Models\PlagiarismCheck::where('status', 'pending')->count();
        $elibraryBadges = [];
        if ($pendingSubmissions > 0) $elibraryBadges[] = ['icon' => 'fa-upload', 'count' => $pendingSubmissions, 'color' => 'amber'];
        if ($pendingPlagiarism > 0) $elibraryBadges[] = ['icon' => 'fa-search', 'count' => $pendingPlagiarism, 'color' => 'violet'];
        
        $navItems = [
            ['label' => 'Dashboard', 'icon' => 'fa-house', 'route' => 'staff.dashboard', 'patterns' => ['staff.dashboard*']],
            ['label' => 'Tasks', 'icon' => 'fa-clipboard-list', 'route' => 'staff.task.index', 'patterns' => ['staff.task*']],
            ['label' => 'Kehadiran', 'icon' => 'fa-fingerprint', 'route' => 'staff.attendance.index', 'patterns' => ['staff.attendance*']],
            ['label' => 'Sirkulasi', 'icon' => 'fa-arrows-rotate', 'route' => 'staff.circulation.index', 'patterns' => ['staff.circulation*']],
            ['label' => 'Katalog', 'icon' => 'fa-book', 'route' => 'staff.biblio.index', 'patterns' => ['staff.biblio*']],
            ['label' => 'E-Library', 'icon' => 'fa-cloud', 'route' => 'staff.elibrary.index', 'patterns' => ['staff.elibrary*'], 'badges' => $elibraryBadges],
            ['label' => 'E-Learning', 'icon' => 'fa-graduation-cap', 'route' => 'staff.elearning.index', 'patterns' => ['staff.elearning*']],
            ['label' => 'Anggota', 'icon' => 'fa-users', 'route' => 'staff.member.index', 'patterns' => ['staff.member*']],
            ['label' => 'Berita', 'icon' => 'fa-newspaper', 'route' => 'staff.news.index', 'patterns' => ['staff.news*']],
            ['label' => 'Stock Opname', 'icon' => 'fa-clipboard-check', 'route' => 'staff.stock-opname.index', 'patterns' => ['staff.stock-opname*']],
        ];
        
        // Statistik, Analytics, Survey - only for admin/librarian
        if ($isAdminOrAbove) {
            $navItems[] = ['label' => 'Statistik', 'icon' => 'fa-chart-pie', 'route' => 'staff.statistics.index', 'patterns' => ['staff.statistics*']];
            $navItems[] = ['label' => 'Analytics', 'icon' => 'fa-chart-line', 'route' => 'staff.analytics.index', 'patterns' => ['staff.analytics*']];
            $navItems[] = ['label' => 'Survey', 'icon' => 'fa-clipboard-question', 'route' => 'staff.survey.index', 'patterns' => ['staff.survey*']];
        }
        
        // Security & Pengaturan - only for super_admin/admin
        if (in_array($user->role, ['super_admin', 'admin'])) {
            $navItems[] = ['label' => 'Email', 'icon' => 'fa-envelope', 'route' => 'staff.email.index', 'patterns' => ['staff.email*']];
            $navItems[] = ['label' => 'Security', 'icon' => 'fa-shield-alt', 'route' => 'staff.security.index', 'patterns' => ['staff.security*']];
            $navItems[] = ['label' => 'Pengaturan', 'icon' => 'fa-cog', 'route' => 'staff.control.index', 'patterns' => ['staff.control*']];
        }
    @endphp

    <link rel="stylesheet" href="{{ asset('css/staff-portal.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.default.min.css" rel="stylesheet">
    @stack('styles')

    <style>
        [x-cloak] { display: none !important; }
        
        /* Sidebar & content positioning - CSS only for instant state */
        .staff-sidebar { width: 14rem; transition: width 0.3s; }
        html.sidebar-collapsed .staff-sidebar { width: 5rem; }
        
        .main-content-wrapper { margin-left: 14rem; transition: margin-left 0.3s; }
        html.sidebar-collapsed .main-content-wrapper { margin-left: 5rem; }
        
        .desktop-header { left: 14rem; width: calc(100vw - 14rem); transition: left 0.3s, width 0.3s; }
        html.sidebar-collapsed .desktop-header { left: 5rem; width: calc(100vw - 5rem); }
        
        /* Disable transitions during navigation */
        html.navigating .staff-sidebar,
        html.navigating .main-content-wrapper,
        html.navigating .desktop-header { transition: none !important; }
        
        @media (max-width: 1023px) {
            .staff-sidebar { display: none !important; }
            .main-content-wrapper { margin-left: 0 !important; }
            .desktop-header { left: 0 !important; width: 100% !important; }
        }
        
        /* Ensure modals with high z-index appear above everything */
        .fixed[style*="z-index: 9999"],
        .fixed[style*="z-index: 10000"] {
            position: fixed !important;
        }
    </style>

    <script>
        (function() {
            function syncState() {
                var c = localStorage.getItem('staffSidebarCollapsed') === 'true';
                var d = localStorage.getItem('staffPortalDarkMode') === 'true';
                document.documentElement.classList.toggle('sidebar-collapsed', c);
                document.documentElement.classList.toggle('dark', d);
                // Sync Alpine state
                if (window.Alpine && document.body._x_dataStack) {
                    var data = document.body._x_dataStack[0];
                    if (data) {
                        data.sidebarCollapsed = c;
                        data.darkMode = d;
                    }
                }
            }
            
            document.addEventListener('livewire:navigate', function() {
                document.documentElement.classList.add('navigating');
                syncState();
            });
            
            document.addEventListener('livewire:navigated', function() {
                syncState();
                // Delay removal to ensure DOM is fully rendered
                setTimeout(function() {
                    document.documentElement.classList.remove('navigating');
                }, 50);
            });
        })();
    </script>
    @livewireStyles
    @filamentStyles
</head>
<body x-data="staffPortal()" class="antialiased font-['Inter']">
<script>document.body.classList.add('ready');</script>

    {{-- Right side corner curves --}}
    <div class="hidden lg:block fixed top-0 right-0 w-8 h-8 z-50 pointer-events-none right-curve-top"></div>
    <div class="hidden lg:block fixed bottom-0 right-0 w-8 h-8 z-50 pointer-events-none right-curve-bottom"></div>

    {{-- Mobile Header --}}
    <header class="mobile-header lg:hidden fixed top-0 left-0 right-0 h-16 flex items-center justify-between px-4 z-50 bg-gradient-to-r from-blue-700 via-blue-800 to-indigo-900 shadow-xl">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-white rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg overflow-hidden">
                <img src="{{ asset('storage/logo-portal.png') }}" alt="Logo" class="w-7 h-7 object-contain">
            </div>
            <div class="leading-tight">
                <p class="text-white text-sm font-bold">UNIDA LIBRARY</p>
                <p class="text-blue-200 text-[10px]">Admin Portal</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            {{-- Mobile Dark Mode Toggle --}}
            <button @click="toggleDarkMode()" 
                    class="w-9 h-9 rounded-full flex items-center justify-center transition-all"
                    :class="darkMode ? 'bg-amber-500/30 text-amber-300' : 'bg-white/20 text-white'"
                    title="Toggle Dark Mode">
                <i class="fas text-sm transition-transform duration-300"
                   :class="darkMode ? 'fa-sun rotate-0' : 'fa-moon rotate-0'"></i>
            </button>
            
            <div class="w-9 h-9 rounded-full bg-white/20 overflow-hidden border-2 border-white/30">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random" class="w-full h-full object-cover">
            </div>
        </div>
    </header>

    <div class="lg:flex lg:min-h-screen w-full lg:pt-0">
        {{-- Desktop Sidebar - FIXED --}}
        <aside class="staff-sidebar hidden lg:flex lg:flex-col lg:fixed lg:top-0 lg:left-0 lg:h-screen text-white/80 shadow-xl z-40"
               :class="darkMode ? 'bg-gradient-to-b from-slate-800 via-slate-900 to-slate-950' : 'bg-gradient-to-b from-blue-700 via-blue-800 to-indigo-900'">
            
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
                    <a href="{{ route($item['route']) }}" wire:navigate
                       x-data="{ showTooltip: false }"
                       @mouseenter="if(sidebarCollapsed) showTooltip = true"
                       @mouseleave="showTooltip = false"
                       class="group relative flex items-center transition-all duration-200"
                       :class="[
                           sidebarCollapsed ? 'justify-center py-1' : 'gap-3 px-3 py-2.5 rounded-xl',
                           !sidebarCollapsed && {{ $active ? 'true' : 'false' }} 
                               ? (darkMode ? 'bg-indigo-500/20 text-white' : 'bg-white/15 text-white')
                               : (!sidebarCollapsed ? (darkMode ? 'text-slate-300 hover:bg-white/5 hover:text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white') : '')
                       ]">
                        {{-- Active indicator for expanded --}}
                        <div x-show="!sidebarCollapsed" class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 rounded-r-full transition-all {{ $active ? 'opacity-100' : 'opacity-0' }}" :class="darkMode ? 'bg-indigo-400' : 'bg-white'"></div>
                        
                        {{-- Icon wrapper --}}
                        <div class="relative flex items-center justify-center flex-shrink-0 transition-all duration-200"
                             :class="{
                                 'w-11 h-11 rounded-xl': sidebarCollapsed,
                                 'w-9 h-9 rounded-lg': !sidebarCollapsed,
                                 'bg-indigo-500 ring-2 ring-indigo-300 shadow-lg shadow-indigo-500/40 text-white': sidebarCollapsed && {{ $active ? 'true' : 'false' }} && darkMode,
                                 'bg-white/30 ring-2 ring-white shadow-lg shadow-white/20 text-white': sidebarCollapsed && {{ $active ? 'true' : 'false' }} && !darkMode,
                                 'bg-indigo-500/40 shadow-lg text-white': !sidebarCollapsed && {{ $active ? 'true' : 'false' }} && darkMode,
                                 'bg-white/20 shadow-lg text-white': !sidebarCollapsed && {{ $active ? 'true' : 'false' }} && !darkMode,
                                 'bg-slate-700/50 hover:bg-slate-600/50 text-slate-300 hover:text-white': !{{ $active ? 'true' : 'false' }} && darkMode,
                                 'bg-white/10 hover:bg-white/20 text-blue-200 hover:text-white': !{{ $active ? 'true' : 'false' }} && !darkMode
                             }">
                            <i class="fas {{ $item['icon'] }} text-sm relative z-10"></i>
                            @if(!empty($item['badges']))
                            <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-amber-400 rounded-full border animate-pulse" :class="darkMode ? 'border-slate-800' : 'border-blue-800'" x-show="sidebarCollapsed"></span>
                            @endif
                            @if($active)
                            <div x-show="sidebarCollapsed" class="absolute -bottom-1.5 left-1/2 -translate-x-1/2 w-2 h-2 rounded-full shadow-lg" :class="darkMode ? 'bg-indigo-300 shadow-indigo-300/60' : 'bg-white shadow-white/50'"></div>
                            @endif
                        </div>
                        
                        {{-- Label for expanded --}}
                        <span class="text-sm font-medium transition-all duration-300"
                              :class="[sidebarCollapsed ? 'hidden' : 'block', {{ $active ? 'true' : 'false' }} ? 'text-white' : (darkMode ? 'text-slate-300 group-hover:text-white' : 'text-blue-100 group-hover:text-white')]">{{ $item['label'] }}</span>
                        
                        {{-- Tooltip teleported to body --}}
                        <template x-teleport="body">
                            <div x-show="showTooltip" 
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 translate-x-1"
                                 x-transition:enter-end="opacity-100 translate-x-0"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 class="fixed z-[99999] ml-2 px-4 py-2.5 bg-gray-900/95 backdrop-blur-sm text-white text-sm font-semibold rounded-xl shadow-2xl border border-white/10"
                                 :style="'left: ' + ($el.closest('a')?.getBoundingClientRect().right || 80) + 'px; top: ' + (($el.closest('a')?.getBoundingClientRect().top || 0) + ($el.closest('a')?.getBoundingClientRect().height || 0) / 2 - 18) + 'px;'"
                                 x-init="$watch('showTooltip', v => { if(v) $nextTick(() => { const a = $el.closest('a') || document.querySelector('a[href=\'{{ route($item['route']) }}\']'); if(a) { const r = a.getBoundingClientRect(); $el.style.left = (r.right + 8) + 'px'; $el.style.top = (r.top + r.height/2 - 18) + 'px'; } }) })">
                                <div class="flex items-center gap-2">
                                    <i class="fas {{ $item['icon'] }} text-blue-400"></i>
                                    <span>{{ $item['label'] }}</span>
                                </div>
                                <div class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-1.5 w-3 h-3 bg-gray-900/95 rotate-45 border-l border-b border-white/10"></div>
                            </div>
                        </template>
                        
                        {{-- Badges when expanded --}}
                        @if(!empty($item['badges']))
                        <div class="flex items-center gap-1 ml-auto transition-all duration-300"
                             :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                            @foreach($item['badges'] as $badge)
                            <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 text-[10px] font-bold rounded-full
                                @if($badge['color'] === 'amber') bg-amber-400 text-amber-900
                                @elseif($badge['color'] === 'violet') bg-violet-400 text-violet-900
                                @else bg-blue-400 text-blue-900 @endif">
                                <i class="fas {{ $badge['icon'] }} text-[8px]"></i>
                                {{ $badge['count'] }}
                            </span>
                            @endforeach
                        </div>
                        @endif
                    </a>
                @endforeach
            </nav>

            {{-- User Info --}}
            <div class="border-t border-white/10 p-4" x-data="{ showLogoutModal: false }">
                <button @click="showLogoutModal = true" 
                        type="button"
                        class="w-full flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-white/10 hover:bg-red-500/80 border border-white/20 hover:border-red-500 text-white transition-all">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="text-sm font-medium transition-all duration-300"
                          :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">Logout</span>
                </button>

                {{-- System Info --}}
                <div class="mt-3 text-center">
                    <a href="https://wa.me/6282117049501" target="_blank" class="group">
                        <template x-if="!sidebarCollapsed">
                            <div>
                                <p class="text-[10px] text-blue-300/70 group-hover:text-blue-200 font-medium transition">SYSTEM ILMU v2.0</p>
                                <p class="text-[9px] text-blue-400/50 group-hover:text-blue-300/70 transition">Integrated Library Management UNIDA</p>
                            </div>
                        </template>
                        <template x-if="sidebarCollapsed">
                            <div>
                                <p class="text-[9px] text-blue-300/70 group-hover:text-blue-200 font-medium transition">ILMU</p>
                                <p class="text-[8px] text-blue-400/50 group-hover:text-blue-300/70 transition">v2</p>
                            </div>
                        </template>
                    </a>
                </div>

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
        <header class="desktop-header hidden lg:flex items-center justify-between px-8 shadow-sm fixed top-0 z-30 h-[72px]"
                :class="darkMode ? 'bg-slate-800 border-b border-slate-700' : 'bg-white border-b border-slate-200'">
            <div class="flex items-center gap-4">
                <button @click="toggleSidebar()" 
                        class="w-9 h-9 rounded-lg border flex items-center justify-center transition-all"
                        :class="darkMode ? 'border-slate-600 text-slate-400 hover:text-slate-200 hover:border-slate-500' : 'border-slate-200 text-slate-500 hover:text-slate-900 hover:border-slate-400'">
                    <i class="fas" :class="sidebarCollapsed ? 'fa-angles-right' : 'fa-angles-left'"></i>
                </button>
                <div class="h-8 w-px" :class="darkMode ? 'bg-slate-600' : 'bg-slate-200'"></div>
                <div>
                    <h2 class="text-lg font-semibold leading-tight" :class="darkMode ? 'text-slate-100' : 'text-slate-900'">@yield('title', 'Dashboard')</h2>
                    <p class="text-xs" :class="darkMode ? 'text-slate-400' : 'text-slate-500'">{{ $currentDate }}</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                @include('staff.components.portal-switcher')
                @livewire('staff.attendance.quick-attendance')
                @include('staff.components.quick-actions')
                
                {{-- Notification Bell --}}
                @livewire('staff.notification.notification-bell')
                
                {{-- Dark Mode Toggle --}}
                <button @click="toggleDarkMode()" 
                        class="relative w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300 group"
                        :class="darkMode ? 'bg-slate-700 hover:bg-slate-600 text-amber-400' : 'bg-slate-100 hover:bg-slate-200 text-slate-600'"
                        title="Toggle Dark Mode">
                    {{-- Sun Icon --}}
                    <i class="fas fa-sun text-lg absolute transition-all duration-300"
                       :class="darkMode ? 'opacity-100 rotate-0 scale-100' : 'opacity-0 -rotate-90 scale-0'"></i>
                    {{-- Moon Icon --}}
                    <i class="fas fa-moon text-lg absolute transition-all duration-300"
                       :class="darkMode ? 'opacity-0 rotate-90 scale-0' : 'opacity-100 rotate-0 scale-100'"></i>
                </button>
                
                {{-- Online Staff Indicator (Super Admin & Admin) --}}
                @if(in_array($user->role, ['super_admin', 'admin']))
                @livewire('staff.online-staff-indicator')
                @endif
                
                <div class="h-8 w-px" :class="darkMode ? 'bg-slate-600' : 'bg-slate-200'"></div>
                
                {{-- Profile Dropdown --}}
                <div x-data="{ profileOpen: false }" class="relative">
                    <button @click="profileOpen = !profileOpen" 
                            class="flex items-center gap-3 rounded-xl px-2 py-1.5 transition"
                            :class="darkMode ? 'hover:bg-slate-700' : 'hover:bg-slate-50'">
                        <div class="text-right hidden sm:block">
                            <p class="text-xs" :class="darkMode ? 'text-slate-400' : 'text-slate-400'">{{ $branch?->name ?? 'Semua Cabang' }}</p>
                            <p class="text-sm font-semibold leading-tight" :class="darkMode ? 'text-slate-100' : 'text-slate-900'">{{ $user->name }}</p>
                            <span style="font-size:9px;padding:1px 6px;background:{{ $user->role === 'super_admin' ? '#ef4444' : ($user->role === 'admin' ? '#f59e0b' : '#3b82f6') }};color:#fff;border-radius:3px;font-weight:600">{{ $user->role === 'super_admin' ? 'SUPER ADMIN' : ($user->role === 'admin' ? 'ADMIN' : 'STAF') }}</span>
                        </div>
                        <div class="w-10 h-10 rounded-full border-2 overflow-hidden" :class="darkMode ? 'border-slate-600' : 'border-slate-200'">
                            <img src="{{ $user->getAvatarUrl(100) }}" class="w-full h-full object-cover">
                        </div>
                        <i class="fas fa-chevron-down text-xs transition" 
                           :class="[profileOpen ? 'rotate-180' : '', darkMode ? 'text-slate-400' : 'text-slate-400']"></i>
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
                         class="absolute right-0 top-full mt-2 w-72 rounded-2xl shadow-2xl overflow-hidden z-50"
                         :class="darkMode ? 'bg-slate-800 border border-slate-700' : 'bg-white border border-slate-100'">
                        
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

        {{-- Main Content Wrapper - background controlled by CSS dark mode --}}
        <div class="flex-1 flex flex-col min-h-screen lg:pt-0 lg:pb-0 main-content-wrapper overflow-x-hidden bg-slate-50">
            <main class="flex-1 w-full max-w-full px-4 pt-20 pb-24 sm:px-6 lg:px-8 lg:pt-4 lg:pb-8 overflow-x-hidden">
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
        <div class="relative border-t shadow-[0_-4px_20px_rgba(0,0,0,0.08)] transition-colors duration-300"
             :class="darkMode ? 'bg-slate-800 border-slate-700' : 'bg-white border-gray-200'">
            {{-- Safe area for notch devices --}}
            <div class="flex items-end justify-around px-2 pt-2 pb-safe" style="padding-bottom: max(0.5rem, env(safe-area-inset-bottom));">
                
                {{-- Dashboard --}}
                @php $dashActive = request()->routeIs('staff.dashboard*'); @endphp
                <a href="{{ route('staff.dashboard') }}" 
                   class="flex flex-col items-center gap-0.5 py-1 px-3 min-w-[60px] transition-all duration-200"
                   :class="'{{ $dashActive }}' === '1' ? 'text-blue-500' : (darkMode ? 'text-slate-400' : 'text-gray-400')">
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
                <a href="{{ route('staff.task.index') }}" 
                   class="flex flex-col items-center gap-0.5 py-1 px-3 min-w-[60px] transition-all duration-200"
                   :class="'{{ $taskActive }}' === '1' ? 'text-blue-500' : (darkMode ? 'text-slate-400' : 'text-gray-400')">
                    <div class="relative">
                        <i class="fas fa-clipboard-list text-xl"></i>
                        @if($taskActive)
                        <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 bg-blue-500 rounded-full"></span>
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
                <a href="{{ route('staff.circulation.index') }}" 
                   class="flex flex-col items-center gap-0.5 py-1 px-3 min-w-[60px] transition-all duration-200"
                   :class="'{{ $circActive }}' === '1' ? 'text-blue-500' : (darkMode ? 'text-slate-400' : 'text-gray-400')">
                    <div class="relative">
                        <i class="fas fa-arrows-rotate text-xl"></i>
                        @if($circActive)
                        <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 bg-blue-500 rounded-full"></span>
                        @endif
                    </div>
                    <span class="text-[10px] font-medium">Sirkulasi</span>
                </a>
                
                {{-- Menu --}}
                <button @click="menuOpen = true" 
                        class="flex flex-col items-center gap-0.5 py-1 px-3 min-w-[60px] transition-all duration-200"
                        :class="darkMode ? 'text-slate-400 hover:text-blue-400' : 'text-gray-400 hover:text-blue-600'">
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
             class="fixed bottom-0 left-0 right-0 rounded-t-3xl shadow-2xl z-50 max-h-[85vh] overflow-hidden transition-colors duration-300"
             :class="darkMode ? 'bg-slate-800' : 'bg-white'"
             style="display: none;">
            
            {{-- Handle Bar --}}
            <div class="flex justify-center py-3">
                <div class="w-10 h-1 rounded-full" :class="darkMode ? 'bg-slate-600' : 'bg-gray-300'"></div>
            </div>
            
            {{-- User Profile Header --}}
            <div class="px-5 pb-4 border-b" :class="darkMode ? 'border-slate-700' : 'border-gray-100'">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center overflow-hidden shadow-lg">
                        <img src="{{ $user->getAvatarUrl(100) }}" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold truncate" :class="darkMode ? 'text-slate-100' : 'text-gray-900'">{{ $user->name }}</p>
                        <p class="text-sm truncate" :class="darkMode ? 'text-slate-400' : 'text-gray-500'">{{ $user->email }}</p>
                        <span class="inline-block mt-1 px-2 py-0.5 text-xs font-medium rounded-lg"
                              :class="darkMode ? 'bg-blue-900/50 text-blue-300' : 'bg-blue-100 text-blue-700'">
                            {{ \App\Models\User::getRoles()[$user->role] ?? ucfirst($user->role) }}
                        </span>
                    </div>
                    <button @click="menuOpen = false" 
                            class="p-2 rounded-xl transition"
                            :class="darkMode ? 'hover:bg-slate-700 text-slate-400' : 'hover:bg-gray-100 text-gray-400'">
                        <i class="fas fa-times"></i>
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
                           class="relative flex flex-col items-center gap-2 p-3 rounded-2xl transition-all duration-200"
                           :class="'{{ $active }}' === '1' 
                               ? (darkMode ? 'bg-blue-900/30 text-blue-400' : 'bg-blue-50 text-blue-600') 
                               : (darkMode ? 'hover:bg-slate-700 text-slate-300' : 'hover:bg-gray-50 text-gray-600')">
                            <div class="relative w-12 h-12 rounded-xl flex items-center justify-center transition-all"
                                 :class="'{{ $active }}' === '1' 
                                     ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/30' 
                                     : (darkMode ? 'bg-slate-700' : 'bg-gray-100')">
                                <i class="fas {{ $item['icon'] }} text-lg"></i>
                                @if(!empty($item['badges']))
                                <span class="absolute -top-1 -right-1 w-3 h-3 bg-amber-400 rounded-full border-2 border-white animate-pulse"></span>
                                @endif
                            </div>
                            <span class="text-[11px] font-medium text-center leading-tight">{{ $item['label'] }}</span>
                            @if(!empty($item['badges']))
                            <div class="flex items-center gap-0.5">
                                @foreach($item['badges'] as $badge)
                                <span class="inline-flex items-center gap-0.5 px-1 py-0.5 text-[8px] font-bold rounded
                                    @if($badge['color'] === 'amber') bg-amber-100 text-amber-700
                                    @elseif($badge['color'] === 'violet') bg-violet-100 text-violet-700
                                    @else bg-blue-100 text-blue-700 @endif">
                                    <i class="fas {{ $badge['icon'] }}"></i>{{ $badge['count'] }}
                                </span>
                                @endforeach
                            </div>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
            
            {{-- Quick Actions --}}
            <div class="px-4 pb-4">
                <div class="flex gap-3">
                    <a href="{{ route('staff.profile') }}" @click="menuOpen = false"
                       class="flex-1 flex items-center justify-center gap-2 py-3 rounded-xl transition"
                       :class="darkMode ? 'bg-slate-700 hover:bg-slate-600 text-slate-300' : 'bg-gray-100 hover:bg-gray-200 text-gray-700'">
                        <i class="fas fa-user"></i>
                        <span class="text-sm font-medium">Profil</span>
                    </a>
                    <a href="{{ route('staff.logout') }}" 
                       class="flex-1 flex items-center justify-center gap-2 py-3 rounded-xl transition"
                       :class="darkMode ? 'bg-red-900/30 hover:bg-red-900/50 text-red-400' : 'bg-red-50 hover:bg-red-100 text-red-600'">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="text-sm font-medium">Logout</span>
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
                // Read from HTML class (already set by inline script in <head>)
                sidebarCollapsed: document.documentElement.classList.contains('sidebar-collapsed'),
                darkMode: document.documentElement.classList.contains('dark'),
                menuOpen: false,
                
                toggleSidebar() {
                    this.sidebarCollapsed = !this.sidebarCollapsed;
                    localStorage.setItem('staffSidebarCollapsed', this.sidebarCollapsed);
                    document.documentElement.classList.toggle('sidebar-collapsed', this.sidebarCollapsed);
                },
                
                toggleDarkMode() {
                    this.darkMode = !this.darkMode;
                    localStorage.setItem('staffPortalDarkMode', this.darkMode);
                    document.documentElement.classList.toggle('dark', this.darkMode);
                }
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    
    {{-- Global Image Preview Modal --}}
    <div id="globalImageModal" onclick="if(event.target===this)closeGlobalImage()" style="display:none;position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,0.9);justify-content:center;align-items:center;">
        <img id="globalImageSrc" style="max-width:90vw;max-height:90vh;border-radius:8px;">
        <button onclick="closeGlobalImage()" style="position:absolute;top:20px;right:20px;background:rgba(255,255,255,0.2);border:none;width:44px;height:44px;border-radius:50%;color:#fff;font-size:20px;cursor:pointer;">✕</button>
    </div>
    <script>
    function openGlobalImage(src){document.getElementById('globalImageSrc').src=src;document.getElementById('globalImageModal').style.display='flex';}
    function closeGlobalImage(){document.getElementById('globalImageModal').style.display='none';}
    document.addEventListener('keydown',function(e){if(e.key==='Escape')closeGlobalImage();});
    </script>
    
    {{-- Global Voice Bar (outside Livewire) --}}
    <div id="globalVoiceBar" class="hidden"></div>
    
    {{-- Global Voice Recorder --}}
    <script>
    window.VoiceRecorder = {
        recording: false, hasRecording: false, mediaRecorder: null, stream: null, chunks: [], timer: null,
        recordingTime: 0, finalDuration: 0, audioBlob: null, audioUrl: null, audio: new Audio(), isPlaying: false, wire: null,
        
        init(wire) { this.wire = wire; },
        
        start() {
            navigator.mediaDevices.getUserMedia({ audio: true }).then(stream => {
                this.stream = stream;
                this.mediaRecorder = new MediaRecorder(stream, { mimeType: MediaRecorder.isTypeSupported('audio/webm') ? 'audio/webm' : 'audio/mp4' });
                this.chunks = [];
                this.mediaRecorder.ondataavailable = e => { if(e.data.size > 0) this.chunks.push(e.data); };
                this.mediaRecorder.onstop = () => this.onStop();
                this.mediaRecorder.start(1000);
                this.recording = true;
                this.recordingTime = 0;
                this.showBar();
                this.timer = setInterval(() => { this.recordingTime++; this.updateTime(); if(this.recordingTime >= 180) this.stop(); }, 1000);
            }).catch(e => alert('Tidak dapat mengakses mikrofon'));
        },
        
        stop() { if(this.mediaRecorder && this.recording) { this.finalDuration = this.recordingTime; this.mediaRecorder.stop(); this.recording = false; clearInterval(this.timer); } },
        
        onStop() { this.stream.getTracks().forEach(t => t.stop()); this.audioBlob = new Blob(this.chunks, { type: this.mediaRecorder.mimeType }); this.audioUrl = URL.createObjectURL(this.audioBlob); this.audio.src = this.audioUrl; this.hasRecording = true; this.showBar(); },
        
        play() { if(this.isPlaying) { this.audio.pause(); this.isPlaying = false; } else { this.audio.play(); this.isPlaying = true; } this.audio.onended = () => { this.isPlaying = false; this.showBar(); }; this.showBar(); },
        
        cancel() { if(this.recording) { this.mediaRecorder?.stop(); this.stream?.getTracks().forEach(t => t.stop()); clearInterval(this.timer); this.recording = false; } this.hasRecording = false; if(this.audioUrl) URL.revokeObjectURL(this.audioUrl); this.audioBlob = null; this.audioUrl = null; this.hideBar(); },
        
        send() { const reader = new FileReader(); reader.onloadend = () => { this.wire?.sendVoice(reader.result, this.finalDuration); this.cancel(); }; reader.readAsDataURL(this.audioBlob); },
        
        showBar() {
            const bar = document.getElementById('globalVoiceBar');
            const anchor = document.getElementById('voiceBarAnchor');
            if(anchor) {
                const rect = anchor.getBoundingClientRect();
                bar.style.cssText = `position:fixed;left:${rect.left}px;top:${rect.top - 60}px;width:${rect.width || 400}px;z-index:9999;`;
            }
            bar.className = '';
            bar.innerHTML = this.recording ? 
                `<div class="flex items-center gap-3 px-4 py-2.5 bg-red-50 border border-red-200 rounded-xl shadow-lg"><div class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div><span id="vTime" class="text-sm font-bold text-red-600">${this.fmt(this.recordingTime)}</span><div class="flex-1"></div><button onclick="VoiceRecorder.cancel()" class="p-2 text-gray-400 hover:text-red-500"><i class="fas fa-trash"></i></button><button onclick="VoiceRecorder.stop()" class="w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center"><i class="fas fa-stop text-xs"></i></button></div>` :
                `<div class="flex items-center gap-3 px-4 py-2.5 bg-blue-50 border border-blue-200 rounded-xl shadow-lg"><button onclick="VoiceRecorder.play()" class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center"><i class="fas fa-${this.isPlaying?'pause':'play'} text-xs"></i></button><span class="text-sm font-bold text-blue-600">${this.fmt(this.finalDuration)}</span><div class="flex-1"></div><button onclick="VoiceRecorder.cancel()" class="p-2 text-gray-400 hover:text-red-500"><i class="fas fa-trash"></i></button><button onclick="VoiceRecorder.send()" class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center"><i class="fas fa-paper-plane text-xs"></i></button></div>`;
        },
        
        updateTime() { const el = document.getElementById('vTime'); if(el) el.textContent = this.fmt(this.recordingTime); },
        hideBar() { document.getElementById('globalVoiceBar').className = 'hidden'; },
        fmt(s) { return Math.floor(s/60).toString().padStart(2,'0') + ':' + (s%60).toString().padStart(2,'0'); }
    };
    </script>
    
    {{-- SweetAlert2 Custom Styling --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    .swal2-container { z-index: 9999999 !important; }
    .swal2-popup.swal-custom {
        border-radius: 1.5rem !important;
        padding: 2rem !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
    }
    .swal2-popup.swal-custom .swal2-title {
        font-size: 1.25rem !important;
        font-weight: 700 !important;
        color: #1f2937 !important;
    }
    .swal2-popup.swal-custom .swal2-html-container {
        font-size: 0.95rem !important;
        color: #6b7280 !important;
    }
    .swal2-popup.swal-custom .swal2-actions {
        gap: 0.75rem !important;
        margin-top: 1.5rem !important;
    }
    .swal2-popup.swal-custom .swal2-confirm,
    .swal2-popup.swal-custom .swal2-cancel {
        border-radius: 0.75rem !important;
        padding: 0.75rem 1.5rem !important;
        font-weight: 600 !important;
        font-size: 0.875rem !important;
        min-width: 100px !important;
    }
    .swal2-popup.swal-custom .swal2-cancel {
        background-color: #f3f4f6 !important;
        color: #4b5563 !important;
    }
    .swal2-popup.swal-custom .swal2-cancel:hover {
        background-color: #e5e7eb !important;
    }
    .swal2-popup.swal-custom .swal2-icon {
        margin: 1rem auto !important;
    }
    /* Toast styling */
    .swal2-popup.swal-toast {
        padding: 0.75rem 1rem !important;
        border-radius: 0.75rem !important;
        font-size: 0.875rem !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
    }
    .swal2-popup.swal-toast .swal2-title {
        font-size: 0.875rem !important;
        font-weight: 500 !important;
        margin: 0 !important;
    }
    .swal2-popup.swal-toast .swal2-icon {
        width: 1.5rem !important;
        height: 1.5rem !important;
        margin: 0 0.5rem 0 0 !important;
        border-width: 2px !important;
    }
    .swal2-popup.swal-toast .swal2-icon .swal2-icon-content {
        font-size: 0.875rem !important;
    }
    </style>
    <script>
    // Custom SweetAlert config
    const SwalCustom = Swal.mixin({
        customClass: { popup: 'swal-custom' },
        buttonsStyling: true,
        reverseButtons: true,
        focusCancel: true,
    });
    
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        customClass: { popup: 'swal-toast' }
    });
    
    // Global confirm function for Livewire
    window.confirmAction = function(options, callback) {
        SwalCustom.fire({
            title: options.title || 'Konfirmasi',
            text: options.text || '',
            icon: options.icon || 'question',
            showCancelButton: true,
            confirmButtonColor: options.confirmColor || '#10b981',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: options.confirmText || 'Ya, Lanjutkan',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed && callback) callback();
        });
    };
    
    // Listen for Livewire notifications
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('notify', (data) => {
            const config = data[0] || data;
            Toast.fire({ icon: config.type || 'success', title: config.message });
        });
    });
    </script>
    
    {{-- Push Notification Subscription --}}
    <script>
    const vapidPublicKey = '{{ config("webpush.vapid.public_key") }}';
    
    if ('serviceWorker' in navigator && 'PushManager' in window) {
        navigator.serviceWorker.register('/sw.js').then(function(reg) {
            reg.pushManager.getSubscription().then(function(sub) {
                if (!sub) {
                    // Ask for permission after 3 seconds
                    setTimeout(() => {
                        if (Notification.permission === 'default') {
                            Notification.requestPermission().then(function(permission) {
                                if (permission === 'granted') subscribePush(reg);
                            });
                        } else if (Notification.permission === 'granted') {
                            subscribePush(reg);
                        }
                    }, 3000);
                }
            });
        });
    }
    
    function subscribePush(reg) {
        reg.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(vapidPublicKey)
        }).then(function(sub) {
            fetch('/staff/push-subscription', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify(sub.toJSON())
            });
        });
    }
    
    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);
        for (let i = 0; i < rawData.length; ++i) outputArray[i] = rawData.charCodeAt(i);
        return outputArray;
    }
    </script>
</body>
</html>
