<x-opac.layout :title="__('opac.pages.jam_layanan.title')">
    <x-opac.page-header 
        :title="__('opac.pages.jam_layanan.title')" 
        :subtitle="__('opac.pages.jam_layanan.subtitle')"
        :breadcrumbs="[['label' => __('opac.pages.jam_layanan.breadcrumb_guide')], ['label' => __('opac.pages.jam_layanan.title')]]"
    />

    <section class="max-w-4xl mx-auto px-4 py-6 lg:py-10">
        <!-- Schedule -->
        <div class="grid sm:grid-cols-2 gap-4 mb-6">
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-briefcase text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">{{ __('opac.pages.jam_layanan.weekday') }}</h3>
                        <p class="text-xs text-gray-500">{{ __('opac.pages.jam_layanan.weekday_label') }}</p>
                    </div>
                </div>
                <div class="bg-primary-50 rounded-lg p-4 text-center">
                    <p class="text-2xl lg:text-3xl font-bold text-primary-600">08.00 - 16.00</p>
                    <p class="text-xs text-gray-500 mt-1">{{ __('opac.pages.jam_layanan.timezone') }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-day text-emerald-600"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">{{ __('opac.pages.jam_layanan.saturday') }}</h3>
                        <p class="text-xs text-gray-500">{{ __('opac.pages.jam_layanan.weekend_label') }}</p>
                    </div>
                </div>
                <div class="bg-emerald-50 rounded-lg p-4 text-center">
                    <p class="text-2xl lg:text-3xl font-bold text-emerald-600">08.00 - 12.00</p>
                    <p class="text-xs text-gray-500 mt-1">{{ __('opac.pages.jam_layanan.timezone') }}</p>
                </div>
            </div>
        </div>

        <!-- Closed -->
        <div class="bg-red-50 rounded-xl p-4 border border-red-100 mb-6">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-calendar-times text-red-600"></i>
                <h3 class="font-bold text-gray-900 text-sm">{{ __('opac.pages.jam_layanan.closed_title') }}</h3>
            </div>
            <div class="flex flex-wrap gap-2 text-sm">
                <span class="px-2 py-1 bg-white rounded text-gray-600">{{ __('opac.pages.jam_layanan.closed_sunday') }}</span>
                <span class="px-2 py-1 bg-white rounded text-gray-600">{{ __('opac.pages.jam_layanan.closed_national') }}</span>
                <span class="px-2 py-1 bg-white rounded text-gray-600">{{ __('opac.pages.jam_layanan.closed_semester') }}</span>
            </div>
        </div>

        <!-- Notes -->
        <div class="bg-amber-50 rounded-xl p-4 border border-amber-200">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-info-circle text-amber-600"></i>
                <h3 class="font-bold text-gray-900 text-sm">{{ __('opac.pages.jam_layanan.notes_title') }}</h3>
            </div>
            <ul class="space-y-1 text-sm text-gray-700">
                <li class="flex items-start gap-2"><i class="fas fa-check text-amber-600 mt-0.5 text-xs"></i> {{ __('opac.pages.jam_layanan.note_1') }}</li>
                <li class="flex items-start gap-2"><i class="fas fa-check text-amber-600 mt-0.5 text-xs"></i> {{ __('opac.pages.jam_layanan.note_2') }}</li>
                <li class="flex items-start gap-2"><i class="fas fa-check text-amber-600 mt-0.5 text-xs"></i> {{ __('opac.pages.jam_layanan.note_3') }}</li>
            </ul>
        </div>
    </section>
</x-opac.layout>
