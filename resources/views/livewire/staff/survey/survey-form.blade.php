@section('title', $isEdit ? 'Edit Survey' : 'Buat Survey')

<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('staff.survey.index') }}" class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ $isEdit ? 'Edit Survey' : 'Buat Survey Baru' }}</h1>
                <p class="text-sm text-gray-500">{{ $isEdit ? 'Perbarui survey yang sudah ada' : 'Buat survey kepuasan pemustaka' }}</p>
            </div>
        </div>
        @if(!$isEdit)
            <button wire:click="$set('showTemplateModal', true)" 
                    class="inline-flex items-center gap-2 px-4 py-2 bg-violet-100 text-violet-700 font-medium rounded-xl hover:bg-violet-200 transition">
                <i class="fas fa-wand-magic-sparkles"></i>
                <span>Template SERVQUAL</span>
            </button>
        @endif
    </div>

    <form wire:submit="save" class="space-y-6">
        {{-- Basic Info --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Dasar</h2>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Survey *</label>
                    <input type="text" wire:model="title" 
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                           placeholder="Contoh: Survey Kepuasan Layanan Perpustakaan 2024">
                    @error('title') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug URL</label>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-500">/survey/</span>
                        <input type="text" wire:model="slug" 
                               class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                               placeholder="slug-url-survey (kosongkan untuk auto-generate)">
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Hanya huruf, angka, strip (-) dan underscore (_). Kosongkan untuk generate otomatis.</p>
                    @error('slug') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea wire:model="description" rows="3"
                              class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                              placeholder="Jelaskan tujuan dari survey ini..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select wire:model="status"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        <option value="draft">Draft (Belum Aktif)</option>
                        <option value="active">Aktif (Bisa Diisi)</option>
                        <option value="closed">Ditutup</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Periode</label>
                    <div class="flex gap-2">
                        <input type="date" wire:model="start_date" 
                               class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        <span class="self-center text-gray-400">-</span>
                        <input type="date" wire:model="end_date"
                               class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>
                </div>

                <div class="lg:col-span-2 flex flex-wrap gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="is_anonymous" 
                               class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Izinkan responden anonim</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="require_login"
                               class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Wajib login untuk mengisi</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Sections & Questions --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Bagian & Pertanyaan</h2>
                <button type="button" wire:click="addSection"
                        class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition text-sm">
                    <i class="fas fa-plus"></i>
                    <span>Tambah Bagian</span>
                </button>
            </div>

            @error('sections') <div class="text-sm text-red-500 bg-red-50 p-3 rounded-lg">{{ $message }}</div> @enderror

            @foreach($sections as $sectionIndex => $section)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden" wire:key="section-{{ $sectionIndex }}">
                    {{-- Section Header --}}
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 border-b border-gray-100">
                        <div class="flex items-start gap-3">
                            <div class="flex flex-col gap-1">
                                <button type="button" wire:click="moveSection({{ $sectionIndex }}, -1)"
                                        class="w-6 h-6 flex items-center justify-center text-gray-400 hover:text-gray-600 disabled:opacity-30"
                                        @if($sectionIndex === 0) disabled @endif>
                                    <i class="fas fa-chevron-up text-xs"></i>
                                </button>
                                <button type="button" wire:click="moveSection({{ $sectionIndex }}, 1)"
                                        class="w-6 h-6 flex items-center justify-center text-gray-400 hover:text-gray-600 disabled:opacity-30"
                                        @if($sectionIndex === count($sections) - 1) disabled @endif>
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </button>
                            </div>
                            <div class="flex-1 space-y-2">
                                <input type="text" wire:model="sections.{{ $sectionIndex }}.name"
                                       placeholder="Nama bagian (contoh: Tangible / Bukti Fisik)"
                                       class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-gray-900 font-medium focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                                @error("sections.{$sectionIndex}.name") <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>
                            <button type="button" wire:click="removeSection({{ $sectionIndex }})"
                                    wire:confirm="Yakin ingin menghapus bagian ini?"
                                    class="w-8 h-8 flex items-center justify-center text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                    @if(count($sections) <= 1) disabled @endif>
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Questions --}}
                    <div class="p-4 space-y-3">
                        @error("sections.{$sectionIndex}.questions") <div class="text-sm text-red-500">{{ $message }}</div> @enderror
                        
                        @foreach($section['questions'] as $questionIndex => $question)
                            <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-xl" wire:key="question-{{ $sectionIndex }}-{{ $questionIndex }}">
                                <div class="flex flex-col gap-1">
                                    <button type="button" wire:click="moveQuestion({{ $sectionIndex }}, {{ $questionIndex }}, -1)"
                                            class="w-5 h-5 flex items-center justify-center text-gray-400 hover:text-gray-600 disabled:opacity-30"
                                            @if($questionIndex === 0) disabled @endif>
                                        <i class="fas fa-chevron-up text-[10px]"></i>
                                    </button>
                                    <button type="button" wire:click="moveQuestion({{ $sectionIndex }}, {{ $questionIndex }}, 1)"
                                            class="w-5 h-5 flex items-center justify-center text-gray-400 hover:text-gray-600 disabled:opacity-30"
                                            @if($questionIndex === count($section['questions']) - 1) disabled @endif>
                                        <i class="fas fa-chevron-down text-[10px]"></i>
                                    </button>
                                </div>
                                
                                <div class="flex-1 space-y-2">
                                    <div class="flex gap-2">
                                        <span class="w-8 h-8 flex items-center justify-center bg-blue-100 text-blue-600 rounded-lg text-sm font-semibold flex-shrink-0">
                                            {{ $questionIndex + 1 }}
                                        </span>
                                        <input type="text" wire:model="sections.{{ $sectionIndex }}.questions.{{ $questionIndex }}.text"
                                               placeholder="Tulis pertanyaan..."
                                               class="flex-1 px-3 py-1.5 rounded-lg border border-gray-200 bg-white text-gray-900 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                                    </div>
                                    @error("sections.{$sectionIndex}.questions.{$questionIndex}.text") <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    
                                    <div class="flex items-center gap-3">
                                        <select wire:model="sections.{{ $sectionIndex }}.questions.{{ $questionIndex }}.type"
                                                class="px-3 py-1 rounded-lg border border-gray-200 bg-white text-gray-700 text-xs focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                                            @foreach($questionTypes as $typeKey => $typeLabel)
                                                <option value="{{ $typeKey }}">{{ $typeLabel }}</option>
                                            @endforeach
                                        </select>
                                        <label class="flex items-center gap-1 text-xs text-gray-500 cursor-pointer">
                                            <input type="checkbox" wire:model="sections.{{ $sectionIndex }}.questions.{{ $questionIndex }}.is_required"
                                                   class="w-4 h-4 rounded border-gray-300 text-blue-600">
                                            <span>Wajib</span>
                                        </label>
                                    </div>
                                </div>

                                <button type="button" wire:click="removeQuestion({{ $sectionIndex }}, {{ $questionIndex }})"
                                        class="w-7 h-7 flex items-center justify-center text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                        @if(count($section['questions']) <= 1) disabled @endif>
                                    <i class="fas fa-times text-sm"></i>
                                </button>
                            </div>
                        @endforeach

                        <button type="button" wire:click="addQuestion({{ $sectionIndex }})"
                                class="w-full py-2 border-2 border-dashed border-gray-200 rounded-xl text-gray-500 hover:border-blue-400 hover:text-blue-500 transition text-sm flex items-center justify-center gap-2">
                            <i class="fas fa-plus"></i>
                            <span>Tambah Pertanyaan</span>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3 pt-4">
            <a href="{{ route('staff.survey.index') }}" 
               class="px-6 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition">
                Batal
            </a>
            <button type="submit" 
                    class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 transition-all flex items-center gap-2">
                <i class="fas fa-save"></i>
                <span>{{ $isEdit ? 'Simpan Perubahan' : 'Buat Survey' }}</span>
            </button>
        </div>
    </form>

    {{-- Template Modal --}}
    @if($showTemplateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" wire:click.self="$set('showTemplateModal', false)">
            <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden">
                <div class="p-6">
                    <div class="w-16 h-16 bg-violet-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-wand-magic-sparkles text-2xl text-violet-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Template SERVQUAL</h3>
                    <p class="text-center text-gray-500 text-sm mb-4">
                        Template ini berisi 6 dimensi SERVQUAL dengan pertanyaan standar untuk mengukur kepuasan layanan perpustakaan.
                    </p>
                    
                    <div class="bg-gray-50 rounded-xl p-4 mb-6">
                        <p class="text-sm font-medium text-gray-700 mb-2">Dimensi yang tersedia:</p>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li><i class="fas fa-check text-emerald-500 mr-2"></i>Tangible (Bukti Fisik) - 4 pertanyaan</li>
                            <li><i class="fas fa-check text-emerald-500 mr-2"></i>Reliability (Keandalan) - 3 pertanyaan</li>
                            <li><i class="fas fa-check text-emerald-500 mr-2"></i>Responsiveness (Daya Tanggap) - 3 pertanyaan</li>
                            <li><i class="fas fa-check text-emerald-500 mr-2"></i>Assurance (Jaminan) - 3 pertanyaan</li>
                            <li><i class="fas fa-check text-emerald-500 mr-2"></i>Empathy (Empati) - 3 pertanyaan</li>
                            <li><i class="fas fa-check text-emerald-500 mr-2"></i>Penilaian Keseluruhan - 2 pertanyaan</li>
                        </ul>
                    </div>

                    <div class="flex gap-3">
                        <button wire:click="$set('showTemplateModal', false)" 
                                class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition">
                            Batal
                        </button>
                        <button wire:click="loadServqualTemplate"
                                class="flex-1 px-4 py-2.5 bg-gradient-to-r from-violet-600 to-purple-600 text-white font-semibold rounded-xl shadow-lg shadow-violet-500/25 hover:shadow-violet-500/40 transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-magic"></i>
                            <span>Gunakan Template</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
