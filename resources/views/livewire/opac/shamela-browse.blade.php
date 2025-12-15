<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-blue-50">
    
    {{-- Hero Section --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800">
        {{-- Decorative elements --}}
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full -ml-24 -mb-24"></div>
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"30\" height=\"30\" viewBox=\"0 0 30 30\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cpath d=\"M15 0C6.716 0 0 6.716 0 15c0 8.284 6.716 15 15 15 8.284 0 15-6.716 15-15 0-8.284-6.716-15-15-15zm0 27C8.373 27 3 21.627 3 15S8.373 3 15 3s12 5.373 12 12-5.373 12-12 12z\" fill=\"%23fff\" fill-opacity=\".03\"/%3E%3C/svg%3E')]"></div>
        
        <div class="relative max-w-7xl mx-auto px-4 py-12">
            {{-- Header --}}
            <div class="text-center mb-8">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur rounded-full text-sm text-white mb-4">
                    <i class="fas fa-book-quran"></i>
                    <span>Perpustakaan Digital Islam</span>
                </div>
                <h1 class="text-4xl lg:text-5xl font-bold text-white mb-4" dir="rtl">
                    المكتبة الشاملة
                </h1>
                <p class="text-blue-100 text-lg mb-2">Maktabah Shamela Library</p>
                
                @if($isAvailable)
                <div class="flex flex-wrap justify-center gap-4 mt-6">
                    <div class="px-4 py-2 bg-white/10 backdrop-blur rounded-xl text-white text-center">
                        <p class="text-2xl font-bold">{{ number_format($stats['total_books'] ?? 0) }}</p>
                        <p class="text-xs text-blue-200">Kitab</p>
                    </div>
                    <div class="px-4 py-2 bg-white/10 backdrop-blur rounded-xl text-white text-center">
                        <p class="text-2xl font-bold">{{ number_format($stats['total_authors'] ?? 0) }}</p>
                        <p class="text-xs text-blue-200">Ulama</p>
                    </div>
                    <div class="px-4 py-2 bg-white/10 backdrop-blur rounded-xl text-white text-center">
                        <p class="text-2xl font-bold">{{ number_format($stats['total_categories'] ?? 0) }}</p>
                        <p class="text-xs text-blue-200">Kategori</p>
                    </div>
                </div>
                @endif
            </div>
            
            {{-- Search Box --}}
            <div class="max-w-2xl mx-auto">
                <div class="relative">
                    <div class="absolute -inset-1 bg-gradient-to-r from-blue-400 via-white to-blue-400 rounded-full opacity-30 blur-lg"></div>
                    <div class="relative flex items-center bg-white rounded-full shadow-2xl overflow-hidden">
                        <div class="pl-5 pr-2">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input 
                            type="text" 
                            wire:model.live.debounce.400ms="search"
                            placeholder="ابحث عن كتاب... (مثل: صحيح البخاري، تفسير، فقه)" 
                            class="flex-1 px-2 py-4 text-gray-700 focus:outline-none bg-transparent placeholder-gray-400"
                            dir="rtl"
                        >
                        @if($search)
                        <button wire:click="$set('search', '')" class="px-3 text-gray-400 hover:text-gray-600 transition">
                            <i class="fas fa-times-circle"></i>
                        </button>
                        @endif
                        <button class="m-1.5 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-full shadow-lg">
                            بحث
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @if(!$isAvailable)
    {{-- Not Available State --}}
    <div class="max-w-4xl mx-auto px-4 py-16 text-center">
        <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-database text-3xl text-amber-600"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Database Shamela Belum Tersedia</h2>
        <p class="text-gray-600 mb-8">
            Database konten Maktabah Shamela sedang dalam proses instalasi. 
            Silakan hubungi administrator untuk informasi lebih lanjut.
        </p>
        <a href="{{ route('opac.search') }}" 
           class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition">
            <i class="fas fa-search"></i>
            Cari Koleksi Lainnya
        </a>
    </div>
    @else
    
    {{-- Main Content --}}
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            
            {{-- Sidebar Categories --}}
            <aside class="w-full lg:w-72 flex-shrink-0">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-4">
                    <div class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-blue-100">
                        <h3 class="font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-folder-tree text-blue-600"></i>
                            التصنيفات
                        </h3>
                    </div>
                    <div class="p-2 max-h-[60vh] overflow-y-auto">
                        <button 
                            wire:click="setCategory(null)"
                            class="w-full flex items-center gap-3 p-3 rounded-xl text-sm transition {{ !$categoryId ? 'bg-blue-100 text-blue-700 font-medium' : 'hover:bg-gray-50 text-gray-700' }}"
                        >
                            <i class="fas fa-layer-group"></i>
                            <span>الكل</span>
                            <span class="ml-auto px-2 py-0.5 bg-blue-200 text-emerald-800 text-xs rounded-full">{{ number_format($stats['total_books'] ?? 0) }}</span>
                        </button>
                        @foreach($categories as $cat)
                        <button 
                            wire:click="setCategory({{ $cat['id'] }})"
                            class="w-full flex items-center gap-3 p-3 rounded-xl text-sm transition {{ $categoryId === $cat['id'] ? 'bg-blue-100 text-blue-700 font-medium' : 'hover:bg-gray-50 text-gray-700' }}"
                            dir="rtl"
                        >
                            <span class="truncate">{{ $cat['name'] }}</span>
                        </button>
                        @endforeach
                    </div>
                </div>
            </aside>
            
            {{-- Main Content Area --}}
            <main class="flex-1 min-w-0">
                
                {{-- Active Filters --}}
                @if($search || $categoryId)
                <div class="flex items-center gap-2 mb-6 flex-wrap">
                    <span class="text-sm text-gray-500">Filter aktif:</span>
                    @if($search)
                    <span class="inline-flex items-center gap-2 px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm">
                        <i class="fas fa-search text-xs"></i>
                        "{{ $search }}"
                        <button wire:click="$set('search', '')" class="hover:text-emerald-900"><i class="fas fa-times"></i></button>
                    </span>
                    @endif
                    @if($categoryId)
                    <span class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm" dir="rtl">
                        <i class="fas fa-folder"></i>
                        {{ $this->categoryName }}
                        <button wire:click="setCategory(null)" class="hover:text-teal-900"><i class="fas fa-times"></i></button>
                    </span>
                    @endif
                    <button wire:click="clearFilters" class="text-sm text-red-500 hover:text-red-600">
                        <i class="fas fa-times-circle"></i> Hapus semua
                    </button>
                </div>
                @endif
                
                {{-- Results Header --}}
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900">
                        @if($search)
                            نتائج البحث: "{{ $search }}"
                        @elseif($categoryId)
                            {{ $this->categoryName }}
                        @else
                            كتب مختارة
                        @endif
                    </h2>
                    <p class="text-sm text-gray-500">
                        {{ number_format($this->results['total'] ?? 0) }} كتاب
                    </p>
                </div>
                
                {{-- Books List --}}
                <div class="space-y-3">
                    @forelse($this->results['results'] ?? [] as $book)
                    <a href="{{ route('opac.shamela.show', $book['id']) }}" 
                       class="flex gap-4 bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-lg hover:border-blue-200 transition group">
                        {{-- Cover --}}
                        <div class="flex-shrink-0 w-20 h-28 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg overflow-hidden">
                            <img src="{{ $book['cover'] }}" 
                                 alt="{{ $book['title'] }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                 loading="lazy"
                                 onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(mb_substr($book['title'], 0, 2)) }}&background=2563eb&color=fff&size=200'">
                        </div>
                        
                        {{-- Info --}}
                        <div class="flex-1 min-w-0" dir="rtl">
                            <h3 class="font-bold text-gray-900 text-base line-clamp-2 mb-1 group-hover:text-blue-600 transition">
                                {{ $book['title'] }}
                            </h3>
                            <p class="text-sm text-gray-600 truncate mb-2">
                                <i class="fas fa-user-edit text-blue-400 ml-1"></i>
                                {{ $book['author'] ?? 'المؤلف غير معروف' }}
                            </p>
                            
                            <div class="flex flex-wrap items-center gap-2 text-xs">
                                @if($book['category'] ?? null)
                                <span class="px-2 py-1 bg-indigo-50 text-indigo-600 rounded-full">
                                    <i class="fas fa-folder-open ml-1"></i>{{ $book['category'] }}
                                </span>
                                @endif
                                @if($book['hijri_year'] ?? null)
                                <span class="px-2 py-1 bg-blue-50 text-blue-600 rounded-full">
                                    <i class="fas fa-calendar-alt ml-1"></i>{{ $book['hijri_year'] }}
                                </span>
                                @endif
                                @if($book['has_pdf'] ?? false)
                                <span class="px-2 py-1 bg-rose-50 text-rose-600 rounded-full">
                                    <i class="fas fa-file-pdf ml-1"></i>PDF
                                </span>
                                @endif
                            </div>
                        </div>
                        
                        {{-- Arrow --}}
                        <div class="flex-shrink-0 flex items-center text-gray-300 group-hover:text-blue-500 transition">
                            <i class="fas fa-chevron-left text-lg"></i>
                        </div>
                    </a>
                    @empty
                    <div class="text-center py-12 bg-white rounded-xl">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-book text-2xl text-gray-400"></i>
                        </div>
                        <p class="text-gray-500">لا توجد نتائج</p>
                    </div>
                    @endforelse
                </div>
                
                {{-- Pagination --}}
                @if($this->totalPages > 1)
                <div class="flex justify-center gap-2 mt-8">
                    @if($this->getPage() > 1)
                    <a href="?page={{ $this->getPage() - 1 }}{{ $categoryId ? '&cat='.$categoryId : '' }}{{ $search ? '&search='.$search : '' }}" 
                       class="px-4 py-2 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                    @endif
                    
                    <span class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                        {{ $this->getPage() }} / {{ $this->totalPages }}
                    </span>
                    
                    @if($this->getPage() < $this->totalPages)
                    <a href="?page={{ $this->getPage() + 1 }}{{ $categoryId ? '&cat='.$categoryId : '' }}{{ $search ? '&search='.$search : '' }}" 
                       class="px-4 py-2 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    @endif
                </div>
                @endif
                
                {{-- Classic Books Section --}}
                @if(!$search && !$categoryId && !empty($classicBooks))
                <div class="mt-12">
                    <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <i class="fas fa-scroll text-amber-600"></i>
                        كتب كلاسيكية
                        <span class="text-sm font-normal text-gray-500">(الأقدم)</span>
                    </h2>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        @foreach($classicBooks as $book)
                        <a href="{{ route('opac.shamela.show', $book['id']) }}" 
                           class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl p-4 border border-amber-100 hover:border-amber-200 hover:shadow-md transition group">
                            <div class="flex items-start gap-3">
                                <img src="{{ $book['cover'] }}" 
                                     alt="{{ $book['title'] }}"
                                     class="w-12 h-16 object-cover rounded-lg shadow"
                                     loading="lazy"
                                     onerror="this.src='https://ui-avatars.com/api/?name=ك&background=d97706&color=fff&size=100'">
                                <div class="flex-1 min-w-0" dir="rtl">
                                    <h3 class="font-semibold text-gray-900 text-sm line-clamp-2 group-hover:text-amber-700 transition">
                                        {{ $book['title'] }}
                                    </h3>
                                    <p class="text-xs text-gray-500 truncate mt-1">{{ $book['author'] ?? '-' }}</p>
                                    <p class="text-xs text-amber-600 font-medium mt-1">{{ $book['hijri_year'] ?? '' }}</p>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
                
            </main>
        </div>
    </div>
    
    @endif
</div>
