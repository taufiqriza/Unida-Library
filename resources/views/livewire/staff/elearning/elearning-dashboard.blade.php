@section('title', 'E-Learning')

<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-gradient-to-br from-violet-500 to-purple-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-violet-500/30">
                <i class="fas fa-graduation-cap text-2xl"></i>
            </div>
            <div>
                <h1 class="text-xl lg:text-2xl font-bold text-gray-900">E-Learning</h1>
                <p class="text-sm text-gray-500">Kelola kelas, materi, dan peserta pelatihan</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            {{-- Branch Switcher for Super Admin --}}
            @if($isSuperAdmin)
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition shadow-sm">
                    <i class="fas fa-building text-violet-500"></i>
                    <span class="max-w-[150px] truncate">{{ $selectedBranchId ? $branches->firstWhere('id', $selectedBranchId)?->name : 'Semua Cabang' }}</span>
                    <i class="fas fa-chevron-down text-[10px] text-gray-400 transition-transform" :class="{ 'rotate-180': open }"></i>
                </button>
                <div x-show="open" @click.away="open = false" x-transition
                     class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-xl border border-gray-100 py-2 z-50 max-h-80 overflow-y-auto">
                    <div class="px-3 py-2 border-b border-gray-100">
                        <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold">Pilih Cabang</p>
                    </div>
                    <button wire:click="$set('selectedBranchId', null)" @click="open = false"
                            class="w-full px-3 py-2.5 text-left text-sm hover:bg-violet-50 transition flex items-center gap-3 {{ !$selectedBranchId ? 'bg-violet-50 text-violet-700' : 'text-gray-700' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ !$selectedBranchId ? 'bg-violet-100' : 'bg-gray-100' }}">
                            <i class="fas fa-globe text-xs {{ !$selectedBranchId ? 'text-violet-600' : 'text-gray-500' }}"></i>
                        </div>
                        <span class="flex-1">Semua Cabang</span>
                        @if(!$selectedBranchId)<i class="fas fa-check text-violet-600 text-xs"></i>@endif
                    </button>
                    @foreach($branches as $branch)
                    <button wire:click="$set('selectedBranchId', {{ $branch->id }})" @click="open = false"
                            class="w-full px-3 py-2.5 text-left text-sm hover:bg-violet-50 transition flex items-center gap-3 {{ $selectedBranchId == $branch->id ? 'bg-violet-50 text-violet-700' : 'text-gray-700' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $selectedBranchId == $branch->id ? 'bg-violet-100' : 'bg-gray-100' }}">
                            <i class="fas fa-building text-xs {{ $selectedBranchId == $branch->id ? 'text-violet-600' : 'text-gray-500' }}"></i>
                        </div>
                        <span class="flex-1 truncate">{{ $branch->name }}</span>
                        @if($selectedBranchId == $branch->id)<i class="fas fa-check text-violet-600 text-xs"></i>@endif
                    </button>
                    @endforeach
                </div>
            </div>
            @endif

            @if($canCreate)
            <a href="{{ route('staff.elearning.create') }}" wire:navigate
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-violet-600 to-purple-600 text-white rounded-xl font-semibold shadow-lg shadow-violet-500/25 hover:shadow-violet-500/40 transition">
                <i class="fas fa-plus"></i>
                <span>Buat Kelas Baru</span>
            </a>
            @endif
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-violet-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-book-open text-violet-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_courses'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500">Total Kelas</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['published_courses'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500">Dipublikasi</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_enrollments'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500">Total Peserta</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-amber-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_enrollments'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500">Menunggu</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-award text-emerald-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['completed_enrollments'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500">Lulus</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-rose-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-play-circle text-rose-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['active_courses'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500">Aktif</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-2 border-b border-gray-200">
        <button wire:click="$set('tab', 'courses')" class="px-6 py-3 font-semibold text-sm transition border-b-2 {{ $tab === 'courses' ? 'text-violet-600 border-violet-600' : 'text-gray-500 border-transparent hover:text-gray-700' }}">
            <i class="fas fa-book-open mr-2"></i>Daftar Kelas
        </button>
        <button wire:click="$set('tab', 'enrollments')" class="px-6 py-3 font-semibold text-sm transition border-b-2 {{ $tab === 'enrollments' ? 'text-violet-600 border-violet-600' : 'text-gray-500 border-transparent hover:text-gray-700' }}">
            <i class="fas fa-user-plus mr-2"></i>Pendaftaran
            @if(($stats['pending_enrollments'] ?? 0) > 0)
            <span class="ml-1 px-2 py-0.5 bg-amber-100 text-amber-700 text-xs rounded-full">{{ $stats['pending_enrollments'] }}</span>
            @endif
        </button>
        <button wire:click="$set('tab', 'categories')" class="px-6 py-3 font-semibold text-sm transition border-b-2 {{ $tab === 'categories' ? 'text-violet-600 border-violet-600' : 'text-gray-500 border-transparent hover:text-gray-700' }}">
            <i class="fas fa-tags mr-2"></i>Kategori
        </button>
    </div>

    @if($tab === 'courses')
    {{-- Filters --}}
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari kelas..." 
                       class="w-full pl-11 pr-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
            </div>
            <select wire:model.live="statusFilter" class="px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500">
                <option value="">Semua Status</option>
                <option value="draft">Draft</option>
                <option value="published">Dipublikasi</option>
                <option value="archived">Diarsipkan</option>
            </select>
            <select wire:model.live="categoryFilter" class="px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Course Grid --}}
    @if($courses->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        @foreach($courses as $course)
        <a href="{{ route('staff.elearning.show', $course->id) }}" wire:navigate
           class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:border-violet-200 transition-all flex flex-col">
            {{-- Thumbnail --}}
            <div class="relative h-36 bg-gradient-to-br from-violet-500 to-purple-600 overflow-hidden flex-shrink-0">
                @if($course->thumbnail)
                <img src="{{ Storage::url($course->thumbnail) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                @else
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fas fa-graduation-cap text-white/30 text-5xl"></i>
                </div>
                @endif
                {{-- Status Badge --}}
                <div class="absolute top-2 left-2">
                    @if($course->status === 'published')
                    <span class="px-2 py-0.5 bg-green-500 text-white text-[10px] font-semibold rounded">Aktif</span>
                    @elseif($course->status === 'draft')
                    <span class="px-2 py-0.5 bg-gray-500 text-white text-[10px] font-semibold rounded">Draft</span>
                    @else
                    <span class="px-2 py-0.5 bg-red-500 text-white text-[10px] font-semibold rounded">Arsip</span>
                    @endif
                </div>
                {{-- Level Badge --}}
                <div class="absolute top-2 right-2">
                    <span class="px-2 py-0.5 bg-white/90 backdrop-blur text-gray-700 text-[10px] font-semibold rounded capitalize">{{ $course->level }}</span>
                </div>
            </div>
            
            {{-- Content --}}
            <div class="p-4 flex flex-col flex-1">
                {{-- Labels --}}
                <div class="flex items-center gap-1.5 mb-2 h-5 overflow-hidden">
                    @if($course->category)
                    <span class="text-[10px] font-medium text-violet-600 bg-violet-50 px-1.5 py-0.5 rounded truncate max-w-[80px]" title="{{ $course->category->name }}">{{ $course->category->name }}</span>
                    @endif
                    <span class="text-[10px] font-medium {{ $course->branch_id ? 'text-blue-600 bg-blue-50' : 'text-emerald-600 bg-emerald-50' }} px-1.5 py-0.5 rounded truncate max-w-[80px]" title="{{ $course->branch?->name ?? 'Global' }}">
                        {{ $course->branch?->name ?? 'Global' }}
                    </span>
                </div>
                
                {{-- Title --}}
                <h3 class="font-bold text-gray-900 text-sm line-clamp-2 h-10 group-hover:text-violet-600 transition">{{ $course->title }}</h3>
                
                {{-- Description --}}
                <p class="text-xs text-gray-500 mt-1 line-clamp-2 h-8">{{ $course->description ?: '-' }}</p>
                
                {{-- Meta --}}
                <div class="flex items-center gap-3 mt-auto pt-3 border-t border-gray-100 text-[10px] text-gray-500">
                    <span class="flex items-center gap-1">
                        <i class="fas fa-layer-group text-violet-400"></i>
                        {{ $course->modules_count }}
                    </span>
                    <span class="flex items-center gap-1">
                        <i class="fas fa-file-alt text-blue-400"></i>
                        {{ $course->materials_count }}
                    </span>
                    <span class="flex items-center gap-1">
                        <i class="fas fa-users text-green-400"></i>
                        {{ $course->enrollments_count }}
                    </span>
                </div>
                
                {{-- Instructor --}}
                <div class="flex items-center gap-2 mt-2">
                    <img src="{{ $course->instructor->getAvatarUrl(20) }}" class="w-5 h-5 rounded-full object-cover">
                    <span class="text-[10px] text-gray-600 truncate">{{ $course->instructor->name }}</span>
                </div>
            </div>
        </a>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $courses->links() }}
    </div>
    @else
    <div class="bg-white rounded-2xl p-12 text-center shadow-sm border border-gray-100">
        <div class="w-20 h-20 bg-violet-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-graduation-cap text-violet-400 text-3xl"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-2">Belum Ada Kelas</h3>
        <p class="text-gray-500 mb-6">Mulai buat kelas pertama untuk memulai e-learning</p>
        <a href="{{ route('staff.elearning.create') }}" wire:navigate
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-violet-600 text-white rounded-xl font-semibold hover:bg-violet-700 transition">
            <i class="fas fa-plus"></i>
            Buat Kelas Baru
        </a>
    </div>
    @endif
    @endif

    @if($tab === 'enrollments')
    {{-- Pending Enrollments --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-900">Pendaftaran Menunggu Persetujuan</h3>
        </div>
        @if(isset($pendingEnrollments) && $pendingEnrollments->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Peserta</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Kelas</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Tanggal Daftar</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($pendingEnrollments as $enrollment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($enrollment->member->name) }}&size=32&background=random" class="w-8 h-8 rounded-full">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $enrollment->member->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $enrollment->member->member_id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900">{{ $enrollment->course->title }}</p>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ $enrollment->created_at->format('d M Y H:i') }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <button wire:click="approveEnrollment({{ $enrollment->id }})" class="px-3 py-1.5 bg-green-100 text-green-700 rounded-lg text-xs font-semibold hover:bg-green-200 transition">
                                    <i class="fas fa-check mr-1"></i> Setujui
                                </button>
                                <button wire:click="rejectEnrollment({{ $enrollment->id }})" class="px-3 py-1.5 bg-red-100 text-red-700 rounded-lg text-xs font-semibold hover:bg-red-200 transition">
                                    <i class="fas fa-times mr-1"></i> Tolak
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-8 text-center">
            <i class="fas fa-inbox text-gray-300 text-4xl mb-3"></i>
            <p class="text-gray-500">Tidak ada pendaftaran yang menunggu persetujuan</p>
        </div>
        @endif
    </div>
    @endif

    @if($tab === 'categories')
    {{-- Categories Management --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-900">Kategori Kelas</h3>
            <button wire:click="$dispatch('openCategoryModal')" class="px-4 py-2 bg-violet-100 text-violet-700 rounded-xl text-sm font-semibold hover:bg-violet-200 transition">
                <i class="fas fa-plus mr-1"></i> Tambah Kategori
            </button>
        </div>
        <div class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($categories as $cat)
            <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: {{ $cat->color ?? '#8B5CF6' }}20">
                    <i class="fas {{ $cat->icon ?? 'fa-folder' }}" style="color: {{ $cat->color ?? '#8B5CF6' }}"></i>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-900">{{ $cat->name }}</p>
                    <p class="text-xs text-gray-500">{{ $cat->courses->count() }} kelas</p>
                </div>
                <button class="p-2 text-gray-400 hover:text-gray-600">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
