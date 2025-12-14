<div>
    <div class="min-h-screen bg-gray-50">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-teal-600 to-emerald-700 text-white">
            <div class="max-w-3xl mx-auto px-4 py-6">
                <div class="flex items-center gap-3">
                    <a href="{{ route('opac.member.plagiarism.show', $check) }}" class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center hover:bg-white/30 transition">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-xl font-bold">Sertifikat Plagiasi</h1>
                        <p class="text-teal-200 text-sm">{{ $check->certificate_number }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-3xl mx-auto px-4 py-6">
            {{-- Certificate Preview Card --}}
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-lg">
                {{-- Header --}}
                <div class="p-6 bg-gradient-to-r from-teal-500 to-emerald-600 text-white text-center">
                    <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-certificate text-3xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold">Sertifikat Hasil Cek Plagiasi</h2>
                    <p class="text-teal-100 text-sm mt-2">Perpustakaan Universitas Darussalam Gontor</p>
                </div>

                {{-- Content --}}
                <div class="p-6 space-y-6">
                    {{-- Document Info --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs text-gray-500 block">Nomor Sertifikat</label>
                            <p class="font-bold text-gray-900">{{ $check->certificate_number }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 block">Tanggal Terbit</label>
                            <p class="font-medium text-gray-900">{{ $check->completed_at?->format('d F Y') }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs text-gray-500 block">Judul Dokumen</label>
                        <p class="font-medium text-gray-900">{{ $check->document_title }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs text-gray-500 block">Nama</label>
                            <p class="font-medium text-gray-900">{{ $member->name }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 block">NIM / No. Anggota</label>
                            <p class="font-medium text-gray-900">{{ $member->member_id }}</p>
                        </div>
                    </div>

                    {{-- Similarity Score --}}
                    <div class="p-4 bg-gray-50 rounded-xl text-center">
                        <label class="text-xs text-gray-500 block mb-2">TINGKAT SIMILARITY</label>
                        <div class="text-5xl font-bold {{ $check->similarity_level === 'low' ? 'text-emerald-600' : ($check->similarity_level === 'moderate' ? 'text-amber-600' : 'text-red-600') }}">
                            {{ $check->similarity_score }}%
                        </div>
                        <span class="inline-block mt-2 px-3 py-1 text-sm font-medium rounded-full
                            @if($check->similarity_level === 'low') bg-emerald-100 text-emerald-700
                            @elseif($check->similarity_level === 'moderate') bg-amber-100 text-amber-700
                            @else bg-red-100 text-red-700
                            @endif">
                            @if($check->similarity_level === 'low') LOLOS
                            @elseif($check->similarity_level === 'moderate') PERINGATAN
                            @else TIDAK LOLOS
                            @endif
                        </span>
                    </div>

                    {{-- Provider Info --}}
                    <div class="text-center text-sm text-gray-500">
                        <p>Pengecekan menggunakan: <strong class="text-gray-700">{{ $check->provider_label }}</strong></p>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="mt-6 flex flex-col sm:flex-row gap-3">
                <a href="{{ route('opac.member.plagiarism.certificate.download', $check) }}" 
                   class="flex-1 py-4 bg-gradient-to-r from-teal-500 to-emerald-600 text-white font-semibold rounded-xl hover:from-teal-600 hover:to-emerald-700 transition shadow-lg shadow-teal-500/30 flex items-center justify-center gap-2">
                    <i class="fas fa-download"></i>
                    Download Sertifikat PDF
                </a>
                <a href="{{ route('opac.member.plagiarism.index') }}" 
                   class="flex-1 py-4 bg-white border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition flex items-center justify-center gap-2">
                    <i class="fas fa-list"></i>
                    Kembali ke Riwayat
                </a>
            </div>

            {{-- QR Verification Note --}}
            <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-xl">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-qrcode text-blue-600 text-sm"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-blue-800 text-sm">Verifikasi Sertifikat</h4>
                        <p class="text-blue-700 text-xs mt-1">
                            Sertifikat PDF dilengkapi dengan QR Code untuk verifikasi keaslian. 
                            Scan QR Code atau kunjungi 
                            <a href="{{ route('plagiarism.verify', $check->certificate_number) }}" class="underline">halaman verifikasi</a>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
