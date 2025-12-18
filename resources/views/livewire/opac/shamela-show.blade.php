<div class="min-h-screen bg-gradient-to-br from-stone-50 via-amber-50/30 to-orange-50/20">
    
    {{-- Hero Section with Islamic Pattern --}}
    <div class="relative overflow-hidden">
        {{-- Decorative Background --}}
        <div class="absolute inset-0 z-0">
            {{-- Main Gradient --}}
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-800 via-teal-800 to-cyan-900"></div>
            
            {{-- Islamic Pattern Overlay --}}
            <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;1&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
            
            {{-- Decorative Circles --}}
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-gradient-to-br from-amber-400/20 to-orange-500/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-12 -left-12 w-72 h-72 bg-gradient-to-br from-teal-400/20 to-emerald-500/10 rounded-full blur-3xl"></div>
        </div>
        
        <div class="relative z-10 max-w-6xl mx-auto px-4 py-8 lg:py-12">
            {{-- Navigation Bar --}}
            <div class="flex items-center justify-between mb-8">
                <button onclick="window.history.back()" 
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl transition backdrop-blur-sm border border-white/10">
                    <i class="fas fa-arrow-left"></i>
                    <span class="hidden sm:inline">Kembali</span>
                </button>
                
                <nav class="hidden sm:block">
                    <ol class="flex items-center gap-2 text-sm text-teal-100">
                        <li><a href="{{ route('opac.home') }}" class="hover:text-white transition"><i class="fas fa-home"></i></a></li>
                        <li class="text-teal-400">/</li>
                        <li><a href="{{ route('opac.shamela.index') }}" class="hover:text-white transition">المكتبة الشاملة</a></li>
                        <li class="text-teal-400">/</li>
                        <li class="text-white font-medium truncate max-w-[150px] lg:max-w-[250px]" dir="rtl">{{ $book['title'] ?? 'كتاب' }}</li>
                    </ol>
                </nav>
                
                {{-- Share Button --}}
                <button onclick="navigator.share ? navigator.share({title: '{{ $book['title'] ?? '' }}', url: window.location.href}) : navigator.clipboard.writeText(window.location.href)"
                        class="p-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl transition backdrop-blur-sm border border-white/10">
                    <i class="fas fa-share-alt"></i>
                </button>
            </div>
            
            @if($loading)
                {{-- Loading State --}}
                <div class="text-center py-20">
                    <div class="relative inline-block">
                        <div class="w-20 h-20 border-4 border-amber-300/30 border-t-amber-300 rounded-full animate-spin"></div>
                        <i class="fas fa-book-quran absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-2xl text-amber-300"></i>
                    </div>
                    <p class="text-teal-100 mt-4">جارٍ تحميل بيانات الكتاب...</p>
                </div>
            @elseif($error)
                {{-- Error State --}}
                <div class="text-center py-20">
                    <div class="w-24 h-24 bg-white/10 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-6 border border-white/10">
                        <i class="fas fa-exclamation-triangle text-4xl text-amber-300"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-3">{{ $error }}</h2>
                    <p class="text-teal-200 mb-6">لم نتمكن من العثور على الكتاب المطلوب</p>
                    <a href="{{ route('opac.shamela.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white/20 text-white rounded-xl hover:bg-white/30 transition backdrop-blur-sm">
                        <i class="fas fa-arrow-left"></i>
                        العودة إلى المكتبة
                    </a>
                </div>
            @else
                {{-- Book Header --}}
                <div class="flex flex-col lg:flex-row items-center lg:items-start gap-8">
                    {{-- Cover Section --}}
                    <div class="relative flex-shrink-0">
                        {{-- Glow Effect --}}
                        <div class="absolute -inset-4 bg-gradient-to-br from-amber-400/30 via-orange-400/20 to-rose-400/10 rounded-3xl blur-2xl"></div>
                        
                        {{-- Book Cover --}}
                        <div class="relative">
                            <img src="{{ $book['cover'] }}" 
                                 alt="{{ $book['title'] }}"
                                 class="w-44 h-60 lg:w-52 lg:h-72 object-cover rounded-2xl shadow-2xl border-4 border-white/30"
                                 onerror="this.src='https://ui-avatars.com/api/?name=كتاب&background=059669&color=fff&size=200'">
                            
                            {{-- Shamela Badge --}}
                            <div class="absolute -top-3 -right-3 px-3 py-1.5 bg-gradient-to-r from-amber-400 via-orange-400 to-rose-400 text-white text-xs font-bold rounded-full shadow-xl flex items-center gap-1.5 border border-white/20">
                                <i class="fas fa-book-quran"></i>
                                <span>الشاملة</span>
                            </div>
                            
                            {{-- Available Badge --}}
                            @if($this->hasContent)
                            <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 px-4 py-1 bg-emerald-500 text-white text-xs font-bold rounded-full shadow-lg flex items-center gap-1.5">
                                <i class="fas fa-check-circle"></i>
                                <span>متوفر للقراءة</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Book Info --}}
                    <div class="flex-1 text-center lg:text-right" dir="rtl">
                        {{-- Category Tag --}}
                        @if($book['category'])
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-white/10 backdrop-blur-sm rounded-full text-teal-100 text-sm mb-4 border border-white/10">
                            <i class="fas fa-folder-open text-amber-300"></i>
                            <span>{{ $book['category'] }}</span>
                        </div>
                        @endif
                        
                        {{-- Title --}}
                        <h1 class="text-3xl lg:text-4xl xl:text-5xl font-bold text-white mb-4 leading-relaxed font-arabic">
                            {{ $book['title'] }}
                        </h1>
                        
                        {{-- Author --}}
                        @if($book['author'])
                        <div class="flex items-center justify-center lg:justify-end gap-3 mb-6">
                            <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full flex items-center justify-center text-white shadow-lg">
                                <i class="fas fa-user-pen"></i>
                            </div>
                            <div>
                                <p class="text-lg lg:text-xl text-white font-medium">{{ $book['author'] }}</p>
                                @if($book['author_death'] ?? null)
                                <p class="text-teal-200 text-sm">المتوفى سنة {{ $book['author_death'] }} هـ</p>
                                @endif
                            </div>
                        </div>
                        @endif
                        
                        {{-- Stats Row --}}
                        <div class="flex flex-wrap justify-center lg:justify-end gap-4 mb-8">
                            <div class="flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-sm rounded-xl border border-white/10">
                                <i class="fas fa-hashtag text-amber-300"></i>
                                <span class="text-white font-medium">#{{ $book['id'] }}</span>
                            </div>
                            
                            @if($book['hijri_year'] ?? null)
                            <div class="flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-sm rounded-xl border border-white/10">
                                <i class="fas fa-moon text-amber-300"></i>
                                <span class="text-white font-medium">{{ $book['hijri_year'] }} هـ</span>
                            </div>
                            @endif
                            
                            <div class="flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-sm rounded-xl border border-white/10">
                                <i class="fas fa-database text-teal-300"></i>
                                <span class="text-white font-medium">قاعدة محلية</span>
                            </div>
                        </div>
                        
                        {{-- CTA Buttons --}}
                        <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-end gap-4">
                            @if($this->hasContent)
                            <button onclick="Livewire.dispatch('openReader', { bookId: {{ $book['id'] }} })"
                               class="group relative inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-amber-400 via-orange-500 to-rose-500 text-white font-bold text-lg rounded-2xl hover:from-amber-500 hover:via-orange-600 hover:to-rose-600 transition-all duration-300 shadow-2xl shadow-orange-500/40 hover:shadow-orange-500/60 hover:scale-105 transform overflow-hidden">
                                {{-- Shine Effect --}}
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                                <i class="fas fa-book-open-reader text-2xl"></i>
                                <span>اقرأ الكتاب</span>
                                <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                            </button>
                            @else
                            <div class="inline-flex items-center gap-3 px-8 py-4 bg-white/10 text-white/70 rounded-2xl backdrop-blur-sm border border-white/10">
                                <i class="fas fa-hourglass-half text-xl animate-pulse"></i>
                                <span>قريباً - الكتاب قيد المعالجة</span>
                            </div>
                            @endif
                            
                            @if($book['category_id'] ?? null)
                            <a href="{{ route('opac.shamela.index') }}?cat={{ $book['category_id'] }}"
                               class="inline-flex items-center gap-2 px-6 py-4 bg-white/10 text-white rounded-2xl hover:bg-white/20 transition backdrop-blur-sm border border-white/10">
                                <i class="fas fa-layer-group"></i>
                                <span>كتب مشابهة</span>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        {{-- Wave Divider --}}
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
                <path d="M0 120L60 110C120 100 240 80 360 75C480 70 600 80 720 85C840 90 960 90 1080 85C1200 80 1320 70 1380 65L1440 60V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="currentColor" class="text-stone-50"/>
            </svg>
        </div>
    </div>
    
    @if($book && !$loading && !$error)
    {{-- Content Section --}}
    <div class="max-w-6xl mx-auto px-4 py-8 lg:py-12">
        <div class="grid lg:grid-cols-3 gap-8">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-8">
                
                {{-- Table of Contents --}}
                @if(!empty($book['toc']))
                <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-emerald-50 via-teal-50 to-cyan-50">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center text-white shadow-lg">
                                <i class="fas fa-list-ol text-lg"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900" dir="rtl">فهرس الموضوعات</h2>
                                <p class="text-sm text-gray-500">{{ count($book['toc']) }} موضوع</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 max-h-[500px] overflow-y-auto" dir="rtl">
                        <div class="space-y-1">
                            @foreach($book['toc'] as $index => $item)
                            <button onclick="Livewire.dispatch('openReader', { bookId: {{ $book['id'] }}, page: {{ $item['page'] }} })"
                               class="w-full flex items-center justify-between p-4 rounded-2xl hover:bg-gradient-to-r hover:from-emerald-50 hover:to-teal-50 transition-all group border border-transparent hover:border-emerald-100">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 bg-gray-100 group-hover:bg-emerald-100 text-gray-500 group-hover:text-emerald-600 rounded-lg flex items-center justify-center text-sm font-bold transition">
                                        {{ $index + 1 }}
                                    </span>
                                    <span class="text-gray-700 group-hover:text-emerald-700 font-medium transition">{{ $item['title'] }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-gray-400 group-hover:text-emerald-500 transition">
                                    <span class="text-sm">ص {{ $item['page'] }}</span>
                                    <i class="fas fa-book-open text-xs opacity-0 group-hover:opacity-100 transition"></i>
                                </div>
                            </button>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
                
                {{-- About Shamela Collection --}}
                <div class="relative bg-gradient-to-br from-emerald-600 via-teal-600 to-cyan-700 rounded-3xl p-8 overflow-hidden">
                    {{-- Pattern --}}
                    <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;40&quot; height=&quot;40&quot; viewBox=&quot;0 0 40 40&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;1&quot;%3E%3Cpath d=&quot;M20 0L0 20h20V0zm0 40V20H0l20 20zm0-40v20h20L20 0zm0 40L40 20H20v20z&quot;/%3E%3C/g%3E%3C/svg%3E');"></div>
                    
                    <div class="relative flex items-start gap-6">
                        <div class="hidden sm:flex w-20 h-20 bg-white/20 backdrop-blur-sm rounded-2xl items-center justify-center flex-shrink-0 border border-white/20">
                            <i class="fas fa-mosque text-4xl text-white"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold text-white mb-3">المكتبة الشاملة</h3>
                            <p class="text-teal-100 leading-relaxed mb-4">
                                مكتبة رقمية شاملة تحتوي على <strong class="text-amber-300">8,425 كتاباً</strong> إسلامياً في مختلف العلوم الشرعية، 
                                من الحديث والفقه والتفسير والتاريخ وغيرها، مع أكثر من <strong class="text-amber-300">7 ملايين صفحة</strong> متاحة للقراءة.
                            </p>
                            <a href="{{ route('opac.shamela.index') }}" 
                               class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/20 hover:bg-white/30 text-white rounded-xl transition backdrop-blur-sm border border-white/20">
                                <i class="fas fa-compass"></i>
                                <span>تصفح جميع الكتب</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Book Details Card --}}
                <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
                    <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-amber-50 to-orange-50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center text-white">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <h3 class="font-bold text-gray-900">معلومات الكتاب</h3>
                        </div>
                    </div>
                    <div class="p-5" dir="rtl">
                        <dl class="space-y-4">
                            <div class="flex items-center justify-between py-3 border-b border-gray-50">
                                <dt class="flex items-center gap-2 text-gray-500">
                                    <i class="fas fa-barcode text-amber-400 w-5"></i>
                                    رقم الكتاب
                                </dt>
                                <dd class="text-gray-900 font-bold bg-amber-50 px-3 py-1 rounded-lg">#{{ $book['id'] }}</dd>
                            </div>
                            @if($book['author'])
                            <div class="flex items-center justify-between py-3 border-b border-gray-50">
                                <dt class="flex items-center gap-2 text-gray-500">
                                    <i class="fas fa-user-pen text-emerald-400 w-5"></i>
                                    المؤلف
                                </dt>
                                <dd class="text-gray-900 font-medium text-left max-w-[150px] truncate">{{ $book['author'] }}</dd>
                            </div>
                            @endif
                            @if($book['category'])
                            <div class="flex items-center justify-between py-3 border-b border-gray-50">
                                <dt class="flex items-center gap-2 text-gray-500">
                                    <i class="fas fa-folder-open text-blue-400 w-5"></i>
                                    التصنيف
                                </dt>
                                <dd class="text-gray-900 font-medium max-w-[150px] truncate">{{ $book['category'] }}</dd>
                            </div>
                            @endif
                            <div class="flex items-center justify-between py-3">
                                <dt class="flex items-center gap-2 text-gray-500">
                                    <i class="fas fa-database text-teal-400 w-5"></i>
                                    المصدر
                                </dt>
                                <dd class="inline-flex items-center gap-1.5 px-3 py-1 bg-teal-50 text-teal-700 rounded-lg text-sm font-medium">
                                    <span class="w-2 h-2 bg-teal-500 rounded-full animate-pulse"></span>
                                    قاعدة محلية
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
                
                {{-- Quick Actions --}}
                <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 p-5">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-bolt text-amber-500"></i>
                        إجراءات سريعة
                    </h3>
                    <div class="space-y-3">
                        @if($this->hasContent)
                        <button onclick="Livewire.dispatch('openReader', { bookId: {{ $book['id'] }} })"
                           class="flex items-center justify-center gap-3 w-full py-4 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-2xl hover:from-emerald-600 hover:to-teal-700 transition font-bold shadow-lg shadow-emerald-500/25">
                            <i class="fas fa-book-open-reader text-xl"></i>
                            <span>ابدأ القراءة الآن</span>
                        </button>
                        @else
                        <div class="flex items-center justify-center gap-2 w-full py-4 bg-gray-100 text-gray-500 rounded-2xl">
                            <i class="fas fa-clock animate-pulse"></i>
                            <span>قريباً</span>
                        </div>
                        @endif
                        
                        <a href="{{ route('opac.shamela.index') }}"
                           class="flex items-center justify-center gap-2 w-full py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition">
                            <i class="fas fa-search"></i>
                            <span>البحث في المكتبة</span>
                        </a>
                    </div>
                </div>
                
                {{-- Related Books --}}
                @if(!empty($relatedBooks))
                <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
                    <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white">
                                <i class="fas fa-layer-group"></i>
                            </div>
                            <h3 class="font-bold text-gray-900" dir="rtl">كتب ذات صلة</h3>
                        </div>
                    </div>
                    <div class="p-4 space-y-2">
                        @foreach($relatedBooks as $related)
                        <a href="{{ route('opac.shamela.show', $related['id']) }}" 
                           class="flex items-center gap-4 p-3 rounded-2xl hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition group border border-transparent hover:border-blue-100">
                            <img src="{{ $related['cover'] }}" 
                                 alt="{{ $related['title'] }}"
                                 class="w-12 h-16 object-cover rounded-xl shadow-md border border-gray-100 group-hover:scale-105 transition"
                                 onerror="this.src='https://ui-avatars.com/api/?name=ك&background=059669&color=fff&size=80'">
                            <div class="flex-1 min-w-0" dir="rtl">
                                <p class="font-medium text-gray-900 group-hover:text-blue-600 truncate transition">
                                    {{ $related['title'] }}
                                </p>
                                <p class="text-sm text-gray-500 truncate">{{ $related['author'] ?? '-' }}</p>
                            </div>
                            <i class="fas fa-chevron-left text-gray-300 group-hover:text-blue-500 transition opacity-0 group-hover:opacity-100"></i>
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
    
    {{-- Arabic Font --}}
    <style>
        .font-arabic {
            font-family: 'Amiri', 'Traditional Arabic', 'Scheherazade New', serif;
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap" rel="stylesheet">
</div>
