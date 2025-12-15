<x-opac.layout title="E-Resources">
    <x-opac.page-header 
        title="E-Resources" 
        subtitle="Akses digital perpustakaan dan referensi ilmiah"
        :breadcrumbs="[['label' => 'E-Resources']]"
    />

    <section class="max-w-6xl mx-auto px-4 py-6 lg:py-10">
        
        {{-- Compact Hero Stats --}}
        <div class="bg-gradient-to-br from-primary-600 via-blue-600 to-indigo-700 rounded-2xl p-5 lg:p-6 mb-6 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-48 h-48 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2 blur-2xl"></div>
            <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                        <i class="fas fa-globe text-white text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl lg:text-2xl font-bold text-white">E-Resources UNIDA Gontor</h2>
                        <p class="text-blue-200 text-sm">Koleksi digital untuk mendukung pembelajaran dan penelitian</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <div class="px-4 py-2 bg-white/10 backdrop-blur rounded-xl text-center">
                        <span class="text-xl font-bold text-white">8.5K+</span>
                        <p class="text-[10px] text-blue-200">Kitab Klasik</p>
                    </div>
                    <div class="px-4 py-2 bg-white/10 backdrop-blur rounded-xl text-center">
                        <span class="text-xl font-bold text-white">70+</span>
                        <p class="text-[10px] text-blue-200">Arsip Sejarah</p>
                    </div>
                    <div class="px-4 py-2 bg-white/10 backdrop-blur rounded-xl text-center">
                        <span class="text-xl font-bold text-white">120K+</span>
                        <p class="text-[10px] text-blue-200">Jurnal Ilmiah</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Built-in Collections (Premium) --}}
        <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
            <div class="w-8 h-8 bg-gradient-to-br from-amber-400 to-orange-500 rounded-lg flex items-center justify-center shadow">
                <i class="fas fa-crown text-white text-xs"></i>
            </div>
            <span>Koleksi Digital Lokal</span>
            <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-[10px] font-bold rounded-full">TERSEDIA OFFLINE</span>
        </h3>

        <div class="grid md:grid-cols-2 gap-4 mb-8">
            {{-- Maktabah Shamela --}}
            <a href="{{ route('opac.shamela.index') }}" class="group relative bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 rounded-2xl p-5 text-white overflow-hidden hover:shadow-xl hover:shadow-blue-500/25 transition-all">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
                
                <div class="relative">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                <i class="fas fa-book-quran text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-lg">Maktabah Shamela</h4>
                                <p class="text-blue-200 text-sm" dir="rtl">المكتبة الشاملة</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 bg-white/20 text-[10px] font-bold rounded-full">LOKAL</span>
                    </div>
                    
                    <p class="text-blue-100 text-sm mb-4">
                        Perpustakaan digital Islam terlengkap dengan 8,425 kitab klasik dan 7+ juta halaman. 
                        Mencakup Tafsir, Hadits, Fiqih, Aqidah, dan berbagai disiplin ilmu Islam lainnya.
                    </p>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex gap-2">
                            <span class="px-2 py-1 bg-white/15 text-[10px] rounded">8,425 Kitab</span>
                            <span class="px-2 py-1 bg-white/15 text-[10px] rounded">7M+ Halaman</span>
                        </div>
                        <i class="fas fa-arrow-right text-blue-300 group-hover:translate-x-1 transition"></i>
                    </div>
                </div>
            </a>

            {{-- Universitaria --}}
            <a href="{{ route('opac.universitaria.index') }}" class="group relative bg-gradient-to-br from-amber-500 via-orange-500 to-red-600 rounded-2xl p-5 text-white overflow-hidden hover:shadow-xl hover:shadow-amber-500/25 transition-all">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute top-3 right-3">
                    <span class="px-2 py-1 bg-white/25 text-[10px] font-bold rounded-full"><i class="fas fa-crown mr-1"></i>PREMIUM</span>
                </div>
                
                <div class="relative">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-landmark text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg">Universitaria</h4>
                            <p class="text-amber-100 text-sm">Warisan Intelektual PMDG</p>
                        </div>
                    </div>
                    
                    <p class="text-amber-50 text-sm mb-4">
                        Koleksi bersejarah Pondok Modern Darussalam Gontor. Wardun Gontor sejak 1968, 
                        buku peringatan, dokumentasi sejarah, dan naskah kuno Tarbiyatul Athfal.
                    </p>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex gap-2">
                            <span class="px-2 py-1 bg-white/15 text-[10px] rounded">70+ Dokumen</span>
                            <span class="px-2 py-1 bg-white/15 text-[10px] rounded">Baca Online</span>
                        </div>
                        <i class="fas fa-arrow-right text-amber-200 group-hover:translate-x-1 transition"></i>
                    </div>
                </div>
            </a>
        </div>

        {{-- Database Berlangganan --}}
        <div class="bg-gradient-to-r from-indigo-600 to-blue-700 rounded-2xl p-5 mb-6 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fas fa-database"></i>
                        <h3 class="font-bold text-lg">Database Jurnal Berlangganan</h3>
                    </div>
                    <p class="text-blue-200 text-sm">Akses Gale Academic & ProQuest via konsorsium FPPTI Jawa Timur. Login diperlukan.</p>
                </div>
                <a href="{{ route('opac.database-access') }}" 
                   class="inline-flex items-center gap-2 px-5 py-3 bg-white text-blue-700 font-bold rounded-xl hover:bg-blue-50 transition shadow-lg whitespace-nowrap">
                    <i class="fas fa-key"></i>
                    Akses Database
                    <i class="fas fa-arrow-right text-sm"></i>
                </a>
            </div>
        </div>

        {{-- E-Book Gratis Section --}}
        <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
            <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-book text-emerald-600 text-sm"></i>
            </div>
            E-Book & Perpustakaan Digital Terbuka
            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded-full">GRATIS</span>
        </h3>
        
        @php
        $ebooks = [
            ['name' => 'Waqfeya', 'url' => 'https://waqfeya.net/', 'desc' => 'Perpustakaan Arab digital', 'count' => '15K+', 'icon' => 'fa-book-quran', 'color' => 'teal'],
            ['name' => 'Open Library', 'url' => 'https://openlibrary.org/', 'desc' => 'Internet Archive', 'count' => '4M+', 'icon' => 'fa-landmark', 'color' => 'blue'],
            ['name' => 'PDF Drive', 'url' => 'https://www.pdfdrive.com/', 'desc' => 'Search engine PDF', 'count' => '80M+', 'icon' => 'fa-file-pdf', 'color' => 'rose'],
            ['name' => 'Harvard DASH', 'url' => 'https://dash.harvard.edu/', 'desc' => 'Scholarship Harvard', 'count' => '100K+', 'icon' => 'fa-university', 'color' => 'red'],
            ['name' => 'ManyBooks', 'url' => 'https://manybooks.net/', 'desc' => 'Free e-books', 'count' => '50K+', 'icon' => 'fa-books', 'color' => 'orange'],
            ['name' => 'Noor-Book', 'url' => 'https://www.noor-book.com/en/', 'desc' => 'Multi-language library', 'count' => '200K+', 'icon' => 'fa-sun', 'color' => 'amber'],
        ];
        
        $colors = [
            'teal' => 'from-teal-500 to-teal-600',
            'blue' => 'from-blue-500 to-blue-600',
            'rose' => 'from-rose-500 to-rose-600',
            'red' => 'from-red-500 to-red-600',
            'orange' => 'from-orange-500 to-orange-600',
            'amber' => 'from-amber-500 to-amber-600',
        ];
        @endphp

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-8">
            @foreach($ebooks as $e)
            <a href="{{ $e['url'] }}" target="_blank" 
               class="group bg-white rounded-xl p-4 border border-gray-100 hover:border-{{ $e['color'] }}-300 hover:shadow-lg transition-all text-center">
                <div class="w-12 h-12 bg-gradient-to-br {{ $colors[$e['color']] }} rounded-xl flex items-center justify-center mx-auto mb-3 shadow-lg group-hover:scale-110 transition-transform">
                    <i class="fas {{ $e['icon'] }} text-white"></i>
                </div>
                <h4 class="font-bold text-gray-900 text-sm group-hover:text-{{ $e['color'] }}-600 transition">{{ $e['name'] }}</h4>
                <p class="text-[10px] text-gray-500 mt-1">{{ $e['desc'] }}</p>
                <span class="inline-block mt-2 px-2 py-0.5 bg-gray-100 text-gray-600 text-[10px] font-medium rounded-full">{{ $e['count'] }}</span>
            </a>
            @endforeach
        </div>

        {{-- Perpusnas --}}
        <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-landmark text-purple-600 text-sm"></i>
            </div>
            Perpustakaan Nasional RI
        </h3>
        
        <div class="grid sm:grid-cols-2 gap-4 mb-8">
            <a href="https://ipusnas.id" target="_blank" class="flex items-center gap-4 bg-white rounded-xl p-4 border border-gray-100 hover:border-blue-300 hover:shadow-lg transition group">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg group-hover:scale-110 transition-transform">
                    <i class="fas fa-book-reader text-white"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="font-bold text-gray-900 group-hover:text-blue-600 transition">iPusnas</h4>
                    <p class="text-xs text-gray-500">Perpustakaan digital nasional</p>
                    <span class="inline-block mt-1 px-2 py-0.5 bg-blue-100 text-blue-700 text-[10px] font-bold rounded-full">500K+ E-Books</span>
                </div>
                <i class="fas fa-external-link-alt text-gray-300 group-hover:text-blue-500 transition"></i>
            </a>

            <a href="https://bfrpn.perpusnas.go.id" target="_blank" class="flex items-center gap-4 bg-white rounded-xl p-4 border border-gray-100 hover:border-green-300 hover:shadow-lg transition group">
                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg group-hover:scale-110 transition-transform">
                    <i class="fas fa-star text-white"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="font-bold text-gray-900 group-hover:text-green-600 transition">Bintang Pusnas</h4>
                    <p class="text-xs text-gray-500">Koleksi bahan pustaka langka</p>
                    <span class="inline-block mt-1 px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-bold rounded-full">Akses Terbuka</span>
                </div>
                <i class="fas fa-external-link-alt text-gray-300 group-hover:text-green-500 transition"></i>
            </a>
        </div>

        {{-- Tips --}}
        <div class="bg-gradient-to-br from-slate-50 to-gray-100 rounded-xl p-4 border border-gray-200">
            <div class="flex items-center gap-2 mb-3">
                <i class="fas fa-lightbulb text-amber-500"></i>
                <h4 class="font-bold text-gray-900 text-sm">Tips Mengakses E-Resources</h4>
            </div>
            <div class="grid sm:grid-cols-2 gap-2 text-xs text-gray-600">
                <div class="flex items-start gap-2">
                    <i class="fas fa-check-circle text-emerald-500 mt-0.5 flex-shrink-0"></i>
                    <span>Maktabah Shamela & Universitaria tersedia <strong>offline</strong> di jaringan kampus</span>
                </div>
                <div class="flex items-start gap-2">
                    <i class="fas fa-check-circle text-emerald-500 mt-0.5 flex-shrink-0"></i>
                    <span>Database berlangganan memerlukan <strong>login</strong> via portal khusus</span>
                </div>
                <div class="flex items-start gap-2">
                    <i class="fas fa-check-circle text-emerald-500 mt-0.5 flex-shrink-0"></i>
                    <span>Gunakan browser terbaru untuk pengalaman optimal</span>
                </div>
                <div class="flex items-start gap-2">
                    <i class="fas fa-check-circle text-emerald-500 mt-0.5 flex-shrink-0"></i>
                    <span>Hubungi pustakawan jika mengalami kendala akses</span>
                </div>
            </div>
        </div>
    </section>
</x-opac.layout>
