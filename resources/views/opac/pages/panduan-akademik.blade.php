<x-opac.layout title="Panduan Akademik">
    <x-opac.page-header 
        title="Panduan Akademik" 
        subtitle="Informasi akademik terkait perpustakaan"
        :breadcrumbs="[['label' => 'Guide'], ['label' => 'Panduan Akademik']]"
    />

    <section class="max-w-4xl mx-auto px-4 py-6 lg:py-10">
        <!-- Intro -->
        <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl p-5 lg:p-6 border border-purple-100 mb-6">
            <p class="text-gray-700 text-sm lg:text-base leading-relaxed">
                Panduan ini berisi informasi penting terkait <span class="text-purple-600 font-semibold">kewajiban akademik</span> mahasiswa yang berhubungan dengan perpustakaan, termasuk bebas pustaka dan ketentuan tugas akhir.
            </p>
        </div>

        <!-- Bebas Pustaka -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg flex items-center gap-2">
            <i class="fas fa-certificate text-purple-500"></i> Bebas Pustaka
        </h3>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 mb-6">
            <p class="text-sm text-gray-600 mb-4">Surat Keterangan Bebas Pustaka diperlukan untuk:</p>
            <div class="grid sm:grid-cols-2 gap-3 mb-4">
                <div class="flex items-center gap-2 text-sm text-gray-700">
                    <i class="fas fa-check text-purple-500"></i> Pendaftaran wisuda
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-700">
                    <i class="fas fa-check text-purple-500"></i> Pengambilan ijazah
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-700">
                    <i class="fas fa-check text-purple-500"></i> Pindah/keluar universitas
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-700">
                    <i class="fas fa-check text-purple-500"></i> Cuti akademik
                </div>
            </div>
            <div class="bg-purple-50 rounded-lg p-3 border border-purple-100">
                <p class="text-xs text-purple-700"><strong>Syarat:</strong> Tidak memiliki pinjaman aktif dan denda, serta telah mengunggah tugas akhir ke repository.</p>
            </div>
        </div>

        <!-- Ketentuan Tugas Akhir -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg flex items-center gap-2">
            <i class="fas fa-graduation-cap text-indigo-500"></i> Ketentuan Tugas Akhir
        </h3>
        <div class="space-y-3 mb-8">
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <h4 class="font-semibold text-gray-900 text-sm mb-2">Wajib Serah Simpan</h4>
                <p class="text-xs text-gray-600">Setiap mahasiswa wajib menyerahkan 1 eksemplar hardcopy tugas akhir ke perpustakaan</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <h4 class="font-semibold text-gray-900 text-sm mb-2">Upload Repository</h4>
                <p class="text-xs text-gray-600">Wajib mengunggah softcopy ke repository institusi (repo.unida.gontor.ac.id)</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <h4 class="font-semibold text-gray-900 text-sm mb-2">Cek Plagiarisme</h4>
                <p class="text-xs text-gray-600">Tugas akhir harus lolos pengecekan plagiarisme dengan similarity maksimal 25%</p>
            </div>
        </div>

        <!-- Denda -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg flex items-center gap-2">
            <i class="fas fa-exclamation-triangle text-amber-500"></i> Ketentuan Denda
        </h3>
        <div class="bg-amber-50 rounded-xl p-5 border border-amber-200 mb-6">
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-semibold text-gray-900 mb-1">Keterlambatan</p>
                    <p class="text-xs text-gray-600">Rp 500 / buku / hari</p>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900 mb-1">Buku Hilang/Rusak</p>
                    <p class="text-xs text-gray-600">Ganti buku yang sama atau 2x harga buku</p>
                </div>
            </div>
        </div>

        <!-- Contact -->
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-xl p-5 text-white flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <h4 class="font-bold">Ada Pertanyaan?</h4>
                <p class="text-purple-200 text-sm">Hubungi bagian layanan perpustakaan</p>
            </div>
            <a href="mailto:library@unida.gontor.ac.id" class="px-4 py-2 bg-white text-purple-600 rounded-lg text-sm font-medium hover:bg-purple-50 transition">
                <i class="fas fa-envelope mr-1"></i> Email Kami
            </a>
        </div>
    </section>
</x-opac.layout>
