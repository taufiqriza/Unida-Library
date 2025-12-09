<x-opac.layout title="Panduan Unggah Tugas Akhir">
    <x-opac.page-header 
        title="Panduan Unggah Tugas Akhir" 
        subtitle="Langkah-langkah upload skripsi, tesis, dan disertasi ke repository perpustakaan"
        :breadcrumbs="[['label' => 'Panduan'], ['label' => 'Unggah Tugas Akhir']]"
    />

    <section class="max-w-4xl mx-auto px-4 py-6 lg:py-10">
        <!-- Intro -->
        <div class="bg-gradient-to-br from-primary-50 to-indigo-50 rounded-2xl p-5 lg:p-6 border border-primary-100 mb-8">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-graduation-cap text-primary-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 mb-1">Wajib Unggah Tugas Akhir</h3>
                    <p class="text-gray-700 text-sm leading-relaxed">
                        Setiap mahasiswa <span class="text-primary-600 font-semibold">wajib mengunggah</span> tugas akhir (skripsi/tesis/disertasi) ke repository perpustakaan sebagai syarat kelulusan dan bebas pustaka.
                    </p>
                </div>
            </div>
        </div>

        <!-- Alur Pengajuan - 5 Steps -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg flex items-center gap-2">
            <i class="fas fa-route text-primary-500"></i> Alur Pengajuan (5 Langkah)
        </h3>
        
        <div class="relative mb-8">
            <!-- Timeline Line -->
            <div class="absolute left-5 top-8 bottom-8 w-0.5 bg-gradient-to-b from-primary-500 to-primary-300 hidden sm:block"></div>
            
            <div class="space-y-4">
                <!-- Step 1 -->
                <div class="flex gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100 relative">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-700 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-lg shadow-primary-200 z-10">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-900">Langkah 1: Informasi Tugas Akhir</h4>
                        <p class="text-sm text-gray-600 mt-1">Isi informasi dasar tugas akhir Anda:</p>
                        <ul class="text-xs text-gray-500 mt-2 space-y-1">
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500"></i> Pilih jenis (Skripsi/Tesis/Disertasi)</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500"></i> Judul dalam Bahasa Indonesia & Inggris</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500"></i> Pilih Fakultas dan Program Studi</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500"></i> Abstrak dan kata kunci</li>
                        </ul>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="flex gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100 relative">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-700 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-lg shadow-primary-200 z-10">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-900">Langkah 2: Data Penulis</h4>
                        <p class="text-sm text-gray-600 mt-1">Lengkapi informasi penulis:</p>
                        <ul class="text-xs text-gray-500 mt-2 space-y-1">
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500"></i> Nama lengkap sesuai ijazah</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500"></i> NIM (Nomor Induk Mahasiswa)</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500"></i> Email aktif untuk notifikasi</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500"></i> Nomor telepon/WhatsApp</li>
                        </ul>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="flex gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100 relative">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-700 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-lg shadow-primary-200 z-10">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-900">Langkah 3: Dosen Pembimbing</h4>
                        <p class="text-sm text-gray-600 mt-1">Masukkan data dosen pembimbing:</p>
                        <ul class="text-xs text-gray-500 mt-2 space-y-1">
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500"></i> Pembimbing 1 (wajib)</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500"></i> Pembimbing 2 (opsional)</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500"></i> Penguji 1 & 2 (opsional)</li>
                        </ul>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="flex gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100 relative">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-700 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-lg shadow-primary-200 z-10">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-900">Langkah 4: Upload Dokumen</h4>
                        <p class="text-sm text-gray-600 mt-1">Unggah file-file yang diperlukan:</p>
                        <div class="grid sm:grid-cols-2 gap-3 mt-3">
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-xs font-semibold text-gray-700 flex items-center gap-2">
                                    <i class="fas fa-image text-blue-500"></i> Cover (Wajib)
                                </p>
                                <p class="text-[10px] text-gray-500 mt-1">JPG/PNG, maks 2MB</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-xs font-semibold text-gray-700 flex items-center gap-2">
                                    <i class="fas fa-file-signature text-green-500"></i> Lembar Pengesahan (Wajib)
                                </p>
                                <p class="text-[10px] text-gray-500 mt-1">PDF, maks 5MB</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-xs font-semibold text-gray-700 flex items-center gap-2">
                                    <i class="fas fa-file-alt text-purple-500"></i> Preview/Abstrak (Wajib)
                                </p>
                                <p class="text-[10px] text-gray-500 mt-1">PDF, maks 10MB</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-xs font-semibold text-gray-700 flex items-center gap-2">
                                    <i class="fas fa-book text-orange-500"></i> Full Text (Opsional)
                                </p>
                                <p class="text-[10px] text-gray-500 mt-1">PDF, maks 50MB</p>
                            </div>
                        </div>
                        <div class="mt-3 p-2 bg-amber-50 rounded-lg border border-amber-200">
                            <p class="text-[10px] text-amber-700"><i class="fas fa-info-circle mr-1"></i> Pilih akses: <strong>Publik</strong> (dapat diakses semua orang) atau <strong>Terbatas</strong> (hanya abstrak yang dapat diakses)</p>
                        </div>
                    </div>
                </div>

                <!-- Step 5 -->
                <div class="flex gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100 relative">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-lg shadow-emerald-200 z-10">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-900">Langkah 5: Review & Submit</h4>
                        <p class="text-sm text-gray-600 mt-1">Periksa kembali dan kirim pengajuan:</p>
                        <ul class="text-xs text-gray-500 mt-2 space-y-1">
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500"></i> Review semua data yang diisi</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500"></i> Centang pernyataan keaslian karya</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500"></i> Klik "Submit Pengajuan"</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Pengajuan -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg flex items-center gap-2">
            <i class="fas fa-tasks text-primary-500"></i> Status Pengajuan
        </h3>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 mb-8">
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                    <span class="w-3 h-3 bg-gray-400 rounded-full"></span>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Draft</p>
                        <p class="text-[10px] text-gray-500">Belum diajukan</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg">
                    <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Diajukan</p>
                        <p class="text-[10px] text-gray-500">Menunggu review pustakawan</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-orange-50 rounded-lg">
                    <span class="w-3 h-3 bg-orange-500 rounded-full"></span>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Perlu Revisi</p>
                        <p class="text-[10px] text-gray-500">Ada perbaikan yang diminta</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-emerald-50 rounded-lg">
                    <span class="w-3 h-3 bg-emerald-500 rounded-full"></span>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Disetujui</p>
                        <p class="text-[10px] text-gray-500">Lolos verifikasi</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-primary-50 rounded-lg">
                    <span class="w-3 h-3 bg-primary-500 rounded-full"></span>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Dipublikasikan</p>
                        <p class="text-[10px] text-gray-500">Tersedia di repository</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-red-50 rounded-lg">
                    <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Ditolak</p>
                        <p class="text-[10px] text-gray-500">Tidak memenuhi syarat</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tips -->
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 mb-8">
            <h4 class="font-bold text-amber-800 mb-3 flex items-center gap-2">
                <i class="fas fa-lightbulb text-amber-500"></i> Tips Agar Cepat Disetujui
            </h4>
            <ul class="text-sm text-amber-700 space-y-2">
                <li class="flex items-start gap-2">
                    <i class="fas fa-check-circle text-amber-500 mt-0.5"></i>
                    <span>Pastikan file PDF tidak di-password dan dapat dibuka</span>
                </li>
                <li class="flex items-start gap-2">
                    <i class="fas fa-check-circle text-amber-500 mt-0.5"></i>
                    <span>Judul harus sesuai dengan lembar pengesahan</span>
                </li>
                <li class="flex items-start gap-2">
                    <i class="fas fa-check-circle text-amber-500 mt-0.5"></i>
                    <span>Abstrak minimal 150 kata dalam Bahasa Indonesia</span>
                </li>
                <li class="flex items-start gap-2">
                    <i class="fas fa-check-circle text-amber-500 mt-0.5"></i>
                    <span>Gunakan cover dengan kualitas baik (tidak blur)</span>
                </li>
            </ul>
        </div>

        <!-- CTA -->
        <div class="grid sm:grid-cols-2 gap-4">
            @auth('member')
            <a href="{{ route('opac.member.submit-thesis') }}" class="bg-gradient-to-r from-primary-600 to-indigo-600 rounded-xl p-5 text-white text-center hover:shadow-lg hover:shadow-primary-200 transition group">
                <i class="fas fa-upload text-2xl mb-2 group-hover:scale-110 transition"></i>
                <h4 class="font-bold">Mulai Unggah</h4>
                <p class="text-primary-100 text-xs">Ajukan tugas akhir Anda sekarang</p>
            </a>
            @else
            <a href="{{ route('opac.login') }}" class="bg-gradient-to-r from-primary-600 to-indigo-600 rounded-xl p-5 text-white text-center hover:shadow-lg hover:shadow-primary-200 transition group">
                <i class="fas fa-sign-in-alt text-2xl mb-2 group-hover:scale-110 transition"></i>
                <h4 class="font-bold">Login Dulu</h4>
                <p class="text-primary-100 text-xs">Masuk untuk mulai mengunggah</p>
            </a>
            @endauth
            <a href="https://wa.me/6285183053934?text=Halo, saya butuh bantuan untuk upload tugas akhir" target="_blank" class="bg-white rounded-xl p-5 text-center border border-gray-200 hover:border-emerald-300 hover:shadow-lg transition group">
                <i class="fab fa-whatsapp text-2xl text-emerald-600 mb-2 group-hover:scale-110 transition"></i>
                <h4 class="font-bold text-gray-900">Butuh Bantuan?</h4>
                <p class="text-gray-500 text-xs">Chat pustakawan via WhatsApp</p>
            </a>
        </div>
    </section>
</x-opac.layout>
