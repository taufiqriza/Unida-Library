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
        <div class="px-4 lg:px-0 mt-6 relative z-20 space-y-4 pb-8">
            
            {{-- Files Section - Clear & Detailed --}}
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-4 py-3 bg-gradient-to-r from-purple-50 to-white border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-folder-open text-purple-500"></i>
                        Dokumen Tersedia
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    {{-- Preview/BAB 1-3 --}}
                    @if($thesis->file_path)
                    <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-file-pdf text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">BAB 1-3 (Preview)</p>
                                <p class="text-xs text-green-600 flex items-center gap-1">
                                    <i class="fas fa-unlock"></i> Dapat diakses publik
                                </p>
                            </div>
                        </div>
                        <a href="{{ asset('storage/' . $thesis->file_path) }}" target="_blank" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                            <i class="fas fa-eye"></i>
                            <span class="hidden sm:inline">Baca</span>
                        </a>
                    </div>
                    @endif

                    {{-- Full Text --}}
                    <div class="flex items-center justify-between p-4 {{ $thesis->is_fulltext_public ? 'bg-blue-50 border-blue-200' : 'bg-gray-50 border-gray-200' }} border rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 {{ $thesis->is_fulltext_public ? 'bg-blue-100' : 'bg-gray-100' }} rounded-xl flex items-center justify-center">
                                <i class="fas fa-book {{ $thesis->is_fulltext_public ? 'text-blue-600' : 'text-gray-400' }} text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Full Text (Lengkap)</p>
                                @if($thesis->is_fulltext_public)
                                    <p class="text-xs text-blue-600 flex items-center gap-1">
                                        <i class="fas fa-unlock"></i> Dapat diakses publik
                                    </p>
                                @else
                                    <p class="text-xs text-gray-500 flex items-center gap-1">
                                        <i class="fas fa-lock"></i> Hanya untuk anggota perpustakaan
                                    </p>
                                @endif
                            </div>
                        </div>
                        @if($thesis->is_fulltext_public && $thesis->file_path)
                            <a href="{{ asset('storage/' . $thesis->file_path) }}" target="_blank" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                                <i class="fas fa-download"></i>
                                <span class="hidden sm:inline">Unduh</span>
                            </a>
                        @elseif(auth('member')->check())
                            <a href="{{ asset('storage/' . $thesis->file_path) }}" target="_blank" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                                <i class="fas fa-download"></i>
                                <span class="hidden sm:inline">Unduh</span>
                            </a>
                        @else
                            <a href="{{ route('opac.login') }}" class="px-4 py-2 bg-gray-400 text-white text-sm font-medium rounded-lg hover:bg-gray-500 transition flex items-center gap-2">
                                <i class="fas fa-sign-in-alt"></i>
                                <span class="hidden sm:inline">Login</span>
                            </a>
                        @endif
                    </div>

                    {{-- Info Box --}}
                    <div class="mt-2 p-3 bg-amber-50 border border-amber-200 rounded-xl">
                        <p class="text-xs text-amber-700 flex items-start gap-2">
                            <i class="fas fa-info-circle mt-0.5"></i>
                            <span>Untuk mengakses full text, silakan login sebagai anggota perpustakaan. Jika belum terdaftar, kunjungi perpustakaan untuk mendaftar.</span>
                        </p>
                    </div>
                </div>
            </div>

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
                    @if($thesis->advisor1)
                    <div class="flex items-center px-4 py-3">
                        <span class="w-28 text-sm text-gray-500 flex-shrink-0">Pembimbing 1</span>
                        <span class="text-sm text-gray-900 font-medium">{{ $thesis->advisor1 }}</span>
                    </div>
                    @endif
                    @if($thesis->advisor2)
                    <div class="flex items-center px-4 py-3">
                        <span class="w-28 text-sm text-gray-500 flex-shrink-0">Pembimbing 2</span>
                        <span class="text-sm text-gray-900 font-medium">{{ $thesis->advisor2 }}</span>
                    </div>
                    @endif
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
                    <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ $thesis->abstract }}</p>
                </div>
            </div>
            @endif

            {{-- Share Button --}}
            <div class="bg-white rounded-2xl p-4 shadow-lg">
                <div class="flex gap-3">
                    <button onclick="navigator.share ? navigator.share({title: '{{ $thesis->title }}', url: window.location.href}) : navigator.clipboard.writeText(window.location.href).then(() => alert('Link disalin!'))" class="flex-1 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl flex items-center justify-center gap-2 hover:bg-gray-200 transition">
                        <i class="fas fa-share-alt"></i>
                        <span>Bagikan</span>
                    </button>
                    <button onclick="window.print()" class="flex-1 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl flex items-center justify-center gap-2 hover:bg-gray-200 transition">
                        <i class="fas fa-print"></i>
                        <span>Cetak</span>
                    </button>
                </div>
            </div>

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
