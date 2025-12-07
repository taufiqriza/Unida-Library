<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'OPAC' }} - Perpustakaan UNIDA Gontor</title>
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
    </style>
    @livewireStyles
</head>
<body class="pattern-bg min-h-screen">
    <!-- Header -->
    <header class="bg-gradient-to-r from-blue-600 to-indigo-700 sticky top-0 z-50 shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-14">
                <a href="{{ route('opac.home') }}" class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-white/20 backdrop-blur rounded-lg flex items-center justify-center">
                        <i class="fas fa-book-open text-white"></i>
                    </div>
                    <div class="hidden sm:block">
                        <span class="font-bold text-white">Perpustakaan</span>
                        <span class="text-xs text-blue-200 block -mt-1">UNIDA Gontor</span>
                    </div>
                </a>
                
                <!-- Desktop Nav -->
                <nav class="hidden lg:flex items-center gap-1">
                    <a href="{{ route('opac.home') }}" class="flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('opac.home') ? 'bg-white/20 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <i class="fas fa-home"></i> Beranda
                    </a>
                    <a href="{{ route('opac.catalog') }}" class="flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('opac.catalog*') ? 'bg-white/20 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <i class="fas fa-search"></i> Katalog
                    </a>
                    <a href="{{ route('opac.ebooks') }}" class="flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('opac.ebooks*') ? 'bg-white/20 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <i class="fas fa-file-pdf"></i> E-Book
                    </a>
                    <a href="{{ route('opac.etheses') }}" class="flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('opac.etheses*') ? 'bg-white/20 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <i class="fas fa-graduation-cap"></i> E-Thesis
                    </a>
                    <a href="{{ route('opac.news') }}" class="flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('opac.news*') ? 'bg-white/20 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <i class="fas fa-newspaper"></i> Berita
                    </a>
                </nav>

                <div class="flex items-center gap-2">
                    <a href="https://wa.me/6285183053934" target="_blank" class="w-9 h-9 bg-white/10 hover:bg-white/20 rounded-lg flex items-center justify-center text-white transition">
                        <i class="fab fa-whatsapp text-lg"></i>
                    </a>
                    @auth('member')
                        <a href="{{ route('opac.member.dashboard') }}" class="hidden sm:flex items-center gap-2 px-3 py-2 text-sm font-medium text-white bg-white/10 hover:bg-white/20 rounded-lg">
                            <i class="fas fa-user-circle"></i> {{ auth('member')->user()->name }}
                        </a>
                    @else
                        <a href="{{ route('opac.login') }}" class="hidden sm:block px-4 py-2 text-sm font-medium text-white hover:bg-white/10 rounded-lg">Masuk</a>
                        <a href="{{ route('opac.register') }}" class="hidden sm:block px-4 py-2 text-sm font-medium text-blue-600 bg-white rounded-lg hover:bg-blue-50 transition">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <main class="pb-20 lg:pb-0">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-blue-600 to-indigo-700 mt-12 hidden lg:block">
        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-book-open text-white text-sm"></i>
                        </div>
                        <span class="font-bold text-white">Perpustakaan</span>
                    </div>
                    <p class="text-sm text-blue-200">Perpustakaan Universitas Darussalam Gontor melayani civitas akademika dalam akses koleksi dan layanan informasi.</p>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-3">Layanan</h4>
                    <ul class="space-y-2 text-sm text-blue-200">
                        <li><a href="{{ route('opac.catalog') }}" class="hover:text-white"><i class="fas fa-search mr-2"></i>Katalog Online</a></li>
                        <li><a href="{{ route('opac.ebooks') }}" class="hover:text-white"><i class="fas fa-file-pdf mr-2"></i>E-Book</a></li>
                        <li><a href="{{ route('opac.etheses') }}" class="hover:text-white"><i class="fas fa-graduation-cap mr-2"></i>E-Thesis</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-3">Jam Layanan</h4>
                    <ul class="space-y-2 text-sm text-blue-200">
                        <li><i class="fas fa-clock mr-2"></i>Sen-Jum: 08.00 - 16.00</li>
                        <li><i class="fas fa-clock mr-2"></i>Sabtu: 08.00 - 12.00</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-3">Kontak</h4>
                    <ul class="space-y-2 text-sm text-blue-200">
                        <li><a href="mailto:library@unida.gontor.ac.id" class="hover:text-white"><i class="fas fa-envelope mr-2"></i>library@unida.gontor.ac.id</a></li>
                        <li><a href="https://wa.me/6285183053934" target="_blank" class="hover:text-white"><i class="fab fa-whatsapp mr-2"></i>0851-8305-3934</a></li>
                        <li><a href="https://library.unida.gontor.ac.id" target="_blank" class="hover:text-white"><i class="fas fa-globe mr-2"></i>library.unida.gontor.ac.id</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-white/20 mt-8 pt-6 text-center text-sm text-blue-200">
                &copy; {{ date('Y') }} Perpustakaan Universitas Darussalam Gontor. All rights reserved.
            </div>
        </div>
    </footer>

    <!-- Mobile Bottom Nav -->
    <div class="fixed bottom-0 left-0 right-0 bg-gradient-to-r from-blue-600 to-indigo-700 p-2 lg:hidden z-50">
        <div class="flex items-center justify-around">
            <a href="{{ route('opac.home') }}" class="flex flex-col items-center py-2 px-3 {{ request()->routeIs('opac.home') ? 'text-white' : 'text-blue-200' }}">
                <i class="fas fa-home text-lg"></i>
                <span class="text-[10px] mt-1 font-medium">Beranda</span>
            </a>
            <a href="{{ route('opac.catalog') }}" class="flex flex-col items-center py-2 px-3 {{ request()->routeIs('opac.catalog*') ? 'text-white' : 'text-blue-200' }}">
                <i class="fas fa-search text-lg"></i>
                <span class="text-[10px] mt-1 font-medium">Katalog</span>
            </a>
            <a href="{{ route('opac.ebooks') }}" class="flex items-center justify-center w-12 h-12 -mt-5 bg-white text-blue-600 rounded-full shadow-lg">
                <i class="fas fa-book-reader text-lg"></i>
            </a>
            <a href="{{ route('opac.etheses') }}" class="flex flex-col items-center py-2 px-3 {{ request()->routeIs('opac.etheses*') ? 'text-white' : 'text-blue-200' }}">
                <i class="fas fa-graduation-cap text-lg"></i>
                <span class="text-[10px] mt-1 font-medium">E-Thesis</span>
            </a>
            @auth('member')
            <a href="{{ route('opac.member.dashboard') }}" class="flex flex-col items-center py-2 px-3 {{ request()->routeIs('opac.member*') ? 'text-white' : 'text-blue-200' }}">
                <i class="fas fa-user text-lg"></i>
                <span class="text-[10px] mt-1 font-medium">Akun</span>
            </a>
            @else
            <a href="{{ route('opac.login') }}" class="flex flex-col items-center py-2 px-3 {{ request()->routeIs('opac.login') ? 'text-white' : 'text-blue-200' }}">
                <i class="fas fa-sign-in-alt text-lg"></i>
                <span class="text-[10px] mt-1 font-medium">Masuk</span>
            </a>
            @endauth
        </div>
    </div>

    @livewireScripts
</body>
</html>
