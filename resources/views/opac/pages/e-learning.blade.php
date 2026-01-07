<x-opac.layout :title="__('opac.pages.e_learning.title')">
    <x-opac.page-header 
        :title="__('opac.pages.e_learning.title')" 
        :subtitle="__('opac.pages.e_learning.subtitle')"
        :breadcrumbs="[['label' => __('opac.pages.e_learning.breadcrumb')], ['label' => __('opac.pages.e_learning.title')]]"
    />

    @php
        $activeCourses = \App\Models\Course::with(['category', 'instructor', 'branch'])
            ->withCount(['enrollments', 'modules'])
            ->where('status', 'published')
            ->where(fn($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', now()))
            ->latest()
            ->get();
        
        $categories = \App\Models\CourseCategory::where('is_active', true)
            ->withCount(['courses' => fn($q) => $q->where('status', 'published')])
            ->orderBy('sort_order')
            ->get();
    @endphp

    <section class="max-w-6xl mx-auto px-4 py-6 lg:py-10" x-data="{ showIntro: !localStorage.getItem('hideElearningIntro') }">
        {{-- Intro Banner --}}
        <div x-show="showIntro" x-transition class="relative bg-gradient-to-r from-violet-600 to-indigo-600 rounded-2xl p-4 lg:p-5 mb-6 text-white overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-graduation-cap text-lg"></i>
                    </div>
                    <p class="text-sm lg:text-base text-white/90">{{ __('opac.pages.e_learning.intro') }}</p>
                </div>
                <button @click="showIntro = false; localStorage.setItem('hideElearningIntro', '1')" class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center flex-shrink-0 transition">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>

        {{-- Categories --}}
        @if($categories->count() > 0)
        <div class="flex items-center gap-2 overflow-x-auto pb-2 mb-6 scrollbar-hide">
            @foreach($categories as $cat)
            <div class="flex items-center gap-2 px-3 py-2 bg-white rounded-full shadow-sm border border-gray-100 hover:border-violet-200 hover:shadow transition flex-shrink-0 cursor-pointer">
                <i class="fas {{ $cat->icon ?? 'fa-folder' }} text-xs" style="color: {{ $cat->color ?? '#8B5CF6' }}"></i>
                <span class="text-xs font-medium text-gray-700">{{ $cat->name }}</span>
                <span class="text-[10px] text-gray-400">({{ $cat->courses_count }})</span>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Active Courses --}}
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-gray-900 text-base lg:text-lg flex items-center gap-2">
                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                Kelas Aktif
            </h3>
            <span class="text-sm text-gray-500">{{ $activeCourses->count() }} kelas</span>
        </div>

        @if($activeCourses->count() > 0)
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
            @foreach($activeCourses as $course)
            <a href="{{ route('opac.elearning.show', $course->slug) }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:border-violet-200 transition-all group block">
                {{-- Thumbnail --}}
                <div class="relative h-36 bg-gradient-to-br from-violet-500 to-purple-600 overflow-hidden">
                    @if($course->thumbnail)
                    <img src="{{ Storage::url($course->thumbnail) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-white/30 text-5xl"></i>
                    </div>
                    @endif
                    <div class="absolute top-2 right-2">
                        <span class="px-2 py-0.5 bg-white/90 backdrop-blur text-gray-700 text-[10px] font-semibold rounded capitalize">{{ $course->level }}</span>
                    </div>
                    @if($course->branch)
                    <div class="absolute bottom-2 left-2">
                        <span class="px-2 py-0.5 bg-black/50 backdrop-blur text-white text-[10px] font-medium rounded">
                            {{ $course->branch->name }}
                        </span>
                    </div>
                    @endif
                </div>
                
                {{-- Content --}}
                <div class="p-4">
                    <h4 class="font-bold text-gray-900 text-sm line-clamp-2 h-10 group-hover:text-violet-600 transition">{{ $course->title }}</h4>
                    <p class="text-xs text-gray-500 mt-1 line-clamp-2 h-8">{{ $course->description ?: '-' }}</p>
                    
                    {{-- Labels --}}
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        @if($course->category)
                        <span class="text-[10px] font-medium text-violet-600 bg-violet-50 px-1.5 py-0.5 rounded">{{ $course->category->name }}</span>
                        @endif
                        @if($course->has_certificate)
                        <span class="text-[10px] font-medium text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded"><i class="fas fa-award mr-0.5"></i>Sertifikat</span>
                        @endif
                    </div>
                    
                    {{-- Meta --}}
                    <div class="flex items-center gap-3 mt-3 pt-3 border-t border-gray-100 text-[10px] text-gray-500">
                        <span class="flex items-center gap-1"><i class="fas fa-layer-group text-violet-400"></i>{{ $course->modules_count }}</span>
                        <span class="flex items-center gap-1"><i class="fas fa-users text-green-400"></i>{{ $course->enrollments_count }}</span>
                        @if($course->duration_hours)
                        <span class="flex items-center gap-1"><i class="fas fa-clock text-blue-400"></i>{{ $course->duration_hours }}j</span>
                        @endif
                        @if($course->is_online)
                        <span class="flex items-center gap-1"><i class="fas fa-video text-cyan-400"></i></span>
                        @endif
                    </div>
                    
                    {{-- Instructor --}}
                    <div class="flex items-center gap-2 mt-3">
                        <img src="{{ $course->instructor->getAvatarUrl(20) }}" class="w-5 h-5 rounded-full object-cover">
                        <span class="text-[10px] text-gray-600 truncate">{{ $course->instructor->name }}</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        @else
        <div class="bg-gray-50 rounded-xl p-8 text-center border border-gray-200 mb-8">
            <div class="w-14 h-14 bg-gray-200 rounded-xl flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-graduation-cap text-gray-400 text-xl"></i>
            </div>
            <h3 class="font-bold text-gray-900 mb-1 text-sm">Belum Ada Kelas Aktif</h3>
            <p class="text-gray-500 text-xs">Kelas baru akan segera tersedia</p>
        </div>
        @endif

        {{-- Contact --}}
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl p-4 border border-amber-200/50 flex items-center gap-4">
            <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-lightbulb text-amber-600"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-900 text-sm">{{ __('opac.pages.e_learning.need_training') }}</p>
                <a href="mailto:library@unida.gontor.ac.id" class="text-xs text-amber-700 hover:text-amber-800">
                    <i class="fas fa-envelope mr-1"></i>library@unida.gontor.ac.id
                </a>
            </div>
        </div>
    </section>
</x-opac.layout>
