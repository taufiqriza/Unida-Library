<div class="min-h-screen bg-gray-50">
    {{-- Hero Section --}}
    <div class="relative bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-white rounded-full translate-y-1/2 -translate-x-1/2"></div>
        </div>
        
        <div class="relative max-w-6xl mx-auto px-4 py-8 lg:py-12">
            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-sm text-white/70 mb-6">
                <a href="{{ route('opac.home') }}" class="hover:text-white transition">Beranda</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <a href="{{ route('opac.page', 'e-learning') }}" class="hover:text-white transition">E-Learning</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <span class="text-white">{{ Str::limit($course->title, 30) }}</span>
            </nav>

            <div class="grid lg:grid-cols-3 gap-8">
                {{-- Course Info --}}
                <div class="lg:col-span-2">
                    @if($course->category)
                    <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur text-white text-sm font-medium rounded-full mb-4">
                        {{ $course->category->name }}
                    </span>
                    @endif
                    
                    <h1 class="text-2xl lg:text-4xl font-bold text-white mb-4">{{ $course->title }}</h1>
                    <p class="text-white/80 text-lg mb-6">{{ $course->description }}</p>
                    
                    {{-- Meta Info --}}
                    <div class="flex flex-wrap items-center gap-4 text-white/90 text-sm">
                        <div class="flex items-center gap-2">
                            <img src="{{ $course->instructor->getAvatarUrl(32) }}" class="w-8 h-8 rounded-full object-cover">
                            <span>{{ $course->instructor->name }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-signal"></i>
                            <span class="capitalize">{{ $course->level }}</span>
                        </div>
                        @if($course->duration_hours)
                        <div class="flex items-center gap-2">
                            <i class="fas fa-clock"></i>
                            <span>{{ $course->duration_hours }} jam</span>
                        </div>
                        @endif
                        <div class="flex items-center gap-2">
                            <i class="fas fa-users"></i>
                            <span>{{ $course->enrollments()->whereIn('status', ['approved', 'completed'])->count() }} peserta</span>
                        </div>
                    </div>
                </div>

                {{-- Enrollment Card --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-xl p-4">
                        {{-- Thumbnail --}}
                        <div class="relative h-32 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-xl overflow-hidden mb-3">
                            @if($course->thumbnail)
                            <img src="{{ Storage::url($course->thumbnail) }}" class="w-full h-full object-cover">
                            @else
                            <div class="absolute inset-0 flex items-center justify-center">
                                <i class="fas fa-graduation-cap text-blue-300 text-4xl"></i>
                            </div>
                            @endif
                        </div>

                        {{-- Schedule Info --}}
                        @if($course->start_date)
                        <div class="bg-blue-50 rounded-xl p-3 mb-3">
                            <h4 class="font-semibold text-blue-900 text-sm mb-1.5">Jadwal Kelas</h4>
                            <div class="space-y-1.5 text-sm">
                                <div class="flex items-center gap-2 text-gray-700">
                                    <i class="fas fa-calendar-alt text-blue-500 w-4"></i>
                                    <span>{{ $course->start_date->format('d M Y') }}@if($course->end_date && $course->end_date != $course->start_date) - {{ $course->end_date->format('d M Y') }}@endif</span>
                                </div>
                                @if($course->schedule_time)
                                <div class="flex items-center gap-2 text-gray-700">
                                    <i class="fas fa-clock text-blue-500 w-4"></i>
                                    <span>{{ \Carbon\Carbon::parse($course->schedule_time)->format('H:i') }} WIB</span>
                                </div>
                                @endif
                                <div class="flex items-center gap-2 text-gray-700">
                                    <i class="fas {{ $course->is_online ? 'fa-video' : 'fa-map-marker-alt' }} text-blue-500 w-4"></i>
                                    <span>{{ $course->is_online ? 'Online' : $course->location }}</span>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Enrollment Status --}}
                        @if($myEnrollment)
                            @if($myEnrollment->status === 'pending')
                            <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 mb-3">
                                <div class="flex items-center gap-2 text-amber-700">
                                    <i class="fas fa-hourglass-half"></i>
                                    <span class="font-semibold text-sm">Menunggu Persetujuan</span>
                                </div>
                                <p class="text-amber-600 text-xs mt-1">Pendaftaran sedang direview admin.</p>
                            </div>
                            @elseif($myEnrollment->status === 'approved' || $myEnrollment->status === 'completed')
                            <div class="bg-green-50 border border-green-200 rounded-xl p-3 mb-3">
                                <div class="flex items-center gap-2 text-green-700">
                                    <i class="fas fa-check-circle"></i>
                                    <span class="font-semibold text-sm">{{ $myEnrollment->status === 'completed' ? 'Selesai!' : 'Anda Terdaftar' }}</span>
                                </div>
                                <p class="text-green-600 text-xs mt-1">Progress: {{ $myEnrollment->progress_percent }}%</p>
                                <div class="w-full h-1.5 bg-green-200 rounded-full mt-1.5 overflow-hidden">
                                    <div class="h-full bg-green-500 rounded-full" style="width: {{ $myEnrollment->progress_percent }}%"></div>
                                </div>
                            </div>
                            <a href="{{ route('opac.classroom', $course->slug) }}" class="block w-full py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold rounded-xl text-center hover:from-blue-700 hover:to-indigo-700 transition shadow-lg shadow-blue-500/25 text-sm">
                                <i class="fas fa-play mr-2"></i>{{ $myEnrollment->progress_percent > 0 ? 'Lanjutkan Belajar' : 'Mulai Belajar' }}
                            </a>
                            @endif
                        @else
                            @auth('member')
                            <button wire:click="$set('showEnrollModal', true)" class="w-full py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold rounded-xl hover:from-blue-700 hover:to-indigo-700 transition shadow-lg shadow-blue-500/25 text-sm">
                                <i class="fas fa-user-plus mr-2"></i>Daftar Kelas
                            </button>
                            @if($course->requires_approval)
                            <p class="text-xs text-gray-500 text-center mt-1.5"><i class="fas fa-info-circle mr-1"></i>Memerlukan persetujuan</p>
                            @endif
                            @else
                            <a href="{{ route('login') }}" class="block w-full py-2.5 bg-gray-100 text-gray-700 font-bold rounded-xl text-center hover:bg-gray-200 transition text-sm">
                                <i class="fas fa-sign-in-alt mr-2"></i>Login untuk Daftar
                            </a>
                            @endauth
                        @endif

                        {{-- Course Stats --}}
                        <div class="grid grid-cols-3 gap-2 mt-4 pt-4 border-t border-gray-100">
                            <div class="text-center">
                                <p class="text-xl font-bold text-gray-900">{{ $course->modules->count() }}</p>
                                <p class="text-xs text-gray-500">Modul</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xl font-bold text-gray-900">{{ $course->modules->sum(fn($m) => $m->materials->count()) }}</p>
                                <p class="text-xs text-gray-500">Materi</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xl font-bold text-gray-900">{{ $course->has_certificate ? 'Ya' : '-' }}</p>
                                <p class="text-xs text-gray-500">Sertifikat</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Content Section --}}
    <div class="max-w-6xl mx-auto px-4 py-8">
        <div class="grid lg:grid-cols-3 gap-8">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- What You'll Learn --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 bg-violet-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-lightbulb text-violet-600"></i>
                        </div>
                        Yang Akan Dipelajari
                    </h2>
                    <div class="grid sm:grid-cols-2 gap-3">
                        @foreach($course->modules as $module)
                        <div class="flex items-start gap-2">
                            <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                            <span class="text-gray-700 text-sm">{{ $module->title }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Course Curriculum --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-list-alt text-blue-600"></i>
                            </div>
                            Kurikulum Kelas
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">{{ $course->modules->count() }} modul • {{ $course->modules->sum(fn($m) => $m->materials->count()) }} materi</p>
                    </div>
                    
                    <div class="divide-y divide-gray-100">
                        @foreach($course->modules as $index => $module)
                        <div x-data="{ open: {{ $index === 0 ? 'true' : 'false' }} }" class="bg-white">
                            <button @click="open = !open" class="w-full p-4 flex items-center justify-between hover:bg-gray-50 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-violet-500 to-purple-600 rounded-xl flex items-center justify-center text-white font-bold">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="text-left">
                                        <h3 class="font-semibold text-gray-900">{{ $module->title }}</h3>
                                        <p class="text-xs text-gray-500">{{ $module->materials->count() }} materi</p>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-down text-gray-400 transition-transform" :class="{ 'rotate-180': open }"></i>
                            </button>
                            
                            <div x-show="open" x-collapse class="border-t border-gray-100 bg-gray-50">
                                @if($module->description)
                                <p class="px-4 py-3 text-sm text-gray-600 border-b border-gray-100">{{ $module->description }}</p>
                                @endif
                                <div class="divide-y divide-gray-100">
                                    @foreach($module->materials as $material)
                                    <div class="px-4 py-3 flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0
                                            {{ $material->type === 'video' ? 'bg-red-100 text-red-600' : '' }}
                                            {{ $material->type === 'document' ? 'bg-blue-100 text-blue-600' : '' }}
                                            {{ $material->type === 'text' ? 'bg-green-100 text-green-600' : '' }}
                                            {{ $material->type === 'link' ? 'bg-amber-100 text-amber-600' : '' }}
                                            {{ $material->type === 'quiz' ? 'bg-purple-100 text-purple-600' : '' }}">
                                            <i class="fas {{ $material->type === 'video' ? 'fa-play' : ($material->type === 'document' ? 'fa-file-pdf' : ($material->type === 'text' ? 'fa-file-alt' : ($material->type === 'link' ? 'fa-link' : 'fa-question-circle'))) }} text-sm"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-gray-900 text-sm">{{ $material->title }}</p>
                                            <p class="text-xs text-gray-500">{{ ucfirst($material->type) }}@if($material->duration_minutes) • {{ $material->duration_minutes }} menit @endif</p>
                                        </div>
                                        @if($myEnrollment && $myEnrollment->status === 'approved')
                                        <i class="fas fa-lock-open text-green-500"></i>
                                        @else
                                        <i class="fas fa-lock text-gray-300"></i>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Instructor --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-900 mb-4">Instruktur</h3>
                    <div class="flex items-center gap-4">
                        <img src="{{ $course->instructor->getAvatarUrl(64) }}" class="w-16 h-16 rounded-xl object-cover">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $course->instructor->name }}</p>
                            <p class="text-sm text-gray-500">{{ $course->instructor->email }}</p>
                        </div>
                    </div>
                </div>

                {{-- Requirements --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-900 mb-4">Persyaratan</h3>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check text-green-500 mt-0.5"></i>
                            <span>Terdaftar sebagai anggota perpustakaan</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check text-green-500 mt-0.5"></i>
                            <span>Memiliki akses internet yang stabil</span>
                        </li>
                        @if(!$course->is_online)
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check text-green-500 mt-0.5"></i>
                            <span>Hadir di lokasi sesuai jadwal</span>
                        </li>
                        @endif
                    </ul>
                </div>

                {{-- Certificate Info --}}
                @if($course->has_certificate)
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl border border-amber-200 p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-certificate text-amber-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-amber-900">Sertifikat</h3>
                            <p class="text-sm text-amber-700">Nilai kelulusan: {{ $course->passing_score }}%</p>
                        </div>
                    </div>
                    <p class="text-sm text-amber-800">Dapatkan sertifikat resmi setelah menyelesaikan kelas dengan nilai minimal {{ $course->passing_score }}%.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Enroll Modal --}}
    @if($showEnrollModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" wire:click.self="$set('showEnrollModal', false)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
            <div class="p-6 border-b border-gray-100 text-center">
                <div class="w-16 h-16 bg-violet-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-plus text-violet-600 text-2xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Daftar Kelas</h3>
                <p class="text-gray-500 text-sm mt-1">{{ $course->title }}</p>
            </div>
            <div class="p-6">
                <div class="bg-gray-50 rounded-xl p-4 mb-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Jadwal</span>
                        <span class="font-medium text-gray-900">{{ $course->start_date?->format('d M Y') ?? 'Fleksibel' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm mt-2">
                        <span class="text-gray-600">Mode</span>
                        <span class="font-medium text-gray-900">{{ $course->is_online ? 'Online' : 'Offline' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm mt-2">
                        <span class="text-gray-600">Sertifikat</span>
                        <span class="font-medium text-gray-900">{{ $course->has_certificate ? 'Ya' : 'Tidak' }}</span>
                    </div>
                </div>
                
                @if($course->requires_approval)
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 mb-4">
                    <p class="text-amber-700 text-sm"><i class="fas fa-info-circle mr-1"></i>Pendaftaran akan direview oleh admin terlebih dahulu.</p>
                </div>
                @endif

                <div class="flex gap-3">
                    <button wire:click="$set('showEnrollModal', false)" class="flex-1 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition">
                        Batal
                    </button>
                    <button wire:click="enroll" class="flex-1 py-2.5 bg-violet-600 text-white font-semibold rounded-xl hover:bg-violet-700 transition">
                        <span wire:loading.remove wire:target="enroll">Konfirmasi</span>
                        <span wire:loading wire:target="enroll"><i class="fas fa-spinner fa-spin"></i></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
