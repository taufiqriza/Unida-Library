<x-opac.layout title="Prosa Kreatif">
    <x-opac.page-header 
        title="Prosa Kreatif" 
        subtitle="Komunitas menulis kreatif perpustakaan"
        :breadcrumbs="[['label' => 'Discover'], ['label' => 'Prosa Kreatif']]"
    />

    <section class="max-w-4xl mx-auto px-4 py-6 lg:py-10">
        <!-- Intro -->
        <div class="bg-gradient-to-br from-rose-50 to-pink-50 rounded-2xl p-5 lg:p-6 border border-rose-100 mb-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-rose-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-pen-fancy text-rose-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 mb-1">Wadah Kreativitas Menulis</h3>
                    <p class="text-gray-600 text-sm">Prosa Kreatif adalah komunitas menulis yang diinisiasi oleh Perpustakaan UNIDA Gontor untuk mengembangkan bakat menulis civitas akademika dalam berbagai genre sastra dan non-fiksi.</p>
                </div>
            </div>
        </div>

        <!-- Writing Categories -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg">Genre Tulisan</h3>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-8">
            <div class="text-center p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="w-10 h-10 bg-rose-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-feather-alt text-rose-600"></i>
                </div>
                <p class="text-xs font-medium text-gray-900">Puisi</p>
            </div>
            <div class="text-center p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-book text-purple-600"></i>
                </div>
                <p class="text-xs font-medium text-gray-900">Cerpen</p>
            </div>
            <div class="text-center p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-newspaper text-blue-600"></i>
                </div>
                <p class="text-xs font-medium text-gray-900">Esai</p>
            </div>
            <div class="text-center p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-star text-emerald-600"></i>
                </div>
                <p class="text-xs font-medium text-gray-900">Review Buku</p>
            </div>
        </div>

        <!-- Activities -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg">Kegiatan Komunitas</h3>
        <div class="space-y-3 mb-8">
            <div class="flex items-center gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-calendar-week text-amber-600"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 text-sm">Kelas Menulis Mingguan</h4>
                    <p class="text-xs text-gray-500">Diskusi dan workshop menulis setiap minggu</p>
                </div>
            </div>
            <div class="flex items-center gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 text-sm">Sharing Session</h4>
                    <p class="text-xs text-gray-500">Berbagi pengalaman dengan penulis dan praktisi</p>
                </div>
            </div>
            <div class="flex items-center gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-trophy text-emerald-600"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 text-sm">Lomba Menulis</h4>
                    <p class="text-xs text-gray-500">Kompetisi menulis berkala dengan hadiah menarik</p>
                </div>
            </div>
            <div class="flex items-center gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-book-open text-purple-600"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 text-sm">Penerbitan Antologi</h4>
                    <p class="text-xs text-gray-500">Publikasi karya terbaik dalam bentuk buku antologi</p>
                </div>
            </div>
        </div>

        <!-- Join CTA -->
        <div class="bg-gradient-to-r from-rose-500 to-pink-600 rounded-xl p-5 lg:p-6 text-white text-center">
            <h3 class="font-bold text-xl mb-2">Bergabung dengan Prosa Kreatif</h3>
            <p class="text-rose-100 text-sm mb-4">Kembangkan bakat menulismu bersama komunitas kami</p>
            <div class="flex flex-wrap justify-center gap-3">
                <a href="https://wa.me/6285183053934?text=Halo, saya ingin bergabung dengan Prosa Kreatif" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-white text-rose-600 rounded-lg text-sm font-medium hover:bg-rose-50 transition">
                    <i class="fab fa-whatsapp"></i> Daftar via WhatsApp
                </a>
                <a href="mailto:library@unida.gontor.ac.id?subject=Pendaftaran Prosa Kreatif" class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 text-white rounded-lg text-sm font-medium hover:bg-white/20 transition">
                    <i class="fas fa-envelope"></i> Email
                </a>
            </div>
        </div>
    </section>
</x-opac.layout>
