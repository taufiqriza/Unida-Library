<div class="space-y-5" x-data="{ showPreview: false, previewUrl: '', previewTitle: '' }">
    {{-- External Pending Review --}}
    @if($selectedItem->check_type === 'external' && $selectedItem->status === 'pending')
    <div class="p-5 bg-gradient-to-br from-violet-50 to-purple-50 border border-violet-200 rounded-2xl">
        {{-- Header --}}
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-violet-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-upload text-violet-600 text-xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-violet-800">Pengajuan Eksternal</h3>
                <p class="text-sm text-violet-600">{{ $selectedItem->member->name ?? 'Member' }} • {{ $selectedItem->created_at->diffForHumans() }}</p>
            </div>
        </div>

        {{-- Score Display --}}
        <div class="p-4 bg-white rounded-xl mb-4 text-center">
            <p class="text-4xl font-black @if($selectedItem->similarity_score <= 25) text-emerald-600 @else text-red-600 @endif">
                {{ $selectedItem->similarity_score }}<span class="text-xl">%</span>
            </p>
            <p class="text-sm text-gray-500 mt-1">Skor Similarity dari {{ ucfirst($selectedItem->external_platform) }}</p>
            @if($selectedItem->similarity_score <= 25)
            <span class="inline-flex items-center gap-1 mt-2 px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-full">
                <i class="fas fa-check-circle"></i> Di Bawah Batas (25%)
            </span>
            @else
            <span class="inline-flex items-center gap-1 mt-2 px-3 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full">
                <i class="fas fa-exclamation-triangle"></i> Melebihi Batas (25%)
            </span>
            @endif
        </div>

        {{-- Document Title --}}
        <div class="bg-white/60 rounded-xl p-3 mb-4">
            <p class="text-xs text-gray-500 mb-1">Judul Dokumen</p>
            <p class="font-semibold text-gray-900">{{ $selectedItem->document_title }}</p>
        </div>

        {{-- Preview Files --}}
        <div class="grid grid-cols-2 gap-2 mb-4">
            @if($selectedItem->file_path)
            <button @click="showPreview = true; previewUrl = '{{ Storage::url($selectedItem->file_path) }}'; previewTitle = 'Dokumen'" 
                    class="flex items-center gap-2 p-3 bg-white rounded-xl text-sm text-gray-700 hover:bg-gray-50 border transition">
                <i class="fas fa-file-pdf text-red-500"></i> 
                <span>Lihat Dokumen</span>
            </button>
            @endif
            @if($selectedItem->external_report_file)
            <button @click="showPreview = true; previewUrl = '{{ Storage::url($selectedItem->external_report_file) }}'; previewTitle = 'Laporan Plagiasi'" 
                    class="flex items-center gap-2 p-3 bg-white rounded-xl text-sm text-gray-700 hover:bg-gray-50 border transition">
                <i class="fas fa-file-alt text-blue-500"></i> 
                <span>Lihat Report</span>
            </button>
            @endif
        </div>

        {{-- Review Notes --}}
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-600 mb-1">Catatan Review (opsional)</label>
            <textarea wire:model="reviewNotes" rows="2" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500" placeholder="Tambahkan catatan jika perlu..."></textarea>
        </div>

        {{-- Action Buttons --}}
        <div class="flex gap-2">
            <button wire:click="approvePlagiarism" wire:confirm="Setujui pengajuan ini? Sertifikat akan diterbitkan." 
                    class="flex-1 px-4 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-xl transition flex items-center justify-center gap-2">
                <i class="fas fa-check"></i> Setujui & Terbitkan Sertifikat
            </button>
            <button wire:click="rejectPlagiarism" wire:confirm="Tolak pengajuan ini?" 
                    class="px-4 py-3 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    {{-- External Completed/Rejected --}}
    @elseif($selectedItem->check_type === 'external')
    <div class="p-5 rounded-2xl text-center @if($selectedItem->status === 'completed') bg-gradient-to-br from-emerald-50 to-green-50 border border-emerald-200 @else bg-gradient-to-br from-red-50 to-rose-50 border border-red-200 @endif">
        <p class="text-5xl font-black @if($selectedItem->status === 'completed') text-emerald-600 @else text-red-600 @endif">
            {{ $selectedItem->similarity_score }}<span class="text-2xl">%</span>
        </p>
        <p class="text-sm font-semibold mt-2 @if($selectedItem->status === 'completed') text-emerald-700 @else text-red-700 @endif">
            {{ ucfirst($selectedItem->external_platform) }} • {{ $selectedItem->status === 'completed' ? 'Disetujui' : 'Ditolak' }}
        </p>
    </div>

    {{-- Document Info for External --}}
    <div class="bg-gray-50 rounded-xl p-4">
        <p class="text-xs text-gray-500 mb-1">Judul Dokumen</p>
        <p class="font-semibold text-gray-900">{{ $selectedItem->document_title }}</p>
    </div>

    @if($selectedItem->review_notes)
    <div class="bg-amber-50 rounded-xl p-4 border border-amber-200">
        <p class="text-xs text-amber-600 font-medium mb-1">Catatan Review</p>
        <p class="text-sm text-amber-800">{{ $selectedItem->review_notes }}</p>
    </div>
    @endif

    {{-- System Check (non-external) --}}
    @else
    {{-- Similarity Score Banner --}}
    @if($selectedItem->status === 'completed' && $selectedItem->similarity_score !== null)
    <div class="p-5 rounded-2xl text-center @if($selectedItem->similarity_score <= 15) bg-gradient-to-br from-emerald-50 to-green-50 border border-emerald-200 @elseif($selectedItem->similarity_score <= 25) bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200 @else bg-gradient-to-br from-red-50 to-rose-50 border border-red-200 @endif">
        <p class="text-5xl font-black @if($selectedItem->similarity_score <= 15) text-emerald-600 @elseif($selectedItem->similarity_score <= 25) text-amber-600 @else text-red-600 @endif">
            {{ round($selectedItem->similarity_score, 1) }}<span class="text-2xl">%</span>
        </p>
        <p class="text-sm font-semibold mt-2 @if($selectedItem->similarity_score <= 15) text-emerald-700 @elseif($selectedItem->similarity_score <= 25) text-amber-700 @else text-red-700 @endif">
            {{ $selectedItem->similarity_label }}
        </p>
        @if($selectedItem->isPassed())
        <span class="inline-flex items-center gap-1 mt-3 px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-full">
            <i class="fas fa-check-circle"></i> Lolos
        </span>
        @endif
    </div>
    @else
    <div class="p-5 rounded-2xl text-center @if($selectedItem->status === 'processing') bg-blue-50 border border-blue-200 @elseif($selectedItem->status === 'failed') bg-red-50 border border-red-200 @else bg-gray-50 border border-gray-200 @endif">
        <div class="w-12 h-12 mx-auto rounded-xl flex items-center justify-center @if($selectedItem->status === 'processing') bg-blue-100 text-blue-600 @elseif($selectedItem->status === 'failed') bg-red-100 text-red-600 @else bg-gray-100 text-gray-500 @endif">
            <i class="fas @if($selectedItem->status === 'processing') fa-spinner fa-spin @elseif($selectedItem->status === 'failed') fa-times @else fa-clock @endif text-xl"></i>
        </div>
        <p class="font-bold mt-3">{{ $selectedItem->status_label }}</p>
        @if($selectedItem->error_message)
        <p class="text-sm text-red-600 mt-2">{{ $selectedItem->error_message }}</p>
        @endif
    </div>
    @endif

    {{-- Document Info --}}
    <div class="bg-gray-50 rounded-xl p-4">
        <p class="text-xs text-gray-500 mb-1">Judul Dokumen</p>
        <p class="font-semibold text-gray-900">{{ $selectedItem->document_title }}</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-2 text-center text-sm">
        <div class="bg-gray-50 rounded-xl p-3">
            <p class="text-gray-500 text-xs">Ukuran</p>
            <p class="font-semibold">{{ $selectedItem->file_size_formatted }}</p>
        </div>
        <div class="bg-gray-50 rounded-xl p-3">
            <p class="text-gray-500 text-xs">Provider</p>
            <p class="font-semibold">{{ $selectedItem->provider_label }}</p>
        </div>
        <div class="bg-gray-50 rounded-xl p-3">
            <p class="text-gray-500 text-xs">Waktu</p>
            <p class="font-semibold">{{ $selectedItem->processing_time ?? '-' }}</p>
        </div>
    </div>
    @endif

    {{-- Member Info --}}
    @if($selectedItem->member)
    <div class="flex items-center gap-3 bg-gray-50 rounded-xl p-3">
        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
            <i class="fas fa-user text-gray-500"></i>
        </div>
        <div class="min-w-0">
            <p class="font-semibold text-gray-900 truncate">{{ $selectedItem->member->name }}</p>
            <p class="text-xs text-gray-500">{{ $selectedItem->member->member_id }}</p>
        </div>
    </div>
    @endif

    {{-- Certificate --}}
    @if($selectedItem->hasCertificate())
    <div class="p-4 bg-violet-50 border border-violet-200 rounded-xl">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-violet-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-award text-white"></i>
                </div>
                <div>
                    <p class="font-bold text-violet-800">Sertifikat</p>
                    <p class="text-xs text-violet-600 font-mono">{{ $selectedItem->certificate_number }}</p>
                </div>
            </div>
            <a href="{{ route('opac.member.plagiarism.certificate', $selectedItem) }}" target="_blank" class="px-3 py-2 bg-violet-500 hover:bg-violet-600 text-white text-sm font-semibold rounded-lg transition">
                <i class="fas fa-download"></i>
            </a>
        </div>
    </div>
    @endif

    {{-- Preview Modal --}}
    <div x-show="showPreview" x-cloak 
         class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-black/70"
         @click.self="showPreview = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <div class="bg-white rounded-2xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden shadow-2xl">
            <div class="flex items-center justify-between p-4 border-b">
                <h3 class="font-bold text-gray-900" x-text="previewTitle"></h3>
                <button @click="showPreview = false" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition">
                    <i class="fas fa-times text-gray-500"></i>
                </button>
            </div>
            <div class="flex-1 overflow-hidden">
                <iframe :src="previewUrl" class="w-full h-full min-h-[70vh]"></iframe>
            </div>
        </div>
    </div>
</div>
