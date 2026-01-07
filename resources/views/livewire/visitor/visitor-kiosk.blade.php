<div class="min-h-screen flex flex-col items-center justify-center p-6" wire:poll.10s>
    {{-- Success Modal --}}
    @if($mode === 'success')
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data x-init="setTimeout(() => @this.reset_form(), 3000)">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl p-10 max-w-md w-full text-center transform animate-[bounceIn_0.5s_ease-out]">
            <div class="w-28 h-28 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl shadow-emerald-500/40">
                <i class="fas fa-check text-5xl text-white"></i>
            </div>
            <h2 class="text-3xl font-bold text-slate-800 mb-3">{{ $message }}</h2>
            <p class="text-slate-500 text-lg">Kunjungan berhasil dicatat</p>
            <div class="mt-6 flex justify-center gap-1">
                <div class="w-2 h-2 bg-emerald-400 rounded-full animate-bounce" style="animation-delay: 0s"></div>
                <div class="w-2 h-2 bg-emerald-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                <div class="w-2 h-2 bg-emerald-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
            </div>
        </div>
    </div>
    @endif

    {{-- Main Content --}}
    <div class="w-full max-w-4xl">
        {{-- Header --}}
        <div class="text-center mb-10">
            <img src="{{ asset('storage/logo-portal.png') }}" alt="Logo" class="h-20 mx-auto mb-5" onerror="this.style.display='none'">
            <h1 class="text-4xl font-bold text-slate-800 mb-2">{{ $branch->name }}</h1>
            <p class="text-slate-500 text-lg">Sistem Pencatatan Kunjungan Perpustakaan</p>
        </div>

        {{-- Main Card --}}
        <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden mb-8">
            @if($mode === 'idle' || $mode === 'success')
            {{-- Input NIM --}}
            <div class="p-10">
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl mb-4 shadow-lg shadow-blue-500/30">
                        <i class="fas fa-id-card text-3xl text-white"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-800">Masukkan NIM / No. Anggota</h2>
                    <p class="text-slate-500 mt-1">Atau daftar sebagai tamu</p>
                </div>

                <div class="max-w-lg mx-auto" x-data="{ 
                    lastKeyTime: 0,
                    inputBuffer: '',
                    handleInput(e) {
                        const now = Date.now();
                        // Scanner types very fast (< 50ms between keys)
                        if (now - this.lastKeyTime < 50) {
                            // Likely scanner input
                            this.inputBuffer += e.data || '';
                        } else {
                            this.inputBuffer = e.data || '';
                        }
                        this.lastKeyTime = now;
                    },
                    handleKeydown(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            // Small delay to ensure model is updated
                            setTimeout(() => $wire.searchMember(), 50);
                        }
                    }
                }">
                    <input type="text" wire:model="nim" 
                        @input="handleInput($event)"
                        @keydown="handleKeydown($event)"
                        class="w-full px-8 py-6 text-3xl text-center border-3 border-slate-200 rounded-2xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition font-mono tracking-widest"
                        placeholder="Ketik NIM..." autofocus>

                    @if($errorMessage)
                    <div class="mt-4 p-4 bg-red-50 border border-red-200 text-red-600 rounded-xl text-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>{{ $errorMessage }}
                    </div>
                    @endif

                    <div class="mt-6 flex gap-3">
                        <button wire:click="searchMember" wire:loading.attr="disabled"
                            class="flex-1 py-5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-xl font-bold rounded-2xl transition shadow-lg shadow-blue-500/30 disabled:opacity-50">
                            <span wire:loading.remove wire:target="searchMember"><i class="fas fa-search mr-3"></i>Cari</span>
                            <span wire:loading wire:target="searchMember"><i class="fas fa-spinner fa-spin mr-3"></i>Mencari...</span>
                        </button>
                        <button wire:click="switchToGuest"
                            class="px-8 py-5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xl font-bold rounded-2xl transition">
                            <i class="fas fa-user-plus"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Member Found - Quick Select Purpose --}}
            @elseif($mode === 'member' && $foundMember)
            <div class="p-10">
                {{-- Member Info --}}
                <div class="flex items-center gap-6 mb-8 pb-8 border-b border-slate-100">
                    @if($foundMember->photo)
                    <img src="{{ asset('storage/' . $foundMember->photo) }}" class="w-28 h-28 rounded-2xl object-cover border-4 border-blue-100 shadow-lg">
                    @else
                    <div class="w-28 h-28 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg">
                        <span class="text-5xl text-white font-bold">{{ substr($foundMember->name, 0, 1) }}</span>
                    </div>
                    @endif
                    <div class="flex-1">
                        <h3 class="text-3xl font-bold text-slate-800">{{ $foundMember->name }}</h3>
                        <p class="text-slate-500 text-xl mt-1">{{ $foundMember->member_id }}</p>
                        <span class="inline-block mt-2 px-4 py-1.5 bg-blue-100 text-blue-700 text-sm font-semibold rounded-full">
                            {{ $foundMember->memberType?->name ?? 'Anggota' }}
                        </span>
                    </div>
                    <button wire:click="reset_form" class="w-12 h-12 bg-slate-100 hover:bg-slate-200 rounded-xl flex items-center justify-center text-slate-500 transition">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                {{-- Purpose Selection - Click to Submit --}}
                <div class="text-center mb-6">
                    <h4 class="text-xl font-bold text-slate-700">Pilih Tujuan Kunjungan</h4>
                    <p class="text-slate-500">Klik salah satu untuk langsung check-in</p>
                </div>

                <div class="grid grid-cols-5 gap-4">
                    @foreach([
                        'baca' => ['Membaca', 'fa-book-open', 'from-blue-500 to-blue-600', 'blue'],
                        'pinjam' => ['Pinjam Buku', 'fa-hand-holding', 'from-emerald-500 to-teal-600', 'emerald'],
                        'belajar' => ['Belajar', 'fa-graduation-cap', 'from-violet-500 to-purple-600', 'violet'],
                        'penelitian' => ['Penelitian', 'fa-flask', 'from-amber-500 to-orange-600', 'amber'],
                        'lainnya' => ['Lainnya', 'fa-ellipsis', 'from-slate-500 to-slate-600', 'slate']
                    ] as $key => [$label, $icon, $gradient, $color])
                    <button wire:click="quickCheckIn('{{ $key }}')" wire:loading.attr="disabled"
                        class="group flex flex-col items-center gap-4 p-6 rounded-2xl border-2 border-slate-200 hover:border-{{ $color }}-400 hover:bg-{{ $color }}-50 transition-all hover:scale-105 active:scale-95">
                        <div class="w-16 h-16 bg-gradient-to-br {{ $gradient }} rounded-2xl flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-transform">
                            <i class="fas {{ $icon }} text-2xl"></i>
                        </div>
                        <span class="font-bold text-slate-700">{{ $label }}</span>
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Guest Mode --}}
            @elseif($mode === 'guest')
            <div class="p-10">
                <div class="flex items-center justify-between mb-8 pb-6 border-b border-slate-100">
                    <div>
                        <h3 class="text-2xl font-bold text-slate-800">Registrasi Tamu</h3>
                        <p class="text-slate-500">Isi data singkat untuk mencatat kunjungan</p>
                    </div>
                    <button wire:click="reset_form" class="w-12 h-12 bg-slate-100 hover:bg-slate-200 rounded-xl flex items-center justify-center text-slate-500 transition">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-600 mb-2">Nama Lengkap</label>
                        <input type="text" wire:model="guestName" class="w-full px-5 py-4 text-lg border-2 border-slate-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100" placeholder="Masukkan nama">
                        @error('guestName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-600 mb-2">Institusi / Asal</label>
                        <input type="text" wire:model="guestInstitution" class="w-full px-5 py-4 text-lg border-2 border-slate-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100" placeholder="Universitas / Sekolah">
                        @error('guestInstitution') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="text-center mb-4">
                    <p class="text-slate-600 font-semibold">Pilih Tujuan Kunjungan</p>
                </div>

                <div class="grid grid-cols-4 gap-3">
                    @foreach([
                        'baca' => ['Membaca', 'fa-book-open', 'from-blue-500 to-blue-600'],
                        'belajar' => ['Belajar', 'fa-graduation-cap', 'from-violet-500 to-purple-600'],
                        'penelitian' => ['Penelitian', 'fa-flask', 'from-amber-500 to-orange-600'],
                        'lainnya' => ['Lainnya', 'fa-ellipsis', 'from-slate-500 to-slate-600']
                    ] as $key => [$label, $icon, $gradient])
                    <button wire:click="quickGuestCheckIn('{{ $key }}')" wire:loading.attr="disabled"
                        class="group flex flex-col items-center gap-3 p-5 rounded-xl border-2 border-slate-200 hover:border-blue-400 hover:bg-blue-50 transition-all">
                        <div class="w-12 h-12 bg-gradient-to-br {{ $gradient }} rounded-xl flex items-center justify-center text-white shadow group-hover:scale-110 transition-transform">
                            <i class="fas {{ $icon }} text-lg"></i>
                        </div>
                        <span class="font-semibold text-slate-700 text-sm">{{ $label }}</span>
                    </button>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Statistics Bar --}}
        <div class="bg-white/80 backdrop-blur rounded-2xl shadow-lg border border-slate-200 p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-chart-line text-white"></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Pengunjung Hari Ini</p>
                        <p class="text-2xl font-bold text-slate-800">{{ $todayCount }}</p>
                    </div>
                </div>
                <div class="flex gap-8">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-blue-600">{{ $todayStats['member'] ?? 0 }}</p>
                        <p class="text-xs text-slate-500">Anggota</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-emerald-600">{{ $todayStats['guest'] ?? 0 }}</p>
                        <p class="text-xs text-slate-500">Tamu</p>
                    </div>
                    <div class="h-12 w-px bg-slate-200"></div>
                    @foreach(['baca' => 'Baca', 'pinjam' => 'Pinjam', 'belajar' => 'Belajar', 'penelitian' => 'Riset'] as $key => $label)
                    <div class="text-center">
                        <p class="text-xl font-bold text-slate-700">{{ $todayStats[$key] ?? 0 }}</p>
                        <p class="text-xs text-slate-500">{{ $label }}</p>
                    </div>
                    @endforeach
                </div>
                <div class="text-right">
                    <p class="text-xs text-slate-400">{{ now()->locale('id')->isoFormat('dddd, D MMM Y') }}</p>
                    <p class="text-sm font-semibold text-slate-600">Perpustakaan UNIDA Gontor</p>
                </div>
            </div>
        </div>
    </div>
</div>
