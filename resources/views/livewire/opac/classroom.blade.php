<div class="min-h-screen bg-gray-900" x-data="{ showCurriculum: true }">
    {{-- Top Bar --}}
    <div class="sticky top-0 z-50 bg-gray-800 border-b border-gray-700">
        <div class="flex items-center justify-between px-4 py-3">
            <div class="flex items-center gap-4">
                <a href="{{ route('opac.elearning.show', $course->slug) }}" class="w-9 h-9 bg-gray-700 hover:bg-gray-600 rounded-lg flex items-center justify-center text-gray-300 transition">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="hidden sm:block">
                    <h1 class="text-white font-semibold text-sm line-clamp-1">{{ $course->title }}</h1>
                    <p class="text-gray-400 text-xs">{{ $course->instructor->name }}</p>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                {{-- Progress --}}
                <div class="hidden sm:flex items-center gap-3">
                    <div class="w-32 h-2 bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full transition-all" style="width: {{ $enrollment->progress_percent }}%"></div>
                    </div>
                    <span class="text-white text-sm font-medium">{{ $enrollment->progress_percent }}%</span>
                </div>
                
                {{-- Toggle Curriculum --}}
                <button @click="showCurriculum = !showCurriculum" class="w-9 h-9 bg-gray-700 hover:bg-gray-600 rounded-lg flex items-center justify-center text-gray-300 transition lg:hidden">
                    <i class="fas fa-list"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="flex">
        {{-- Sidebar Curriculum --}}
        <div x-show="showCurriculum" x-transition:enter="transition ease-out duration-200" 
             class="fixed lg:relative inset-y-0 left-0 z-40 w-80 bg-gray-800 border-r border-gray-700 overflow-y-auto lg:block"
             style="top: 57px; height: calc(100vh - 57px);">
            
            <div class="p-4 border-b border-gray-700">
                <h2 class="text-white font-bold text-sm">Kurikulum</h2>
                <p class="text-gray-400 text-xs mt-1">{{ $course->modules->count() }} Modul • {{ $course->modules->sum(fn($m) => $m->materials->count()) }} Materi</p>
            </div>

            <div class="p-2">
                @foreach($course->modules as $moduleIndex => $module)
                <div x-data="{ open: {{ $currentMaterial && $currentMaterial->module_id === $module->id ? 'true' : 'false' }} }" class="mb-2">
                    <button @click="open = !open" class="w-full flex items-center justify-between p-3 bg-gray-700/50 hover:bg-gray-700 rounded-xl text-left transition">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-blue-600/20 text-blue-400 rounded-lg flex items-center justify-center text-sm font-bold">
                                {{ $moduleIndex + 1 }}
                            </div>
                            <div>
                                <p class="text-white text-sm font-medium line-clamp-1">{{ $module->title }}</p>
                                <p class="text-gray-400 text-xs">{{ $module->materials->count() }} materi</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform" :class="open && 'rotate-180'"></i>
                    </button>
                    
                    <div x-show="open" x-collapse class="mt-1 space-y-1 pl-4">
                        @foreach($module->materials as $material)
                        @php $isCompleted = in_array($material->id, $completedIds); @endphp
                        <button wire:click="selectMaterial({{ $material->id }})" 
                                class="w-full flex items-center gap-3 p-2.5 rounded-lg text-left transition {{ $currentMaterialId === $material->id ? 'bg-blue-600 text-white' : 'hover:bg-gray-700 text-gray-300' }}">
                            <div class="w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 {{ $isCompleted ? 'bg-green-500 text-white' : ($currentMaterialId === $material->id ? 'bg-white/20' : 'bg-gray-600') }}">
                                @if($isCompleted)
                                <i class="fas fa-check text-xs"></i>
                                @else
                                <i class="fas fa-{{ $material->type === 'video' ? 'play' : ($material->type === 'document' ? 'file-pdf' : ($material->type === 'quiz' ? 'question' : 'file-alt')) }} text-xs"></i>
                                @endif
                            </div>
                            <span class="text-sm line-clamp-1">{{ $material->title }}</span>
                        </button>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Overlay for mobile --}}
        <div x-show="showCurriculum" @click="showCurriculum = false" class="fixed inset-0 bg-black/50 z-30 lg:hidden" style="top: 57px;"></div>

        {{-- Main Content --}}
        <div class="flex-1 min-h-screen" style="min-height: calc(100vh - 57px);">
            @if($currentMaterial)
            <div class="max-w-4xl mx-auto p-4 lg:p-8">
                {{-- Material Header --}}
                <div class="mb-6">
                    <div class="flex items-center gap-2 text-gray-400 text-sm mb-2">
                        <span>{{ $currentMaterial->module->title }}</span>
                        <i class="fas fa-chevron-right text-xs"></i>
                        <span class="text-white">{{ $currentMaterial->title }}</span>
                    </div>
                    <h1 class="text-2xl font-bold text-white">{{ $currentMaterial->title }}</h1>
                    @if($currentMaterial->duration_minutes)
                    <p class="text-gray-400 text-sm mt-1"><i class="fas fa-clock mr-1"></i>{{ $currentMaterial->duration_minutes }} menit</p>
                    @endif
                </div>

                {{-- Content Area --}}
                <div class="bg-gray-800 rounded-2xl overflow-hidden mb-6">
                    @if($currentMaterial->type === 'video')
                        @if($currentMaterial->video_url)
                        <div class="aspect-video">
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
                        <div class="aspect-[4/3]">
                            <iframe src="{{ Storage::url($currentMaterial->file_path) }}" class="w-full h-full"></iframe>
                        </div>
                        @endif
                    @elseif($currentMaterial->type === 'link')
                        <div class="p-8 text-center">
                            <div class="w-16 h-16 bg-blue-600/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-external-link-alt text-blue-400 text-2xl"></i>
                            </div>
                            <h3 class="text-white font-semibold mb-2">Link Eksternal</h3>
                            <p class="text-gray-400 text-sm mb-4">Materi ini akan membuka halaman eksternal</p>
                            <a href="{{ $currentMaterial->external_link }}" target="_blank" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition">
                                <i class="fas fa-external-link-alt"></i> Buka Link
                            </a>
                        </div>
                    @endif

                    {{-- Text Content --}}
                    @if($currentMaterial->content)
                    <div class="p-6 prose prose-invert max-w-none">
                        {!! $currentMaterial->content !!}
                    </div>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-between gap-4">
                    <button wire:click="prevMaterial" class="px-4 py-2.5 bg-gray-700 hover:bg-gray-600 text-white rounded-xl font-medium transition flex items-center gap-2">
                        <i class="fas fa-chevron-left"></i> <span class="hidden sm:inline">Sebelumnya</span>
                    </button>

                    <div class="flex items-center gap-3">
                        @if(!in_array($currentMaterialId, $completedIds))
                        <button wire:click="markComplete" class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold transition flex items-center gap-2">
                            <i class="fas fa-check"></i> Tandai Selesai
                        </button>
                        @else
                        <span class="px-4 py-2.5 bg-green-600/20 text-green-400 rounded-xl font-medium flex items-center gap-2">
                            <i class="fas fa-check-circle"></i> Selesai
                        </span>
                        @endif
                    </div>

                    <button wire:click="nextMaterial" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium transition flex items-center gap-2">
                        <span class="hidden sm:inline">Selanjutnya</span> <i class="fas fa-chevron-right"></i>
                    </button>
                </div>

                {{-- Completion Card --}}
                @if($enrollment->progress_percent >= 100)
                <div class="mt-8 p-6 bg-gradient-to-r from-emerald-600/20 to-teal-600/20 border border-emerald-500/30 rounded-2xl text-center">
                    <div class="w-16 h-16 bg-emerald-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-trophy text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Selamat! 🎉</h3>
                    <p class="text-gray-300 mb-4">Anda telah menyelesaikan semua materi kelas ini.</p>
                    @if($course->has_certificate)
                    <a href="#" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold transition">
                        <i class="fas fa-certificate"></i> Unduh Sertifikat
                    </a>
                    @endif
                </div>
                @endif
            </div>
            @else
            <div class="flex items-center justify-center h-full">
                <div class="text-center">
                    <div class="w-20 h-20 bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-book-open text-gray-500 text-3xl"></i>
                    </div>
                    <p class="text-gray-400">Pilih materi untuk memulai</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
