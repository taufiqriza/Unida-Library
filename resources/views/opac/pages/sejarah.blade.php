<x-opac.layout title="Sejarah">
    <x-opac.page-header 
        title="Sejarah Perpustakaan" 
        subtitle="Perjalanan dari ISID hingga UNIDA Gontor"
        :breadcrumbs="[['label' => 'Profil'], ['label' => 'Sejarah']]"
    />

    <section class="max-w-4xl mx-auto px-4 py-6 lg:py-10">
        <!-- Intro -->
        <div class="bg-gradient-to-br from-primary-50 to-blue-50 rounded-2xl p-5 lg:p-6 border border-primary-100 mb-6 lg:mb-8">
            <p class="text-gray-700 text-sm lg:text-base leading-relaxed">
                Perpustakaan Universitas Darussalam Gontor (UNIDA) merupakan <span class="text-primary-600 font-semibold">lembaga kunci</span> dalam mendukung proses pembelajaran dan penelitian di kampus. Dengan perjalanan sejarah yang dimulai dari Institut Studi Islam Darussalam (ISID) hingga menjadi universitas yang terkemuka, perpustakaan ini berfungsi sebagai <span class="text-primary-600 font-semibold">pusat pengetahuan dan penelitian yang dinamis</span>.
            </p>
        </div>

        <!-- Timeline -->
        <div class="relative pl-6 lg:pl-8 border-l-2 border-primary-200 space-y-6 lg:space-y-8">
            <!-- Era ISID -->
            <div class="relative">
                <div class="absolute -left-[25px] lg:-left-[33px] w-4 h-4 lg:w-5 lg:h-5 bg-primary-500 rounded-full border-4 border-white shadow"></div>
                <div class="bg-white rounded-xl p-4 lg:p-5 shadow-sm border border-gray-100">
                    <span class="inline-block px-2 py-0.5 bg-primary-100 text-primary-700 rounded text-xs font-medium mb-2">Era Awal</span>
                    <h3 class="font-bold text-gray-900 mb-2">Institut Studi Islam Darussalam (ISID)</h3>
                    <p class="text-gray-600 text-sm">Perpustakaan bermula sebagai bagian dari ISID, melayani kebutuhan literatur mahasiswa dengan fokus pada studi Islam dan ilmu-ilmu keagamaan.</p>
                </div>
            </div>

            <!-- 2014 -->
            <div class="relative">
                <div class="absolute -left-[25px] lg:-left-[33px] w-4 h-4 lg:w-5 lg:h-5 bg-emerald-500 rounded-full border-4 border-white shadow"></div>
                <div class="bg-white rounded-xl p-4 lg:p-5 shadow-sm border border-gray-100">
                    <span class="inline-block px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded text-xs font-medium mb-2">2014</span>
                    <h3 class="font-bold text-gray-900 mb-2">Transformasi ke UNIDA Gontor</h3>
                    <p class="text-gray-600 text-sm">Dengan berdirinya Universitas Darussalam Gontor (UNIDA) pada tahun 2014, perpustakaan mengalami <span class="text-emerald-600 font-medium">transformasi signifikan</span>. Kebutuhan untuk mengembangkan dan memperluas perpustakaan menjadi lebih dari sekadar tempat penyimpanan buku.</p>
                </div>
            </div>

            <!-- Revitalisasi -->
            <div class="relative">
                <div class="absolute -left-[25px] lg:-left-[33px] w-4 h-4 lg:w-5 lg:h-5 bg-purple-500 rounded-full border-4 border-white shadow"></div>
                <div class="bg-white rounded-xl p-4 lg:p-5 shadow-sm border border-gray-100">
                    <span class="inline-block px-2 py-0.5 bg-purple-100 text-purple-700 rounded text-xs font-medium mb-2">Revitalisasi</span>
                    <h3 class="font-bold text-gray-900 mb-2">Pengembangan Koleksi & Fasilitas</h3>
                    <p class="text-gray-600 text-sm">Koleksi diperluas mencakup berbagai disiplin ilmu: <span class="text-purple-600 font-medium">ilmu sosial, humaniora, sains, hingga teknologi</span>, selain studi Islam.</p>
                </div>
            </div>

            <!-- Modern -->
            <div class="relative">
                <div class="absolute -left-[25px] lg:-left-[33px] w-4 h-4 lg:w-5 lg:h-5 bg-orange-500 rounded-full border-4 border-white shadow"></div>
                <div class="bg-gradient-to-br from-primary-600 to-primary-700 rounded-xl p-4 lg:p-5 text-white">
                    <span class="inline-block px-2 py-0.5 bg-white/20 rounded text-xs font-medium mb-2">Sekarang</span>
                    <h3 class="font-bold mb-2">Perpustakaan Modern</h3>
                    <p class="text-primary-100 text-sm">Fasilitas modern dengan ruang baca nyaman, ruang diskusi, dan area penelitian yang dirancang untuk memenuhi kebutuhan akademik mahasiswa dan dosen.</p>
                </div>
            </div>
        </div>

        <!-- Closing -->
        <div class="mt-6 lg:mt-8 bg-white rounded-xl p-4 lg:p-5 shadow-sm border border-gray-100">
            <p class="text-gray-700 text-sm lg:text-base leading-relaxed">
                <i class="fas fa-quote-left text-gray-300 mr-2"></i>
                Perpustakaan UNIDA adalah cerminan dari perjalanan panjang institusi. Dengan komitmen terhadap pengembangan pengetahuan dan penelitian, perpustakaan ini terus beradaptasi dengan perkembangan zaman, mendukung visi institusi untuk menjadi <span class="text-primary-600 font-semibold">pusat keunggulan akademik dan penelitian</span>.
            </p>
        </div>
    </section>
</x-opac.layout>
