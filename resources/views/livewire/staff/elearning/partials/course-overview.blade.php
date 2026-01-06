{{-- Overview Tab --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        {{-- Description --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-900 mb-3">Deskripsi</h3>
            <p class="text-gray-600 whitespace-pre-line">{{ $course->description ?: 'Belum ada deskripsi' }}</p>
        </div>

        {{-- Quick Modules Preview --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-900">Modul Pembelajaran</h3>
                <button wire:click="$set('tab', 'modules')" class="text-sm text-violet-600 hover:text-violet-700 font-medium">Lihat Semua →</button>
            </div>
            @if($course->modules->count() > 0)
            <div class="space-y-3">
                @foreach($course->modules->take(5) as $index => $module)
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                    <div class="w-8 h-8 bg-violet-100 rounded-lg flex items-center justify-center text-violet-600 font-bold text-sm">{{ $index + 1 }}</div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900">{{ $module->title }}</p>
                        <p class="text-xs text-gray-500">{{ $module->materials->count() }} materi</p>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-500 text-center py-4">Belum ada modul</p>
            @endif
        </div>
    </div>

    <div class="space-y-6">
        {{-- Course Info --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-900 mb-4">Informasi Kelas</h3>
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-violet-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-signal text-violet-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Level</p>
                        <p class="font-medium text-gray-900 capitalize">{{ $course->level }}</p>
                    </div>
                </div>
                @if($course->duration_hours)
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-blue-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Durasi</p>
                        <p class="font-medium text-gray-900">{{ $course->duration_hours }} jam</p>
                    </div>
                </div>
                @endif
                @if($course->start_date)
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar text-green-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Periode</p>
                        <p class="font-medium text-gray-900">{{ $course->start_date->format('d M Y') }} - {{ $course->end_date?->format('d M Y') ?? 'Selesai' }}</p>
                    </div>
                </div>
                @endif
                @if($course->is_online)
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-video text-amber-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Mode</p>
                        <p class="font-medium text-gray-900">Online</p>
                    </div>
                </div>
                @elseif($course->location)
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-map-marker-alt text-amber-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Lokasi</p>
                        <p class="font-medium text-gray-900">{{ $course->location }}</p>
                    </div>
                </div>
                @endif
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-rose-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-award text-rose-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Sertifikat</p>
                        <p class="font-medium text-gray-900">{{ $course->has_certificate ? 'Ya' : 'Tidak' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-percentage text-indigo-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Nilai Kelulusan</p>
                        <p class="font-medium text-gray-900">{{ $course->passing_score }}%</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Instructor --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-900 mb-4">Instruktur</h3>
            <div class="flex items-center gap-3">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($course->instructor->name) }}&size=48&background=random" class="w-12 h-12 rounded-full">
                <div>
                    <p class="font-semibold text-gray-900">{{ $course->instructor->name }}</p>
                    <p class="text-sm text-gray-500">{{ $course->instructor->email }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
