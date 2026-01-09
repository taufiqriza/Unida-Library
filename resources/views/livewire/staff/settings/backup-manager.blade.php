<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-indigo-600 via-blue-600 to-purple-700 rounded-xl p-6 text-white">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h3 class="text-2xl font-bold mb-2">💾 Backup & Restore Manager</h3>
                <p class="text-blue-100">Kelola backup data sistem dengan aman dan terpercaya</p>
            </div>
            
            <button wire:click="$set('showCreateModal', true)" 
                    class="px-6 py-3 bg-white/20 hover:bg-white/30 border border-white/30 rounded-lg text-white font-semibold transition-all duration-200 backdrop-blur-sm">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Buat Backup Baru
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-4 text-white">
            <div class="text-2xl font-bold">{{ $backups->where('status', 'completed')->count() }}</div>
            <div class="text-green-100 text-sm">Backup Berhasil</div>
        </div>
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-4 text-white">
            <div class="text-2xl font-bold">{{ $backups->sum('total_records') }}</div>
            <div class="text-blue-100 text-sm">Total Records</div>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-4 text-white">
            <div class="text-2xl font-bold">{{ $backups->where('status', 'pending')->count() }}</div>
            <div class="text-purple-100 text-sm">Sedang Proses</div>
        </div>
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg p-4 text-white">
            <div class="text-2xl font-bold">{{ $backups->where('status', 'failed')->count() }}</div>
            <div class="text-orange-100 text-sm">Gagal</div>
        </div>
    </div>

    {{-- Backup List --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h4 class="text-lg font-semibold text-gray-900">Riwayat Backup</h4>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama & Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cabang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($backups as $backup)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <div class="text-sm font-medium text-gray-900">{{ $backup->name }}</div>
                                    <div class="flex items-center mt-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $backup->type === 'full' ? 'bg-red-100 text-red-800' : 
                                               ($backup->type === 'branch' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                                            {{ ucfirst($backup->type) }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $backup->branch?->name ?? 'Semua Cabang' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ number_format($backup->total_records) }} records</div>
                                <div class="text-sm text-gray-500">{{ $backup->formatted_size }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $backup->status_badge }}">
                                    {{ ucfirst($backup->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $backup->created_at->format('d/m/Y H:i') }}</div>
                                <div class="text-sm text-gray-500">{{ $backup->creator->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button wire:click="showDetail({{ $backup->id }})" 
                                        class="text-blue-600 hover:text-blue-900 transition-colors">
                                    Detail
                                </button>
                                @if($backup->status === 'completed')
                                    <button class="text-green-600 hover:text-green-900 transition-colors">
                                        Restore
                                    </button>
                                @endif
                                <button wire:click="deleteBackup({{ $backup->id }})" 
                                        onclick="return confirm('Yakin ingin menghapus backup ini?')"
                                        class="text-red-600 hover:text-red-900 transition-colors">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada backup</h3>
                                    <p class="text-gray-500">Buat backup pertama untuk mengamankan data sistem</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Create Backup Modal --}}
    @if($showCreateModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Buat Backup Baru</h3>
                    
                    <form wire:submit.prevent="createBackup" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Backup</label>
                            <input wire:model="backupName" type="text" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Contoh: Backup Harian 09-01-2026">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Backup</label>
                            <select wire:model.live="backupType" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="branch">Backup Cabang</option>
                                @if(auth()->user()->role === 'super_admin')
                                    <option value="full">Backup Lengkap</option>
                                    <option value="partial">Backup Sebagian</option>
                                @endif
                            </select>
                        </div>

                        @if($backupType === 'branch' && count($branches) > 1)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Cabang</label>
                                <select wire:model="selectedBranch" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Pilih Cabang</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi (Opsional)</label>
                            <textarea wire:model="description" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="Deskripsi backup..."></textarea>
                        </div>

                        <div class="flex justify-end space-x-3 pt-4">
                            <button type="button" wire:click="resetForm" 
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors">
                                Batal
                            </button>
                            <button type="submit" :disabled="$wire.isCreating"
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md transition-colors disabled:opacity-50">
                                <span wire:loading.remove wire:target="createBackup">Buat Backup</span>
                                <span wire:loading wire:target="createBackup">Membuat...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Detail Modal --}}
    @if($showDetailModal && $selectedBackup)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-10 mx-auto p-5 border w-2/3 max-w-4xl shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Detail Backup: {{ $selectedBackup->name }}</h3>
                        <button wire:click="$set('showDetailModal', false)" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Informasi Umum</label>
                                <div class="mt-1 bg-gray-50 rounded-lg p-3 space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Tipe:</span>
                                        <span class="text-sm font-medium">{{ ucfirst($selectedBackup->type) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Cabang:</span>
                                        <span class="text-sm font-medium">{{ $selectedBackup->branch?->name ?? 'Semua' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Status:</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $selectedBackup->status_badge }}">
                                            {{ ucfirst($selectedBackup->status) }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Ukuran:</span>
                                        <span class="text-sm font-medium">{{ $selectedBackup->formatted_size }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Data yang Dibackup</label>
                                <div class="mt-1 bg-gray-50 rounded-lg p-3 space-y-2">
                                    @foreach($selectedBackup->data_counts as $table => $count)
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">{{ ucfirst($table) }}:</span>
                                            <span class="text-sm font-medium">{{ number_format($count) }} records</span>
                                        </div>
                                    @endforeach
                                    <div class="border-t pt-2 mt-2">
                                        <div class="flex justify-between font-semibold">
                                            <span class="text-sm text-gray-900">Total:</span>
                                            <span class="text-sm text-gray-900">{{ number_format($selectedBackup->total_records) }} records</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($selectedBackup->description)
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                            <p class="mt-1 text-sm text-gray-600 bg-gray-50 rounded-lg p-3">{{ $selectedBackup->description }}</p>
                        </div>
                    @endif

                    <div class="mt-6 flex justify-end space-x-3">
                        @if($selectedBackup->status === 'completed')
                            <button class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-md transition-colors">
                                Restore Backup
                            </button>
                        @endif
                        <button wire:click="$set('showDetailModal', false)" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
