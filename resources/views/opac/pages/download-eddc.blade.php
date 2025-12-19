<x-opac.layout :title="__('opac.additional_pages.download_eddc.title')">
    <x-opac.page-header 
        :title="__('opac.additional_pages.download_eddc.title')" 
        :subtitle="__('opac.additional_pages.download_eddc.subtitle')"
        :breadcrumbs="[['label' => 'Guide'], ['label' => __('opac.additional_pages.download_eddc.title')]]"
    />

    <section class="max-w-4xl mx-auto px-4 py-6 lg:py-10">
        <!-- Intro -->
        <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-2xl p-5 lg:p-6 border border-indigo-100 mb-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-layer-group text-indigo-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-700 text-sm lg:text-base leading-relaxed">
                        {!! __('opac.additional_pages.download_eddc.intro') !!}
                    </p>
                </div>
            </div>
        </div>

        <!-- About DDC -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg flex items-center gap-2">
            <i class="fas fa-info-circle text-indigo-500"></i> {{ __('opac.additional_pages.download_eddc.about_ddc') }}
        </h3>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 mb-6">
            <p class="text-sm text-gray-600 mb-4">
                {{ __('opac.additional_pages.download_eddc.ddc_desc') }}
            </p>
            <div class="grid sm:grid-cols-2 gap-2">
                <div class="flex items-center gap-2 text-sm">
                    <span class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center text-red-600 font-bold text-xs">000</span>
                    <span class="text-gray-700">{{ __('opac.additional_pages.download_eddc.class_000') }}</span>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <span class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center text-orange-600 font-bold text-xs">100</span>
                    <span class="text-gray-700">{{ __('opac.additional_pages.download_eddc.class_100') }}</span>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <span class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center text-amber-600 font-bold text-xs">200</span>
                    <span class="text-gray-700">{{ __('opac.additional_pages.download_eddc.class_200') }}</span>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <span class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center text-yellow-600 font-bold text-xs">300</span>
                    <span class="text-gray-700">{{ __('opac.additional_pages.download_eddc.class_300') }}</span>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <span class="w-8 h-8 bg-lime-100 rounded-lg flex items-center justify-center text-lime-600 font-bold text-xs">400</span>
                    <span class="text-gray-700">{{ __('opac.additional_pages.download_eddc.class_400') }}</span>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <span class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center text-green-600 font-bold text-xs">500</span>
                    <span class="text-gray-700">{{ __('opac.additional_pages.download_eddc.class_500') }}</span>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <span class="w-8 h-8 bg-teal-100 rounded-lg flex items-center justify-center text-teal-600 font-bold text-xs">600</span>
                    <span class="text-gray-700">{{ __('opac.additional_pages.download_eddc.class_600') }}</span>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <span class="w-8 h-8 bg-cyan-100 rounded-lg flex items-center justify-center text-cyan-600 font-bold text-xs">700</span>
                    <span class="text-gray-700">{{ __('opac.additional_pages.download_eddc.class_700') }}</span>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <span class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 font-bold text-xs">800</span>
                    <span class="text-gray-700">{{ __('opac.additional_pages.download_eddc.class_800') }}</span>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <span class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center text-purple-600 font-bold text-xs">900</span>
                    <span class="text-gray-700">{{ __('opac.additional_pages.download_eddc.class_900') }}</span>
                </div>
            </div>
        </div>

        <!-- Download Section -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg flex items-center gap-2">
            <i class="fas fa-download text-emerald-500"></i> {{ __('opac.additional_pages.download_eddc.download_section') }}
        </h3>
        <div class="grid sm:grid-cols-2 gap-4 mb-8">
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:border-indigo-200 hover:shadow-md transition">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-desktop text-indigo-600 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900">{{ __('opac.additional_pages.download_eddc.desktop_title') }}</h4>
                        <p class="text-xs text-gray-500">{{ __('opac.additional_pages.download_eddc.for_windows') }}</p>
                    </div>
                </div>
                <p class="text-xs text-gray-600 mb-4">{{ __('opac.additional_pages.download_eddc.desktop_desc') }}</p>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-400"><i class="fas fa-file-archive mr-1"></i> ~150 MB</span>
                    <a href="#" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition flex items-center gap-2">
                        <i class="fas fa-download"></i> {{ __('opac.additional_pages.download_eddc.download') }}
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:border-emerald-200 hover:shadow-md transition">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-file-pdf text-emerald-600 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900">{{ __('opac.additional_pages.download_eddc.summary_title') }}</h4>
                        <p class="text-xs text-gray-500">{{ __('opac.additional_pages.download_eddc.format_pdf') }}</p>
                    </div>
                </div>
                <p class="text-xs text-gray-600 mb-4">{{ __('opac.additional_pages.download_eddc.summary_desc') }}</p>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-400"><i class="fas fa-file-pdf mr-1"></i> ~5 MB</span>
                    <a href="#" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition flex items-center gap-2">
                        <i class="fas fa-download"></i> {{ __('opac.additional_pages.download_eddc.download') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Installation Guide -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg flex items-center gap-2">
            <i class="fas fa-cogs text-amber-500"></i> {{ __('opac.additional_pages.download_eddc.install_guide') }}
        </h3>
        <div class="space-y-3 mb-8">
            <div class="flex gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold text-sm flex-shrink-0">1</div>
                <div>
                    <h4 class="font-semibold text-gray-900 text-sm">{{ __('opac.additional_pages.download_eddc.step_1_title') }}</h4>
                    <p class="text-xs text-gray-600">{{ __('opac.additional_pages.download_eddc.step_1_desc') }}</p>
                </div>
            </div>
            <div class="flex gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold text-sm flex-shrink-0">2</div>
                <div>
                    <h4 class="font-semibold text-gray-900 text-sm">{{ __('opac.additional_pages.download_eddc.step_2_title') }}</h4>
                    <p class="text-xs text-gray-600">{{ __('opac.additional_pages.download_eddc.step_2_desc') }}</p>
                </div>
            </div>
            <div class="flex gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold text-sm flex-shrink-0">3</div>
                <div>
                    <h4 class="font-semibold text-gray-900 text-sm">{{ __('opac.additional_pages.download_eddc.step_3_title') }}</h4>
                    <p class="text-xs text-gray-600">{!! __('opac.additional_pages.download_eddc.step_3_desc') !!}</p>
                </div>
            </div>
        </div>

        <!-- Note -->
        <div class="bg-amber-50 rounded-xl p-4 border border-amber-200 mb-6">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-triangle text-amber-600 mt-0.5"></i>
                <div>
                    <h4 class="font-semibold text-gray-900 text-sm mb-1">{{ __('opac.additional_pages.download_eddc.important_note') }}</h4>
                    <p class="text-xs text-gray-600">{{ __('opac.additional_pages.download_eddc.note_desc') }}</p>
                </div>
            </div>
        </div>

        <!-- Help -->
        <div class="bg-gradient-to-r from-indigo-600 to-blue-600 rounded-xl p-5 text-white flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <h4 class="font-bold">{{ __('opac.additional_pages.download_eddc.need_help') }}</h4>
                <p class="text-indigo-200 text-sm">{{ __('opac.additional_pages.download_eddc.need_help_desc') }}</p>
            </div>
            <a href="https://wa.me/6285183053934?text=Halo, saya butuh bantuan instalasi E-DDC 23" target="_blank" class="px-4 py-2 bg-white text-indigo-600 rounded-lg text-sm font-medium hover:bg-indigo-50 transition flex items-center gap-2">
                <i class="fab fa-whatsapp"></i> {{ __('opac.additional_pages.download_eddc.chat_whatsapp') }}
            </a>
        </div>
    </section>
</x-opac.layout>
