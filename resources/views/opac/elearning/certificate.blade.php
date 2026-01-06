<x-opac.layout>
<div class="min-h-screen bg-gray-50">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
        <div class="max-w-3xl mx-auto px-4 py-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('opac.classroom', $cert->enrollment->course->slug) }}" class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center hover:bg-white/30 transition">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-xl font-bold">Sertifikat E-Learning</h1>
                    <p class="text-blue-200 text-sm">{{ $cert->certificate_number }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 py-6">
        {{-- Certificate Preview Card --}}
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-lg">
            {{-- Header --}}
            <div class="p-6 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-center">
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-award text-3xl"></i>
                </div>
                <h2 class="text-2xl font-bold">Sertifikat Kelulusan</h2>
                <p class="text-blue-100 text-sm mt-2">Perpustakaan Universitas Darussalam Gontor</p>
            </div>

            {{-- Content --}}
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-gray-500 block">Nomor Sertifikat</label>
                        <p class="font-bold text-gray-900">{{ $cert->certificate_number }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 block">Tanggal Terbit</label>
                        <p class="font-medium text-gray-900">{{ $cert->issued_at->translatedFormat('d F Y') }}</p>
                    </div>
                </div>

                <div>
                    <label class="text-xs text-gray-500 block">Nama Kelas</label>
                    <p class="font-semibold text-gray-900">{{ $cert->enrollment->course->title }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-gray-500 block">Nama Peserta</label>
                        <p class="font-medium text-gray-900">{{ $cert->enrollment->member->name }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 block">No. Anggota</label>
                        <p class="font-medium text-gray-900">{{ $cert->enrollment->member->member_id }}</p>
                    </div>
                </div>

                {{-- Completion Badge --}}
                <div class="p-4 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-xl text-center border border-emerald-200">
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-full flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-check text-white text-lg"></i>
                    </div>
                    <p class="text-emerald-700 font-bold">LULUS</p>
                    <p class="text-emerald-600 text-xs">Telah menyelesaikan seluruh materi</p>
                </div>

                <div class="text-center text-sm text-gray-500">
                    <p>Instruktur: <strong class="text-gray-700">{{ $cert->enrollment->course->instructor->name }}</strong></p>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="mt-4 flex gap-3">
            <button onclick="window.print()" class="flex-1 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-indigo-700 transition flex items-center justify-center gap-2">
                <i class="fas fa-print"></i> Cetak
            </button>
            <a href="{{ route('opac.classroom', $cert->enrollment->course->slug) }}" class="flex-1 py-3 bg-white border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition flex items-center justify-center gap-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>
</x-opac.layout>
