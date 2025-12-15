<div>
    <!-- Hero Section - Full Width Gradient -->
    <section class="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 text-white relative overflow-hidden">
        <!-- Decorative Elements -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -mr-48 -mt-48"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/5 rounded-full -ml-32 -mb-32"></div>
        <div class="absolute top-1/2 left-1/4 w-32 h-32 bg-white/5 rounded-full"></div>
        
        <!-- Background Image Blur -->
        @if($news->image_url)
        <div class="absolute inset-0">
            <img src="{{ $news->image_url }}" class="w-full h-full object-cover opacity-10 blur-3xl scale-110">
        </div>
        @endif
        
        <div class="max-w-5xl mx-auto px-4 py-8 lg:py-16 relative z-10">
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-sm text-blue-200 mb-6">
                <a href="{{ route('opac.home') }}" class="hover:text-white transition">
                    <i class="fas fa-home"></i>
                </a>
                <i class="fas fa-chevron-right text-xs opacity-50"></i>
                <a href="{{ route('opac.news.index') }}" class="hover:text-white transition">Berita</a>
                <i class="fas fa-chevron-right text-xs opacity-50"></i>
                <span class="text-white/80 truncate max-w-[200px]">{{ Str::limit($news->title, 30) }}</span>
            </nav>
            
            <!-- Meta Info -->
            <div class="flex flex-wrap items-center gap-3 mb-4">
                @if($news->category)
                <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-sm font-medium">
                    {{ $news->category->name }}
                </span>
                @endif
                @if($news->is_featured)
                <span class="px-3 py-1 bg-amber-500/30 backdrop-blur-sm rounded-full text-sm font-medium text-amber-200">
                    <i class="fas fa-star mr-1"></i> Featured
                </span>
                @endif
                @if($news->is_pinned)
                <span class="px-3 py-1 bg-rose-500/30 backdrop-blur-sm rounded-full text-sm font-medium text-rose-200">
                    <i class="fas fa-thumbtack mr-1"></i> Pinned
                </span>
                @endif
            </div>
            
            <!-- Title -->
            <h1 class="text-2xl lg:text-4xl font-bold leading-tight mb-6">{{ $news->title }}</h1>
            
            <!-- Author & Date -->
            <div class="flex flex-wrap items-center gap-6 text-blue-200">
                @if($news->author)
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                        @if($news->author->photo)
                            <img src="{{ asset('storage/' . $news->author->photo) }}" class="w-full h-full object-cover rounded-full">
                        @else
                            <i class="fas fa-user text-sm"></i>
                        @endif
                    </div>
                    <div>
                        <p class="text-white font-medium text-sm">{{ $news->author->name }}</p>
                        <p class="text-xs">Penulis</p>
                    </div>
                </div>
                @endif
                
                <div class="flex items-center gap-2">
                    <i class="far fa-calendar"></i>
                    <span>{{ $news->published_at?->format('d F Y') }}</span>
                </div>
                
                <div class="flex items-center gap-2">
                    <i class="far fa-eye"></i>
                    <span>{{ number_format($news->views) }} views</span>
                </div>
                
                <div class="flex items-center gap-2">
                    <i class="far fa-clock"></i>
                    <span>{{ ceil(str_word_count(strip_tags($news->content)) / 200) }} menit baca</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="max-w-7xl mx-auto px-4 py-8 lg:py-12">
        <div class="flex flex-col lg:flex-row gap-8">
            
            <!-- Article Content -->
            <article class="flex-1 max-w-3xl">
                <!-- Featured Image -->
                @if($news->image_url)
                <div class="mb-8 -mt-16 lg:-mt-24 relative z-20">
                    <div class="aspect-video bg-white rounded-2xl shadow-2xl overflow-hidden ring-4 ring-white">
                        <img src="{{ $news->image_url }}" alt="{{ $news->title }}" class="w-full h-full object-cover">
                    </div>
                </div>
                @endif
                
                <!-- Excerpt -->
                @if($news->excerpt)
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 rounded-r-xl p-5 mb-8">
                    <p class="text-gray-700 text-lg leading-relaxed italic">{{ $news->excerpt }}</p>
                </div>
                @endif
                
                <!-- Content Body -->
                <div class="bg-white rounded-2xl shadow-lg shadow-gray-200/50 p-6 lg:p-10 mb-8">
                    <div class="prose prose-lg max-w-none
                        prose-headings:text-gray-900 prose-headings:font-bold
                        prose-h2:text-2xl prose-h2:mt-8 prose-h2:mb-4 prose-h2:pb-2 prose-h2:border-b prose-h2:border-gray-200
                        prose-h3:text-xl prose-h3:mt-6 prose-h3:mb-3
                        prose-p:text-gray-700 prose-p:leading-relaxed prose-p:mb-4
                        prose-a:text-blue-600 prose-a:no-underline hover:prose-a:underline
                        prose-strong:text-gray-900
                        prose-ul:my-4 prose-li:text-gray-700
                        prose-ol:my-4
                        prose-img:rounded-xl prose-img:shadow-lg
                        prose-blockquote:border-l-4 prose-blockquote:border-blue-500 prose-blockquote:bg-blue-50 prose-blockquote:rounded-r-xl prose-blockquote:py-1 prose-blockquote:italic
                    ">
                        {!! $news->content !!}
                    </div>
                </div>
                
                <!-- Tags & Share -->
                <div class="bg-white rounded-2xl shadow-lg shadow-gray-200/50 p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <!-- Tags -->
                        <div class="flex items-center gap-2">
                            <span class="text-gray-500 text-sm">Tags:</span>
                            @if($news->category)
                            <a href="{{ route('opac.news.index') }}?category={{ $news->category->id }}" class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm hover:bg-blue-200 transition">
                                {{ $news->category->name }}
                            </a>
                            @endif
                        </div>
                        
                        <!-- Share Buttons -->
                        <div class="flex items-center gap-2">
                            <span class="text-gray-500 text-sm">Share:</span>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" class="w-9 h-9 bg-blue-600 text-white rounded-lg flex items-center justify-center hover:bg-blue-700 transition">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($news->title) }}" target="_blank" class="w-9 h-9 bg-sky-500 text-white rounded-lg flex items-center justify-center hover:bg-sky-600 transition">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://wa.me/?text={{ urlencode($news->title . ' - ' . request()->url()) }}" target="_blank" class="w-9 h-9 bg-green-500 text-white rounded-lg flex items-center justify-center hover:bg-green-600 transition">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            <button onclick="navigator.clipboard.writeText('{{ request()->url() }}'); alert('Link copied!')" class="w-9 h-9 bg-gray-200 text-gray-600 rounded-lg flex items-center justify-center hover:bg-gray-300 transition">
                                <i class="fas fa-link"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Navigation -->
                <div class="mt-8 flex items-center justify-between">
                    <a href="{{ route('opac.news.index') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition font-medium">
                        <i class="fas fa-arrow-left"></i>
                        <span>Kembali ke Berita</span>
                    </a>
                </div>
            </article>
            
            <!-- Sidebar -->
            <aside class="lg:w-80 space-y-6">
                <!-- Related News -->
                @if($relatedNews->count() > 0)
                <div class="bg-white rounded-2xl shadow-lg shadow-gray-200/50 p-5">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-newspaper text-blue-500"></i> Berita Terkait
                    </h3>
                    <div class="space-y-4">
                        @foreach($relatedNews as $related)
                        <a href="{{ route('opac.news.show', $related->slug) }}" class="flex gap-3 group">
                            <div class="w-20 h-16 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                @if($related->image_url)
                                    <img src="{{ $related->image_url }}" alt="{{ $related->title }}" class="w-full h-full object-cover group-hover:scale-105 transition">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center">
                                        <i class="fas fa-newspaper text-blue-300"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-900 line-clamp-2 group-hover:text-blue-600 transition">{{ $related->title }}</h4>
                                <p class="text-xs text-gray-400 mt-1">{{ $related->published_at?->format('d M Y') }}</p>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    
                    <a href="{{ route('opac.news.index') }}" class="block text-center mt-4 py-2 text-blue-600 text-sm font-medium hover:text-blue-700">
                        Lihat Semua Berita <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                @endif
                
                <!-- E-Resources Promo -->
                <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-5 text-white">
                    <h3 class="font-bold mb-4 flex items-center gap-2">
                        <i class="fas fa-gem"></i> Jelajahi E-Resources
                    </h3>
                    
                    <div class="space-y-3">
                        <a href="{{ route('opac.shamela.index') }}" class="block p-3 bg-white/10 rounded-xl hover:bg-white/20 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-emerald-500/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-book-quran text-emerald-300"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-sm">Maktabah Shamela</h4>
                                    <p class="text-blue-200 text-xs">8,425 Kitab Klasik</p>
                                </div>
                            </div>
                        </a>
                        
                        <a href="{{ route('opac.journals.index') }}" class="block p-3 bg-white/10 rounded-xl hover:bg-white/20 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-amber-500/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-newspaper text-amber-300"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-sm">Jurnal UNIDA</h4>
                                    <p class="text-blue-200 text-xs">Artikel Ilmiah</p>
                                </div>
                            </div>
                        </a>
                        
                        <a href="{{ route('opac.search') }}?type=ethesis" class="block p-3 bg-white/10 rounded-xl hover:bg-white/20 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-purple-500/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-graduation-cap text-purple-300"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-sm">E-Thesis</h4>
                                    <p class="text-blue-200 text-xs">Tugas Akhir Mahasiswa</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                
                <!-- Contact CTA -->
                <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-5 text-white">
                    <h3 class="font-bold mb-2">Ada Pertanyaan?</h3>
                    <p class="text-emerald-100 text-sm mb-4">Hubungi kami untuk informasi lebih lanjut</p>
                    <a href="https://wa.me/6285183053934" target="_blank" class="block text-center py-3 bg-white text-emerald-600 rounded-xl font-bold text-sm hover:bg-emerald-50 transition">
                        <i class="fab fa-whatsapp mr-2"></i>Chat WhatsApp
                    </a>
                </div>
            </aside>
        </div>
    </section>
</div>
