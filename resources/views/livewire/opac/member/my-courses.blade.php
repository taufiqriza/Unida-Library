<div class="min-h-screen bg-gray-50">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
        <div class="max-w-5xl mx-auto px-4 py-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('opac.member.dashboard') }}" class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center hover:bg-white/30 transition">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-xl font-bold">Kelas Saya</h1>
                    <p class="text-blue-200 text-sm">E-Learning yang sedang diikuti</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 py-6">
        @if($enrollments->count() > 0)
        {{-- Stats --}}
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-book-open text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $enrollments->whereIn('status', ['approved', 'completed'])->count() }}</p>
                        <p class="text-xs text-gray-500">Kelas Aktif</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-amber-600"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $enrollments->where('status', 'pending')->count() }}</p>
                        <p class="text-xs text-gray-500">Menunggu</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-emerald-600"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $enrollments->where('status', 'completed')->count() }}</p>
                        <p class="text-xs text-gray-500">Selesai</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Course List --}}
        <div class="space-y-4">
            @foreach($enrollments as $enrollment)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="flex flex-col sm:flex-row">
                    {{-- Thumbnail --}}
                    <div class="sm:w-48 h-32 sm:h-auto bg-gradient-to-br from-blue-500 to-indigo-600 flex-shrink-0">
                        @if($enrollment->course->thumbnail)
                        <img src="{{ Storage::url($enrollment->course->thumbnail) }}" class="w-full h-full object-cover">
                        @else
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="fas fa-graduation-cap text-white/30 text-4xl"></i>
                        </div>
                        @endif
                    </div>
                    
                    {{-- Content --}}
                    <div class="flex-1 p-4">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                @if($enrollment->course->category)
                                <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-0.5 rounded">{{ $enrollment->course->category->name }}</span>
                                @endif
                                <h3 class="font-bold text-gray-900 mt-1">{{ $enrollment->course->title }}</h3>
                                <p class="text-sm text-gray-500 mt-1">{{ $enrollment->course->instructor->name }}</p>
                            </div>
                            
                            {{-- Status Badge --}}
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-lg flex-shrink-0
                                @if($enrollment->status === 'completed') bg-emerald-100 text-emerald-700
                                @elseif($enrollment->status === 'approved') bg-blue-100 text-blue-700
                                @elseif($enrollment->status === 'pending') bg-amber-100 text-amber-700
                                @else bg-red-100 text-red-700
                                @endif">
                                @if($enrollment->status === 'completed') Selesai
                                @elseif($enrollment->status === 'approved') Aktif
                                @elseif($enrollment->status === 'pending') Menunggu
                                @else Ditolak
                                @endif
                            </span>
                        </div>
                        
                        {{-- Progress --}}
                        @if($enrollment->status === 'approved' || $enrollment->status === 'completed')
                        <div class="mt-4">
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="text-gray-500">Progress</span>
                                <span class="font-semibold text-gray-900">{{ $enrollment->progress_percent }}%</span>
                            </div>
                            <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full transition-all" style="width: {{ $enrollment->progress_percent }}%"></div>
                            </div>
                        </div>
                        
                        {{-- Meta & Action --}}
                        <div class="flex items-center justify-between mt-4">
                            <div class="flex items-center gap-4 text-xs text-gray-500">
                                <span><i class="fas fa-layer-group mr-1"></i>{{ $enrollment->course->modules->count() }} Modul</span>
                                @if($enrollment->course->duration_hours)
                                <span><i class="fas fa-clock mr-1"></i>{{ $enrollment->course->duration_hours }} jam</span>
                                @endif
                            </div>
                            <a href="{{ route('opac.classroom', $enrollment->course->slug) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition flex items-center gap-2">
                                <i class="fas fa-play"></i>
                                {{ $enrollment->progress_percent > 0 ? 'Lanjutkan' : 'Mulai' }}
                            </a>
                        </div>
                        @elseif($enrollment->status === 'pending')
                        <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                            <p class="text-amber-700 text-sm"><i class="fas fa-info-circle mr-1"></i>Pendaftaran Anda sedang menunggu persetujuan admin.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Browse More --}}
        <div class="mt-6 text-center">
            <a href="{{ route('opac.page', 'e-learning') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold transition">
                <i class="fas fa-search"></i> Cari Kelas Lainnya
            </a>
        </div>

        @else
        {{-- Empty State --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
            <div class="w-24 h-24 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-graduation-cap text-blue-300 text-4xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Kelas</h3>
            <p class="text-gray-500 mb-6 max-w-md mx-auto">Anda belum mengikuti kelas apapun. Jelajahi berbagai kelas menarik yang tersedia.</p>
            <a href="{{ route('opac.page', 'e-learning') }}" class="inline-flex items-center gap-2 px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition shadow-lg shadow-blue-500/25">
                <i class="fas fa-search"></i> Jelajahi Kelas
            </a>
        </div>
        @endif
    </div>
</div>
