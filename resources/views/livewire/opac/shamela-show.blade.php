<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-emerald-50">
    
    {{-- Hero Section --}}
    <div class="relative overflow-hidden">
        {{-- Background with blur --}}
        <div class="absolute inset-0 z-0">
            @if($book)
            <div class="absolute inset-0 bg-cover bg-center opacity-20" 
                 style="background-image: url('{{ $book['cover'] }}'); filter: blur(40px);"></div>
            @endif
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/90 via-teal-600/90 to-cyan-700/90"></div>
        </div>
        
        <div class="relative z-10 max-w-6xl mx-auto px-4 py-12">
            {{-- Breadcrumb --}}
            <nav class="mb-6">
                <ol class="flex items-center gap-2 text-sm text-emerald-100">
                    <li><a href="{{ route('opac.home') }}" class="hover:text-white transition">Beranda</a></li>
                    <li class="text-emerald-300">/</li>
                    <li><a href="{{ route('opac.search') }}" class="hover:text-white transition">Pencarian</a></li>
                    <li class="text-emerald-300">/</li>
                    <li class="text-white font-medium">Maktabah Shamela</li>
                </ol>
            </nav>
            
            @if($loading)
                {{-- Loading State --}}
                <div class="text-center py-16">
                    <div class="w-16 h-16 border-4 border-white/30 border-t-white rounded-full animate-spin mx-auto mb-4"></div>
                    <p class="text-emerald-100">Memuat data kitab...</p>
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
                {{-- Book Header --}}
                <div class="flex flex-col lg:flex-row gap-8">
                    {{-- Cover --}}
                    <div class="flex-shrink-0 flex justify-center lg:justify-start">
                        <div class="relative group">
                            <div class="absolute -inset-4 bg-gradient-to-br from-white/20 to-white/5 rounded-2xl blur-xl group-hover:from-white/30 transition"></div>
                            <img 
                                src="{{ $book['cover'] }}" 
                                alt="{{ $book['title'] }}"
                                class="relative w-48 h-64 object-cover rounded-xl shadow-2xl border-4 border-white/30"
                                onerror="this.src='https://ui-avatars.com/api/?name=كتاب&background=059669&color=fff&size=200'"
                            >
                            {{-- Shamela Badge --}}
                            <div class="absolute -top-3 -right-3 px-3 py-1 bg-emerald-500 text-white text-xs font-bold rounded-full shadow-lg">
                                <i class="fas fa-book-quran mr-1"></i> Shamela
                            </div>
                        </div>
                    </div>
                    
                    {{-- Book Info --}}
                    <div class="flex-1 text-center lg:text-right" dir="rtl">
                        <h1 class="text-3xl lg:text-4xl font-bold text-white mb-4 leading-relaxed">
                            {{ $book['title'] }}
                        </h1>
                        
                        @if($book['author'])
                        <p class="text-xl text-emerald-100 mb-4">
                            <i class="fas fa-user-pen ml-2"></i>
                            {{ $book['author'] }}
                        </p>
                        @endif
                        
                            @if($book['category'])
                        <span class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 backdrop-blur rounded-full text-white text-sm mb-3">
                            <i class="fas fa-folder"></i>
                            {{ $book['category'] }}
                        </span>
                        @endif
                        
                        {{-- Year Badge --}}
                        @if($book['hijri_year'] ?? null)
                        <span class="inline-flex items-center gap-2 px-4 py-2 bg-amber-400/30 backdrop-blur rounded-full text-white text-sm mr-2 mb-3">
                            <i class="fas fa-calendar"></i>
                            {{ $book['hijri_year'] }}
                        </span>
                        @endif
                        
                        {{-- Author Death Year --}}
                        @if($book['author_death'] ?? null)
                        <p class="text-emerald-200 text-sm mb-4">
                            <i class="fas fa-skull mr-1"></i> وفاة المؤلف: {{ $book['author_death'] }} هـ
                        </p>
                        @endif
                        
                        <div class="flex flex-wrap justify-center lg:justify-end gap-3 mt-6" dir="ltr">
                            {{-- Local Reader Button (Primary) --}}
                            @if($this->hasContent)
                            <button onclick="Livewire.dispatch('openReader', { bookId: {{ $book['id'] }} })"
                               class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-amber-400 to-orange-500 text-white font-semibold rounded-xl hover:from-amber-500 hover:to-orange-600 transition shadow-lg shadow-amber-500/30">
                                <i class="fas fa-book-open-reader text-lg"></i>
                                اقرأ الآن
                                <span class="text-xs bg-white/20 px-2 py-0.5 rounded-full">مجاني</span>
                            </button>
                            @endif
                            
                            {{-- PDF Download if available --}}
                            @if(!empty($book['pdf_links']))
                            <a href="{{ $book['pdf_links'][0] }}" 
                               target="_blank"
                               class="inline-flex items-center gap-2 px-6 py-3 bg-rose-500 text-white font-semibold rounded-xl hover:bg-rose-600 transition shadow-lg">
                                <i class="fas fa-file-pdf text-lg"></i>
                                تحميل PDF
                            </a>
                            @endif
                            
                            {{-- Shamela.ws Link --}}
                            <a href="{{ $book['url'] }}" 
                               target="_blank"
                               class="inline-flex items-center gap-2 px-6 py-3 bg-white/20 text-white font-semibold rounded-xl hover:bg-white/30 transition backdrop-blur">
                                <i class="fas fa-external-link-alt"></i>
                                Shamela.ws
                            </a>
                        </div>
                        
                        @if($isLocalDatabase)
                        <div class="mt-4 px-3 py-1.5 bg-white/10 backdrop-blur-sm rounded-lg inline-flex items-center gap-2 text-sm text-white/80">
                            <i class="fas fa-database text-emerald-300"></i>
                            <span>Data dari database lokal (8,425 kitab)</span>
                        </div>
                        @endif
                    </div>
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
                    <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-teal-50">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-list-ol text-emerald-600"></i>
                            فهرس الموضوعات
                        </h2>
                    </div>
                    <div class="p-5 max-h-96 overflow-y-auto" dir="rtl">
                        <ul class="space-y-2">
                            @foreach($book['toc'] as $item)
                            <li>
                                <a href="{{ $book['url'] }}/{{ $item['page'] }}" 
                                   target="_blank"
                                   class="flex items-center justify-between p-3 rounded-lg hover:bg-emerald-50 transition group">
                                    <span class="text-gray-700 group-hover:text-emerald-700">{{ $item['title'] }}</span>
                                    <span class="text-xs text-gray-400 group-hover:text-emerald-500">
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
                <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl p-6 border border-emerald-100">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-mosque text-emerald-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 mb-2">Tentang Maktabah Shamela</h3>
                            <p class="text-gray-600 text-sm leading-relaxed">
                                المكتبة الشاملة adalah perpustakaan digital terbesar untuk kitab-kitab Islam klasik dan kontemporer. 
                                Berisi ribuan kitab dalam berbagai bidang ilmu syar'i seperti hadits, fiqh, tafsir, sejarah, dan lainnya.
                            </p>
                            <a href="https://shamela.ws" target="_blank" class="inline-flex items-center gap-2 text-emerald-600 hover:text-emerald-700 text-sm mt-3">
                                Kunjungi Shamela.ws
                                <i class="fas fa-external-link-alt text-xs"></i>
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
                        <i class="fas fa-info-circle text-emerald-600"></i>
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
                            <dd class="text-emerald-600">Shamela.ws</dd>
                        </div>
                    </dl>
                </div>
                
                {{-- Action Card --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-hand-pointer text-emerald-600"></i>
                        Aksi
                    </h3>
                    <div class="space-y-3">
                        @if(!empty($book['pdf_links']))
                        <a href="{{ $book['pdf_links'][0] }}" target="_blank"
                           class="flex items-center justify-center gap-2 w-full py-3 bg-rose-500 text-white rounded-xl hover:bg-rose-600 transition font-medium">
                            <i class="fas fa-file-pdf"></i>
                            Download PDF
                        </a>
                        @endif
                        <a href="{{ $book['url'] }}" target="_blank"
                           class="flex items-center justify-center gap-2 w-full py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition font-medium">
                            <i class="fas fa-book-open"></i>
                            Baca Kitab
                        </a>
                        <a href="https://shamela.ws/category/{{ $book['category_id'] ?? '' }}" target="_blank"
                           class="flex items-center justify-center gap-2 w-full py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition {{ !($book['category_id'] ?? null) ? 'opacity-50 pointer-events-none' : '' }}">
                            <i class="fas fa-folder"></i>
                            Kitab Kategori Ini
                        </a>
                    </div>
                </div>
                
                {{-- Related Books --}}
                @if(!empty($relatedBooks))
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-books text-emerald-600"></i>
                        كتب ذات صلة
                    </h3>
                    <div class="space-y-3">
                        @foreach($relatedBooks as $related)
                        <a href="{{ route('opac.shamela.show', $related['id']) }}" 
                           class="flex items-start gap-3 p-3 rounded-xl hover:bg-emerald-50 transition group">
                            <img src="{{ $related['cover'] }}" 
                                 alt="{{ $related['title'] }}"
                                 class="w-10 h-14 object-cover rounded-lg shadow-sm border border-gray-100"
                                 onerror="this.src='https://ui-avatars.com/api/?name=ك&background=059669&color=fff&size=80'">
                            <div class="flex-1 min-w-0" dir="rtl">
                                <p class="text-sm font-medium text-gray-900 group-hover:text-emerald-600 truncate">
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
