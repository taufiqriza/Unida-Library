<x-opac.layout :title="__('opac.pages.fasilitas.title')">
    <x-opac.page-header 
        :title="__('opac.pages.fasilitas.title')" 
        :subtitle="__('opac.pages.fasilitas.subtitle')"
        :breadcrumbs="[['label' => __('opac.pages.fasilitas.breadcrumb_guide')], ['label' => __('opac.pages.fasilitas.title')]]"
    />

    <section class="max-w-5xl mx-auto px-4 py-6 lg:py-10">
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 lg:gap-4 mb-6 lg:mb-8">
            @php
            $facilities = [
                ['icon' => 'fa-book-reader', 'color' => 'blue', 'name' => __('opac.pages.fasilitas.reading_room'), 'desc' => __('opac.pages.fasilitas.reading_room_desc')],
                ['icon' => 'fa-users', 'color' => 'emerald', 'name' => __('opac.pages.fasilitas.discussion_room'), 'desc' => __('opac.pages.fasilitas.discussion_room_desc')],
                ['icon' => 'fa-desktop', 'color' => 'purple', 'name' => __('opac.pages.fasilitas.computer_area'), 'desc' => __('opac.pages.fasilitas.computer_area_desc')],
                ['icon' => 'fa-wifi', 'color' => 'cyan', 'name' => __('opac.pages.fasilitas.free_wifi'), 'desc' => __('opac.pages.fasilitas.free_wifi_desc')],
                ['icon' => 'fa-lock', 'color' => 'orange', 'name' => __('opac.pages.fasilitas.locker'), 'desc' => __('opac.pages.fasilitas.locker_desc')],
                ['icon' => 'fa-print', 'color' => 'pink', 'name' => __('opac.pages.fasilitas.photocopy'), 'desc' => __('opac.pages.fasilitas.photocopy_desc')],
            ];
            $colors = [
                'blue' => 'bg-blue-100 text-blue-600',
                'emerald' => 'bg-emerald-100 text-emerald-600',
                'purple' => 'bg-purple-100 text-purple-600',
                'cyan' => 'bg-cyan-100 text-cyan-600',
                'orange' => 'bg-orange-100 text-orange-600',
                'pink' => 'bg-pink-100 text-pink-600',
            ];
            @endphp

            @foreach($facilities as $f)
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="w-10 h-10 lg:w-12 lg:h-12 {{ $colors[$f['color']] }} rounded-xl flex items-center justify-center mb-3">
                    <i class="fas {{ $f['icon'] }} text-lg lg:text-xl"></i>
                </div>
                <h3 class="font-bold text-gray-900 text-sm mb-1">{{ $f['name'] }}</h3>
                <p class="text-xs text-gray-500">{{ $f['desc'] }}</p>
            </div>
            @endforeach
        </div>

        <!-- Location -->
        <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-xl p-4 lg:p-6 text-white">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h3 class="font-bold mb-1">{{ __('opac.pages.fasilitas.location_title') }}</h3>
                    <p class="text-primary-200 text-sm">{{ __('opac.pages.fasilitas.location_address') }}</p>
                </div>
                <a href="https://maps.google.com" target="_blank" class="px-4 py-2 bg-white text-primary-600 rounded-lg text-sm font-medium hover:bg-primary-50 transition flex-shrink-0">
                    <i class="fas fa-map-marker-alt mr-1"></i> {{ __('opac.pages.fasilitas.google_maps') }}
                </a>
            </div>
        </div>
    </section>
</x-opac.layout>
