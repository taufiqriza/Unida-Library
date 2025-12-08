<x-opac.layout :title="$thesis->title">
    <div class="lg:max-w-7xl lg:mx-auto lg:px-4 lg:py-8">
        
        {{-- Hero Section --}}
        <div class="relative bg-gradient-to-b from-purple-600 via-purple-700 to-purple-800 lg:rounded-2xl lg:overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                @if($thesis->cover_path)
                    <img src="{{ asset('storage/' . $thesis->cover_path) }}" class="w-full h-full object-cover opacity-20 blur-2xl scale-110">
                @endif
            </div>
            
            {{-- Back Button (Mobile) --}}
            <div class="relative z-10 px-4 pt-4 lg:hidden">
                <a href="{{ route('opac.search') }}?type=ethesis" class="inline-flex items-center gap-2 text-white/80 hover:text-white text-sm">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
            
            {{-- Breadcrumb (Desktop) --}}
            <nav class="hidden lg:block relative z-10 px-6 pt-6 text-sm text-white/70">
                <a href="{{ route('opac.home') }}" class="hover:text-white">Beranda</a>
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
                            @if($thesis->cover_path)
                                <img src="{{ asset('storage/' . $thesis->cover_path) }}" alt="{{ $thesis->title }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-purple-100 to-purple-50 flex items-center justify-center">
                                    <i class="fas fa-graduation-cap text-5xl text-purple-300"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="text-center lg:text-left flex-1">
                        <span class="inline-block px-3 py-1 bg-white/20 text-white text-xs font-semibold rounded-full mb-2">{{ $thesis->getTypeLabel() }}</span>
                        <h1 class="text-xl lg:text-3xl font-bold text-white leading-tight">{{ $thesis->title }}</h1>
                        <p class="text-purple-200 mt-2 text-sm lg:text-base">{{ $thesis->author }}</p>
                        
                        <div class="flex items-center justify-center lg:justify-start gap-4 mt-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-white">{{ $thesis->year }}</div>
                                <div class="text-xs text-purple-200">Tahun</div>
                            </div>
                            <div class="w-px h-10 bg-white/20"></div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-white">{{ $thesis->views ?? 0 }}</div>
                                <div class="text-xs text-purple-200">Dilihat</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Content Cards --}}
        <div class="px-4 lg:px-0 -mt-4 relative z-20 space-y-4 pb-8">
            
            {{-- Action Buttons --}}
            @if($thesis->file_path)
            <div class="bg-white rounded-2xl p-4 shadow-lg flex gap-3">
                <a href="{{ asset('storage/' . $thesis->file_path) }}" target="_blank" class="flex-1 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-xl shadow-lg shadow-purple-500/30 flex items-center justify-center gap-2">
                    <i class="fas fa-file-pdf"></i>
                    <span>Baca PDF</span>
                </a>
                <button class="flex-1 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl flex items-center justify-center gap-2 hover:bg-gray-200 transition">
                    <i class="fas fa-share-alt"></i>
                    <span>Bagikan</span>
                </button>
            </div>
            @endif

            {{-- Detail Info --}}
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-info-circle text-purple-500"></i>
                        Informasi Tugas Akhir
                    </h3>
                </div>
                <div class="divide-y divide-gray-100">
                    <div class="flex items-center px-4 py-3">
                        <span class="w-28 text-sm text-gray-500 flex-shrink-0">Penulis</span>
                        <span class="text-sm text-gray-900 font-medium">{{ $thesis->author }}</span>
                    </div>
                    <div class="flex items-center px-4 py-3">
                        <span class="w-28 text-sm text-gray-500 flex-shrink-0">NIM</span>
                        <span class="text-sm text-gray-900 font-medium font-mono">{{ $thesis->nim ?? '-' }}</span>
                    </div>
                    <div class="flex items-center px-4 py-3">
                        <span class="w-28 text-sm text-gray-500 flex-shrink-0">Jenis</span>
                        <span class="text-sm text-gray-900 font-medium">{{ $thesis->getTypeLabel() }}</span>
                    </div>
                    <div class="flex items-center px-4 py-3">
                        <span class="w-28 text-sm text-gray-500 flex-shrink-0">Tahun</span>
                        <span class="text-sm text-gray-900 font-medium">{{ $thesis->year }}</span>
                    </div>
                    <div class="flex items-center px-4 py-3">
                        <span class="w-28 text-sm text-gray-500 flex-shrink-0">Fakultas</span>
                        <span class="text-sm text-gray-900 font-medium">{{ $thesis->department?->faculty?->name ?? '-' }}</span>
                    </div>
                    <div class="flex items-center px-4 py-3">
                        <span class="w-28 text-sm text-gray-500 flex-shrink-0">Prodi</span>
                        <span class="text-sm text-gray-900 font-medium">{{ $thesis->department?->name ?? '-' }}</span>
                    </div>
                    @if($thesis->keywords)
                    <div class="flex items-start px-4 py-3">
                        <span class="w-28 text-sm text-gray-500 flex-shrink-0 pt-0.5">Kata Kunci</span>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach(explode(',', $thesis->keywords) as $keyword)
                                <span class="px-2 py-0.5 bg-purple-50 text-purple-700 text-xs rounded-full">{{ trim($keyword) }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Abstract --}}
            @if($thesis->abstract)
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-align-left text-purple-500"></i>
                        Abstrak
                    </h3>
                </div>
                <div class="p-4">
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $thesis->abstract }}</p>
                </div>
            </div>
            @endif

            {{-- Related --}}
            @if($relatedTheses->count() > 0)
            <div class="pt-4">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Tugas Akhir Terkait</h2>
                </div>
                <div class="flex gap-3 overflow-x-auto pb-2 -mx-4 px-4 lg:mx-0 lg:px-0 scrollbar-hide">
                    @foreach($relatedTheses as $related)
                    <a href="{{ route('opac.ethesis.show', $related->id) }}" class="flex-shrink-0 w-32 lg:w-40 group">
                        <div class="aspect-[3/4] bg-white rounded-xl shadow-lg overflow-hidden mb-2">
                            @if($related->cover_path)
                                <img src="{{ asset('storage/' . $related->cover_path) }}" alt="{{ $related->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-purple-100 to-purple-50 flex items-center justify-center">
                                    <i class="fas fa-graduation-cap text-2xl text-purple-300"></i>
                                </div>
                            @endif
                        </div>
                        <h3 class="text-sm font-medium text-gray-900 line-clamp-2 group-hover:text-purple-600 transition">{{ $related->title }}</h3>
                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ $related->author }}</p>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
            
        </div>
    </div>
</x-opac.layout>
