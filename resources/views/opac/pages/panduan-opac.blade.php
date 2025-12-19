<x-opac.layout :title="__('opac.pages.panduan_opac.title')">
    <x-opac.page-header 
        :title="__('opac.pages.panduan_opac.title')" 
        :subtitle="__('opac.pages.panduan_opac.subtitle')"
        :breadcrumbs="[['label' => __('opac.pages.panduan_opac.breadcrumb')], ['label' => __('opac.pages.panduan_opac.title')]]"
    />

    <section class="max-w-4xl mx-auto px-4 py-6 lg:py-10">
        <!-- Intro -->
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-5 lg:p-6 border border-blue-100 mb-6">
            <p class="text-gray-700 text-sm lg:text-base leading-relaxed">
                {{ __('opac.pages.panduan_opac.intro') }}
            </p>
        </div>

        <!-- Steps -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg">{{ __('opac.pages.panduan_opac.steps_title') }}</h3>
        <div class="space-y-4 mb-8">
            <div class="flex gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white font-bold flex-shrink-0">1</div>
                <div>
                    <h4 class="font-semibold text-gray-900 mb-1">{{ __('opac.pages.panduan_opac.step1_title') }}</h4>
                    <p class="text-sm text-gray-600">{{ __('opac.pages.panduan_opac.step1_desc') }}</p>
                </div>
            </div>
            <div class="flex gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white font-bold flex-shrink-0">2</div>
                <div>
                    <h4 class="font-semibold text-gray-900 mb-1">{{ __('opac.pages.panduan_opac.step2_title') }}</h4>
                    <p class="text-sm text-gray-600">{{ __('opac.pages.panduan_opac.step2_desc') }}</p>
                </div>
            </div>
            <div class="flex gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white font-bold flex-shrink-0">3</div>
                <div>
                    <h4 class="font-semibold text-gray-900 mb-1">{{ __('opac.pages.panduan_opac.step3_title') }}</h4>
                    <p class="text-sm text-gray-600">{{ __('opac.pages.panduan_opac.step3_desc') }}</p>
                </div>
            </div>
            <div class="flex gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white font-bold flex-shrink-0">4</div>
                <div>
                    <h4 class="font-semibold text-gray-900 mb-1">{{ __('opac.pages.panduan_opac.step4_title') }}</h4>
                    <p class="text-sm text-gray-600">{{ __('opac.pages.panduan_opac.step4_desc') }}</p>
                </div>
            </div>
        </div>

        <!-- Tips -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg">{{ __('opac.pages.panduan_opac.tips_title') }}</h3>
        <div class="grid sm:grid-cols-2 gap-3 mb-6">
            <div class="bg-emerald-50 rounded-xl p-4 border border-emerald-100">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-lightbulb text-emerald-600"></i>
                    <span class="font-semibold text-gray-900 text-sm">{{ __('opac.pages.panduan_opac.tip1_title') }}</span>
                </div>
                <p class="text-xs text-gray-600">{{ __('opac.pages.panduan_opac.tip1_desc') }}</p>
            </div>
            <div class="bg-emerald-50 rounded-xl p-4 border border-emerald-100">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-lightbulb text-emerald-600"></i>
                    <span class="font-semibold text-gray-900 text-sm">{{ __('opac.pages.panduan_opac.tip2_title') }}</span>
                </div>
                <p class="text-xs text-gray-600">{{ __('opac.pages.panduan_opac.tip2_desc') }}</p>
            </div>
        </div>

        <!-- CTA -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl p-5 text-white text-center">
            <h4 class="font-bold mb-2">{{ __('opac.pages.panduan_opac.cta_title') }}</h4>
            <p class="text-blue-100 text-sm mb-4">{{ __('opac.pages.panduan_opac.cta_desc') }}</p>
            <a href="{{ route('opac.search') . '?type=book' }}" class="inline-flex items-center gap-2 px-5 py-2 bg-white text-blue-600 rounded-lg font-medium hover:bg-blue-50 transition">
                <i class="fas fa-search"></i> {{ __('opac.pages.panduan_opac.open_catalog') }}
            </a>
        </div>
    </section>
</x-opac.layout>
