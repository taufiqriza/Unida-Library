<x-opac.layout title="Visi & Misi">
    <x-opac.page-header 
        title="Visi & Misi" 
        subtitle="Komitmen kami dalam mendukung Tri Dharma Perguruan Tinggi"
        :breadcrumbs="[['label' => 'Profil'], ['label' => 'Visi & Misi']]"
    />

    <section class="max-w-4xl mx-auto px-4 py-6 lg:py-10">
        <!-- Visi -->
        <div class="mb-8 lg:mb-12">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-primary-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-eye text-primary-600 text-lg lg:text-xl"></i>
                </div>
                <h2 class="text-lg lg:text-xl font-bold text-gray-900">Visi</h2>
            </div>
            <div class="bg-gradient-to-br from-primary-50 to-blue-50 rounded-2xl p-5 lg:p-8 border border-primary-100">
                <p class="text-gray-700 leading-relaxed text-sm lg:text-base">
                    <i class="fas fa-quote-left text-primary-300 mr-2"></i>
                    Menjadi perpustakaan yang <span class="text-primary-600 font-semibold">unggul</span> terutama dalam mendukung pelaksanaan <span class="text-primary-600 font-semibold">Tri Dharma Perguruan Tinggi</span> dan pengembangan ilmu pengetahuan dan teknologi yang berorientasi pada <span class="text-primary-600 font-semibold">nilai-nilai keislaman</span>.
                    <i class="fas fa-quote-right text-primary-300 ml-2"></i>
                </p>
            </div>
        </div>

        <!-- Misi -->
        <div class="mb-8 lg:mb-12">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-rocket text-emerald-600 text-lg lg:text-xl"></i>
                </div>
                <h2 class="text-lg lg:text-xl font-bold text-gray-900">Misi</h2>
            </div>
            <div class="space-y-3">
                <div class="flex gap-3 lg:gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                    <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center text-white font-bold text-sm flex-shrink-0">1</div>
                    <p class="text-gray-700 text-sm lg:text-base">Menunjang keberhasilan misi UNIDA Gontor dengan memberikan layanan informasi secara <span class="text-primary-600 font-medium">efektif dan efisien</span> berbasis ICT.</p>
                </div>
                <div class="flex gap-3 lg:gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                    <div class="w-8 h-8 bg-emerald-600 rounded-lg flex items-center justify-center text-white font-bold text-sm flex-shrink-0">2</div>
                    <p class="text-gray-700 text-sm lg:text-base">Menjadi <span class="text-emerald-600 font-medium">mitra profesional</span> bagi seluruh sivitas akademika UNIDA Gontor dan masyarakat akademik pada umumnya.</p>
                </div>
                <div class="flex gap-3 lg:gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                    <div class="w-8 h-8 bg-purple-600 rounded-lg flex items-center justify-center text-white font-bold text-sm flex-shrink-0">3</div>
                    <p class="text-gray-700 text-sm lg:text-base">Membangun <span class="text-purple-600 font-medium">kerjasama dan jejaring</span> dengan berbagai perpustakaan di tingkat nasional, regional maupun internasional dengan tetap berpedoman pada nilai-nilai keislaman.</p>
                </div>
            </div>
        </div>

        <!-- Values -->
        <div>
            <h3 class="text-base lg:text-lg font-bold text-gray-900 mb-4 text-center">Nilai-Nilai Kami</h3>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
                <div class="text-center p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-star text-blue-600"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 text-sm">Keunggulan</h4>
                    <p class="text-xs text-gray-500 mt-1">Layanan terbaik</p>
                </div>
                <div class="text-center p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-handshake text-emerald-600"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 text-sm">Profesional</h4>
                    <p class="text-xs text-gray-500 mt-1">Standar tinggi</p>
                </div>
                <div class="text-center p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-mosque text-purple-600"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 text-sm">Islami</h4>
                    <p class="text-xs text-gray-500 mt-1">Nilai keislaman</p>
                </div>
                <div class="text-center p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-lightbulb text-orange-600"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 text-sm">Inovatif</h4>
                    <p class="text-xs text-gray-500 mt-1">Terus berkembang</p>
                </div>
            </div>
        </div>
    </section>
</x-opac.layout>
