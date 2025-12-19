<x-opac.layout :title="__('opac.pages.tata_tertib.title')">
    <x-opac.page-header 
        :title="__('opac.pages.tata_tertib.title')" 
        :subtitle="__('opac.pages.tata_tertib.subtitle')"
        :breadcrumbs="[['label' => __('opac.pages.tata_tertib.breadcrumb_guide')], ['label' => __('opac.pages.tata_tertib.title')]]"
    />

    <section class="max-w-4xl mx-auto px-4 py-6 lg:py-10">
        <!-- Kewajiban -->
        <div class="mb-6 lg:mb-8">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-emerald-600"></i>
                </div>
                <h2 class="font-bold text-gray-900">{{ __('opac.pages.tata_tertib.obligations_title') }}</h2>
            </div>
            <div class="space-y-2">
                @php
                $obligations = [
                    __('opac.pages.tata_tertib.obligation_1'),
                    __('opac.pages.tata_tertib.obligation_2'),
                    __('opac.pages.tata_tertib.obligation_3'),
                    __('opac.pages.tata_tertib.obligation_4'),
                    __('opac.pages.tata_tertib.obligation_5'),
                    __('opac.pages.tata_tertib.obligation_6'),
                ];
                @endphp
                @foreach($obligations as $i => $item)
                <div class="flex items-start gap-3 bg-emerald-50 rounded-lg p-3 border border-emerald-100">
                    <span class="w-6 h-6 bg-emerald-500 rounded text-white text-xs font-bold flex items-center justify-center flex-shrink-0">{{ $i + 1 }}</span>
                    <p class="text-gray-700 text-sm">{{ $item }}</p>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Larangan -->
        <div class="mb-6 lg:mb-8">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-ban text-red-600"></i>
                </div>
                <h2 class="font-bold text-gray-900">{{ __('opac.pages.tata_tertib.prohibitions_title') }}</h2>
            </div>
            <div class="space-y-2">
                @php
                $prohibitions = [
                    __('opac.pages.tata_tertib.prohibition_1'),
                    __('opac.pages.tata_tertib.prohibition_2'),
                    __('opac.pages.tata_tertib.prohibition_3'),
                    __('opac.pages.tata_tertib.prohibition_4'),
                    __('opac.pages.tata_tertib.prohibition_5'),
                    __('opac.pages.tata_tertib.prohibition_6'),
                ];
                @endphp
                @foreach($prohibitions as $item)
                <div class="flex items-start gap-3 bg-red-50 rounded-lg p-3 border border-red-100">
                    <span class="w-6 h-6 bg-red-500 rounded text-white flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-times text-xs"></i>
                    </span>
                    <p class="text-gray-700 text-sm">{{ $item }}</p>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Sanksi -->
        <div class="bg-amber-50 rounded-xl p-4 lg:p-5 border border-amber-200">
            <div class="flex items-center gap-2 mb-3">
                <i class="fas fa-exclamation-triangle text-amber-600"></i>
                <h3 class="font-bold text-gray-900">{{ __('opac.pages.tata_tertib.sanctions_title') }}</h3>
            </div>
            <ul class="space-y-2 text-sm text-gray-700">
                <li class="flex items-start gap-2"><i class="fas fa-gavel text-amber-600 mt-0.5 text-xs"></i> {{ __('opac.pages.tata_tertib.sanction_1') }}</li>
                <li class="flex items-start gap-2"><i class="fas fa-gavel text-amber-600 mt-0.5 text-xs"></i> {{ __('opac.pages.tata_tertib.sanction_2') }}</li>
                <li class="flex items-start gap-2"><i class="fas fa-gavel text-amber-600 mt-0.5 text-xs"></i> {{ __('opac.pages.tata_tertib.sanction_3') }}</li>
                <li class="flex items-start gap-2"><i class="fas fa-gavel text-amber-600 mt-0.5 text-xs"></i> {{ __('opac.pages.tata_tertib.sanction_4') }}</li>
            </ul>
        </div>
    </section>
</x-opac.layout>
