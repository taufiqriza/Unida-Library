<div class="p-6">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Review Cek Plagiasi</h1>
            <p class="text-gray-500 text-sm">Kelola pengajuan cek plagiasi dari member</p>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
        <button wire:click="$set('statusFilter', '')" class="p-4 rounded-xl border transition {{ $statusFilter === '' ? 'bg-gray-900 text-white border-gray-900' : 'bg-white border-gray-200 hover:border-gray-300' }}">
            <p class="text-2xl font-bold">{{ $counts['all'] }}</p>
            <p class="text-xs {{ $statusFilter === '' ? 'text-gray-300' : 'text-gray-500' }}">Semua</p>
        </button>
        <button wire:click="$set('typeFilter', 'external'); $set('statusFilter', 'pending')" class="p-4 rounded-xl border transition {{ $typeFilter === 'external' && $statusFilter === 'pending' ? 'bg-orange-500 text-white border-orange-500' : 'bg-white border-gray-200 hover:border-orange-300' }}">
            <p class="text-2xl font-bold">{{ $counts['external_pending'] }}</p>
            <p class="text-xs {{ $typeFilter === 'external' ? 'text-orange-100' : 'text-gray-500' }}">Eksternal Pending</p>
        </button>
        <button wire:click="$set('statusFilter', 'pending'); $set('typeFilter', '')" class="p-4 rounded-xl border transition {{ $statusFilter === 'pending' && $typeFilter === '' ? 'bg-amber-500 text-white border-amber-500' : 'bg-white border-gray-200 hover:border-amber-300' }}">
            <p class="text-2xl font-bold">{{ $counts['pending'] }}</p>
            <p class="text-xs {{ $statusFilter === 'pending' ? 'text-amber-100' : 'text-gray-500' }}">Pending</p>
        </button>
        <button wire:click="$set('statusFilter', 'completed'); $set('typeFilter', '')" class="p-4 rounded-xl border transition {{ $statusFilter === 'completed' ? 'bg-emerald-500 text-white border-emerald-500' : 'bg-white border-gray-200 hover:border-emerald-300' }}">
            <p class="text-2xl font-bold">{{ $counts['completed'] }}</p>
            <p class="text-xs {{ $statusFilter === 'completed' ? 'text-emerald-100' : 'text-gray-500' }}">Selesai</p>
        </button>
        <button wire:click="$set('statusFilter', 'failed'); $set('typeFilter', '')" class="p-4 rounded-xl border transition {{ $statusFilter === 'failed' ? 'bg-red-500 text-white border-red-500' : 'bg-white border-gray-200 hover:border-red-300' }}">
            <p class="text-2xl font-bold">{{ $counts['failed'] }}</p>
            <p class="text-xs {{ $statusFilter === 'failed' ? 'text-red-100' : 'text-gray-500' }}">Gagal/Ditolak</p>
        </button>
    </div>

    {{-- Search --}}
    <div class="mb-4">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari judul atau nama member..." 
               class="w-full md:w-96 px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Member</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Dokumen</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600">Tipe</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600">Skor</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600">Status</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600">Tanggal</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($checks as $check)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3">
                        <p class="font-medium text-gray-900 text-sm">{{ $check->member?->name ?? '-' }}</p>
                        <p class="text-xs text-gray-500">{{ $check->member?->member_id }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <p class="text-sm text-gray-900 line-clamp-1">{{ Str::limit($check->document_title, 40) }}</p>
                        <p class="text-xs text-gray-500">{{ $check->original_filename }}</p>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($check->isExternal())
                        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-lg">
                            {{ $check->getExternalPlatformLabel() }}
                        </span>
                        @else
                        <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-lg">Sistem</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($check->similarity_score !== null)
                        <span @class([
                            'px-2 py-1 text-xs font-bold rounded-lg',
                            'bg-emerald-100 text-emerald-700' => $check->similarity_score <= 25,
                            'bg-amber-100 text-amber-700' => $check->similarity_score > 25 && $check->similarity_score <= 40,
                            'bg-red-100 text-red-700' => $check->similarity_score > 40,
                        ])>{{ number_format($check->similarity_score, 1) }}%</span>
                        @else
                        <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span @class([
                            'px-2 py-1 text-xs font-medium rounded-lg',
                            'bg-gray-100 text-gray-600' => $check->status === 'pending',
                            'bg-amber-100 text-amber-700' => $check->status === 'processing',
                            'bg-emerald-100 text-emerald-700' => $check->status === 'completed',
                            'bg-red-100 text-red-700' => $check->status === 'failed',
                        ])>
                            {{ ['pending' => 'Pending', 'processing' => 'Proses', 'completed' => 'Selesai', 'failed' => 'Gagal'][$check->status] ?? $check->status }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center text-xs text-gray-500">
                        {{ $check->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button wire:click="viewDetail({{ $check->id }})" class="p-2 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-3xl text-gray-300 mb-2"></i>
                        <p>Tidak ada data</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $checks->links() }}
    </div>

    {{-- Detail Modal --}}
    @if($showModal && $selectedCheck)
    <div class="fixed inset-0 z-50 overflow-y-auto" x-data x-init="document.body.classList.add('overflow-hidden')" x-on:close-modal.window="document.body.classList.remove('overflow-hidden')">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50" wire:click="closeModal"></div>
            
            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                {{-- Header --}}
                <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Detail Pengajuan</h3>
                    <button wire:click="closeModal" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    {{-- Status Banner --}}
                    <div @class([
                        'p-4 rounded-xl flex items-center gap-3',
                        'bg-gray-50 border border-gray-200' => $selectedCheck->status === 'pending',
                        'bg-amber-50 border border-amber-200' => $selectedCheck->status === 'processing',
                        'bg-emerald-50 border border-emerald-200' => $selectedCheck->status === 'completed',
                        'bg-red-50 border border-red-200' => $selectedCheck->status === 'failed',
                    ])>
                        <div @class([
                            'w-10 h-10 rounded-xl flex items-center justify-center',
                            'bg-gray-100 text-gray-500' => $selectedCheck->status === 'pending',
                            'bg-amber-100 text-amber-600' => $selectedCheck->status === 'processing',
                            'bg-emerald-100 text-emerald-600' => $selectedCheck->status === 'completed',
                            'bg-red-100 text-red-600' => $selectedCheck->status === 'failed',
                        ])>
                            <i class="fas {{ $selectedCheck->status === 'completed' ? 'fa-check-circle' : ($selectedCheck->status === 'failed' ? 'fa-times-circle' : 'fa-clock') }}"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ ['pending' => 'Menunggu Review', 'processing' => 'Sedang Diproses', 'completed' => 'Selesai', 'failed' => 'Gagal/Ditolak'][$selectedCheck->status] }}</p>
                            @if($selectedCheck->isExternal())
                            <p class="text-sm text-gray-600">Pengajuan Eksternal via {{ $selectedCheck->getExternalPlatformLabel() }}</p>
                            @endif
                        </div>
                        @if($selectedCheck->similarity_score !== null)
                        <div class="ml-auto text-right">
                            <p class="text-2xl font-bold {{ $selectedCheck->similarity_score <= 25 ? 'text-emerald-600' : ($selectedCheck->similarity_score <= 40 ? 'text-amber-600' : 'text-red-600') }}">
                                {{ number_format($selectedCheck->similarity_score, 1) }}%
                            </p>
                            <p class="text-xs text-gray-500">Similarity</p>
                        </div>
                        @endif
                    </div>

                    {{-- Member Info --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-xs text-gray-500 mb-1">Member</p>
                            <p class="font-semibold text-gray-900">{{ $selectedCheck->member?->name }}</p>
                            <p class="text-sm text-gray-600">{{ $selectedCheck->member?->member_id }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-xs text-gray-500 mb-1">Tanggal Submit</p>
                            <p class="font-semibold text-gray-900">{{ $selectedCheck->created_at->format('d M Y') }}</p>
                            <p class="text-sm text-gray-600">{{ $selectedCheck->created_at->format('H:i') }}</p>
                        </div>
                    </div>

                    {{-- Document Info --}}
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-xs text-gray-500 mb-1">Judul Dokumen</p>
                        <p class="font-semibold text-gray-900">{{ $selectedCheck->document_title }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ $selectedCheck->original_filename }}</p>
                    </div>

                    {{-- Files Preview --}}
                    <div class="grid grid-cols-2 gap-3">
                        @if($selectedCheck->file_path)
                        <a href="{{ Storage::url($selectedCheck->file_path) }}" target="_blank" class="flex items-center gap-3 p-3 bg-red-50 border border-red-200 rounded-xl hover:bg-red-100 transition">
                            <i class="fas fa-file-pdf text-2xl text-red-500"></i>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Dokumen</p>
                                <p class="text-xs text-gray-500">Klik untuk preview</p>
                            </div>
                        </a>
                        @endif
                        @if($selectedCheck->external_report_file)
                        <a href="{{ Storage::url($selectedCheck->external_report_file) }}" target="_blank" class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-xl hover:bg-blue-100 transition">
                            <i class="fas fa-file-image text-2xl text-blue-500"></i>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Bukti Report</p>
                                <p class="text-xs text-gray-500">Klik untuk preview</p>
                            </div>
                        </a>
                        @endif
                    </div>

                    {{-- Certificate Info --}}
                    @if($selectedCheck->hasCertificate())
                    <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-3">
                        <i class="fas fa-certificate text-emerald-500 text-xl"></i>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-emerald-800">Sertifikat Terbit</p>
                            <p class="text-xs text-emerald-600">No. {{ $selectedCheck->certificate_number }}</p>
                        </div>
                    </div>
                    @endif

                    {{-- Review Section (for pending external) --}}
                    @if($selectedCheck->isExternal() && $selectedCheck->status === 'pending')
                    <div class="border-t border-gray-200 pt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Review</label>
                        <textarea wire:model="reviewNotes" rows="2" class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500" placeholder="Tambahkan catatan (wajib untuk penolakan)"></textarea>
                        
                        <div class="flex gap-2 mt-4" x-data>
                            <button type="button" @click="confirmAction({title:'Setujui & Terbitkan Sertifikat?',text:'Sertifikat bebas plagiasi akan diterbitkan untuk member.',icon:'question',confirmText:'Ya, Setujui',confirmColor:'#10b981'},()=>$wire.approveCheck())" 
                                    class="flex-1 py-2.5 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white font-semibold rounded-xl transition flex items-center justify-center gap-2">
                                <i class="fas fa-check-circle"></i> Setujui
                            </button>
                            <button type="button" @click="confirmAction({title:'Tolak Pengajuan?',text:'Pastikan catatan review sudah diisi.',icon:'warning',confirmText:'Ya, Tolak',confirmColor:'#ef4444'},()=>$wire.rejectCheck())" 
                                    class="flex-1 py-2.5 bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white font-semibold rounded-xl transition flex items-center justify-center gap-2">
                                <i class="fas fa-times-circle"></i> Tolak
                            </button>
                        </div>
                    </div>
                    @endif

                    {{-- Review Notes (if reviewed) --}}
                    @if($selectedCheck->review_notes && $selectedCheck->reviewed_at)
                    <div class="p-3 bg-gray-50 border border-gray-200 rounded-xl">
                        <p class="text-xs text-gray-500 mb-1">Catatan Review</p>
                        <p class="text-sm text-gray-900">{{ $selectedCheck->review_notes }}</p>
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-user mr-1"></i>{{ $selectedCheck->reviewer?->name }} • {{ $selectedCheck->reviewed_at->format('d M Y H:i') }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
