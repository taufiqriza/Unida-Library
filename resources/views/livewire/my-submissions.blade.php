<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Submission Saya</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola pengajuan tugas akhir Anda</p>
        </div>
        <a href="{{ route('opac.member.submit-thesis') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-primary-600 text-white font-medium rounded-xl hover:bg-primary-700 transition shadow-sm">
            <i class="fas fa-plus"></i> Ajukan Baru
        </a>
    </div>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center gap-3">
            <i class="fas fa-check-circle text-emerald-500"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Status Legend --}}
    <div class="mb-4 p-4 bg-gray-50 rounded-xl">
        <p class="text-xs font-medium text-gray-500 mb-2">Keterangan Status:</p>
        <div class="flex flex-wrap gap-3 text-xs">
            <span class="flex items-center gap-1.5"><span class="w-2 h-2 bg-gray-400 rounded-full"></span> Draft</span>
            <span class="flex items-center gap-1.5"><span class="w-2 h-2 bg-blue-500 rounded-full"></span> Diajukan/Review</span>
            <span class="flex items-center gap-1.5"><span class="w-2 h-2 bg-orange-500 rounded-full"></span> Perlu Revisi</span>
            <span class="flex items-center gap-1.5"><span class="w-2 h-2 bg-emerald-500 rounded-full"></span> Disetujui</span>
            <span class="flex items-center gap-1.5"><span class="w-2 h-2 bg-primary-500 rounded-full"></span> Dipublikasikan</span>
            <span class="flex items-center gap-1.5"><span class="w-2 h-2 bg-red-500 rounded-full"></span> Ditolak</span>
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div class="flex gap-2 overflow-x-auto pb-2 mb-4 scrollbar-hide">
        @foreach([
            'all' => ['label' => 'Semua', 'icon' => 'fa-list'],
            'draft' => ['label' => 'Draft', 'icon' => 'fa-file'],
            'submitted' => ['label' => 'Diajukan', 'icon' => 'fa-paper-plane'],
            'revision_required' => ['label' => 'Perlu Revisi', 'icon' => 'fa-edit'],
            'approved' => ['label' => 'Disetujui', 'icon' => 'fa-check'],
            'rejected' => ['label' => 'Ditolak', 'icon' => 'fa-times'],
        ] as $key => $tab)
            <button 
                wire:click="setFilter('{{ $key }}')"
                @class([
                    'px-4 py-2 rounded-xl text-sm font-medium whitespace-nowrap transition flex items-center gap-2',
                    'bg-primary-600 text-white shadow-sm' => $filter === $key,
                    'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200' => $filter !== $key,
                ])
            >
                <i class="fas {{ $tab['icon'] }} text-xs"></i>
                {{ $tab['label'] }}
                @if($counts[$key] > 0)
                    <span @class([
                        'px-1.5 py-0.5 text-xs rounded-full min-w-[20px] text-center',
                        'bg-white/20' => $filter === $key,
                        'bg-gray-100' => $filter !== $key,
                    ])>{{ $counts[$key] }}</span>
                @endif
            </button>
        @endforeach
    </div>

    {{-- Submissions List --}}
    <div class="space-y-4">
        @forelse($submissions as $submission)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                {{-- Main Content --}}
                <div class="p-4 lg:p-5">
                    <div class="flex items-start gap-4">
                        {{-- Cover --}}
                        <div class="w-16 h-20 lg:w-20 lg:h-24 bg-gradient-to-br from-primary-100 to-primary-200 rounded-xl flex items-center justify-center flex-shrink-0 overflow-hidden">
                            @if($submission->cover_file)
                                <img src="{{ Storage::url($submission->cover_file) }}" alt="" class="w-full h-full object-cover">
                            @else
                                <i class="fas fa-book text-primary-400 text-2xl"></i>
                            @endif
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <h3 class="font-semibold text-gray-900 text-sm lg:text-base line-clamp-2">{{ $submission->title }}</h3>
                                    <p class="text-xs text-gray-500 mt-1 flex items-center gap-2">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 rounded text-gray-600">
                                            <i class="fas {{ $submission->getThesisTypeEnum()?->icon() ?? 'fa-file' }} text-[10px]"></i>
                                            {{ $submission->getTypeDegree() }}
                                        </span>
                                        <span>{{ $submission->department?->name ?? '-' }}</span>
                                        <span>•</span>
                                        <span>{{ $submission->year }}</span>
                                    </p>
                                </div>
                                {{-- Status Badge --}}
                                <span @class([
                                    'px-3 py-1.5 text-xs font-semibold rounded-lg flex-shrink-0 flex items-center gap-1.5',
                                    'bg-gray-100 text-gray-600' => $submission->status === 'draft',
                                    'bg-blue-100 text-blue-700' => in_array($submission->status, ['submitted', 'under_review']),
                                    'bg-orange-100 text-orange-700' => $submission->status === 'revision_required',
                                    'bg-emerald-100 text-emerald-700' => $submission->status === 'approved',
                                    'bg-primary-100 text-primary-700' => $submission->status === 'published',
                                    'bg-red-100 text-red-700' => $submission->status === 'rejected',
                                ])>
                                    @if($submission->status === 'under_review')
                                        <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
                                    @endif
                                    {{ $submission->status_label }}
                                </span>
                            </div>

                            {{-- File Status --}}
                            <div class="flex items-center gap-3 mt-3 text-xs">
                                @php $files = $submission->getFilesInfo(); @endphp
                                @foreach($files as $key => $file)
                                    <span @class([
                                        'flex items-center gap-1',
                                        'text-emerald-600' => $file['exists'],
                                        'text-gray-400' => !$file['exists'],
                                    ])>
                                        <i class="fas {{ $file['icon'] }}"></i>
                                        @if($file['exists'])
                                            <i class="fas fa-check text-[10px]"></i>
                                        @endif
                                    </span>
                                @endforeach
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center gap-2 mt-3">
                                @if($submission->canEdit())
                                    <a href="{{ route('opac.member.edit-submission', $submission->id) }}" class="px-3 py-1.5 bg-primary-100 text-primary-700 text-xs font-medium rounded-lg hover:bg-primary-200 transition inline-flex items-center gap-1.5">
                                        <i class="fas fa-edit"></i> Edit & Revisi
                                    </a>
                                @endif

                                @if($submission->isDraft())
                                    <button wire:click="deleteSubmission({{ $submission->id }})" wire:confirm="Yakin ingin menghapus draft ini?" class="px-3 py-1.5 bg-red-100 text-red-700 text-xs font-medium rounded-lg hover:bg-red-200 transition inline-flex items-center gap-1.5">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                @endif

                                @if($submission->isPublished() && $submission->ethesis_id)
                                    <a href="{{ route('opac.ethesis.show', $submission->ethesis_id) }}" class="px-3 py-1.5 bg-emerald-100 text-emerald-700 text-xs font-medium rounded-lg hover:bg-emerald-200 transition inline-flex items-center gap-1.5">
                                        <i class="fas fa-external-link-alt"></i> Lihat di E-Thesis
                                    </a>
                                @endif

                                <span class="text-xs text-gray-400 ml-auto">
                                    {{ $submission->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Review Notes / Rejection Reason --}}
                @if($submission->isRevisionRequired() && $submission->review_notes)
                    <div class="px-4 lg:px-5 pb-4 lg:pb-5">
                        <div class="p-4 bg-orange-50 border border-orange-200 rounded-xl">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-orange-600 text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-orange-800">Catatan Revisi dari Pustakawan</p>
                                    <p class="text-sm text-orange-700 mt-1 whitespace-pre-wrap">{{ $submission->review_notes }}</p>
                                    @if($submission->reviewed_at)
                                        <p class="text-xs text-orange-600 mt-2">
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ $submission->reviewed_at->format('d M Y H:i') }}
                                            @if($submission->reviewer)
                                                oleh {{ $submission->reviewer->name }}
                                            @endif
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($submission->isRejected() && $submission->rejection_reason)
                    <div class="px-4 lg:px-5 pb-4 lg:pb-5">
                        <div class="p-4 bg-red-50 border border-red-200 rounded-xl">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-times-circle text-red-600 text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-red-800">Alasan Penolakan</p>
                                    <p class="text-sm text-red-700 mt-1 whitespace-pre-wrap">{{ $submission->rejection_reason }}</p>
                                    @if($submission->reviewed_at)
                                        <p class="text-xs text-red-600 mt-2">
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ $submission->reviewed_at->format('d M Y H:i') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($submission->isApproved() && !$submission->isPublished())
                    <div class="px-4 lg:px-5 pb-4 lg:pb-5">
                        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-emerald-800">Submission Disetujui!</p>
                                    <p class="text-sm text-emerald-700 mt-1">Tugas akhir Anda telah disetujui dan akan segera dipublikasikan ke E-Thesis.</p>
                                    @if($submission->review_notes)
                                        <p class="text-sm text-emerald-600 mt-2 italic">"{{ $submission->review_notes }}"</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if(in_array($submission->status, ['submitted', 'under_review']))
                    <div class="px-4 lg:px-5 pb-4 lg:pb-5">
                        <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-hourglass-half text-blue-600 text-sm animate-pulse"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-blue-800">
                                        {{ $submission->status === 'under_review' ? 'Sedang Direview' : 'Menunggu Review' }}
                                    </p>
                                    <p class="text-sm text-blue-700 mt-1">
                                        Submission Anda sedang dalam proses verifikasi oleh pustakawan. Anda akan mendapat notifikasi jika ada update.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-inbox text-2xl text-gray-300"></i>
                </div>
                <p class="text-gray-500 font-medium">Belum ada submission</p>
                <p class="text-sm text-gray-400 mt-1">Mulai ajukan tugas akhir Anda sekarang</p>
                <a href="{{ route('opac.member.submit-thesis') }}" class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 bg-primary-600 text-white font-medium rounded-xl hover:bg-primary-700 transition">
                    <i class="fas fa-plus"></i> Ajukan Tugas Akhir
                </a>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($submissions->hasPages())
        <div class="mt-6">
            {{ $submissions->links() }}
        </div>
    @endif
</div>
