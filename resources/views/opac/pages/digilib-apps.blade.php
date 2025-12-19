<x-opac.layout :title="__('opac.additional_pages.digilib_apps.title')">
    <x-opac.page-header 
        :title="__('opac.additional_pages.digilib_apps.title')" 
        :subtitle="__('opac.additional_pages.digilib_apps.subtitle')"
        :breadcrumbs="[['label' => 'E-Resources'], ['label' => __('opac.additional_pages.digilib_apps.title')]]"
    />

    <section class="max-w-4xl mx-auto px-4 py-6 lg:py-10">
        <!-- Intro -->
        <div class="bg-gradient-to-br from-primary-50 to-blue-50 rounded-2xl p-5 lg:p-6 border border-primary-100 mb-6">
            <p class="text-gray-700 text-sm lg:text-base leading-relaxed">
                {!! __('opac.additional_pages.digilib_apps.intro_p1') !!}
            </p>
            <p class="text-gray-700 text-sm lg:text-base leading-relaxed mt-3">
                {!! __('opac.additional_pages.digilib_apps.intro_p2') !!}
            </p>
        </div>

        <!-- Download Apps -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg">{{ __('opac.additional_pages.digilib_apps.download_app') }}</h3>
        <div class="grid sm:grid-cols-2 gap-4 mb-6">
            <!-- Android -->
            <a href="https://play.google.com/store/apps/details?id=id.kubuku.kbk1954547&pcampaignid=web_share" target="_blank" class="flex items-center gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:border-green-300 hover:shadow-md transition group">
                <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-green-200 transition">
                    <i class="fab fa-google-play text-green-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">{{ __('opac.additional_pages.digilib_apps.download_at') }}</p>
                    <p class="font-bold text-gray-900">Google Play Store</p>
                    <p class="text-xs text-green-600 mt-1"><i class="fab fa-android mr-1"></i> Android</p>
                </div>
                <i class="fas fa-external-link-alt text-gray-300 ml-auto group-hover:text-green-500 transition"></i>
            </a>

            <!-- iOS -->
            <a href="https://apps.apple.com/id/app/digilib-unida-gontor/id6738683621" target="_blank" class="flex items-center gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:border-gray-400 hover:shadow-md transition group">
                <div class="w-14 h-14 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-gray-200 transition">
                    <i class="fab fa-apple text-gray-700 text-2xl"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">{{ __('opac.additional_pages.digilib_apps.download_at') }}</p>
                    <p class="font-bold text-gray-900">App Store</p>
                    <p class="text-xs text-gray-600 mt-1"><i class="fab fa-apple mr-1"></i> iOS / iPadOS</p>
                </div>
                <i class="fas fa-external-link-alt text-gray-300 ml-auto group-hover:text-gray-500 transition"></i>
            </a>
        </div>

        <!-- Web Preview -->
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 mb-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-globe text-primary-600"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 text-sm">{{ __('opac.additional_pages.digilib_apps.web_preview') }}</h4>
                    <p class="text-xs text-gray-500">{{ __('opac.additional_pages.digilib_apps.web_preview_desc') }}</p>
                </div>
            </div>
            <a href="https://ebook.digilib-unida.id/" target="_blank" class="flex items-center justify-between p-3 bg-primary-50 rounded-lg hover:bg-primary-100 transition">
                <span class="text-sm text-primary-700 font-medium">ebook.digilib-unida.id</span>
                <i class="fas fa-external-link-alt text-primary-500"></i>
            </a>
        </div>

        <!-- Features -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg">{{ __('opac.additional_pages.digilib_apps.features') }}</h3>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
            <div class="text-center p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-book text-blue-600"></i>
                </div>
                <p class="text-xs font-medium text-gray-900">{{ __('opac.additional_pages.digilib_apps.read_ebook') }}</p>
            </div>
            <div class="text-center p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-download text-emerald-600"></i>
                </div>
                <p class="text-xs font-medium text-gray-900">{{ __('opac.additional_pages.digilib_apps.offline_reading') }}</p>
            </div>
            <div class="text-center p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-bookmark text-purple-600"></i>
                </div>
                <p class="text-xs font-medium text-gray-900">{{ __('opac.additional_pages.digilib_apps.bookmark') }}</p>
            </div>
            <div class="text-center p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-search text-orange-600"></i>
                </div>
                <p class="text-xs font-medium text-gray-900">{{ __('opac.additional_pages.digilib_apps.search') }}</p>
            </div>
        </div>

        <!-- Provider -->
        <div class="bg-amber-50 rounded-xl p-4 border border-amber-200">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-info-circle text-amber-600"></i>
                <span class="font-bold text-gray-900 text-sm">{{ __('opac.additional_pages.digilib_apps.powered_by') }}</span>
            </div>
            <p class="text-sm text-gray-600">{{ __('opac.additional_pages.digilib_apps.powered_by_desc') }}</p>
        </div>
    </section>
</x-opac.layout>
