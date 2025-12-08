<x-opac.layout title="MoU & Kerjasama">
    <x-opac.page-header 
        title="MoU & Kerjasama" 
        subtitle="Membangun jejaring dengan berbagai institusi"
        :breadcrumbs="[['label' => 'Profil'], ['label' => 'MoU & Kerjasama']]"
    />

    <section class="max-w-4xl mx-auto px-4 py-6 lg:py-10">
        <p class="text-gray-600 text-sm lg:text-base mb-6 text-center">
            Perpustakaan UNIDA Gontor menjalin kerjasama dengan berbagai institusi untuk meningkatkan kualitas layanan dan akses informasi.
        </p>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:border-primary-200 transition">
                <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center mb-3">
                    <i class="fas fa-university text-primary-600 text-xl"></i>
                </div>
                <h3 class="font-bold text-gray-900 text-sm mb-1">Perpustakaan Nasional RI</h3>
                <p class="text-xs text-gray-500 mb-3">Pengembangan koleksi dan akses e-resources nasional</p>
                <span class="inline-block px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-medium">Aktif</span>
            </div>

            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:border-primary-200 transition">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-3">
                    <i class="fas fa-globe text-blue-600 text-xl"></i>
                </div>
                <h3 class="font-bold text-gray-900 text-sm mb-1">Forum Perpustakaan PT</h3>
                <p class="text-xs text-gray-500 mb-3">Jejaring antar perpustakaan perguruan tinggi</p>
                <span class="inline-block px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-medium">Aktif</span>
            </div>

            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:border-primary-200 transition">
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-3">
                    <i class="fas fa-book-open text-emerald-600 text-xl"></i>
                </div>
                <h3 class="font-bold text-gray-900 text-sm mb-1">Penerbit & Distributor</h3>
                <p class="text-xs text-gray-500 mb-3">Pengadaan koleksi dengan penerbit terkemuka</p>
                <span class="inline-block px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-medium">Aktif</span>
            </div>
        </div>

        <!-- CTA -->
        <div class="bg-gradient-to-br from-primary-50 to-blue-50 rounded-xl p-5 lg:p-6 text-center border border-primary-100">
            <h3 class="font-bold text-gray-900 mb-2">Tertarik Menjalin Kerjasama?</h3>
            <p class="text-gray-600 text-sm mb-4">Hubungi kami untuk informasi lebih lanjut</p>
            <a href="mailto:library@unida.gontor.ac.id" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition">
                <i class="fas fa-envelope"></i> Hubungi Kami
            </a>
        </div>
    </section>
</x-opac.layout>
