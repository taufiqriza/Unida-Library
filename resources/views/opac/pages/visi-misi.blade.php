<x-opac.layout :title="__('opac.pages.visi_misi.title')">
    <x-opac.page-header 
        :title="__('opac.pages.visi_misi.title')" 
        :subtitle="__('opac.pages.visi_misi.subtitle')"
        :breadcrumbs="[['label' => __('opac.pages.visi_misi.breadcrumb_profile')], ['label' => __('opac.pages.visi_misi.title')]]"
    />

    <section class="max-w-4xl mx-auto px-4 py-6 lg:py-10">
        <!-- Visi -->
        <div class="mb-8 lg:mb-12">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-primary-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-eye text-primary-600 text-lg lg:text-xl"></i>
                </div>
                <h2 class="text-lg lg:text-xl font-bold text-gray-900">{{ __('opac.pages.visi_misi.vision') }}</h2>
            </div>
            <div class="bg-gradient-to-br from-primary-50 to-blue-50 rounded-2xl p-5 lg:p-8 border border-primary-100">
                <p class="text-gray-700 leading-relaxed text-sm lg:text-base">
                    <i class="fas fa-quote-left text-primary-300 mr-2"></i>
                    {{ __('opac.pages.visi_misi.vision_text') }}
                    <i class="fas fa-quote-right text-primary-300 ml-2"></i>
                </p>
            </div>
        </div>

        <!-- Misi -->
        <div class="mb-8 lg:mb-12">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-rocket text-emerald-600 text-lg lg:text-xl"></i>
                </div>
                <h2 class="text-lg lg:text-xl font-bold text-gray-900">{{ __('opac.pages.visi_misi.mission') }}</h2>
            </div>
            <div class="space-y-3">
                <div class="flex gap-3 lg:gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                    <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center text-white font-bold text-sm flex-shrink-0">1</div>
                    <p class="text-gray-700 text-sm lg:text-base">{{ __('opac.pages.visi_misi.mission_1') }}</p>
                </div>
                <div class="flex gap-3 lg:gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                    <div class="w-8 h-8 bg-emerald-600 rounded-lg flex items-center justify-center text-white font-bold text-sm flex-shrink-0">2</div>
                    <p class="text-gray-700 text-sm lg:text-base">{{ __('opac.pages.visi_misi.mission_2') }}</p>
                </div>
                <div class="flex gap-3 lg:gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                    <div class="w-8 h-8 bg-purple-600 rounded-lg flex items-center justify-center text-white font-bold text-sm flex-shrink-0">3</div>
                    <p class="text-gray-700 text-sm lg:text-base">{{ __('opac.pages.visi_misi.mission_3') }}</p>
                </div>
            </div>
        </div>

        <!-- Values -->
        <div>
            <h3 class="text-base lg:text-lg font-bold text-gray-900 mb-4 text-center">{{ __('opac.pages.visi_misi.values_title') }}</h3>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
                <div class="text-center p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-star text-blue-600"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 text-sm">{{ __('opac.pages.visi_misi.value_excellence') }}</h4>
                    <p class="text-xs text-gray-500 mt-1">{{ __('opac.pages.visi_misi.value_excellence_desc') }}</p>
                </div>
                <div class="text-center p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-handshake text-emerald-600"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 text-sm">{{ __('opac.pages.visi_misi.value_professional') }}</h4>
                    <p class="text-xs text-gray-500 mt-1">{{ __('opac.pages.visi_misi.value_professional_desc') }}</p>
                </div>
                <div class="text-center p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-mosque text-purple-600"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 text-sm">{{ __('opac.pages.visi_misi.value_islamic') }}</h4>
                    <p class="text-xs text-gray-500 mt-1">{{ __('opac.pages.visi_misi.value_islamic_desc') }}</p>
                </div>
                <div class="text-center p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-lightbulb text-orange-600"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 text-sm">{{ __('opac.pages.visi_misi.value_innovative') }}</h4>
                    <p class="text-xs text-gray-500 mt-1">{{ __('opac.pages.visi_misi.value_innovative_desc') }}</p>
                </div>
            </div>
        </div>
    </section>
</x-opac.layout>
