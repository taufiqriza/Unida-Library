<x-opac.layout title="Masuk">
    <div class="min-h-[70vh] flex items-center justify-center py-8 px-4">
        <div class="w-full max-w-4xl">
            <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 overflow-hidden">
                <div class="flex flex-col lg:flex-row">
                    <!-- Left - Info (Hidden on Mobile) -->
                    <div class="hidden lg:flex lg:w-5/12 bg-gradient-to-br from-primary-600 to-primary-800 p-8 flex-col justify-center text-white">
                        <div class="mb-8">
                            <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mb-4">
                                <i class="fas fa-book-reader text-3xl"></i>
                            </div>
                            <h2 class="text-2xl font-bold mb-2">Perpustakaan Digital</h2>
                            <p class="text-primary-200">Akses ribuan koleksi buku, jurnal, dan karya ilmiah UNIDA Gontor</p>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-book text-lg"></i>
                                </div>
                                <div>
                                    <p class="font-medium">Peminjaman Online</p>
                                    <p class="text-primary-200 text-sm">Pinjam buku dari mana saja</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-file-pdf text-lg"></i>
                                </div>
                                <div>
                                    <p class="font-medium">E-Book & E-Thesis</p>
                                    <p class="text-primary-200 text-sm">Akses koleksi digital 24/7</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-history text-lg"></i>
                                </div>
                                <div>
                                    <p class="font-medium">Riwayat Lengkap</p>
                                    <p class="text-primary-200 text-sm">Pantau peminjaman & denda</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right - Form -->
                    <div class="lg:w-7/12">
                        <!-- Header - Mobile Only -->
                        <div class="lg:hidden bg-gradient-to-r from-primary-600 to-primary-800 px-6 py-4 flex items-center gap-4">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-user-circle text-2xl text-white"></i>
                            </div>
                            <div>
                                <h1 class="text-lg font-bold text-white">Masuk Anggota</h1>
                                <p class="text-primary-200 text-sm">Akses akun perpustakaan Anda</p>
                            </div>
                        </div>

                        <div class="p-6 lg:p-8">
                            <!-- Desktop Title -->
                            <div class="hidden lg:block mb-6">
                                <h1 class="text-xl font-bold text-gray-900">Masuk Anggota</h1>
                                <p class="text-gray-500 text-sm">Akses akun perpustakaan Anda</p>
                            </div>

                            @if($errors->any())
                            <div class="bg-red-50 border border-red-200 text-red-600 text-sm p-4 rounded-xl mb-5 flex items-start gap-3">
                                <i class="fas fa-exclamation-circle mt-0.5"></i>
                                <span>{{ $errors->first() }}</span>
                            </div>
                            @endif

                            @if(session('success'))
                            <div class="bg-emerald-50 border border-emerald-200 text-emerald-600 text-sm p-4 rounded-xl mb-5 flex items-start gap-3">
                                <i class="fas fa-check-circle mt-0.5"></i>
                                <span>{{ session('success') }}</span>
                            </div>
                            @endif

                            @if(session('error'))
                            <div class="bg-red-50 border border-red-200 text-red-600 text-sm p-4 rounded-xl mb-5 flex items-start gap-3">
                                <i class="fas fa-exclamation-circle mt-0.5"></i>
                                <span>{{ session('error') }}</span>
                            </div>
                            @endif

                            @if(\App\Models\Setting::get('google_enabled'))
                            <a href="{{ route('auth.google') }}" class="w-full py-3 bg-white border-2 border-gray-200 rounded-xl flex items-center justify-center gap-3 hover:bg-gray-50 hover:border-gray-300 transition mb-2">
                                <svg class="w-5 h-5" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                                <span class="text-gray-700 font-medium">Masuk / Daftar dengan Email UNIDA</span>
                            </a>
                            <p class="text-[11px] text-gray-400 text-center mb-4">Email @unida.gontor.ac.id untuk login atau registrasi otomatis</p>

                            <div class="relative my-5">
                                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
                                <div class="relative flex justify-center text-xs"><span class="px-3 bg-white text-gray-400">atau masuk manual</span></div>
                            </div>
                            @endif

                            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">No. Anggota / Email</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                            <i class="fas fa-user"></i>
                                        </span>
                                        <input type="text" name="identifier" value="{{ old('identifier') }}" required 
                                            placeholder="Masukkan no. anggota atau email"
                                            class="w-full pl-11 pr-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                                    <div class="relative" x-data="{ show: false }">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                        <input :type="show ? 'text' : 'password'" name="password" required 
                                            placeholder="Masukkan password"
                                            class="w-full pl-11 pr-12 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                                        <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                            <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="submit" class="w-full py-3 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-xl shadow-lg shadow-primary-500/30 hover:shadow-xl hover:shadow-primary-500/40 transition flex items-center justify-center gap-2">
                                    <i class="fas fa-sign-in-alt"></i> Masuk
                                </button>
                            </form>

                            <div class="mt-5 pt-5 border-t border-gray-100 text-center">
                                <p class="text-sm text-gray-500">
                                    Belum punya akun? 
                                    <a href="{{ route('opac.register') }}" class="text-primary-600 font-semibold hover:text-primary-700">Daftar Manual</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-opac.layout>
