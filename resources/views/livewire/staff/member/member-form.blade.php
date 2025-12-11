<div class="space-y-5">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('staff.member.index') }}" 
               class="w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center transition">
                <i class="fas fa-arrow-left text-gray-600"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">
                    {{ $member ? 'Edit Anggota' : 'Tambah Anggota Baru' }}
                </h1>
                <p class="text-sm text-gray-500">
                    {{ $member ? $member->name : 'Isi data lengkap anggota' }}
                </p>
            </div>
        </div>

        <button wire:click="save" 
                wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-medium rounded-xl shadow-lg shadow-purple-500/25 transition disabled:opacity-50">
            <span wire:loading.remove wire:target="save"><i class="fas fa-save mr-1"></i> Simpan</span>
            <span wire:loading wire:target="save"><i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...</span>
        </button>
    </div>

    {{-- Form --}}
    <form wire:submit="save" class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Left Column - Photo --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 sticky top-5">
                <h3 class="font-bold text-gray-900 mb-4"><i class="fas fa-camera text-purple-500 mr-2"></i>Foto Anggota</h3>
                
                <div class="text-center">
                    @if($photo)
                        <img src="{{ $photo->temporaryUrl() }}" class="w-32 h-40 object-cover rounded-xl mx-auto mb-4 border-4 border-purple-100">
                    @elseif($existing_photo)
                        <img src="{{ asset('storage/' . $existing_photo) }}" class="w-32 h-40 object-cover rounded-xl mx-auto mb-4 border-4 border-purple-100">
                    @else
                        <div class="w-32 h-40 bg-gradient-to-br from-purple-100 to-pink-100 rounded-xl mx-auto mb-4 flex items-center justify-center">
                            <i class="fas fa-user text-4xl text-purple-300"></i>
                        </div>
                    @endif

                    <label class="cursor-pointer">
                        <input type="file" wire:model="photo" accept="image/*" class="hidden">
                        <span class="text-sm text-purple-600 hover:underline">
                            <i class="fas fa-upload mr-1"></i>Upload Foto
                        </span>
                    </label>
                    <p class="text-xs text-gray-400 mt-2">Max 2MB, JPG/PNG</p>
                </div>

                @error('photo') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror

                {{-- ID Preview --}}
                <div class="mt-6 p-4 bg-gray-50 rounded-xl">
                    <p class="text-xs text-gray-500 mb-1">No. Anggota</p>
                    <p class="font-mono font-bold text-lg text-gray-900">{{ $member_id }}</p>
                </div>
            </div>
        </div>

        {{-- Right Column - Form Fields --}}
        <div class="lg:col-span-2 space-y-5">
            {{-- Data Pribadi --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user text-purple-600 text-sm"></i>
                    </div>
                    Data Pribadi
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. Anggota <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="member_id" 
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 font-mono">
                        @error('member_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="name" 
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                        <select wire:model="gender" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                            <option value="">Pilih</option>
                            <option value="M">Laki-laki</option>
                            <option value="F">Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                        <input type="date" wire:model="birth_date" 
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                    </div>
                </div>
            </div>

            {{-- Kontak & Alamat --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-map-marker-alt text-blue-600 text-sm"></i>
                    </div>
                    Kontak & Alamat
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                        <textarea wire:model="address" rows="2"
                                  class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                        <input type="text" wire:model="city" 
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                        <input type="tel" wire:model="phone" 
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" wire:model="email" 
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Keanggotaan --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-id-card text-emerald-600 text-sm"></i>
                    </div>
                    Keanggotaan
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kampus/Cabang <span class="text-red-500">*</span></label>
                        <select wire:model="branch_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500" disabled>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Cabang sesuai akun Anda</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Anggota <span class="text-red-500">*</span></label>
                        <select wire:model.live="member_type_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                            <option value="">Pilih Tipe</option>
                            @foreach($memberTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                        @error('member_type_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fakultas</label>
                        <select wire:model.live="faculty_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                            <option value="">Pilih Fakultas</option>
                            @foreach($faculties as $faculty)
                                <option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Program Studi</label>
                        <select wire:model="department_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500" {{ !$faculty_id ? 'disabled' : '' }}>
                            <option value="">{{ $faculty_id ? 'Pilih Prodi' : 'Pilih fakultas dulu' }}</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Daftar <span class="text-red-500">*</span></label>
                        <input type="date" wire:model="register_date" 
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                        @error('register_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kadaluarsa <span class="text-red-500">*</span></label>
                        <input type="date" wire:model="expire_date" 
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                        @error('expire_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2 flex items-center gap-3">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="is_active" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                        </label>
                        <span class="text-sm font-medium text-gray-700">Keanggotaan Aktif</span>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                        <textarea wire:model="notes" rows="2"
                                  class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500"
                                  placeholder="Catatan tambahan..."></textarea>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
