<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'OPAC' }} - Perpustakaan UNIDA Gontor</title>
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
                <span class="flex items-center gap-1">
                    <i class="fas fa-id-card"></i> NPP: 350210D2014338
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
                            <i class="fas fa-home text-xs"></i> HOME <i class="fas fa-chevron-down text-[10px] ml-1"></i>
                        </button>
                        <div class="mega-menu left-0 w-[700px] -ml-20">
                            <div class="bg-white rounded-xl shadow-2xl p-6">
                            <div class="grid grid-cols-3 gap-6">
                                <!-- Profil -->
                                <div>
                                    <h3 class="font-bold text-gray-900 mb-3 text-sm">Profil</h3>
                                    <div class="space-y-2">
                                        <a href="{{ route('opac.page', 'visi-misi') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                            <div class="w-9 h-9 bg-primary-100 rounded-lg flex items-center justify-center"><i class="fas fa-bullseye text-primary-600"></i></div>
                                            <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">Visi & Misi</p><p class="text-xs text-gray-500">Tujuan & Nilai</p></div>
                                        </a>
                                        <a href="{{ route('opac.page', 'sejarah') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                            <div class="w-9 h-9 bg-amber-100 rounded-lg flex items-center justify-center"><i class="fas fa-landmark text-amber-600"></i></div>
                                            <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">Sejarah</p><p class="text-xs text-gray-500">Perjalanan Kami</p></div>
                                        </a>
                                        <a href="{{ route('opac.page', 'struktur-organisasi') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                            <div class="w-9 h-9 bg-emerald-100 rounded-lg flex items-center justify-center"><i class="fas fa-sitemap text-emerald-600"></i></div>
                                            <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">Struktur Organisasi</p><p class="text-xs text-gray-500">Tim & Pimpinan</p></div>
                                        </a>
                                        <a href="{{ route('opac.page', 'mou') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                            <div class="w-9 h-9 bg-purple-100 rounded-lg flex items-center justify-center"><i class="fas fa-handshake text-purple-600"></i></div>
                                            <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">MoU</p><p class="text-xs text-gray-500">Kerjasama</p></div>
                                        </a>
                                    </div>
                                </div>
                                <!-- Guidelines -->
                                <div>
                                    <h3 class="font-bold text-gray-900 mb-3 text-sm">Panduan</h3>
                                    <div class="space-y-2">
                                        <a href="{{ route('opac.page', 'tata-tertib') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                            <div class="w-9 h-9 bg-red-100 rounded-lg flex items-center justify-center"><i class="fas fa-clipboard-list text-red-600"></i></div>
                                            <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">Tata Tertib</p><p class="text-xs text-gray-500">Aturan & Kebijakan</p></div>
                                        </a>
                                        <a href="{{ route('opac.page', 'jam-layanan') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                            <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center"><i class="fas fa-clock text-green-600"></i></div>
                                            <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">Jam Layanan</p><p class="text-xs text-gray-500">Waktu Operasional</p></div>
                                        </a>
                                        <a href="{{ route('opac.page', 'fasilitas') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                            <div class="w-9 h-9 bg-cyan-100 rounded-lg flex items-center justify-center"><i class="fas fa-building text-cyan-600"></i></div>
                                            <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">Fasilitas</p><p class="text-xs text-gray-500">Ruang & Layanan</p></div>
                                        </a>
                                        <a href="{{ route('opac.page', 'karir') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                            <div class="w-9 h-9 bg-orange-100 rounded-lg flex items-center justify-center"><i class="fas fa-user-tie text-orange-600"></i></div>
                                            <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">Karir</p><p class="text-xs text-gray-500">Lowongan</p></div>
                                        </a>
                                    </div>
                                </div>
                                <!-- Video -->
                                <div>
                                    <h3 class="font-bold text-gray-900 mb-3 text-sm">Video Profil</h3>
                                    <div class="aspect-video bg-gray-100 rounded-xl overflow-hidden">
                                        <iframe class="w-full h-full" src="https://www.youtube.com/embed/YOUR_VIDEO_ID" frameborder="0" allowfullscreen></iframe>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2 text-center">Profil Perpustakaan UNIDA</p>
                                </div>
                            </div>
                        </div></div>
                    </div>

                    <!-- E-RESOURCES Menu -->
                    <div class="menu-item">
                        <button class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white hover:bg-white/10 rounded-lg transition">
                            <i class="fas fa-database text-xs"></i> E-RESOURCES <i class="fas fa-chevron-down text-[10px] ml-1"></i>
                        </button>
                        <div class="mega-menu left-0 w-[700px] -ml-32">
                            <div class="bg-white rounded-xl shadow-2xl p-6">
                                <div class="grid grid-cols-3 gap-6">
                                    <!-- Internal - Sistem Laravel -->
                                    <div>
                                        <h3 class="font-bold text-gray-900 mb-3 text-sm">Koleksi Internal</h3>
                                        <div class="space-y-2">
                                            <a href="{{ route('opac.search') }}?type=book" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                                <div class="w-9 h-9 bg-primary-100 rounded-lg flex items-center justify-center"><i class="fas fa-book text-primary-600"></i></div>
                                                <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">Buku</p><p class="text-xs text-gray-500">Katalog Online</p></div>
                                            </a>
                                            <a href="{{ route('opac.search') }}?type=ebook" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                                <div class="w-9 h-9 bg-orange-100 rounded-lg flex items-center justify-center"><i class="fas fa-file-pdf text-orange-600"></i></div>
                                                <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">E-Book</p><p class="text-xs text-gray-500">Koleksi Digital</p></div>
                                            </a>
                                            <a href="{{ route('opac.search') }}?type=ethesis" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                                <div class="w-9 h-9 bg-pink-100 rounded-lg flex items-center justify-center"><i class="fas fa-graduation-cap text-pink-600"></i></div>
                                                <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">E-Thesis</p><p class="text-xs text-gray-500">Tugas Akhir</p></div>
                                            </a>
                                            <a href="{{ route('opac.page', 'digilib-apps') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                                <div class="w-9 h-9 bg-teal-100 rounded-lg flex items-center justify-center"><i class="fas fa-mobile-alt text-teal-600"></i></div>
                                                <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">Digilib Apps</p><p class="text-xs text-gray-500">Mobile & Desktop</p></div>
                                            </a>
                                        </div>
                                    </div>
                                    <!-- External Sources -->
                                    <div>
                                        <h3 class="font-bold text-gray-900 mb-3 text-sm">Sumber External</h3>
                                        <div class="space-y-2">
                                            <a href="https://repo.unida.gontor.ac.id" target="_blank" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                                <div class="w-9 h-9 bg-indigo-100 rounded-lg flex items-center justify-center"><i class="fas fa-database text-indigo-600"></i></div>
                                                <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">Repository <i class="fas fa-external-link-alt text-[8px] text-gray-400"></i></p><p class="text-xs text-gray-500">Karya Ilmiah Civitas</p></div>
                                            </a>
                                            <a href="https://ejournal.unida.gontor.ac.id" target="_blank" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                                <div class="w-9 h-9 bg-red-100 rounded-lg flex items-center justify-center"><i class="fas fa-journal-whills text-red-600"></i></div>
                                                <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">UNIDA Journal <i class="fas fa-external-link-alt text-[8px] text-gray-400"></i></p><p class="text-xs text-gray-500">Jurnal Ilmiah UNIDA</p></div>
                                            </a>
                                            <a href="{{ route('opac.page', 'journal-subscription') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                                <div class="w-9 h-9 bg-yellow-100 rounded-lg flex items-center justify-center"><i class="fas fa-bookmark text-yellow-600"></i></div>
                                                <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">Akses E-Resources</p><p class="text-xs text-gray-500">E-Book & Database</p></div>
                                            </a>
                                        </div>
                                    </div>
                                    <!-- Perpusnas -->
                                    <div>
                                        <h3 class="font-bold text-gray-900 mb-3 text-sm">Perpustakaan Nasional</h3>
                                        <div class="space-y-2">
                                            <a href="https://ipusnas.id" target="_blank" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                                <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center"><i class="fas fa-book-reader text-blue-600"></i></div>
                                                <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">iPusnas <i class="fas fa-external-link-alt text-[8px] text-gray-400"></i></p><p class="text-xs text-gray-500">E-Book Perpusnas</p></div>
                                            </a>
                                            <a href="https://bfrpn.perpusnas.go.id" target="_blank" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                                <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center"><i class="fas fa-star text-green-600"></i></div>
                                                <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">Bintang Pusnas <i class="fas fa-external-link-alt text-[8px] text-gray-400"></i></p><p class="text-xs text-gray-500">Bahan Pustaka</p></div>
                                            </a>
                                        </div>
                                        <div class="mt-4 bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl p-3 text-white">
                                            <p class="text-xs text-primary-100 mb-2">Akses koleksi nasional gratis untuk seluruh masyarakat Indonesia</p>
                                            <a href="https://perpusnas.go.id" target="_blank" class="text-xs font-medium hover:underline">perpusnas.go.id →</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DISCOVER Menu -->
                    <div class="menu-item">
                        <button class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white hover:bg-white/10 rounded-lg transition">
                            <i class="fas fa-compass text-xs"></i> DISCOVER <i class="fas fa-chevron-down text-[10px] ml-1"></i>
                        </button>
                        <div class="mega-menu left-0 w-[700px] -ml-48">
                            <div class="bg-white rounded-xl shadow-2xl p-6">
                                <div class="grid grid-cols-3 gap-6">
                                <!-- New Experience -->
                                <div>
                                    <h3 class="font-bold text-gray-900 mb-3 text-sm">New Experience</h3>
                                    <div class="space-y-2">
                                        <a href="{{ route('opac.page', 'event-library') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                            <div class="w-9 h-9 bg-pink-100 rounded-lg flex items-center justify-center"><i class="fas fa-calendar-star text-pink-600"></i></div>
                                            <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">Event Library</p><p class="text-xs text-gray-500">Kegiatan Perpustakaan</p></div>
                                        </a>
                                        <a href="{{ route('opac.page', 'virtual-tour') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                            <div class="w-9 h-9 bg-cyan-100 rounded-lg flex items-center justify-center"><i class="fas fa-vr-cardboard text-cyan-600"></i></div>
                                            <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">Virtual Tour</p><p class="text-xs text-gray-500">Tur Virtual 360°</p></div>
                                        </a>
                                        <a href="{{ route('opac.page', 'e-learning') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                            <div class="w-9 h-9 bg-violet-100 rounded-lg flex items-center justify-center"><i class="fas fa-laptop text-violet-600"></i></div>
                                            <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">E-Learning</p><p class="text-xs text-gray-500">Pembelajaran Online</p></div>
                                        </a>
                                    </div>
                                </div>
                                <!-- Explore More -->
                                <div>
                                    <h3 class="font-bold text-gray-900 mb-3 text-sm">Explore More</h3>
                                    <div class="space-y-2">
                                        <a href="{{ route('opac.page', 'prosa-kreatif') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                            <div class="w-9 h-9 bg-rose-100 rounded-lg flex items-center justify-center"><i class="fas fa-pen-fancy text-rose-600"></i></div>
                                            <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">Prosa Kreatif</p><p class="text-xs text-gray-500">Komunitas Menulis</p></div>
                                        </a>
                                        <a href="{{ route('opac.page', 'survey') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                            <div class="w-9 h-9 bg-lime-100 rounded-lg flex items-center justify-center"><i class="fas fa-poll text-lime-600"></i></div>
                                            <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">Experience Survey</p><p class="text-xs text-gray-500">Survei Kepuasan</p></div>
                                        </a>
                                        <a href="{{ route('opac.page', 'research-tools') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                            <div class="w-9 h-9 bg-amber-100 rounded-lg flex items-center justify-center"><i class="fas fa-microscope text-amber-600"></i></div>
                                            <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">Research Tools</p><p class="text-xs text-gray-500">Alat Riset</p></div>
                                        </a>
                                    </div>
                                </div>
                                <!-- Book Recommendation -->
                                <div>
                                    <h3 class="font-bold text-gray-900 mb-3 text-sm">Rekomendasi Buku</h3>
                                    <a href="{{ route('opac.search') . '?type=book' }}?sort=latest" class="block bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl p-4 hover:shadow-lg transition">
                                        <div class="flex gap-3">
                                            <div class="w-16 h-20 bg-gradient-to-br from-primary-400 to-primary-600 rounded-lg flex items-center justify-center shadow-lg">
                                                <i class="fas fa-book text-white text-xl"></i>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-xs text-gray-500">Koleksi Terbaru</p>
                                                <p class="font-bold text-gray-900 text-sm">Lihat Buku Baru</p>
                                                <span class="inline-block mt-2 px-2 py-0.5 bg-accent-400 text-xs font-medium rounded">NEW</span>
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
                            <i class="fas fa-book-open text-xs"></i> GUIDE <i class="fas fa-chevron-down text-[10px] ml-1"></i>
                        </button>
                        <div class="mega-menu left-0 w-[550px] -ml-48">
                            <div class="bg-white rounded-xl shadow-2xl p-6">
                                <div class="grid grid-cols-2 gap-6">
                                    <!-- Panduan Pengguna -->
                                    <div>
                                        <h3 class="font-bold text-gray-900 mb-3 text-sm">Panduan Pengguna</h3>
                                        <div class="space-y-2">
                                            <a href="{{ route('opac.page', 'panduan-opac') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                                <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center"><i class="fas fa-search text-blue-600"></i></div>
                                                <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">Panduan OPAC</p><p class="text-xs text-gray-500">Cara menggunakan katalog</p></div>
                                            </a>
                                            <a href="{{ route('opac.panduan.thesis') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                                <div class="w-9 h-9 bg-emerald-100 rounded-lg flex items-center justify-center"><i class="fas fa-upload text-emerald-600"></i></div>
                                                <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">Unggah Tugas Akhir</p><p class="text-xs text-gray-500">Panduan upload skripsi</p></div>
                                            </a>
                                            <a href="{{ route('opac.panduan.plagiarism') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                                <div class="w-9 h-9 bg-teal-100 rounded-lg flex items-center justify-center"><i class="fas fa-shield-alt text-teal-600"></i></div>
                                                <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">Cek Plagiasi</p><p class="text-xs text-gray-500">Panduan cek similarity</p></div>
                                            </a>
                                            <a href="{{ route('opac.page', 'panduan-ospek') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                                <div class="w-9 h-9 bg-orange-100 rounded-lg flex items-center justify-center"><i class="fas fa-user-graduate text-orange-600"></i></div>
                                                <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">Panduan Ospek</p><p class="text-xs text-gray-500">Orientasi mahasiswa baru</p></div>
                                            </a>
                                        </div>
                                    </div>
                                    <!-- Materi & Akademik -->
                                    <div>
                                        <h3 class="font-bold text-gray-900 mb-3 text-sm">Materi & Akademik</h3>
                                        <div class="space-y-2">
                                            <a href="{{ route('opac.page', 'panduan-akademik') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                                <div class="w-9 h-9 bg-purple-100 rounded-lg flex items-center justify-center"><i class="fas fa-graduation-cap text-purple-600"></i></div>
                                                <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">Panduan Akademik</p><p class="text-xs text-gray-500">Info akademik perpustakaan</p></div>
                                            </a>
                                            <a href="{{ route('opac.page', 'materi-perpustakaan') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                                <div class="w-9 h-9 bg-pink-100 rounded-lg flex items-center justify-center"><i class="fas fa-chalkboard-teacher text-pink-600"></i></div>
                                                <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">Materi Perpustakaan</p><p class="text-xs text-gray-500">Bahan ajar & presentasi</p></div>
                                            </a>
                                            <a href="{{ route('opac.page', 'download-eddc') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-primary-50 group">
                                                <div class="w-9 h-9 bg-indigo-100 rounded-lg flex items-center justify-center"><i class="fas fa-download text-indigo-600"></i></div>
                                                <div><p class="text-sm font-medium text-gray-900 group-hover:text-primary-600">Download E-DDC 23</p><p class="text-xs text-gray-500">Dewey Decimal Classification</p></div>
                                            </a>
                                        </div>
                                        <div class="mt-3 bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl p-3 text-white">
                                            <p class="text-xs text-primary-100 mb-2">Butuh bantuan? Hubungi pustakawan kami</p>
                                            <a href="https://wa.me/6285183053934" target="_blank" class="text-xs font-medium hover:underline flex items-center gap-1">
                                                <i class="fab fa-whatsapp"></i> Chat WhatsApp →
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- NEWS Link -->
                    <a href="{{ route('opac.search') . '?type=news' }}" class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white hover:bg-white/10 rounded-lg transition">
                        <i class="fas fa-newspaper text-xs"></i> NEWS
                    </a>
                </nav>

                <!-- Right Side -->
                <div class="flex items-center gap-2">
                    <!-- Search Button with Card Style -->
                    <button 
                        @click="searchOpen = true" 
                        class="hidden lg:flex items-center gap-2 px-4 py-2 text-white bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-xl text-sm font-medium transition shadow-sm"
                    >
                        <i class="fas fa-search"></i>
                        <span>Cari</span>
                    </button>
                    
                    <!-- Auth Buttons Desktop -->
                    @auth('member')
                        <a href="{{ route('opac.member.dashboard') }}" class="hidden lg:flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-xl shadow-sm transition">
                            <i class="fas fa-user-circle"></i>
                            <span>{{ auth('member')->user()->name }}</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="hidden lg:flex items-center gap-2 px-4 py-2 text-sm font-medium text-primary-700 bg-white hover:bg-gray-50 rounded-xl shadow-sm transition">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Masuk</span>
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
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </div>
                    <a href="{{ route('opac.member.dashboard') }}" class="w-9 h-9 bg-white rounded-lg flex items-center justify-center text-primary-600 shadow-sm hover:shadow transition">
                        <i class="fas fa-chevron-right text-sm"></i>
                    </a>
                </div>
            </div>
            @else
            <div class="p-4 bg-gradient-to-r from-gray-50 to-slate-50 border-b">
                <p class="text-xs text-gray-500 mb-3 text-center">Masuk untuk akses penuh</p>
                <div class="flex gap-2">
                    <a href="{{ route('login') }}" class="flex-1 py-2.5 text-center text-sm font-semibold text-primary-600 bg-white border-2 border-primary-600 rounded-xl hover:bg-primary-50 transition">Masuk</a>
                    <a href="{{ route('opac.register') }}" class="flex-1 py-2.5 text-center text-sm font-semibold text-white bg-gradient-to-r from-primary-600 to-primary-700 rounded-xl shadow-lg shadow-primary-500/30 hover:shadow-xl transition">Daftar</a>
                </div>
            </div>
            @endauth

            <!-- Search -->
            <div class="p-4 border-b">
                <form action="{{ route('opac.search') }}" method="GET">
                    <div class="flex bg-gray-100 rounded-xl overflow-hidden border-2 border-transparent focus-within:border-primary-300 focus-within:bg-white transition">
                        <input type="text" name="q" placeholder="Cari buku, e-book, tugas akhir..." class="flex-1 px-4 py-2.5 bg-transparent text-sm focus:outline-none">
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
                            <span class="font-semibold text-gray-900">HOME</span>
                        </span>
                        <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="openMenu === 'home' ? 'rotate-180 text-primary-600' : ''"></i>
                    </button>
                    <div x-show="openMenu === 'home'" x-collapse class="px-3 pb-3">
                        <div class="pl-12 space-y-1 border-l-2 border-primary-200 ml-1.5">
                            <p class="text-[10px] font-bold text-primary-600 uppercase tracking-wider px-3 py-1.5">Profil</p>
                            <a href="{{ route('opac.page', 'visi-misi') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-primary-600 hover:bg-white rounded-lg transition"><i class="fas fa-bullseye text-xs text-gray-400"></i> Visi & Misi</a>
                            <a href="{{ route('opac.page', 'sejarah') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-primary-600 hover:bg-white rounded-lg transition"><i class="fas fa-landmark text-xs text-gray-400"></i> Sejarah</a>
                            <a href="{{ route('opac.page', 'struktur-organisasi') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-primary-600 hover:bg-white rounded-lg transition"><i class="fas fa-sitemap text-xs text-gray-400"></i> Struktur Organisasi</a>
                            <a href="{{ route('opac.page', 'mou') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-primary-600 hover:bg-white rounded-lg transition"><i class="fas fa-handshake text-xs text-gray-400"></i> MoU</a>
                            <p class="text-[10px] font-bold text-primary-600 uppercase tracking-wider px-3 py-1.5 mt-2">Panduan</p>
                            <a href="{{ route('opac.page', 'tata-tertib') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-primary-600 hover:bg-white rounded-lg transition"><i class="fas fa-clipboard-list text-xs text-gray-400"></i> Tata Tertib</a>
                            <a href="{{ route('opac.page', 'jam-layanan') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-primary-600 hover:bg-white rounded-lg transition"><i class="fas fa-clock text-xs text-gray-400"></i> Jam Layanan</a>
                            <a href="{{ route('opac.page', 'fasilitas') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-primary-600 hover:bg-white rounded-lg transition"><i class="fas fa-building text-xs text-gray-400"></i> Fasilitas</a>
                            <a href="{{ route('opac.page', 'karir') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-primary-600 hover:bg-white rounded-lg transition"><i class="fas fa-user-tie text-xs text-gray-400"></i> Karir</a>
                        </div>
                    </div>
                </div>

                <!-- E-RESOURCES -->
                <div class="rounded-xl overflow-hidden" :class="openMenu === 'eresources' ? 'bg-indigo-50 ring-1 ring-indigo-100' : ''">
                    <button @click="openMenu = openMenu === 'eresources' ? null : 'eresources'" class="w-full flex items-center justify-between p-3 hover:bg-gray-50 transition" :class="openMenu === 'eresources' ? 'bg-indigo-50 hover:bg-indigo-50' : ''">
                        <span class="flex items-center gap-3">
                            <span class="w-9 h-9 rounded-lg flex items-center justify-center" :class="openMenu === 'eresources' ? 'bg-indigo-600 text-white' : 'bg-indigo-100 text-indigo-600'">
                                <i class="fas fa-database"></i>
                            </span>
                            <span class="font-semibold text-gray-900">E-RESOURCES</span>
                        </span>
                        <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="openMenu === 'eresources' ? 'rotate-180 text-indigo-600' : ''"></i>
                    </button>
                    <div x-show="openMenu === 'eresources'" x-collapse class="px-3 pb-3">
                        <div class="pl-12 space-y-1 border-l-2 border-indigo-200 ml-1.5">
                            <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider px-3 py-1.5">Koleksi Internal</p>
                            <a href="{{ route('opac.search') }}?type=book" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-indigo-600 hover:bg-white rounded-lg transition"><i class="fas fa-book text-xs text-gray-400"></i> Buku</a>
                            <a href="{{ route('opac.search') }}?type=ebook" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-indigo-600 hover:bg-white rounded-lg transition"><i class="fas fa-file-pdf text-xs text-gray-400"></i> E-Book</a>
                            <a href="{{ route('opac.search') }}?type=ethesis" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-indigo-600 hover:bg-white rounded-lg transition"><i class="fas fa-graduation-cap text-xs text-gray-400"></i> E-Thesis</a>
                            <a href="{{ route('opac.page', 'digilib-apps') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-indigo-600 hover:bg-white rounded-lg transition"><i class="fas fa-mobile-alt text-xs text-gray-400"></i> Digilib Apps</a>
                            <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider px-3 py-1.5 mt-2">Sumber External</p>
                            <a href="https://repo.unida.gontor.ac.id" target="_blank" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-indigo-600 hover:bg-white rounded-lg transition"><i class="fas fa-database text-xs text-gray-400"></i> Repository <i class="fas fa-external-link-alt text-[10px] text-gray-300 ml-auto"></i></a>
                            <a href="https://ejournal.unida.gontor.ac.id" target="_blank" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-indigo-600 hover:bg-white rounded-lg transition"><i class="fas fa-journal-whills text-xs text-gray-400"></i> UNIDA Journal <i class="fas fa-external-link-alt text-[10px] text-gray-300 ml-auto"></i></a>
                            <a href="{{ route('opac.page', 'journal-subscription') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-indigo-600 hover:bg-white rounded-lg transition"><i class="fas fa-bookmark text-xs text-gray-400"></i> Akses E-Resources</a>
                            <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider px-3 py-1.5 mt-2">Perpusnas</p>
                            <a href="https://ipusnas.id" target="_blank" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-indigo-600 hover:bg-white rounded-lg transition"><i class="fas fa-book-reader text-xs text-gray-400"></i> iPusnas <i class="fas fa-external-link-alt text-[10px] text-gray-300 ml-auto"></i></a>
                            <a href="https://bfrpn.perpusnas.go.id" target="_blank" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-indigo-600 hover:bg-white rounded-lg transition"><i class="fas fa-star text-xs text-gray-400"></i> Bintang Pusnas <i class="fas fa-external-link-alt text-[10px] text-gray-300 ml-auto"></i></a>
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
                            <span class="font-semibold text-gray-900">DISCOVER</span>
                        </span>
                        <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="openMenu === 'discover' ? 'rotate-180 text-emerald-600' : ''"></i>
                    </button>
                    <div x-show="openMenu === 'discover'" x-collapse class="px-3 pb-3">
                        <div class="pl-12 space-y-1 border-l-2 border-emerald-200 ml-1.5">
                            <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider px-3 py-1.5">New Experience</p>
                            <a href="{{ route('opac.page', 'event-library') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-emerald-600 hover:bg-white rounded-lg transition"><i class="fas fa-calendar-star text-xs text-gray-400"></i> Event Library</a>
                            <a href="{{ route('opac.page', 'virtual-tour') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-emerald-600 hover:bg-white rounded-lg transition"><i class="fas fa-vr-cardboard text-xs text-gray-400"></i> Virtual Tour</a>
                            <a href="{{ route('opac.page', 'e-learning') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-emerald-600 hover:bg-white rounded-lg transition"><i class="fas fa-laptop text-xs text-gray-400"></i> E-Learning</a>
                            <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider px-3 py-1.5 mt-2">Explore More</p>
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
                            <span class="font-semibold text-gray-900">GUIDE</span>
                        </span>
                        <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="openMenu === 'guide' ? 'rotate-180 text-orange-600' : ''"></i>
                    </button>
                    <div x-show="openMenu === 'guide'" x-collapse class="px-3 pb-3">
                        <div class="pl-12 space-y-1 border-l-2 border-orange-200 ml-1.5">
                            <p class="text-[10px] font-bold text-orange-600 uppercase tracking-wider px-3 py-1.5">Panduan Pengguna</p>
                            <a href="{{ route('opac.page', 'panduan-opac') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-orange-600 hover:bg-white rounded-lg transition"><i class="fas fa-search text-xs text-gray-400"></i> Panduan OPAC</a>
                            <a href="{{ route('opac.panduan.thesis') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-orange-600 hover:bg-white rounded-lg transition"><i class="fas fa-upload text-xs text-gray-400"></i> Unggah Tugas Akhir</a>
                            <a href="{{ route('opac.panduan.plagiarism') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-teal-600 hover:bg-white rounded-lg transition"><i class="fas fa-shield-alt text-xs text-gray-400"></i> Cek Plagiasi</a>
                            <a href="{{ route('opac.page', 'panduan-ospek') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-orange-600 hover:bg-white rounded-lg transition"><i class="fas fa-user-graduate text-xs text-gray-400"></i> Panduan Ospek</a>
                            <p class="text-[10px] font-bold text-orange-600 uppercase tracking-wider px-3 py-1.5 mt-2">Materi & Akademik</p>
                            <a href="{{ route('opac.page', 'panduan-akademik') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-orange-600 hover:bg-white rounded-lg transition"><i class="fas fa-graduation-cap text-xs text-gray-400"></i> Panduan Akademik</a>
                            <a href="{{ route('opac.page', 'materi-perpustakaan') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-orange-600 hover:bg-white rounded-lg transition"><i class="fas fa-chalkboard-teacher text-xs text-gray-400"></i> Materi Perpustakaan</a>
                            <a href="{{ route('opac.page', 'download-eddc') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-orange-600 hover:bg-white rounded-lg transition"><i class="fas fa-download text-xs text-gray-400"></i> Download E-DDC 23</a>
                        </div>
                    </div>
                </div>

                <!-- NEWS -->
                <a href="{{ route('opac.search') . '?type=news' }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-pink-50 transition group">
                    <span class="w-9 h-9 bg-pink-100 group-hover:bg-pink-600 rounded-lg flex items-center justify-center text-pink-600 group-hover:text-white transition">
                        <i class="fas fa-newspaper"></i>
                    </span>
                    <span class="font-semibold text-gray-900">NEWS</span>
                </a>
            </div>

            <!-- Quick Access -->
            <div class="p-4 border-t">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3">Akses Cepat</p>
                <div class="grid grid-cols-3 gap-2">
                    <a href="{{ route('opac.search') . '?type=book' }}" class="flex flex-col items-center gap-1 p-3 bg-gray-50 hover:bg-primary-50 rounded-xl transition group">
                        <i class="fas fa-search text-gray-400 group-hover:text-primary-600"></i>
                        <span class="text-[10px] text-gray-600 group-hover:text-primary-600">Katalog</span>
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
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3">Hubungi Kami</p>
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
                    <p class="text-sm text-primary-200">Perpustakaan Universitas Darussalam Gontor melayani civitas akademika dalam akses koleksi dan layanan informasi.</p>
                    <div class="flex gap-3 mt-4">
                        <a href="https://youtube.com/@perpustakaanunidagontor" target="_blank" class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center text-white hover:bg-red-500 transition"><i class="fab fa-youtube"></i></a>
                        <a href="https://instagram.com/perpustakaanunida" target="_blank" class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center text-white hover:bg-pink-500 transition"><i class="fab fa-instagram"></i></a>
                        <a href="https://tiktok.com/@perpustakaanunida" target="_blank" class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center text-white hover:bg-gray-700 transition"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-4">E-Resources</h4>
                    <ul class="space-y-2 text-sm text-primary-200">
                        <li><a href="{{ route('opac.search') . '?type=book' }}" class="hover:text-white transition"><i class="fas fa-search mr-2"></i>OPAC</a></li>
                        <li><a href="{{ route('opac.search') . '?type=ebook' }}" class="hover:text-white transition"><i class="fas fa-file-pdf mr-2"></i>E-Book</a></li>
                        <li><a href="{{ route('opac.search') . '?type=ethesis' }}" class="hover:text-white transition"><i class="fas fa-graduation-cap mr-2"></i>E-Thesis</a></li>
                        <li><a href="https://repo.unida.gontor.ac.id" target="_blank" class="hover:text-white transition"><i class="fas fa-database mr-2"></i>Repository</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-4">Jam Layanan</h4>
                    <ul class="space-y-2 text-sm text-primary-200">
                        <li class="flex items-start gap-2"><i class="fas fa-clock mt-1"></i><span>Senin - Jumat<br>08.00 - 16.00 WIB</span></li>
                        <li class="flex items-start gap-2"><i class="fas fa-clock mt-1"></i><span>Sabtu<br>08.00 - 12.00 WIB</span></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-4">Kontak</h4>
                    <ul class="space-y-2 text-sm text-primary-200">
                        <li><a href="mailto:library@unida.gontor.ac.id" class="hover:text-white transition flex items-center gap-2"><i class="fas fa-envelope"></i>library@unida.gontor.ac.id</a></li>
                        <li><a href="https://wa.me/6285183053934" target="_blank" class="hover:text-white transition flex items-center gap-2"><i class="fab fa-whatsapp"></i>0851-8305-3934</a></li>
                        <li><a href="https://library.unida.gontor.ac.id" target="_blank" class="hover:text-white transition flex items-center gap-2"><i class="fas fa-globe"></i>library.unida.gontor.ac.id</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-white/20 mt-8 pt-6 text-center text-sm text-primary-300">
                &copy; {{ date('Y') }} Perpustakaan Universitas Darussalam Gontor. All rights reserved.
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
                <h3 class="text-white text-xl lg:text-2xl font-bold mb-2">Pencarian Global</h3>
                <p class="text-primary-200 text-sm">Temukan buku, e-book, tugas akhir, dan berita</p>
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
                            <span class="hidden sm:inline">Cari</span>
                            <i class="fas fa-arrow-right text-sm"></i>
                        </button>
                    </div>
                </div>

                <!-- Quick search categories -->
                <div class="mt-6 flex flex-wrap items-center justify-center gap-2">
                    <span class="text-primary-200 text-xs">Filter:</span>
                    <a href="{{ route('opac.search') }}?type=book" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-xs font-medium rounded-full transition flex items-center gap-1.5">
                        <i class="fas fa-book text-[10px]"></i> Buku
                    </a>
                    <a href="{{ route('opac.search') }}?type=ebook" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-xs font-medium rounded-full transition flex items-center gap-1.5">
                        <i class="fas fa-file-pdf text-[10px]"></i> E-Book
                    </a>
                    <a href="{{ route('opac.search') }}?type=ethesis" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-xs font-medium rounded-full transition flex items-center gap-1.5">
                        <i class="fas fa-graduation-cap text-[10px]"></i> E-Thesis
                    </a>
                    <a href="{{ route('opac.search') }}?type=news" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-xs font-medium rounded-full transition flex items-center gap-1.5">
                        <i class="fas fa-newspaper text-[10px]"></i> Berita
                    </a>
                </div>

                <!-- Popular searches -->
                <div class="mt-4 flex flex-wrap items-center justify-center gap-2">
                    <span class="text-primary-300 text-xs">Populer:</span>
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
                <span class="text-[10px] mt-0.5 font-medium">Beranda</span>
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
                <span class="text-[10px] mt-1 font-medium text-white">Cari</span>
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
