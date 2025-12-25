<div class="min-h-screen bg-gray-50">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-orange-500 to-amber-600 text-white">
        <div class="max-w-4xl mx-auto px-4 py-8">
            <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 text-white/80 hover:text-white mb-4 text-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-archive text-3xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold">Repository EPrints</h1>
                    <p class="text-orange-100">Akses koleksi karya ilmiah UNIDA Gontor</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 py-6 space-y-6">
        {{-- Info Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-info-circle text-blue-500"></i>
                Tentang Repository
            </h2>
            <p class="text-gray-600 text-sm leading-relaxed mb-4">
                Repository EPrints UNIDA Gontor menyimpan koleksi karya ilmiah sivitas akademika termasuk skripsi, tesis, disertasi, jurnal, dan publikasi lainnya.
            </p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="bg-blue-50 rounded-xl p-3 text-center">
                    <i class="fas fa-graduation-cap text-blue-500 text-xl mb-1"></i>
                    <p class="text-xs text-gray-600">Skripsi</p>
                </div>
                <div class="bg-purple-50 rounded-xl p-3 text-center">
                    <i class="fas fa-book text-purple-500 text-xl mb-1"></i>
                    <p class="text-xs text-gray-600">Tesis</p>
                </div>
                <div class="bg-green-50 rounded-xl p-3 text-center">
                    <i class="fas fa-file-alt text-green-500 text-xl mb-1"></i>
                    <p class="text-xs text-gray-600">Jurnal</p>
                </div>
                <div class="bg-orange-50 rounded-xl p-3 text-center">
                    <i class="fas fa-newspaper text-orange-500 text-xl mb-1"></i>
                    <p class="text-xs text-gray-600">Artikel</p>
                </div>
            </div>
        </div>

        {{-- Login Info --}}
        <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl border border-amber-200 p-6">
            <h2 class="font-bold text-amber-800 mb-3 flex items-center gap-2">
                <i class="fas fa-key"></i>
                Cara Login ke Repository
            </h2>
            
            @auth('member')
            <div class="bg-white rounded-xl p-4 mb-4 border border-amber-200">
                <p class="text-sm text-gray-700 mb-2">Gunakan kredensial berikut untuk login:</p>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-3">
                        <span class="text-gray-500 text-sm w-20">Email:</span>
                        <code class="bg-amber-100 text-amber-800 px-2 py-1 rounded text-sm font-mono">{{ auth('member')->user()->email }}</code>
                    </div>
                    <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-3">
                        <span class="text-gray-500 text-sm w-20">Password:</span>
                        <span class="text-gray-600 text-sm">Password akun perpustakaan Anda</span>
                    </div>
                </div>
            </div>
            @endauth

            <div class="space-y-3 text-sm text-amber-900">
                <div class="flex items-start gap-3">
                    <span class="w-6 h-6 bg-amber-500 text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">1</span>
                    <p>Klik tombol "Buka Repository" di bawah</p>
                </div>
                <div class="flex items-start gap-3">
                    <span class="w-6 h-6 bg-amber-500 text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">2</span>
                    <p>Browser akan menampilkan popup login (HTTP Basic Auth)</p>
                </div>
                <div class="flex items-start gap-3">
                    <span class="w-6 h-6 bg-amber-500 text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">3</span>
                    <p>Masukkan email dan password akun perpustakaan Anda</p>
                </div>
                <div class="flex items-start gap-3">
                    <span class="w-6 h-6 bg-amber-500 text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">4</span>
                    <p>Jika belum punya akun EPrints, daftar dulu di halaman Register</p>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="https://repo.unida.gontor.ac.id/cgi/users/home" target="_blank" 
               class="flex items-center justify-center gap-3 bg-gradient-to-r from-orange-500 to-amber-600 text-white rounded-xl p-4 font-semibold hover:shadow-lg transition">
                <i class="fas fa-sign-in-alt text-xl"></i>
                <span>Buka Repository (Login)</span>
                <i class="fas fa-external-link-alt text-sm opacity-70"></i>
            </a>
            
            <a href="https://repo.unida.gontor.ac.id/cgi/register" target="_blank"
               class="flex items-center justify-center gap-3 bg-white text-orange-600 border-2 border-orange-500 rounded-xl p-4 font-semibold hover:bg-orange-50 transition">
                <i class="fas fa-user-plus text-xl"></i>
                <span>Daftar Akun Baru</span>
                <i class="fas fa-external-link-alt text-sm opacity-70"></i>
            </a>
        </div>

        {{-- Browse Without Login --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-search text-green-500"></i>
                Jelajahi Tanpa Login
            </h2>
            <p class="text-gray-600 text-sm mb-4">
                Anda bisa menjelajahi dan mencari koleksi repository tanpa perlu login:
            </p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <a href="https://repo.unida.gontor.ac.id" target="_blank" class="bg-gray-50 hover:bg-gray-100 rounded-xl p-3 text-center transition">
                    <i class="fas fa-home text-gray-500 text-lg mb-1"></i>
                    <p class="text-xs text-gray-600">Beranda</p>
                </a>
                <a href="https://repo.unida.gontor.ac.id/view/year/" target="_blank" class="bg-gray-50 hover:bg-gray-100 rounded-xl p-3 text-center transition">
                    <i class="fas fa-calendar text-gray-500 text-lg mb-1"></i>
                    <p class="text-xs text-gray-600">Per Tahun</p>
                </a>
                <a href="https://repo.unida.gontor.ac.id/view/subjects/" target="_blank" class="bg-gray-50 hover:bg-gray-100 rounded-xl p-3 text-center transition">
                    <i class="fas fa-tags text-gray-500 text-lg mb-1"></i>
                    <p class="text-xs text-gray-600">Per Subjek</p>
                </a>
                <a href="https://repo.unida.gontor.ac.id/view/divisions/" target="_blank" class="bg-gray-50 hover:bg-gray-100 rounded-xl p-3 text-center transition">
                    <i class="fas fa-building text-gray-500 text-lg mb-1"></i>
                    <p class="text-xs text-gray-600">Per Fakultas</p>
                </a>
            </div>
        </div>
    </div>
</div>
