<div class="min-h-[80vh] flex items-center justify-center py-8 px-4">
    <div class="w-full max-w-2xl">
        <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 overflow-hidden">
            
            {{-- Header --}}
            <div class="bg-gradient-to-r from-primary-600 to-primary-700 px-6 py-5 text-white">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user-plus text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold">Daftar Anggota Perpustakaan</h1>
                        <p class="text-primary-200 text-sm">UNIDA Library - Universitas Darussalam Gontor</p>
                    </div>
                </div>
                
                {{-- Progress Steps --}}
                <div class="flex items-center gap-2 mt-5">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold {{ $step >= 1 ? 'bg-white text-primary-600' : 'bg-white/30' }}">1</div>
                        <span class="text-sm {{ $step >= 1 ? 'text-white' : 'text-white/60' }}">Pilih Status</span>
                    </div>
                    <div class="flex-1 h-0.5 {{ $step >= 2 ? 'bg-white' : 'bg-white/30' }}"></div>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold {{ $step >= 2 ? 'bg-white text-primary-600' : 'bg-white/30' }}">2</div>
                        <span class="text-sm {{ $step >= 2 ? 'text-white' : 'text-white/60' }}">Verifikasi</span>
                    </div>
                    <div class="flex-1 h-0.5 {{ $step >= 3 ? 'bg-white' : 'bg-white/30' }}"></div>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold {{ $step >= 3 ? 'bg-white text-primary-600' : 'bg-white/30' }}">3</div>
                        <span class="text-sm {{ $step >= 3 ? 'text-white' : 'text-white/60' }}">Buat Akun</span>
                    </div>
                </div>
            </div>

            <div class="p-6">
                {{-- Step 1: Choose User Type --}}
                @if($step === 1)
                <div class="space-y-4">
                    <p class="text-gray-600 text-center mb-6">Pilih status Anda untuk melanjutkan pendaftaran</p>
                    
                    {{-- Auto-detected notice --}}
                    @if($detectedType === 'mahasiswa' && $detectedMember)
                    <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-check text-green-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-green-800">Data SIAKAD Ditemukan!</p>
                                <p class="text-green-700 text-sm">{{ $detectedMember->name }} ({{ $detectedMember->member_id }})</p>
                                <p class="text-green-600 text-xs mt-1">Klik "Mahasiswa UNIDA" untuk melanjutkan</p>
                            </div>
                        </div>
                    </div>
                    @elseif($detectedType === 'dosen_tendik' && $detectedEmployee)
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-check text-blue-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-blue-800">Data SDM Ditemukan!</p>
                                <p class="text-blue-700 text-sm">{{ $detectedEmployee->full_name ?? $detectedEmployee->name }}</p>
                                <p class="text-blue-600 text-xs mt-1">{{ ucfirst($detectedEmployee->type) }} - {{ $detectedEmployee->faculty ?? $detectedEmployee->satker }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- User Type Cards --}}
                    <div class="grid grid-cols-2 gap-3">
                        {{-- Mahasiswa --}}
                        <button wire:click="selectUserType('mahasiswa')" 
                                class="group p-4 rounded-xl border-2 transition-all text-left hover:border-primary-500 hover:bg-primary-50 {{ $detectedType === 'mahasiswa' ? 'border-primary-500 bg-primary-50' : 'border-gray-200' }}">
                            <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center mb-3 group-hover:bg-primary-200 transition">
                                <i class="fas fa-graduation-cap text-xl text-primary-600"></i>
                            </div>
                            <h3 class="font-semibold text-gray-900">Mahasiswa UNIDA</h3>
                            <p class="text-xs text-gray-500 mt-1">Mahasiswa aktif Universitas Darussalam Gontor</p>
                            @if($detectedType === 'mahasiswa')
                            <span class="inline-flex items-center gap-1 text-xs text-primary-600 mt-2"><i class="fas fa-check-circle"></i> Terdeteksi</span>
                            @endif
                        </button>

                        {{-- Dosen --}}
                        <button wire:click="selectUserType('dosen')" 
                                class="group p-4 rounded-xl border-2 transition-all text-left hover:border-blue-500 hover:bg-blue-50 {{ $detectedType === 'dosen_tendik' && $detectedEmployee?->type === 'dosen' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-3 group-hover:bg-blue-200 transition">
                                <i class="fas fa-chalkboard-teacher text-xl text-blue-600"></i>
                            </div>
                            <h3 class="font-semibold text-gray-900">Dosen UNIDA</h3>
                            <p class="text-xs text-gray-500 mt-1">Dosen tetap atau tidak tetap UNIDA</p>
                            @if($detectedType === 'dosen_tendik' && $detectedEmployee?->type === 'dosen')
                            <span class="inline-flex items-center gap-1 text-xs text-blue-600 mt-2"><i class="fas fa-check-circle"></i> Terdeteksi</span>
                            @endif
                        </button>

                        {{-- Tendik --}}
                        <button wire:click="selectUserType('tendik')" 
                                class="group p-4 rounded-xl border-2 transition-all text-left hover:border-emerald-500 hover:bg-emerald-50 {{ $detectedType === 'dosen_tendik' && $detectedEmployee?->type === 'tendik' ? 'border-emerald-500 bg-emerald-50' : 'border-gray-200' }}">
                            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-3 group-hover:bg-emerald-200 transition">
                                <i class="fas fa-user-tie text-xl text-emerald-600"></i>
                            </div>
                            <h3 class="font-semibold text-gray-900">Tendik UNIDA</h3>
                            <p class="text-xs text-gray-500 mt-1">Tenaga kependidikan UNIDA</p>
                            @if($detectedType === 'dosen_tendik' && $detectedEmployee?->type === 'tendik')
                            <span class="inline-flex items-center gap-1 text-xs text-emerald-600 mt-2"><i class="fas fa-check-circle"></i> Terdeteksi</span>
                            @endif
                        </button>

                        {{-- Umum --}}
                        <button wire:click="selectUserType('umum')" 
                                class="group p-4 rounded-xl border-2 border-gray-200 transition-all text-left hover:border-gray-400 hover:bg-gray-50">
                            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center mb-3 group-hover:bg-gray-200 transition">
                                <i class="fas fa-users text-xl text-gray-600"></i>
                            </div>
                            <h3 class="font-semibold text-gray-900">Pengunjung Umum</h3>
                            <p class="text-xs text-gray-500 mt-1">Bukan civitas akademika UNIDA</p>
                        </button>
                    </div>

                    <div class="text-center pt-4 border-t mt-6">
                        <p class="text-sm text-gray-500">Sudah punya akun? <a href="{{ route('login') }}" class="text-primary-600 font-semibold hover:underline">Masuk di sini</a></p>
                    </div>
                </div>
                @endif

                {{-- Step 2: Verification --}}
                @if($step === 2)
                <div>
                    <button wire:click="goToStep(1)" class="flex items-center gap-2 text-gray-500 hover:text-gray-700 mb-4">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </button>

                    @if($userType === 'mahasiswa')
                    {{-- Mahasiswa Verification --}}
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-graduation-cap text-2xl text-primary-600"></i>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900">Verifikasi Mahasiswa</h2>
                        <p class="text-gray-500 text-sm">Masukkan NIM untuk menghubungkan dengan data SIAKAD</p>
                    </div>

                    @if($detectedMember && $claimVerified)
                    {{-- Verified --}}
                    <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-green-600 text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-green-800">{{ $detectedMember->name }}</p>
                                <p class="text-green-700 text-sm">NIM: {{ $detectedMember->member_id }}</p>
                                <p class="text-green-600 text-xs">{{ $detectedMember->department?->name ?? $detectedMember->faculty?->name ?? 'UNIDA Gontor' }}</p>
                            </div>
                        </div>
                    </div>
                    <button wire:click="goToStep(3)" class="w-full py-3 bg-primary-600 text-white rounded-xl font-semibold hover:bg-primary-700 transition">
                        Lanjutkan <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                    @else
                    {{-- Input NIM --}}
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Induk Mahasiswa (NIM)</label>
                            <input type="text" wire:model="claimNim" placeholder="Contoh: 432022413017" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-lg tracking-wider">
                            @error('claimNim') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <button wire:click="verifyClaim" class="w-full py-3 bg-primary-600 text-white rounded-xl font-semibold hover:bg-primary-700 transition">
                            <span wire:loading.remove wire:target="verifyClaim">Verifikasi NIM</span>
                            <span wire:loading wire:target="verifyClaim"><i class="fas fa-spinner fa-spin mr-2"></i> Mencari...</span>
                        </button>
                    </div>
                    @endif

                    @elseif(in_array($userType, ['dosen', 'tendik']))
                    {{-- Dosen/Tendik Verification --}}
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 {{ $userType === 'dosen' ? 'bg-blue-100' : 'bg-emerald-100' }} rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas {{ $userType === 'dosen' ? 'fa-chalkboard-teacher text-blue-600' : 'fa-user-tie text-emerald-600' }} text-2xl"></i>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900">Verifikasi {{ ucfirst($userType) }}</h2>
                        <p class="text-gray-500 text-sm">Masukkan NIY untuk menghubungkan dengan data SDM</p>
                    </div>

                    @if($detectedEmployee && $claimVerified)
                    {{-- Verified --}}
                    <div class="{{ $userType === 'dosen' ? 'bg-blue-50 border-blue-200' : 'bg-emerald-50 border-emerald-200' }} border rounded-xl p-4 mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 {{ $userType === 'dosen' ? 'bg-blue-100' : 'bg-emerald-100' }} rounded-full flex items-center justify-center">
                                <i class="fas fa-check {{ $userType === 'dosen' ? 'text-blue-600' : 'text-emerald-600' }} text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold {{ $userType === 'dosen' ? 'text-blue-800' : 'text-emerald-800' }}">{{ $detectedEmployee->full_name ?? $detectedEmployee->name }}</p>
                                <p class="{{ $userType === 'dosen' ? 'text-blue-700' : 'text-emerald-700' }} text-sm">NIY: {{ $detectedEmployee->niy }}</p>
                                <p class="{{ $userType === 'dosen' ? 'text-blue-600' : 'text-emerald-600' }} text-xs">{{ $detectedEmployee->faculty ?? $detectedEmployee->satker }} {{ $detectedEmployee->prodi ? '- '.$detectedEmployee->prodi : '' }}</p>
                            </div>
                        </div>
                    </div>
                    <button wire:click="goToStep(3)" class="w-full py-3 {{ $userType === 'dosen' ? 'bg-blue-600 hover:bg-blue-700' : 'bg-emerald-600 hover:bg-emerald-700' }} text-white rounded-xl font-semibold transition">
                        Lanjutkan <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                    @else
                    {{-- Input NIY --}}
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Induk Yayasan (NIY)</label>
                            <input type="text" wire:model="claimNiy" placeholder="Contoh: 200776" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-lg tracking-wider">
                            @error('claimNiy') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <button wire:click="verifyClaim" class="w-full py-3 {{ $userType === 'dosen' ? 'bg-blue-600 hover:bg-blue-700' : 'bg-emerald-600 hover:bg-emerald-700' }} text-white rounded-xl font-semibold transition">
                            <span wire:loading.remove wire:target="verifyClaim">Verifikasi NIY</span>
                            <span wire:loading wire:target="verifyClaim"><i class="fas fa-spinner fa-spin mr-2"></i> Mencari...</span>
                        </button>
                        
                        <p class="text-center text-sm text-gray-500">
                            Tidak tahu NIY? <a href="https://ekinerja.unida.gontor.ac.id" target="_blank" class="text-primary-600 hover:underline">Cek di E-Kinerja</a>
                        </p>
                    </div>
                    @endif

                    @elseif($userType === 'umum')
                    {{-- Umum - Skip to step 3 --}}
                    @php $this->goToStep(3); @endphp
                    @endif
                </div>
                @endif

                {{-- Step 3: Create Account --}}
                @if($step === 3)
                <div>
                    @if($userType !== 'umum')
                    <button wire:click="goToStep(2)" class="flex items-center gap-2 text-gray-500 hover:text-gray-700 mb-4">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </button>
                    @else
                    <button wire:click="goToStep(1)" class="flex items-center gap-2 text-gray-500 hover:text-gray-700 mb-4">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </button>
                    @endif

                    <div class="text-center mb-6">
                        <h2 class="text-lg font-bold text-gray-900">Buat Akun</h2>
                        <p class="text-gray-500 text-sm">Lengkapi data untuk membuat akun perpustakaan</p>
                    </div>

                    {{-- Verified Info Badge --}}
                    @if($detectedMember || $detectedEmployee)
                    <div class="bg-gray-50 rounded-xl p-3 mb-4 flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-check text-green-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">{{ $name }}</p>
                            <p class="text-xs text-gray-500">
                                @if($detectedMember) NIM: {{ $detectedMember->member_id }}
                                @elseif($detectedEmployee) NIY: {{ $detectedEmployee->niy }} - {{ ucfirst($detectedEmployee->type) }}
                                @endif
                            </p>
                        </div>
                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full font-medium">Terverifikasi</span>
                    </div>
                    @endif

                    <form wire:submit="register" class="space-y-4">
                        @if(!$detectedMember && !$detectedEmployee)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" wire:model="name" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" wire:model="email" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500" {{ $detectedType ? 'readonly' : '' }}>
                            @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            @if($detectedType === 'mahasiswa' || $detectedType === 'dosen_tendik')
                            <p class="text-xs text-green-600 mt-1"><i class="fas fa-check-circle mr-1"></i> Email kampus terverifikasi</p>
                            @endif
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. HP (Opsional)</label>
                            <input type="tel" wire:model="phone" placeholder="08xxxxxxxxxx" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            @error('phone') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        @if($userType === 'umum')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Institusi / Asal</label>
                            <input type="text" wire:model="institution" placeholder="Nama universitas/instansi" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            @error('institution') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kota Asal</label>
                            <input type="text" wire:model="institution_city" placeholder="Kota" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        @endif

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                <input type="password" wire:model="password" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                                <input type="password" wire:model="password_confirmation" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>

                        <button type="submit" class="w-full py-3 bg-primary-600 text-white rounded-xl font-semibold hover:bg-primary-700 transition mt-2">
                            <span wire:loading.remove wire:target="register">Daftar Sekarang</span>
                            <span wire:loading wire:target="register"><i class="fas fa-spinner fa-spin mr-2"></i> Memproses...</span>
                        </button>
                    </form>

                    <p class="text-center text-sm text-gray-500 mt-4">
                        Dengan mendaftar, Anda menyetujui <a href="#" class="text-primary-600 hover:underline">Syarat & Ketentuan</a>
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
