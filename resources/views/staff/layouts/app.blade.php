<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Staff Portal' }} - Perpustakaan UNIDA</title>

    {{-- QR Scanner Library - Global --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>

    {{-- Fonts & Icons --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    
    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50: '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe', 300: '#93c5fd', 400: '#60a5fa', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8', 800: '#1e40af', 900: '#1e3a8a' },
                    }
                }
            }
        }
    </script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        
        /* Sidebar Animation */
        .sidebar-transition {
            transition: width 0.3s ease-in-out, transform 0.3s ease-in-out;
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        /* Sidebar collapsed state */
        .sidebar-collapsed { width: 80px; }
        .sidebar-collapsed .sidebar-text { display: none; }
        .sidebar-collapsed .sidebar-icon { margin-right: 0; }
        .sidebar-collapsed .sidebar-header-text { display: none; }
        
        /* Hide collapse button text on collapsed */
        .sidebar-collapsed .collapse-text { display: none; }
        
        /* Mobile sidebar */
        @media (max-width: 1023px) {
            .sidebar-mobile-hidden { transform: translateX(-100%); }
        }
        
        /* Smooth transitions */
        .nav-link {
            transition: all 0.2s ease-in-out;
        }
        
        /* Top navigation dropdown on hover */
        .nav-dropdown:hover .nav-dropdown-content {
            display: block;
        }

        /* Main content area */
        .main-content {
            min-height: calc(100vh - 64px);
        }
        
        /* Popup styling */
        .popup-enter { animation: popupEnter 0.2s ease-out forwards; }
        .popup-leave { animation: popupLeave 0.15s ease-in forwards; }
        @keyframes popupEnter { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        @keyframes popupLeave { from { opacity: 1; transform: scale(1); } to { opacity: 0; transform: scale(0.95); } }
        
        /* Toast styling for Quick Attendance popup */
        .qa-toast {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 99999;
            padding: 1rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 500;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: toastPop 0.3s ease-out;
        }
        @keyframes toastPop {
            0% { opacity: 0; transform: translate(-50%, -50%) scale(0.9); }
            100% { opacity: 1; transform: translate(-50%, -50%) scale(1); }
        }
        .qa-toast-success { background: linear-gradient(135deg, #10b981, #059669); color: white; }
        .qa-toast-error { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; }
        .qa-toast-info { background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; }
    </style>
    
    @filamentStyles
    @livewireStyles
    @stack('styles')
</head>
<body class="bg-gray-50 antialiased" 
      x-data="{ 
          sidebarOpen: window.innerWidth >= 1024,
          sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
          mobileMenuOpen: false,
          activeSubmenu: null,
          
          toggleSidebar() {
              if (window.innerWidth < 1024) {
                  this.mobileMenuOpen = !this.mobileMenuOpen;
              } else {
                  this.sidebarCollapsed = !this.sidebarCollapsed;
                  localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
              }
          },
          
          closeMobileMenu() {
              this.mobileMenuOpen = false;
          }
      }"
      @resize.window="sidebarOpen = window.innerWidth >= 1024"
      @keydown.escape="mobileMenuOpen = false"
>
    {{-- Mobile Overlay --}}
    <div x-show="mobileMenuOpen" 
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="closeMobileMenu()"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 lg:hidden"
         style="display: none;"></div>

    {{-- Sidebar --}}
    <aside class="fixed top-0 left-0 h-screen bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 text-white z-50 sidebar-transition overflow-hidden shadow-2xl"
           :class="{
               'w-72': !sidebarCollapsed,
               'sidebar-collapsed': sidebarCollapsed,
               'sidebar-mobile-hidden': !mobileMenuOpen,
               'w-72': mobileMenuOpen
           }"
           x-show="sidebarOpen || mobileMenuOpen"
           x-transition:enter="transition ease-out duration-300"
           x-transition:enter-start="-translate-x-full lg:translate-x-0"
           x-transition:enter-end="translate-x-0">
        
        {{-- Header --}}
        <div class="flex items-center justify-between h-16 px-4 border-b border-white/10 bg-gradient-to-r from-blue-600/20 to-indigo-600/20">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-book-reader text-white text-lg"></i>
                </div>
                <div class="sidebar-text">
                    <h1 class="font-bold text-white text-sm tracking-wide sidebar-header-text">PERPUSTAKAAN</h1>
                    <p class="text-[10px] text-blue-300 sidebar-header-text">UNIDA Gontor</p>
                </div>
            </div>
            <button @click="toggleSidebar()" class="p-2 hover:bg-white/10 rounded-lg transition lg:block hidden">
                <i class="fas fa-chevron-left text-gray-400 text-sm transition-transform duration-300" :class="sidebarCollapsed ? 'rotate-180' : ''"></i>
            </button>
            <button @click="closeMobileMenu()" class="p-2 hover:bg-white/10 rounded-lg transition lg:hidden">
                <i class="fas fa-times text-gray-400"></i>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="p-3 space-y-1 overflow-y-auto h-[calc(100vh-64px)]">
            @php
                $currentRoute = request()->route()?->getName() ?? '';
                $navItems = [
                    ['route' => 'staff.dashboard', 'icon' => 'fa-home', 'label' => 'Dashboard', 'roles' => ['super_admin', 'admin', 'librarian', 'staff']],
                    ['route' => 'staff.member.index', 'icon' => 'fa-users', 'label' => 'Anggota', 'roles' => ['super_admin', 'admin', 'librarian']],
                    ['route' => 'staff.attendance', 'icon' => 'fa-calendar-check', 'label' => 'Kehadiran', 'roles' => ['super_admin', 'admin', 'librarian', 'staff']],
                    ['route' => 'staff.statistics', 'icon' => 'fa-chart-bar', 'label' => 'Statistik', 'roles' => ['super_admin', 'admin', 'librarian']],
                    ['route' => 'staff.task.index', 'icon' => 'fa-tasks', 'label' => 'Tugas', 'roles' => ['super_admin', 'admin', 'librarian', 'staff']],
                    ['route' => 'staff.profile', 'icon' => 'fa-cog', 'label' => 'Profil', 'roles' => ['super_admin', 'admin', 'librarian', 'staff']],
                ];
                $userRole = auth()->user()?->role ?? 'staff';
            @endphp
            
            @foreach($navItems as $item)
                @if(in_array($userRole, $item['roles']))
                    <a href="{{ route($item['route']) }}" 
                       class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group
                              {{ str_starts_with($currentRoute, $item['route']) 
                                 ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/30' 
                                 : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                        <i class="fas {{ $item['icon'] }} w-5 text-center sidebar-icon {{ str_starts_with($currentRoute, $item['route']) ? '' : 'text-gray-400 group-hover:text-white' }}"></i>
                        <span class="sidebar-text font-medium text-sm">{{ $item['label'] }}</span>
                    </a>
                @endif
            @endforeach

            {{-- Divider --}}
            <div class="my-4 border-t border-white/10"></div>

            {{-- Admin Panel Link (for super_admin only) --}}
            @if(auth()->user()?->role === 'super_admin')
                <a href="/control" 
                   class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-amber-500/20 hover:text-amber-400 transition-all duration-200 group">
                    <i class="fas fa-shield-alt w-5 text-center sidebar-icon text-amber-500"></i>
                    <span class="sidebar-text font-medium text-sm">Admin Panel</span>
                </a>
            @endif

            {{-- OPAC Link --}}
            <a href="/" target="_blank"
               class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-white/10 hover:text-white transition-all duration-200 group">
                <i class="fas fa-globe w-5 text-center sidebar-icon text-gray-400 group-hover:text-white"></i>
                <span class="sidebar-text font-medium text-sm">Lihat OPAC</span>
                <i class="fas fa-external-link-alt text-xs ml-auto sidebar-text text-gray-500"></i>
            </a>
        </nav>
    </aside>

    {{-- Main Content Wrapper --}}
    <div class="lg:transition-all lg:duration-300"
         :class="{
             'lg:ml-72': !sidebarCollapsed,
             'lg:ml-20': sidebarCollapsed
         }">
        
        {{-- Top Navbar --}}
        <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-lg border-b border-gray-100 shadow-sm">
            <div class="flex items-center justify-between h-16 px-4 lg:px-6">
                {{-- Left: Hamburger & Title --}}
                <div class="flex items-center gap-4">
                    <button @click="toggleSidebar()" class="p-2 hover:bg-gray-100 rounded-xl transition lg:hidden">
                        <i class="fas fa-bars text-gray-600"></i>
                    </button>
                    <div class="hidden lg:block">
                        <h2 class="font-semibold text-gray-800">{{ $pageTitle ?? 'Dashboard' }}</h2>
                        <p class="text-xs text-gray-500">{{ now()->translatedFormat('l, d F Y') }}</p>
                    </div>
                </div>

                {{-- Right: Actions --}}
                <div class="flex items-center gap-2">
                    {{-- Quick Attendance Widget --}}
                    @livewire('staff.attendance.quick-attendance')
                    
                    {{-- Notifications --}}
                    <button class="relative p-2.5 hover:bg-gray-100 rounded-xl transition">
                        <i class="fas fa-bell text-gray-600"></i>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>

                    {{-- User Menu --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" 
                                class="flex items-center gap-3 pl-2 pr-3 py-1.5 hover:bg-gray-100 rounded-xl transition">
                            <div class="w-9 h-9 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                                @if(auth()->user()?->avatar)
                                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" class="w-full h-full object-cover rounded-xl">
                                @else
                                    <span class="text-white font-semibold text-sm">{{ substr(auth()->user()?->name ?? 'U', 0, 1) }}</span>
                                @endif
                            </div>
                            <div class="hidden sm:block text-left">
                                <p class="font-medium text-gray-800 text-sm">{{ Str::words(auth()->user()?->name ?? 'User', 2, '') }}</p>
                                <p class="text-xs text-gray-500 capitalize">{{ str_replace('_', ' ', auth()->user()?->role ?? 'Staff') }}</p>
                            </div>
                            <i class="fas fa-chevron-down text-xs text-gray-400 hidden sm:block"></i>
                        </button>
                        
                        {{-- Dropdown --}}
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-2"
                             class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50"
                             style="display: none;">
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="font-semibold text-gray-800">{{ auth()->user()?->name }}</p>
                                <p class="text-sm text-gray-500">{{ auth()->user()?->email }}</p>
                            </div>
                            <a href="{{ route('staff.settings') }}" class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition">
                                <i class="fas fa-user-cog text-gray-400 w-5"></i>
                                <span class="text-gray-700">Profil Saya</span>
                            </a>
                            <a href="{{ route('staff.settings') }}" class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition">
                                <i class="fas fa-cog text-gray-400 w-5"></i>
                                <span class="text-gray-700">Pengaturan</span>
                            </a>
                            <div class="border-t border-gray-100 mt-2 pt-2">
                                <a href="{{ route('staff.logout') }}"
                                   class="flex items-center gap-3 px-4 py-2.5 hover:bg-red-50 text-red-600 transition">
                                    <i class="fas fa-sign-out-alt w-5"></i>
                                    <span>Keluar</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Main Content --}}
        <main class="main-content p-4 lg:p-6">
            {{ $slot }}
        </main>

        {{-- Footer --}}
        <footer class="border-t border-gray-100 bg-white/50 py-4 px-6">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-2 text-sm text-gray-500">
                <p>© {{ date('Y') }} IT Perpustakaan UNIDA Gontor</p>
                <p class="flex items-center gap-1">
                    <i class="fas fa-code text-gray-400"></i> 
                    <span>v1.0.0</span>
                </p>
            </div>
        </footer>
    </div>

    @stack('scripts')
    @livewireScripts
    
    {{-- Session Expired Modal --}}
    <div id="session-expired-modal" class="fixed inset-0 hidden" style="z-index: 999999 !important;">
        {{-- Backdrop - blocks everything --}}
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
        
        {{-- Modal Content --}}
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="session-modal-content">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-blue-500 to-blue-700 px-6 py-5 text-center">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-clock text-3xl text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white">Sesi Berakhir</h3>
                </div>
                
                {{-- Body --}}
                <div class="p-6 text-center">
                    <p class="text-gray-600 mb-2">Sesi Anda telah berakhir karena tidak ada aktivitas.</p>
                    <p class="text-sm text-gray-400">Silakan klik tombol di bawah untuk login kembali.</p>
                </div>
                
                {{-- Footer --}}
                <div class="px-6 pb-6">
                    <a href="/login" id="session-redirect-btn"
                       class="w-full py-3 bg-gradient-to-r from-blue-500 to-blue-700 hover:from-blue-600 hover:to-blue-800 text-white font-bold rounded-xl transition flex items-center justify-center gap-2 no-underline block text-center">
                        <i class="fas fa-sign-in-alt"></i>
                        Login Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Handle session expired (419 error) - MUST be before filamentScripts --}}
    <script>
        // Global flag - checked by everything
        window.sessionExpired = false;
        
        function showSessionExpiredModal() {
            // Prevent multiple triggers
            if (window.sessionExpired) return;
            window.sessionExpired = true;
            
            // Stop all Livewire activity if possible
            try {
                if (window.Livewire && window.Livewire.stop) {
                    window.Livewire.stop();
                }
            } catch(e) {}
            
            const modal = document.getElementById('session-expired-modal');
            const content = document.getElementById('session-modal-content');
            if (modal && content) {
                modal.classList.remove('hidden');
                // Animate modal appearance
                setTimeout(() => {
                    content.classList.remove('scale-95', 'opacity-0');
                    content.classList.add('scale-100', 'opacity-100');
                }, 10);
            }
        }
        
        // Intercept 419 at XMLHttpRequest level EARLY (catches ALL AJAX including Filament)
        (function() {
            const originalXHROpen = XMLHttpRequest.prototype.open;
            const originalXHRSend = XMLHttpRequest.prototype.send;
            
            XMLHttpRequest.prototype.open = function() {
                this._url = arguments[1];
                return originalXHROpen.apply(this, arguments);
            };
            
            XMLHttpRequest.prototype.send = function() {
                const self = this;
                this.addEventListener('readystatechange', function() {
                    if (self.readyState === 4 && self.status === 419 && !window.sessionExpired) {
                        showSessionExpiredModal();
                    }
                });
                return originalXHRSend.apply(this, arguments);
            };
        })();
        
        // Also intercept fetch API
        (function() {
            const originalFetch = window.fetch;
            window.fetch = function(...args) {
                return originalFetch.apply(this, args).then(response => {
                    if (response.status === 419 && !window.sessionExpired) {
                        showSessionExpiredModal();
                    }
                    return response;
                });
            };
        })();
        
        // Livewire hook as backup
        document.addEventListener('livewire:init', () => {
            Livewire.hook('request', ({ fail }) => {
                fail(({ status, preventDefault }) => {
                    if (status === 419 && !window.sessionExpired) {
                        preventDefault();
                        showSessionExpiredModal();
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
