<x-opac.layout title="E-Learning">
    <x-opac.page-header 
        title="E-Learning" 
        subtitle="Pembelajaran online perpustakaan"
        :breadcrumbs="[['label' => 'Discover'], ['label' => 'E-Learning']]"
    />

    <section class="max-w-4xl mx-auto px-4 py-6 lg:py-10">
        <!-- Intro -->
        <div class="bg-gradient-to-br from-violet-50 to-purple-50 rounded-2xl p-5 lg:p-6 border border-violet-100 mb-6">
            <p class="text-gray-700 text-sm lg:text-base leading-relaxed">
                Tingkatkan kemampuan literasi informasi Anda melalui berbagai <span class="text-violet-600 font-semibold">materi pembelajaran online</span> yang disediakan oleh Perpustakaan UNIDA Gontor. Belajar kapan saja, di mana saja!
            </p>
        </div>

        <!-- Learning Categories -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg">Materi Pembelajaran</h3>
        <div class="grid sm:grid-cols-2 gap-4 mb-8">
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:border-violet-200 hover:shadow-md transition">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-violet-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-search text-violet-600 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 mb-1">Literasi Informasi</h4>
                        <p class="text-xs text-gray-500 mb-2">Cara mencari, mengevaluasi, dan menggunakan informasi secara efektif</p>
                        <span class="inline-block px-2 py-0.5 bg-violet-100 text-violet-700 text-[10px] font-medium rounded">6 Modul</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:border-blue-200 hover:shadow-md transition">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-database text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 mb-1">Penggunaan Database</h4>
                        <p class="text-xs text-gray-500 mb-2">Tutorial mengakses dan memanfaatkan database jurnal ilmiah</p>
                        <span class="inline-block px-2 py-0.5 bg-blue-100 text-blue-700 text-[10px] font-medium rounded">4 Modul</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:border-emerald-200 hover:shadow-md transition">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-quote-right text-emerald-600 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 mb-1">Manajemen Referensi</h4>
                        <p class="text-xs text-gray-500 mb-2">Penggunaan Mendeley, Zotero, dan tools sitasi lainnya</p>
                        <span class="inline-block px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[10px] font-medium rounded">3 Modul</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:border-orange-200 hover:shadow-md transition">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-pen-fancy text-orange-600 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 mb-1">Penulisan Ilmiah</h4>
                        <p class="text-xs text-gray-500 mb-2">Teknik menulis karya ilmiah yang baik dan benar</p>
                        <span class="inline-block px-2 py-0.5 bg-orange-100 text-orange-700 text-[10px] font-medium rounded">5 Modul</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Coming Soon -->
        <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-8 text-center border border-gray-200 mb-6">
            <div class="w-16 h-16 bg-gray-200 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-laptop text-gray-400 text-2xl"></i>
            </div>
            <h3 class="font-bold text-gray-900 mb-2">Platform E-Learning Segera Hadir</h3>
            <p class="text-gray-500 text-sm mb-4">Kami sedang mengembangkan platform e-learning terintegrasi untuk pengalaman belajar yang lebih baik.</p>
            <div class="flex justify-center gap-2">
                <span class="px-3 py-1 bg-white text-gray-600 rounded-lg text-xs font-medium border">Video Tutorial</span>
                <span class="px-3 py-1 bg-white text-gray-600 rounded-lg text-xs font-medium border">Quiz Interaktif</span>
                <span class="px-3 py-1 bg-white text-gray-600 rounded-lg text-xs font-medium border">Sertifikat</span>
            </div>
        </div>

        <!-- Contact -->
        <div class="bg-amber-50 rounded-xl p-4 border border-amber-200">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-lightbulb text-amber-600"></i>
                <span class="font-bold text-gray-900 text-sm">Butuh Pelatihan Khusus?</span>
            </div>
            <p class="text-sm text-gray-600 mb-3">Hubungi kami untuk mengadakan sesi pelatihan literasi informasi untuk kelas atau kelompok Anda.</p>
            <a href="mailto:library@unida.gontor.ac.id" class="inline-flex items-center gap-1 text-amber-700 hover:text-amber-800 text-sm font-medium">
                <i class="fas fa-envelope"></i> library@unida.gontor.ac.id
            </a>
        </div>
    </section>
</x-opac.layout>
