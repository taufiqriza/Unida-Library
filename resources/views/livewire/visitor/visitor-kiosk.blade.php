<div class="min-h-screen flex flex-col items-center justify-center p-4" wire:poll.5s>
    {{-- Header --}}
    <div class="text-center mb-6">
        <h1 class="text-3xl font-bold text-amber-800">{{ $branch->name }}</h1>
        <p class="text-amber-600">Pengunjung Hari Ini: <span class="font-bold text-2xl">{{ $todayCount }}</span></p>
    </div>

    <div class="w-full max-w-md">
        {{-- IDLE MODE --}}
        @if($mode === 'idle')
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <h2 class="text-xl font-semibold text-center text-gray-800 mb-6">Selamat Datang</h2>
            
            {{-- NIM Input --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-600 mb-2">Masukkan NIM / No. Anggota</label>
                <input type="text" wire:model="nim" wire:keydown.enter="searchMember"
                    class="w-full px-4 py-4 text-2xl text-center border-2 border-amber-200 rounded-xl focus:border-amber-500 focus:ring-2 focus:ring-amber-200"
                    placeholder="Contoh: 2024001" autofocus>
            </div>

            @if($errorMessage)
            <div class="mb-4 p-3 bg-red-50 text-red-600 rounded-lg text-center">{{ $errorMessage }}</div>
            @endif

            <button wire:click="searchMember" class="w-full py-4 bg-amber-500 hover:bg-amber-600 text-white text-xl font-semibold rounded-xl transition">
                Cari
            </button>

            <div class="mt-6 text-center">
                <button wire:click="switchToGuest" class="text-amber-600 hover:text-amber-800 underline">
                    Bukan anggota? Daftar sebagai Tamu
                </button>
            </div>
        </div>

        {{-- MEMBER FOUND --}}
        @elseif($mode === 'member' && $foundMember)
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-6">
                @if($foundMember->photo)
                <img src="{{ asset('storage/' . $foundMember->photo) }}" class="w-32 h-32 rounded-full mx-auto mb-4 object-cover border-4 border-amber-200">
                @else
                <div class="w-32 h-32 rounded-full mx-auto mb-4 bg-amber-100 flex items-center justify-center">
                    <span class="text-4xl text-amber-600">{{ substr($foundMember->name, 0, 1) }}</span>
                </div>
                @endif
                <h3 class="text-2xl font-bold text-gray-800">{{ $foundMember->name }}</h3>
                <p class="text-gray-500">{{ $foundMember->member_id }}</p>
                <p class="text-sm text-gray-400">{{ $foundMember->memberType?->name }}</p>
            </div>

            {{-- Purpose --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-600 mb-2">Tujuan Kunjungan</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach(['baca' => 'Membaca', 'pinjam' => 'Pinjam Buku', 'belajar' => 'Belajar', 'penelitian' => 'Penelitian', 'lainnya' => 'Lainnya'] as $key => $label)
                    <button wire:click="$set('purpose', '{{ $key }}')"
                        class="py-3 px-4 rounded-lg border-2 transition {{ $purpose === $key ? 'border-amber-500 bg-amber-50 text-amber-700' : 'border-gray-200 hover:border-amber-300' }}">
                        {{ $label }}
                    </button>
                    @endforeach
                </div>
            </div>

            <button wire:click="confirmMemberVisit" class="w-full py-4 bg-green-500 hover:bg-green-600 text-white text-xl font-semibold rounded-xl transition">
                Konfirmasi Kunjungan
            </button>

            <button wire:click="reset_form" class="w-full mt-3 py-3 text-gray-500 hover:text-gray-700">
                Batal
            </button>
        </div>

        {{-- GUEST MODE --}}
        @elseif($mode === 'guest')
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <h2 class="text-xl font-semibold text-center text-gray-800 mb-6">Daftar Tamu</h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Nama Lengkap</label>
                    <input type="text" wire:model="guestName" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-amber-500" placeholder="Nama Anda">
                    @error('guestName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Institusi / Asal</label>
                    <input type="text" wire:model="guestInstitution" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-amber-500" placeholder="Universitas / Sekolah / Umum">
                    @error('guestInstitution') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">Tujuan Kunjungan</label>
                    <select wire:model="purpose" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-amber-500">
                        <option value="baca">Membaca</option>
                        <option value="belajar">Belajar</option>
                        <option value="penelitian">Penelitian</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
            </div>

            <button wire:click="submitGuest" class="w-full mt-6 py-4 bg-green-500 hover:bg-green-600 text-white text-xl font-semibold rounded-xl transition">
                Daftar Kunjungan
            </button>

            <button wire:click="reset_form" class="w-full mt-3 py-3 text-gray-500 hover:text-gray-700">
                Kembali
            </button>
        </div>

        {{-- SUCCESS --}}
        @elseif($mode === 'success')
        <div class="bg-white rounded-2xl shadow-xl p-8 text-center" wire:init="$dispatch('auto-reset')">
            <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-green-600 mb-2">{{ $message }}</h2>
            <p class="text-gray-500 mb-6">Kunjungan Anda telah tercatat</p>
            
            <button wire:click="reset_form" class="px-8 py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-xl transition">
                Selesai
            </button>
        </div>

        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('auto-reset', () => {
                    setTimeout(() => { @this.reset_form() }, 5000);
                });
            });
        </script>
        @endif
    </div>

    {{-- Footer --}}
    <div class="mt-8 text-center text-amber-600 text-sm">
        Perpustakaan UNIDA Gontor
    </div>
</div>
