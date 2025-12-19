<x-opac.layout :title="__('opac.pages.event_library.title')">
    <x-opac.page-header 
        :title="__('opac.pages.event_library.title')" 
        :subtitle="__('opac.pages.event_library.subtitle')"
        :breadcrumbs="[['label' => __('opac.pages.event_library.breadcrumb')], ['label' => __('opac.pages.event_library.title')]]"
    />

    <section class="max-w-5xl mx-auto px-4 py-6 lg:py-10">
        <!-- Intro -->
        <div class="bg-gradient-to-br from-pink-50 to-rose-50 rounded-2xl p-5 lg:p-6 border border-pink-100 mb-6">
            <p class="text-gray-700 text-sm lg:text-base leading-relaxed">
                {{ __('opac.pages.event_library.intro') }}
            </p>
        </div>

        <!-- Event Categories -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg">{{ __('opac.pages.event_library.event_types') }}</h3>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-3">
                    <i class="fas fa-chalkboard-teacher text-blue-600 text-xl"></i>
                </div>
                <h4 class="font-bold text-gray-900 mb-1">{{ __('opac.pages.event_library.workshop') }}</h4>
                <p class="text-xs text-gray-500">{{ __('opac.pages.event_library.workshop_desc') }}</p>
            </div>
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-3">
                    <i class="fas fa-book-open text-emerald-600 text-xl"></i>
                </div>
                <h4 class="font-bold text-gray-900 mb-1">{{ __('opac.pages.event_library.book_review') }}</h4>
                <p class="text-xs text-gray-500">{{ __('opac.pages.event_library.book_review_desc') }}</p>
            </div>
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-3">
                    <i class="fas fa-trophy text-purple-600 text-xl"></i>
                </div>
                <h4 class="font-bold text-gray-900 mb-1">{{ __('opac.pages.event_library.competition') }}</h4>
                <p class="text-xs text-gray-500">{{ __('opac.pages.event_library.competition_desc') }}</p>
            </div>
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center mb-3">
                    <i class="fas fa-users text-orange-600 text-xl"></i>
                </div>
                <h4 class="font-bold text-gray-900 mb-1">{{ __('opac.pages.event_library.orientation') }}</h4>
                <p class="text-xs text-gray-500">{{ __('opac.pages.event_library.orientation_desc') }}</p>
            </div>
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                <div class="w-12 h-12 bg-cyan-100 rounded-xl flex items-center justify-center mb-3">
                    <i class="fas fa-calendar-alt text-cyan-600 text-xl"></i>
                </div>
                <h4 class="font-bold text-gray-900 mb-1">{{ __('opac.pages.event_library.book_fair') }}</h4>
                <p class="text-xs text-gray-500">{{ __('opac.pages.event_library.book_fair_desc') }}</p>
            </div>
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                <div class="w-12 h-12 bg-rose-100 rounded-xl flex items-center justify-center mb-3">
                    <i class="fas fa-microphone text-rose-600 text-xl"></i>
                </div>
                <h4 class="font-bold text-gray-900 mb-1">{{ __('opac.pages.event_library.talkshow') }}</h4>
                <p class="text-xs text-gray-500">{{ __('opac.pages.event_library.talkshow_desc') }}</p>
            </div>
        </div>

        <!-- Upcoming Events -->
        <div class="bg-white rounded-xl p-5 lg:p-6 shadow-sm border border-gray-100 mb-6">
            <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-calendar-check text-primary-500"></i> {{ __('opac.pages.event_library.upcoming') }}
            </h3>
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-calendar-day text-gray-400 text-2xl"></i>
                </div>
                <p class="text-gray-500 text-sm">{{ __('opac.pages.event_library.no_event') }}</p>
                <p class="text-gray-400 text-xs mt-1">{{ __('opac.pages.event_library.no_event_desc') }}</p>
            </div>
        </div>

        <!-- Contact -->
        <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-xl p-4 lg:p-5 flex flex-col sm:flex-row items-center justify-between gap-4 text-white">
            <div>
                <h4 class="font-bold">{{ __('opac.pages.event_library.collaborate_title') }}</h4>
                <p class="text-primary-200 text-sm">{{ __('opac.pages.event_library.collaborate_desc') }}</p>
            </div>
            <a href="mailto:library@unida.gontor.ac.id" class="px-4 py-2 bg-white text-primary-600 rounded-lg text-sm font-medium hover:bg-primary-50 transition">
                <i class="fas fa-envelope mr-1"></i> {{ __('opac.pages.event_library.contact_us') }}
            </a>
        </div>
    </section>
</x-opac.layout>
