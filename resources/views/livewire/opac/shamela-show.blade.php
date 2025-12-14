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
                        <span class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 backdrop-blur rounded-full text-white text-sm mb-6">
                            <i class="fas fa-folder"></i>
                            {{ $book['category'] }}
                        </span>
                        @endif
                        
                        <div class="flex flex-wrap justify-center lg:justify-end gap-3 mt-6" dir="ltr">
                            <a href="{{ $book['url'] }}" 
                               target="_blank"
                               class="inline-flex items-center gap-2 px-6 py-3 bg-white text-emerald-600 font-semibold rounded-xl hover:bg-emerald-50 transition shadow-lg">
                                <i class="fas fa-book-open"></i>
                                اقرأ الكتاب
                                <i class="fas fa-external-link-alt text-sm"></i>
                            </a>
                            <a href="{{ $book['url'] }}" 
                               target="_blank"
                               class="inline-flex items-center gap-2 px-6 py-3 bg-white/20 text-white font-semibold rounded-xl hover:bg-white/30 transition backdrop-blur">
                                <i class="fas fa-globe"></i>
                                Buka di Shamela.ws
                            </a>
                        </div>
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
                        <a href="{{ $book['url'] }}" target="_blank"
                           class="flex items-center justify-center gap-2 w-full py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition font-medium">
                            <i class="fas fa-book-open"></i>
                            Baca Kitab
                        </a>
                        <a href="https://shamela.ws/author/{{ $book['author_id'] }}" target="_blank"
                           class="flex items-center justify-center gap-2 w-full py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition {{ !$book['author_id'] ? 'opacity-50 pointer-events-none' : '' }}">
                            <i class="fas fa-user"></i>
                            Lihat Penulis Lain
                        </a>
                        <a href="https://shamela.ws/category/{{ $book['category_id'] }}" target="_blank"
                           class="flex items-center justify-center gap-2 w-full py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition {{ !$book['category_id'] ? 'opacity-50 pointer-events-none' : '' }}">
                            <i class="fas fa-folder"></i>
                            Kitab Kategori Ini
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
