@section('title', 'Daftar Responden')

<div class="space-y-5">
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('staff.survey.index') }}" class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Daftar Responden</h1>
                <p class="text-sm text-gray-500">{{ $survey->title }}</p>
            </div>
        </div>
        <a href="{{ route('staff.survey.analytics', $survey) }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-violet-100 text-violet-700 font-medium rounded-xl hover:bg-violet-200 transition">
            <i class="fas fa-chart-pie"></i>
            <span>Lihat Analitik</span>
        </a>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <div class="flex flex-col lg:flex-row lg:items-center gap-4">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" wire:model.live.debounce.300ms="search"
                       placeholder="Cari nama atau email..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            </div>
            <select wire:model.live="filterType"
                    class="px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                <option value="">Semua Tipe</option>
                @foreach($respondentTypes as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Responses Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        @if($responses->isEmpty())
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-users text-2xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum ada responden</h3>
                <p class="text-sm text-gray-500">Survey ini belum memiliki responden yang mengisi.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Responden</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Skor</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Waktu</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($responses as $response)
                            <tr class="hover:bg-gray-50 transition {{ $selectedResponseId === $response->id ? 'bg-blue-50' : '' }}">
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-900">{{ $response->respondent_display_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $response->respondent_email ?: $response->member?->email ?: '-' }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-medium rounded-lg bg-gray-100 text-gray-600">
                                        {{ $response->getRespondentTypeLabel() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $percentage = $response->percentage_score;
                                        $category = \App\Models\Survey::getCategory($percentage);
                                        $color = \App\Models\Survey::getCategoryColor($category);
                                    @endphp
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-gray-900">{{ $percentage }}%</span>
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-{{ $color }}-100 text-{{ $color }}-700">
                                            {{ $category }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    {{ $response->submitted_at?->format('d M Y H:i') ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button wire:click="viewResponse({{ $response->id }})"
                                                class="w-8 h-8 rounded-lg {{ $selectedResponseId === $response->id ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-500' }} hover:bg-blue-100 hover:text-blue-600 transition flex items-center justify-center">
                                            <i class="fas fa-eye text-sm"></i>
                                        </button>
                                        <button wire:click="deleteResponse({{ $response->id }})"
                                                wire:confirm="Yakin ingin menghapus respons ini?"
                                                class="w-8 h-8 rounded-lg bg-gray-100 text-gray-500 hover:bg-red-100 hover:text-red-600 transition flex items-center justify-center">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            {{-- Response Detail --}}
                            @if($selectedResponseId === $response->id && $selectedResponse)
                                <tr>
                                    <td colspan="5" class="px-4 py-4 bg-gray-50">
                                        <div class="space-y-4">
                                            @foreach($survey->sections as $section)
                                                <div>
                                                    <h4 class="font-semibold text-gray-900 mb-2">{{ $section->name }}</h4>
                                                    <div class="space-y-2">
                                                        @foreach($section->questions as $question)
                                                            @php
                                                                $answer = $selectedResponse->answers->firstWhere('question_id', $question->id);
                                                            @endphp
                                                            <div class="flex items-start gap-3 text-sm">
                                                                <span class="w-6 h-6 flex items-center justify-center bg-gray-200 rounded text-gray-600 text-xs font-medium flex-shrink-0">
                                                                    {{ $loop->iteration }}
                                                                </span>
                                                                <div class="flex-1">
                                                                    <p class="text-gray-700">{{ $question->text }}</p>
                                                                    <p class="text-gray-900 font-medium mt-1">
                                                                        @if($question->type === 'likert' && $answer?->score)
                                                                            <span class="inline-flex items-center gap-1">
                                                                                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded">{{ $answer->score }}</span>
                                                                                {{ \App\Models\SurveyQuestion::getLikertLabels()[$answer->score] ?? '' }}
                                                                            </span>
                                                                        @else
                                                                            {{ $answer?->answer ?? '-' }}
                                                                        @endif
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($responses->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $responses->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
