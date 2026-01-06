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

    <section class="max-w-6xl mx-auto px-4 py-6 lg:py-10">
        {{-- Intro --}}
        <div class="bg-gradient-to-br from-violet-50 to-purple-50 rounded-2xl p-5 lg:p-6 border border-violet-100 mb-8">
            <p class="text-gray-700 text-sm lg:text-base leading-relaxed">
                {{ __('opac.pages.e_learning.intro') }}
            </p>
        </div>

        {{-- Categories --}}
        @if($categories->count() > 0)
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg">Kategori Pembelajaran</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-8">
            @foreach($categories as $cat)
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:border-violet-200 hover:shadow-md transition text-center">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center mx-auto mb-2" style="background-color: {{ $cat->color ?? '#8B5CF6' }}20">
                    <i class="fas {{ $cat->icon ?? 'fa-folder' }}" style="color: {{ $cat->color ?? '#8B5CF6' }}"></i>
                </div>
                <p class="font-semibold text-gray-900 text-sm">{{ $cat->name }}</p>
                <p class="text-xs text-gray-500">{{ $cat->courses_count }} kelas</p>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Active Courses --}}
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg flex items-center gap-2">
            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
            Kelas Aktif
        </h3>

        @if($activeCourses->count() > 0)
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
            @foreach($activeCourses as $course)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:border-violet-200 transition-all group">
                {{-- Thumbnail --}}
                <div class="relative h-40 bg-gradient-to-br from-violet-500 to-purple-600 overflow-hidden">
                    @if($course->thumbnail)
                    <img src="{{ Storage::url($course->thumbnail) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-white/30 text-5xl"></i>
                    </div>
                    @endif
                    {{-- Level Badge --}}
                    <div class="absolute top-3 right-3">
                        <span class="px-2.5 py-1 bg-white/90 backdrop-blur text-gray-700 text-xs font-semibold rounded-lg capitalize">{{ $course->level }}</span>
                    </div>
                    {{-- Branch Badge --}}
                    @if($course->branch)
                    <div class="absolute bottom-3 left-3">
                        <span class="px-2.5 py-1 bg-black/50 backdrop-blur text-white text-xs font-medium rounded-lg">
                            <i class="fas fa-building mr-1"></i>{{ $course->branch->name }}
                        </span>
                    </div>
                    @endif
                </div>
                
                {{-- Content --}}
                <div class="p-4">
                    @if($course->category)
                    <span class="text-xs font-medium text-violet-600 bg-violet-50 px-2 py-0.5 rounded">{{ $course->category->name }}</span>
                    @endif
                    <h4 class="font-bold text-gray-900 mt-2 line-clamp-2 group-hover:text-violet-600 transition">{{ $course->title }}</h4>
                    <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ $course->description }}</p>
                    
                    {{-- Meta --}}
                    <div class="flex items-center gap-4 mt-4 pt-4 border-t border-gray-100 text-xs text-gray-500">
                        <span class="flex items-center gap-1">
                            <i class="fas fa-layer-group text-violet-400"></i>
                            {{ $course->modules_count }} Modul
                        </span>
                        <span class="flex items-center gap-1">
                            <i class="fas fa-users text-green-400"></i>
                            {{ $course->enrollments_count }} Peserta
                        </span>
                        @if($course->duration_hours)
                        <span class="flex items-center gap-1">
                            <i class="fas fa-clock text-blue-400"></i>
                            {{ $course->duration_hours }} Jam
                        </span>
                        @endif
                    </div>
                    
                    {{-- Schedule --}}
                    @if($course->start_date)
                    <div class="mt-3 p-2 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-600">
                            <i class="fas fa-calendar-alt text-gray-400 mr-1"></i>
                            {{ $course->start_date->format('d M Y') }}
                            @if($course->end_date) - {{ $course->end_date->format('d M Y') }} @endif
                            @if($course->schedule_time) • {{ $course->schedule_time }} @endif
                        </p>
                        @if($course->is_online)
                        <p class="text-xs text-blue-600 mt-1"><i class="fas fa-video mr-1"></i>Online</p>
                        @elseif($course->location)
                        <p class="text-xs text-gray-500 mt-1"><i class="fas fa-map-marker-alt mr-1"></i>{{ $course->location }}</p>
                        @endif
                    </div>
                    @endif
                    
                    {{-- Instructor & Action --}}
                    <div class="flex items-center justify-between mt-4">
                        <div class="flex items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($course->instructor->name) }}&size=24&background=random" class="w-6 h-6 rounded-full">
                            <span class="text-xs text-gray-600">{{ $course->instructor->name }}</span>
                        </div>
                        <a href="{{ route('opac.elearning.show', $course->slug) }}" class="px-3 py-1.5 bg-violet-600 text-white text-xs font-semibold rounded-lg hover:bg-violet-700 transition">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        {{-- No Active Courses --}}
        <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-8 text-center border border-gray-200 mb-8">
            <div class="w-16 h-16 bg-gray-200 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-graduation-cap text-gray-400 text-2xl"></i>
            </div>
            <h3 class="font-bold text-gray-900 mb-2">Belum Ada Kelas Aktif</h3>
            <p class="text-gray-500 text-sm mb-4">Kelas-kelas baru akan segera tersedia. Pantau terus halaman ini!</p>
        </div>
        @endif

        {{-- Contact --}}
        <div class="bg-amber-50 rounded-xl p-4 border border-amber-200">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-lightbulb text-amber-600"></i>
                <span class="font-bold text-gray-900 text-sm">{{ __('opac.pages.e_learning.need_training') }}</span>
            </div>
            <p class="text-sm text-gray-600 mb-3">{{ __('opac.pages.e_learning.training_desc') }}</p>
            <a href="mailto:library@unida.gontor.ac.id" class="inline-flex items-center gap-1 text-amber-700 hover:text-amber-800 text-sm font-medium">
                <i class="fas fa-envelope"></i> library@unida.gontor.ac.id
            </a>
        </div>
    </section>
</x-opac.layout>
