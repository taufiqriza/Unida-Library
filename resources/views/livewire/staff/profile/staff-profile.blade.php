<div class="space-y-5">
    {{-- Header --}}
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-500/30">
            <i class="fas fa-user-circle text-xl"></i>
        </div>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Profil Saya</h1>
            <p class="text-sm text-gray-500">Kelola informasi akun Anda</p>
        </div>
    </div>

    {{-- Profile Card --}}
    <div class="bg-gradient-to-br from-blue-700 via-blue-800 to-indigo-900 rounded-2xl p-6 text-white" x-data="{ showLinkingModal: false }">
        <div class="flex flex-col sm:flex-row items-center gap-5">
            {{-- Avatar with Upload --}}
            <div class="relative group">
                <div class="w-24 h-24 rounded-2xl bg-white/10 overflow-hidden border-2 border-white/20">
                    @if($user->photo)
                        <img src="{{ asset('storage/'.$user->photo) }}" class="w-full h-full object-cover">
                    @elseif($photo)
                        <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-white/10">
                            <span class="text-3xl font-bold">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                        </div>
                    @endif
                </div>
                <label class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 group-hover:opacity-100 rounded-2xl cursor-pointer transition">
                    <i class="fas fa-camera text-xl"></i>
                    <input type="file" wire:model="photo" accept="image/*" class="hidden">
                </label>
                @if($user->photo)
                <button wire:click="removePhoto" wire:confirm="Hapus foto profil?" class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 hover:bg-red-600 rounded-full flex items-center justify-center text-xs shadow-lg transition">
                    <i class="fas fa-times"></i>
                </button>
                @endif
            </div>
            
            {{-- Info --}}
            <div class="text-center sm:text-left flex-1">
                <h2 class="text-xl font-bold">{{ $user->name }}</h2>
                <p class="text-blue-200 text-sm">{{ $user->email }}</p>
                <div class="flex flex-wrap justify-center sm:justify-start gap-2 mt-2">
                    <span class="px-3 py-1 bg-white/15 text-xs font-medium rounded-full">
                        <i class="fas fa-user-shield mr-1"></i>{{ $user->getRoleLabel() }}
                    </span>
                    @if($branch)
                    <span class="px-3 py-1 bg-white/15 text-xs font-medium rounded-full">
                        <i class="fas fa-building mr-1"></i>{{ $branch->name }}
                    </span>
                    @endif
                </div>
            </div>
            
            {{-- Stats & Member Linking --}}
            <div class="flex gap-6 text-center">
                <div>
                    <p class="text-2xl font-bold">{{ number_format($user->created_at->diffInDays(now()), 0) }}</p>
                    <p class="text-xs text-blue-200">Hari Aktif</p>
                </div>
                <div class="w-px bg-white/20"></div>
                <div>
                    <p class="text-2xl font-bold">{{ $user->created_at->format('Y') }}</p>
                    <p class="text-xs text-blue-200">Bergabung</p>
                </div>
                <div class="w-px bg-white/20"></div>
                <div class="text-left">
                    {{-- Member Linking Component --}}
                    @livewire('staff.profile.member-linking')
                </div>
            </div>
        </div>
        
        {{-- Photo Upload Button --}}
        @if($photo)
        <div class="mt-4 flex items-center justify-center gap-3">
            <span class="text-sm text-blue-200">Foto baru dipilih</span>
            <button wire:click="updatePhoto" class="px-4 py-2 bg-white text-blue-700 font-semibold text-sm rounded-lg hover:bg-blue-50 transition">
                <span wire:loading.remove wire:target="updatePhoto">Simpan Foto</span>
                <span wire:loading wire:target="updatePhoto"><i class="fas fa-spinner fa-spin"></i></span>
            </button>
            <button wire:click="$set('photo', null)" class="px-4 py-2 bg-white/20 text-white text-sm rounded-lg hover:bg-white/30 transition">
                Batal
            </button>
        </div>
        @endif
        @error('photo') <p class="mt-2 text-center text-red-300 text-sm">{{ $message }}</p> @enderror
    </div>

    <div class="grid lg:grid-cols-3 gap-5">
        {{-- Left Column --}}
        <div class="lg:col-span-2 space-y-5">
            {{-- Edit Profile --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user-edit text-white"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Informasi Profil</h3>
                        <p class="text-xs text-gray-500">Perbarui data profil Anda</p>
                    </div>
                </div>
                <form wire:submit="updateProfile" class="p-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" wire:model="name" 
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" wire:model="email"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-medium rounded-xl shadow-lg shadow-blue-500/25 transition flex items-center gap-2">
                            <span wire:loading.remove wire:target="updateProfile"><i class="fas fa-save mr-1"></i> Simpan</span>
                            <span wire:loading wire:target="updateProfile"><i class="fas fa-spinner fa-spin"></i></span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Change Password --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-key text-white"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Keamanan</h3>
                            <p class="text-xs text-gray-500">Ubah password akun</p>
                        </div>
                    </div>
                    @if(!$showPasswordForm)
                    <button wire:click="$set('showPasswordForm', true)" class="px-4 py-2 text-sm font-medium text-amber-600 hover:bg-amber-50 rounded-lg transition">
                        Ubah Password
                    </button>
                    @endif
                </div>
                
                @if($showPasswordForm)
                <form wire:submit="updatePassword" class="p-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password Lama</label>
                        <input type="password" wire:model="current_password" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                        @error('current_password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                        <input type="password" wire:model="password" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                        @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                        <input type="password" wire:model="password_confirmation" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" wire:click="$set('showPasswordForm', false)" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition">Batal</button>
                        <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-medium rounded-xl shadow-lg shadow-amber-500/25 transition">
                            <span wire:loading.remove wire:target="updatePassword">Simpan Password</span>
                            <span wire:loading wire:target="updatePassword"><i class="fas fa-spinner fa-spin"></i></span>
                        </button>
                    </div>
                </form>
                @else
                <div class="p-5 flex items-center gap-3 text-gray-500">
                    <i class="fas fa-lock"></i>
                    <span class="text-sm">Password terakhir diubah: <span class="font-medium">{{ $user->updated_at->format('d M Y, H:i') }} WIB</span></span>
                </div>
                @endif
            </div>
        </div>

        {{-- Right Column --}}
        <div class="space-y-5">
            {{-- Branch Info --}}
            @if($branch)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-building text-gray-400"></i> Cabang
                    </h3>
                </div>
                <div class="p-5">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-11 h-11 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-map-marker-alt text-white"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $branch->name }}</p>
                            <p class="text-xs text-gray-500">{{ $branch->code }}</p>
                        </div>
                    </div>
                    <div class="space-y-2 text-sm text-gray-600">
                        @if($branch->address)
                        <p><i class="fas fa-location-dot text-gray-400 w-5"></i>{{ $branch->address }}</p>
                        @endif
                        @if($branch->phone)
                        <p><i class="fas fa-phone text-gray-400 w-5"></i>{{ $branch->phone }}</p>
                        @endif
                        @if($branch->email)
                        <p><i class="fas fa-envelope text-gray-400 w-5"></i>{{ $branch->email }}</p>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- Google Account --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fab fa-google text-gray-400"></i> Akun Google
                    </h3>
                </div>
                <div class="p-5">
                    @if($googleAccount)
                    <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-200">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                                <i class="fab fa-google text-emerald-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-emerald-800 truncate text-sm">{{ $googleAccount->provider_email }}</p>
                                <p class="text-xs text-emerald-600">Terhubung</p>
                            </div>
                        </div>
                        <button wire:click="unlinkGoogle" wire:confirm="Yakin ingin memutuskan akun Google?" class="w-full px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 border border-red-200 rounded-lg transition">
                            <i class="fas fa-unlink mr-1"></i> Putuskan
                        </button>
                    </div>
                    @else
                    <div class="text-center py-2">
                        <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                            <i class="fab fa-google text-gray-300 text-lg"></i>
                        </div>
                        <p class="text-sm text-gray-600 mb-3">Belum terhubung</p>
                        <a href="{{ route('auth.google') }}?link_staff=1" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 hover:border-blue-500 hover:bg-blue-50 text-gray-700 hover:text-blue-600 font-medium rounded-xl transition text-sm">
                            <i class="fab fa-google"></i> Hubungkan
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Member Linking --}}

            {{-- Account Info --}}
            <div class="bg-gray-50 rounded-2xl border border-gray-200 p-5">
                <h4 class="font-medium text-gray-700 mb-3 text-sm flex items-center gap-2">
                    <i class="fas fa-info-circle text-gray-400"></i> Info Akun
                </h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Status</span>
                        <span class="font-medium {{ $user->is_active ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Terdaftar</span>
                        <span class="font-medium text-gray-700">{{ $user->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('notify', (data) => {
                alert((data[0].type === 'success' ? '✓ ' : '⚠ ') + data[0].message);
            });
        });
    </script>
</div>
