<div class="min-h-[80vh] flex items-center justify-center py-8 px-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 overflow-hidden">
            
            {{-- Header --}}
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-5 text-white">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-id-badge text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold">Daftar Staff Perpustakaan</h1>
                        <p class="text-indigo-200 text-sm">UNIDA Library</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                {{-- Info Box --}}
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-info-circle text-amber-500 mt-0.5"></i>
                        <div class="text-sm text-amber-800">
                            <p class="font-medium">Perlu Persetujuan Admin</p>
                            <p class="text-amber-700">Akun staff akan aktif setelah disetujui oleh admin cabang perpustakaan.</p>
                        </div>
                    </div>
                </div>

                <form wire:submit="register" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="name" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Nama lengkap">
                        @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" wire:model="email" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="email@example.com">
                        @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cabang Perpustakaan <span class="text-red-500">*</span></label>
                        <select wire:model="branch_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Pilih Cabang</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        @error('branch_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                            <input type="password" wire:model="password" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Min. 8 karakter">
                            @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi <span class="text-red-500">*</span></label>
                            <input type="password" wire:model="password_confirmation" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ulangi password">
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition mt-2">
                        <span wire:loading.remove wire:target="register">Daftar Staff</span>
                        <span wire:loading wire:target="register"><i class="fas fa-spinner fa-spin mr-2"></i> Memproses...</span>
                    </button>
                </form>

                <div class="text-center pt-4 border-t mt-6">
                    <p class="text-sm text-gray-500">
                        <a href="{{ route('opac.register') }}" class="text-indigo-600 hover:underline"><i class="fas fa-arrow-left mr-1"></i> Kembali ke pilihan pendaftaran</a>
                    </p>
                    <p class="text-sm text-gray-500 mt-2">Sudah punya akun? <a href="{{ route('login') }}" class="text-indigo-600 font-semibold hover:underline">Masuk di sini</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
