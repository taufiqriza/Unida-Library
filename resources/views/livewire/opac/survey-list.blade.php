<div class="py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-500 via-indigo-500 to-violet-600 rounded-3xl mb-6 shadow-xl shadow-blue-500/25">
                <i class="fas fa-clipboard-question text-3xl text-white"></i>
            </div>
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3">Survey Kepuasan Layanan</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Bantu kami meningkatkan kualitas layanan perpustakaan dengan mengisi survey kepuasan. 
                Pendapat Anda sangat berharga bagi kami.
            </p>
        </div>

        {{-- Survey Cards --}}
        @if($surveys->isEmpty())
            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-12 text-center">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-clipboard-list text-4xl text-gray-400"></i>
                </div>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Survey Aktif</h2>
                <p class="text-gray-500">Saat ini tidak ada survey yang tersedia untuk diisi.</p>
            </div>
        @else
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($surveys as $survey)
                    <div class="group bg-white rounded-2xl shadow-md hover:shadow-xl border border-gray-100 overflow-hidden transition-all duration-300 hover:-translate-y-1">
                        {{-- Card Header --}}
                        <div class="bg-gradient-to-br from-blue-500 via-indigo-500 to-violet-600 p-6 relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                            <div class="relative">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-poll text-xl text-white"></i>
                                    </div>
                                    <div>
                                        <span class="px-2 py-0.5 bg-white/20 rounded-full text-[10px] uppercase tracking-wide text-white/80">Active</span>
                                    </div>
                                </div>
                                <h3 class="text-lg font-bold text-white line-clamp-2">{{ $survey->title }}</h3>
                            </div>
                        </div>

                        {{-- Card Body --}}
                        <div class="p-6">
                            @if($survey->description)
                                <p class="text-gray-600 text-sm line-clamp-2 mb-4">{{ $survey->description }}</p>
                            @endif

                            <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500 mb-4">
                                @if($survey->branch)
                                    <span class="flex items-center gap-1.5 px-2 py-1 bg-violet-50 text-violet-600 rounded-lg">
                                        <i class="fas fa-building text-xs"></i>
                                        {{ $survey->branch->name }}
                                    </span>
                                @else
                                    <span class="flex items-center gap-1.5 px-2 py-1 bg-purple-50 text-purple-600 rounded-lg">
                                        <i class="fas fa-globe text-xs"></i>
                                        Semua Cabang
                                    </span>
                                @endif
                                <span class="flex items-center gap-1.5">
                                    <i class="fas fa-layer-group text-xs text-blue-500"></i>
                                    {{ $survey->sections_count }} Bagian
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <i class="fas fa-users text-xs text-emerald-500"></i>
                                    {{ number_format($survey->responses_count) }} Responden
                                </span>
                            </div>

                            @if($survey->end_date)
                                <div class="flex items-center gap-2 text-xs text-amber-600 bg-amber-50 rounded-lg px-3 py-2 mb-4">
                                    <i class="fas fa-clock"></i>
                                    <span>Berakhir: {{ $survey->end_date->format('d M Y') }}</span>
                                </div>
                            @endif

                            <a href="{{ route('opac.survey.show', $survey->slug) }}" 
                               class="block w-full text-center py-3 px-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/25 transition-all group-hover:shadow-blue-500/40">
                                <i class="fas fa-clipboard-check mr-2"></i>
                                Isi Survey
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Info Section --}}
        <div class="mt-12 bg-gradient-to-br from-slate-50 to-slate-100 rounded-2xl p-8">
            <div class="grid md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shield-alt text-2xl text-blue-600"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Anonim & Aman</h4>
                    <p class="text-sm text-gray-500">Data Anda akan dijaga kerahasiaannya sesuai ketentuan</p>
                </div>
                <div class="text-center">
                    <div class="w-14 h-14 bg-emerald-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clock text-2xl text-emerald-600"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">5-10 Menit</h4>
                    <p class="text-sm text-gray-500">Estimasi waktu pengisian survey</p>
                </div>
                <div class="text-center">
                    <div class="w-14 h-14 bg-violet-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-chart-line text-2xl text-violet-600"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Feedback Berharga</h4>
                    <p class="text-sm text-gray-500">Membantu meningkatkan kualitas layanan</p>
                </div>
            </div>
        </div>
    </div>
</div>
