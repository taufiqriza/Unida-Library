<x-opac.layout :title="__('opac.pages.karir.title')">
    <x-opac.page-header 
        :title="__('opac.pages.karir.title')" 
        :subtitle="__('opac.pages.karir.subtitle')"
        :breadcrumbs="[['label' => __('opac.pages.karir.title')]]"
    />

    <section class="max-w-4xl mx-auto px-4 py-6 lg:py-10">
        <!-- Why Join -->
        <div class="grid grid-cols-3 gap-3 lg:gap-4 mb-6 lg:mb-8">
            <div class="text-center p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-graduation-cap text-blue-600"></i>
                </div>
                <h3 class="font-semibold text-gray-900 text-xs lg:text-sm">{{ __('opac.pages.karir.development') }}</h3>
                <p class="text-[10px] lg:text-xs text-gray-500 mt-1 hidden sm:block">{{ __('opac.pages.karir.development_desc') }}</p>
            </div>
            <div class="text-center p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-users text-emerald-600"></i>
                </div>
                <h3 class="font-semibold text-gray-900 text-xs lg:text-sm">{{ __('opac.pages.karir.solid_team') }}</h3>
                <p class="text-[10px] lg:text-xs text-gray-500 mt-1 hidden sm:block">{{ __('opac.pages.karir.solid_team_desc') }}</p>
            </div>
            <div class="text-center p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-mosque text-purple-600"></i>
                </div>
                <h3 class="font-semibold text-gray-900 text-xs lg:text-sm">{{ __('opac.pages.karir.islamic') }}</h3>
                <p class="text-[10px] lg:text-xs text-gray-500 mt-1 hidden sm:block">{{ __('opac.pages.karir.islamic_desc') }}</p>
            </div>
        </div>

        <!-- Current Openings -->
        <div class="bg-gray-50 rounded-xl p-6 lg:p-8 text-center border border-gray-200 mb-6">
            <div class="w-14 h-14 bg-gray-200 rounded-xl flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-briefcase text-gray-400 text-xl"></i>
            </div>
            <h3 class="font-bold text-gray-900 mb-1">{{ __('opac.pages.karir.no_vacancy') }}</h3>
            <p class="text-gray-500 text-sm mb-4">{{ __('opac.pages.karir.no_vacancy_desc') }}</p>
            <a href="mailto:library@unida.gontor.ac.id?subject=Lamaran Kerja" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition">
                <i class="fas fa-paper-plane"></i> {{ __('opac.pages.karir.send_cv') }}
            </a>
        </div>

        <!-- Contact -->
        <div class="bg-amber-50 rounded-xl p-4 border border-amber-200">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-envelope text-amber-600"></i>
                <h3 class="font-bold text-gray-900 text-sm">{{ __('opac.pages.karir.contact_hrd') }}</h3>
            </div>
            <div class="flex flex-wrap gap-4 text-sm">
                <a href="mailto:library@unida.gontor.ac.id" class="text-amber-700 hover:text-amber-800">
                    <i class="fas fa-envelope mr-1"></i> library@unida.gontor.ac.id
                </a>
                <a href="tel:085183053934" class="text-amber-700 hover:text-amber-800">
                    <i class="fas fa-phone mr-1"></i> 0851-8305-3934
                </a>
            </div>
        </div>
    </section>
</x-opac.layout>
