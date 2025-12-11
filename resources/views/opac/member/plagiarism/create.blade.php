<x-opac.layout title="Cek Plagiasi">
    <div class="min-h-screen bg-gray-50">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-primary-600 to-primary-800 text-white">
            <div class="max-w-5xl mx-auto px-4 py-6">
                <div class="flex items-center gap-3">
                    <a href="{{ route('opac.member.plagiarism.index') }}" class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center hover:bg-white/30 transition">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-xl font-bold">Cek Plagiasi</h1>
                        <p class="text-primary-200 text-sm">Upload dokumen untuk mengecek tingkat plagiarisme</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-5xl mx-auto px-4 py-6">
            {{-- Errors --}}
            @if($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800">
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Quota Info & Notice --}}
            <div class="mb-4 space-y-3">
                {{-- Notice for Tugas Akhir --}}
                <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-amber-600 text-sm"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-amber-800 text-sm">Khusus Dokumen Tugas Akhir</h4>
                            <p class="text-amber-700 text-xs mt-1">
                                Layanan cek plagiasi ini diperuntukkan <strong>hanya untuk dokumen tugas akhir</strong> 
                                (Skripsi, Tesis, atau Disertasi) yang akan disubmit ke perpustakaan.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Quota Card --}}
                <div class="p-4 bg-teal-50 border border-teal-200 rounded-xl">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-teal-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-ticket-alt text-teal-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-teal-800 text-sm">Kuota Pengecekan</h4>
                                <p class="text-teal-600 text-xs">Maksimal {{ $quotaLimit }} kali pengecekan</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-teal-700">{{ $remainingQuota }}</p>
                            <p class="text-xs text-teal-600">tersisa</p>
                        </div>
                    </div>
                    @if($usedQuota > 0)
                    <div class="mt-3 pt-3 border-t border-teal-200">
                        <p class="text-xs text-teal-600">
                            <i class="fas fa-history mr-1"></i> 
                            Anda sudah menggunakan {{ $usedQuota }} dari {{ $quotaLimit }} kuota.
                            <a href="{{ route('opac.member.plagiarism.index') }}" class="underline hover:text-teal-800">Lihat riwayat →</a>
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            <form action="{{ route('opac.member.plagiarism.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                {{-- Member Info (Auto-filled) --}}
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                    <div class="p-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-user text-teal-600"></i>
                            Informasi Pengaju
                        </h3>
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Nama</label>
                                <p class="font-medium text-gray-900">{{ $member->name }}</p>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">NIM / No. Anggota</label>
                                <p class="font-medium text-gray-900">{{ $member->member_id }}</p>
                            </div>
                            @if($member->memberType)
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Jenis Anggota</label>
                                <p class="font-medium text-gray-900">{{ $member->memberType->name }}</p>
                            </div>
                            @endif
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Email</label>
                                <p class="font-medium text-gray-900">{{ $member->email }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Document Info --}}
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                    <div class="p-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-file-alt text-teal-600"></i>
                            Informasi Dokumen
                        </h3>
                    </div>
                    <div class="p-4 space-y-4">
                        <div>
                            <label for="document_title" class="block text-sm font-medium text-gray-700 mb-1">
                                Judul Dokumen <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="document_title" 
                                   id="document_title" 
                                   value="{{ old('document_title') }}"
                                   placeholder="Contoh: Implementasi Sistem Informasi Perpustakaan Berbasis Web"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                   required>
                        </div>

                        @if($submissions->count() > 0)
                        <div>
                            <label for="thesis_submission_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Kaitkan dengan Submission (Opsional)
                            </label>
                            <select name="thesis_submission_id" 
                                    id="thesis_submission_id"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                <option value="">-- Tidak dikaitkan --</option>
                                @foreach($submissions as $sub)
                                <option value="{{ $sub->id }}">{{ Str::limit($sub->title, 60) }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- File Upload --}}
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                    <div class="p-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-upload text-teal-600"></i>
                            Upload Dokumen
                        </h3>
                    </div>
                    <div class="p-4">
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-teal-400 transition cursor-pointer" 
                             onclick="document.getElementById('document').click()">
                            <div id="upload-preview" class="hidden">
                                <div class="w-16 h-16 bg-teal-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-file-pdf text-2xl text-teal-600"></i>
                                </div>
                                <p id="file-name" class="font-medium text-gray-900"></p>
                                <p id="file-size" class="text-sm text-gray-500 mt-1"></p>
                            </div>
                            <div id="upload-placeholder">
                                <div class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-cloud-upload-alt text-2xl text-gray-400"></i>
                                </div>
                                <p class="text-gray-600 font-medium">Klik untuk memilih file</p>
                                <p class="text-gray-400 text-sm mt-1">atau drag & drop di sini</p>
                                <p class="text-xs text-gray-400 mt-3">Format: PDF, DOCX • Maks. 20MB</p>
                            </div>
                        </div>
                        <input type="file" 
                               name="document" 
                               id="document" 
                               accept=".pdf,.docx"
                               class="hidden"
                               required
                               onchange="handleFileSelect(this)">
                    </div>
                </div>

                {{-- Agreement --}}
                <div class="bg-white rounded-2xl border border-gray-200 p-4">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" 
                               name="agreement" 
                               value="1"
                               class="mt-1 w-5 h-5 text-teal-600 border-gray-300 rounded focus:ring-teal-500"
                               required>
                        <span class="text-sm text-gray-700">
                            Saya menyatakan bahwa dokumen ini adalah <strong>karya saya sendiri</strong> dan saya bertanggung jawab atas isi dokumen ini. 
                            Saya memahami bahwa hasil pengecekan ini hanya untuk referensi dan tidak menjamin dokumen bebas dari plagiarisme.
                        </span>
                    </label>
                </div>

                {{-- Submit Button --}}
                <button type="submit" 
                        class="w-full py-4 bg-gradient-to-r from-teal-500 to-emerald-600 text-white font-semibold rounded-xl hover:from-teal-600 hover:to-emerald-700 transition shadow-lg shadow-teal-500/30 flex items-center justify-center gap-2">
                    <i class="fas fa-search"></i>
                    Mulai Pengecekan Plagiasi
                </button>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function handleFileSelect(input) {
            const file = input.files[0];
            if (file) {
                document.getElementById('upload-placeholder').classList.add('hidden');
                document.getElementById('upload-preview').classList.remove('hidden');
                document.getElementById('file-name').textContent = file.name;
                document.getElementById('file-size').textContent = formatFileSize(file.size);
            }
        }

        function formatFileSize(bytes) {
            if (bytes >= 1048576) {
                return (bytes / 1048576).toFixed(2) + ' MB';
            } else if (bytes >= 1024) {
                return (bytes / 1024).toFixed(2) + ' KB';
            }
            return bytes + ' bytes';
        }
    </script>
    @endpush
</x-opac.layout>
