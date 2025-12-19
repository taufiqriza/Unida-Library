<div>
    <div class="lg:max-w-7xl lg:mx-auto lg:px-4 lg:py-8">
        
        {{-- Hero Section --}}
        <div class="relative bg-gradient-to-b from-indigo-600 via-indigo-700 to-purple-800 lg:rounded-2xl lg:overflow-hidden">
            {{-- Back Button (Mobile) --}}
            <div class="relative z-10 px-4 pt-4 lg:hidden">
                <a href="{{ route('opac.search') }}?type=ethesis" class="inline-flex items-center gap-2 text-white/80 hover:text-white text-sm">
                    <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i>
                    <span>{{ __('opac.ethesis_show.back') }}</span>
                </a>
            </div>
            
            {{-- Breadcrumb (Desktop) --}}
            <nav class="hidden lg:block relative z-10 px-6 pt-6 text-sm text-white/70">
                <a href="{{ route('opac.home') }}" class="hover:text-white">{{ __('opac.ethesis_show.home') }}</a>
                <span class="mx-2">/</span>
                <a href="{{ route('opac.search') }}?type=ethesis" class="hover:text-white">E-Thesis</a>
                <span class="mx-2">/</span>
                <span class="text-white">{{ Str::limit($thesis->title, 40) }}</span>
            </nav>
            
            {{-- Cover & Basic Info --}}
            <div class="relative z-10 px-4 pb-6 pt-4 lg:p-8">
                <div class="flex flex-col items-center lg:flex-row lg:items-end gap-4 lg:gap-8">
                    <div class="w-40 lg:w-52 flex-shrink-0">
                        <div class="aspect-[3/4] bg-white rounded-xl shadow-2xl overflow-hidden ring-4 ring-white/20">
                            <img src="{{ $thesis->cover_url }}" alt="{{ $thesis->title }}" class="w-full h-full object-cover">
                        </div>
                    </div>
                    
                    <div class="text-center lg:text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} flex-1">
                        <div class="flex items-center justify-center lg:justify-start gap-2 mb-2">
                            <span class="inline-block px-3 py-1 bg-white/20 text-white text-xs font-semibold rounded-full">
                                {{ $thesis->getTypeLabel() }}
                            </span>
                            <span class="inline-block px-3 py-1 bg-indigo-500/50 text-white text-xs font-semibold rounded-full">
                                <i class="fas fa-university mr-1"></i> {{ __('opac.ethesis_show.repository') }}
                            </span>
                        </div>
                        <h1 class="text-xl lg:text-3xl font-bold text-white leading-tight">{{ $thesis->title }}</h1>
                        <p class="text-indigo-200 mt-2 text-sm lg:text-base">{{ $thesis->author }}</p>
                        
                        <div class="flex items-center justify-center lg:justify-start gap-4 mt-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-white">{{ $thesis->year }}</div>
                                <div class="text-xs text-indigo-200">{{ __('opac.ethesis_show.year') }}</div>
                            </div>
                            <div class="w-px h-10 bg-white/20"></div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-white">{{ $thesis->views ?? 0 }}</div>
                                <div class="text-xs text-indigo-200">{{ __('opac.ethesis_show.views') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Content Cards --}}
        <div class="px-4 lg:px-0 mt-6 relative z-20 space-y-4 pb-8">
            <div class="grid lg:grid-cols-3 gap-6">
                {{-- Main Content --}}
                <div class="lg:col-span-2 space-y-4">
                    {{-- Abstract --}}
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-4 py-3 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-100">
                            <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-file-alt text-indigo-500"></i>
                                {{ __('opac.ethesis_show.abstract') }}
                            </h3>
                        </div>
                        <div class="p-4">
                            @if($thesis->abstract)
                                <p class="text-gray-700 leading-relaxed text-justify">{{ $thesis->abstract }}</p>
                            @else
                                <p class="text-gray-500 italic">{{ __('opac.ethesis_show.abstract_not_available') }}</p>
                            @endif
                            
                            @if($thesis->abstract_en)
                                <hr class="my-4">
                                <h4 class="font-semibold text-gray-800 mb-2">{{ __('opac.ethesis_show.abstract_english') }}</h4>
                                <p class="text-gray-700 leading-relaxed text-justify">{{ $thesis->abstract_en }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- Keywords --}}
                    @if($thesis->keywords)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-4 py-3 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-100">
                            <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-tags text-indigo-500"></i>
                                {{ __('opac.ethesis_show.keywords') }}
                            </h3>
                        </div>
                        <div class="p-4">
                            <div class="flex flex-wrap gap-2">
                                @foreach(explode(',', $thesis->keywords) as $keyword)
                                    <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm">
                                        {{ trim($keyword) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Action Button --}}
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ $thesis->external_url ?? $thesis->url }}" target="_blank" rel="noopener" 
                           class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-6 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition shadow-lg shadow-indigo-600/30">
                            <i class="fas fa-external-link-alt"></i>
                            {{ __('opac.ethesis_show.open_repository') }}
                        </a>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="space-y-4">
                    {{-- Info Card --}}
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-4 py-3 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-100">
                            <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-info-circle text-indigo-500"></i>
                                {{ __('opac.ethesis_show.information') }}
                            </h3>
                        </div>
                        <div class="p-4">
                            <dl class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">{{ __('opac.ethesis_show.author') }}</dt>
                                    <dd class="font-medium text-gray-900 text-right">{{ $thesis->author }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">{{ __('opac.ethesis_show.year') }}</dt>
                                    <dd class="font-medium text-gray-900">{{ $thesis->year }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">{{ __('opac.ethesis_show.type') }}</dt>
                                    <dd class="font-medium text-gray-900">{{ $thesis->getTypeLabel() }}</dd>
                                </div>
                                @if($thesis->nim)
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">{{ __('opac.ethesis_show.nim') }}</dt>
                                    <dd class="font-medium text-gray-900">{{ $thesis->nim }}</dd>
                                </div>
                                @endif
                                <div class="flex justify-between pt-2 border-t">
                                    <dt class="text-gray-500">{{ __('opac.ethesis_show.views') }}</dt>
                                    <dd class="font-medium text-gray-900">{{ number_format($thesis->views ?? 0) }}x</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    {{-- Source Info --}}
                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl p-6 border border-indigo-100">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-university text-white"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900">{{ __('opac.ethesis_show.repository_unida') }}</h3>
                                <p class="text-xs text-gray-500">repo.unida.gontor.ac.id</p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600">
                            {{ __('opac.ethesis_show.source_info') }}
                        </p>
                    </div>

                    {{-- Related --}}
                    @if($relatedTheses->count() > 0)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-4 py-3 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-100">
                            <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-book text-indigo-500"></i>
                                {{ __('opac.ethesis_show.other_thesis') }}
                            </h3>
                        </div>
                        <div class="p-4 space-y-3">
                            @foreach($relatedTheses as $rel)
                            <a href="{{ route('opac.ethesis.show', $rel->id) }}" class="block group">
                                <h4 class="text-sm font-medium text-gray-900 group-hover:text-indigo-600 line-clamp-2">
                                    {{ $rel->title }}
                                </h4>
                                <p class="text-xs text-gray-500 mt-1">{{ $rel->author }} • {{ $rel->year }}</p>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
