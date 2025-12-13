<div class="space-y-5">
    {{-- Similarity Score Banner --}}
    @if($selectedItem->status === 'completed' && $selectedItem->similarity_score !== null)
    <div class="p-5 rounded-2xl text-center relative overflow-hidden
        @if($selectedItem->similarity_score <= 15) bg-gradient-to-br from-emerald-50 to-green-50 border border-emerald-200/60
        @elseif($selectedItem->similarity_score <= 25) bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200/60
        @else bg-gradient-to-br from-red-50 to-rose-50 border border-red-200/60 @endif">
        <div class="relative">
            <p class="text-6xl font-black
                @if($selectedItem->similarity_score <= 15) text-emerald-600
                @elseif($selectedItem->similarity_score <= 25) text-amber-600
                @else text-red-600 @endif">
                {{ round($selectedItem->similarity_score, 1) }}<span class="text-3xl">%</span>
            </p>
            <p class="text-sm font-semibold mt-2
                @if($selectedItem->similarity_score <= 15) text-emerald-700
                @elseif($selectedItem->similarity_score <= 25) text-amber-700
                @else text-red-700 @endif">
                {{ $selectedItem->similarity_label }}
            </p>
            @if($selectedItem->isPassed())
            <div class="inline-flex items-center gap-1.5 mt-3 px-3 py-1.5 bg-emerald-100 rounded-full">
                <i class="fas fa-circle-check text-emerald-600"></i>
                <span class="text-xs font-semibold text-emerald-700">Lolos Batas Maksimal</span>
            </div>
            @else
            <div class="inline-flex items-center gap-1.5 mt-3 px-3 py-1.5 bg-red-100 rounded-full">
                <i class="fas fa-triangle-exclamation text-red-600"></i>
                <span class="text-xs font-semibold text-red-700">Melebihi Batas Maksimal</span>
            </div>
            @endif
        </div>
    </div>
    @else
    <div class="p-5 rounded-2xl text-center
        @if($selectedItem->status === 'processing') bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200/60
        @elseif($selectedItem->status === 'failed') bg-gradient-to-br from-red-50 to-rose-50 border border-red-200/60
        @else bg-gradient-to-br from-gray-50 to-slate-50 border border-gray-200/60 @endif">
        <div class="w-14 h-14 mx-auto rounded-2xl flex items-center justify-center shadow-sm
            @if($selectedItem->status === 'processing') bg-blue-100 text-blue-600
            @elseif($selectedItem->status === 'failed') bg-red-100 text-red-600
            @else bg-gray-100 text-gray-500 @endif">
            <i class="fas 
                @if($selectedItem->status === 'processing') fa-spinner fa-spin
                @elseif($selectedItem->status === 'failed') fa-circle-xmark
                @else fa-hourglass-half @endif text-2xl"></i>
        </div>
        <p class="font-bold text-lg mt-3
            @if($selectedItem->status === 'processing') text-blue-800
            @elseif($selectedItem->status === 'failed') text-red-800
            @else text-gray-800 @endif">
            {{ $selectedItem->status_label }}
        </p>
        @if($selectedItem->error_message)
        <p class="text-sm text-red-600 mt-2 bg-red-50 rounded-lg px-3 py-2">{{ $selectedItem->error_message }}</p>
        @endif
    </div>
    @endif

    {{-- Document Info --}}
    <div class="bg-gray-50/50 rounded-xl p-4">
        <label class="flex items-center gap-2 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
            <i class="fas fa-file-lines text-violet-400"></i> Judul Dokumen
        </label>
        <p class="font-semibold text-gray-900 leading-relaxed">{{ $selectedItem->document_title }}</p>
    </div>

    {{-- File Stats --}}
    <div class="grid grid-cols-3 gap-3">
        <div class="bg-blue-50/50 rounded-xl p-3 text-center">
            <i class="fas fa-weight-hanging text-blue-500 mb-1"></i>
            <p class="text-xs text-gray-500">Ukuran</p>
            <p class="font-semibold text-blue-700">{{ $selectedItem->file_size_formatted }}</p>
        </div>
        <div class="bg-violet-50/50 rounded-xl p-3 text-center">
            <i class="fas fa-file text-violet-500 mb-1"></i>
            <p class="text-xs text-gray-500">Halaman</p>
            <p class="font-semibold text-violet-700">{{ $selectedItem->page_count ?? '-' }}</p>
        </div>
        <div class="bg-emerald-50/50 rounded-xl p-3 text-center">
            <i class="fas fa-font text-emerald-500 mb-1"></i>
            <p class="text-xs text-gray-500">Kata</p>
            <p class="font-semibold text-emerald-700">{{ $selectedItem->word_count ? number_format($selectedItem->word_count) : '-' }}</p>
        </div>
    </div>

    {{-- Provider & Time --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="flex items-center gap-3 bg-gray-50/50 rounded-xl p-3">
            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-server text-indigo-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Provider</p>
                <p class="font-semibold text-gray-900">{{ $selectedItem->provider_label }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3 bg-gray-50/50 rounded-xl p-3">
            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-stopwatch text-amber-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Waktu Proses</p>
                <p class="font-semibold text-gray-900">{{ $selectedItem->processing_time ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- Dates --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="flex items-center gap-3 bg-gray-50/50 rounded-xl p-3">
            <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-calendar-plus text-cyan-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Diajukan</p>
                <p class="font-semibold text-gray-900">{{ $selectedItem->created_at->format('d M Y, H:i') }}</p>
            </div>
        </div>
        @if($selectedItem->completed_at)
        <div class="flex items-center gap-3 bg-gray-50/50 rounded-xl p-3">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-calendar-check text-green-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Selesai</p>
                <p class="font-semibold text-gray-900">{{ $selectedItem->completed_at->format('d M Y, H:i') }}</p>
            </div>
        </div>
        @endif
    </div>

    {{-- Member Info --}}
    @if($selectedItem->member)
    <div class="bg-gradient-to-r from-slate-50 to-gray-50 rounded-xl p-4 border border-gray-100">
        <label class="flex items-center gap-2 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
            <i class="fas fa-user-circle text-gray-400"></i> Diajukan Oleh
        </label>
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-gray-200 to-gray-300 rounded-full flex items-center justify-center">
                <i class="fas fa-user text-gray-500"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-900">{{ $selectedItem->member->name }}</p>
                <p class="text-sm text-gray-500 flex items-center gap-2">
                    <span><i class="fas fa-envelope text-xs mr-1"></i>{{ $selectedItem->member->email }}</span>
                    <span class="text-gray-300">•</span>
                    <span class="font-mono">{{ $selectedItem->member->member_id }}</span>
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- Certificate --}}
    @if($selectedItem->hasCertificate())
    <div class="p-4 bg-gradient-to-r from-violet-50 to-purple-50 border border-violet-200/60 rounded-2xl">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-violet-400 to-purple-500 rounded-xl flex items-center justify-center shadow-lg shadow-violet-500/25">
                    <i class="fas fa-award text-white text-lg"></i>
                </div>
                <div>
                    <p class="font-bold text-violet-800">Sertifikat Tersedia</p>
                    <p class="text-sm text-violet-600 font-mono">{{ $selectedItem->certificate_number }}</p>
                </div>
            </div>
            <a href="{{ route('opac.member.plagiarism.certificate', $selectedItem) }}" target="_blank" class="px-4 py-2.5 bg-gradient-to-r from-violet-500 to-purple-600 hover:from-violet-600 hover:to-purple-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-violet-500/25 transition-all flex items-center gap-2">
                <i class="fas fa-download"></i> Unduh
            </a>
        </div>
    </div>
    @endif

    {{-- Similarity Sources --}}
    @if($selectedItem->similarity_sources && count($selectedItem->similarity_sources) > 0)
    <div>
        <label class="flex items-center gap-2 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
            <i class="fas fa-link text-rose-400"></i> Sumber Kesamaan
        </label>
        <div class="space-y-2 max-h-48 overflow-y-auto custom-scrollbar">
            @foreach($selectedItem->similarity_sources as $source)
            <div class="p-3 bg-gray-50 hover:bg-gray-100 rounded-xl transition flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <div class="w-8 h-8 bg-rose-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-globe text-rose-500 text-sm"></i>
                    </div>
                    <span class="text-sm text-gray-700 truncate">{{ $source['title'] ?? $source['source'] ?? 'Unknown' }}</span>
                </div>
                <span class="px-2.5 py-1 bg-rose-100 text-rose-700 rounded-lg text-sm font-bold flex-shrink-0">
                    {{ $source['percentage'] ?? $source['score'] ?? 0 }}%
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
