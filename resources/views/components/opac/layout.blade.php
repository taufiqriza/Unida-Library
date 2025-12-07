<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'OPAC' }} - Perpustakaan Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .pattern-bg {
            background-color: #eff6ff;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%233b82f6' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .gradient-blue { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
        .gradient-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    </style>
    @livewireStyles
</head>
<body class="pattern-bg min-h-screen">
    <!-- Header -->
    <header class="bg-white border-b border-blue-100 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('opac.home') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 gradient-blue rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/25">
                        <i class="fas fa-book-open text-white"></i>
                    </div>
                    <div>
                        <span class="font-bold text-gray-900">Perpustakaan</span>
                        <span class="text-xs text-gray-500 block -mt-1">Digital Library</span>
                    </div>
                </a>
                
                <nav class="hidden md:flex items-center gap-6">
                    <a href="{{ route('opac.home') }}" class="text-sm font-medium {{ request()->routeIs('opac.home') ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600' }}">Beranda</a>
                    <a href="{{ route('opac.catalog') }}" class="text-sm font-medium {{ request()->routeIs('opac.catalog*') ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600' }}">Katalog</a>
                    <a href="{{ route('opac.ebooks') }}" class="text-sm font-medium {{ request()->routeIs('opac.ebooks*') ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600' }}">E-Book</a>
                    <a href="{{ route('opac.etheses') }}" class="text-sm font-medium {{ request()->routeIs('opac.etheses*') ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600' }}">E-Thesis</a>
                    <a href="{{ route('opac.news') }}" class="text-sm font-medium {{ request()->routeIs('opac.news*') ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600' }}">Berita</a>
                </nav>

                <div class="flex items-center gap-3">
                    @auth('member')
                        <a href="{{ route('opac.member.dashboard') }}" class="text-sm font-medium text-gray-600 hover:text-blue-600">
                            <i class="fas fa-user-circle mr-1"></i> {{ auth('member')->user()->name }}
                        </a>
                    @else
                        <a href="{{ route('opac.login') }}" class="px-4 py-2 text-sm font-medium text-blue-600 hover:text-blue-700">Masuk</a>
                        <a href="{{ route('opac.register') }}" class="px-4 py-2 text-sm font-medium text-white gradient-blue rounded-lg shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 transition">Daftar</a>
                    @endauth
                    
                    <!-- Mobile Menu -->
                    <button class="md:hidden p-2 text-gray-600" onclick="document.getElementById('mobileMenu').classList.toggle('hidden')">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div id="mobileMenu" class="hidden md:hidden bg-white border-t border-gray-100 px-4 py-3">
            <a href="{{ route('opac.home') }}" class="block py-2 text-sm text-gray-600">Beranda</a>
            <a href="{{ route('opac.catalog') }}" class="block py-2 text-sm text-gray-600">Katalog</a>
            <a href="{{ route('opac.ebooks') }}" class="block py-2 text-sm text-gray-600">E-Book</a>
            <a href="{{ route('opac.etheses') }}" class="block py-2 text-sm text-gray-600">E-Thesis</a>
            <a href="{{ route('opac.news') }}" class="block py-2 text-sm text-gray-600">Berita</a>
        </div>
    </header>

    <main>
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 mt-12">
        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 gradient-blue rounded-lg flex items-center justify-center">
                            <i class="fas fa-book-open text-white text-sm"></i>
                        </div>
                        <span class="font-bold text-gray-900">Perpustakaan</span>
                    </div>
                    <p class="text-sm text-gray-500">Sistem Informasi Perpustakaan Digital untuk kemudahan akses koleksi dan layanan.</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 mb-3">Layanan</h4>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><a href="{{ route('opac.catalog') }}" class="hover:text-blue-600">Katalog Online</a></li>
                        <li><a href="{{ route('opac.ebooks') }}" class="hover:text-blue-600">E-Book</a></li>
                        <li><a href="{{ route('opac.etheses') }}" class="hover:text-blue-600">E-Thesis</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 mb-3">Informasi</h4>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><a href="#" class="hover:text-blue-600">Tentang Kami</a></li>
                        <li><a href="#" class="hover:text-blue-600">Jam Operasional</a></li>
                        <li><a href="#" class="hover:text-blue-600">Kontak</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 mb-3">Kontak</h4>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><i class="fas fa-envelope mr-2 text-blue-500"></i> perpustakaan@unida.ac.id</li>
                        <li><i class="fas fa-phone mr-2 text-blue-500"></i> (021) 123-4567</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-100 mt-8 pt-6 text-center text-sm text-gray-400">
                &copy; {{ date('Y') }} Perpustakaan Digital. All rights reserved.
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
