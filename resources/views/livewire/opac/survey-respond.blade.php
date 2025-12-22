<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-100">
    @if($alreadySubmitted)
        {{-- Already Submitted --}}
        <div class="min-h-screen flex items-center justify-center p-4">
            <div class="max-w-md w-full text-center">
                <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-exclamation-triangle text-3xl text-amber-600"></i>
                </div>
                <h1 class="text-2xl font-bold text-slate-900 mb-2">Sudah Mengisi Survey</h1>
                <p class="text-slate-600 mb-6">Anda sudah mengisi survey ini sebelumnya. Terima kasih atas partisipasi Anda!</p>
                <a href="{{ route('opac.survey.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition">
                    <i class="fas fa-list"></i>
                    <span>Lihat Survey Lain</span>
                </a>
            </div>
        </div>
    @elseif($submitted)
        {{-- Success --}}
        <div class="min-h-screen flex items-center justify-center p-4">
            <div class="max-w-md w-full text-center">
                <div class="w-24 h-24 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg shadow-emerald-500/30 animate-bounce">
                    <i class="fas fa-check text-4xl text-white"></i>
                </div>
                <h1 class="text-3xl font-bold text-slate-900 mb-2">Terima Kasih!</h1>
                <p class="text-slate-600 mb-6">Respons Anda telah berhasil disimpan. Umpan balik Anda sangat berharga untuk peningkatan layanan perpustakaan.</p>
                <a href="{{ route('opac.survey.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 text-white font-semibold rounded-xl hover:bg-emerald-700 transition shadow-lg shadow-emerald-500/25">
                    <i class="fas fa-list"></i>
                    <span>Lihat Survey Lain</span>
                </a>
            </div>
        </div>
    @else
        <div class="max-w-5xl mx-auto px-4 py-8">
            {{-- Header --}}
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-blue-500/30">
                    <i class="fas fa-clipboard-question text-2xl text-white"></i>
                </div>
                <h1 class="text-2xl lg:text-3xl font-bold text-slate-900 mb-2">{{ $survey->title }}</h1>
                @if($survey->description)
                    <p class="text-slate-600 max-w-2xl mx-auto">{{ $survey->description }}</p>
                @endif
            </div>

            {{-- Sticky Progress Bar --}}
            <div class="sticky top-20 z-40 bg-white/95 backdrop-blur-sm rounded-2xl shadow-lg border border-slate-100 p-4 mb-6">
                <div class="flex items-center justify-between">
                    @foreach($survey->sections as $index => $section)
                        <div class="flex items-center {{ $index < $survey->sections->count() - 1 ? 'flex-1' : '' }}">
                            {{-- Step Circle --}}
                            <div class="flex flex-col items-center">
                                <button wire:click="$set('currentSection', {{ $index }})" 
                                        class="relative flex items-center justify-center w-10 h-10 rounded-full font-semibold text-sm transition-all
                                        {{ $currentSection === $index 
                                            ? 'bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-500/30' 
                                            : ($index < $currentSection ? 'bg-emerald-500 text-white' : 'bg-gray-200 text-gray-500') }}">
                                    @if($index < $currentSection)
                                        <i class="fas fa-check text-xs"></i>
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </button>
                                <span class="text-[10px] text-gray-500 mt-1 hidden sm:block whitespace-nowrap">{{ Str::limit($section->name, 12) }}</span>
                            </div>
                            {{-- Connector Line --}}
                            @if($index < $survey->sections->count() - 1)
                                <div class="flex-1 h-0.5 mx-2 {{ $index < $currentSection ? 'bg-emerald-500' : 'bg-gray-200' }}"></div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Form --}}
            <form wire:submit="submit">
                {{-- Respondent Info (only on first section) --}}
                @if($currentSection === 0)
                    <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-6 lg:p-8 mb-6">
                        <h2 class="font-semibold text-slate-900 mb-6 flex items-center gap-2 text-lg">
                            <i class="fas fa-user-circle text-blue-500"></i>
                            Data Responden
                        </h2>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Tipe Responden *</label>
                                <select wire:model="respondentType"
                                        class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Pilih tipe...</option>
                                    @foreach($respondentTypes as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('respondentType') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                            </div>

                            @if(!Auth::guard('member')->check())
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Nama</label>
                                    <input type="text" wire:model="respondentName"
                                           class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="Nama Anda">
                                    @error('respondentName') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                                </div>
                            @else
                                <div class="flex items-center p-4 bg-slate-50 rounded-xl">
                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                        <i class="fas fa-user text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-500">Login sebagai</p>
                                        <p class="font-semibold text-slate-900">{{ Auth::guard('member')->user()->name }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Section Questions --}}
                @foreach($survey->sections as $sectionIndex => $section)
                    <div class="{{ $currentSection === $sectionIndex ? '' : 'hidden' }}">
                        <div class="bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-600 px-6 lg:px-8 py-5">
                                <h2 class="font-bold text-white text-lg lg:text-xl">{{ $section->name }}</h2>
                                @if($section->description)
                                    <p class="text-blue-100 text-sm mt-1">{{ $section->description }}</p>
                                @endif
                            </div>

                            <div class="p-6 lg:p-8 space-y-8">
                                @foreach($section->questions as $questionIndex => $question)
                                    <div class="space-y-4" wire:key="q-{{ $question->id }}">
                                        <label class="block text-slate-800 font-medium text-base lg:text-lg">
                                            <span class="inline-flex items-center justify-center w-7 h-7 bg-blue-100 text-blue-600 rounded-lg text-sm font-bold mr-2">{{ $questionIndex + 1 }}</span>
                                            {{ $question->text }}
                                            @if($question->is_required)
                                                <span class="text-red-500">*</span>
                                            @endif
                                        </label>

                                        @if($question->type === 'likert')
                                            {{-- Compact Likert Scale --}}
                                            <div class="bg-slate-50 rounded-xl p-4">
                                                <div class="flex items-center justify-between gap-2">
                                                    @foreach([1, 2, 3, 4, 5] as $score)
                                                        <label class="flex-1 cursor-pointer">
                                                            <input type="radio" wire:model="answers.{{ $question->id }}" value="{{ $score }}" class="sr-only peer">
                                                            <div class="py-3 lg:py-4 text-center rounded-xl border-2 transition-all
                                                                peer-checked:border-blue-500 peer-checked:bg-blue-600 peer-checked:text-white peer-checked:shadow-lg peer-checked:shadow-blue-500/30
                                                                border-slate-200 bg-white hover:border-blue-300 hover:bg-blue-50">
                                                                <span class="block text-xl lg:text-2xl font-bold">{{ $score }}</span>
                                                            </div>
                                                        </label>
                                                    @endforeach
                                                </div>
                                                <div class="flex justify-between mt-3 px-1 text-xs text-slate-500">
                                                    <span>Sangat Tidak Setuju</span>
                                                    <span>Sangat Setuju</span>
                                                </div>
                                            </div>
                                        @elseif($question->type === 'text')
                                            <textarea wire:model="answers.{{ $question->id }}" rows="3"
                                                      class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                      placeholder="Tulis jawaban Anda..."></textarea>
                                        @elseif($question->type === 'rating')
                                            <div class="flex gap-3">
                                                @for($i = 1; $i <= ($question->max_value ?? 5); $i++)
                                                    <label class="cursor-pointer">
                                                        <input type="radio" wire:model="answers.{{ $question->id }}" value="{{ $i }}" class="sr-only peer">
                                                        <div class="w-14 h-14 flex items-center justify-center rounded-xl border-2 text-lg font-bold transition-all
                                                            peer-checked:border-blue-500 peer-checked:bg-blue-600 peer-checked:text-white peer-checked:shadow-lg
                                                            border-slate-200 text-slate-600 hover:border-blue-300 hover:bg-blue-50">
                                                            {{ $i }}
                                                        </div>
                                                    </label>
                                                @endfor
                                            </div>
                                        @endif

                                        @error("answers.{$question->id}")
                                            <p class="text-sm text-red-500 flex items-center gap-1">
                                                <i class="fas fa-exclamation-circle"></i>
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Navigation --}}
                <div class="flex items-center justify-between mt-8">
                    @if($currentSection > 0)
                        <button type="button" wire:click="prevSection"
                                class="px-6 lg:px-8 py-3 lg:py-4 bg-white text-slate-700 font-semibold rounded-xl border border-slate-200 hover:bg-slate-50 transition flex items-center gap-2 shadow-sm">
                            <i class="fas fa-arrow-left"></i>
                            <span>Sebelumnya</span>
                        </button>
                    @else
                        <a href="{{ route('opac.survey.index') }}" 
                           class="px-6 lg:px-8 py-3 lg:py-4 bg-white text-slate-700 font-semibold rounded-xl border border-slate-200 hover:bg-slate-50 transition flex items-center gap-2 shadow-sm">
                            <i class="fas fa-arrow-left"></i>
                            <span>Kembali</span>
                        </a>
                    @endif

                    @if($currentSection < $survey->sections->count() - 1)
                        <button type="button" wire:click="nextSection"
                                class="px-8 lg:px-10 py-3 lg:py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 transition flex items-center gap-2">
                            <span>Selanjutnya</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    @else
                        <button type="submit"
                                class="px-10 lg:px-12 py-3 lg:py-4 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-semibold rounded-xl shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 transition flex items-center gap-2">
                            <i class="fas fa-paper-plane"></i>
                            <span>Kirim Survey</span>
                        </button>
                    @endif
                </div>
            </form>
        </div>
    @endif
</div>
