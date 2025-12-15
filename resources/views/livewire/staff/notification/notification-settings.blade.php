@section('title', 'Pengaturan Notifikasi')

<div class="max-w-2xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('staff.notification.index') }}" class="w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center text-gray-600 transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Pengaturan Notifikasi</h1>
            <p class="text-sm text-gray-500">Kelola preferensi notifikasi Anda</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-3">
            <i class="fas fa-check-circle text-emerald-500"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <form wire:submit="save" class="space-y-6">
        {{-- Channel Settings --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-paper-plane text-violet-600"></i>
                    Saluran Notifikasi
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">Pilih cara Anda menerima notifikasi</p>
            </div>
            
            <div class="p-5 space-y-4">
                {{-- In-App --}}
                <label class="flex items-center justify-between p-4 bg-gray-50 rounded-xl cursor-pointer hover:bg-gray-100 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-violet-100 rounded-lg flex items-center justify-center text-violet-600">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Notifikasi Dalam Aplikasi</p>
                            <p class="text-xs text-gray-500">Muncul di lonceng notifikasi portal</p>
                        </div>
                    </div>
                    <div class="relative">
                        <input type="checkbox" wire:model="channelDatabase" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-300 peer-checked:bg-violet-600 rounded-full transition-colors"></div>
                        <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow peer-checked:translate-x-5 transition-transform"></div>
                    </div>
                </label>

                {{-- Email --}}
                <label class="flex items-center justify-between p-4 bg-gray-50 rounded-xl cursor-pointer hover:bg-gray-100 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Email</p>
                            <p class="text-xs text-gray-500">Dikirim ke {{ auth()->user()->email }}</p>
                        </div>
                    </div>
                    <div class="relative">
                        <input type="checkbox" wire:model="channelEmail" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-300 peer-checked:bg-violet-600 rounded-full transition-colors"></div>
                        <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow peer-checked:translate-x-5 transition-transform"></div>
                    </div>
                </label>

                {{-- WhatsApp --}}
                <label class="flex items-center justify-between p-4 bg-gray-50 rounded-xl cursor-pointer hover:bg-gray-100 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center text-emerald-600">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">WhatsApp</p>
                            <p class="text-xs text-gray-500">Untuk notifikasi penting & mendesak</p>
                        </div>
                    </div>
                    <div class="relative">
                        <input type="checkbox" wire:model="channelWhatsapp" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-300 peer-checked:bg-violet-600 rounded-full transition-colors"></div>
                        <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow peer-checked:translate-x-5 transition-transform"></div>
                    </div>
                </label>

                {{-- Browser Push --}}
                <label class="flex items-center justify-between p-4 bg-gray-50 rounded-xl cursor-pointer hover:bg-gray-100 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center text-orange-600">
                            <i class="fas fa-desktop"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Push Browser</p>
                            <p class="text-xs text-gray-500">Notifikasi desktop meski browser tertutup</p>
                        </div>
                    </div>
                    <div class="relative">
                        <input type="checkbox" wire:model="channelPush" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-300 peer-checked:bg-violet-600 rounded-full transition-colors"></div>
                        <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow peer-checked:translate-x-5 transition-transform"></div>
                    </div>
                </label>
            </div>
        </div>

        {{-- Quiet Hours --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-moon text-indigo-600"></i>
                    Jam Tenang
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">Jeda notifikasi pada jam tertentu</p>
            </div>
            
            <div class="p-5 space-y-4">
                <label class="flex items-center justify-between p-4 bg-gray-50 rounded-xl cursor-pointer hover:bg-gray-100 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-600">
                            <i class="fas fa-bell-slash"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Aktifkan Jam Tenang</p>
                            <p class="text-xs text-gray-500">Notifikasi ditahan selama periode ini</p>
                        </div>
                    </div>
                    <div class="relative">
                        <input type="checkbox" wire:model.live="quietHoursEnabled" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-300 peer-checked:bg-violet-600 rounded-full transition-colors"></div>
                        <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow peer-checked:translate-x-5 transition-transform"></div>
                    </div>
                </label>

                @if($quietHoursEnabled)
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="text-sm font-medium text-gray-700 mb-1 block">Mulai</label>
                            <input type="time" wire:model="quietHoursStart" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500/20 focus:border-violet-400">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700 mb-1 block">Selesai</label>
                            <input type="time" wire:model="quietHoursEnd" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500/20 focus:border-violet-400">
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Digest Mode --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-clock text-amber-600"></i>
                    Mode Ringkasan
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">Gabungkan notifikasi email menjadi ringkasan</p>
            </div>
            
            <div class="p-5">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                    @foreach(['instant' => ['Langsung', 'Kirim segera'], 'hourly' => ['Per Jam', 'Ringkasan tiap jam'], 'daily' => ['Harian', 'Ringkasan pagi'], 'weekly' => ['Mingguan', 'Ringkasan Senin']] as $mode => $info)
                        <label class="relative cursor-pointer">
                            <input type="radio" wire:model="digestMode" value="{{ $mode }}" class="sr-only peer">
                            <div class="p-4 bg-gray-50 border-2 border-transparent peer-checked:border-violet-500 peer-checked:bg-violet-50 rounded-xl text-center transition hover:bg-gray-100">
                                <p class="font-semibold text-gray-900">{{ $info[0] }}</p>
                                <p class="text-[10px] text-gray-500 mt-0.5">{{ $info[1] }}</p>
                            </div>
                            <div class="absolute top-2 right-2 w-5 h-5 bg-violet-600 rounded-full items-center justify-center text-white hidden peer-checked:flex">
                                <i class="fas fa-check text-[10px]"></i>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Save Button --}}
        <button type="submit" 
                class="w-full px-6 py-4 bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 text-white font-bold rounded-xl shadow-lg shadow-violet-500/25 transition flex items-center justify-center gap-2">
            <i class="fas fa-save"></i>
            Simpan Pengaturan
        </button>
    </form>
</div>
