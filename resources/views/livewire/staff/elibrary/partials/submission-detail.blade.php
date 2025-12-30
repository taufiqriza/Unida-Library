<div class="space-y-5">
    {{-- Status Banner --}}
    <div class="p-4 rounded-2xl
        @if($selectedItem->status === 'submitted') bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200/60
        @elseif($selectedItem->status === 'approved') bg-gradient-to-r from-emerald-50 to-green-50 border border-emerald-200/60
        @elseif($selectedItem->status === 'rejected') bg-gradient-to-r from-red-50 to-rose-50 border border-red-200/60
        @elseif($selectedItem->status === 'published') bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200/60
        @elseif($selectedItem->status === 'revision_required') bg-gradient-to-r from-orange-50 to-amber-50 border border-orange-200/60
        @else bg-gradient-to-r from-gray-50 to-slate-50 border border-gray-200/60 @endif">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-sm
                @if($selectedItem->status === 'submitted') bg-amber-100 text-amber-600
                @elseif($selectedItem->status === 'approved') bg-emerald-100 text-emerald-600
                @elseif($selectedItem->status === 'rejected') bg-red-100 text-red-600
                @elseif($selectedItem->status === 'published') bg-blue-100 text-blue-600
                @elseif($selectedItem->status === 'revision_required') bg-orange-100 text-orange-600
                @else bg-gray-100 text-gray-500 @endif">
                <i class="fas 
                    @if($selectedItem->status === 'submitted') fa-hourglass-half
                    @elseif($selectedItem->status === 'approved') fa-circle-check
                    @elseif($selectedItem->status === 'rejected') fa-circle-xmark
                    @elseif($selectedItem->status === 'published') fa-globe
                    @elseif($selectedItem->status === 'revision_required') fa-pen-to-square
                    @else fa-file-lines @endif text-xl"></i>
            </div>
            <div>
                <p class="font-bold text-base
                    @if($selectedItem->status === 'submitted') text-amber-800
                    @elseif($selectedItem->status === 'approved') text-emerald-800
                    @elseif($selectedItem->status === 'rejected') text-red-800
                    @elseif($selectedItem->status === 'published') text-blue-800
                    @elseif($selectedItem->status === 'revision_required') text-orange-800
                    @else text-gray-800 @endif">
                    {{ $selectedItem->status_label }}
                </p>
                <p class="text-sm text-gray-600 flex items-center gap-1.5 mt-0.5">
                    <i class="fas fa-clock text-xs text-gray-400"></i>
                    Diajukan {{ $selectedItem->created_at->format('d M Y, H:i') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Clearance Letter Notice --}}
    @if($selectedItem->clearanceLetter && $selectedItem->clearanceLetter->status === 'approved')
    <div class="p-3 bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 rounded-xl flex items-center gap-3">
        <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
            <i class="fas fa-file-certificate text-emerald-600"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-emerald-800">Surat Bebas Pustaka Terbit</p>
            <p class="text-xs text-emerald-600">No. {{ $selectedItem->clearanceLetter->letter_number }} • Tersedia di dashboard member</p>
        </div>
        <i class="fas fa-check-circle text-emerald-500"></i>
    </div>
    @endif

    {{-- Title Section --}}
    <div class="bg-gray-50/50 rounded-xl p-4">
        <label class="flex items-center gap-2 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
            <i class="fas fa-heading text-violet-400"></i> Judul
        </label>
        <p class="font-semibold text-gray-900 leading-relaxed">{{ $selectedItem->title }}</p>
        @if($selectedItem->title_en)
        <p class="text-sm text-gray-500 italic mt-2 border-l-2 border-gray-200 pl-3">{{ $selectedItem->title_en }}</p>
        @endif
    </div>

    {{-- Author Info --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-gray-50/50 rounded-xl p-4">
            <label class="flex items-center gap-2 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                <i class="fas fa-user text-blue-400"></i> Penulis
            </label>
            <p class="font-semibold text-gray-900">{{ $selectedItem->author }}</p>
        </div>
        <div class="bg-gray-50/50 rounded-xl p-4">
            <label class="flex items-center gap-2 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                <i class="fas fa-id-card text-emerald-400"></i> NIM
            </label>
            <p class="font-mono font-semibold text-gray-900">{{ $selectedItem->nim }}</p>
        </div>
    </div>

    {{-- Academic Info --}}
    <div class="grid grid-cols-3 gap-3">
        <div class="bg-violet-50/50 rounded-xl p-3 text-center">
            <i class="fas fa-bookmark text-violet-500 mb-1"></i>
            <p class="text-xs text-gray-500">Jenis</p>
            <p class="font-semibold text-violet-700">{{ ucfirst($selectedItem->type) }}</p>
        </div>
        <div class="bg-blue-50/50 rounded-xl p-3 text-center">
            <i class="fas fa-building-columns text-blue-500 mb-1"></i>
            <p class="text-xs text-gray-500">Fakultas</p>
            <p class="font-semibold text-blue-700 text-sm">{{ $selectedItem->department?->faculty?->name ?? '-' }}</p>
        </div>
        <div class="bg-cyan-50/50 rounded-xl p-3 text-center">
            <i class="fas fa-graduation-cap text-cyan-500 mb-1"></i>
            <p class="text-xs text-gray-500">Prodi</p>
            <p class="font-semibold text-cyan-700 text-sm">{{ $selectedItem->department?->name ?? '-' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div class="flex items-center gap-3 bg-gray-50/50 rounded-xl p-3">
            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-calendar text-amber-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Tahun</p>
                <p class="font-semibold text-gray-900">{{ $selectedItem->year }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3 bg-gray-50/50 rounded-xl p-3">
            <div class="w-10 h-10 bg-rose-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-gavel text-rose-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Tanggal Sidang</p>
                <p class="font-semibold text-gray-900">{{ $selectedItem->defense_date?->format('d M Y') ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- Advisors --}}
    <div class="bg-gradient-to-r from-indigo-50/50 to-purple-50/50 rounded-xl p-4">
        <label class="flex items-center gap-2 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
            <i class="fas fa-chalkboard-user text-indigo-400"></i> Pembimbing
        </label>
        <div class="grid grid-cols-2 gap-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center text-xs font-bold text-indigo-600">1</div>
                <p class="text-gray-900 font-medium">{{ $selectedItem->advisor1 ?? '-' }}</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center text-xs font-bold text-purple-600">2</div>
                <p class="text-gray-900 font-medium">{{ $selectedItem->advisor2 ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- Abstract --}}
    @if($selectedItem->abstract)
    <div>
        <label class="flex items-center gap-2 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
            <i class="fas fa-align-left text-gray-400"></i> Abstrak
        </label>
        <p class="text-gray-700 text-sm leading-relaxed bg-gray-50/50 rounded-xl p-4">{{ Str::limit($selectedItem->abstract, 500) }}</p>
    </div>
    @endif

    {{-- Files with PDF Preview Modal --}}
    <div x-data="{ pdfPreviewUrl: null, pdfPreviewTitle: '' }">
        <label class="flex items-center gap-2 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
            <i class="fas fa-folder-open text-amber-400"></i> File Dokumen
        </label>
        <div class="grid grid-cols-2 gap-2">
            @if($selectedItem->cover_file)
            <button type="button" x-on:click="pdfPreviewUrl = '{{ route('admin.thesis.file', [$selectedItem, 'cover']) }}'; pdfPreviewTitle = 'Cover'" class="flex items-center gap-3 p-3 bg-violet-50 hover:bg-violet-100 rounded-xl transition group text-left">
                <div class="w-9 h-9 bg-violet-100 group-hover:bg-violet-200 rounded-lg flex items-center justify-center transition">
                    <i class="fas fa-image text-violet-600"></i>
                </div>
                <span class="text-sm font-medium text-violet-700">Cover</span>
                <i class="fas fa-eye text-violet-400 ml-auto text-xs"></i>
            </button>
            @endif
            @if($selectedItem->approval_file)
            <button type="button" x-on:click="pdfPreviewUrl = '{{ route('admin.thesis.file', [$selectedItem, 'approval']) }}'; pdfPreviewTitle = 'Lembar Pengesahan'" class="flex items-center gap-3 p-3 bg-blue-50 hover:bg-blue-100 rounded-xl transition group text-left">
                <div class="w-9 h-9 bg-blue-100 group-hover:bg-blue-200 rounded-lg flex items-center justify-center transition">
                    <i class="fas fa-file-signature text-blue-600"></i>
                </div>
                <span class="text-sm font-medium text-blue-700">Pengesahan</span>
                <i class="fas fa-eye text-blue-400 ml-auto text-xs"></i>
            </button>
            @endif
            @if($selectedItem->preview_file)
            <button type="button" x-on:click="pdfPreviewUrl = '{{ route('admin.thesis.file', [$selectedItem, 'preview']) }}'; pdfPreviewTitle = 'BAB 1-3 (Preview)'" class="flex items-center gap-3 p-3 bg-amber-50 hover:bg-amber-100 rounded-xl transition group text-left">
                <div class="w-9 h-9 bg-amber-100 group-hover:bg-amber-200 rounded-lg flex items-center justify-center transition">
                    <i class="fas fa-file-pdf text-amber-600"></i>
                </div>
                <span class="text-sm font-medium text-amber-700">BAB 1-3</span>
                <i class="fas fa-eye text-amber-400 ml-auto text-xs"></i>
            </button>
            @endif
            @if($selectedItem->fulltext_file)
            <button type="button" x-on:click="pdfPreviewUrl = '{{ route('admin.thesis.file', [$selectedItem, 'fulltext']) }}'; pdfPreviewTitle = 'Full Text'" class="flex items-center gap-3 p-3 bg-emerald-50 hover:bg-emerald-100 rounded-xl transition group text-left">
                <div class="w-9 h-9 bg-emerald-100 group-hover:bg-emerald-200 rounded-lg flex items-center justify-center transition">
                    <i class="fas fa-file-pdf text-emerald-600"></i>
                </div>
                <span class="text-sm font-medium text-emerald-700">Full Text</span>
                <i class="fas fa-eye text-emerald-400 ml-auto text-xs"></i>
            </button>
            @endif
        </div>

        {{-- PDF Preview Modal --}}
        <div x-show="pdfPreviewUrl" x-cloak @keydown.escape.window="pdfPreviewUrl = null" 
            class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-black/70" style="position: fixed;">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl h-[85vh] flex flex-col">
                {{-- Modal Header --}}
                <div class="flex items-center justify-between p-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900" x-text="pdfPreviewTitle"></h3>
                    <div class="flex items-center gap-2">
                        <a :href="pdfPreviewUrl" target="_blank" class="px-3 py-1.5 text-sm bg-violet-600 text-white rounded-lg hover:bg-violet-700 transition flex items-center gap-1">
                            <i class="fas fa-external-link text-xs"></i> Tab Baru
                        </a>
                        <button type="button" @click="pdfPreviewUrl = null" class="w-9 h-9 bg-gray-100 hover:bg-red-100 rounded-lg flex items-center justify-center transition">
                            <i class="fas fa-xmark text-gray-500 hover:text-red-500"></i>
                        </button>
                    </div>
                </div>
                {{-- Modal Body --}}
                <div class="flex-1 p-2 min-h-0">
                    <iframe :src="pdfPreviewUrl" class="w-full h-full rounded-lg border border-gray-200"></iframe>
                </div>
            </div>
        </div>
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

    {{-- Review Info --}}
    @if($selectedItem->reviewed_at)
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 border border-blue-100">
        <label class="flex items-center gap-2 text-xs font-semibold text-blue-600 uppercase tracking-wider mb-2">
            <i class="fas fa-clipboard-check"></i> Riwayat Review
        </label>
        <p class="text-sm text-gray-700">
            <span class="font-medium">{{ $selectedItem->reviewer?->name ?? 'Admin' }}</span> 
            <span class="text-gray-500">pada {{ $selectedItem->reviewed_at->format('d M Y, H:i') }}</span>
        </p>
        @if($selectedItem->review_notes)
        <div class="mt-2 p-3 bg-white/60 rounded-lg border-l-3 border-blue-400">
            <p class="text-sm text-gray-600 italic">"{{ $selectedItem->review_notes }}"</p>
        </div>
        @endif
    </div>
    @endif
</div>
