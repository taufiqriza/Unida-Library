<div>
    {{-- Hero Header --}}
    {{-- Stats Cards --}}
    <div class="grid grid-cols-3 lg:grid-cols-6 gap-2 lg:gap-3 mb-4 lg:mb-6">
        @foreach([
            'all' => ['label' => 'Total', 'icon' => 'fa-layer-group', 'color' => 'bg-gray-100 text-gray-600', 'border' => 'border-gray-200'],
            'draft' => ['label' => 'Draft', 'icon' => 'fa-file', 'color' => 'bg-gray-50 text-gray-500', 'border' => 'border-gray-200'],
            'submitted' => ['label' => 'Diajukan', 'icon' => 'fa-paper-plane', 'color' => 'bg-blue-50 text-blue-600', 'border' => 'border-blue-200'],
            'revision_required' => ['label' => 'Revisi', 'icon' => 'fa-edit', 'color' => 'bg-orange-50 text-orange-600', 'border' => 'border-orange-200'],
            'approved' => ['label' => 'Disetujui', 'icon' => 'fa-check-circle', 'color' => 'bg-emerald-50 text-emerald-600', 'border' => 'border-emerald-200'],
            'rejected' => ['label' => 'Ditolak', 'icon' => 'fa-times-circle', 'color' => 'bg-red-50 text-red-600', 'border' => 'border-red-200'],
        ] as $key => $stat)
        <div class="bg-white rounded-xl border {{ $stat['border'] }} p-3 text-center hover:shadow-md transition cursor-pointer group" wire:click="setFilter('{{ $key }}')">
            <div class="text-xl lg:text-2xl font-bold text-gray-900 group-hover:scale-110 transition-transform">{{ $counts[$key] ?? 0 }}</div>
            <div class="text-[10px] lg:text-xs text-gray-500 flex items-center justify-center gap-1.5 mt-1">
                <i class="fas {{ $stat['icon'] }} {{ Str::after($stat['color'], ' ') }}"></i>
                <span class="hidden sm:inline">{{ $stat['label'] }}</span>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl flex items-center gap-3 animate-fade-in">
            <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-check-circle text-emerald-500"></i>
            </div>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl flex items-center gap-3">
            <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-500"></i>
            </div>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif


    {{-- Filter Tabs --}}
    <div class="bg-white rounded-xl lg:rounded-2xl shadow-sm border border-gray-100 p-1.5 lg:p-2 mb-4 lg:mb-6">
        <div class="flex gap-1 overflow-x-auto scrollbar-hide">
            @foreach([
                'all' => ['label' => 'Semua', 'icon' => 'fa-th-large'],
                'draft' => ['label' => 'Draft', 'icon' => 'fa-file'],
                'submitted' => ['label' => 'Diajukan', 'icon' => 'fa-paper-plane'],
                'revision_required' => ['label' => 'Revisi', 'icon' => 'fa-edit'],
                'approved' => ['label' => 'Disetujui', 'icon' => 'fa-check'],
                'rejected' => ['label' => 'Ditolak', 'icon' => 'fa-times'],
            ] as $key => $tab)
                <button 
                    wire:click="setFilter('{{ $key }}')"
                    @class([
                        'px-3 lg:px-4 py-2 lg:py-2.5 rounded-lg lg:rounded-xl text-xs lg:text-sm font-medium whitespace-nowrap transition-all flex items-center gap-1.5 lg:gap-2',
                        'bg-gradient-to-r from-primary-600 to-primary-700 text-white shadow-md shadow-primary-500/30' => $filter === $key,
                        'text-gray-600 hover:bg-gray-100' => $filter !== $key,
                    ])
                >
                    <i class="fas {{ $tab['icon'] }} text-[10px] lg:text-xs"></i>
                    <span class="hidden sm:inline">{{ $tab['label'] }}</span>
                    @if($counts[$key] > 0)
                        <span @class([
                            'px-1.5 lg:px-2 py-0.5 text-[10px] lg:text-xs rounded-full min-w-[18px] lg:min-w-[22px] text-center font-semibold',
                            'bg-white/20' => $filter === $key,
                            'bg-gray-200 text-gray-600' => $filter !== $key,
                        ])>{{ $counts[$key] }}</span>
                    @endif
                </button>
            @endforeach
        </div>
    </div>

    {{-- Status Legend Card - Horizontal Scroll --}}
    <div class="bg-gradient-to-r from-gray-50 to-slate-50 rounded-xl lg:rounded-2xl p-3 lg:p-4 mb-4 lg:mb-6 border border-gray-100">
        <div class="flex items-center gap-2 lg:gap-3 overflow-x-auto scrollbar-hide pb-1">
            <span class="flex-shrink-0 text-[10px] lg:text-xs font-semibold text-gray-500 flex items-center gap-1">
                <i class="fas fa-info-circle"></i> Status:
            </span>
            <div class="flex items-center gap-2 lg:gap-3 flex-nowrap">
                <span class="flex-shrink-0 flex items-center gap-1.5 px-2 lg:px-3 py-1 lg:py-1.5 bg-white rounded-lg shadow-sm text-[10px] lg:text-xs whitespace-nowrap">
                    <span class="w-2 h-2 bg-gray-400 rounded-full"></span> 
                    <span class="text-gray-600">Draft</span>
                </span>
                <span class="flex-shrink-0 flex items-center gap-1.5 px-2 lg:px-3 py-1 lg:py-1.5 bg-white rounded-lg shadow-sm text-[10px] lg:text-xs whitespace-nowrap">
                    <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span> 
                    <span class="text-gray-600">Diajukan</span>
                </span>
                <span class="flex-shrink-0 flex items-center gap-1.5 px-2 lg:px-3 py-1 lg:py-1.5 bg-white rounded-lg shadow-sm text-[10px] lg:text-xs whitespace-nowrap">
                    <span class="w-2 h-2 bg-orange-500 rounded-full"></span> 
                    <span class="text-gray-600">Revisi</span>
                </span>
                <span class="flex-shrink-0 flex items-center gap-1.5 px-2 lg:px-3 py-1 lg:py-1.5 bg-white rounded-lg shadow-sm text-[10px] lg:text-xs whitespace-nowrap">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full"></span> 
                    <span class="text-gray-600">Disetujui</span>
                </span>
                <span class="flex-shrink-0 flex items-center gap-1.5 px-2 lg:px-3 py-1 lg:py-1.5 bg-white rounded-lg shadow-sm text-[10px] lg:text-xs whitespace-nowrap">
                    <span class="w-2 h-2 bg-primary-500 rounded-full"></span> 
                    <span class="text-gray-600">Publikasi</span>
                </span>
                <span class="flex-shrink-0 flex items-center gap-1.5 px-2 lg:px-3 py-1 lg:py-1.5 bg-white rounded-lg shadow-sm text-[10px] lg:text-xs whitespace-nowrap">
                    <span class="w-2 h-2 bg-red-500 rounded-full"></span> 
                    <span class="text-gray-600">Ditolak</span>
                </span>
            </div>
        </div>
    </div>

    {{-- Submissions List --}}
    <div class="space-y-3 lg:space-y-4">
        @forelse($submissions as $submission)
            <div class="bg-white rounded-xl lg:rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md hover:border-primary-200 transition-all group">
                {{-- Main Content --}}
                <div class="p-3 lg:p-6">
                    <div class="flex items-start gap-3 lg:gap-4">
                        {{-- Cover with Status Indicator --}}
                        <div class="relative flex-shrink-0">
                            <div class="w-16 h-22 lg:w-24 lg:h-32 bg-gradient-to-br from-primary-50 via-blue-50 to-sky-50 rounded-lg lg:rounded-xl flex items-center justify-center overflow-hidden shadow-sm group-hover:shadow-md transition">
                                @if($submission->cover_file)
                                    <img src="{{ Storage::url($submission->cover_file) }}" alt="" class="w-full h-full object-cover">
                                @else
                                    <div class="text-center">
                                        <i class="fas fa-book text-primary-300 text-xl lg:text-3xl"></i>
                                    </div>
                                @endif
                            </div>
                            {{-- Status Dot --}}
                            <div @class([
                                'absolute -top-1 -right-1 w-4 h-4 lg:w-5 lg:h-5 rounded-full border-2 border-white shadow-sm flex items-center justify-center',
                                'bg-gray-400' => $submission->status === 'draft',
                                'bg-blue-500' => in_array($submission->status, ['submitted', 'under_review']),
                                'bg-orange-500' => $submission->status === 'revision_required',
                                'bg-emerald-500' => $submission->status === 'approved',
                                'bg-primary-500' => $submission->status === 'published',
                                'bg-red-500' => $submission->status === 'rejected',
                            ])>
                                @if($submission->status === 'under_review')
                                    <span class="w-1.5 h-1.5 lg:w-2 lg:h-2 bg-white rounded-full animate-ping"></span>
                                @endif
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            {{-- Title & Status --}}
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="font-bold text-gray-900 text-sm lg:text-lg line-clamp-2 group-hover:text-primary-700 transition">{{ $submission->title }}</h3>
                                {{-- Status Badge - Desktop --}}
                                <span @class([
                                    'hidden lg:flex px-3 py-1.5 text-xs font-bold rounded-xl flex-shrink-0 items-center gap-1.5 shadow-sm',
                                    'bg-gray-100 text-gray-600' => $submission->status === 'draft',
                                    'bg-blue-100 text-blue-700' => in_array($submission->status, ['submitted', 'under_review']),
                                    'bg-orange-100 text-orange-700' => $submission->status === 'revision_required',
                                    'bg-emerald-100 text-emerald-700' => $submission->status === 'approved',
                                    'bg-gradient-to-r from-primary-100 to-blue-100 text-primary-700' => $submission->status === 'published',
                                    'bg-red-100 text-red-700' => $submission->status === 'rejected',
                                ])>
                                    @if($submission->status === 'under_review')
                                        <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
                                    @elseif($submission->status === 'published')
                                        <i class="fas fa-check-double text-[10px]"></i>
                                    @endif
                                    {{ $submission->status_label }}
                                </span>
                            </div>
                            
                            {{-- Meta Info --}}
                            <div class="flex items-center gap-1.5 lg:gap-2 mt-1.5 lg:mt-2 flex-wrap">
                                {{-- Status Badge - Mobile --}}
                                <span @class([
                                    'lg:hidden px-2 py-0.5 text-[10px] font-bold rounded-lg flex-shrink-0 flex items-center gap-1',
                                    'bg-gray-100 text-gray-600' => $submission->status === 'draft',
                                    'bg-blue-100 text-blue-700' => in_array($submission->status, ['submitted', 'under_review']),
                                    'bg-orange-100 text-orange-700' => $submission->status === 'revision_required',
                                    'bg-emerald-100 text-emerald-700' => $submission->status === 'approved',
                                    'bg-primary-100 text-primary-700' => $submission->status === 'published',
                                    'bg-red-100 text-red-700' => $submission->status === 'rejected',
                                ])>
                                    {{ $submission->status_label }}
                                </span>
                                @if($submission->thesis_type)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 lg:px-2.5 lg:py-1 bg-primary-50 text-primary-700 rounded lg:rounded-lg text-[10px] lg:text-xs font-medium">
                                    <i class="fas {{ $submission->getThesisTypeEnum()?->icon() ?? 'fa-file' }} text-[8px] lg:text-[10px]"></i>
                                    {{ $submission->getTypeDegree() }}
                                </span>
                                @endif
                                <span class="text-[10px] lg:text-xs text-gray-500 truncate max-w-[100px] lg:max-w-none">{{ $submission->department?->name ?? '-' }}</span>
                                <span class="text-gray-300 hidden lg:inline">•</span>
                                <span class="text-[10px] lg:text-xs text-gray-500">{{ $submission->year }}</span>
                            </div>

                            {{-- File Status Icons --}}
                            <div class="flex items-center gap-2 lg:gap-4 mt-2 lg:mt-4">
                                @php $files = $submission->getFilesInfo(); @endphp
                                @foreach($files as $key => $file)
                                    <div @class([
                                        'flex items-center gap-1 text-[10px] lg:text-xs',
                                        'text-emerald-600' => $file['exists'],
                                        'text-gray-300' => !$file['exists'],
                                    ]) title="{{ $file['label'] ?? $key }}">
                                        <i class="fas {{ $file['icon'] }}"></i>
                                        @if($file['exists'])
                                            <i class="fas fa-check-circle text-[8px] lg:text-[10px]"></i>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center gap-1.5 lg:gap-2 mt-3 lg:mt-4 pt-3 lg:pt-4 border-t border-gray-100 flex-wrap">
                                @if($submission->canEdit())
                                    <a href="{{ route('opac.member.edit-submission', $submission->id) }}" class="px-2.5 lg:px-4 py-1.5 lg:py-2 bg-gradient-to-r from-primary-600 to-primary-700 text-white text-[10px] lg:text-xs font-semibold rounded-lg hover:from-primary-700 hover:to-primary-800 transition shadow-sm inline-flex items-center gap-1">
                                        <i class="fas fa-edit"></i> <span class="hidden sm:inline">Edit &</span> Revisi
                                    </a>
                                @endif

                                @if($submission->isDraft())
                                    <button wire:click="deleteSubmission({{ $submission->id }})" wire:confirm="Yakin ingin menghapus draft ini?" class="px-2.5 lg:px-4 py-1.5 lg:py-2 bg-red-50 text-red-600 text-[10px] lg:text-xs font-semibold rounded-lg hover:bg-red-100 transition inline-flex items-center gap-1">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                @endif

                                @if($submission->isPublished() && $submission->ethesis_id)
                                    <a href="{{ route('opac.ethesis.show', $submission->ethesis_id) }}" class="px-2.5 lg:px-4 py-1.5 lg:py-2 bg-emerald-50 text-emerald-700 text-[10px] lg:text-xs font-semibold rounded-lg hover:bg-emerald-100 transition inline-flex items-center gap-1">
                                        <i class="fas fa-external-link-alt"></i> E-Thesis
                                    </a>
                                @endif

                                <span class="text-[10px] lg:text-xs text-gray-400 ml-auto flex items-center gap-1">
                                    <i class="fas fa-clock"></i>
                                    <span class="hidden sm:inline">{{ $submission->created_at->diffForHumans() }}</span>
                                    <span class="sm:hidden">{{ $submission->created_at->shortRelativeDiffForHumans() }}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>


                {{-- Status Messages --}}
                @if($submission->isRevisionRequired() && $submission->review_notes)
                    <div class="px-3 lg:px-6 pb-3 lg:pb-6">
                        <div class="p-3 lg:p-4 bg-gradient-to-r from-orange-50 to-amber-50 border border-orange-200 rounded-lg lg:rounded-xl">
                            <div class="flex items-start gap-2 lg:gap-3">
                                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-orange-100 rounded-lg lg:rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-orange-600 text-xs lg:text-base"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs lg:text-sm font-bold text-orange-800">Catatan Revisi</p>
                                    <p class="text-xs lg:text-sm text-orange-700 mt-1 whitespace-pre-wrap">{{ $submission->review_notes }}</p>
                                    @if($submission->reviewed_at)
                                        <p class="text-[10px] lg:text-xs text-orange-600 mt-2 lg:mt-3 flex items-center gap-1 lg:gap-2 flex-wrap">
                                            <i class="fas fa-calendar-alt"></i>
                                            {{ $submission->reviewed_at->format('d M Y H:i') }}
                                            @if($submission->reviewer)
                                                <span class="px-1.5 lg:px-2 py-0.5 bg-orange-100 rounded text-[10px] lg:text-xs">{{ $submission->reviewer->name }}</span>
                                            @endif
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($submission->isRejected() && $submission->rejection_reason)
                    <div class="px-3 lg:px-6 pb-3 lg:pb-6">
                        <div class="p-3 lg:p-4 bg-gradient-to-r from-red-50 to-rose-50 border border-red-200 rounded-lg lg:rounded-xl">
                            <div class="flex items-start gap-2 lg:gap-3">
                                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-red-100 rounded-lg lg:rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-times-circle text-red-600 text-xs lg:text-base"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs lg:text-sm font-bold text-red-800">Alasan Penolakan</p>
                                    <p class="text-xs lg:text-sm text-red-700 mt-1 whitespace-pre-wrap">{{ $submission->rejection_reason }}</p>
                                    @if($submission->reviewed_at)
                                        <p class="text-[10px] lg:text-xs text-red-600 mt-2 lg:mt-3 flex items-center gap-1 lg:gap-2">
                                            <i class="fas fa-calendar-alt"></i>
                                            {{ $submission->reviewed_at->format('d M Y H:i') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($submission->isApproved() && !$submission->isPublished())
                    <div class="px-3 lg:px-6 pb-3 lg:pb-6">
                        <div class="p-3 lg:p-4 bg-gradient-to-r from-emerald-50 to-green-50 border border-emerald-200 rounded-lg lg:rounded-xl">
                            <div class="flex items-start gap-2 lg:gap-3">
                                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-emerald-100 rounded-lg lg:rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-check-circle text-emerald-600 text-xs lg:text-base"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs lg:text-sm font-bold text-emerald-800">🎉 Disetujui!</p>
                                    <p class="text-xs lg:text-sm text-emerald-700 mt-1">Akan segera dipublikasikan ke E-Thesis.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if(in_array($submission->status, ['submitted', 'under_review']))
                    <div class="px-3 lg:px-6 pb-3 lg:pb-6">
                        <div class="p-3 lg:p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg lg:rounded-xl">
                            <div class="flex items-start gap-2 lg:gap-3">
                                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-blue-100 rounded-lg lg:rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-hourglass-half text-blue-600 animate-pulse text-xs lg:text-base"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs lg:text-sm font-bold text-blue-800">
                                        {{ $submission->status === 'under_review' ? '🔍 Sedang Direview' : '📤 Menunggu Review' }}
                                    </p>
                                    <p class="text-xs lg:text-sm text-blue-700 mt-1">
                                        Sedang diverifikasi oleh pustakawan.
                                    </p>
                                    <div class="mt-2 lg:mt-3 flex items-center gap-2">
                                        <div class="flex -space-x-1">
                                            <div class="w-1.5 h-1.5 lg:w-2 lg:h-2 bg-blue-400 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                                            <div class="w-1.5 h-1.5 lg:w-2 lg:h-2 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                                            <div class="w-1.5 h-1.5 lg:w-2 lg:h-2 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                                        </div>
                                        <span class="text-[10px] lg:text-xs text-blue-600">Proses verifikasi...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            {{-- Empty State --}}
            <div class="bg-white rounded-2xl lg:rounded-3xl shadow-sm border border-gray-100 p-8 lg:p-12 text-center">
                <div class="w-16 h-16 lg:w-24 lg:h-24 bg-gradient-to-br from-primary-50 to-blue-50 rounded-2xl lg:rounded-3xl flex items-center justify-center mx-auto mb-4 lg:mb-6 shadow-inner">
                    <i class="fas fa-inbox text-2xl lg:text-4xl text-primary-300"></i>
                </div>
                <h3 class="text-lg lg:text-xl font-bold text-gray-900 mb-2">Belum Ada Submission</h3>
                <p class="text-sm lg:text-base text-gray-500 mb-4 lg:mb-6 max-w-md mx-auto">Mulai ajukan tugas akhir Anda sekarang.</p>
                <a href="{{ route('opac.member.submit-thesis') }}" class="inline-flex items-center gap-2 px-4 lg:px-6 py-2.5 lg:py-3 bg-gradient-to-r from-primary-600 to-primary-700 text-white text-sm lg:text-base font-semibold rounded-xl hover:from-primary-700 hover:to-primary-800 transition shadow-lg shadow-primary-500/30">
                    <i class="fas fa-plus"></i> Ajukan Tugas Akhir
                </a>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($submissions->hasPages())
        <div class="mt-6 lg:mt-8">
            {{ $submissions->links() }}
        </div>
    @endif

    {{-- Clearance Letters Section --}}
    @if($this->clearanceLetters->count() > 0)
    <div class="mt-8 lg:mt-10">
        <div class="flex items-center gap-2 lg:gap-3 mb-3 lg:mb-4">
            <div class="w-8 h-8 lg:w-10 lg:h-10 bg-gradient-to-br from-emerald-500 to-green-600 rounded-lg lg:rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                <i class="fas fa-certificate text-white text-sm lg:text-base"></i>
            </div>
            <div>
                <h2 class="text-base lg:text-lg font-bold text-gray-900">Surat Bebas Pustaka</h2>
                <p class="text-[10px] lg:text-xs text-gray-500">Surat keterangan bebas pinjaman</p>
            </div>
        </div>
        <div class="space-y-2 lg:space-y-3">
            @foreach($this->clearanceLetters as $letter)
                <div class="bg-gradient-to-r from-emerald-50 via-green-50 to-teal-50 rounded-xl lg:rounded-2xl shadow-sm border border-emerald-200 p-3 lg:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:shadow-md transition">
                    <div class="flex items-center gap-3 lg:gap-4">
                        <div class="w-10 h-10 lg:w-14 lg:h-14 rounded-lg lg:rounded-xl flex items-center justify-center bg-gradient-to-br from-emerald-500 to-green-600 shadow-lg shadow-emerald-500/30 flex-shrink-0">
                            <i class="fas fa-file-certificate text-white text-sm lg:text-xl"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-gray-900 text-sm lg:text-base">{{ $letter->letter_number }}</p>
                            <p class="text-xs lg:text-sm text-gray-500 mt-0.5">{{ $letter->approved_at?->format('d M Y') }}</p>
                            @if($letter->thesisSubmission)
                                <p class="text-[10px] lg:text-xs text-emerald-600 mt-1 truncate">{{ Str::limit($letter->thesisSubmission->title, 40) }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2 lg:gap-3 sm:flex-shrink-0">
                        @if($letter->file_path)
                            <a href="{{ Storage::url($letter->file_path) }}" target="_blank" class="flex-1 sm:flex-none px-3 lg:px-5 py-2 lg:py-2.5 bg-gradient-to-r from-emerald-600 to-green-600 text-white text-xs lg:text-sm font-semibold rounded-lg lg:rounded-xl hover:from-emerald-700 hover:to-green-700 transition shadow-lg shadow-emerald-500/30 inline-flex items-center justify-center gap-1.5 lg:gap-2">
                                <i class="fas fa-download"></i> Unduh
                            </a>
                        @else
                            <span class="flex-1 sm:flex-none px-3 lg:px-5 py-2 lg:py-2.5 bg-emerald-100 text-emerald-700 text-xs lg:text-sm font-semibold rounded-lg lg:rounded-xl inline-flex items-center justify-center gap-1.5 lg:gap-2">
                                <i class="fas fa-check-circle"></i> Disetujui
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
