<x-opac.layout title="Jam Layanan">
    <x-opac.page-header 
        title="Jam Layanan" 
        subtitle="Waktu operasional perpustakaan"
        :breadcrumbs="[['label' => 'Panduan'], ['label' => 'Jam Layanan']]"
    />

    <section class="max-w-4xl mx-auto px-4 py-6 lg:py-10">
        <!-- Schedule -->
        <div class="grid sm:grid-cols-2 gap-4 mb-6">
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-briefcase text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">Senin - Jumat</h3>
                        <p class="text-xs text-gray-500">Hari Kerja</p>
                    </div>
                </div>
                <div class="bg-primary-50 rounded-lg p-4 text-center">
                    <p class="text-2xl lg:text-3xl font-bold text-primary-600">08.00 - 16.00</p>
                    <p class="text-xs text-gray-500 mt-1">WIB</p>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-day text-emerald-600"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">Sabtu</h3>
                        <p class="text-xs text-gray-500">Akhir Pekan</p>
                    </div>
                </div>
                <div class="bg-emerald-50 rounded-lg p-4 text-center">
                    <p class="text-2xl lg:text-3xl font-bold text-emerald-600">08.00 - 12.00</p>
                    <p class="text-xs text-gray-500 mt-1">WIB</p>
                </div>
            </div>
        </div>

        <!-- Closed -->
        <div class="bg-red-50 rounded-xl p-4 border border-red-100 mb-6">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-calendar-times text-red-600"></i>
                <h3 class="font-bold text-gray-900 text-sm">Hari Libur</h3>
            </div>
            <div class="flex flex-wrap gap-2 text-sm">
                <span class="px-2 py-1 bg-white rounded text-gray-600">Minggu</span>
                <span class="px-2 py-1 bg-white rounded text-gray-600">Libur Nasional</span>
                <span class="px-2 py-1 bg-white rounded text-gray-600">Libur Semester</span>
            </div>
        </div>

        <!-- Notes -->
        <div class="bg-amber-50 rounded-xl p-4 border border-amber-200">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-info-circle text-amber-600"></i>
                <h3 class="font-bold text-gray-900 text-sm">Catatan Penting</h3>
            </div>
            <ul class="space-y-1 text-sm text-gray-700">
                <li class="flex items-start gap-2"><i class="fas fa-check text-amber-600 mt-0.5 text-xs"></i> Peminjaman maksimal 30 menit sebelum tutup</li>
                <li class="flex items-start gap-2"><i class="fas fa-check text-amber-600 mt-0.5 text-xs"></i> Pengembalian via book drop 24 jam</li>
                <li class="flex items-start gap-2"><i class="fas fa-check text-amber-600 mt-0.5 text-xs"></i> Akses e-resources tersedia 24/7</li>
            </ul>
        </div>
    </section>
</x-opac.layout>
