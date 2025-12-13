<div class="space-y-5">
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-500/25">
                <i class="fas fa-user-cog text-xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Staff Control</h1>
                <p class="text-sm text-gray-500">Kelola pendaftaran staff perpustakaan</p>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4">
        <div wire:click="setTab('pending')" class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm cursor-pointer hover:shadow-md transition {{ $activeTab === 'pending' ? 'ring-2 ring-amber-500' : '' }}">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-amber-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['pending'] }}</p>
                    <p class="text-xs text-gray-500">Menunggu</p>
                </div>
            </div>
        </div>
        <div wire:click="setTab('approved')" class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm cursor-pointer hover:shadow-md transition {{ $activeTab === 'approved' ? 'ring-2 ring-emerald-500' : '' }}">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-emerald-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['approved'] }}</p>
                    <p class="text-xs text-gray-500">Disetujui</p>
                </div>
            </div>
        </div>
        <div wire:click="setTab('rejected')" class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm cursor-pointer hover:shadow-md transition {{ $activeTab === 'rejected' ? 'ring-2 ring-red-500' : '' }}">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['rejected'] }}</p>
                    <p class="text-xs text-gray-500">Ditolak</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Search --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3">
        <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama atau email..."
                   class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-0 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
        </div>
    </div>

    {{-- List --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
            <h3 class="font-semibold text-gray-900">
                @if($activeTab === 'pending') Pendaftaran Menunggu Persetujuan
                @elseif($activeTab === 'approved') Staff Aktif
                @else Pendaftaran Ditolak
                @endif
            </h3>
        </div>

        @if($users->count() > 0)
        <div class="divide-y divide-gray-50">
            @foreach($users as $user)
            <div class="p-4 hover:bg-gray-50 transition">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold
                        @if($user->status === 'pending') bg-gradient-to-br from-amber-400 to-orange-500
                        @elseif($user->status === 'approved') bg-gradient-to-br from-emerald-400 to-green-500
                        @else bg-gradient-to-br from-red-400 to-rose-500 @endif">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs font-medium">
                                {{ $user->branch?->name ?? 'No Branch' }}
                            </span>
                            <span class="text-xs text-gray-400">{{ $user->created_at->format('d M Y H:i') }}</span>
                        </div>
                    </div>
                    <button wire:click="viewUser({{ $user->id }})" class="px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-600 font-medium text-sm rounded-lg transition">
                        <i class="fas fa-eye mr-1"></i> Detail
                    </button>
                </div>
            </div>
            @endforeach
        </div>

        @if($users->hasPages())
        <div class="p-4 border-t border-gray-100 bg-gray-50">{{ $users->links() }}</div>
        @endif
        @else
        <div class="p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-users text-gray-300 text-2xl"></i>
            </div>
            <p class="text-gray-500">Tidak ada data</p>
        </div>
        @endif
    </div>

    {{-- Modal --}}
    @if($showModal && $selectedUser)
    <div class="fixed inset-0 z-[99999] overflow-hidden">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="closeModal"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
                {{-- Header --}}
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-lg">Detail Pendaftaran</h3>
                        <button wire:click="closeModal" class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                {{-- Body --}}
                <div class="p-6 space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white text-xl font-bold">
                            {{ strtoupper(substr($selectedUser->name, 0, 2)) }}
                        </div>
                        <div>
                            <p class="text-xl font-bold text-gray-900">{{ $selectedUser->name }}</p>
                            <p class="text-gray-500">{{ $selectedUser->email }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-gray-500 text-xs mb-1">Cabang</p>
                            <p class="font-semibold text-gray-900">{{ $selectedUser->branch?->name ?? '-' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-gray-500 text-xs mb-1">Tanggal Daftar</p>
                            <p class="font-semibold text-gray-900">{{ $selectedUser->created_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="bg-{{ $selectedUser->status === 'pending' ? 'amber' : ($selectedUser->status === 'approved' ? 'emerald' : 'red') }}-50 rounded-xl p-4">
                        <p class="font-semibold text-{{ $selectedUser->status === 'pending' ? 'amber' : ($selectedUser->status === 'approved' ? 'emerald' : 'red') }}-800">
                            Status: {{ $selectedUser->status === 'pending' ? 'Menunggu Persetujuan' : ($selectedUser->status === 'approved' ? 'Disetujui' : 'Ditolak') }}
                        </p>
                        @if($selectedUser->rejection_reason)
                        <p class="text-sm text-red-600 mt-1">Alasan: {{ $selectedUser->rejection_reason }}</p>
                        @endif
                    </div>

                    @if($selectedUser->status === 'pending')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Penolakan (jika ditolak)</label>
                        <textarea wire:model="rejectionReason" rows="2" class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500" placeholder="Masukkan alasan jika menolak..."></textarea>
                        @error('rejectionReason') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    @endif
                </div>

                {{-- Footer --}}
                @if($selectedUser->status === 'pending')
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3">
                    <button wire:click="rejectUser" class="px-5 py-2.5 bg-red-500 hover:bg-red-600 text-white font-medium rounded-xl transition flex items-center gap-2">
                        <i class="fas fa-times"></i> Tolak
                    </button>
                    <button wire:click="approveUser" class="px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white font-medium rounded-xl shadow-lg shadow-emerald-500/25 transition flex items-center gap-2">
                        <i class="fas fa-check"></i> Setujui
                    </button>
                </div>
                @else
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end">
                    <button wire:click="closeModal" class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-xl transition">
                        Tutup
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('notify', (data) => {
                alert((data[0].type === 'success' ? '✓ ' : '⚠ ') + data[0].message);
            });
        });
    </script>
</div>
