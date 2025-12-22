@section('title', 'Analitik Survey')

<div class="space-y-5">
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('staff.survey.index') }}" class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Analitik Survey</h1>
                <p class="text-sm text-gray-500">{{ $survey->title }}</p>
            </div>
        </div>
        <a href="{{ route('staff.survey.responses', $survey) }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-100 text-blue-700 font-medium rounded-xl hover:bg-blue-200 transition">
            <i class="fas fa-list"></i>
            <span>Lihat Responden</span>
        </a>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <div class="flex items-center gap-4">
            <label class="text-sm font-medium text-gray-700">Filter Responden:</label>
            <select wire:model.live="filterType"
                    class="px-4 py-2 rounded-xl border border-gray-200 bg-white text-gray-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                <option value="">Semua Tipe</option>
                @foreach($respondentTypes as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
            <span class="text-sm text-gray-500">
                {{ number_format($analytics['response_count'] ?? 0) }} responden
            </span>
        </div>
    </div>

    @if(empty($analytics['sections']))
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-chart-pie text-2xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum ada data</h3>
            <p class="text-sm text-gray-500">Survey ini belum memiliki responden yang mengisi.</p>
        </div>
    @else
        {{-- Overall Score --}}
        <div class="bg-gradient-to-br from-blue-600 via-indigo-600 to-violet-700 rounded-2xl p-6 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="flex items-center justify-between relative">
                <div>
                    <p class="text-blue-200 text-sm font-medium mb-1">Skor Kepuasan Keseluruhan</p>
                    <p class="text-5xl font-bold">{{ $analytics['overall']['percentage'] }}%</p>
                    <span class="inline-block mt-2 px-3 py-1 bg-white/20 rounded-full text-sm font-medium">
                        {{ $analytics['overall']['category'] }}
                    </span>
                </div>
                <div class="w-32 h-32 relative">
                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="40" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="10"/>
                        <circle cx="50" cy="50" r="40" fill="none" stroke="rgba(255,255,255,0.9)" stroke-width="10"
                                stroke-dasharray="{{ $analytics['overall']['percentage'] * 2.51 }}, 251"
                                stroke-linecap="round"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-star text-3xl text-white/80"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Category Legend --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <p class="text-sm font-medium text-gray-700 mb-3">Skala Penilaian:</p>
            <div class="flex flex-wrap gap-3">
                @foreach([
                    ['range' => '81-100%', 'label' => 'Sangat Memuaskan', 'color' => 'emerald'],
                    ['range' => '61-80%', 'label' => 'Memuaskan', 'color' => 'green'],
                    ['range' => '41-60%', 'label' => 'Cukup', 'color' => 'yellow'],
                    ['range' => '21-40%', 'label' => 'Kurang Memuaskan', 'color' => 'orange'],
                    ['range' => '1-20%', 'label' => 'Tidak Memuaskan', 'color' => 'red'],
                ] as $scale)
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-{{ $scale['color'] }}-500"></div>
                        <span class="text-xs text-gray-600">{{ $scale['range'] }}: {{ $scale['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Sections Score --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-violet-50 to-indigo-50">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-chart-bar text-violet-500"></i>
                    Skor Per Dimensi
                </h2>
            </div>
            <div class="p-6 space-y-4">
                @foreach($analytics['sections'] as $sectionId => $section)
                    @php
                        $color = \App\Models\Survey::getCategoryColor($section['category']);
                    @endphp
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <p class="font-medium text-gray-900">{{ $section['name'] }}</p>
                                <p class="text-xs text-gray-500">{{ $section['question_count'] }} pertanyaan</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-gray-900">{{ $section['percentage'] }}%</p>
                                <span class="text-xs px-2 py-0.5 rounded-full bg-{{ $color }}-100 text-{{ $color }}-700">
                                    {{ $section['category'] }}
                                </span>
                            </div>
                        </div>
                        <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-{{ $color }}-500 rounded-full transition-all duration-500" style="width: {{ $section['percentage'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Questions Detail --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-list-check text-blue-500"></i>
                    Detail Per Pertanyaan
                </h2>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($survey->sections as $section)
                    <div class="p-6">
                        <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-white text-sm">
                                {{ $loop->iteration }}
                            </span>
                            {{ $section->name }}
                        </h3>
                        <div class="space-y-3">
                            @foreach($section->questions as $question)
                                @php
                                    $qData = $analytics['questions'][$question->id] ?? null;
                                    $qColor = $qData ? \App\Models\Survey::getCategoryColor($qData['category']) : 'gray';
                                @endphp
                                @if($qData && $question->type === 'likert')
                                    <div class="p-4 bg-gray-50 rounded-xl">
                                        <div class="flex items-start justify-between gap-4 mb-3">
                                            <p class="text-sm text-gray-700">{{ $question->text }}</p>
                                            <div class="text-right flex-shrink-0">
                                                <span class="font-bold text-gray-900">{{ $qData['percentage'] }}%</span>
                                                <span class="ml-2 text-xs px-2 py-0.5 rounded-full bg-{{ $qColor }}-100 text-{{ $qColor }}-700">
                                                    {{ $qData['category'] }}
                                                </span>
                                            </div>
                                        </div>
                                        {{-- Score Distribution --}}
                                        <div class="flex gap-1">
                                            @foreach([1,2,3,4,5] as $score)
                                                @php
                                                    $count = $qData['distribution'][$score] ?? 0;
                                                    $total = array_sum($qData['distribution'] ?? []);
                                                    $pct = $total > 0 ? ($count / $total) * 100 : 0;
                                                @endphp
                                                <div class="flex-1">
                                                    <div class="h-12 bg-gray-200 rounded relative overflow-hidden">
                                                        <div class="absolute bottom-0 left-0 right-0 bg-blue-500 transition-all duration-300" style="height: {{ $pct }}%"></div>
                                                    </div>
                                                    <p class="text-center text-xs text-gray-500 mt-1">{{ $score }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                        <p class="text-xs text-gray-400 mt-2">Rata-rata: {{ $qData['avg_score'] }}/5 dari {{ $qData['response_count'] }} responden</p>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Respondent Distribution --}}
        @if(!empty($analytics['by_type']))
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-users text-emerald-500"></i>
                    Distribusi Responden
                </h2>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($analytics['by_type'] as $type => $count)
                        <div class="p-4 bg-gray-50 rounded-xl text-center">
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($count) }}</p>
                            <p class="text-sm text-gray-500">{{ $respondentTypes[$type] ?? $type }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif
</div>
