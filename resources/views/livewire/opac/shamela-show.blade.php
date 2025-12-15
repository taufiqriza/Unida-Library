<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-blue-50">
    
    {{-- Hero Section --}}
    <div class="relative overflow-hidden">
        {{-- Background with blur --}}
        <div class="absolute inset-0 z-0">
            @if($book)
            <div class="absolute inset-0 bg-cover bg-center opacity-20" 
                 style="background-image: url('{{ $book['cover'] }}'); filter: blur(40px);"></div>
            @endif
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600/90 via-blue-700/90 to-indigo-800/90"></div>
        </div>
        
        <div class="relative z-10 max-w-6xl mx-auto px-4 py-12">
            {{-- Back Button & Breadcrumb --}}
            <div class="flex items-center justify-between mb-6">
                <button onclick="window.history.back()" 
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-xl transition backdrop-blur-sm">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </button>
                
                <nav>
                    <ol class="flex items-center gap-2 text-sm text-blue-100">
                        <li><a href="{{ route('opac.home') }}" class="hover:text-white transition">Beranda</a></li>
                        <li class="text-blue-300">/</li>
                        <li><a href="{{ route('opac.shamela.index') }}" class="hover:text-white transition">Shamela</a></li>
                        <li class="text-blue-300">/</li>
                        <li class="text-white font-medium truncate max-w-[200px]">{{ $book['title'] ?? 'Kitab' }}</li>
                    </ol>
                </nav>
            </div>
            
            @if($loading)
                {{-- Loading State --}}
                <div class="text-center py-16">
                    <div class="w-16 h-16 border-4 border-white/30 border-t-white rounded-full animate-spin mx-auto mb-4"></div>
                    <p class="text-blue-100">Memuat data kitab...</p>
                </div>
            @elseif($error)
                {{-- Error State --}}
                <div class="text-center py-16">
                    <div class="w-20 h-20 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-exclamation-triangle text-3xl text-yellow-300"></i>
                    </div>
                    <h2 class="text-xl font-bold text-white mb-2">{{ $error }}</h2>
                    <a href="{{ route('opac.search') }}" class="inline-flex items-center gap-2 mt-4 px-6 py-2 bg-white/20 text-white rounded-xl hover:bg-white/30 transition">
                        <i class="fas fa-arrow-left"></i>
                        Kembali ke Pencarian
                    </a>
                </div>
            @else
                {{-- Book Header - Centered Layout --}}
                <div class="text-center">
                    {{-- Cover & Badge --}}
                    <div class="relative inline-block mb-6">
                        <div class="absolute -inset-6 bg-gradient-to-br from-white/20 to-white/5 rounded-3xl blur-2xl"></div>
                        <img 
                            src="{{ $book['cover'] }}" 
                            alt="{{ $book['title'] }}"
                            class="relative w-40 h-56 lg:w-48 lg:h-64 object-cover rounded-2xl shadow-2xl border-4 border-white/40 mx-auto"
                            onerror="this.src='https://ui-avatars.com/api/?name=كتاب&background=2563eb&color=fff&size=200'"
                        >
                        {{-- Shamela Badge --}}
                        <div class="absolute -top-3 -right-3 px-3 py-1.5 bg-gradient-to-r from-amber-400 to-orange-500 text-white text-xs font-bold rounded-full shadow-lg flex items-center gap-1">
                            <i class="fas fa-book-quran"></i>
                            <span>Shamela</span>
                        </div>
                    </div>
                    
                    {{-- Title & Author --}}
                    <div dir="rtl" class="mb-6">
                        <h1 class="text-2xl lg:text-4xl font-bold text-white mb-3 leading-relaxed">
                            {{ $book['title'] }}
                        </h1>
                        
                        @if($book['author'])
                        <p class="text-lg lg:text-xl text-blue-100 mb-4">
                            <i class="fas fa-user-pen ml-2 text-blue-300"></i>
                            {{ $book['author'] }}
                        </p>
                        @endif
                        
                        @if($book['author_death'] ?? null)
                        <p class="text-blue-200 text-sm">
                            <i class="fas fa-star ml-1 text-amber-300"></i> 
                            توفي {{ $book['author_death'] }} هـ
                        </p>
                        @endif
                    </div>
                    
                    {{-- Stats Cards --}}
                    <div class="flex flex-wrap justify-center gap-3 mb-6">
                        @if($book['category'])
                        <div class="px-4 py-2.5 bg-white/15 backdrop-blur-sm rounded-xl border border-white/20">
                            <div class="flex items-center gap-2 text-white">
                                <i class="fas fa-folder-open text-blue-300"></i>
                                <span class="text-sm font-medium" dir="rtl">{{ $book['category'] }}</span>
                            </div>
                        </div>
                        @endif
                        
                        @if($book['hijri_year'] ?? null)
                        <div class="px-4 py-2.5 bg-white/15 backdrop-blur-sm rounded-xl border border-white/20">
                            <div class="flex items-center gap-2 text-white">
                                <i class="fas fa-calendar-alt text-amber-300"></i>
                                <span class="text-sm font-medium">{{ $book['hijri_year'] }}</span>
                            </div>
                        </div>
                        @endif
                        
                        @if($this->hasContent)
                        <div class="px-4 py-2.5 bg-emerald-500/30 backdrop-blur-sm rounded-xl border border-emerald-400/30">
                            <div class="flex items-center gap-2 text-white">
                                <i class="fas fa-check-circle text-emerald-300"></i>
                                <span class="text-sm font-medium">متوفر للقراءة</span>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    {{-- CTA Button --}}
                    <div class="mb-4">
                        @if($this->hasContent)
                        <button onclick="Livewire.dispatch('openReader', { bookId: {{ $book['id'] }} })"
                           class="inline-flex items-center gap-3 px-10 py-4 bg-gradient-to-r from-amber-400 via-orange-500 to-rose-500 text-white font-bold text-lg rounded-2xl hover:from-amber-500 hover:via-orange-600 hover:to-rose-600 transition-all duration-300 shadow-2xl shadow-orange-500/40 hover:shadow-orange-500/60 hover:scale-105 transform">
                            <i class="fas fa-book-open-reader text-2xl"></i>
                            <span dir="rtl">اقرأ الكتاب الآن</span>
                            <i class="fas fa-arrow-left"></i>
                        </button>
                        @else
                        <div class="inline-flex items-center gap-3 px-8 py-4 bg-white/10 text-white/70 rounded-2xl backdrop-blur">
                            <i class="fas fa-hourglass-half text-xl"></i>
                            <span dir="rtl">قريباً - الكتاب قيد المعالجة</span>
                        </div>
                        @endif
                    </div>
                    
                    {{-- Database Info --}}
                    @if($isLocalDatabase)
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-sm text-blue-100">
                        <i class="fas fa-database text-blue-300"></i>
                        <span>Database Lokal • 8,425 Kitab • 7+ Juta Halaman</span>
                    </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
    
    @if($book && !$loading && !$error)
    {{-- Content Section --}}
    <div class="max-w-6xl mx-auto px-4 py-8">
        <div class="grid lg:grid-cols-3 gap-8">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Table of Contents --}}
                @if(!empty($book['toc']))
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-list-ol text-blue-600"></i>
                            فهرس الموضوعات
                        </h2>
                    </div>
                    <div class="p-5 max-h-96 overflow-y-auto" dir="rtl">
                        <ul class="space-y-2">
                            @foreach($book['toc'] as $item)
                            <li>
                                <a href="{{ $book['url'] }}/{{ $item['page'] }}" 
                                   target="_blank"
                                   class="flex items-center justify-between p-3 rounded-lg hover:bg-blue-50 transition group">
                                    <span class="text-gray-700 group-hover:text-blue-700">{{ $item['title'] }}</span>
                                    <span class="text-xs text-gray-400 group-hover:text-blue-500">
                                        صفحة {{ $item['page'] }}
                                        <i class="fas fa-arrow-left mr-1"></i>
                                    </span>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif
                
                {{-- About Shamela --}}
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-100">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-mosque text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 mb-2">Koleksi Maktabah Shamela</h3>
                            <p class="text-gray-600 text-sm leading-relaxed">
                                Perpustakaan digital berisi <strong>8,425 kitab</strong> Islam klasik dan kontemporer 
                                dalam berbagai bidang ilmu syar'i seperti hadits, fiqh, tafsir, sejarah, dan lainnya.
                                Semua konten tersedia secara offline di database lokal.
                            </p>
                            <a href="{{ route('opac.shamela.index') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 text-sm mt-3">
                                <i class="fas fa-search"></i>
                                Jelajahi Semua Kitab
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Book Details Card --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-600"></i>
                        Detail Kitab
                    </h3>
                    <dl class="space-y-3" dir="rtl">
                        <div class="flex justify-between py-2 border-b border-gray-50">
                            <dt class="text-gray-500">رقم الكتاب</dt>
                            <dd class="text-gray-900 font-medium">#{{ $book['id'] }}</dd>
                        </div>
                        @if($book['author'])
                        <div class="flex justify-between py-2 border-b border-gray-50">
                            <dt class="text-gray-500">المؤلف</dt>
                            <dd class="text-gray-900">{{ $book['author'] }}</dd>
                        </div>
                        @endif
                        @if($book['category'])
                        <div class="flex justify-between py-2 border-b border-gray-50">
                            <dt class="text-gray-500">التصنيف</dt>
                            <dd class="text-gray-900">{{ $book['category'] }}</dd>
                        </div>
                        @endif
                        <div class="flex justify-between py-2">
                            <dt class="text-gray-500">المصدر</dt>
                            <dd class="text-blue-600">
                                <i class="fas fa-database mr-1 text-xs"></i>
                                Database Lokal
                            </dd>
                        </div>
                    </dl>
                </div>
                
                {{-- Action Card --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-hand-pointer text-blue-600"></i>
                        Aksi
                    </h3>
                    <div class="space-y-3">
                        @if($this->hasContent)
                        <button onclick="Livewire.dispatch('openReader', { bookId: {{ $book['id'] }} })"
                           class="flex items-center justify-center gap-2 w-full py-3 bg-gradient-to-r from-amber-400 to-orange-500 text-white rounded-xl hover:from-amber-500 hover:to-orange-600 transition font-medium shadow-lg shadow-amber-500/20">
                            <i class="fas fa-book-open-reader"></i>
                            Baca Kitab
                        </button>
                        @else
                        <div class="flex items-center justify-center gap-2 w-full py-3 bg-gray-100 text-gray-500 rounded-xl">
                            <i class="fas fa-clock"></i>
                            Segera Tersedia
                        </div>
                        @endif
                        
                        @if($book['category_id'] ?? null)
                        <a href="{{ route('opac.shamela.index') }}?cat={{ $book['category_id'] }}"
                           class="flex items-center justify-center gap-2 w-full py-3 bg-blue-50 text-blue-700 rounded-xl hover:bg-blue-100 transition">
                            <i class="fas fa-folder"></i>
                            Kitab Kategori Ini
                        </a>
                        @endif
                        
                        <a href="{{ route('opac.shamela.index') }}"
                           class="flex items-center justify-center gap-2 w-full py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition">
                            <i class="fas fa-search"></i>
                            Jelajahi Semua Kitab
                        </a>
                    </div>
                </div>
                
                {{-- Related Books --}}
                @if(!empty($relatedBooks))
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-books text-blue-600"></i>
                        كتب ذات صلة
                    </h3>
                    <div class="space-y-3">
                        @foreach($relatedBooks as $related)
                        <a href="{{ route('opac.shamela.show', $related['id']) }}" 
                           class="flex items-start gap-3 p-3 rounded-xl hover:bg-blue-50 transition group">
                            <img src="{{ $related['cover'] }}" 
                                 alt="{{ $related['title'] }}"
                                 class="w-10 h-14 object-cover rounded-lg shadow-sm border border-gray-100"
                                 onerror="this.src='https://ui-avatars.com/api/?name=ك&background=059669&color=fff&size=80'">
                            <div class="flex-1 min-w-0" dir="rtl">
                                <p class="text-sm font-medium text-gray-900 group-hover:text-blue-600 truncate">
                                    {{ $related['title'] }}
                                </p>
                                <p class="text-xs text-gray-500 truncate">{{ $related['author'] ?? '-' }}</p>
                                @if($related['has_pdf'] ?? false)
                                <span class="inline-flex items-center gap-1 mt-1 px-1.5 py-0.5 bg-rose-50 text-rose-600 text-[10px] rounded">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </span>
                                @endif
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
    
    {{-- Shamela Reader Modal Component --}}
    @livewire('opac.shamela-reader', ['bookId' => $bookId])
</div>
