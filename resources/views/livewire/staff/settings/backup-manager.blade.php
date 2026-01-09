<div class="space-y-6">
<div class="space-y-6">
    {{-- Modern Header with Stats --}}
    <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-700 rounded-2xl p-6 text-white shadow-xl">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-database text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold mb-1">Backup & Restore Manager</h2>
                    <p class="text-indigo-100">Kelola backup data sistem dengan aman dan terpercaya</p>
                </div>
            </div>
            
            <button wire:click="$set('showCreateModal', true)" 
                    class="px-6 py-3 bg-white/20 hover:bg-white/30 border border-white/30 rounded-xl text-white font-semibold transition-all duration-200 backdrop-blur-sm flex items-center gap-2 shadow-lg">
                <i class="fas fa-plus"></i>
                <span>Buat Backup Baru</span>
            </button>
        </div>
    </div>

    {{-- Compact Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl px-4 py-3 border-2 border-emerald-100 hover:border-emerald-200 transition-colors shadow-sm">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-green-600 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check text-white text-sm"></i>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold text-gray-900">{{ $backups->where('status', 'completed')->count() }}</p>
                    <p class="text-xs text-gray-500 uppercase">Berhasil</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl px-4 py-3 border-2 border-blue-100 hover:border-blue-200 transition-colors shadow-sm">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-database text-white text-sm"></i>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($backups->sum('total_records')) }}</p>
                    <p class="text-xs text-gray-500 uppercase">Records</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl px-4 py-3 border-2 border-amber-100 hover:border-amber-200 transition-colors shadow-sm">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-600 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-clock text-white text-sm"></i>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold text-gray-900">{{ $backups->where('status', 'pending')->count() }}</p>
                    <p class="text-xs text-gray-500 uppercase">Proses</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl px-4 py-3 border-2 border-red-100 hover:border-red-200 transition-colors shadow-sm">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-rose-600 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-times text-white text-sm"></i>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold text-gray-900">{{ $backups->where('status', 'failed')->count() }}</p>
                    <p class="text-xs text-gray-500 uppercase">Gagal</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Modern Backup List --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100">
            <div class="flex items-center justify-between">
                <h4 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-history text-gray-600"></i>
                    Riwayat Backup
                </h4>
                <div class="text-sm text-gray-500">
                    {{ $backups->count() }} backup tersimpan
                </div>
            </div>
        </div>
        
        @if($backups->count() > 0)
        <div class="divide-y divide-gray-100">
            @foreach($backups as $backup)
                <div class="p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            {{-- Backup Icon --}}
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0
                                {{ $backup->type === 'full' ? 'bg-gradient-to-br from-red-500 to-rose-600' : 
                                   ($backup->type === 'branch' ? 'bg-gradient-to-br from-blue-500 to-indigo-600' : 'bg-gradient-to-br from-green-500 to-emerald-600') }}">
                                <i class="fas {{ $backup->type === 'full' ? 'fa-globe' : ($backup->type === 'branch' ? 'fa-building' : 'fa-layer-group') }} text-white"></i>
                            </div>
                            
                            {{-- Backup Info --}}
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-1">
                                    <h5 class="font-bold text-gray-900">{{ $backup->name }}</h5>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                        {{ $backup->type === 'full' ? 'bg-red-100 text-red-700' : 
                                           ($backup->type === 'branch' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700') }}">
                                        {{ ucfirst($backup->type) }}
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $backup->status_badge }}">
                                        {{ ucfirst($backup->status) }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-4 text-sm text-gray-500">
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-building text-xs"></i>
                                        {{ $backup->branch?->name ?? 'Semua Cabang' }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-database text-xs"></i>
                                        {{ number_format($backup->total_records) }} records
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-hdd text-xs"></i>
                                        {{ $backup->formatted_size }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-user text-xs"></i>
                                        {{ $backup->creator->name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Actions --}}
                        <div class="flex items-center gap-2">
                            <div class="text-right mr-4">
                                <p class="text-sm font-medium text-gray-900">{{ $backup->created_at->format('d/m/Y') }}</p>
                                <p class="text-xs text-gray-500">{{ $backup->created_at->format('H:i') }}</p>
                            </div>
                            
                            <button wire:click="showDetail({{ $backup->id }})" 
                                    class="w-9 h-9 bg-blue-100 hover:bg-blue-200 text-blue-600 rounded-lg flex items-center justify-center transition-colors">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                            
                            @if($backup->status === 'completed')
                                <button class="w-9 h-9 bg-green-100 hover:bg-green-200 text-green-600 rounded-lg flex items-center justify-center transition-colors">
                                    <i class="fas fa-undo text-sm"></i>
                                </button>
                            @endif
                            
                            <button wire:click="deleteBackup({{ $backup->id }})" 
                                    onclick="return confirm('Yakin ingin menghapus backup ini?')"
                                    class="w-9 h-9 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg flex items-center justify-center transition-colors">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @else
        <div class="p-12 text-center">
            <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-database text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Belum ada backup</h3>
            <p class="text-gray-500 mb-4">Buat backup pertama untuk mengamankan data sistem</p>
            <button wire:click="$set('showCreateModal', true)" 
                    class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-200">
                <i class="fas fa-plus mr-2"></i>
                Buat Backup Sekarang
            </button>
        </div>
        @endif
    </div>

    {{-- Modals teleported to body for proper z-index --}}
    <template x-teleport="body">
        <div style="position: relative; z-index: 99999;">
            {{-- Modern Create Backup Modal --}}
            @if($showCreateModal)
                <div class="fixed inset-0 bg-black/60 backdrop-blur-sm overflow-y-auto h-full w-full flex items-center justify-center p-4" x-data x-transition>
                    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden" @click.stop>
                        {{-- Header --}}
                        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-indigo-600 to-purple-700 text-white">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-bold flex items-center gap-2">
                                    <i class="fas fa-plus-circle"></i>
                                    Buat Backup Baru
                                </h3>
                                <button wire:click="resetForm" class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        
                        {{-- Body --}}
                        <form wire:submit.prevent="createBackup" class="p-6 space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Backup</label>
                                <input wire:model.defer="backupName" type="text" 
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                       placeholder="Contoh: Backup Harian 09-01-2026">
                                @error('backupName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tipe Backup</label>
                                <div class="grid grid-cols-1 gap-3" x-data="{ backupType: @entangle('backupType').defer }">
                                    <label class="flex items-center p-3 border-2 rounded-xl cursor-pointer transition-all hover:bg-gray-50"
                                           :class="backupType === 'branch' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200'"
                                           @click="backupType = 'branch'">
                                        <input type="radio" value="branch" class="sr-only" x-model="backupType">
                                        <div class="w-5 h-5 rounded-full border-2 mr-3 flex items-center justify-center"
                                             :class="backupType === 'branch' ? 'border-indigo-500 bg-indigo-500' : 'border-gray-300'">
                                            <div class="w-2 h-2 bg-white rounded-full" x-show="backupType === 'branch'"></div>
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-semibold text-gray-900">Backup Cabang</div>
                                            <div class="text-sm text-gray-500">Data spesifik cabang (books, items, members)</div>
                                        </div>
                                    </label>
                                    
                                    @if(auth()->user()->role === 'super_admin')
                                        <label class="flex items-center p-3 border-2 rounded-xl cursor-pointer transition-all hover:bg-gray-50"
                                               :class="backupType === 'full' ? 'border-red-500 bg-red-50' : 'border-gray-200'"
                                               @click="backupType = 'full'">
                                            <input type="radio" value="full" class="sr-only" x-model="backupType">
                                            <div class="w-5 h-5 rounded-full border-2 mr-3 flex items-center justify-center"
                                                 :class="backupType === 'full' ? 'border-red-500 bg-red-500' : 'border-gray-300'">
                                                <div class="w-2 h-2 bg-white rounded-full" x-show="backupType === 'full'"></div>
                                            </div>
                                            <div class="flex-1">
                                                <div class="font-semibold text-gray-900">Backup Lengkap</div>
                                                <div class="text-sm text-gray-500">Semua data sistem dan cabang</div>
                                            </div>
                                        </label>
                                        
                                        <label class="flex items-center p-3 border-2 rounded-xl cursor-pointer transition-all hover:bg-gray-50"
                                               :class="backupType === 'partial' ? 'border-green-500 bg-green-50' : 'border-gray-200'"
                                               @click="backupType = 'partial'">
                                            <input type="radio" value="partial" class="sr-only" x-model="backupType">
                                            <div class="w-5 h-5 rounded-full border-2 mr-3 flex items-center justify-center"
                                                 :class="backupType === 'partial' ? 'border-green-500 bg-green-500' : 'border-gray-300'">
                                                <div class="w-2 h-2 bg-white rounded-full" x-show="backupType === 'partial'"></div>
                                            </div>
                                            <div class="flex-1">
                                                <div class="font-semibold text-gray-900">Backup Sebagian</div>
                                                <div class="text-sm text-gray-500">Data tertentu sesuai kebutuhan</div>
                                            </div>
                                        </label>
                                    @endif
                                </div>
                            </div>

                            @if($backupType === 'branch' && count($branches) > 1)
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Cabang</label>
                                    <select wire:model.defer="selectedBranch" 
                                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                                        <option value="">Pilih Cabang</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('selectedBranch') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            @endif

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi (Opsional)</label>
                                <textarea wire:model.defer="description" rows="3"
                                          class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all resize-none"
                                          placeholder="Deskripsi backup..."></textarea>
                            </div>

                            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                                <button type="button" wire:click="resetForm" 
                                        class="px-6 py-3 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
                                    Batal
                                </button>
                                <button type="submit" wire:loading.attr="disabled"
                                        class="px-6 py-3 text-sm font-semibold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 rounded-xl transition-all duration-200 disabled:opacity-50 flex items-center gap-2">
                                    <span wire:loading.remove wire:target="createBackup">
                                        <i class="fas fa-save"></i>
                                        Buat Backup
                                    </span>
                                    <span wire:loading wire:target="createBackup" class="flex items-center gap-2">
                                        <i class="fas fa-spinner fa-spin"></i>
                                        Membuat...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Modern Detail Modal --}}
            @if($showDetailModal && $selectedBackup)
                <div class="fixed inset-0 bg-black/60 backdrop-blur-sm overflow-y-auto h-full w-full flex items-center justify-center p-4" x-data x-transition>
                    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl overflow-hidden" @click.stop>
                        {{-- Header --}}
                        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-indigo-600 to-purple-700 text-white">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-bold flex items-center gap-2">
                                    <i class="fas fa-info-circle"></i>
                                    Detail Backup: {{ $selectedBackup->name }}
                                </h3>
                                <button wire:click="$set('showDetailModal', false)" class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        
                        {{-- Body --}}
                        <div class="p-6">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                {{-- General Info --}}
                                <div class="space-y-4">
                                    <div class="bg-gray-50 rounded-xl p-4">
                                        <h4 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                                            <i class="fas fa-info text-blue-600"></i>
                                            Informasi Umum
                                        </h4>
                                        <div class="space-y-3">
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600">Tipe:</span>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                                    {{ $selectedBackup->type === 'full' ? 'bg-red-100 text-red-700' : 
                                                       ($selectedBackup->type === 'branch' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700') }}">
                                                    {{ ucfirst($selectedBackup->type) }}
                                                </span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600">Cabang:</span>
                                                <span class="text-sm font-medium text-gray-900">{{ $selectedBackup->branch?->name ?? 'Semua' }}</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600">Status:</span>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $selectedBackup->status_badge }}">
                                                    {{ ucfirst($selectedBackup->status) }}
                                                </span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600">Ukuran:</span>
                                                <span class="text-sm font-medium text-gray-900">{{ $selectedBackup->formatted_size }}</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600">Dibuat:</span>
                                                <span class="text-sm font-medium text-gray-900">{{ $selectedBackup->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600">Oleh:</span>
                                                <span class="text-sm font-medium text-gray-900">{{ $selectedBackup->creator->name }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- Data Breakdown --}}
                                <div class="space-y-4">
                                    <div class="bg-gray-50 rounded-xl p-4">
                                        <h4 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                                            <i class="fas fa-database text-green-600"></i>
                                            Data yang Dibackup
                                        </h4>
                                        <div class="space-y-3">
                                            @foreach($selectedBackup->data_counts as $table => $count)
                                                <div class="flex justify-between items-center">
                                                    <span class="text-sm text-gray-600 capitalize">{{ str_replace('_', ' ', $table) }}:</span>
                                                    <span class="text-sm font-medium text-gray-900">{{ number_format($count) }} records</span>
                                                </div>
                                            @endforeach
                                            <div class="border-t border-gray-200 pt-3 mt-3">
                                                <div class="flex justify-between items-center font-semibold">
                                                    <span class="text-sm text-gray-900">Total Records:</span>
                                                    <span class="text-sm text-gray-900">{{ number_format($selectedBackup->total_records) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($selectedBackup->description)
                                <div class="mt-6">
                                    <div class="bg-blue-50 rounded-xl p-4">
                                        <h4 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
                                            <i class="fas fa-comment text-blue-600"></i>
                                            Deskripsi
                                        </h4>
                                        <p class="text-sm text-gray-700">{{ $selectedBackup->description }}</p>
                                    </div>
                                </div>
                            @endif

                            {{-- Actions --}}
                            <div class="mt-6 flex justify-end space-x-3 pt-4 border-t border-gray-100">
                                @if($selectedBackup->status === 'completed')
                                    <button class="px-6 py-3 text-sm font-semibold text-white bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 rounded-xl transition-all duration-200 flex items-center gap-2">
                                        <i class="fas fa-undo"></i>
                                        Restore Backup
                                    </button>
                                @endif
                                <button wire:click="$set('showDetailModal', false)" 
                                        class="px-6 py-3 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </template>
</div>
