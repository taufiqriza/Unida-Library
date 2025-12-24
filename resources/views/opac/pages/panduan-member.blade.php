<x-opac.layout :title="__('opac.pages.panduan_member.title')">
    <x-opac.page-header 
        :title="__('opac.pages.panduan_member.title')" 
        :subtitle="__('opac.pages.panduan_member.subtitle')"
        :breadcrumbs="[['label' => __('opac.pages.panduan_member.breadcrumb')], ['label' => __('opac.pages.panduan_member.title')]]"
    />

    <section class="max-w-5xl mx-auto px-4 py-8 lg:py-12">
        
        {{-- Hero Card --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-blue-600 via-indigo-600 to-violet-600 rounded-3xl p-8 lg:p-12 mb-12 text-white">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
            <div class="relative z-10 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-sm mb-4">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                    Member Portal
                </div>
                <h2 class="text-2xl lg:text-3xl font-bold mb-3">Akses Semua Layanan Perpustakaan</h2>
                <p class="text-blue-100 text-sm lg:text-base leading-relaxed">
                    Login dengan email UNIDA untuk akses penuh ke koleksi digital, e-journal berlangganan, repository, cek plagiasi, dan bebas pustaka.
                </p>
            </div>
        </div>

        {{-- Quick Steps --}}
        <div class="mb-12">
            <h3 class="text-center text-xl font-bold text-gray-900 mb-8">Mulai dalam 3 Langkah</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="group relative">
                    <div class="absolute -top-3 -left-3 w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white font-bold shadow-lg shadow-blue-500/30 group-hover:scale-110 transition">1</div>
                    <div class="bg-white rounded-2xl p-6 pt-8 shadow-sm border border-gray-100 h-full hover:shadow-lg hover:border-blue-100 transition-all duration-300">
                        <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center mb-4">
                            <i class="fab fa-google text-blue-500 text-xl"></i>
                        </div>
                        <h4 class="font-bold text-gray-900 mb-2">Login dengan Email UNIDA</h4>
                        <p class="text-sm text-gray-500">Klik "Masuk dengan Email UNIDA" dan pilih akun @unida.gontor.ac.id</p>
                    </div>
                </div>
                <div class="group relative">
                    <div class="absolute -top-3 -left-3 w-10 h-10 bg-gradient-to-br from-violet-500 to-violet-600 rounded-xl flex items-center justify-center text-white font-bold shadow-lg shadow-violet-500/30 group-hover:scale-110 transition">2</div>
                    <div class="bg-white rounded-2xl p-6 pt-8 shadow-sm border border-gray-100 h-full hover:shadow-lg hover:border-violet-100 transition-all duration-300">
                        <div class="w-12 h-12 bg-violet-50 rounded-xl flex items-center justify-center mb-4">
                            <i class="fas fa-user-edit text-violet-500 text-xl"></i>
                        </div>
                        <h4 class="font-bold text-gray-900 mb-2">Lengkapi Profil</h4>
                        <p class="text-sm text-gray-500">Jika pertama kali, isi NIM dan program studi untuk aktivasi</p>
                    </div>
                </div>
                <div class="group relative">
                    <div class="absolute -top-3 -left-3 w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center text-white font-bold shadow-lg shadow-emerald-500/30 group-hover:scale-110 transition">3</div>
                    <div class="bg-white rounded-2xl p-6 pt-8 shadow-sm border border-gray-100 h-full hover:shadow-lg hover:border-emerald-100 transition-all duration-300">
                        <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center mb-4">
                            <i class="fas fa-check-circle text-emerald-500 text-xl"></i>
                        </div>
                        <h4 class="font-bold text-gray-900 mb-2">Akses Aktif!</h4>
                        <p class="text-sm text-gray-500">Langsung bisa upload tugas akhir, cek plagiasi, dan lainnya</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Member Features Grid --}}
        <div class="mb-12">
            <h3 class="text-center text-xl font-bold text-gray-900 mb-2">Fitur Dashboard Member</h3>
            <p class="text-center text-gray-500 text-sm mb-8">Semua yang Anda butuhkan dalam satu portal</p>
            
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100/50 rounded-2xl p-5 border border-blue-100 hover:scale-105 transition-transform cursor-default">
                    <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center mb-3">
                        <i class="fas fa-book text-white"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 text-sm mb-1">Peminjaman</h4>
                    <p class="text-xs text-gray-500">Lihat buku yang dipinjam & jatuh tempo</p>
                </div>
                <div class="bg-gradient-to-br from-violet-50 to-violet-100/50 rounded-2xl p-5 border border-violet-100 hover:scale-105 transition-transform cursor-default">
                    <div class="w-10 h-10 bg-violet-500 rounded-xl flex items-center justify-center mb-3">
                        <i class="fas fa-upload text-white"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 text-sm mb-1">Upload TA</h4>
                    <p class="text-xs text-gray-500">Submit tugas akhir ke repository</p>
                </div>
                <div class="bg-gradient-to-br from-teal-50 to-teal-100/50 rounded-2xl p-5 border border-teal-100 hover:scale-105 transition-transform cursor-default">
                    <div class="w-10 h-10 bg-teal-500 rounded-xl flex items-center justify-center mb-3">
                        <i class="fas fa-shield-alt text-white"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 text-sm mb-1">Cek Plagiasi</h4>
                    <p class="text-xs text-gray-500">Scan dengan iThenticate</p>
                </div>
                <div class="bg-gradient-to-br from-emerald-50 to-emerald-100/50 rounded-2xl p-5 border border-emerald-100 hover:scale-105 transition-transform cursor-default">
                    <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center mb-3">
                        <i class="fas fa-certificate text-white"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 text-sm mb-1">Bebas Pustaka</h4>
                    <p class="text-xs text-gray-500">Surat otomatis saat TA disetujui</p>
                </div>
            </div>
        </div>

        {{-- Digital Collections --}}
        <div class="mb-12">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-amber-400 to-orange-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-crown text-white text-xs"></i>
                    </div>
                    Koleksi Digital Lokal
                </h3>
                <a href="{{ url('/page/e-resources') }}" class="text-sm text-primary-600 hover:underline">Lihat Semua →</a>
            </div>
            
            <div class="grid md:grid-cols-2 gap-4">
                {{-- Maktabah Shamela --}}
                <a href="{{ route('opac.shamela.index') }}" class="group relative bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-5 text-white overflow-hidden hover:shadow-xl hover:shadow-blue-500/25 transition-all">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                    <div class="relative flex items-start gap-4">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-book-quran text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-lg">Maktabah Shamela</h4>
                            <p class="text-blue-200 text-xs mb-2" dir="rtl">المكتبة الشاملة</p>
                            <p class="text-blue-100 text-sm">8,425 kitab klasik Islam - Tafsir, Hadits, Fiqih, dll.</p>
                            <div class="flex items-center gap-2 mt-3">
                                <span class="px-2 py-1 bg-white/15 text-[10px] rounded">Database Lokal</span>
                                <span class="px-2 py-1 bg-white/15 text-[10px] rounded">7M+ Halaman</span>
                            </div>
                        </div>
                        <i class="fas fa-arrow-right text-blue-300 group-hover:translate-x-1 transition self-center"></i>
                    </div>
                </a>

                {{-- Universitaria --}}
                <a href="{{ route('opac.universitaria.index') }}" class="group relative bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl p-5 text-white overflow-hidden hover:shadow-xl hover:shadow-amber-500/25 transition-all">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                    <div class="relative flex items-start gap-4">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-landmark text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-lg">Universitaria</h4>
                            <p class="text-amber-100 text-sm">Arsip historis UNIDA Gontor - dokumen langka universitas.</p>
                            <div class="flex items-center gap-2 mt-3">
                                <span class="px-2 py-1 bg-white/15 text-[10px] rounded">70+ Dokumen</span>
                                <span class="px-2 py-1 bg-white/15 text-[10px] rounded">Premium</span>
                            </div>
                        </div>
                        <i class="fas fa-arrow-right text-amber-200 group-hover:translate-x-1 transition self-center"></i>
                    </div>
                </a>
            </div>
        </div>

        {{-- E-Journal Berlangganan --}}
        <div class="mb-12">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-database text-blue-600 text-sm"></i>
                    </div>
                    Database Jurnal Berlangganan
                </h3>
                <a href="{{ url('/page/journal-subscription') }}" class="text-sm text-primary-600 hover:underline">Lihat Detail →</a>
            </div>

            <div class="bg-gradient-to-r from-slate-800 to-slate-900 rounded-2xl p-5 text-white mb-4">
                <div class="flex items-center gap-2 mb-3">
                    <i class="fas fa-info-circle text-blue-400"></i>
                    <span class="text-sm">Akses jurnal internasional via FPPTI Jawa Timur</span>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div class="bg-white/10 rounded-xl p-3 text-center">
                        <div class="w-10 h-10 bg-orange-500/30 rounded-lg flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-database text-orange-400"></i>
                        </div>
                        <p class="font-bold text-sm">Gale Academic</p>
                        <p class="text-[10px] text-slate-400">32K+ Journals</p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-3 text-center">
                        <div class="w-10 h-10 bg-blue-500/30 rounded-lg flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-journal-whills text-blue-400"></i>
                        </div>
                        <p class="font-bold text-sm">ProQuest</p>
                        <p class="text-[10px] text-slate-400">90K+ Articles</p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-3 text-center">
                        <div class="w-10 h-10 bg-emerald-500/30 rounded-lg flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-key text-emerald-400"></i>
                        </div>
                        <p class="font-bold text-sm">Akses</p>
                        <a href="{{ url('/database-access') }}" class="text-[10px] text-emerald-400 hover:underline">Portal Login →</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Free E-Resources --}}
        <div class="mb-12">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-book-open text-emerald-600 text-sm"></i>
                    </div>
                    E-Resources Gratis
                </h3>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <a href="https://ipusnas.id" target="_blank" class="bg-white rounded-xl p-4 border border-gray-100 hover:shadow-lg hover:border-blue-200 transition group text-center">
                    <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition">
                        <i class="fas fa-book-reader text-white"></i>
                    </div>
                    <p class="font-semibold text-gray-900 text-sm">iPusnas</p>
                    <p class="text-[10px] text-gray-500">500K+ E-Books</p>
                </a>
                <a href="https://waqfeya.net" target="_blank" class="bg-white rounded-xl p-4 border border-gray-100 hover:shadow-lg hover:border-teal-200 transition group text-center">
                    <div class="w-10 h-10 bg-teal-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition">
                        <i class="fas fa-book-quran text-white"></i>
                    </div>
                    <p class="font-semibold text-gray-900 text-sm">Waqfeya</p>
                    <p class="text-[10px] text-gray-500">15K+ Kitab Arab</p>
                </a>
                <a href="https://openlibrary.org" target="_blank" class="bg-white rounded-xl p-4 border border-gray-100 hover:shadow-lg hover:border-indigo-200 transition group text-center">
                    <div class="w-10 h-10 bg-indigo-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition">
                        <i class="fas fa-landmark text-white"></i>
                    </div>
                    <p class="font-semibold text-gray-900 text-sm">Open Library</p>
                    <p class="text-[10px] text-gray-500">4M+ Books</p>
                </a>
                <a href="https://www.pdfdrive.com" target="_blank" class="bg-white rounded-xl p-4 border border-gray-100 hover:shadow-lg hover:border-rose-200 transition group text-center">
                    <div class="w-10 h-10 bg-rose-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition">
                        <i class="fas fa-file-pdf text-white"></i>
                    </div>
                    <p class="font-semibold text-gray-900 text-sm">PDF Drive</p>
                    <p class="text-[10px] text-gray-500">80M+ PDFs</p>
                </a>
            </div>
        </div>

        {{-- Clearance Letter Info --}}
        <div class="bg-gradient-to-r from-emerald-500 to-teal-500 rounded-3xl p-6 lg:p-8 mb-12 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10 flex flex-col lg:flex-row items-start lg:items-center gap-6">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-file-certificate text-3xl"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-bold text-lg mb-2">Surat Bebas Pustaka Otomatis</h4>
                    <p class="text-emerald-100 text-sm leading-relaxed">
                        Saat tugas akhir disetujui admin + tidak ada pinjaman/denda aktif = surat langsung terbit di dashboard dan bisa download PDF.
                    </p>
                </div>
            </div>
        </div>

        {{-- FAQ Accordion --}}
        <div class="mb-12">
            <h3 class="text-center text-xl font-bold text-gray-900 mb-8">Pertanyaan Umum</h3>
            <div class="max-w-2xl mx-auto space-y-3">
                <details class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <summary class="p-5 cursor-pointer flex items-center justify-between font-medium text-gray-900">
                        <span class="flex items-center gap-3">
                            <span class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center text-blue-500"><i class="fas fa-envelope"></i></span>
                            Email UNIDA tidak bisa login?
                        </span>
                        <i class="fas fa-chevron-down text-gray-400 group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <div class="px-5 pb-5 text-sm text-gray-600 border-t border-gray-50 pt-4">
                        Pastikan email @unida.gontor.ac.id atau @mhs.unida.gontor.ac.id. Jika masih error, hubungi IT Helpdesk UNIDA.
                    </div>
                </details>

                <details class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <summary class="p-5 cursor-pointer flex items-center justify-between font-medium text-gray-900">
                        <span class="flex items-center gap-3">
                            <span class="w-8 h-8 bg-violet-50 rounded-lg flex items-center justify-center text-violet-500"><i class="fas fa-user-plus"></i></span>
                            Bukan mahasiswa UNIDA?
                        </span>
                        <i class="fas fa-chevron-down text-gray-400 group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <div class="px-5 pb-5 text-sm text-gray-600 border-t border-gray-50 pt-4">
                        Klik "Daftar Manual" untuk mendaftar sebagai anggota umum. Isi formulir dan tunggu verifikasi dari admin perpustakaan.
                    </div>
                </details>

                <details class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <summary class="p-5 cursor-pointer flex items-center justify-between font-medium text-gray-900">
                        <span class="flex items-center gap-3">
                            <span class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center text-amber-500"><i class="fas fa-database"></i></span>
                            Bagaimana akses database FPPTI?
                        </span>
                        <i class="fas fa-chevron-down text-gray-400 group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <div class="px-5 pb-5 text-sm text-gray-600 border-t border-gray-50 pt-4">
                        Kunjungi halaman <a href="{{ route('opac.database-access') }}" class="text-primary-600 hover:underline">Akses Database</a> untuk melihat username dan password. Gunakan credentials tersebut untuk login ke Gale atau ProQuest.
                    </div>
                </details>

                <details class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <summary class="p-5 cursor-pointer flex items-center justify-between font-medium text-gray-900">
                        <span class="flex items-center gap-3">
                            <span class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center text-emerald-500"><i class="fas fa-file-alt"></i></span>
                            Kapan surat bebas pustaka terbit?
                        </span>
                        <i class="fas fa-chevron-down text-gray-400 group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <div class="px-5 pb-5 text-sm text-gray-600 border-t border-gray-50 pt-4">
                        Otomatis saat admin menyetujui tugas akhir Anda, asalkan tidak ada peminjaman atau denda yang belum lunas.
                    </div>
                </details>
            </div>
        </div>

        {{-- CTA --}}
        <div class="text-center">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-2xl hover:shadow-xl hover:shadow-blue-500/25 hover:-translate-y-1 transition-all duration-300">
                <i class="fas fa-sign-in-alt text-lg"></i>
                <span>Masuk Sekarang</span>
                <i class="fas fa-arrow-right text-sm opacity-70"></i>
            </a>
            <p class="text-gray-400 text-sm mt-4">Login dengan email UNIDA untuk akses langsung</p>
        </div>
    </section>
</x-opac.layout>
