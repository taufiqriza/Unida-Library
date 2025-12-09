<x-opac.layout title="Daftar Anggota">
    <div class="min-h-[70vh] flex items-center justify-center py-8 px-4">
        <div class="w-full max-w-md">
            <!-- Card -->
            <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-primary-600 to-primary-800 p-6 text-center">
                    <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-user-plus text-3xl text-white"></i>
                    </div>
                    <h1 class="text-xl font-bold text-white">Daftar Anggota Baru</h1>
                    <p class="text-primary-200 text-sm mt-1">Buat akun untuk akses perpustakaan</p>
                </div>

                <!-- Form -->
                <div class="p-6">
                    @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-600 text-sm p-4 rounded-xl mb-5">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-exclamation-circle mt-0.5"></i>
                            <ul class="space-y-1">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif

                    <form action="{{ route('opac.register') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input type="text" name="name" value="{{ old('name') }}" required 
                                    placeholder="Masukkan nama lengkap"
                                    class="w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input type="email" name="email" value="{{ old('email') }}" required 
                                    placeholder="contoh@email.com"
                                    class="w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">No. HP</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                    <i class="fas fa-phone"></i>
                                </span>
                                <input type="text" name="phone" value="{{ old('phone') }}" 
                                    placeholder="08xxxxxxxxxx"
                                    class="w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                            <div class="relative" x-data="{ show: false }">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input :type="show ? 'text' : 'password'" name="password" required 
                                    placeholder="Minimal 6 karakter"
                                    class="w-full pl-11 pr-12 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                                <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password <span class="text-red-500">*</span></label>
                            <div class="relative" x-data="{ show: false }">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input :type="show ? 'text' : 'password'" name="password_confirmation" required 
                                    placeholder="Ulangi password"
                                    class="w-full pl-11 pr-12 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                                <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-xl shadow-lg shadow-primary-500/30 hover:shadow-xl hover:shadow-primary-500/40 transition flex items-center justify-center gap-2 mt-2">
                            <i class="fas fa-user-plus"></i> Daftar Sekarang
                        </button>
                    </form>

                    <div class="mt-6 pt-6 border-t border-gray-100 text-center">
                        <p class="text-sm text-gray-500">
                            Sudah punya akun? 
                            <a href="{{ route('login') }}" class="text-primary-600 font-semibold hover:text-primary-700">Masuk</a>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Info -->
            <div class="mt-6 bg-primary-50 rounded-xl p-4 border border-primary-100">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-info-circle text-primary-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Keuntungan Menjadi Anggota</p>
                        <ul class="text-xs text-gray-600 mt-1 space-y-1">
                            <li><i class="fas fa-check text-emerald-500 mr-1"></i> Pinjam buku hingga 3 eksemplar</li>
                            <li><i class="fas fa-check text-emerald-500 mr-1"></i> Akses e-book dan e-thesis</li>
                            <li><i class="fas fa-check text-emerald-500 mr-1"></i> Notifikasi jatuh tempo peminjaman</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-opac.layout>
