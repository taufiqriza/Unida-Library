<x-opac.layout :title="$news->title">
    <div class="lg:max-w-7xl lg:mx-auto lg:px-4 lg:py-8">
        
        {{-- Hero Section --}}
        <div class="relative bg-gradient-to-b from-emerald-600 via-emerald-700 to-teal-700 lg:rounded-2xl lg:overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                @if($news->image_url)
                    <img src="{{ $news->image_url }}" class="w-full h-full object-cover opacity-20 blur-2xl scale-110">
                @endif
            </div>
            
            {{-- Back Button (Mobile) --}}
            <div class="relative z-10 px-4 pt-4 lg:hidden">
                <a href="{{ route('opac.search') }}?type=news" class="inline-flex items-center gap-2 text-white/80 hover:text-white text-sm">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
            
            {{-- Breadcrumb (Desktop) --}}
            <nav class="hidden lg:block relative z-10 px-6 pt-6 text-sm text-white/70">
                <a href="{{ route('opac.home') }}" class="hover:text-white">Beranda</a>
                <span class="mx-2">/</span>
                <a href="{{ route('opac.search') }}?type=news" class="hover:text-white">Berita</a>
                <span class="mx-2">/</span>
                <span class="text-white">{{ Str::limit($news->title, 40) }}</span>
            </nav>
            
            {{-- Image & Title --}}
            <div class="relative z-10 px-4 pb-6 pt-4 lg:p-8">
                <div class="flex flex-col items-center lg:flex-row lg:items-end gap-4 lg:gap-8">
                    @if($news->image_url)
                    <div class="w-full lg:w-72 flex-shrink-0">
                        <div class="aspect-video bg-white rounded-xl shadow-2xl overflow-hidden ring-4 ring-white/20">
                            <img src="{{ $news->image_url }}" alt="{{ $news->title }}" class="w-full h-full object-cover">
                        </div>
                    </div>
                    @endif
                    
                    <div class="text-center lg:text-left flex-1">
                        @if($news->category)
                        <span class="inline-block px-3 py-1 bg-white/20 text-white text-xs font-semibold rounded-full mb-2">{{ $news->category->name }}</span>
                        @endif
                        <h1 class="text-xl lg:text-3xl font-bold text-white leading-tight">{{ $news->title }}</h1>
                        
                        <div class="flex items-center justify-center lg:justify-start gap-4 mt-4 text-emerald-200 text-sm">
                            <span class="flex items-center gap-1">
                                <i class="fas fa-calendar"></i>
                                {{ $news->published_at?->format('d M Y') }}
                            </span>
                            <span class="flex items-center gap-1">
                                <i class="fas fa-eye"></i>
                                {{ $news->views ?? 0 }} views
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Content Cards --}}
        <div class="px-4 lg:px-0 -mt-4 relative z-20 space-y-4 pb-8">
            
            {{-- Action Buttons --}}
            <div class="bg-white rounded-2xl p-4 shadow-lg flex gap-3">
                <button class="flex-1 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl flex items-center justify-center gap-2 hover:bg-gray-200 transition">
                    <i class="fas fa-share-alt"></i>
                    <span>Bagikan</span>
                </button>
            </div>

            {{-- Content --}}
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="p-4 lg:p-6">
                    <div class="prose prose-sm max-w-none text-gray-600">
                        {!! $news->content !!}
                    </div>
                </div>
            </div>

            {{-- Related --}}
            @if($relatedNews->count() > 0)
            <div class="pt-4">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Berita Lainnya</h2>
                </div>
                <div class="flex gap-3 overflow-x-auto pb-2 -mx-4 px-4 lg:mx-0 lg:px-0 scrollbar-hide">
                    @foreach($relatedNews as $related)
                    <a href="{{ route('opac.news.show', $related->slug) }}" class="flex-shrink-0 w-48 lg:w-56 group">
                        <div class="aspect-video bg-white rounded-xl shadow-lg overflow-hidden mb-2">
                            @if($related->image_url)
                                <img src="{{ $related->image_url }}" alt="{{ $related->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-emerald-100 to-emerald-50 flex items-center justify-center">
                                    <i class="fas fa-newspaper text-2xl text-emerald-300"></i>
                                </div>
                            @endif
                        </div>
                        <h3 class="text-sm font-medium text-gray-900 line-clamp-2 group-hover:text-emerald-600 transition">{{ $related->title }}</h3>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $related->published_at?->format('d M Y') }}</p>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
            
        </div>
    </div>
</x-opac.layout>
