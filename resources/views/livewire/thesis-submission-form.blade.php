<div class="max-w-4xl mx-auto px-1">
    {{-- Revision Notice --}}
    @if($isEdit && $submission?->isRevisionRequired())
        <div class="mb-6 p-4 bg-orange-50 border border-orange-200 rounded-2xl">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-orange-600"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-orange-800">Revisi Diperlukan</h3>
                    <p class="text-sm text-orange-700 mt-1">{{ $submission->review_notes }}</p>
                    <p class="text-xs text-orange-600 mt-2">
                        <i class="fas fa-clock mr-1"></i>
                        Direview oleh {{ $submission->reviewer?->name }} pada {{ $submission->reviewed_at?->format('d M Y H:i') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Progress Header --}}
    <div class="mb-6 lg:mb-8">
        {{-- Completion Bar --}}
        <div class="mb-4 bg-gray-100 rounded-full h-2 overflow-hidden">
            <div class="bg-gradient-to-r from-primary-500 to-primary-600 h-full transition-all duration-500" style="width: {{ $this->completionPercentage }}%"></div>
        </div>
        
        {{-- Step Indicators --}}
        <div class="flex items-center justify-between relative">
            <div class="absolute top-4 lg:top-5 left-0 right-0 h-0.5 bg-gray-200 mx-8 lg:mx-12"></div>
            <div class="absolute top-4 lg:top-5 left-0 h-0.5 bg-primary-600 mx-8 lg:mx-12 transition-all duration-300" style="width: {{ ($step - 1) * 25 }}%"></div>
            
            @foreach([
                ['step' => 1, 'label' => 'Informasi', 'icon' => 'fa-file-alt'],
                ['step' => 2, 'label' => 'Penulis', 'icon' => 'fa-user'],
                ['step' => 3, 'label' => 'Dosen', 'icon' => 'fa-chalkboard-teacher'],
                ['step' => 4, 'label' => 'Dokumen', 'icon' => 'fa-cloud-upload-alt'],
                ['step' => 5, 'label' => 'Kirim', 'icon' => 'fa-paper-plane'],
            ] as $s)
                <button 
                    wire:click="goToStep({{ $s['step'] }})"
                    type="button"
                    @class([
                        'flex flex-col items-center z-10 group',
                        'cursor-pointer' => $step > $s['step'],
                        'cursor-default' => $step <= $s['step'],
                    ])
                    @if($step < $s['step']) disabled @endif
                >
                    <div @class([
                        'w-8 h-8 lg:w-10 lg:h-10 rounded-full flex items-center justify-center transition-all border-2',
                        'bg-primary-600 border-primary-600 text-white shadow-lg shadow-primary-200' => $step === $s['step'],
                        'bg-primary-600 border-primary-600 text-white' => $step > $s['step'],
                        'bg-white border-gray-300 text-gray-400' => $step < $s['step'],
                        'group-hover:scale-110' => $step > $s['step'],
                    ])>
                        @if($step > $s['step'])
                            <i class="fas fa-check text-xs lg:text-sm"></i>
                        @else
                            <i class="fas {{ $s['icon'] }} text-xs lg:text-sm"></i>
                        @endif
                    </div>
                    <span @class([
                        'text-[9px] lg:text-xs mt-1.5 font-medium transition-colors',
                        'text-primary-600' => $step >= $s['step'],
                        'text-gray-400' => $step < $s['step'],
                    ])>{{ $s['label'] }}</span>
                </button>
            @endforeach
        </div>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        {{-- Step 1: Informasi Dasar --}}
        @if($step === 1)
        <div class="p-4 lg:p-6 border-b border-gray-100 bg-gradient-to-r from-primary-50 to-white">
            <h2 class="text-base lg:text-lg font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-file-alt text-primary-500"></i> Informasi Tugas Akhir
            </h2>
            <p class="text-xs lg:text-sm text-gray-500 mt-1">Lengkapi informasi dasar tugas akhir Anda</p>
        </div>
        <div class="p-4 lg:p-6 space-y-5">
            {{-- Jenis Tugas Akhir - Card Style --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Jenis Tugas Akhir <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-3 gap-3">
                    {{-- Skripsi --}}
                    <label class="cursor-pointer">
                        <input type="radio" wire:model.live="type" value="skripsi" class="sr-only peer">
                        <div class="p-4 border-2 rounded-xl text-center transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50 border-gray-200 hover:border-gray-300">
                            <div class="w-12 h-12 mx-auto mb-2 rounded-full bg-blue-100 flex items-center justify-center">
                                <i class="fas fa-graduation-cap text-blue-600 text-xl"></i>
                            </div>
                            <p class="font-semibold text-gray-900">S1</p>
                            <p class="text-xs text-gray-500">Skripsi</p>
                        </div>
                    </label>
                    {{-- Tesis --}}
                    <label class="cursor-pointer">
                        <input type="radio" wire:model.live="type" value="tesis" class="sr-only peer">
                        <div class="p-4 border-2 rounded-xl text-center transition-all peer-checked:border-purple-500 peer-checked:bg-purple-50 border-gray-200 hover:border-gray-300">
                            <div class="w-12 h-12 mx-auto mb-2 rounded-full bg-purple-100 flex items-center justify-center">
                                <i class="fas fa-user-graduate text-purple-600 text-xl"></i>
                            </div>
                            <p class="font-semibold text-gray-900">S2</p>
                            <p class="text-xs text-gray-500">Tesis</p>
                        </div>
                    </label>
                    {{-- Disertasi --}}
                    <label class="cursor-pointer">
                        <input type="radio" wire:model.live="type" value="disertasi" class="sr-only peer">
                        <div class="p-4 border-2 rounded-xl text-center transition-all peer-checked:border-amber-500 peer-checked:bg-amber-50 border-gray-200 hover:border-gray-300">
                            <div class="w-12 h-12 mx-auto mb-2 rounded-full bg-amber-100 flex items-center justify-center">
                                <i class="fas fa-award text-amber-600 text-xl"></i>
                            </div>
                            <p class="font-semibold text-gray-900">S3</p>
                            <p class="text-xs text-gray-500">Disertasi</p>
                        </div>
                    </label>
                </div>
                @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Judul <span class="text-red-500">*</span></label>
                <textarea wire:model="title" rows="2" class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition" placeholder="Masukkan judul tugas akhir dalam Bahasa Indonesia"></textarea>
                <p class="text-xs text-gray-400 mt-1">{{ strlen($title) }}/500 karakter</p>
                @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Judul (English) 
                    <span class="text-gray-400 font-normal">- opsional</span>
                </label>
                <textarea wire:model="title_en" rows="2" class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition" placeholder="English title (optional)"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Abstrak <span class="text-red-500">*</span></label>
                <textarea wire:model="abstract" rows="5" class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition" placeholder="Masukkan abstrak tugas akhir (minimal 100 karakter)"></textarea>
                <p class="text-xs {{ strlen($abstract) >= 100 ? 'text-green-600' : 'text-gray-400' }} mt-1">
                    {{ strlen($abstract) }}/100 karakter minimum
                    @if(strlen($abstract) >= 100) <i class="fas fa-check-circle ml-1"></i> @endif
                </p>
                @error('abstract') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Abstract (English)
                    <span class="text-gray-400 font-normal">- opsional</span>
                </label>
                <textarea wire:model="abstract_en" rows="4" class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition" placeholder="English abstract (optional)"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tahun <span class="text-red-500">*</span></label>
                    <input type="number" wire:model="year" min="2000" max="{{ date('Y') + 1 }}" class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                    @error('year') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Tanggal Sidang
                        <span class="text-gray-400 font-normal">- opsional</span>
                    </label>
                    <input type="date" wire:model="defense_date" class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Kata Kunci
                    <span class="text-gray-400 font-normal">- opsional</span>
                </label>
                <input type="text" wire:model="keywords" class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition" placeholder="Contoh: machine learning, klasifikasi, neural network">
                <p class="text-xs text-gray-400 mt-1">Pisahkan dengan koma</p>
            </div>
        </div>
        @endif

        {{-- Step 2: Data Penulis --}}
        @if($step === 2)
        <div class="p-4 lg:p-6 border-b border-gray-100 bg-gradient-to-r from-primary-50 to-white">
            <h2 class="text-base lg:text-lg font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-user text-primary-500"></i> Data Penulis
            </h2>
            <p class="text-xs lg:text-sm text-gray-500 mt-1">Informasi penulis dan program studi</p>
        </div>
        <div class="p-4 lg:p-6 space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="author" class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition bg-gray-50" readonly>
                    <p class="text-xs text-gray-400 mt-1">Diambil dari data akun Anda</p>
                    @error('author') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">NIM <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="nim" class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition bg-gray-50" readonly>
                    <p class="text-xs text-gray-400 mt-1">Diambil dari data akun Anda</p>
                    @error('nim') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Fakultas <span class="text-red-500">*</span></label>
                <select wire:model.live="faculty_id" class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition bg-white">
                    <option value="">-- Pilih Fakultas --</option>
                    @foreach($this->faculties as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Program Studi <span class="text-red-500">*</span></label>
                <select wire:model="department_id" class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition bg-white disabled:bg-gray-100 disabled:cursor-not-allowed" {{ !$faculty_id ? 'disabled' : '' }}>
                    <option value="">{{ $faculty_id ? '-- Pilih Program Studi --' : '-- Pilih fakultas terlebih dahulu --' }}</option>
                    @foreach($this->departments as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                @error('department_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        @endif

        {{-- Step 3: Pembimbing & Penguji --}}
        @if($step === 3)
        <div class="p-4 lg:p-6 border-b border-gray-100 bg-gradient-to-r from-primary-50 to-white">
            <h2 class="text-base lg:text-lg font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-chalkboard-teacher text-primary-500"></i> Pembimbing & Penguji
            </h2>
            <p class="text-xs lg:text-sm text-gray-500 mt-1">Data dosen pembimbing dan penguji</p>
        </div>
        <div class="p-4 lg:p-6 space-y-5">
            {{-- Pembimbing Section --}}
            <div class="space-y-4">
                <h3 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                    <span class="w-6 h-6 bg-primary-100 text-primary-600 rounded-lg flex items-center justify-center text-xs">
                        <i class="fas fa-user-tie"></i>
                    </span>
                    Dosen Pembimbing
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Pembimbing 1 <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="advisor1" class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition" placeholder="Nama lengkap dengan gelar">
                        @error('advisor1') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Pembimbing 2
                            <span class="text-gray-400 font-normal">- opsional</span>
                        </label>
                        <input type="text" wire:model="advisor2" class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition" placeholder="Nama lengkap dengan gelar">
                    </div>
                </div>
            </div>

            {{-- Penguji Section --}}
            <div class="space-y-4 pt-4 border-t border-gray-100">
                <h3 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                    <span class="w-6 h-6 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center text-xs">
                        <i class="fas fa-users"></i>
                    </span>
                    Dosen Penguji
                    <span class="text-gray-400 font-normal text-xs">- opsional</span>
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Penguji 1</label>
                        <input type="text" wire:model="examiner1" class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition" placeholder="Nama dengan gelar">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Penguji 2</label>
                        <input type="text" wire:model="examiner2" class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition" placeholder="Nama dengan gelar">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Penguji 3</label>
                        <input type="text" wire:model="examiner3" class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition" placeholder="Nama dengan gelar">
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Step 4: Upload Files --}}
        @if($step === 4)
        <div class="p-4 lg:p-6 border-b border-gray-100 bg-gradient-to-r from-primary-50 to-white">
            <h2 class="text-base lg:text-lg font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-cloud-upload-alt text-primary-500"></i> Upload Dokumen
            </h2>
            <p class="text-xs lg:text-sm text-gray-500 mt-1">Unggah file dokumen tugas akhir Anda</p>
        </div>
        <div class="p-4 lg:p-6 space-y-5">
            {{-- File Requirements Info --}}
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl">
                <h4 class="text-sm font-semibold text-blue-800 flex items-center gap-2">
                    <i class="fas fa-info-circle"></i> Ketentuan File
                </h4>
                <ul class="mt-2 text-xs text-blue-700 space-y-1">
                    <li><i class="fas fa-check mr-1.5 text-blue-500"></i> Cover: Format gambar (JPG/PNG), maksimal 2MB</li>
                    <li><i class="fas fa-check mr-1.5 text-blue-500"></i> Lembar Pengesahan: Format PDF, maksimal 5MB</li>
                    <li><i class="fas fa-check mr-1.5 text-blue-500"></i> BAB 1-3: Format PDF, maksimal 20MB (akan ditampilkan publik)</li>
                    <li><i class="fas fa-check mr-1.5 text-blue-500"></i> Full Text: Format PDF, maksimal 50MB (opsional)</li>
                </ul>
            </div>

            {{-- Required Files Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Cover --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">
                        <i class="fas fa-image text-primary-500 mr-1"></i> Cover <span class="text-red-500">*</span>
                    </label>
                    <div class="border-2 border-dashed border-gray-200 rounded-xl hover:border-primary-400 transition aspect-[3/4] flex flex-col items-center justify-center relative overflow-hidden group">
                        <input type="file" wire:model="cover_file" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer z-10" id="cover_file">
                        @if($cover_file)
                            <img src="{{ $cover_file->temporaryUrl() }}" class="absolute inset-0 w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                <span class="text-white text-xs font-medium">Ganti File</span>
                            </div>
                        @elseif($isEdit && $submission?->cover_file)
                            <img src="{{ Storage::url($submission->cover_file) }}" class="absolute inset-0 w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                <span class="text-white text-xs font-medium">Ganti File</span>
                            </div>
                        @else
                            <i class="fas fa-image text-3xl text-gray-300 mb-2"></i>
                            <p class="text-xs text-gray-500">Upload Cover</p>
                            <p class="text-[10px] text-gray-400">JPG/PNG, max 2MB</p>
                        @endif
                    </div>
                    @error('cover_file') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Approval --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">
                        <i class="fas fa-file-signature text-primary-500 mr-1"></i> Lembar Pengesahan <span class="text-red-500">*</span>
                    </label>
                    <div class="border-2 border-dashed border-gray-200 rounded-xl hover:border-primary-400 transition aspect-[3/4] flex flex-col items-center justify-center relative overflow-hidden group">
                        <input type="file" wire:model="approval_file" accept=".pdf" class="absolute inset-0 opacity-0 cursor-pointer z-10" id="approval_file">
                        @if($approval_file)
                            <div class="text-center">
                                <i class="fas fa-file-pdf text-4xl text-red-500 mb-2"></i>
                                <p class="text-xs text-primary-600 font-medium px-2 truncate max-w-full">{{ Str::limit($approval_file->getClientOriginalName(), 20) }}</p>
                            </div>
                        @elseif($isEdit && $submission?->approval_file)
                            <div class="text-center">
                                <i class="fas fa-file-pdf text-4xl text-red-500 mb-2"></i>
                                <p class="text-xs text-gray-500">File tersimpan</p>
                                <p class="text-[10px] text-primary-600 mt-1">Klik untuk ganti</p>
                            </div>
                        @else
                            <i class="fas fa-file-pdf text-3xl text-gray-300 mb-2"></i>
                            <p class="text-xs text-gray-500">Upload PDF</p>
                            <p class="text-[10px] text-gray-400">max 5MB</p>
                        @endif
                    </div>
                    @error('approval_file') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Preview (BAB 1-3) --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">
                        <i class="fas fa-file-alt text-primary-500 mr-1"></i> BAB 1-3 <span class="text-red-500">*</span>
                    </label>
                    <div class="border-2 border-dashed border-gray-200 rounded-xl hover:border-primary-400 transition aspect-[3/4] flex flex-col items-center justify-center relative overflow-hidden group">
                        <input type="file" wire:model="preview_file" accept=".pdf" class="absolute inset-0 opacity-0 cursor-pointer z-10" id="preview_file">
                        @if($preview_file)
                            <div class="text-center">
                                <i class="fas fa-file-pdf text-4xl text-red-500 mb-2"></i>
                                <p class="text-xs text-primary-600 font-medium px-2 truncate max-w-full">{{ Str::limit($preview_file->getClientOriginalName(), 20) }}</p>
                            </div>
                        @elseif($isEdit && $submission?->preview_file)
                            <div class="text-center">
                                <i class="fas fa-file-pdf text-4xl text-red-500 mb-2"></i>
                                <p class="text-xs text-gray-500">File tersimpan</p>
                                <p class="text-[10px] text-primary-600 mt-1">Klik untuk ganti</p>
                            </div>
                        @else
                            <i class="fas fa-file-alt text-3xl text-gray-300 mb-2"></i>
                            <p class="text-xs text-gray-500">Upload PDF</p>
                            <p class="text-[10px] text-gray-400">max 20MB</p>
                        @endif
                    </div>
                    <p class="text-[10px] text-amber-600 mt-1"><i class="fas fa-eye mr-1"></i>Akan ditampilkan publik</p>
                    @error('preview_file') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Full Text (Optional) --}}
            <div class="pt-4 border-t border-gray-100">
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    <i class="fas fa-file-pdf text-primary-500 mr-1"></i> Full Text 
                    <span class="text-gray-400 font-normal">- opsional</span>
                </label>
                <div class="border-2 border-dashed border-gray-200 rounded-xl hover:border-primary-400 transition p-6 relative">
                    <input type="file" wire:model="fulltext_file" accept=".pdf" class="absolute inset-0 opacity-0 cursor-pointer z-10" id="fulltext_file">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            @if($fulltext_file)
                                <i class="fas fa-file-pdf text-2xl text-red-500"></i>
                            @elseif($isEdit && $submission?->fulltext_file)
                                <i class="fas fa-file-pdf text-2xl text-red-500"></i>
                            @else
                                <i class="fas fa-cloud-upload-alt text-2xl text-gray-400"></i>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            @if($fulltext_file)
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $fulltext_file->getClientOriginalName() }}</p>
                                <p class="text-xs text-gray-500">{{ number_format($fulltext_file->getSize() / 1024 / 1024, 2) }} MB</p>
                            @elseif($isEdit && $submission?->fulltext_file)
                                <p class="text-sm font-medium text-gray-900">File tersimpan</p>
                                <p class="text-xs text-primary-600">Klik untuk mengganti file</p>
                            @else
                                <p class="text-sm font-medium text-gray-700">Upload Full Text (PDF)</p>
                                <p class="text-xs text-gray-500">Dokumen lengkap tugas akhir, maksimal 50MB</p>
                            @endif
                        </div>
                    </div>
                </div>
                @error('fulltext_file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                {{-- Public Access Toggle --}}
                <label class="flex items-start gap-3 p-4 bg-amber-50 border border-amber-200 rounded-xl cursor-pointer mt-4">
                    <input type="checkbox" wire:model="allow_fulltext_public" class="w-5 h-5 text-primary-600 rounded mt-0.5 flex-shrink-0">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Izinkan akses publik untuk Full Text</p>
                        <p class="text-xs text-gray-600 mt-0.5">
                            Jika dicentang, file full text dapat diunduh oleh publik setelah dipublikasikan.
                            Jika tidak, hanya BAB 1-3 yang dapat diakses publik.
                        </p>
                    </div>
                </label>
            </div>
        </div>
        @endif

        {{-- Step 5: Review & Submit --}}
        @if($step === 5)
        <div class="p-4 lg:p-6 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-white">
            <h2 class="text-base lg:text-lg font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-paper-plane text-emerald-500"></i> Review & Submit
            </h2>
            <p class="text-xs lg:text-sm text-gray-500 mt-1">Periksa kembali data sebelum mengirim</p>
        </div>
        <div class="p-4 lg:p-6 space-y-5">
            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Info Card --}}
                <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide flex items-center gap-2">
                        <i class="fas fa-file-alt text-primary-500"></i> Informasi Tugas Akhir
                    </h4>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">Jenis</span>
                            <span class="text-xs font-semibold px-2 py-0.5 bg-primary-100 text-primary-700 rounded">
                                {{ $this->selectedType?->fullLabel() }}
                            </span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">Judul</span>
                            <p class="text-sm font-medium text-gray-900 mt-0.5 line-clamp-2">{{ $title }}</p>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">Tahun</span>
                            <span class="text-xs font-medium text-gray-900">{{ $year }}</span>
                        </div>
                        @if($defense_date)
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">Tanggal Sidang</span>
                            <span class="text-xs font-medium text-gray-900">{{ \Carbon\Carbon::parse($defense_date)->format('d M Y') }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Author Card --}}
                <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide flex items-center gap-2">
                        <i class="fas fa-user text-primary-500"></i> Data Penulis
                    </h4>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">Nama</span>
                            <span class="text-xs font-medium text-gray-900">{{ $author }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">NIM</span>
                            <span class="text-xs font-medium text-gray-900">{{ $nim }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">Pembimbing 1</span>
                            <span class="text-xs font-medium text-gray-900 text-right max-w-[60%] truncate">{{ $advisor1 }}</span>
                        </div>
                        @if($advisor2)
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">Pembimbing 2</span>
                            <span class="text-xs font-medium text-gray-900 text-right max-w-[60%] truncate">{{ $advisor2 }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Files Summary --}}
            <div class="bg-gray-50 rounded-xl p-4">
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide flex items-center gap-2 mb-3">
                    <i class="fas fa-folder-open text-primary-500"></i> Dokumen yang Diunggah
                </h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @php
                        $files = [
                            ['name' => 'Cover', 'file' => $cover_file, 'existing' => $submission?->cover_file, 'icon' => 'fa-image', 'required' => true],
                            ['name' => 'Pengesahan', 'file' => $approval_file, 'existing' => $submission?->approval_file, 'icon' => 'fa-file-signature', 'required' => true],
                            ['name' => 'BAB 1-3', 'file' => $preview_file, 'existing' => $submission?->preview_file, 'icon' => 'fa-file-alt', 'required' => true],
                            ['name' => 'Full Text', 'file' => $fulltext_file, 'existing' => $submission?->fulltext_file, 'icon' => 'fa-file-pdf', 'required' => false],
                        ];
                    @endphp
                    @foreach($files as $f)
                        <div @class([
                            'p-3 rounded-lg text-center',
                            'bg-emerald-100' => $f['file'] || $f['existing'],
                            'bg-gray-100' => !$f['file'] && !$f['existing'],
                        ])>
                            <i @class([
                                'fas text-xl mb-1',
                                $f['icon'],
                                'text-emerald-600' => $f['file'] || $f['existing'],
                                'text-gray-400' => !$f['file'] && !$f['existing'],
                            ])></i>
                            <p class="text-xs font-medium {{ ($f['file'] || $f['existing']) ? 'text-emerald-700' : 'text-gray-500' }}">
                                {{ $f['name'] }}
                            </p>
                            <p class="text-[10px] {{ ($f['file'] || $f['existing']) ? 'text-emerald-600' : 'text-gray-400' }}">
                                @if($f['file'] || $f['existing'])
                                    <i class="fas fa-check-circle"></i> Uploaded
                                @elseif($f['required'])
                                    <i class="fas fa-exclamation-circle text-red-400"></i> Required
                                @else
                                    Opsional
                                @endif
                            </p>
                        </div>
                    @endforeach
                </div>
                @if($allow_fulltext_public)
                    <p class="text-xs text-amber-600 mt-3 flex items-center gap-1">
                        <i class="fas fa-globe"></i> Full text akan dapat diakses publik
                    </p>
                @endif
            </div>

            {{-- Agreement --}}
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" wire:model="agreement" class="w-5 h-5 text-primary-600 rounded mt-0.5 flex-shrink-0">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Pernyataan Keaslian Karya</p>
                        <p class="text-xs text-gray-600 mt-1 leading-relaxed">
                            Dengan ini saya menyatakan bahwa tugas akhir yang saya unggah adalah karya asli saya sendiri, 
                            bukan hasil plagiarisme atau jiplakan dari karya orang lain. Saya bersedia menerima sanksi akademik 
                            sesuai ketentuan yang berlaku apabila di kemudian hari terbukti melakukan pelanggaran.
                        </p>
                    </div>
                </label>
                @error('agreement') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
            </div>

            {{-- What Happens Next --}}
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                <h4 class="text-sm font-semibold text-blue-800 flex items-center gap-2">
                    <i class="fas fa-info-circle"></i> Apa yang terjadi selanjutnya?
                </h4>
                <ol class="mt-2 text-xs text-blue-700 space-y-1.5 list-decimal list-inside">
                    <li>Submission Anda akan masuk ke antrian review pustakawan</li>
                    <li>Pustakawan akan memeriksa kelengkapan dan kesesuaian dokumen</li>
                    <li>Anda akan mendapat notifikasi jika ada revisi yang diperlukan</li>
                    <li>Setelah disetujui, tugas akhir akan dipublikasikan ke E-Thesis</li>
                </ol>
            </div>
        </div>
        @endif

        {{-- Navigation Buttons --}}
        <div class="p-4 lg:p-6 bg-gray-50 border-t border-gray-100">
            <div class="flex items-center justify-between gap-3">
                <div>
                    @if($step > 1)
                        <button wire:click="previousStep" type="button" class="px-4 py-2.5 text-gray-600 hover:text-gray-900 text-sm font-medium rounded-xl hover:bg-gray-100 active:bg-gray-200 transition flex items-center gap-2">
                            <i class="fas fa-arrow-left"></i>
                            <span class="hidden sm:inline">Kembali</span>
                        </button>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    @if($step < $totalSteps)
                        <button wire:click="saveDraft" type="button" class="hidden sm:flex px-4 py-2.5 text-gray-600 hover:text-gray-900 text-sm font-medium rounded-xl hover:bg-gray-100 transition items-center gap-2">
                            <i class="fas fa-save"></i> Simpan Draft
                        </button>
                        <button wire:click="nextStep" type="button" class="px-6 py-2.5 bg-primary-600 text-white text-sm font-semibold rounded-xl hover:bg-primary-700 active:bg-primary-800 transition shadow-sm flex items-center gap-2">
                            Lanjut <i class="fas fa-arrow-right"></i>
                        </button>
                    @else
                        <button wire:click="saveDraft" type="button" class="px-4 py-2.5 text-gray-600 hover:text-gray-900 text-sm font-medium rounded-xl hover:bg-gray-100 transition flex items-center gap-2">
                            <i class="fas fa-save"></i> Simpan Draft
                        </button>
                        <button wire:click="submit" type="button" class="px-6 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 active:bg-emerald-800 transition shadow-sm flex items-center gap-2" {{ !$agreement ? 'disabled' : '' }}>
                            <i class="fas fa-paper-plane"></i> Submit Pengajuan
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Loading Overlay --}}
    <div wire:loading.flex wire:target="nextStep,previousStep,submit,saveDraft,cover_file,approval_file,preview_file,fulltext_file" class="fixed inset-0 bg-black/50 items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-6 flex items-center gap-4 mx-4 shadow-xl">
            <div class="w-8 h-8 border-4 border-primary-600 border-t-transparent rounded-full animate-spin"></div>
            <div>
                <p class="text-gray-900 font-medium">Memproses...</p>
                <p class="text-xs text-gray-500">Mohon tunggu sebentar</p>
            </div>
        </div>
    </div>
</div>
