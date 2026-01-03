<div>
    <div class="lg:max-w-7xl lg:mx-auto lg:px-4 lg:py-8">
        
        {{-- Hero Section --}}
        <div class="relative bg-gradient-to-b from-blue-600 via-blue-700 to-indigo-800 lg:rounded-2xl lg:overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <img src="{{ $thesis->cover_url }}" class="w-full h-full object-cover opacity-20 blur-2xl scale-110">
            </div>
            
            {{-- Back Button --}}
            <div class="relative z-10 px-4 pt-4 lg:px-6 lg:pt-6 flex items-center justify-between">
                <button onclick="window.history.back()" class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 hover:bg-white/30 text-white text-sm rounded-xl transition backdrop-blur-sm">
                    <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i>
                    <span>{{ __('opac.ethesis_show.back') }}</span>
                </button>
                
                {{-- Breadcrumb (Desktop) --}}
                <nav class="hidden lg:flex items-center gap-2 text-sm text-white/70">
                    <a href="{{ route('opac.home') }}" class="hover:text-white">{{ __('opac.ethesis_show.home') }}</a>
                    <span>/</span>
                    <a href="{{ route('opac.search') }}?type=ethesis" class="hover:text-white">E-Thesis</a>
                    <span>/</span>
                    <span class="text-white truncate max-w-[200px]">{{ Str::limit($thesis->title, 30) }}</span>
                </nav>
            </div>
            
            {{-- Cover & Basic Info --}}
            <div class="relative z-10 px-4 pb-6 pt-4 lg:p-8">
                <div class="flex flex-col items-center lg:flex-row lg:items-end gap-4 lg:gap-8">
                    <div class="w-40 lg:w-52 flex-shrink-0">
                        <div class="aspect-[3/4] bg-white rounded-xl shadow-2xl overflow-hidden ring-4 ring-white/20">
                            <img src="{{ $thesis->cover_url }}" alt="{{ $thesis->title }}" class="w-full h-full object-cover">
                        </div>
                    </div>
                    
                    <div class="text-center lg:text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} flex-1">
                        <span class="inline-block px-3 py-1 bg-white/20 text-white text-xs font-semibold rounded-full mb-2">{{ $thesis->getTypeLabel() }}</span>
                        <h1 class="text-xl lg:text-3xl font-bold text-white leading-tight">{{ $thesis->title }}</h1>
                        <p class="text-blue-200 mt-2 text-sm lg:text-base">{{ $thesis->author }}</p>
                        
                        <div class="flex items-center justify-center lg:justify-start gap-4 mt-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-white">{{ $thesis->year }}</div>
                                <div class="text-xs text-blue-200">{{ __('opac.ethesis_show.year') }}</div>
                            </div>
                            <div class="w-px h-10 bg-white/20"></div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-white">{{ $thesis->views ?? 0 }}</div>
                                <div class="text-xs text-blue-200">{{ __('opac.ethesis_show.views') }}</div>
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
                <div class="px-4 py-3 bg-gradient-to-r from-blue-50 to-white border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-folder-open text-blue-500"></i>
                        {{ __('opac.ethesis_show.documents_available') }}
                    </h3>
                </div>
                <div class="p-4 space-y-3" x-data="ethesisViewer({
                    fileUrl: '{{ $thesis->file_path ? asset('storage/thesis/' . $thesis->file_path) : '' }}'
                })">
                    {{-- Full Text / Preview --}}
                    @if($thesis->file_path)
                    <div class="flex items-center justify-between p-4 bg-blue-50 border border-blue-200 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-file-pdf text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ __('opac.ethesis_show.full_text') }}</p>
                                @if(auth('member')->check())
                                <p class="text-xs text-blue-600 flex items-center gap-1">
                                    <i class="fas fa-unlock"></i> {{ __('opac.ethesis_show.access_granted') }}
                                </p>
                                @else
                                <p class="text-xs text-gray-500 flex items-center gap-1">
                                    <i class="fas fa-lock"></i> {{ __('opac.ethesis_show.members_only') }}
                                </p>
                                @endif
                            </div>
                        </div>
                        @if(auth('member')->check())
                        <button @click="openModal()" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                            <i class="fas fa-eye"></i>
                            <span class="hidden sm:inline">{{ __('opac.ethesis_show.read') }}</span>
                        </button>
                        @else
                        <a href="{{ route('opac.login') }}" class="px-4 py-2 bg-gray-400 text-white text-sm font-medium rounded-lg hover:bg-gray-500 transition flex items-center gap-2">
                            <i class="fas fa-sign-in-alt"></i>
                            <span class="hidden sm:inline">{{ __('opac.ethesis_show.login') }}</span>
                        </a>
                        @endif
                    </div>
                    
                    {{-- Modal PDF Viewer --}}
                    <div x-show="showModal" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 z-[99999] flex items-center justify-center pt-16 lg:pt-20 px-2 pb-2 lg:px-4 lg:pb-4 bg-black/80 backdrop-blur-sm"
                         @keydown.escape.window="closeModal()"
                         style="display: none;">
                        
                        {{-- Modal Content --}}
                        <div x-show="showModal"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             @click.away="closeModal()"
                             class="relative w-full max-w-6xl h-full bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col">
                            
                            {{-- Modal Header --}}
                            <div class="flex items-center justify-between px-3 lg:px-4 py-2 lg:py-3 text-white flex-shrink-0 bg-gradient-to-r from-blue-600 to-indigo-600">
                                <div class="flex items-center gap-2 lg:gap-3 min-w-0 flex-1">
                                    <div class="w-8 h-8 lg:w-10 lg:h-10 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-file-pdf text-sm lg:text-base"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="font-bold text-xs lg:text-base line-clamp-1">{{ Str::limit($thesis->title, 40) }}</h3>
                                        <p class="text-white/70 text-[10px] lg:text-xs">{{ __('opac.ethesis_show.full_text') }}</p>
                                    </div>
                                </div>
                                <button @click="closeModal()" class="w-8 h-8 lg:w-9 lg:h-9 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            
                            {{-- PDF Viewer Container --}}
                            <div class="flex-1 bg-gray-900 relative overflow-hidden">
                                {{-- Loading State --}}
                                <div x-show="loading" class="absolute inset-0 flex items-center justify-center bg-gray-900 z-10">
                                    <div class="text-center">
                                        <div class="w-12 h-12 border-4 border-white/50 border-t-white rounded-full animate-spin mx-auto mb-3"></div>
                                        <p class="text-white text-sm">{{ __('opac.ethesis_show.loading_document') }}</p>
                                    </div>
                                </div>
                                
                                {{-- Error State --}}
                                <div x-show="error" x-cloak class="absolute inset-0 flex items-center justify-center bg-gray-900 z-10">
                                    <div class="text-center p-6 max-w-md">
                                        <div class="w-16 h-16 bg-red-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-exclamation-circle text-red-400 text-2xl"></i>
                                        </div>
                                        <h4 class="text-white font-bold mb-2">{{ __('opac.ethesis_show.load_failed') }}</h4>
                                        <p class="text-gray-400 text-sm mb-4">{{ __('opac.ethesis_show.try_again_later') }}</p>
                                        <button @click="retryLoad()" class="px-4 py-2 bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-600 transition">
                                            <i class="fas fa-redo mr-1"></i> {{ __('opac.ethesis_show.retry') }}
                                        </button>
                                    </div>
                                </div>
                                
                                {{-- PDF Viewer --}}
                                <iframe 
                                    x-ref="pdfFrame"
                                    x-show="!error"
                                    x-on:load="onFrameLoad()"
                                    x-on:error="onFrameError()"
                                    class="w-full h-full"
                                    frameborder="0">
                                </iframe>
                            </div>
                            
                            {{-- Modal Footer --}}
                            <div class="px-3 lg:px-4 py-2 bg-gray-100 border-t border-gray-200 flex items-center justify-end flex-shrink-0">
                                <button @click="closeModal()" class="px-4 py-1.5 bg-gray-700 hover:bg-gray-800 text-white text-xs lg:text-sm font-medium rounded-lg transition">
                                    {{ __('opac.ethesis_show.close') }}
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Info Box --}}
                    <div class="mt-2 p-3 bg-amber-50 border border-amber-200 rounded-xl">
                        <p class="text-xs text-amber-700 flex items-start gap-2">
                            <i class="fas fa-info-circle mt-0.5"></i>
                            <span>{{ __('opac.ethesis_show.fulltext_info') }}</span>
                        </p>
                    </div>
                    
                    <script>
                        function ethesisViewer(config) {
                            return {
                                showModal: false,
                                loading: true,
                                error: false,
                                fileUrl: config.fileUrl,
                                loadTimeout: null,
                                
                                openModal() {
                                    this.showModal = true;
                                    this.loading = true;
                                    this.error = false;
                                    document.body.style.overflow = 'hidden';
                                    this.$nextTick(() => this.loadPdf());
                                },
                                
                                closeModal() {
                                    this.showModal = false;
                                    document.body.style.overflow = '';
                                    if (this.$refs.pdfFrame) this.$refs.pdfFrame.src = '';
                                    clearTimeout(this.loadTimeout);
                                },
                                
                                loadPdf() {
                                    const frame = this.$refs.pdfFrame;
                                    if (!frame || !this.fileUrl) return;
                                    
                                    this.loadTimeout = setTimeout(() => {
                                        if (this.loading) {
                                            this.error = true;
                                            this.loading = false;
                                        }
                                    }, 15000);
                                    
                                    frame.src = this.fileUrl + '#toolbar=0&navpanes=0&scrollbar=1&view=FitH';
                                },
                                
                                onFrameLoad() {
                                    clearTimeout(this.loadTimeout);
                                    this.loading = false;
                                },
                                
                                onFrameError() {
                                    clearTimeout(this.loadTimeout);
                                    this.error = true;
                                    this.loading = false;
                                },
                                
                                retryLoad() {
                                    this.error = false;
                                    this.loading = true;
                                    this.loadPdf();
                                }
                            }
                        }
                    </script>
                </div>
            </div>

            {{-- Detail Info - Side by Side Layout --}}
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        {{ __('opac.ethesis_show.thesis_info') }}
                    </h3>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        {{-- Penulis --}}
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-user text-blue-600 text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-gray-500 uppercase tracking-wide">{{ __('opac.ethesis_show.author') }}</p>
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $thesis->author }}</p>
                            </div>
                        </div>
                        
                        {{-- NIM --}}
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-id-card text-blue-600 text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-gray-500 uppercase tracking-wide">{{ __('opac.ethesis_show.nim') }}</p>
                                <p class="text-sm font-semibold text-gray-900 font-mono">{{ $thesis->nim ?? '-' }}</p>
                            </div>
                        </div>
                        
                        {{-- Jenis --}}
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-graduation-cap text-emerald-600 text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-gray-500 uppercase tracking-wide">{{ __('opac.ethesis_show.type') }}</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $thesis->getTypeLabel() }}</p>
                            </div>
                        </div>
                        
                        {{-- Tahun --}}
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-calendar text-amber-600 text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-gray-500 uppercase tracking-wide">{{ __('opac.ethesis_show.year') }}</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $thesis->year }}</p>
                            </div>
                        </div>
                        
                        {{-- Fakultas --}}
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-rose-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-university text-rose-600 text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-gray-500 uppercase tracking-wide">{{ __('opac.ethesis_show.faculty') }}</p>
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $thesis->department?->faculty?->name ?? '-' }}</p>
                            </div>
                        </div>
                        
                        {{-- Prodi --}}
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-book-reader text-indigo-600 text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-gray-500 uppercase tracking-wide">{{ __('opac.ethesis_show.department') }}</p>
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $thesis->department?->name ?? '-' }}</p>
                            </div>
                        </div>
                        
                        {{-- Pembimbing 1 --}}
                        @if($thesis->advisor1)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-chalkboard-teacher text-teal-600 text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-gray-500 uppercase tracking-wide">{{ __('opac.ethesis_show.advisor1') }}</p>
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $thesis->advisor1 }}</p>
                            </div>
                        </div>
                        @endif
                        
                        {{-- Pembimbing 2 --}}
                        @if($thesis->advisor2)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-user-tie text-cyan-600 text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-gray-500 uppercase tracking-wide">{{ __('opac.ethesis_show.advisor2') }}</p>
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $thesis->advisor2 }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    {{-- Kata Kunci --}}
                    @if($thesis->keywords)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-violet-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-tags text-violet-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-[10px] text-gray-500 uppercase tracking-wide mb-2">{{ __('opac.ethesis_show.keywords') }}</p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach(explode(',', $thesis->keywords) as $keyword)
                                        <span class="px-2.5 py-1 bg-blue-50 text-blue-700 text-xs font-medium rounded-lg border border-blue-100">{{ trim($keyword) }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            {{-- Lembar Pengesahan --}}
            @if($thesis->approval_path)
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-4 py-3 bg-gradient-to-r from-amber-50 to-orange-50 border-b border-amber-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-file-signature text-amber-500"></i>
                        {{ __('opac.ethesis_show.approval_sheet') }}
                    </h3>
                </div>
                <div class="p-4">
                    {{-- Preview Image/PDF --}}
                    <div class="relative bg-gray-100 rounded-xl overflow-hidden mb-3">
                        @php
                            $approvalExt = pathinfo($thesis->approval_path, PATHINFO_EXTENSION);
                            $isImage = in_array(strtolower($approvalExt), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        @endphp
                        
                        @if($isImage)
                            <img src="{{ asset('storage/' . $thesis->approval_path) }}" alt="{{ __('opac.ethesis_show.approval_sheet') }}" class="w-full max-h-96 object-contain">
                        @else
                            {{-- PDF Preview --}}
                            <div class="aspect-[3/4] max-h-96 w-full">
                                <iframe src="{{ asset('storage/' . $thesis->approval_path) }}#toolbar=0&navpanes=0" class="w-full h-full" frameborder="0"></iframe>
                            </div>
                        @endif
                    </div>
                    
                    {{-- Action Buttons --}}
                    <div class="flex gap-2">
                        <a href="{{ asset('storage/' . $thesis->approval_path) }}" target="_blank" class="flex-1 px-4 py-2.5 bg-amber-100 text-amber-700 text-sm font-semibold rounded-xl hover:bg-amber-200 transition flex items-center justify-center gap-2">
                            <i class="fas fa-expand"></i>
                            {{ __('opac.ethesis_show.view_full') }}
                        </a>
                        <a href="{{ asset('storage/' . $thesis->approval_path) }}" download class="flex-1 px-4 py-2.5 bg-amber-600 text-white text-sm font-semibold rounded-xl hover:bg-amber-700 transition flex items-center justify-center gap-2">
                            <i class="fas fa-download"></i>
                            {{ __('opac.ethesis_show.download') }}
                        </a>
                    </div>
                </div>
            </div>
            @endif

            {{-- Abstract --}}
            @if($thesis->abstract)
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-align-left text-blue-500"></i>
                        {{ __('opac.ethesis_show.abstract') }}
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
                    <button onclick="navigator.share ? navigator.share({title: '{{ $thesis->title }}', url: window.location.href}) : navigator.clipboard.writeText(window.location.href).then(() => alert('{{ __('opac.ethesis_show.link_copied') }}'))" class="flex-1 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl flex items-center justify-center gap-2 hover:bg-gray-200 transition">
                        <i class="fas fa-share-alt"></i>
                        <span>{{ __('opac.ethesis_show.share') }}</span>
                    </button>
                    <button onclick="window.print()" class="flex-1 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl flex items-center justify-center gap-2 hover:bg-gray-200 transition">
                        <i class="fas fa-print"></i>
                        <span>{{ __('opac.ethesis_show.print') }}</span>
                    </button>
                </div>
            </div>

            {{-- Related --}}
            @if($relatedTheses->count() > 0)
            <div class="pt-4">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900">{{ __('opac.ethesis_show.related_theses') }}</h2>
                </div>
                <div class="flex gap-3 overflow-x-auto pb-2 -mx-4 px-4 lg:mx-0 lg:px-0 scrollbar-hide">
                    @foreach($relatedTheses as $related)
                    <a href="{{ route('opac.ethesis.show', $related->id) }}" class="flex-shrink-0 w-32 lg:w-40 group">
                        <div class="aspect-[3/4] bg-white rounded-xl shadow-lg overflow-hidden mb-2">
                            <img src="{{ $related->cover_url }}" alt="{{ $related->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        </div>
                        <h3 class="text-sm font-medium text-gray-900 line-clamp-2 group-hover:text-blue-600 transition">{{ $related->title }}</h3>
                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ $related->author }}</p>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
            
        </div>
    </div>
</div>
