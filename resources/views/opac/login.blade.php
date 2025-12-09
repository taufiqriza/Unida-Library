<x-opac.layout title="Masuk">
    <div class="min-h-[70vh] flex items-center justify-center py-8 px-4">
        <div class="w-full max-w-md">
            <!-- Card -->
            <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-primary-600 to-primary-800 p-6 text-center">
                    <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-user-circle text-3xl text-white"></i>
                    </div>
                    <h1 class="text-xl font-bold text-white">Masuk Anggota</h1>
                    <p class="text-primary-200 text-sm mt-1">Akses akun perpustakaan Anda</p>
                </div>

                <!-- Form -->
                <div class="p-6">
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

                    <form action="{{ route('login') }}" method="POST" class="space-y-5">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">No. Anggota / Email</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input type="text" name="identifier" value="{{ old('identifier') }}" required 
                                    placeholder="Masukkan no. anggota atau email"
                                    class="w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                            <div class="relative" x-data="{ show: false }">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input :type="show ? 'text' : 'password'" name="password" required 
                                    placeholder="Masukkan password"
                                    class="w-full pl-11 pr-12 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                                <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-xl shadow-lg shadow-primary-500/30 hover:shadow-xl hover:shadow-primary-500/40 transition flex items-center justify-center gap-2">
                            <i class="fas fa-sign-in-alt"></i> Masuk
                        </button>
                    </form>

                    <div class="mt-6 pt-6 border-t border-gray-100 text-center">
                        <p class="text-sm text-gray-500">
                            Belum punya akun? 
                            <a href="{{ route('opac.register') }}" class="text-primary-600 font-semibold hover:text-primary-700">Daftar Sekarang</a>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Info -->
            <div class="mt-6 text-center">
                <p class="text-xs text-gray-400">Dengan masuk, Anda dapat mengakses:</p>
                <div class="flex items-center justify-center gap-4 mt-2">
                    <span class="text-xs text-gray-500"><i class="fas fa-book text-primary-400 mr-1"></i> Peminjaman</span>
                    <span class="text-xs text-gray-500"><i class="fas fa-history text-emerald-400 mr-1"></i> Riwayat</span>
                    <span class="text-xs text-gray-500"><i class="fas fa-file-pdf text-orange-400 mr-1"></i> E-Book</span>
                </div>
            </div>
        </div>
    </div>
</x-opac.layout>
