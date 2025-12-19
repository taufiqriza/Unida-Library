<x-opac.layout :title="__('opac.pages.panduan_ospek.title')">
    <x-opac.page-header 
        :title="__('opac.pages.panduan_ospek.title')" 
        :subtitle="__('opac.pages.panduan_ospek.subtitle')"
        :breadcrumbs="[['label' => __('opac.pages.panduan_ospek.breadcrumb')], ['label' => __('opac.pages.panduan_ospek.title')]]"
    />

    <section class="max-w-4xl mx-auto px-4 py-6 lg:py-10">
        <!-- Welcome -->
        <div class="bg-gradient-to-br from-orange-50 to-amber-50 rounded-2xl p-5 lg:p-6 border border-orange-100 mb-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-user-graduate text-orange-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 mb-1">{{ __('opac.pages.panduan_ospek.welcome_title') }}</h3>
                    <p class="text-gray-600 text-sm">{{ __('opac.pages.panduan_ospek.welcome_desc') }}</p>
                </div>
            </div>
        </div>

        <!-- What You Need to Know -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg">{{ __('opac.pages.panduan_ospek.need_to_know') }}</h3>
        <div class="grid sm:grid-cols-2 gap-4 mb-8">
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mb-3">
                    <i class="fas fa-id-card text-blue-600"></i>
                </div>
                <h4 class="font-semibold text-gray-900 text-sm mb-1">{{ __('opac.pages.panduan_ospek.member_card') }}</h4>
                <p class="text-xs text-gray-500">{{ __('opac.pages.panduan_ospek.member_card_desc') }}</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center mb-3">
                    <i class="fas fa-book text-emerald-600"></i>
                </div>
                <h4 class="font-semibold text-gray-900 text-sm mb-1">{{ __('opac.pages.panduan_ospek.borrowing_rights') }}</h4>
                <p class="text-xs text-gray-500">{{ __('opac.pages.panduan_ospek.borrowing_rights_desc') }}</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mb-3">
                    <i class="fas fa-clock text-purple-600"></i>
                </div>
                <h4 class="font-semibold text-gray-900 text-sm mb-1">{{ __('opac.pages.panduan_ospek.opening_hours') }}</h4>
                <p class="text-xs text-gray-500">{{ __('opac.pages.panduan_ospek.opening_hours_desc') }}</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center mb-3">
                    <i class="fas fa-map-marker-alt text-pink-600"></i>
                </div>
                <h4 class="font-semibold text-gray-900 text-sm mb-1">{{ __('opac.pages.panduan_ospek.location') }}</h4>
                <p class="text-xs text-gray-500">{{ __('opac.pages.panduan_ospek.location_desc') }}</p>
            </div>
        </div>

        <!-- Services -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg">{{ __('opac.pages.panduan_ospek.services_title') }}</h3>
        <div class="space-y-3 mb-8">
            <div class="flex items-center gap-3 bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <i class="fas fa-check-circle text-emerald-500"></i>
                <span class="text-sm text-gray-700">{{ __('opac.pages.panduan_ospek.service1') }}</span>
            </div>
            <div class="flex items-center gap-3 bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <i class="fas fa-check-circle text-emerald-500"></i>
                <span class="text-sm text-gray-700">{{ __('opac.pages.panduan_ospek.service2') }}</span>
            </div>
            <div class="flex items-center gap-3 bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <i class="fas fa-check-circle text-emerald-500"></i>
                <span class="text-sm text-gray-700">{{ __('opac.pages.panduan_ospek.service3') }}</span>
            </div>
            <div class="flex items-center gap-3 bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <i class="fas fa-check-circle text-emerald-500"></i>
                <span class="text-sm text-gray-700">{{ __('opac.pages.panduan_ospek.service4') }}</span>
            </div>
            <div class="flex items-center gap-3 bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <i class="fas fa-check-circle text-emerald-500"></i>
                <span class="text-sm text-gray-700">{{ __('opac.pages.panduan_ospek.service5') }}</span>
            </div>
        </div>

        <!-- CTA -->
        <div class="bg-gradient-to-r from-orange-500 to-amber-500 rounded-xl p-5 text-white text-center">
            <h4 class="font-bold mb-2">{{ __('opac.pages.panduan_ospek.cta_title') }}</h4>
            <p class="text-orange-100 text-sm mb-4">{{ __('opac.pages.panduan_ospek.cta_desc') }}</p>
            <div class="flex flex-wrap justify-center gap-3">
                <a href="{{ route('opac.search') . '?type=book' }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white text-orange-600 rounded-lg text-sm font-medium hover:bg-orange-50 transition">
                    <i class="fas fa-search"></i> {{ __('opac.pages.panduan_ospek.search_books') }}
                </a>
                <a href="{{ route('opac.page', 'virtual-tour') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 text-white rounded-lg text-sm font-medium hover:bg-white/20 transition">
                    <i class="fas fa-vr-cardboard"></i> {{ __('opac.pages.panduan_ospek.virtual_tour') }}
                </a>
            </div>
        </div>
    </section>
</x-opac.layout>
