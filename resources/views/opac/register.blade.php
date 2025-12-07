<x-opac.layout title="Daftar">
    <div class="min-h-[60vh] flex items-center justify-center py-12 px-4">
        <div class="bg-white rounded-xl shadow-lg shadow-gray-200/50 p-8 w-full max-w-md">
            <div class="text-center mb-6">
                <div class="w-16 h-16 gradient-blue rounded-xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-plus text-2xl text-white"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Daftar Anggota</h1>
                <p class="text-sm text-gray-500 mt-1">Buat akun anggota perpustakaan baru</p>
            </div>

            <form action="{{ route('opac.register') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                    <input type="text" name="phone" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-blue-500">
                </div>
                <button type="submit" class="w-full py-3 gradient-blue text-white font-medium rounded-lg shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 transition">
                    Daftar
                </button>
            </form>

            <p class="text-center text-sm text-gray-500 mt-6">
                Sudah punya akun? <a href="{{ route('opac.login') }}" class="text-blue-600 hover:text-blue-700">Masuk</a>
            </p>
        </div>
    </div>
</x-opac.layout>
