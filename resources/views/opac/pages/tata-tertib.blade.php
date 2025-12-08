<x-opac.layout title="Tata Tertib">
    <x-opac.page-header 
        title="Tata Tertib Perpustakaan" 
        subtitle="Aturan dan kebijakan untuk kenyamanan bersama"
        :breadcrumbs="[['label' => 'Panduan'], ['label' => 'Tata Tertib']]"
    />

    <section class="max-w-4xl mx-auto px-4 py-6 lg:py-10">
        <!-- Kewajiban -->
        <div class="mb-6 lg:mb-8">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-emerald-600"></i>
                </div>
                <h2 class="font-bold text-gray-900">Kewajiban Pengunjung</h2>
            </div>
            <div class="space-y-2">
                @foreach([
                    'Menunjukkan kartu anggota perpustakaan yang masih berlaku',
                    'Menitipkan tas, jaket, dan barang bawaan di loker',
                    'Menjaga ketenangan dan tidak membuat gaduh',
                    'Menjaga kebersihan ruangan dan koleksi',
                    'Mengembalikan buku pada tempatnya',
                    'Berpakaian sopan dan rapi'
                ] as $i => $item)
                <div class="flex items-start gap-3 bg-emerald-50 rounded-lg p-3 border border-emerald-100">
                    <span class="w-6 h-6 bg-emerald-500 rounded text-white text-xs font-bold flex items-center justify-center flex-shrink-0">{{ $i + 1 }}</span>
                    <p class="text-gray-700 text-sm">{{ $item }}</p>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Larangan -->
        <div class="mb-6 lg:mb-8">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-ban text-red-600"></i>
                </div>
                <h2 class="font-bold text-gray-900">Larangan</h2>
            </div>
            <div class="space-y-2">
                @foreach([
                    'Membawa makanan dan minuman ke ruang baca',
                    'Merokok di area perpustakaan',
                    'Menggunakan handphone dengan suara keras',
                    'Merusak atau mencoret-coret koleksi',
                    'Mengambil koleksi tanpa prosedur peminjaman',
                    'Mengganggu pengunjung lain'
                ] as $item)
                <div class="flex items-start gap-3 bg-red-50 rounded-lg p-3 border border-red-100">
                    <span class="w-6 h-6 bg-red-500 rounded text-white flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-times text-xs"></i>
                    </span>
                    <p class="text-gray-700 text-sm">{{ $item }}</p>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Sanksi -->
        <div class="bg-amber-50 rounded-xl p-4 lg:p-5 border border-amber-200">
            <div class="flex items-center gap-2 mb-3">
                <i class="fas fa-exclamation-triangle text-amber-600"></i>
                <h3 class="font-bold text-gray-900">Sanksi Pelanggaran</h3>
            </div>
            <ul class="space-y-2 text-sm text-gray-700">
                <li class="flex items-start gap-2"><i class="fas fa-gavel text-amber-600 mt-0.5 text-xs"></i> Teguran lisan untuk pelanggaran ringan</li>
                <li class="flex items-start gap-2"><i class="fas fa-gavel text-amber-600 mt-0.5 text-xs"></i> Pencabutan hak keanggotaan sementara</li>
                <li class="flex items-start gap-2"><i class="fas fa-gavel text-amber-600 mt-0.5 text-xs"></i> Pencabutan hak keanggotaan permanen</li>
                <li class="flex items-start gap-2"><i class="fas fa-gavel text-amber-600 mt-0.5 text-xs"></i> Penggantian koleksi yang rusak/hilang</li>
            </ul>
        </div>
    </section>
</x-opac.layout>
