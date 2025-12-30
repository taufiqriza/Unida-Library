<div>
    <div class="min-h-screen bg-gray-50">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-primary-600 to-primary-800 text-white">
            <div class="max-w-5xl mx-auto px-4 py-6">
                <div class="flex items-center gap-3">
                    <a href="{{ route('opac.member.plagiarism.index') }}" class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center hover:bg-white/30 transition">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-xl font-bold">Submit Hasil Cek Plagiasi Eksternal</h1>
                        <p class="text-primary-200 text-sm">Upload hasil dari Turnitin, iThenticate, atau Copylakes</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-3xl mx-auto px-4 py-6">
            {{-- Info Banner --}}
            <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl mb-6">
                <div class="flex gap-3">
                    <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-info-circle text-amber-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-amber-800">Pengajuan Eksternal</h4>
                        <p class="text-sm text-amber-700 mt-1">
                            Karena sistem iThenticate kami sedang dalam pemeliharaan, Anda dapat mengajukan hasil cek plagiasi dari platform eksternal. 
                            Pustakawan akan mereview dan menerbitkan surat bebas plagiasi jika skor di bawah batas toleransi (25%).
                        </p>
                    </div>
                </div>
            </div>

            <form wire:submit="submit" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
                {{-- Document Title --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Judul Dokumen <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="document_title" 
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition"
                           placeholder="Masukkan judul skripsi/tesis/disertasi">
                    @error('document_title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Platform --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Platform yang Digunakan <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-3 gap-3">
                        @foreach(['turnitin' => 'Turnitin', 'ithenticate' => 'iThenticate', 'copylakes' => 'Copylakes'] as $value => $label)
                        <label class="relative cursor-pointer">
                            <input type="radio" wire:model.live="platform" value="{{ $value }}" class="peer sr-only">
                            <div class="p-3 border-2 border-gray-200 rounded-xl text-center transition peer-checked:border-primary-500 peer-checked:bg-primary-50 hover:border-gray-300">
                                <i class="fas fa-check-circle text-lg mb-1 {{ $platform === $value ? 'text-primary-500' : 'text-gray-300' }}"></i>
                                <p class="text-sm font-medium {{ $platform === $value ? 'text-primary-700' : 'text-gray-600' }}">{{ $label }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('platform') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Similarity Score --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Skor Similarity (%) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" wire:model="similarity_score" step="0.01" min="0" max="100"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition"
                           placeholder="Contoh: 15.5">
                    <p class="text-xs text-gray-500 mt-1">Masukkan skor similarity sesuai hasil report</p>
                    @error('similarity_score') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Document File --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        File Dokumen (PDF) <span class="text-red-500">*</span>
                    </label>
                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 hover:border-primary-400 transition relative">
                        <input type="file" wire:model="document_file" accept=".pdf" class="absolute inset-0 opacity-0 cursor-pointer z-10">
                        <div class="text-center">
                            @if($document_file)
                                <i class="fas fa-file-pdf text-4xl text-red-500 mb-2"></i>
                                <p class="text-sm text-primary-600 font-medium">{{ $document_file->getClientOriginalName() }}</p>
                                <p class="text-xs text-gray-500">{{ number_format($document_file->getSize() / 1024 / 1024, 2) }} MB</p>
                            @else
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-300 mb-2"></i>
                                <p class="text-sm text-gray-500">Klik atau drag file PDF dokumen</p>
                                <p class="text-xs text-gray-400">Maksimal 50MB</p>
                            @endif
                        </div>
                        <div wire:loading wire:target="document_file" class="absolute inset-0 bg-white/80 flex items-center justify-center rounded-xl">
                            <i class="fas fa-spinner fa-spin text-primary-500 text-2xl"></i>
                        </div>
                    </div>
                    @error('document_file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Report File --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Bukti Report Similarity <span class="text-red-500">*</span>
                    </label>
                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 hover:border-primary-400 transition relative">
                        <input type="file" wire:model="report_file" accept=".pdf,.jpg,.jpeg,.png" class="absolute inset-0 opacity-0 cursor-pointer z-10">
                        <div class="text-center">
                            @if($report_file)
                                <i class="fas fa-file-image text-4xl text-blue-500 mb-2"></i>
                                <p class="text-sm text-primary-600 font-medium">{{ $report_file->getClientOriginalName() }}</p>
                                <p class="text-xs text-gray-500">{{ number_format($report_file->getSize() / 1024 / 1024, 2) }} MB</p>
                            @else
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-300 mb-2"></i>
                                <p class="text-sm text-gray-500">Upload screenshot/PDF hasil report</p>
                                <p class="text-xs text-gray-400">PDF, JPG, PNG - Maksimal 10MB</p>
                            @endif
                        </div>
                        <div wire:loading wire:target="report_file" class="absolute inset-0 bg-white/80 flex items-center justify-center rounded-xl">
                            <i class="fas fa-spinner fa-spin text-primary-500 text-2xl"></i>
                        </div>
                    </div>
                    @error('report_file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Submit Button --}}
                <div class="pt-4 border-t border-gray-100">
                    <button type="submit" 
                            wire:loading.attr="disabled"
                            class="w-full py-3 bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white font-semibold rounded-xl shadow-lg shadow-primary-500/30 transition flex items-center justify-center gap-2 disabled:opacity-50">
                        <span wire:loading.remove wire:target="submit">
                            <i class="fas fa-paper-plane"></i> Kirim Pengajuan
                        </span>
                        <span wire:loading wire:target="submit">
                            <i class="fas fa-spinner fa-spin"></i> Mengirim...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
