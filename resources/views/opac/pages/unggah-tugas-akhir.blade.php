<x-opac.layout title="Unggah Tugas Akhir">
    <x-opac.page-header 
        title="Unggah Tugas Akhir" 
        subtitle="Panduan upload skripsi, tesis, dan disertasi"
        :breadcrumbs="[['label' => 'Guide'], ['label' => 'Unggah Tugas Akhir']]"
    />

    <section class="max-w-4xl mx-auto px-4 py-6 lg:py-10">
        <!-- Intro -->
        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl p-5 lg:p-6 border border-emerald-100 mb-6">
            <p class="text-gray-700 text-sm lg:text-base leading-relaxed">
                Setiap mahasiswa <span class="text-emerald-600 font-semibold">wajib mengunggah</span> tugas akhir (skripsi/tesis/disertasi) ke repository perpustakaan sebagai syarat kelulusan dan kontribusi terhadap koleksi ilmiah institusi.
            </p>
        </div>

        <!-- Requirements -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg">Persyaratan Upload</h3>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 mb-6">
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <h4 class="font-semibold text-gray-900 text-sm mb-2 flex items-center gap-2">
                        <i class="fas fa-file-pdf text-red-500"></i> Format File
                    </h4>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• PDF (wajib)</li>
                        <li>• Maksimal 20 MB per file</li>
                        <li>• Tidak di-password</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 text-sm mb-2 flex items-center gap-2">
                        <i class="fas fa-folder text-amber-500"></i> Dokumen yang Diunggah
                    </h4>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Cover & Halaman Pengesahan</li>
                        <li>• Abstrak (Indonesia & Inggris)</li>
                        <li>• Full Text (opsional)</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Steps -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg">Langkah-langkah Upload</h3>
        <div class="space-y-3 mb-8">
            <div class="flex gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="w-8 h-8 bg-emerald-600 rounded-lg flex items-center justify-center text-white font-bold text-sm flex-shrink-0">1</div>
                <div>
                    <h4 class="font-semibold text-gray-900 text-sm">Akses Repository</h4>
                    <p class="text-xs text-gray-600">Buka <a href="https://repo.unida.gontor.ac.id" target="_blank" class="text-emerald-600 hover:underline">repo.unida.gontor.ac.id</a> dan login dengan akun mahasiswa</p>
                </div>
            </div>
            <div class="flex gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="w-8 h-8 bg-emerald-600 rounded-lg flex items-center justify-center text-white font-bold text-sm flex-shrink-0">2</div>
                <div>
                    <h4 class="font-semibold text-gray-900 text-sm">Isi Metadata</h4>
                    <p class="text-xs text-gray-600">Lengkapi informasi judul, abstrak, kata kunci, dan data diri</p>
                </div>
            </div>
            <div class="flex gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="w-8 h-8 bg-emerald-600 rounded-lg flex items-center justify-center text-white font-bold text-sm flex-shrink-0">3</div>
                <div>
                    <h4 class="font-semibold text-gray-900 text-sm">Upload File</h4>
                    <p class="text-xs text-gray-600">Unggah file PDF sesuai ketentuan format</p>
                </div>
            </div>
            <div class="flex gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="w-8 h-8 bg-emerald-600 rounded-lg flex items-center justify-center text-white font-bold text-sm flex-shrink-0">4</div>
                <div>
                    <h4 class="font-semibold text-gray-900 text-sm">Verifikasi</h4>
                    <p class="text-xs text-gray-600">Tunggu verifikasi dari pustakawan (1-3 hari kerja)</p>
                </div>
            </div>
        </div>

        <!-- CTA -->
        <div class="grid sm:grid-cols-2 gap-4">
            <a href="https://repo.unida.gontor.ac.id" target="_blank" class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-xl p-5 text-white text-center hover:shadow-lg transition">
                <i class="fas fa-upload text-2xl mb-2"></i>
                <h4 class="font-bold">Upload Sekarang</h4>
                <p class="text-emerald-100 text-xs">repo.unida.gontor.ac.id</p>
            </a>
            <a href="https://wa.me/6285183053934?text=Halo, saya butuh bantuan untuk upload tugas akhir" target="_blank" class="bg-white rounded-xl p-5 text-center border border-gray-200 hover:border-emerald-300 hover:shadow-lg transition">
                <i class="fab fa-whatsapp text-2xl text-emerald-600 mb-2"></i>
                <h4 class="font-bold text-gray-900">Butuh Bantuan?</h4>
                <p class="text-gray-500 text-xs">Chat pustakawan</p>
            </a>
        </div>
    </section>
</x-opac.layout>
