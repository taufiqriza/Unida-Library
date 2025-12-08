<x-opac.layout title="Experience Survey">
    <x-opac.page-header 
        title="Experience Survey" 
        subtitle="Survei kepuasan pengguna perpustakaan"
        :breadcrumbs="[['label' => 'Discover'], ['label' => 'Experience Survey']]"
    />

    <section class="max-w-4xl mx-auto px-4 py-6 lg:py-10">
        <!-- Intro -->
        <div class="bg-gradient-to-br from-lime-50 to-green-50 rounded-2xl p-5 lg:p-6 border border-lime-100 mb-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-lime-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-poll text-lime-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 mb-1">Suara Anda Penting!</h3>
                    <p class="text-gray-600 text-sm">Bantu kami meningkatkan kualitas layanan perpustakaan dengan mengisi survei kepuasan pengguna. Masukan Anda sangat berharga untuk pengembangan layanan kami.</p>
                </div>
            </div>
        </div>

        <!-- Survey Types -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg">Jenis Survei</h3>
        <div class="grid sm:grid-cols-2 gap-4 mb-8">
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-smile text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 mb-1">Kepuasan Layanan</h4>
                        <p class="text-xs text-gray-500 mb-3">Evaluasi kualitas layanan sirkulasi, referensi, dan fasilitas</p>
                        <span class="inline-block px-2 py-0.5 bg-blue-100 text-blue-700 text-[10px] font-medium rounded">5 menit</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-book text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 mb-1">Kebutuhan Koleksi</h4>
                        <p class="text-xs text-gray-500 mb-3">Usulan pengadaan buku dan koleksi baru</p>
                        <span class="inline-block px-2 py-0.5 bg-purple-100 text-purple-700 text-[10px] font-medium rounded">3 menit</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-desktop text-emerald-600 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 mb-1">Fasilitas Digital</h4>
                        <p class="text-xs text-gray-500 mb-3">Feedback untuk OPAC, e-book, dan layanan digital</p>
                        <span class="inline-block px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[10px] font-medium rounded">5 menit</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-calendar-check text-orange-600 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 mb-1">Evaluasi Event</h4>
                        <p class="text-xs text-gray-500 mb-3">Feedback setelah mengikuti kegiatan perpustakaan</p>
                        <span class="inline-block px-2 py-0.5 bg-orange-100 text-orange-700 text-[10px] font-medium rounded">3 menit</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Survey -->
        <div class="bg-gradient-to-r from-lime-500 to-green-600 rounded-xl p-5 lg:p-6 text-white mb-6">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <span class="inline-block px-2 py-0.5 bg-white/20 rounded text-xs font-medium mb-2">SURVEI AKTIF</span>
                    <h3 class="font-bold text-lg">Survei Kepuasan Pengguna 2024</h3>
                    <p class="text-lime-100 text-sm">Berikan penilaian Anda terhadap layanan perpustakaan</p>
                </div>
                <a href="https://forms.google.com" target="_blank" class="px-5 py-2.5 bg-white text-lime-600 rounded-lg font-medium hover:bg-lime-50 transition flex-shrink-0">
                    <i class="fas fa-external-link-alt mr-2"></i> Isi Survei
                </a>
            </div>
        </div>

        <!-- Why Survey -->
        <div class="bg-amber-50 rounded-xl p-4 border border-amber-200">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-lightbulb text-amber-600"></i>
                <span class="font-bold text-gray-900 text-sm">Mengapa Survei Penting?</span>
            </div>
            <ul class="text-sm text-gray-600 space-y-1">
                <li class="flex items-start gap-2"><i class="fas fa-check text-amber-600 mt-0.5 text-xs"></i> Membantu kami memahami kebutuhan pengguna</li>
                <li class="flex items-start gap-2"><i class="fas fa-check text-amber-600 mt-0.5 text-xs"></i> Menjadi dasar pengembangan layanan</li>
                <li class="flex items-start gap-2"><i class="fas fa-check text-amber-600 mt-0.5 text-xs"></i> Meningkatkan kualitas fasilitas dan koleksi</li>
            </ul>
        </div>
    </section>
</x-opac.layout>
