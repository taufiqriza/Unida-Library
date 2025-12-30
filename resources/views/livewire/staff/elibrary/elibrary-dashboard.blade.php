<div class="space-y-5">
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-gradient-to-br from-violet-500 via-purple-500 to-fuchsia-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-violet-500/25">
                <i class="fas fa-cloud text-xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900">E-Library</h1>
                <p class="text-sm text-gray-500">Kelola koleksi digital & unggah mandiri</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if(!$isMainBranch)
            <span class="px-3 py-1.5 bg-amber-100 text-amber-700 text-xs font-medium rounded-full">
                <i class="fas fa-info-circle mr-1"></i>Aksi review terbatas
            </span>
            @endif
            @if($activeTab === 'ebook')
            <a href="{{ route('staff.elibrary.ebook.create') }}" class="px-4 py-2 bg-gradient-to-r from-violet-500 to-purple-600 text-white text-sm font-medium rounded-xl shadow hover:shadow-lg transition flex items-center gap-2">
                <i class="fas fa-plus"></i> Tambah E-Book
            </a>
            @elseif($activeTab === 'ethesis')
            <a href="{{ route('staff.elibrary.ethesis.create') }}" class="px-4 py-2 bg-gradient-to-r from-blue-500 to-cyan-600 text-white text-sm font-medium rounded-xl shadow hover:shadow-lg transition flex items-center gap-2">
                <i class="fas fa-plus"></i> Tambah E-Thesis
            </a>
            @endif
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-gradient-to-r from-violet-500 to-purple-600 rounded-xl p-4 text-white cursor-pointer hover:shadow-lg transition" wire:click="setTab('ebook')">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center"><i class="fas fa-book-open text-lg"></i></div>
                <div>
                    <p class="text-2xl font-bold">{{ $stats['ebooks'] }}</p>
                    <p class="text-xs text-violet-100">E-Book</p>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-blue-500 to-cyan-600 rounded-xl p-4 text-white cursor-pointer hover:shadow-lg transition" wire:click="setTab('ethesis')">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center"><i class="fas fa-graduation-cap text-lg"></i></div>
                <div>
                    <p class="text-2xl font-bold">{{ $stats['ethesis'] }}</p>
                    <p class="text-xs text-blue-100">E-Thesis</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm cursor-pointer hover:shadow-lg transition" wire:click="setTab('submissions')">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-lg flex items-center justify-center"><i class="fas fa-upload text-white text-lg"></i></div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['submissions_pending'] }}</p>
                    <p class="text-xs text-gray-500">Unggah Pending</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm cursor-pointer hover:shadow-lg transition" wire:click="setTab('plagiarism')">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-rose-400 to-red-500 rounded-lg flex items-center justify-center"><i class="fas fa-shield-halved text-white text-lg"></i></div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['plagiarism_pending'] }}</p>
                    <p class="text-xs text-gray-500">Cek Plagiasi</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs & Filter Pills - INLINE --}}
    <div class="flex flex-col lg:flex-row lg:items-center gap-3">
        {{-- Tabs --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-1 inline-flex flex-wrap gap-1 flex-shrink-0">
            <button wire:click="setTab('ebook')" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $activeTab === 'ebook' ? 'bg-violet-600 text-white shadow' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas fa-book-open mr-2"></i>E-Book
            </button>
            <button wire:click="setTab('ethesis')" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $activeTab === 'ethesis' ? 'bg-blue-600 text-white shadow' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas fa-graduation-cap mr-2"></i>E-Thesis
            </button>
            <button wire:click="setTab('submissions')" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $activeTab === 'submissions' ? 'bg-amber-600 text-white shadow' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas fa-upload mr-2"></i>Unggah Mandiri
            </button>
            <button wire:click="setTab('plagiarism')" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $activeTab === 'plagiarism' ? 'bg-rose-600 text-white shadow' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas fa-shield-halved mr-2"></i>Cek Plagiasi
            </button>
        </div>

        {{-- Filter Pills - Inline with Tabs --}}
        @if($activeTab === 'submissions')
        <div class="flex items-center gap-2 lg:border-l lg:border-gray-200 lg:pl-3">
            <span class="text-xs text-gray-400 font-medium hidden lg:inline">Filter:</span>
            <div class="flex flex-wrap gap-1">
                <button wire:click="setStatusFilter('')" class="px-2.5 py-1 rounded-full text-xs font-medium transition {{ !$statusFilter ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">All</button>
                <button wire:click="setStatusFilter('submitted')" class="px-2.5 py-1 rounded-full text-xs font-medium transition {{ $statusFilter === 'submitted' ? 'bg-amber-500 text-white' : 'bg-amber-50 text-amber-700 hover:bg-amber-100' }}">Diajukan ({{ $submissionStats['submitted'] }})</button>
                <button wire:click="setStatusFilter('revision_required')" class="px-2.5 py-1 rounded-full text-xs font-medium transition {{ $statusFilter === 'revision_required' ? 'bg-orange-500 text-white' : 'bg-orange-50 text-orange-700 hover:bg-orange-100' }}">Revisi ({{ $submissionStats['revision_required'] }})</button>
                <button wire:click="setStatusFilter('approved')" class="px-2.5 py-1 rounded-full text-xs font-medium transition {{ $statusFilter === 'approved' ? 'bg-emerald-500 text-white' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">Disetujui ({{ $submissionStats['approved'] }})</button>
                <button wire:click="setStatusFilter('rejected')" class="px-2.5 py-1 rounded-full text-xs font-medium transition {{ $statusFilter === 'rejected' ? 'bg-red-500 text-white' : 'bg-red-50 text-red-700 hover:bg-red-100' }}">Ditolak ({{ $submissionStats['rejected'] }})</button>
                <button wire:click="setStatusFilter('published')" class="px-2.5 py-1 rounded-full text-xs font-medium transition {{ $statusFilter === 'published' ? 'bg-blue-500 text-white' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }}">Published ({{ $submissionStats['published'] }})</button>
            </div>
        </div>
        @elseif($activeTab === 'plagiarism')
        <div class="flex items-center gap-2 lg:border-l lg:border-gray-200 lg:pl-3">
            <span class="text-xs text-gray-400 font-medium hidden lg:inline">Filter:</span>
            <div class="flex flex-wrap gap-1">
                <button wire:click="setStatusFilter('')" class="px-2.5 py-1 rounded-full text-xs font-medium transition {{ !$statusFilter ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">All</button>
                <button wire:click="setStatusFilter('pending')" class="px-2.5 py-1 rounded-full text-xs font-medium transition {{ $statusFilter === 'pending' ? 'bg-gray-500 text-white' : 'bg-gray-50 text-gray-700 hover:bg-gray-100' }}">Pending ({{ $plagiarismStats['pending'] }})</button>
                <button wire:click="setStatusFilter('processing')" class="px-2.5 py-1 rounded-full text-xs font-medium transition {{ $statusFilter === 'processing' ? 'bg-blue-500 text-white' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }}">Processing ({{ $plagiarismStats['processing'] }})</button>
                <button wire:click="setStatusFilter('completed')" class="px-2.5 py-1 rounded-full text-xs font-medium transition {{ $statusFilter === 'completed' ? 'bg-emerald-500 text-white' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">Selesai ({{ $plagiarismStats['completed'] }})</button>
                <button wire:click="setStatusFilter('failed')" class="px-2.5 py-1 rounded-full text-xs font-medium transition {{ $statusFilter === 'failed' ? 'bg-red-500 text-white' : 'bg-red-50 text-red-700 hover:bg-red-100' }}">Gagal ({{ $plagiarismStats['failed'] }})</button>
            </div>
        </div>
        @endif
    </div>

    {{-- Search --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3">
        <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari judul, nama, NIM..."
                   class="w-full pl-9 pr-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20 rounded-lg text-sm">
        </div>
    </div>

    {{-- Content --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        @if($activeTab === 'ebook')
            @include('livewire.staff.elibrary.partials.ebook-list')
        @elseif($activeTab === 'ethesis')
            @include('livewire.staff.elibrary.partials.ethesis-list')
        @elseif($activeTab === 'submissions')
            @include('livewire.staff.elibrary.partials.submissions-list')
        @elseif($activeTab === 'plagiarism')
            @include('livewire.staff.elibrary.partials.plagiarism-list')
        @endif

        @if($data->hasPages())
        <div class="p-4 border-t border-gray-100 bg-gray-50">{{ $data->links() }}</div>
        @endif
    </div>

    {{-- Modal using Alpine x-teleport --}}
    @if($showDetailModal && $selectedItem)
    <template x-teleport="body">
        <div class="elibrary-modal-overlay" style="position: fixed; inset: 0; z-index: 999999;">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" wire:click="closeModal"></div>
            
            {{-- Modal --}}
            <div class="fixed inset-0 flex items-center justify-center p-4 sm:p-6">
                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col ring-1 ring-black/5 elibrary-modal-content">
                    {{-- Header --}}
                    <div class="relative px-6 py-4 border-b border-gray-100 flex-shrink-0 bg-gradient-to-r from-slate-50 to-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg
                                    @if($selectedType === 'submission') bg-gradient-to-br from-amber-400 via-orange-500 to-red-500
                                    @else bg-gradient-to-br from-rose-400 via-pink-500 to-purple-500 @endif">
                                    <i class="fas @if($selectedType === 'submission') fa-file-lines @else fa-shield-check @endif text-white text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">
                                        @if($selectedType === 'submission') Detail Submission @else Detail Cek Plagiasi @endif
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 rounded-md text-xs font-mono">
                                            <i class="fas fa-hashtag text-gray-400 text-[10px]"></i>{{ $selectedItem->id }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <button wire:click="closeModal" class="w-10 h-10 bg-white hover:bg-red-50 border border-gray-200 hover:border-red-200 rounded-xl flex items-center justify-center transition-all group shadow-sm">
                                <i class="fas fa-xmark text-gray-400 group-hover:text-red-500 transition-colors"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="p-6 overflow-y-auto flex-1 elibrary-modal-body bg-white">
                        @if($selectedType === 'submission')
                            @include('livewire.staff.elibrary.partials.submission-detail')
                        @else
                            @include('livewire.staff.elibrary.partials.plagiarism-detail')
                        @endif
                    </div>

                    {{-- Footer Actions --}}
                    @if($canReviewThesis && $selectedType === 'submission' && in_array($selectedItem->status, ['submitted', 'under_review', 'approved']))
                    <div class="px-6 py-4 border-t border-gray-100 bg-gradient-to-r from-gray-50 via-slate-50 to-gray-50 flex-shrink-0">
                        <div class="mb-4">
                            <label class="flex items-center gap-2 text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                                <i class="fas fa-pen-to-square text-violet-500"></i> Catatan Review
                            </label>
                            <textarea wire:model="reviewNotes" rows="2" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 transition-all placeholder:text-gray-400" placeholder="Tambahkan catatan untuk mahasiswa..."></textarea>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @if($selectedItem->status === 'submitted' || $selectedItem->status === 'under_review')
                            <button @click="$dispatch('swal:confirm', {title: 'Setujui Submission?', text: 'Submission akan disetujui dan siap dipublikasikan.', icon: 'question', confirmText: 'Ya, Setujui', method: 'approveSubmission'})" class="px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-emerald-500/30 transition-all flex items-center gap-2">
                                <i class="fas fa-circle-check"></i> Setujui
                            </button>
                            <button @click="$dispatch('swal:confirm', {title: 'Minta Revisi?', text: 'Mahasiswa akan diminta untuk merevisi submission.', icon: 'warning', confirmText: 'Ya, Minta Revisi', method: 'requestRevision'})" class="px-5 py-2.5 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white text-sm font-semibold rounded-xl shadow-lg shadow-orange-500/30 transition-all flex items-center gap-2">
                                <i class="fas fa-pen"></i> Minta Revisi
                            </button>
                            <button @click="$dispatch('swal:confirm', {title: 'Tolak Submission?', text: 'Submission akan ditolak dan tidak dapat dipublikasikan.', icon: 'error', confirmText: 'Ya, Tolak', method: 'rejectSubmission'})" class="px-5 py-2.5 bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-red-500/30 transition-all flex items-center gap-2">
                                <i class="fas fa-circle-xmark"></i> Tolak
                            </button>
                            @endif
                            @if($selectedItem->status === 'approved')
                            <button @click="$dispatch('swal:confirm', {title: 'Publikasikan ke E-Thesis?', text: 'Karya akan dipublikasikan dan surat bebas pustaka akan diterbitkan otomatis.', icon: 'info', confirmText: 'Ya, Publikasikan', method: 'publishSubmission'})" class="px-5 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-blue-500/30 transition-all flex items-center gap-2">
                                <i class="fas fa-globe"></i> Publikasikan ke E-Thesis
                            </button>
                            <button wire:click="sendPublishNotification" class="px-5 py-2.5 bg-gradient-to-r from-violet-500 to-purple-600 hover:from-violet-600 hover:to-purple-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-violet-500/30 transition-all flex items-center gap-2">
                                <i class="fas fa-envelope"></i> Kirim Notifikasi
                            </button>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- Published Footer --}}
                    @if($canReviewThesis && $selectedType === 'submission' && $selectedItem->status === 'published')
                    <div class="px-6 py-4 border-t border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50 flex-shrink-0">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-check-circle text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-blue-900">Sudah Dipublikasikan</p>
                                    <p class="text-xs text-blue-600">Karya ini sudah tersedia di E-Thesis</p>
                                </div>
                            </div>
                            <button wire:click="sendPublishNotification" class="px-4 py-2 bg-white border border-blue-200 hover:bg-blue-50 text-blue-700 text-sm font-medium rounded-xl transition-all flex items-center gap-2">
                                <i class="fas fa-bell"></i> Kirim Notifikasi
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </template>
    @endif

    <style>
        .elibrary-modal-overlay {
            isolation: isolate;
        }
        .elibrary-modal-content {
            animation: elibraryModalIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes elibraryModalIn {
            from { opacity: 0; transform: scale(0.96) translateY(8px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        .elibrary-modal-body::-webkit-scrollbar { width: 5px; }
        .elibrary-modal-body::-webkit-scrollbar-track { background: transparent; }
        .elibrary-modal-body::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .elibrary-modal-body::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
    </style>

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('notify', (data) => {
                alert((data[0].type === 'success' ? '✓ ' : '⚠ ') + data[0].message);
            });
        });
    </script>
</div>
