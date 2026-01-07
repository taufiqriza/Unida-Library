<div>
    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-amber-600 via-orange-600 to-red-700 text-white py-8 lg:py-12 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -mr-48 -mt-48"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/5 rounded-full -ml-32 -mb-32"></div>
        
        <div class="max-w-7xl mx-auto px-4 relative z-10">
            <div class="flex items-center gap-2 text-orange-200 text-sm mb-4">
                <a href="{{ route('opac.khastara.browse') }}" class="hover:text-white transition">Naskah Nusantara</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <span>Detail Naskah</span>
            </div>
            <h1 class="text-2xl lg:text-4xl font-bold mb-2">{{ $manuscript['title'] }}</h1>
            <div class="flex items-center gap-4 text-orange-200">
                <span class="flex items-center gap-1">
                    <i class="fas fa-tag"></i>
                    {{ $manuscript['worksheet_name'] }}
                </span>
                <span class="flex items-center gap-1">
                    <i class="fas fa-language"></i>
                    {{ $manuscript['language_name'] }}
                </span>
                <span class="flex items-center gap-1">
                    <i class="fas fa-calendar"></i>
                    {{ $manuscript['year'] ?? 'Tidak diketahui' }}
                </span>
            </div>
        </div>
    </section>

    <!-- Detail Content -->
    <section class="max-w-7xl mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cover & Actions -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-8">
                    <!-- Cover Image -->
                    <div class="aspect-[3/4] bg-gradient-to-br from-amber-100 to-orange-100 rounded-xl overflow-hidden mb-6">
                        <img 
                            src="{{ $manuscript['cover_utama'] }}" 
                            alt="{{ $manuscript['title'] }}"
                            class="w-full h-full object-cover"
                            onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjI2NyIgdmlld0JveD0iMCAwIDIwMCAyNjciIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIyMDAiIGhlaWdodD0iMjY3IiBmaWxsPSIjRkVGM0UyIi8+CjxwYXRoIGQ9Ik0xMDAgMTMzLjVDMTE4LjIyNSAxMzMuNSAxMzMgMTE4LjcyNSAxMzMgMTAwLjVDMTMzIDgyLjI3NDYgMTE4LjIyNSA2Ny41IDEwMCA2Ny41QzgxLjc3NDYgNjcuNSA2NyA4Mi4yNzQ2IDY3IDEwMC41QzY3IDExOC43MjUgODEuNzc0NiAxMzMuNSAxMDAgMTMzLjVaIiBmaWxsPSIjRjU5RTBCIi8+CjxwYXRoIGQ9Ik0xNjcgMjAwLjVDMTY3IDE2My4zNTUgMTM3LjE0NSAxMzMuNSAxMDAgMTMzLjVDNjIuODU1IDEzMy41IDMzIDE2My4zNTUgMzMgMjAwLjVIMTY3WiIgZmlsbD0iI0Y1OUUwQiIvPgo8L3N2Zz4K'"
                        >
                    </div>
                    
                    <!-- Actions -->
                    <div class="space-y-3">
                        <a href="{{ $manuscript['external_url'] }}" target="_blank" class="w-full bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-semibold py-3 px-4 rounded-xl transition flex items-center justify-center gap-2">
                            <i class="fas fa-external-link-alt"></i>
                            Lihat di Khastara Perpusnas
                        </a>
                        <button onclick="window.history.back()" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-4 rounded-xl transition flex items-center justify-center gap-2">
                            <i class="fas fa-arrow-left"></i>
                            Kembali
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Detail Information -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-lg p-6 lg:p-8">
                    <!-- Description -->
                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Deskripsi</h2>
                        <p class="text-gray-600 leading-relaxed">{{ $manuscript['description'] }}</p>
                    </div>
                    
                    <!-- Metadata -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Naskah</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between py-2 border-b border-gray-100">
                                    <span class="text-gray-500">Pengarang</span>
                                    <span class="font-medium text-gray-900">{{ $manuscript['author'] ?? 'Tidak diketahui' }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-gray-100">
                                    <span class="text-gray-500">Tahun</span>
                                    <span class="font-medium text-gray-900">{{ $manuscript['year'] ?? 'Tidak diketahui' }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-gray-100">
                                    <span class="text-gray-500">Bahasa</span>
                                    <span class="font-medium text-gray-900">{{ $manuscript['language_name'] }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-gray-100">
                                    <span class="text-gray-500">Jenis</span>
                                    <span class="font-medium text-gray-900">{{ $manuscript['worksheet_name'] }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-gray-100">
                                    <span class="text-gray-500">Aksara</span>
                                    <span class="font-medium text-gray-900">{{ $manuscript['script'] ?? 'Tidak diketahui' }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Detail Fisik</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between py-2 border-b border-gray-100">
                                    <span class="text-gray-500">Jumlah Halaman</span>
                                    <span class="font-medium text-gray-900">{{ $manuscript['pages'] ?? 'Tidak diketahui' }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-gray-100">
                                    <span class="text-gray-500">Ukuran</span>
                                    <span class="font-medium text-gray-900">{{ $manuscript['size'] ?? 'Tidak diketahui' }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-gray-100">
                                    <span class="text-gray-500">Bahan</span>
                                    <span class="font-medium text-gray-900">{{ $manuscript['material'] ?? 'Tidak diketahui' }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-gray-100">
                                    <span class="text-gray-500">Kondisi</span>
                                    <span class="font-medium text-gray-900">{{ $manuscript['condition'] ?? 'Tidak diketahui' }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-gray-100">
                                    <span class="text-gray-500">Lokasi</span>
                                    <span class="font-medium text-gray-900">{{ $manuscript['location'] ?? 'Perpustakaan Nasional RI' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Manuscripts -->
    <section class="bg-gradient-to-r from-amber-50 to-orange-50 py-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Naskah Terkait</h2>
                <p class="text-gray-600">Jelajahi koleksi naskah lainnya dari Khastara Perpusnas</p>
            </div>
            
            <div class="text-center">
                <a href="{{ route('opac.khastara.browse') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-medium transition">
                    <i class="fas fa-scroll"></i>
                    Jelajahi Semua Naskah
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>
</div>
