<div>
    <div class="min-h-screen bg-gray-50">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-primary-600 to-primary-800 text-white">
            <div class="max-w-5xl mx-auto px-4 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('opac.member.dashboard') }}" class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center hover:bg-white/30 transition">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div>
                            <h1 class="text-xl font-bold">Riwayat Cek Plagiasi</h1>
                            <p class="text-primary-200 text-sm">Daftar pengecekan plagiarisme dokumen Anda</p>
                        </div>
                    </div>
                    <a href="{{ route('opac.member.plagiarism.create') }}" class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-xl transition flex items-center gap-2">
                        <i class="fas fa-plus"></i>
                        <span class="hidden sm:inline">Cek Baru</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="max-w-5xl mx-auto px-4 py-6">
            {{-- Alert --}}
            @if(session('success'))
            <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800">
                <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            </div>
            @endif

            {{-- List --}}
            @if($checks->count() > 0)
            <div class="space-y-3">
                @foreach($checks as $check)
                <a href="{{ route('opac.member.plagiarism.show', $check) }}" 
                   class="block bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md hover:border-primary-300 transition">
                    <div class="flex items-start gap-4">
                        {{-- Status Icon --}}
                        <div @class([
                            'w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0',
                            'bg-gray-100 text-gray-400' => $check->isPending(),
                            'bg-amber-100 text-amber-600' => $check->isProcessing(),
                            'bg-emerald-100 text-emerald-600' => $check->isCompleted(),
                            'bg-red-100 text-red-600' => $check->isFailed(),
                        ])>
                            @if($check->isPending())
                                <i class="fas fa-clock text-lg"></i>
                            @elseif($check->isProcessing())
                                <i class="fas fa-spinner fa-spin text-lg"></i>
                            @elseif($check->isCompleted())
                                <i class="fas fa-check-circle text-lg"></i>
                            @else
                                <i class="fas fa-times-circle text-lg"></i>
                            @endif
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <h3 class="font-semibold text-gray-900 line-clamp-1">{{ $check->document_title ?: $check->original_filename }}</h3>
                                    <p class="text-sm text-gray-500 mt-0.5">{{ $check->original_filename }}</p>
                                </div>
                                <span @class([
                                    'px-2 py-1 text-xs font-medium rounded-lg flex-shrink-0',
                                    'bg-gray-100 text-gray-600' => $check->isPending(),
                                    'bg-amber-100 text-amber-700' => $check->isProcessing(),
                                    'bg-emerald-100 text-emerald-700' => $check->isCompleted(),
                                    'bg-red-100 text-red-700' => $check->isFailed(),
                                ])>
                                    {{ $check->status_label }}
                                </span>
                            </div>

                            <div class="flex items-center gap-4 mt-2 text-xs text-gray-500">
                                <span>
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ $check->created_at->format('d M Y, H:i') }}
                                </span>
                                <span>
                                    <i class="fas fa-file mr-1"></i>
                                    {{ $check->file_size_formatted }}
                                </span>
                                @if($check->isCompleted() && $check->similarity_score !== null)
                                <span @class([
                                    'font-medium',
                                    'text-emerald-600' => $check->similarity_level === 'low',
                                    'text-amber-600' => $check->similarity_level === 'moderate',
                                    'text-red-600' => in_array($check->similarity_level, ['high', 'critical']),
                                ])>
                                    <i class="fas fa-percentage mr-1"></i>
                                    {{ number_format($check->similarity_score, 1) }}% similarity
                                </span>
                                @endif
                            </div>
                        </div>

                        <i class="fas fa-chevron-right text-gray-300 flex-shrink-0"></i>
                    </div>
                </a>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $checks->links() }}
            </div>
            @else
            {{-- Empty State --}}
            <div class="text-center py-12">
                <div class="w-20 h-20 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shield-alt text-3xl text-primary-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Pengecekan</h3>
                <p class="text-gray-500 mb-6">Mulai cek plagiarisme dokumen Anda sekarang</p>
                <a href="{{ route('opac.member.plagiarism.create') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-700 text-white font-medium rounded-xl hover:from-primary-600 hover:to-primary-800 transition shadow-lg shadow-primary-500/30">
                    <i class="fas fa-plus"></i>
                    Cek Plagiasi Sekarang
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
