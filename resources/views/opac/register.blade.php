<x-opac.layout title="Daftar Akun">
    <div class="min-h-[70vh] flex items-center justify-center py-8 px-4" x-data="{ registerType: 'member' }">
        <div class="w-full max-w-4xl">
            <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 overflow-hidden">
                <div class="flex flex-col lg:flex-row">
                    <!-- Left - Info -->
                    <div class="hidden lg:flex lg:w-5/12 bg-gradient-to-br from-primary-600 to-primary-800 p-8 flex-col justify-center text-white">
                        <div class="mb-8">
                            <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mb-4">
                                <i class="fas fa-user-plus text-3xl"></i>
                            </div>
                            <h2 class="text-2xl font-bold mb-2">Bergabung Sekarang</h2>
                            <p class="text-primary-200">Daftar untuk menikmati layanan perpustakaan digital UNIDA Gontor</p>
                        </div>
                        
                        {{-- Member Benefits --}}
                        <div x-show="registerType === 'member'" class="space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center"><i class="fas fa-check text-lg"></i></div>
                                <div><p class="font-medium">Pinjam Buku</p><p class="text-primary-200 text-sm">Hingga 3 eksemplar</p></div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center"><i class="fas fa-check text-lg"></i></div>
                                <div><p class="font-medium">Akses Digital</p><p class="text-primary-200 text-sm">E-Book & E-Thesis 24/7</p></div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center"><i class="fas fa-upload text-lg"></i></div>
                                <div><p class="font-medium">Unggah Tugas Akhir</p><p class="text-primary-200 text-sm">Submit skripsi/tesis</p></div>
                            </div>
                        </div>
                        
                        {{-- Staff Benefits --}}
                        <div x-show="registerType === 'staff'" class="space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center"><i class="fas fa-cog text-lg"></i></div>
                                <div><p class="font-medium">Kelola Perpustakaan</p><p class="text-primary-200 text-sm">Sirkulasi & katalog</p></div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center"><i class="fas fa-users text-lg"></i></div>
                                <div><p class="font-medium">Kelola Anggota</p><p class="text-primary-200 text-sm">Data member</p></div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center"><i class="fas fa-chart-bar text-lg"></i></div>
                                <div><p class="font-medium">Laporan</p><p class="text-primary-200 text-sm">Statistik perpustakaan</p></div>
                            </div>
                        </div>
                    </div>

                    <!-- Right - Form -->
                    <div class="lg:w-7/12">
                        <div class="p-6 lg:p-8">
                            {{-- Type Switcher --}}
                            <div class="flex bg-gray-100 rounded-xl p-1 mb-6">
                                <button type="button" @click="registerType = 'member'" 
                                        :class="registerType === 'member' ? 'bg-white shadow text-primary-600' : 'text-gray-500 hover:text-gray-700'"
                                        class="flex-1 py-2.5 rounded-lg font-semibold text-sm transition flex items-center justify-center gap-2">
                                    <i class="fas fa-user"></i> Member
                                </button>
                                <button type="button" @click="registerType = 'staff'" 
                                        :class="registerType === 'staff' ? 'bg-white shadow text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                                        class="flex-1 py-2.5 rounded-lg font-semibold text-sm transition flex items-center justify-center gap-2">
                                    <i class="fas fa-user-tie"></i> Staff
                                </button>
                            </div>

                            <div class="mb-6">
                                <h1 class="text-xl font-bold text-gray-900" x-text="registerType === 'member' ? 'Daftar Member' : 'Daftar Staff'"></h1>
                                <p class="text-gray-500 text-sm" x-text="registerType === 'member' ? 'Buat akun untuk akses perpustakaan' : 'Daftar sebagai staff perpustakaan'"></p>
                            </div>

                            {{-- Staff Notice --}}
                            <div x-show="registerType === 'staff'" class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-5">
                                <div class="flex gap-3">
                                    <i class="fas fa-info-circle text-amber-500 mt-0.5"></i>
                                    <div class="text-sm text-amber-800">
                                        <p class="font-medium">Perlu Persetujuan Admin</p>
                                        <p class="text-amber-600">Akun staff akan aktif setelah disetujui oleh admin cabang perpustakaan.</p>
                                    </div>
                                </div>
                            </div>

                            @if($errors->any())
                            <div class="bg-red-50 border border-red-200 text-red-600 text-sm p-4 rounded-xl mb-5">
                                <ul class="space-y-1">
                                    @foreach($errors->all() as $error)
                                    <li><i class="fas fa-exclamation-circle mr-1"></i>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            {{-- Member Form --}}
                            <form x-show="registerType === 'member'" action="{{ route('opac.register') }}" method="POST" class="space-y-3" x-data="memberForm()">
                                @csrf
                                <input type="hidden" name="type" value="member">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Masukkan nama lengkap"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 transition">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input type="email" name="email" x-model="email" @input="checkEmail()" value="{{ old('email') }}" required placeholder="contoh@email.com"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 transition">
                                    <p x-show="emailType === 'internal'" class="text-xs text-emerald-600 mt-1"><i class="fas fa-check-circle mr-1"></i>Email UNIDA terdeteksi - verifikasi otomatis</p>
                                    <p x-show="emailType === 'external'" class="text-xs text-blue-600 mt-1"><i class="fas fa-university mr-1"></i>Email kampus lain terdeteksi</p>
                                    <p x-show="emailType === 'public'" class="text-xs text-gray-500 mt-1"><i class="fas fa-envelope mr-1"></i>Perlu verifikasi email</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 transition">
                                </div>
                                
                                {{-- Institution fields for public email --}}
                                <div x-show="emailType === 'public'" x-collapse class="bg-gray-50 rounded-xl p-3 space-y-3">
                                    <p class="text-xs text-gray-600 font-medium"><i class="fas fa-building mr-1"></i> Asal Institusi (opsional)</p>
                                    <div class="grid grid-cols-2 gap-3">
                                        <input type="text" name="institution" value="{{ old('institution') }}" placeholder="Nama institusi"
                                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 transition">
                                        <input type="text" name="institution_city" value="{{ old('institution_city') }}" placeholder="Kota"
                                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 transition">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                        <input type="password" name="password" required placeholder="Min 8 karakter"
                                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 transition">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi</label>
                                        <input type="password" name="password_confirmation" required placeholder="Ulangi"
                                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 transition">
                                    </div>
                                </div>
                                <button type="submit" class="w-full py-3 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-xl shadow-lg shadow-primary-500/30 hover:shadow-xl transition flex items-center justify-center gap-2 mt-2">
                                    <i class="fas fa-user-plus"></i> Daftar Member
                                </button>
                            </form>

                            {{-- Staff Form --}}
                            <form x-show="registerType === 'staff'" action="{{ route('opac.register.staff') }}" method="POST" class="space-y-3">
                                @csrf
                                <input type="hidden" name="type" value="staff">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Masukkan nama lengkap"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 transition">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="contoh@email.com"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 transition">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Cabang Perpustakaan</label>
                                    <select name="branch_id" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 transition">
                                        <option value="">Pilih Cabang</option>
                                        @foreach(\App\Models\Branch::where('is_active', true)->orderByDesc('is_main')->orderBy('name')->get() as $branch)
                                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                        <input type="password" name="password" required placeholder="Min 8 karakter"
                                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 transition">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi</label>
                                        <input type="password" name="password_confirmation" required placeholder="Ulangi"
                                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 transition">
                                    </div>
                                </div>
                                <button type="submit" class="w-full py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-xl transition flex items-center justify-center gap-2 mt-2">
                                    <i class="fas fa-user-tie"></i> Daftar Staff
                                </button>
                            </form>

                            <div class="mt-5 pt-5 border-t border-gray-100 text-center">
                                <p class="text-sm text-gray-500">
                                    Sudah punya akun? 
                                    <a href="{{ route('login') }}" class="text-primary-600 font-semibold hover:text-primary-700">Masuk</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function memberForm() {
        const trustedDomains = @json(array_map('trim', file(base_path('docs/email.md'))));
        return {
            email: '{{ old("email") }}',
            emailType: '',
            checkEmail() {
                if (!this.email || !this.email.includes('@')) {
                    this.emailType = '';
                    return;
                }
                const domain = '@' + this.email.split('@')[1].toLowerCase();
                if (trustedDomains.includes(domain)) {
                    this.emailType = 'internal';
                } else if (domain.endsWith('.ac.id')) {
                    this.emailType = 'external';
                } else {
                    this.emailType = 'public';
                }
            },
            init() { this.checkEmail(); }
        }
    }
    </script>
</x-opac.layout>
