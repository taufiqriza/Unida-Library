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
        {{-- SSO Login Card --}}
        @auth('member')
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl border border-green-200 p-6">
            <h2 class="font-bold text-green-800 mb-3 flex items-center gap-2">
                <i class="fas fa-check-circle"></i>
                Login Otomatis
            </h2>
            <p class="text-sm text-green-700 mb-4">
                Anda sudah login sebagai <strong>{{ auth('member')->user()->name }}</strong>. 
                Klik tombol di bawah untuk langsung masuk ke Repository tanpa perlu login ulang.
            </p>
            <a href="{{ route('opac.repository.sso') }}" target="_blank" id="sso-btn"
                    class="w-full flex items-center justify-center gap-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl p-4 font-semibold hover:shadow-lg transition">
                <i class="fas fa-sign-in-alt text-xl"></i>
                <span>Masuk ke Repository</span>
                <i class="fas fa-external-link-alt text-sm opacity-70"></i>
            </a>
            <p class="text-xs text-green-600 mt-2 text-center">
                <i class="fas fa-shield-alt"></i> Login aman menggunakan akun perpustakaan Anda
            </p>
        </div>
        @else
        <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl border border-amber-200 p-6">
            <h2 class="font-bold text-amber-800 mb-3 flex items-center gap-2">
                <i class="fas fa-info-circle"></i>
                Login Diperlukan
            </h2>
            <p class="text-sm text-amber-700 mb-4">
                Silakan login ke perpustakaan terpadu terlebih dahulu untuk akses otomatis ke Repository.
            </p>
            <a href="{{ route('login') }}" 
               class="w-full flex items-center justify-center gap-3 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-xl p-4 font-semibold hover:shadow-lg transition">
                <i class="fas fa-sign-in-alt text-xl"></i>
                <span>Login Perpustakaan</span>
            </a>
        </div>
        @endauth

        {{-- Info Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-info-circle text-blue-500"></i>
                Tentang Repository
            </h2>
            <p class="text-gray-600 text-sm leading-relaxed mb-4">
                Repository EPrints UNIDA Gontor menyimpan koleksi karya ilmiah sivitas akademika.
            </p>
            <div class="grid grid-cols-4 gap-3">
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

        {{-- Browse Without Login --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-search text-green-500"></i>
                Jelajahi Tanpa Login
            </h2>
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

        {{-- Manual Login --}}
        <div class="bg-gray-50 rounded-2xl border border-gray-200 p-4">
            <details class="group">
                <summary class="cursor-pointer text-sm text-gray-600 flex items-center gap-2">
                    <i class="fas fa-chevron-right group-open:rotate-90 transition-transform"></i>
                    Login Manual (jika SSO tidak berfungsi)
                </summary>
                <div class="mt-3 pt-3 border-t border-gray-200 space-y-2">
                    <a href="https://repo.unida.gontor.ac.id/cgi/users/home" target="_blank" 
                       class="block text-center bg-white border border-gray-300 text-gray-700 rounded-lg p-2 text-sm hover:bg-gray-50">
                        <i class="fas fa-sign-in-alt mr-2"></i> Login dengan HTTP Auth
                    </a>
                    <a href="https://repo.unida.gontor.ac.id/cgi/register" target="_blank"
                       class="block text-center bg-white border border-gray-300 text-gray-700 rounded-lg p-2 text-sm hover:bg-gray-50">
                        <i class="fas fa-user-plus mr-2"></i> Daftar Akun Baru
                    </a>
                </div>
            </details>
        </div>
    </div>

    @auth('member')
    <script>
    function ssoLogin() {
        const btn = document.getElementById('sso-btn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
        
        fetch('/api/eprints/login-token', {
            credentials: 'same-origin'
        })
        .then(r => r.json())
        .then(data => {
            if (data.redirect_url) {
                window.open(data.redirect_url, '_blank');
            } else {
                alert('Gagal mendapatkan token login');
            }
        })
        .catch(e => {
            alert('Terjadi kesalahan: ' + e.message);
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-sign-in-alt text-xl"></i> <span>Masuk ke Repository</span> <i class="fas fa-external-link-alt text-sm opacity-70"></i>';
        });
    }
    </script>
    @endauth
</div>
