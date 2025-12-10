<x-opac.layout title="Verifikasi Sertifikat">
    <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
        <div class="max-w-md w-full">
            <div class="text-center mb-6">
                <div class="w-20 h-20 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shield-check text-3xl text-teal-600"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Verifikasi Sertifikat</h1>
                <p class="text-gray-500 mt-1">Sistem Cek Plagiasi Perpustakaan</p>
            </div>

            @if($found)
            {{-- Certificate Found --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden">
                <div class="p-4 bg-emerald-50 border-b border-emerald-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-emerald-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-white"></i>
                        </div>
                        <div>
                            <h2 class="font-bold text-emerald-800">Sertifikat Valid</h2>
                            <p class="text-sm text-emerald-600">{{ $certificate }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-4 space-y-4">
                    <div>
                        <label class="text-xs text-gray-500 uppercase tracking-wide">Nama</label>
                        <p class="font-semibold text-gray-900">{{ $check->member->name }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase tracking-wide">NIM / No. Anggota</label>
                        <p class="font-semibold text-gray-900">{{ $check->member->member_id }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase tracking-wide">Judul Dokumen</label>
                        <p class="font-semibold text-gray-900">{{ $check->document_title }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs text-gray-500 uppercase tracking-wide">Tanggal Cek</label>
                            <p class="font-semibold text-gray-900">{{ $check->completed_at->format('d M Y') }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 uppercase tracking-wide">Similarity</label>
                            <p @class([
                                'font-bold text-lg',
                                'text-emerald-600' => $check->similarity_level === 'low',
                                'text-amber-600' => $check->similarity_level === 'moderate',
                                'text-red-600' => in_array($check->similarity_level, ['high', 'critical']),
                            ])>{{ number_format($check->similarity_score, 1) }}%</p>
                        </div>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase tracking-wide">Status</label>
                        <div class="mt-1">
                            @if($check->isPassed())
                            <span class="px-3 py-1 bg-emerald-100 text-emerald-700 font-semibold rounded-full">
                                <i class="fas fa-check-circle mr-1"></i> LOLOS
                            </span>
                            @else
                            <span class="px-3 py-1 bg-amber-100 text-amber-700 font-semibold rounded-full">
                                <i class="fas fa-exclamation-triangle mr-1"></i> PERLU REVIEW
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @else
            {{-- Certificate Not Found --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden">
                <div class="p-4 bg-red-50 border-b border-red-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-times text-white"></i>
                        </div>
                        <div>
                            <h2 class="font-bold text-red-800">Sertifikat Tidak Ditemukan</h2>
                            <p class="text-sm text-red-600">{{ $certificate }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6 text-center">
                    <p class="text-gray-600">
                        Nomor sertifikat yang Anda masukkan tidak terdaftar dalam sistem kami.
                    </p>
                    <p class="text-sm text-gray-400 mt-2">
                        Pastikan nomor sertifikat dimasukkan dengan benar.
                    </p>
                </div>
            </div>
            @endif

            <div class="mt-6 text-center">
                <a href="{{ route('opac.home') }}" class="text-teal-600 hover:text-teal-700 font-medium">
                    <i class="fas fa-home mr-1"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</x-opac.layout>
