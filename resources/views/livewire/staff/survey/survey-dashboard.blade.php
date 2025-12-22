@section('title', 'Survey')

<div class="space-y-5">
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-gradient-to-br from-blue-600 via-indigo-600 to-violet-700 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-blue-500/30">
                <i class="fas fa-clipboard-question text-2xl"></i>
            </div>
            <div>
                <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Survey Kepuasan</h1>
                <p class="text-sm text-gray-500 flex items-center gap-2">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                    Kelola survey kepuasan pemustaka
                </p>
            </div>
        </div>
        @if($this->canCreate())
            <a href="{{ route('staff.survey.create') }}" 
               class="px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl text-sm font-medium transition flex items-center gap-2 shadow-lg shadow-blue-500/25">
                <i class="fas fa-plus"></i>
                <span>Buat Survey</span>
            </a>
        @endif
    </div>

    {{-- Quick Stats Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-4 text-white relative overflow-hidden group hover:shadow-xl transition">
            <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clipboard-question"></i>
                    </div>
                    <span class="px-2 py-0.5 bg-white/20 rounded-full text-[10px]">Total</span>
                </div>
                <p class="text-2xl font-bold">{{ number_format($stats['total']) }}</p>
                <p class="text-blue-200 text-xs">Survey</p>
            </div>
        </div>

        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-4 text-white relative overflow-hidden group hover:shadow-xl transition">
            <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-circle-check"></i>
                    </div>
                    <span class="px-2 py-0.5 bg-white/20 rounded-full text-[10px]">Status</span>
                </div>
                <p class="text-2xl font-bold">{{ number_format($stats['active']) }}</p>
                <p class="text-emerald-200 text-xs">Aktif</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-pencil text-amber-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['draft']) }}</p>
                    <p class="text-xs text-gray-500">Draft</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-circle-xmark text-gray-500"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['closed']) }}</p>
                    <p class="text-xs text-gray-500">Ditutup</p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-violet-500 to-purple-600 rounded-2xl p-4 text-white relative overflow-hidden group hover:shadow-xl transition">
            <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold">{{ number_format($stats['total_responses']) }}</p>
                <p class="text-violet-200 text-xs">Total Responden</p>
            </div>
        </div>
    </div>

    {{-- Tab Navigation --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-1.5">
        <div class="flex gap-1 overflow-x-auto">
            @foreach([
                'all' => ['icon' => 'fa-layer-group', 'label' => 'Semua'],
                'active' => ['icon' => 'fa-circle-check', 'label' => 'Aktif'],
                'draft' => ['icon' => 'fa-pencil', 'label' => 'Draft'],
                'closed' => ['icon' => 'fa-circle-xmark', 'label' => 'Ditutup'],
            ] as $tabKey => $data)
            <button wire:click="setTab('{{ $tabKey }}')" 
                    class="flex-1 px-4 py-2.5 rounded-xl font-medium text-sm transition flex items-center justify-center gap-2 whitespace-nowrap
                    {{ $tab === $tabKey ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas {{ $data['icon'] }}"></i>
                <span class="hidden sm:inline">{{ $data['label'] }}</span>
            </button>
            @endforeach
        </div>
    </div>

    {{-- Search --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" 
                   wire:model.live.debounce.300ms="search"
                   placeholder="Cari survey..."
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
        </div>
    </div>

    {{-- Survey List --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
        @if($surveys->isEmpty())
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-clipboard-question text-2xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum ada survey</h3>
                <p class="text-sm text-gray-500 mb-4">Buat survey pertama untuk mengumpulkan umpan balik dari pemustaka.</p>
                @if($this->canCreate())
                    <a href="{{ route('staff.survey.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">
                        <i class="fas fa-plus"></i>
                        <span>Buat Survey</span>
                    </a>
                @endif
            </div>
        @else
            <div class="divide-y divide-gray-100">
                @foreach($surveys as $survey)
                    @php
                        $canViewDetails = $this->canViewDetails($survey);
                        $canEdit = $this->canEdit($survey);
                    @endphp
                    <div class="p-4 hover:bg-gray-50 transition" x-data="{ showMenu: false }">
                        <div class="flex items-start gap-4">
                            {{-- Status Icon --}}
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 
                                {{ $survey->status === 'active' ? 'bg-gradient-to-br from-emerald-500 to-teal-600 text-white' : 
                                   ($survey->status === 'draft' ? 'bg-amber-100' : 'bg-gray-100') }}">
                                <i class="fas {{ $survey->status === 'active' ? 'fa-chart-line' : 
                                   ($survey->status === 'draft' ? 'fa-pencil text-amber-600' : 'fa-archive text-gray-500') }}"></i>
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <h3 class="font-semibold text-gray-900">{{ $survey->title }}</h3>
                                        @if($canViewDetails)
                                            <p class="text-sm text-gray-500 line-clamp-1">{{ $survey->description ?: 'Tidak ada deskripsi' }}</p>
                                        @else
                                            <p class="text-sm text-gray-400 italic">Survey cabang lain</p>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        @if($survey->branch)
                                            <span class="px-2 py-1 text-xs font-medium rounded-lg bg-blue-50 text-blue-600">
                                                {{ $survey->branch->name }}
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-medium rounded-lg bg-purple-50 text-purple-600">
                                                Global
                                            </span>
                                        @endif
                                        <span class="px-2 py-1 text-xs font-medium rounded-lg
                                            {{ $survey->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 
                                               ($survey->status === 'draft' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600') }}">
                                            {{ ucfirst($survey->status) }}
                                        </span>
                                    </div>
                                </div>

                                @if($canViewDetails)
                                    {{-- Meta --}}
                                    <div class="flex flex-wrap items-center gap-4 mt-2 text-sm text-gray-500">
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-list-ul"></i>
                                            {{ $survey->sections_count }} bagian
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-users"></i>
                                            {{ number_format($survey->responses_count) }} responden
                                        </span>
                                        @if($survey->start_date || $survey->end_date)
                                            <span class="flex items-center gap-1">
                                                <i class="fas fa-calendar"></i>
                                                @if($survey->start_date && $survey->end_date)
                                                    {{ $survey->start_date->format('d/m/Y') }} - {{ $survey->end_date->format('d/m/Y') }}
                                                @elseif($survey->end_date)
                                                    s/d {{ $survey->end_date->format('d/m/Y') }}
                                                @else
                                                    Mulai {{ $survey->start_date->format('d/m/Y') }}
                                                @endif
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Actions --}}
                                    <div class="flex flex-wrap items-center gap-2 mt-3">
                                        @if($survey->status === 'active')
                                            <a href="{{ route('opac.survey.show', $survey->slug) }}" target="_blank"
                                               class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-lg bg-emerald-100 text-emerald-700 hover:bg-emerald-200 transition">
                                                <i class="fas fa-external-link"></i>
                                                <span>Lihat Form</span>
                                            </a>
                                        @endif
                                        <a href="{{ route('staff.survey.responses', $survey) }}"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 transition">
                                            <i class="fas fa-list"></i>
                                            <span>Responden</span>
                                        </a>
                                        <a href="{{ route('staff.survey.analytics', $survey) }}"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-lg bg-violet-100 text-violet-700 hover:bg-violet-200 transition">
                                            <i class="fas fa-chart-pie"></i>
                                            <span>Analitik</span>
                                        </a>

                                        @if($canEdit)
                                            <a href="{{ route('staff.survey.edit', $survey) }}"
                                               class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                                                <i class="fas fa-edit"></i>
                                                <span>Edit</span>
                                            </a>

                                            {{-- Dropdown --}}
                                            <div class="relative" @click.away="showMenu = false">
                                                <button @click="showMenu = !showMenu" 
                                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 text-gray-500 hover:bg-gray-200 transition">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <div x-show="showMenu" x-cloak
                                                     x-transition:enter="transition ease-out duration-100"
                                                     x-transition:enter-start="opacity-0 scale-95"
                                                     x-transition:enter-end="opacity-100 scale-100"
                                                     class="absolute right-0 bottom-full mb-2 w-48 bg-white rounded-xl shadow-xl border border-gray-200 py-1 z-50">
                                                    <button wire:click="toggleStatus({{ $survey->id }})"
                                                            class="w-full flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                        <i class="fas fa-toggle-on w-4"></i>
                                                        <span>{{ $survey->status === 'active' ? 'Tutup' : ($survey->status === 'draft' ? 'Aktifkan' : 'Set Draft') }}</span>
                                                    </button>
                                                    <button wire:click="duplicateSurvey({{ $survey->id }})"
                                                            class="w-full flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                        <i class="fas fa-copy w-4"></i>
                                                        <span>Duplikasi</span>
                                                    </button>
                                                    <hr class="my-1 border-gray-200">
                                                    <button wire:click="deleteSurvey({{ $survey->id }})"
                                                            wire:confirm="Yakin ingin menghapus survey ini? Semua responden akan ikut terhapus."
                                                            class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                        <i class="fas fa-trash w-4"></i>
                                                        <span>Hapus</span>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($surveys->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $surveys->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
