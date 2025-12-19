<x-opac.layout :title="__('opac.pages.sejarah.title')">
    <x-opac.page-header 
        :title="__('opac.pages.sejarah.title')" 
        :subtitle="__('opac.pages.sejarah.subtitle')"
        :breadcrumbs="[['label' => __('opac.pages.sejarah.breadcrumb_profile')], ['label' => __('opac.pages.sejarah.title')]]"
    />

    <section class="max-w-4xl mx-auto px-4 py-6 lg:py-10">
        <!-- Intro -->
        <div class="bg-gradient-to-br from-primary-50 to-blue-50 rounded-2xl p-5 lg:p-6 border border-primary-100 mb-6 lg:mb-8">
            <p class="text-gray-700 text-sm lg:text-base leading-relaxed">
                {{ __('opac.pages.sejarah.intro') }}
            </p>
        </div>

        <!-- Timeline -->
        <div class="relative pl-6 lg:pl-8 border-l-2 border-primary-200 space-y-6 lg:space-y-8">
            <!-- Era ISID -->
            <div class="relative">
                <div class="absolute -left-[25px] lg:-left-[33px] w-4 h-4 lg:w-5 lg:h-5 bg-primary-500 rounded-full border-4 border-white shadow"></div>
                <div class="bg-white rounded-xl p-4 lg:p-5 shadow-sm border border-gray-100">
                    <span class="inline-block px-2 py-0.5 bg-primary-100 text-primary-700 rounded text-xs font-medium mb-2">{{ __('opac.pages.sejarah.era_awal') }}</span>
                    <h3 class="font-bold text-gray-900 mb-2">{{ __('opac.pages.sejarah.era_isid_title') }}</h3>
                    <p class="text-gray-600 text-sm">{{ __('opac.pages.sejarah.era_isid_desc') }}</p>
                </div>
            </div>

            <!-- 2014 -->
            <div class="relative">
                <div class="absolute -left-[25px] lg:-left-[33px] w-4 h-4 lg:w-5 lg:h-5 bg-emerald-500 rounded-full border-4 border-white shadow"></div>
                <div class="bg-white rounded-xl p-4 lg:p-5 shadow-sm border border-gray-100">
                    <span class="inline-block px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded text-xs font-medium mb-2">{{ __('opac.pages.sejarah.tahun_2014') }}</span>
                    <h3 class="font-bold text-gray-900 mb-2">{{ __('opac.pages.sejarah.transformasi_title') }}</h3>
                    <p class="text-gray-600 text-sm">{{ __('opac.pages.sejarah.transformasi_desc') }} {{ __('opac.pages.sejarah.transformasi_desc_2') }}</p>
                </div>
            </div>

            <!-- Revitalisasi -->
            <div class="relative">
                <div class="absolute -left-[25px] lg:-left-[33px] w-4 h-4 lg:w-5 lg:h-5 bg-purple-500 rounded-full border-4 border-white shadow"></div>
                <div class="bg-white rounded-xl p-4 lg:p-5 shadow-sm border border-gray-100">
                    <span class="inline-block px-2 py-0.5 bg-purple-100 text-purple-700 rounded text-xs font-medium mb-2">{{ __('opac.pages.sejarah.revitalisasi') }}</span>
                    <h3 class="font-bold text-gray-900 mb-2">{{ __('opac.pages.sejarah.pengembangan_title') }}</h3>
                    <p class="text-gray-600 text-sm">{{ __('opac.pages.sejarah.pengembangan_desc') }} <span class="text-purple-600 font-medium">{{ __('opac.pages.sejarah.pengembangan_highlight') }}</span>{{ __('opac.pages.sejarah.pengembangan_desc_2') }}</p>
                </div>
            </div>

            <!-- Modern -->
            <div class="relative">
                <div class="absolute -left-[25px] lg:-left-[33px] w-4 h-4 lg:w-5 lg:h-5 bg-orange-500 rounded-full border-4 border-white shadow"></div>
                <div class="bg-gradient-to-br from-primary-600 to-primary-700 rounded-xl p-4 lg:p-5 text-white">
                    <span class="inline-block px-2 py-0.5 bg-white/20 rounded text-xs font-medium mb-2">{{ __('opac.pages.sejarah.sekarang') }}</span>
                    <h3 class="font-bold mb-2">{{ __('opac.pages.sejarah.modern_title') }}</h3>
                    <p class="text-primary-100 text-sm">{{ __('opac.pages.sejarah.modern_desc') }}</p>
                </div>
            </div>
        </div>

        <!-- Closing -->
        <div class="mt-6 lg:mt-8 bg-white rounded-xl p-4 lg:p-5 shadow-sm border border-gray-100">
            <p class="text-gray-700 text-sm lg:text-base leading-relaxed">
                <i class="fas fa-quote-left text-gray-300 mr-2"></i>
                {{ __('opac.pages.sejarah.closing') }} <span class="text-primary-600 font-semibold">{{ __('opac.pages.sejarah.closing_highlight') }}</span>.
            </p>
        </div>
    </section>
</x-opac.layout>
