<x-opac.layout title="Panduan OPAC">
    <x-opac.page-header 
        title="Panduan OPAC" 
        subtitle="Cara menggunakan katalog online perpustakaan"
        :breadcrumbs="[['label' => 'Guide'], ['label' => 'Panduan OPAC']]"
    />

    <section class="max-w-4xl mx-auto px-4 py-6 lg:py-10">
        <!-- Intro -->
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-5 lg:p-6 border border-blue-100 mb-6">
            <p class="text-gray-700 text-sm lg:text-base leading-relaxed">
                <span class="text-blue-600 font-semibold">OPAC (Online Public Access Catalog)</span> adalah sistem katalog online yang memungkinkan Anda mencari dan menemukan koleksi perpustakaan dengan mudah dan cepat.
            </p>
        </div>

        <!-- Steps -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg">Langkah-langkah Pencarian</h3>
        <div class="space-y-4 mb-8">
            <div class="flex gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white font-bold flex-shrink-0">1</div>
                <div>
                    <h4 class="font-semibold text-gray-900 mb-1">Buka Halaman Katalog</h4>
                    <p class="text-sm text-gray-600">Klik menu <strong>Katalog</strong> atau gunakan kotak pencarian di halaman utama</p>
                </div>
            </div>
            <div class="flex gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white font-bold flex-shrink-0">2</div>
                <div>
                    <h4 class="font-semibold text-gray-900 mb-1">Masukkan Kata Kunci</h4>
                    <p class="text-sm text-gray-600">Ketik judul buku, nama pengarang, atau ISBN yang ingin dicari</p>
                </div>
            </div>
            <div class="flex gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white font-bold flex-shrink-0">3</div>
                <div>
                    <h4 class="font-semibold text-gray-900 mb-1">Filter Hasil Pencarian</h4>
                    <p class="text-sm text-gray-600">Gunakan filter subjek atau lokasi cabang untuk mempersempit hasil</p>
                </div>
            </div>
            <div class="flex gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white font-bold flex-shrink-0">4</div>
                <div>
                    <h4 class="font-semibold text-gray-900 mb-1">Lihat Detail Buku</h4>
                    <p class="text-sm text-gray-600">Klik judul buku untuk melihat informasi lengkap dan ketersediaan</p>
                </div>
            </div>
        </div>

        <!-- Tips -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg">Tips Pencarian Efektif</h3>
        <div class="grid sm:grid-cols-2 gap-3 mb-6">
            <div class="bg-emerald-50 rounded-xl p-4 border border-emerald-100">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-lightbulb text-emerald-600"></i>
                    <span class="font-semibold text-gray-900 text-sm">Gunakan Kata Kunci Spesifik</span>
                </div>
                <p class="text-xs text-gray-600">Semakin spesifik kata kunci, semakin akurat hasil pencarian</p>
            </div>
            <div class="bg-emerald-50 rounded-xl p-4 border border-emerald-100">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-lightbulb text-emerald-600"></i>
                    <span class="font-semibold text-gray-900 text-sm">Cek Ketersediaan</span>
                </div>
                <p class="text-xs text-gray-600">Pastikan status buku "Tersedia" sebelum ke perpustakaan</p>
            </div>
        </div>

        <!-- CTA -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl p-5 text-white text-center">
            <h4 class="font-bold mb-2">Mulai Pencarian Sekarang</h4>
            <p class="text-blue-100 text-sm mb-4">Temukan buku yang Anda butuhkan</p>
            <a href="{{ route('opac.catalog') }}" class="inline-flex items-center gap-2 px-5 py-2 bg-white text-blue-600 rounded-lg font-medium hover:bg-blue-50 transition">
                <i class="fas fa-search"></i> Buka Katalog
            </a>
        </div>
    </section>
</x-opac.layout>
