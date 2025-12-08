<x-opac.layout title="Akses E-Resources">
    <x-opac.page-header 
        title="Akses E-Resources" 
        subtitle="E-Book, jurnal, dan database digital"
        :breadcrumbs="[['label' => 'E-Resources'], ['label' => 'Akses E-Resources']]"
    />

    <section class="max-w-5xl mx-auto px-4 py-6 lg:py-10">
        <!-- Info -->
        <div class="bg-gradient-to-br from-primary-50 to-blue-50 rounded-2xl p-5 lg:p-6 border border-primary-100 mb-6">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-info-circle text-primary-600"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 mb-1">Informasi Akses</h3>
                    <p class="text-gray-600 text-sm">Beberapa sumber memerlukan kredensial khusus. Pastikan Anda menggunakan akun yang disediakan untuk mengakses database berbayar.</p>
                </div>
            </div>
        </div>

        <!-- E-Book Gratis -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg flex items-center gap-2">
            <i class="fas fa-book text-emerald-500"></i> E-Book Gratis
        </h3>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3 mb-8">
            @php
            $ebooks = [
                ['name' => 'Shamela Library', 'url' => 'https://shamela.ws/', 'desc' => 'Perpustakaan digital Islam klasik'],
                ['name' => 'Perpustakaan Islam Digital', 'url' => 'https://perpustakaanislamdigital.com/', 'desc' => 'Koleksi buku-buku Islam digital'],
                ['name' => 'Rumah Fiqih', 'url' => 'https://www.rumahfiqih.com/', 'desc' => 'Kumpulan PDF buku-buku fiqih'],
                ['name' => 'Harvard DASH', 'url' => 'https://dash.harvard.edu/', 'desc' => 'Digital Access to Scholarship at Harvard'],
                ['name' => 'Waqfeya', 'url' => 'https://waqfeya.net/', 'desc' => 'Perpustakaan digital berbahasa Arab'],
                ['name' => 'ManyBooks', 'url' => 'https://manybooks.net/', 'desc' => 'Free e-books collection'],
                ['name' => 'Noor-Book', 'url' => 'https://www.noor-book.com/en/', 'desc' => 'Digital library in multiple languages'],
                ['name' => 'PDF Books World', 'url' => 'https://www.pdfbooksworld.com/', 'desc' => 'Classic literature and academic books'],
                ['name' => 'Open Library', 'url' => 'https://openlibrary.org/', 'desc' => 'Internet Archive open library'],
                ['name' => 'PDF Drive', 'url' => 'https://www.pdfdrive.com/', 'desc' => 'Search engine for PDF files'],
                ['name' => 'NYU Arabic Collections', 'url' => 'http://dlib.nyu.edu/aco/', 'desc' => 'Arabic digital collections from NYU'],
            ];
            @endphp

            @foreach($ebooks as $e)
            <a href="{{ $e['url'] }}" target="_blank" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:border-emerald-200 hover:shadow-md transition group">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-book text-emerald-600 text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-semibold text-gray-900 text-sm group-hover:text-emerald-600 transition flex items-center gap-1">
                            {{ $e['name'] }} <i class="fas fa-external-link-alt text-[10px] text-gray-300"></i>
                        </h4>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $e['desc'] }}</p>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        <!-- Database Berlangganan -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg flex items-center gap-2">
            <i class="fas fa-database text-blue-500"></i> Database & Jurnal Berlangganan
        </h3>
        <div class="grid sm:grid-cols-2 gap-4 mb-6">
            @php
            $databases = [
                [
                    'name' => 'Gale (Teknik)',
                    'url' => 'https://link.gale.com/apps/SPJ.SP01?u=idfpptij',
                    'desc' => 'Jurnal teknik & sains dari Gale Academic OneFile',
                    'type' => 'database',
                    'color' => 'orange'
                ],
                [
                    'name' => 'Gale (Humaniora)',
                    'url' => 'https://link.gale.com/apps/SPJ.SP02?u=fpptijwt',
                    'desc' => 'Referensi humaniora dan sosial',
                    'type' => 'database',
                    'color' => 'orange'
                ],
                [
                    'name' => 'ProQuest',
                    'url' => 'https://www.proquest.com/login',
                    'desc' => 'Academic journals and dissertations',
                    'type' => 'journal',
                    'color' => 'blue'
                ],
            ];
            $colors = [
                'blue' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'border' => 'border-blue-200'],
                'orange' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-600', 'border' => 'border-orange-200'],
            ];
            @endphp

            @foreach($databases as $d)
            <a href="{{ $d['url'] }}" target="_blank" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:{{ $colors[$d['color']]['border'] }} hover:shadow-md transition group">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 {{ $colors[$d['color']]['bg'] }} rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas {{ $d['type'] === 'journal' ? 'fa-journal-whills' : 'fa-database' }} {{ $colors[$d['color']]['text'] }}"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <h4 class="font-bold text-gray-900 text-sm group-hover:text-primary-600">{{ $d['name'] }}</h4>
                            <span class="px-1.5 py-0.5 {{ $colors[$d['color']]['bg'] }} {{ $colors[$d['color']]['text'] }} text-[10px] font-medium rounded uppercase">{{ $d['type'] }}</span>
                        </div>
                        <p class="text-xs text-gray-500">{{ $d['desc'] }}</p>
                        <p class="text-xs text-amber-600 mt-2"><i class="fas fa-key mr-1"></i> Memerlukan kredensial</p>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        <!-- Credentials -->
        <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-5 lg:p-6 text-white mb-8">
            <div class="flex items-center gap-2 mb-4">
                <i class="fas fa-shield-alt text-amber-400"></i>
                <h3 class="font-bold">Kredensial Database</h3>
            </div>
            <p class="text-slate-300 text-sm mb-4">Gunakan akun berikut untuk mengakses database berlangganan. Jaga kerahasiaan kredensial ini.</p>
            
            <div class="grid sm:grid-cols-2 gap-4">
                <!-- Gale -->
                <div class="bg-white/10 backdrop-blur rounded-xl p-4">
                    <h4 class="font-semibold text-amber-400 mb-2">Gale (Teknik & Humaniora)</h4>
                    <p class="text-xs text-slate-400 mb-3">Koleksi jurnal teknik dan humaniora</p>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center justify-between bg-black/20 rounded-lg px-3 py-2">
                            <span class="text-slate-400 text-xs">Username</span>
                            <code class="text-emerald-400 font-mono">UnivKanB</code>
                        </div>
                        <div class="flex items-center justify-between bg-black/20 rounded-lg px-3 py-2">
                            <span class="text-slate-400 text-xs">Password</span>
                            <code class="text-emerald-400 font-mono">FPPTIjatim@1</code>
                        </div>
                    </div>
                </div>

                <!-- ProQuest -->
                <div class="bg-white/10 backdrop-blur rounded-xl p-4">
                    <h4 class="font-semibold text-amber-400 mb-2">ProQuest</h4>
                    <p class="text-xs text-slate-400 mb-3">Jurnal ekonomi, bisnis, dan kesehatan</p>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center justify-between bg-black/20 rounded-lg px-3 py-2">
                            <span class="text-slate-400 text-xs">Username</span>
                            <code class="text-emerald-400 font-mono">UDarussalam</code>
                        </div>
                        <div class="flex items-center justify-between bg-black/20 rounded-lg px-3 py-2">
                            <span class="text-slate-400 text-xs">Password</span>
                            <code class="text-emerald-400 font-mono">FPPTIjatim@1</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Perpusnas -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg flex items-center gap-2">
            <i class="fas fa-landmark text-purple-500"></i> Perpustakaan Nasional
        </h3>
        <div class="grid sm:grid-cols-2 gap-4">
            <a href="https://ipusnas.id" target="_blank" class="flex items-center gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:border-blue-300 hover:shadow-md transition group">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-book-reader text-blue-600 text-xl"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-bold text-gray-900 text-sm group-hover:text-primary-600">iPusnas</h4>
                    <p class="text-xs text-gray-500">Perpustakaan digital Perpusnas RI - Gratis untuk WNI</p>
                </div>
                <i class="fas fa-external-link-alt text-gray-300 group-hover:text-blue-500"></i>
            </a>

            <a href="https://bfrpn.perpusnas.go.id" target="_blank" class="flex items-center gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:border-green-300 hover:shadow-md transition group">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-star text-green-600 text-xl"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-bold text-gray-900 text-sm group-hover:text-primary-600">Bintang Pusnas</h4>
                    <p class="text-xs text-gray-500">Bahan Pustaka Perpusnas</p>
                </div>
                <i class="fas fa-external-link-alt text-gray-300 group-hover:text-green-500"></i>
            </a>
        </div>
    </section>
</x-opac.layout>
