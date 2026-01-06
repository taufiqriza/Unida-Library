<div class="min-h-screen bg-gray-50">
    {{-- Top Bar --}}
    <div class="sticky top-0 z-50 bg-white border-b border-gray-200 shadow-sm">
        <div class="flex items-center justify-between px-4 lg:px-6 py-3">
            <div class="flex items-center gap-4">
                <a href="{{ route('opac.elearning.show', $course->slug) }}" class="w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center text-gray-600 transition">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="hidden sm:block">
                    <h1 class="text-gray-900 font-semibold text-sm line-clamp-1">{{ $course->title }}</h1>
                    <p class="text-gray-500 text-xs">{{ $course->instructor->name }}</p>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                {{-- Progress --}}
                <div class="hidden sm:flex items-center gap-3 px-4 py-2 bg-gray-50 rounded-xl">
                    <div class="w-32 h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full transition-all" style="width: {{ $enrollment->progress_percent }}%"></div>
                    </div>
                    <span class="text-gray-700 text-sm font-semibold">{{ $enrollment->progress_percent }}%</span>
                </div>
                
                {{-- Toggle Curriculum Mobile --}}
                <button @click="showCurriculum = !showCurriculum" class="lg:hidden w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center text-gray-600 transition">
                    <i class="fas fa-list"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="flex" x-data="{ showCurriculum: window.innerWidth >= 1024 }">
        {{-- Sidebar Curriculum --}}
        <div x-show="showCurriculum" x-transition:enter="transition ease-out duration-200"
             x-cloak
             class="fixed lg:sticky inset-y-0 left-0 z-40 w-80 bg-white border-r border-gray-200 overflow-y-auto shadow-lg lg:shadow-none"
             :class="{ 'top-0': window.innerWidth < 1024, 'top-[61px]': window.innerWidth >= 1024 }"
             style="height: calc(100vh - 61px); top: 61px;">
            
            <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                <h2 class="text-gray-900 font-bold">Kurikulum</h2>
                <p class="text-gray-500 text-sm mt-1">{{ $course->modules->count() }} Modul • {{ $course->modules->sum(fn($m) => $m->materials->count()) }} Materi</p>
            </div>

            <div class="p-3">
                @foreach($course->modules as $moduleIndex => $module)
                @php $moduleHasCurrentMaterial = $currentMaterial && $currentMaterial->module_id === $module->id; @endphp
                <div x-data="{ open: {{ $moduleHasCurrentMaterial ? 'true' : 'false' }} }" 
                     x-init="$watch('$wire.currentMaterialId', () => { if ({{ json_encode($module->materials->pluck('id')->toArray()) }}.includes($wire.currentMaterialId)) open = true })"
                     class="mb-2">
                    <button @click="open = !open" class="w-full flex items-center justify-between p-3 hover:bg-gray-50 rounded-xl text-left transition group">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-lg flex items-center justify-center text-sm font-bold shadow-sm">
                                {{ $moduleIndex + 1 }}
                            </div>
                            <div>
                                <p class="text-gray-900 text-sm font-semibold line-clamp-1 group-hover:text-blue-600 transition">{{ $module->title }}</p>
                                <p class="text-gray-400 text-xs">{{ $module->materials->count() }} materi</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform" :class="open && 'rotate-180'"></i>
                    </button>
                    
                    <div x-show="open" x-collapse class="mt-1 space-y-1 ml-3 pl-3 border-l-2 border-gray-100">
                        @foreach($module->materials as $material)
                        @php $isCompleted = in_array($material->id, $completedIds); @endphp
                        <button wire:click="selectMaterial({{ $material->id }})" 
                                class="w-full flex items-center gap-3 p-2.5 rounded-xl text-left transition {{ $currentMaterialId === $material->id ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'hover:bg-gray-50 text-gray-600' }}">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 {{ $isCompleted ? 'bg-green-500 text-white' : ($currentMaterialId === $material->id ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-400') }}">
                                @if($isCompleted)
                                <i class="fas fa-check text-xs"></i>
                                @else
                                <i class="fas fa-{{ $material->type === 'video' ? 'play' : ($material->type === 'document' ? 'file-pdf' : ($material->type === 'quiz' ? 'question' : 'file-alt')) }} text-xs"></i>
                                @endif
                            </div>
                            <span class="text-sm line-clamp-1 {{ $currentMaterialId === $material->id ? 'font-semibold' : '' }}">{{ $material->title }}</span>
                        </button>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Overlay for mobile --}}
        <div x-show="showCurriculum" @click="showCurriculum = false" x-cloak class="fixed inset-0 bg-black/30 z-30 lg:hidden backdrop-blur-sm" style="top: 61px;"></div>

        {{-- Main Content --}}
        <div class="flex-1" style="min-height: calc(100vh - 61px);">
            @if($currentMaterial)
            <div class="max-w-4xl mx-auto p-4 lg:p-8">
                {{-- Material Header --}}
                <div class="mb-6">
                    <div class="flex items-center gap-2 text-sm mb-3">
                        <span class="text-gray-400">{{ $currentMaterial->module->title }}</span>
                        <i class="fas fa-chevron-right text-gray-300 text-xs"></i>
                    </div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">{{ $currentMaterial->title }}</h1>
                    <div class="flex items-center gap-4 mt-3">
                        @if($currentMaterial->duration_minutes)
                        <span class="inline-flex items-center gap-1.5 text-gray-500 text-sm">
                            <i class="fas fa-clock"></i> {{ $currentMaterial->duration_minutes }} menit
                        </span>
                        @endif
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-{{ $currentMaterial->type === 'video' ? 'red' : ($currentMaterial->type === 'document' ? 'blue' : 'gray') }}-100 text-{{ $currentMaterial->type === 'video' ? 'red' : ($currentMaterial->type === 'document' ? 'blue' : 'gray') }}-600 text-xs font-medium rounded-full">
                            <i class="fas fa-{{ $currentMaterial->type === 'video' ? 'play-circle' : ($currentMaterial->type === 'document' ? 'file-pdf' : 'file-alt') }}"></i>
                            {{ ucfirst($currentMaterial->type) }}
                        </span>
                    </div>
                </div>

                {{-- Content Area --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                    @if($currentMaterial->type === 'video')
                        @if($currentMaterial->video_url)
                        <div class="aspect-video bg-gray-900">
                            @if(str_contains($currentMaterial->video_url, 'youtube') || str_contains($currentMaterial->video_url, 'youtu.be'))
                            @php
                                preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $currentMaterial->video_url, $matches);
                                $videoId = $matches[1] ?? '';
                            @endphp
                            <iframe src="https://www.youtube.com/embed/{{ $videoId }}" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                            @else
                            <video src="{{ $currentMaterial->video_url }}" controls class="w-full h-full"></video>
                            @endif
                        </div>
                        @endif
                    @elseif($currentMaterial->type === 'document')
                        @if($currentMaterial->file_path)
                        <div class="aspect-[4/3] bg-gray-100">
                            <iframe src="{{ Storage::url($currentMaterial->file_path) }}" class="w-full h-full"></iframe>
                        </div>
                        @endif
                    @elseif($currentMaterial->type === 'link')
                        <div class="p-12 text-center bg-gradient-to-br from-blue-50 to-indigo-50">
                            <div class="w-20 h-20 bg-white rounded-2xl shadow-lg flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-external-link-alt text-blue-500 text-3xl"></i>
                            </div>
                            <h3 class="text-gray-900 font-bold text-xl mb-2">Link Eksternal</h3>
                            <p class="text-gray-500 mb-6">Materi ini akan membuka halaman di tab baru</p>
                            <a href="{{ $currentMaterial->external_link }}" target="_blank" class="inline-flex items-center gap-2 px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition shadow-lg shadow-blue-500/25">
                                <i class="fas fa-external-link-alt"></i> Buka Link
                            </a>
                        </div>
                    @endif

                    {{-- Text Content --}}
                    @if($currentMaterial->content)
                    <div class="p-6 lg:p-8 prose prose-blue max-w-none">
                        {!! $currentMaterial->content !!}
                    </div>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-between gap-4 bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                    <button wire:click="prevMaterial" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium transition flex items-center gap-2">
                        <i class="fas fa-chevron-left"></i> <span class="hidden sm:inline">Sebelumnya</span>
                    </button>

                    <div class="flex items-center gap-3">
                        @if(!in_array($currentMaterialId, $completedIds))
                        <button wire:click="markComplete" class="px-6 py-2.5 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white rounded-xl font-semibold transition flex items-center gap-2 shadow-lg shadow-green-500/25">
                            <i class="fas fa-check"></i> Tandai Selesai
                        </button>
                        @else
                        <span class="px-5 py-2.5 bg-green-50 text-green-600 rounded-xl font-semibold flex items-center gap-2 border border-green-200">
                            <i class="fas fa-check-circle"></i> Selesai
                        </span>
                        @endif
                    </div>

                    <button wire:click="nextMaterial" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium transition flex items-center gap-2 shadow-lg shadow-blue-500/25">
                        <span class="hidden sm:inline">Selanjutnya</span> <i class="fas fa-chevron-right"></i>
                    </button>
                </div>

                {{-- Completion Card --}}
                @if($enrollment->progress_percent >= 100)
                <div class="mt-8 p-8 bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-200 rounded-2xl text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <i class="fas fa-trophy text-white text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Selamat! 🎉</h3>
                    <p class="text-gray-600 mb-6">Anda telah menyelesaikan semua materi kelas ini.</p>
                    @if($course->has_certificate)
                    <button wire:click="downloadCertificate" class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white rounded-xl font-semibold transition shadow-lg">
                        <i class="fas fa-certificate"></i> Lihat Sertifikat
                    </button>
                    @endif
                </div>
                @endif
            </div>
            @else
            <div class="flex items-center justify-center h-full">
                <div class="text-center p-8">
                    <div class="w-24 h-24 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-book-open text-gray-300 text-4xl"></i>
                    </div>
                    <h3 class="text-gray-900 font-bold text-xl mb-2">Pilih Materi</h3>
                    <p class="text-gray-500">Pilih materi dari kurikulum untuk memulai belajar</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
