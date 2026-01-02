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
                        <span class="text-sm {{ $step >= 1 ? 'text-white' : 'text-white/60' }}">Status</span>
                    </div>
                    <div class="flex-1 h-0.5 {{ $step >= 2 ? 'bg-white' : 'bg-white/30' }}"></div>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold {{ $step >= 2 ? 'bg-white text-primary-600' : 'bg-white/30' }}">2</div>
                        <span class="text-sm {{ $step >= 2 ? 'text-white' : 'text-white/60' }}">Verifikasi</span>
                    </div>
                    <div class="flex-1 h-0.5 {{ $step >= 3 ? 'bg-white' : 'bg-white/30' }}"></div>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold {{ $step >= 3 ? 'bg-white text-primary-600' : 'bg-white/30' }}">3</div>
                        <span class="text-sm {{ $step >= 3 ? 'text-white' : 'text-white/60' }}">Akun</span>
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
                                <p class="text-blue-600 text-xs">{{ ucfirst($detectedEmployee->type) }} - {{ $detectedEmployee->faculty ?? $detectedEmployee->satker }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- User Type Cards --}}
                    <div class="grid grid-cols-2 gap-3">
                        <button wire:click="selectUserType('mahasiswa')" class="group p-4 rounded-xl border-2 transition-all text-left hover:border-primary-500 hover:bg-primary-50 {{ $detectedType === 'mahasiswa' ? 'border-primary-500 bg-primary-50' : 'border-gray-200' }}">
                            <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center mb-3 group-hover:bg-primary-200 transition">
                                <i class="fas fa-graduation-cap text-xl text-primary-600"></i>
                            </div>
                            <h3 class="font-semibold text-gray-900">Mahasiswa UNIDA</h3>
                            <p class="text-xs text-gray-500 mt-1">Mahasiswa aktif UNIDA Gontor</p>
                        </button>

                        <button wire:click="selectUserType('dosen')" class="group p-4 rounded-xl border-2 transition-all text-left hover:border-blue-500 hover:bg-blue-50 {{ $detectedType === 'dosen_tendik' && $detectedEmployee?->type === 'dosen' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-3 group-hover:bg-blue-200 transition">
                                <i class="fas fa-chalkboard-teacher text-xl text-blue-600"></i>
                            </div>
                            <h3 class="font-semibold text-gray-900">Dosen UNIDA</h3>
                            <p class="text-xs text-gray-500 mt-1">Dosen tetap/tidak tetap</p>
                        </button>

                        <button wire:click="selectUserType('tendik')" class="group p-4 rounded-xl border-2 transition-all text-left hover:border-emerald-500 hover:bg-emerald-50 {{ $detectedType === 'dosen_tendik' && $detectedEmployee?->type === 'tendik' ? 'border-emerald-500 bg-emerald-50' : 'border-gray-200' }}">
                            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-3 group-hover:bg-emerald-200 transition">
                                <i class="fas fa-user-tie text-xl text-emerald-600"></i>
                            </div>
                            <h3 class="font-semibold text-gray-900">Tendik UNIDA</h3>
                            <p class="text-xs text-gray-500 mt-1">Tenaga kependidikan</p>
                        </button>

                        <button wire:click="selectUserType('umum')" class="group p-4 rounded-xl border-2 border-gray-200 transition-all text-left hover:border-gray-400 hover:bg-gray-50">
                            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center mb-3 group-hover:bg-gray-200 transition">
                                <i class="fas fa-users text-xl text-gray-600"></i>
                            </div>
                            <h3 class="font-semibold text-gray-900">Pengunjung Umum</h3>
                            <p class="text-xs text-gray-500 mt-1">Bukan civitas UNIDA</p>
                        </button>
                    </div>

                    <div class="text-center pt-4 border-t mt-6">
                        <p class="text-sm text-gray-500">Sudah punya akun? <a href="{{ route('login') }}" class="text-primary-600 font-semibold hover:underline">Masuk di sini</a></p>
                    </div>
                </div>
                @endif

                {{-- Step 2: Search & Verify --}}
                @if($step === 2)
                <div>
                    <button wire:click="goToStep(1)" class="flex items-center gap-2 text-gray-500 hover:text-gray-700 mb-4">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </button>

                    @php
                        $colors = match($userType) {
                            'mahasiswa' => ['bg' => 'bg-primary-100', 'text' => 'text-primary-600', 'border' => 'border-primary-500', 'btn' => 'bg-primary-600 hover:bg-primary-700'],
                            'dosen' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'border' => 'border-blue-500', 'btn' => 'bg-blue-600 hover:bg-blue-700'],
                            'tendik' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'border' => 'border-emerald-500', 'btn' => 'bg-emerald-600 hover:bg-emerald-700'],
                            default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'border' => 'border-gray-500', 'btn' => 'bg-gray-600 hover:bg-gray-700'],
                        };
                        $icon = match($userType) {
                            'mahasiswa' => 'fa-graduation-cap',
                            'dosen' => 'fa-chalkboard-teacher',
                            'tendik' => 'fa-user-tie',
                            default => 'fa-user',
                        };
                    @endphp

                    <div class="text-center mb-6">
                        <div class="w-16 h-16 {{ $colors['bg'] }} rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas {{ $icon }} text-2xl {{ $colors['text'] }}"></i>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900">Cari Data {{ ucfirst($userType) }}</h2>
                        <p class="text-gray-500 text-sm">
                            @if($userType === 'mahasiswa')
                                Cari berdasarkan NIM atau Nama
                            @else
                                Cari berdasarkan NIY, NIDN, Nama, atau Email
                            @endif
                        </p>
                    </div>

                    @if($claimVerified && ($detectedMember || $detectedEmployee))
                    {{-- Selected/Verified --}}
                    <div class="{{ str_replace('bg-', 'bg-', $colors['bg']) }}/50 border {{ $colors['border'] }} rounded-xl p-4 mb-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 {{ $colors['bg'] }} rounded-full flex items-center justify-center">
                                    <i class="fas fa-check {{ $colors['text'] }} text-xl"></i>
                                </div>
                                <div>
                                    @if($detectedMember)
                                    <p class="font-semibold text-gray-900">{{ $detectedMember->name }}</p>
                                    <p class="text-sm text-gray-600">NIM: {{ $detectedMember->member_id }}</p>
                                    @if($detectedMember->faculty)
                                    <p class="text-xs text-gray-500">{{ $detectedMember->faculty?->name }} - {{ $detectedMember->department?->name }}</p>
                                    @endif
                                    @elseif($detectedEmployee)
                                    <p class="font-semibold text-gray-900">{{ $detectedEmployee->full_name ?? $detectedEmployee->name }}</p>
                                    <p class="text-sm text-gray-600">NIY: {{ $detectedEmployee->niy }} @if($detectedEmployee->nidn)• NIDN: {{ $detectedEmployee->nidn }}@endif</p>
                                    <p class="text-xs text-gray-500">{{ $detectedEmployee->faculty ?? $detectedEmployee->satker }} {{ $detectedEmployee->prodi ? '- '.$detectedEmployee->prodi : '' }}</p>
                                    @endif
                                </div>
                            </div>
                            <button wire:click="clearSelection" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-white rounded-lg">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <button wire:click="goToStep(3)" class="w-full py-3 {{ $colors['btn'] }} text-white rounded-xl font-semibold transition">
                        Lanjutkan <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                    @else
                    {{-- Search Input with Dropdown --}}
                    <div x-data="{ 
                        open: false,
                        position: { top: 0, left: 0, width: 0 },
                        updatePosition() {
                            const input = this.$refs.searchInput;
                            const rect = input.getBoundingClientRect();
                            this.position = { 
                                top: rect.bottom + window.scrollY + 8, 
                                left: rect.left + window.scrollX, 
                                width: rect.width 
                            };
                        }
                    }" @click.away="open = false" class="relative">
                        <div class="relative">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" wire:model.live.debounce.300ms="searchQuery" 
                                   x-ref="searchInput"
                                   @focus="updatePosition(); open = true"
                                   @input="updatePosition(); open = true"
                                   placeholder="{{ $userType === 'mahasiswa' ? 'Ketik NIM atau Nama...' : 'Ketik NIY, NIDN, Nama, atau Email...' }}"
                                   class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        
                        {{-- Search Results Dropdown --}}
                        <template x-teleport="body">
                            <div x-show="open && ({{ count($searchResults) }} > 0 || {{ strlen($searchQuery) >= 3 ? 'true' : 'false' }})"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 :style="'position: fixed; top: ' + position.top + 'px; left: ' + position.left + 'px; width: ' + position.width + 'px; z-index: 99999;'"
                                 class="bg-white border border-gray-200 rounded-xl shadow-2xl max-h-64 overflow-y-auto">
                                @if(count($searchResults) > 0)
                                    @foreach($searchResults as $result)
                                    <button wire:click="selectResult({{ $result['id'] }})" @click="open = false"
                                            class="w-full px-4 py-3 text-left hover:bg-primary-50 border-b border-gray-100 last:border-0 transition">
                                        <p class="font-medium text-gray-900">{{ $result['name'] }}</p>
                                        <p class="text-sm text-gray-500">
                                            @if($userType === 'mahasiswa')
                                                NIM: {{ $result['nim'] }}
                                            @else
                                                NIY: {{ $result['niy'] ?? '-' }} 
                                                @if($result['nidn'])• NIDN: {{ $result['nidn'] }}@endif
                                                @if($result['unit'])• {{ $result['unit'] }}@endif
                                            @endif
                                        </p>
                                    </button>
                                    @endforeach
                                @elseif(strlen($searchQuery) >= 3)
                                    <div class="p-4 text-center">
                                        <i class="fas fa-search text-gray-300 text-2xl mb-2"></i>
                                        <p class="text-gray-500 text-sm">Tidak ditemukan data yang cocok</p>
                                    </div>
                                @endif
                            </div>
                        </template>
                    </div>

                    <p class="text-center text-sm text-gray-500 mt-4">
                        @if($userType === 'mahasiswa')
                            Data tidak ditemukan? Pastikan Anda sudah terdaftar di SIAKAD.
                        @else
                            Tidak tahu NIY? <a href="https://ekinerja.unida.gontor.ac.id" target="_blank" class="text-primary-600 hover:underline">Cek di E-Kinerja</a>
                        @endif
                    </p>
                    @endif
                </div>
                @endif

                {{-- Step 3: Create Account --}}
                @if($step === 3)
                <div>
                    <button wire:click="goToStep({{ $userType === 'umum' ? 1 : 2 }})" class="flex items-center gap-2 text-gray-500 hover:text-gray-700 mb-4">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </button>

                    <div class="text-center mb-6">
                        <h2 class="text-lg font-bold text-gray-900">Buat Akun</h2>
                        <p class="text-gray-500 text-sm">Lengkapi data untuk membuat akun perpustakaan</p>
                    </div>

                    {{-- Verified Info Badge --}}
                    @if($claimVerified && ($detectedMember || $detectedEmployee))
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
                        @if($userType === 'umum')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="name" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Nama sesuai KTP">
                            @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                            <input type="email" wire:model="email" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="email@example.com">
                            @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            @if($userType !== 'umum' && $claimVerified)
                            <p class="text-xs text-gray-500 mt-1"><i class="fas fa-info-circle mr-1"></i> Gunakan email aktif untuk verifikasi</p>
                            @endif
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                            <input type="tel" wire:model="phone" placeholder="08xxxxxxxxxx" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>

                        @if($userType === 'umum')
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Institusi/Asal <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="institution" placeholder="Nama universitas/instansi" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                @error('institution') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                                <input type="text" wire:model="institution_city" placeholder="Kota asal" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>
                        @endif

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                                <input type="password" wire:model="password" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Min. 8 karakter">
                                @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi <span class="text-red-500">*</span></label>
                                <input type="password" wire:model="password_confirmation" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Ulangi password">
                            </div>
                        </div>

                        <button type="submit" class="w-full py-3 bg-primary-600 text-white rounded-xl font-semibold hover:bg-primary-700 transition mt-2">
                            <span wire:loading.remove wire:target="register">Daftar Sekarang</span>
                            <span wire:loading wire:target="register"><i class="fas fa-spinner fa-spin mr-2"></i> Memproses...</span>
                        </button>
                    </form>

                    @if($userType === 'umum')
                    <p class="text-center text-xs text-gray-500 mt-4">
                        Setelah mendaftar, Anda akan menerima kode OTP via email untuk verifikasi.
                    </p>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
