<x-opac.layout title="Virtual Tour">
    <x-opac.page-header 
        title="Virtual Tour 360°" 
        subtitle="Jelajahi perpustakaan secara virtual"
        :breadcrumbs="[['label' => 'Discover'], ['label' => 'Virtual Tour']]"
    />

    <section class="max-w-4xl mx-auto px-4 py-6 lg:py-10">
        <!-- Intro -->
        <div class="bg-gradient-to-br from-cyan-50 to-blue-50 rounded-2xl p-5 lg:p-6 border border-cyan-100 mb-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-cyan-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-vr-cardboard text-cyan-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 mb-1">Pengalaman Immersive</h3>
                    <p class="text-gray-600 text-sm">Nikmati pengalaman menjelajahi Perpustakaan UNIDA Gontor secara virtual dengan teknologi 360°. Lihat fasilitas, ruang baca, dan koleksi kami dari mana saja!</p>
                </div>
            </div>
        </div>

        <!-- Virtual Tour Embed -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-primary-600 to-primary-700 px-4 py-3 flex items-center justify-between">
                <h3 class="text-white font-semibold text-sm flex items-center gap-2">
                    <i class="fas fa-street-view"></i> Virtual Tour Perpustakaan
                </h3>
                <a href="https://tour.digilib-unida.id" target="_blank" class="text-xs text-primary-200 hover:text-white transition">
                    <i class="fas fa-external-link-alt mr-1"></i> Buka di Tab Baru
                </a>
            </div>
            <div class="aspect-video bg-gray-100">
                <iframe 
                    src="https://tour.digilib-unida.id" 
                    class="w-full h-full border-0"
                    allowfullscreen
                    loading="lazy"
                ></iframe>
            </div>
        </div>

        <!-- Direct Access -->
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 mb-6">
            <h3 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                <i class="fas fa-link text-primary-500"></i> Akses Langsung
            </h3>
            <a href="https://tour.digilib-unida.id" target="_blank" class="flex items-center justify-between p-4 bg-gradient-to-r from-cyan-50 to-blue-50 rounded-xl hover:from-cyan-100 hover:to-blue-100 transition group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm">
                        <i class="fas fa-globe text-cyan-600"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 group-hover:text-primary-600">tour.digilib-unida.id</p>
                        <p class="text-xs text-gray-500">Klik untuk membuka virtual tour</p>
                    </div>
                </div>
                <i class="fas fa-arrow-right text-gray-400 group-hover:text-primary-600 transition"></i>
            </a>
        </div>

        <!-- Features -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg">Yang Bisa Anda Jelajahi</h3>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="text-center p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-door-open text-blue-600"></i>
                </div>
                <p class="text-xs font-medium text-gray-900">Lobby & Resepsionis</p>
            </div>
            <div class="text-center p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-book-reader text-emerald-600"></i>
                </div>
                <p class="text-xs font-medium text-gray-900">Ruang Baca</p>
            </div>
            <div class="text-center p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-books text-purple-600"></i>
                </div>
                <p class="text-xs font-medium text-gray-900">Rak Koleksi</p>
            </div>
            <div class="text-center p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-users text-orange-600"></i>
                </div>
                <p class="text-xs font-medium text-gray-900">Ruang Diskusi</p>
            </div>
        </div>
    </section>
</x-opac.layout>
