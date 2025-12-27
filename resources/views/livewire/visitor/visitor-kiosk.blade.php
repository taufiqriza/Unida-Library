<div class="min-h-screen flex" wire:poll.10s>
    {{-- Left Panel - Form --}}
    <div class="flex-1 flex flex-col items-center justify-center p-8">
        {{-- Logo & Header --}}
        <div class="text-center mb-8">
            <img src="{{ asset('storage/logo-portal.png') }}" alt="Logo" class="h-16 mx-auto mb-4" onerror="this.style.display='none'">
            <h1 class="text-2xl font-bold text-slate-800">{{ $branch->name }}</h1>
            <p class="text-slate-500">Sistem Pencatatan Kunjungan</p>
        </div>

        <div class="w-full max-w-md">
            {{-- IDLE MODE --}}
            @if($mode === 'idle')
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4">
                    <h2 class="text-white font-semibold text-lg">Selamat Datang</h2>
                    <p class="text-blue-100 text-sm">Silakan masukkan NIM atau No. Anggota</p>
                </div>
                
                <div class="p-6">
                    <div class="mb-4">
                        <input type="text" wire:model="nim" wire:keydown.enter="searchMember"
                            class="w-full px-4 py-4 text-xl text-center border-2 border-slate-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition"
                            placeholder="Masukkan NIM" autofocus>
                    </div>

                    @if($errorMessage)
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-600 rounded-xl text-center text-sm">
                        <i class="fas fa-exclamation-circle mr-2"></i>{{ $errorMessage }}
                    </div>
                    @endif

                    <button wire:click="searchMember" wire:loading.attr="disabled"
                        class="w-full py-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-lg font-semibold rounded-xl transition shadow-lg shadow-blue-500/30 disabled:opacity-50">
                        <span wire:loading.remove wire:target="searchMember">
                            <i class="fas fa-search mr-2"></i>Cari
                        </span>
                        <span wire:loading wire:target="searchMember">
                            <i class="fas fa-spinner fa-spin mr-2"></i>Mencari...
                        </span>
                    </button>

                    <div class="mt-6 text-center">
                        <button wire:click="switchToGuest" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            <i class="fas fa-user-plus mr-1"></i>Bukan anggota? Daftar sebagai Tamu
                        </button>
                    </div>
                </div>
            </div>

            {{-- MEMBER FOUND --}}
            @elseif($mode === 'member' && $foundMember)
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4">
                    <h2 class="text-white font-semibold">Konfirmasi Identitas</h2>
                </div>
                
                <div class="p-6">
                    <div class="text-center mb-6">
                        @if($foundMember->photo)
                        <img src="{{ asset('storage/' . $foundMember->photo) }}" class="w-24 h-24 rounded-full mx-auto mb-3 object-cover border-4 border-blue-100 shadow">
                        @else
                        <div class="w-24 h-24 rounded-full mx-auto mb-3 bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow">
                            <span class="text-3xl text-white font-bold">{{ substr($foundMember->name, 0, 1) }}</span>
                        </div>
                        @endif
                        <h3 class="text-xl font-bold text-slate-800">{{ $foundMember->name }}</h3>
                        <p class="text-slate-500">{{ $foundMember->member_id }}</p>
                        <span class="inline-block mt-1 px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full">
                            {{ $foundMember->memberType?->name ?? 'Anggota' }}
                        </span>
                    </div>

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-slate-600 mb-2">Tujuan Kunjungan</label>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach(['baca' => ['Membaca', 'fa-book-open'], 'pinjam' => ['Pinjam Buku', 'fa-hand-holding'], 'belajar' => ['Belajar', 'fa-graduation-cap'], 'penelitian' => ['Penelitian', 'fa-flask'], 'lainnya' => ['Lainnya', 'fa-ellipsis']] as $key => [$label, $icon])
                            <button wire:click="$set('purpose', '{{ $key }}')"
                                class="flex items-center gap-2 py-2.5 px-3 rounded-lg border-2 transition text-sm {{ $purpose === $key ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-slate-200 hover:border-blue-300 text-slate-600' }}">
                                <i class="fas {{ $icon }} text-xs"></i>{{ $label }}
                            </button>
                            @endforeach
                        </div>
                    </div>

                    <button wire:click="confirmMemberVisit" wire:loading.attr="disabled"
                        class="w-full py-3 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-semibold rounded-xl transition shadow-lg shadow-emerald-500/30">
                        <i class="fas fa-check mr-2"></i>Konfirmasi Kunjungan
                    </button>
                    <button wire:click="reset_form" class="w-full mt-2 py-2 text-slate-500 hover:text-slate-700 text-sm">Batal</button>
                </div>
            </div>

            {{-- GUEST MODE --}}
            @elseif($mode === 'guest')
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4">
                    <h2 class="text-white font-semibold">Registrasi Tamu</h2>
                </div>
                
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">Nama Lengkap</label>
                        <input type="text" wire:model="guestName" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100" placeholder="Masukkan nama">
                        @error('guestName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">Institusi / Asal</label>
                        <input type="text" wire:model="guestInstitution" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100" placeholder="Universitas / Sekolah / Umum">
                        @error('guestInstitution') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">Tujuan Kunjungan</label>
                        <select wire:model="purpose" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-blue-500">
                            <option value="baca">Membaca</option>
                            <option value="belajar">Belajar</option>
                            <option value="penelitian">Penelitian</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>

                    <button wire:click="submitGuest" class="w-full py-3 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-semibold rounded-xl transition shadow-lg shadow-emerald-500/30">
                        <i class="fas fa-check mr-2"></i>Daftar Kunjungan
                    </button>
                    <button wire:click="reset_form" class="w-full py-2 text-slate-500 hover:text-slate-700 text-sm">Kembali</button>
                </div>
            </div>

            {{-- SUCCESS --}}
            @elseif($mode === 'success')
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-8 text-center" wire:init="$dispatch('auto-reset')">
                <div class="w-20 h-20 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-full flex items-center justify-center mx-auto mb-5 shadow-lg shadow-emerald-500/30">
                    <i class="fas fa-check text-3xl text-white"></i>
                </div>
                <h2 class="text-2xl font-bold text-slate-800 mb-2">{{ $message }}</h2>
                <p class="text-slate-500 mb-6">Kunjungan Anda telah tercatat</p>
                <p class="text-slate-400 text-sm mb-4">Halaman akan reset dalam 5 detik...</p>
                <button wire:click="reset_form" class="px-6 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition">
                    Selesai
                </button>
            </div>
            <script>
                document.addEventListener('livewire:init', () => {
                    Livewire.on('auto-reset', () => { setTimeout(() => { @this.reset_form() }, 5000); });
                });
            </script>
            @endif
        </div>
    </div>

    {{-- Right Panel - Statistics --}}
    <div class="hidden lg:flex w-80 bg-gradient-to-b from-blue-600 to-indigo-800 flex-col p-6 text-white">
        <div class="mb-8">
            <h3 class="text-blue-200 text-sm font-medium mb-1">Statistik Hari Ini</h3>
            <p class="text-xs text-blue-300">{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
        </div>

        {{-- Today Stats --}}
        <div class="bg-white/10 backdrop-blur rounded-xl p-4 mb-4">
            <div class="text-center">
                <p class="text-5xl font-bold">{{ $todayCount }}</p>
                <p class="text-blue-200 text-sm">Total Pengunjung</p>
            </div>
        </div>

        {{-- By Type --}}
        <div class="bg-white/10 backdrop-blur rounded-xl p-4 mb-4">
            <p class="text-xs text-blue-200 mb-3 font-medium">Berdasarkan Tipe</p>
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-sm"><i class="fas fa-user-check mr-2 text-blue-300"></i>Anggota</span>
                    <span class="font-bold">{{ $todayStats['member'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm"><i class="fas fa-user mr-2 text-blue-300"></i>Tamu</span>
                    <span class="font-bold">{{ $todayStats['guest'] ?? 0 }}</span>
                </div>
            </div>
        </div>

        {{-- By Purpose --}}
        <div class="bg-white/10 backdrop-blur rounded-xl p-4 flex-1">
            <p class="text-xs text-blue-200 mb-3 font-medium">Berdasarkan Tujuan</p>
            <div class="space-y-2 text-sm">
                @foreach(['baca' => 'Membaca', 'pinjam' => 'Pinjam', 'belajar' => 'Belajar', 'penelitian' => 'Penelitian', 'lainnya' => 'Lainnya'] as $key => $label)
                <div class="flex justify-between items-center">
                    <span>{{ $label }}</span>
                    <span class="font-bold">{{ $todayStats[$key] ?? 0 }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Footer --}}
        <div class="mt-6 pt-4 border-t border-white/20 text-center">
            <p class="text-blue-200 text-xs">Perpustakaan UNIDA Gontor</p>
        </div>
    </div>
</div>
