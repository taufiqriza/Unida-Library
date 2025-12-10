<x-opac.layout title="Hasil Cek Plagiasi">
    <div class="min-h-screen bg-gray-50">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-teal-600 to-emerald-700 text-white">
            <div class="max-w-4xl mx-auto px-4 py-6">
                <div class="flex items-center gap-3">
                    <a href="{{ route('opac.member.plagiarism.index') }}" class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center hover:bg-white/30 transition">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-xl font-bold">Hasil Cek Plagiasi</h1>
                        <p class="text-teal-200 text-sm">{{ $check->document_title }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-4xl mx-auto px-4 py-6">
            {{-- Status: Processing --}}
            @if($check->isPending() || $check->isProcessing())
            <div id="processing-state" class="bg-white rounded-2xl border border-gray-200 p-8 text-center">
                <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-spinner fa-spin text-3xl text-amber-600"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">
                    {{ $check->isProcessing() ? 'Sedang Memproses...' : 'Menunggu Antrian...' }}
                </h2>
                <p class="text-gray-500 mb-4">
                    Dokumen Anda sedang dicek. Proses ini mungkin memakan waktu beberapa menit.
                </p>
                <div class="flex items-center justify-center gap-4 text-sm text-gray-500">
                    <span><i class="fas fa-file mr-1"></i> {{ $check->original_filename }}</span>
                    <span><i class="fas fa-hdd mr-1"></i> {{ $check->file_size_formatted }}</span>
                </div>
                <p class="text-xs text-gray-400 mt-4">
                    Halaman ini akan refresh otomatis...
                </p>
            </div>

            @push('scripts')
            <script>
                // Poll for status update every 5 seconds
                setInterval(async function() {
                    try {
                        const response = await fetch('{{ route('opac.member.plagiarism.status', $check) }}');
                        const data = await response.json();
                        
                        if (data.is_completed || data.is_failed) {
                            window.location.reload();
                        }
                    } catch (e) {
                        console.error('Status check failed:', e);
                    }
                }, 5000);
            </script>
            @endpush

            {{-- Status: Failed --}}
            @elseif($check->isFailed())
            <div class="bg-white rounded-2xl border border-red-200 p-8 text-center">
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-times-circle text-3xl text-red-600"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">Pengecekan Gagal</h2>
                <p class="text-gray-500 mb-4">
                    {{ $check->error_message ?: 'Terjadi kesalahan saat memproses dokumen.' }}
                </p>
                <a href="{{ route('opac.member.plagiarism.create') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-teal-600 text-white font-medium rounded-xl hover:bg-teal-700 transition">
                    <i class="fas fa-redo"></i>
                    Coba Lagi
                </a>
            </div>

            {{-- Status: Completed --}}
            @else
            <div class="space-y-4">
                {{-- Result Summary --}}
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row items-center gap-6">
                            {{-- Score Circle --}}
                            <div class="relative">
                                <div @class([
                                    'w-32 h-32 rounded-full flex items-center justify-center border-8',
                                    'border-emerald-500 bg-emerald-50' => $check->similarity_level === 'low',
                                    'border-amber-500 bg-amber-50' => $check->similarity_level === 'moderate',
                                    'border-red-500 bg-red-50' => in_array($check->similarity_level, ['high', 'critical']),
                                ])>
                                    <div class="text-center">
                                        <p @class([
                                            'text-3xl font-bold',
                                            'text-emerald-600' => $check->similarity_level === 'low',
                                            'text-amber-600' => $check->similarity_level === 'moderate',
                                            'text-red-600' => in_array($check->similarity_level, ['high', 'critical']),
                                        ])>{{ number_format($check->similarity_score, 1) }}%</p>
                                        <p class="text-xs text-gray-500">Similarity</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Info --}}
                            <div class="flex-1 text-center md:text-left">
                                <div class="flex items-center justify-center md:justify-start gap-2 mb-2">
                                    @if($check->isPassed())
                                    <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-sm font-semibold rounded-full">
                                        <i class="fas fa-check-circle mr-1"></i> LOLOS
                                    </span>
                                    @else
                                    <span class="px-3 py-1 bg-red-100 text-red-700 text-sm font-semibold rounded-full">
                                        <i class="fas fa-exclamation-triangle mr-1"></i> PERLU REVIEW
                                    </span>
                                    @endif
                                </div>
                                <h2 class="text-lg font-bold text-gray-900">{{ $check->document_title }}</h2>
                                <p class="text-sm text-gray-500 mt-1">{{ $check->original_filename }}</p>
                                
                                <div class="flex flex-wrap items-center justify-center md:justify-start gap-4 mt-3 text-sm text-gray-500">
                                    <span><i class="fas fa-calendar mr-1"></i> {{ $check->completed_at->format('d M Y, H:i') }}</span>
                                    <span><i class="fas fa-file mr-1"></i> {{ $check->file_size_formatted }}</span>
                                    @if($check->word_count)
                                    <span><i class="fas fa-font mr-1"></i> {{ number_format($check->word_count) }} kata</span>
                                    @endif
                                    @if($check->processing_time)
                                    <span><i class="fas fa-clock mr-1"></i> {{ $check->processing_time }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Score Legend --}}
                <div class="bg-white rounded-2xl border border-gray-200 p-4">
                    <h3 class="font-semibold text-gray-900 mb-3">Keterangan Skor</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 bg-emerald-500 rounded-full"></span>
                            <span>0-15%: Aman</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 bg-amber-500 rounded-full"></span>
                            <span>16-25%: Sedang</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 bg-orange-500 rounded-full"></span>
                            <span>26-40%: Tinggi</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                            <span>&gt;40%: Sangat Tinggi</span>
                        </div>
                    </div>
                </div>

                {{-- Matched Sources --}}
                @if($check->similarity_sources && count($check->similarity_sources) > 0)
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                    <div class="p-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-link text-teal-600"></i>
                            Sumber yang Cocok
                        </h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @foreach($check->similarity_sources as $index => $source)
                        <div class="p-4 flex items-start gap-3">
                            <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0 text-sm font-medium text-gray-600">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 line-clamp-1">{{ $source['title'] ?? 'Dokumen' }}</p>
                                <p class="text-sm text-gray-500 mt-0.5">
                                    {{ $source['author'] ?? '-' }} 
                                    @if(isset($source['year']))• {{ $source['year'] }}@endif
                                    @if(isset($source['department']))• {{ $source['department'] }}@endif
                                </p>
                            </div>
                            <span @class([
                                'px-2 py-1 text-sm font-semibold rounded-lg flex-shrink-0',
                                'bg-emerald-100 text-emerald-700' => $source['similarity'] <= 5,
                                'bg-amber-100 text-amber-700' => $source['similarity'] > 5 && $source['similarity'] <= 10,
                                'bg-red-100 text-red-700' => $source['similarity'] > 10,
                            ])>
                                {{ $source['similarity'] }}%
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="bg-emerald-50 rounded-2xl border border-emerald-200 p-6 text-center">
                    <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-check-double text-2xl text-emerald-600"></i>
                    </div>
                    <p class="font-semibold text-emerald-800">Tidak ditemukan kecocokan signifikan</p>
                    <p class="text-sm text-emerald-600 mt-1">Dokumen Anda tidak memiliki similarity yang perlu diperhatikan dengan database E-Thesis.</p>
                </div>
                @endif

                {{-- Certificate Section --}}
                @if($check->isCompleted())
                <div class="bg-gradient-to-br from-teal-50 to-emerald-50 rounded-2xl border border-teal-200 p-6">
                    <div class="flex flex-col md:flex-row items-center gap-4">
                        <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center shadow-sm">
                            <i class="fas fa-certificate text-2xl text-teal-600"></i>
                        </div>
                        <div class="flex-1 text-center md:text-left">
                            <h3 class="font-bold text-teal-800">Sertifikat Plagiasi</h3>
                            <p class="text-sm text-teal-600">
                                @if($check->certificate_number)
                                No: {{ $check->certificate_number }}
                                @else
                                Sertifikat akan dibuat saat diunduh
                                @endif
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('opac.member.plagiarism.certificate', $check) }}" 
                               class="px-4 py-2 bg-teal-600 text-white font-medium rounded-xl hover:bg-teal-700 transition flex items-center gap-2">
                                <i class="fas fa-eye"></i>
                                <span class="hidden sm:inline">Lihat</span>
                            </a>
                            <a href="{{ route('opac.member.plagiarism.certificate.download', $check) }}" 
                               class="px-4 py-2 bg-emerald-600 text-white font-medium rounded-xl hover:bg-emerald-700 transition flex items-center gap-2">
                                <i class="fas fa-download"></i>
                                <span class="hidden sm:inline">Unduh PDF</span>
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Provider Info --}}
                <div class="text-center text-xs text-gray-400">
                    <p>Diperiksa menggunakan: {{ $check->provider_label }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-opac.layout>
