<div>
    <div class="min-h-screen bg-gray-50 pb-20">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-primary-600 to-primary-800 text-white">
            <div class="max-w-5xl mx-auto px-4 py-8">
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('opac.member.dashboard') }}" class="text-primary-200 hover:text-white transition">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h1 class="text-2xl font-bold">Pengaturan Akun</h1>
                </div>
                <p class="text-primary-100 text-sm ml-7">Kelola informasi profil dan keamanan akun Anda</p>
            </div>
        </div>

        <div class="max-w-5xl mx-auto px-4 -mt-6">
            {{-- Flash Message --}}
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm" role="alert">
                    <i class="fas fa-check-circle text-lg"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl shadow-sm" role="alert">
                    <div class="flex items-center gap-2 font-bold mb-1">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>Terdapat kesalahan input:</span>
                    </div>
                    <ul class="list-disc list-inside text-sm ml-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Left: Profile Card --}}
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white rounded-2xl shadow-lg p-6 text-center border border-gray-100">
                        <div class="relative inline-block mb-4">
                            <div class="w-24 h-24 bg-gradient-to-br from-primary-100 to-primary-50 rounded-full flex items-center justify-center border-4 border-white shadow-md mx-auto overflow-hidden">
                                @if(auth('member')->user()->photo)
                                    <img src="{{ asset('storage/' . auth('member')->user()->photo) }}" class="w-full h-full object-cover">
                                @else
                                    <i class="fas fa-user text-4xl text-primary-300"></i>
                                @endif
                            </div>
                        </div>
                        <h2 class="font-bold text-gray-900 text-lg">{{ auth('member')->user()->name }}</h2>
                        <p class="text-sm text-gray-500 mb-4">{{ auth('member')->user()->member_id }}</p>
                        <p class="text-xs text-gray-400 mb-2">{{ auth('member')->user()->memberType?->name ?? 'Mahasiswa' }}</p>
                        
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 text-blue-600 text-xs font-medium border border-blue-100">
                            <i class="fab fa-google"></i> Terhubung dengan Google
                        </div>
                    </div>
                </div>

                {{-- Right: Forms --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Personal Info Form --}}
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden" x-data="{ editMode: false }">
                        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                            <h3 class="font-bold text-gray-800 flex items-center gap-2"><i class="fas fa-user-edit text-primary-500"></i> Informasi Pribadi</h3>
                            <button @click="editMode = !editMode" class="text-xs font-bold text-primary-600 hover:text-primary-700 hover:bg-primary-50 px-3 py-1.5 rounded-lg transition border border-transparent hover:border-primary-100" x-text="editMode ? 'Batal Ubah' : 'Ubah Data'">Ubah Data</button>
                        </div>
                        
                        <form wire:submit="save" class="p-6 space-y-5">
                            
                            {{-- Section 1: Data Akun (Editable) --}}
                            <div class="space-y-4">
                                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 pb-2 mb-3">Data Kontak & Profil</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    {{-- Nama (Editable) --}}
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Nama Lengkap <span class="text-red-500" x-show="editMode">*</span></label>
                                        <input type="text" wire:model="name" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-50 transition disabled:opacity-60 disabled:cursor-not-allowed" :disabled="!editMode" :class="editMode ? 'bg-white' : ''">
                                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    {{-- Phone (Editable) --}}
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">No. WhatsApp / HP <span class="text-red-500" x-show="editMode">*</span></label>
                                        <input type="text" wire:model="phone" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-50 transition disabled:opacity-60 disabled:cursor-not-allowed" :disabled="!editMode" :class="editMode ? 'bg-white' : ''">
                                        @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    {{-- Gender (Editable) --}}
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Jenis Kelamin <span class="text-red-500" x-show="editMode">*</span></label>
                                        <select wire:model="gender" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-50 transition disabled:opacity-60 disabled:cursor-not-allowed" :disabled="!editMode" :class="editMode ? 'bg-white' : ''">
                                            <option value="M">Laki-laki</option>
                                            <option value="F">Perempuan</option>
                                        </select>
                                        @error('gender') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    {{-- Photo Upload (Editable) --}}
                                    <div class="md:col-span-2" x-show="editMode" x-collapse>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Update Foto Profil</label>
                                        <input type="file" wire:model="photo" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 transition border border-gray-200 rounded-xl p-1 bg-white">
                                        <p class="text-[10px] text-gray-400 mt-1">Format: JPG, PNG. Maksimal 2MB.</p>
                                        @error('photo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        <div wire:loading wire:target="photo" class="text-xs text-primary-600 mt-1">
                                            <i class="fas fa-spinner fa-spin"></i> Uploading...
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Section 2: Data Akademik (Readonly) --}}
                            <div class="space-y-4 pt-2">
                                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 pb-2 mb-3">Data Akademik (Read-only)</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                                    {{-- Email --}}
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Email Akun</label>
                                        <div class="relative">
                                            <input type="email" value="{{ auth('member')->user()->email }}" class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium text-gray-500 cursor-not-allowed bg-gray-50/50" disabled>
                                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                                        </div>
                                    </div>
                                    
                                    {{-- Prodi/Department --}}
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Program Studi</label>
                                        <input type="text" value="{{ auth('member')->user()->major ?? auth('member')->user()->department?->name ?? '-' }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium bg-gray-50/50 text-gray-500 cursor-not-allowed" disabled>
                                    </div>

                                    {{-- Fakultas --}}
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Fakultas</label>
                                        <input type="text" value="{{ auth('member')->user()->faculty?->name ?? '-' }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium bg-gray-50/50 text-gray-500 cursor-not-allowed" disabled>
                                    </div>

                                    {{-- Kampus/Branch --}}
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Kampus</label>
                                        <input type="text" value="{{ auth('member')->user()->branch?->name ?? '-' }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium bg-gray-50/50 text-gray-500 cursor-not-allowed" disabled>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Save Buttons --}}
                            <div x-show="editMode" x-collapse>
                                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 mt-2">
                                    <button type="button" @click="editMode = false" class="px-5 py-2.5 text-gray-600 font-semibold hover:bg-gray-100 rounded-xl transition text-sm">Batal</button>
                                    <button type="submit" wire:loading.attr="disabled" class="px-6 py-2.5 bg-gradient-to-r from-primary-600 to-primary-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-primary-500/30 hover:shadow-xl hover:-translate-y-0.5 transition disabled:opacity-50">
                                        <span wire:loading wire:target="save"><i class="fas fa-spinner fa-spin mr-1"></i></span>
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Danger Zone --}}
                    <div class="bg-red-50 rounded-2xl shadow-sm border border-red-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-red-100 bg-red-100/50 flex items-center justify-between">
                            <h3 class="font-bold text-red-800"><i class="fas fa-exclamation-triangle mr-2"></i> Zona Berbahaya</h3>
                        </div>
                        <div class="p-6">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm text-red-500 shrink-0">
                                    <i class="fas fa-trash-alt"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 mb-1">Hapus Akun Permanen</h4>
                                    <p class="text-sm text-gray-600 mb-4">
                                        Akun yang dihapus tidak dapat dipulihkan. Seluruh data riwayat peminjaman, cek plagiasi, dan skripsi akan terhapus dari sistem.
                                    </p>
                                    <a href="https://wa.me/6285183053934?text=Halo%20Admin%20Perpustakaan,%20saya%20ingin%20mengajukan%20penghapusan%20akun%20saya%20(NPP:%20{{ auth('member')->user()->member_id }})" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-red-200 text-red-600 text-sm font-bold rounded-xl hover:bg-red-600 hover:text-white hover:border-red-600 transition shadow-sm">
                                        <i class="fab fa-whatsapp"></i> Hubungi Admin
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
