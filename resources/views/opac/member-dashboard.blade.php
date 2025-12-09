<x-opac.layout title="Dashboard Anggota">
    <div class="min-h-screen bg-gray-50 lg:bg-transparent">
        {{-- Mobile Header --}}
        <div class="lg:hidden sticky top-0 z-50 bg-gradient-to-r from-primary-600 to-primary-800 safe-area-top">
            <div class="flex items-center justify-between px-4 py-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                        @if($member->photo)
                            <img src="{{ asset('storage/' . $member->photo) }}" alt="{{ $member->name }}" class="w-full h-full object-cover rounded-full">
                        @else
                            <i class="fas fa-user text-white/80"></i>
                        @endif
                    </div>
                    <div>
                        <p class="text-white font-semibold text-sm">{{ Str::limit($member->name, 20) }}</p>
                        <p class="text-primary-200 text-xs">{{ $member->member_id }}</p>
                    </div>
                </div>
                <a href="{{ route('opac.logout') }}" class="w-9 h-9 bg-white/10 text-white rounded-full flex items-center justify-center">
                    <i class="fas fa-sign-out-alt text-sm"></i>
                </a>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-3 sm:px-4 py-3 lg:py-8">
            {{-- Desktop Profile Header --}}
            <div class="hidden lg:block bg-gradient-to-r from-primary-600 to-primary-800 rounded-2xl p-6 mb-6 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="relative flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center">
                            @if($member->photo)
                                <img src="{{ asset('storage/' . $member->photo) }}" alt="{{ $member->name }}" class="w-full h-full object-cover rounded-xl">
                            @else
                                <i class="fas fa-user text-2xl text-white/80"></i>
                            @endif
                        </div>
                        <div>
                            <h1 class="text-xl font-bold">{{ $member->name }}</h1>
                            <p class="text-primary-200 text-sm">{{ $member->member_id }} • {{ $member->memberType?->name ?? 'Umum' }}</p>
                            <p class="text-primary-300 text-xs mt-1">Berlaku s/d {{ $member->expire_date?->format('d M Y') ?? '-' }}</p>
                        </div>
                    </div>
                    <a href="{{ route('opac.logout') }}" class="px-4 py-2 bg-white/10 hover:bg-white/20 rounded-xl transition flex items-center gap-2">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="text-sm font-medium">Keluar</span>
                    </a>
                </div>
            </div>

            {{-- Stats Cards --}}
            <div class="grid grid-cols-4 gap-2 lg:gap-4 mb-4">
                <div class="bg-white rounded-xl p-3 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-2">
                        <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-book-reader text-blue-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-xl font-bold text-gray-900">{{ $loans->count() }}</p>
                            <p class="text-[10px] text-gray-500">Dipinjam</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-3 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-2">
                        <div class="w-9 h-9 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-orange-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-xl font-bold text-gray-900">{{ $loans->where('due_date', '<', now())->count() }}</p>
                            <p class="text-[10px] text-gray-500">Terlambat</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-3 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-2">
                        <div class="w-9 h-9 bg-violet-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-file-alt text-violet-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-xl font-bold text-gray-900">{{ $submissions->count() }}</p>
                            <p class="text-[10px] text-gray-500">Submission</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-3 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-2">
                        <div class="w-9 h-9 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-coins text-red-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-xl font-bold text-gray-900">{{ $fines->count() }}</p>
                            <p class="text-[10px] text-gray-500">Denda</p>
                        </div>
                    </div>
                </div>
            </div>


            {{-- Main Content Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
                
                {{-- Left Column: Peminjaman Aktif + Submissions --}}
                <div class="lg:col-span-2 space-y-4">
                    
                    {{-- Surat Bebas Pustaka (jika ada) --}}
                    @if(isset($clearanceLetters) && $clearanceLetters->where('status', 'approved')->count() > 0)
                    <div class="bg-gradient-to-br from-emerald-50 to-green-50 rounded-2xl shadow-sm border border-emerald-200/50 overflow-hidden">
                        <div class="p-4 border-b border-emerald-200/50">
                            <h2 class="font-bold text-emerald-800 text-sm flex items-center gap-2">
                                <div class="w-7 h-7 bg-emerald-500 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-certificate text-white text-xs"></i>
                                </div>
                                Surat Bebas Pustaka
                            </h2>
                        </div>
                        <div class="p-3 space-y-2">
                            @foreach($clearanceLetters->where('status', 'approved') as $letter)
                            <div class="p-3 bg-white rounded-xl border border-emerald-100 flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $letter->letter_number }}</p>
                                    <p class="text-xs text-gray-500">{{ $letter->approved_at?->format('d M Y') }}</p>
                                </div>
                                @if($letter->file_path)
                                <a href="{{ Storage::url($letter->file_path) }}" target="_blank" class="px-3 py-1.5 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700 transition">
                                    <i class="fas fa-download mr-1"></i> Unduh
                                </a>
                                @else
                                <span class="px-3 py-1.5 bg-emerald-100 text-emerald-700 text-xs font-medium rounded-lg">
                                    <i class="fas fa-check mr-1"></i> Disetujui
                                </span>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Unggah Tugas Akhir CTA --}}
                    <a href="{{ route('opac.member.submit-thesis') }}" class="block bg-gradient-to-br from-violet-500 to-purple-600 rounded-2xl p-4 lg:p-5 text-white shadow-lg shadow-violet-500/30 hover:shadow-xl hover:shadow-violet-500/40 transition-all group">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0 backdrop-blur-sm group-hover:scale-110 transition-transform">
                                <i class="fas fa-upload text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold text-sm lg:text-base">Unggah Tugas Akhir</h3>
                                <p class="text-violet-200 text-xs lg:text-sm">Submit skripsi/tesis/disertasi</p>
                            </div>
                            <i class="fas fa-chevron-right text-white/60 group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </a>

                    {{-- Peminjaman Aktif --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                            <h2 class="font-bold text-gray-900 text-sm flex items-center gap-2">
                                <div class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-book-reader text-blue-600 text-xs"></i>
                                </div>
                                Peminjaman Aktif
                            </h2>
                            <span class="text-xs text-gray-500">{{ $loans->count() }} buku</span>
                        </div>
                        
                        @if($loans->count() > 0)
                            <div class="divide-y divide-gray-50">
                                @foreach($loans as $loan)
                                <div class="p-4 flex items-center gap-3">
                                    <div class="w-12 h-16 bg-gray-100 rounded-lg flex-shrink-0 overflow-hidden">
                                        @if($loan->item->book->cover_url)
                                            <img src="{{ $loan->item->book->cover_url }}" alt="{{ $loan->item->book->title }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <i class="fas fa-book text-gray-400 text-xs"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 line-clamp-1">{{ $loan->item->book->title }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $loan->item->book->author_names }}</p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-xs text-gray-400">Kembali:</span>
                                            <span class="text-xs font-medium {{ $loan->due_date < now() ? 'text-red-600' : 'text-gray-600' }}">
                                                {{ $loan->due_date->format('d M Y') }}
                                            </span>
                                            @if($loan->due_date < now())
                                                <span class="px-1.5 py-0.5 bg-red-100 text-red-600 text-[10px] font-medium rounded">
                                                    Terlambat
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-8 text-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-book text-gray-400 text-xl"></i>
                                </div>
                                <p class="text-sm text-gray-500">Tidak ada peminjaman aktif</p>
                            </div>
                        @endif
                    </div>


                    {{-- Submission Tugas Akhir --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-4 border-b border-gray-100">
                            <div class="flex items-center justify-between mb-3">
                                <h2 class="font-bold text-gray-900 text-sm flex items-center gap-2">
                                    <div class="w-7 h-7 bg-violet-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-file-alt text-violet-600 text-xs"></i>
                                    </div>
                                    Pengajuan Tugas Akhir
                                </h2>
                                <a href="{{ route('opac.member.submissions') }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium">
                                    Lihat Semua <i class="fas fa-chevron-right ml-1 text-[10px]"></i>
                                </a>
                            </div>
                            {{-- Status Legend --}}
                            <div class="flex flex-wrap gap-2 text-[10px]">
                                <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span> Draft</span>
                                <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span> Diajukan</span>
                                <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 bg-orange-500 rounded-full"></span> Revisi</span>
                                <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Disetujui</span>
                                <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 bg-primary-500 rounded-full"></span> Publikasi</span>
                                <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span> Ditolak</span>
                            </div>
                        </div>
                        
                        @if($submissions->count() > 0)
                            <div class="divide-y divide-gray-50">
                                @foreach($submissions->take(5) as $sub)
                                <div class="p-4 hover:bg-gray-50/50 transition">
                                    <div class="flex items-start gap-3">
                                        {{-- Cover --}}
                                        <div class="w-12 h-16 bg-gradient-to-br from-primary-100 to-primary-200 rounded-lg flex items-center justify-center flex-shrink-0 overflow-hidden">
                                            @if($sub->cover_file)
                                                <img src="{{ Storage::url($sub->cover_file) }}" alt="" class="w-full h-full object-cover">
                                            @else
                                                <i class="fas fa-book text-primary-400 text-sm"></i>
                                            @endif
                                        </div>
                                        
                                        {{-- Content --}}
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-start justify-between gap-2">
                                                <p class="text-sm font-medium text-gray-900 line-clamp-2">{{ $sub->title }}</p>
                                                {{-- Status Badge --}}
                                                <span @class([
                                                    'px-2 py-1 text-[10px] font-semibold rounded-lg flex-shrink-0 flex items-center gap-1',
                                                    'bg-gray-100 text-gray-600' => $sub->status === 'draft',
                                                    'bg-blue-100 text-blue-700' => in_array($sub->status, ['submitted', 'under_review']),
                                                    'bg-orange-100 text-orange-700' => $sub->status === 'revision_required',
                                                    'bg-emerald-100 text-emerald-700' => $sub->status === 'approved',
                                                    'bg-primary-100 text-primary-700' => $sub->status === 'published',
                                                    'bg-red-100 text-red-700' => $sub->status === 'rejected',
                                                ])>
                                                    @if($sub->status === 'under_review')
                                                        <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></span>
                                                    @endif
                                                    {{ $sub->status_label ?? ucfirst(str_replace('_', ' ', $sub->status)) }}
                                                </span>
                                            </div>
                                            
                                            {{-- Meta Info --}}
                                            <p class="text-[10px] text-gray-500 mt-1 flex items-center gap-2 flex-wrap">
                                                @if($sub->thesis_type)
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-gray-100 rounded">
                                                    <i class="fas {{ $sub->getThesisTypeEnum()?->icon() ?? 'fa-file' }} text-[8px]"></i>
                                                    {{ $sub->getTypeDegree() }}
                                                </span>
                                                @endif
                                                <span>{{ $sub->department?->name ?? '-' }}</span>
                                                <span>•</span>
                                                <span>{{ $sub->created_at->diffForHumans() }}</span>
                                            </p>

                                            {{-- File Status --}}
                                            @if(method_exists($sub, 'getFilesInfo'))
                                            <div class="flex items-center gap-2 mt-2 text-[10px]">
                                                @php $files = $sub->getFilesInfo(); @endphp
                                                @foreach($files as $key => $file)
                                                    <span @class([
                                                        'flex items-center gap-0.5',
                                                        'text-emerald-600' => $file['exists'],
                                                        'text-gray-300' => !$file['exists'],
                                                    ]) title="{{ $file['label'] ?? $key }}">
                                                        <i class="fas {{ $file['icon'] }}"></i>
                                                        @if($file['exists'])
                                                            <i class="fas fa-check text-[8px]"></i>
                                                        @endif
                                                    </span>
                                                @endforeach
                                            </div>
                                            @endif

                                            {{-- Actions --}}
                                            <div class="flex items-center gap-2 mt-2">
                                                @if($sub->canEdit())
                                                    <a href="{{ route('opac.member.edit-submission', $sub->id) }}" class="px-2 py-1 bg-primary-100 text-primary-700 text-[10px] font-medium rounded hover:bg-primary-200 transition inline-flex items-center gap-1">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                @endif
                                                @if($sub->isPublished() && $sub->ethesis_id)
                                                    <a href="{{ route('opac.ethesis.show', $sub->ethesis_id) }}" class="px-2 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-medium rounded hover:bg-emerald-200 transition inline-flex items-center gap-1">
                                                        <i class="fas fa-external-link-alt"></i> E-Thesis
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Status Messages --}}
                                    @if($sub->status === 'revision_required' && $sub->review_notes)
                                        <div class="mt-3 p-2.5 bg-orange-50 border border-orange-200 rounded-lg">
                                            <div class="flex items-start gap-2">
                                                <i class="fas fa-exclamation-triangle text-orange-500 text-xs mt-0.5"></i>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-[10px] font-semibold text-orange-800">Catatan Revisi</p>
                                                    <p class="text-[10px] text-orange-700 mt-0.5 line-clamp-2">{{ $sub->review_notes }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if($sub->status === 'rejected' && $sub->rejection_reason)
                                        <div class="mt-3 p-2.5 bg-red-50 border border-red-200 rounded-lg">
                                            <div class="flex items-start gap-2">
                                                <i class="fas fa-times-circle text-red-500 text-xs mt-0.5"></i>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-[10px] font-semibold text-red-800">Alasan Penolakan</p>
                                                    <p class="text-[10px] text-red-700 mt-0.5 line-clamp-2">{{ $sub->rejection_reason }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if($sub->status === 'approved' && !$sub->isPublished())
                                        <div class="mt-3 p-2.5 bg-emerald-50 border border-emerald-200 rounded-lg">
                                            <div class="flex items-start gap-2">
                                                <i class="fas fa-check-circle text-emerald-500 text-xs mt-0.5"></i>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-[10px] font-semibold text-emerald-800">Disetujui!</p>
                                                    <p class="text-[10px] text-emerald-700 mt-0.5">Akan segera dipublikasikan ke E-Thesis.</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if(in_array($sub->status, ['submitted', 'under_review']))
                                        <div class="mt-3 p-2.5 bg-blue-50 border border-blue-200 rounded-lg">
                                            <div class="flex items-start gap-2">
                                                <i class="fas fa-hourglass-half text-blue-500 text-xs mt-0.5 animate-pulse"></i>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-[10px] font-semibold text-blue-800">
                                                        {{ $sub->status === 'under_review' ? 'Sedang Direview' : 'Menunggu Review' }}
                                                    </p>
                                                    <p class="text-[10px] text-blue-700 mt-0.5">Sedang diverifikasi oleh pustakawan.</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            
                            @if($submissions->count() > 5)
                            <div class="p-3 bg-gray-50 border-t border-gray-100 text-center">
                                <a href="{{ route('opac.member.submissions') }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium">
                                    Lihat {{ $submissions->count() - 5 }} submission lainnya <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                            @endif
                        @else
                            <div class="p-8 text-center">
                                <div class="w-16 h-16 bg-violet-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-file-alt text-violet-300 text-xl"></i>
                                </div>
                                <p class="text-sm text-gray-500 mb-3">Belum ada pengajuan tugas akhir</p>
                                <a href="{{ route('opac.member.submit-thesis') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-violet-600 text-white text-xs font-medium rounded-lg hover:bg-violet-700 transition">
                                    <i class="fas fa-plus"></i>
                                    Buat Pengajuan
                                </a>
                            </div>
                        @endif
                    </div>
                </div>


                {{-- Right Column: History Pinjaman (Compact) --}}
                <div class="space-y-4">
                    {{-- Denda (jika ada) --}}
                    @if($fines->count() > 0)
                    <div class="bg-gradient-to-br from-red-50 to-orange-50 rounded-2xl shadow-sm border border-red-200/50 overflow-hidden">
                        <div class="p-3 border-b border-red-200/50">
                            <h2 class="font-bold text-red-800 text-xs flex items-center gap-2">
                                <div class="w-6 h-6 bg-red-500 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-coins text-white text-[10px]"></i>
                                </div>
                                Denda Belum Dibayar
                            </h2>
                        </div>
                        <div class="p-3 space-y-2">
                            @foreach($fines->take(3) as $fine)
                            <div class="p-2 bg-white rounded-lg border border-red-100">
                                <p class="text-xs font-medium text-gray-900 line-clamp-1">{{ $fine->loan?->item?->book?->title ?? 'Denda' }}</p>
                                <p class="text-sm font-bold text-red-600 mt-0.5">Rp {{ number_format($fine->amount, 0, ',', '.') }}</p>
                            </div>
                            @endforeach
                            @if($fines->count() > 3)
                            <p class="text-[10px] text-red-600 text-center">+{{ $fines->count() - 3 }} denda lainnya</p>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- History Pinjaman --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-3 border-b border-gray-100 flex items-center justify-between">
                            <h2 class="font-bold text-gray-900 text-xs flex items-center gap-2">
                                <div class="w-6 h-6 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-history text-gray-500 text-[10px]"></i>
                                </div>
                                Riwayat Pinjaman
                            </h2>
                        </div>
                        
                        @if($history->count() > 0)
                            <div class="divide-y divide-gray-50 max-h-80 overflow-y-auto">
                                @foreach($history as $loan)
                                <div class="p-3 flex items-center gap-2">
                                    <div class="w-8 h-10 bg-gray-100 rounded flex-shrink-0 overflow-hidden">
                                        @if($loan->item?->book?->cover_url)
                                            <img src="{{ $loan->item->book->cover_url }}" alt="" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <i class="fas fa-book text-gray-300 text-[8px]"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-medium text-gray-900 line-clamp-1">{{ $loan->item?->book?->title ?? '-' }}</p>
                                        <p class="text-[10px] text-gray-400 mt-0.5">
                                            {{ $loan->return_date?->format('d M Y') ?? '-' }}
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="px-1.5 py-0.5 bg-green-100 text-green-600 text-[9px] font-medium rounded">
                                            Selesai
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-6 text-center">
                                <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                    <i class="fas fa-history text-gray-300 text-sm"></i>
                                </div>
                                <p class="text-xs text-gray-400">Belum ada riwayat</p>
                            </div>
                        @endif
                    </div>

                    {{-- Quick Links --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-3 border-b border-gray-100">
                            <h2 class="font-bold text-gray-900 text-xs flex items-center gap-2">
                                <div class="w-6 h-6 bg-primary-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-link text-primary-600 text-[10px]"></i>
                                </div>
                                Menu Cepat
                            </h2>
                        </div>
                        <div class="p-2 space-y-1">
                            <a href="{{ route('opac.home') }}" class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 transition">
                                <div class="w-7 h-7 bg-blue-50 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-search text-blue-500 text-[10px]"></i>
                                </div>
                                <span class="text-xs text-gray-700">Cari Buku</span>
                            </a>
                            <a href="{{ route('opac.member.submit-thesis') }}" class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 transition">
                                <div class="w-7 h-7 bg-violet-50 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-upload text-violet-500 text-[10px]"></i>
                                </div>
                                <span class="text-xs text-gray-700">Unggah Tugas Akhir</span>
                            </a>
                            <a href="{{ route('opac.page', 'panduan-opac') }}" class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 transition">
                                <div class="w-7 h-7 bg-amber-50 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-question-circle text-amber-500 text-[10px]"></i>
                                </div>
                                <span class="text-xs text-gray-700">Panduan OPAC</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-opac.layout>
