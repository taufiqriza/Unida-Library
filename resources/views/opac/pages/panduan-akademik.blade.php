<x-opac.layout :title="__('opac.pages.panduan_akademik.title')">
    <x-opac.page-header 
        :title="__('opac.pages.panduan_akademik.title')" 
        :subtitle="__('opac.pages.panduan_akademik.subtitle')"
        :breadcrumbs="[['label' => __('opac.pages.panduan_akademik.breadcrumb')], ['label' => __('opac.pages.panduan_akademik.title')]]"
    />

    <section class="max-w-4xl mx-auto px-4 py-6 lg:py-10">
        <!-- Intro -->
        <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl p-5 lg:p-6 border border-purple-100 mb-6">
            <p class="text-gray-700 text-sm lg:text-base leading-relaxed">
                {{ __('opac.pages.panduan_akademik.intro') }}
            </p>
        </div>

        <!-- Bebas Pustaka -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg flex items-center gap-2">
            <i class="fas fa-certificate text-purple-500"></i> {{ __('opac.pages.panduan_akademik.clearance_title') }}
        </h3>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 mb-6">
            <p class="text-sm text-gray-600 mb-4">{{ __('opac.pages.panduan_akademik.clearance_intro') }}</p>
            <div class="grid sm:grid-cols-2 gap-3 mb-4">
                <div class="flex items-center gap-2 text-sm text-gray-700">
                    <i class="fas fa-check text-purple-500"></i> {{ __('opac.pages.panduan_akademik.clearance1') }}
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-700">
                    <i class="fas fa-check text-purple-500"></i> {{ __('opac.pages.panduan_akademik.clearance2') }}
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-700">
                    <i class="fas fa-check text-purple-500"></i> {{ __('opac.pages.panduan_akademik.clearance3') }}
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-700">
                    <i class="fas fa-check text-purple-500"></i> {{ __('opac.pages.panduan_akademik.clearance4') }}
                </div>
            </div>
            <div class="bg-purple-50 rounded-lg p-3 border border-purple-100">
                <p class="text-xs text-purple-700">{{ __('opac.pages.panduan_akademik.clearance_req') }}</p>
            </div>
        </div>

        <!-- Ketentuan Tugas Akhir -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg flex items-center gap-2">
            <i class="fas fa-graduation-cap text-indigo-500"></i> {{ __('opac.pages.panduan_akademik.thesis_title') }}
        </h3>
        <div class="space-y-3 mb-8">
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <h4 class="font-semibold text-gray-900 text-sm mb-2">{{ __('opac.pages.panduan_akademik.thesis_deposit') }}</h4>
                <p class="text-xs text-gray-600">{{ __('opac.pages.panduan_akademik.thesis_deposit_desc') }}</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <h4 class="font-semibold text-gray-900 text-sm mb-2">{{ __('opac.pages.panduan_akademik.thesis_upload') }}</h4>
                <p class="text-xs text-gray-600">{{ __('opac.pages.panduan_akademik.thesis_upload_desc') }}</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <h4 class="font-semibold text-gray-900 text-sm mb-2">{{ __('opac.pages.panduan_akademik.thesis_plagiarism') }}</h4>
                <p class="text-xs text-gray-600">{{ __('opac.pages.panduan_akademik.thesis_plagiarism_desc') }}</p>
            </div>
        </div>

        <!-- Denda -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg flex items-center gap-2">
            <i class="fas fa-exclamation-triangle text-amber-500"></i> {{ __('opac.pages.panduan_akademik.fine_title') }}
        </h3>
        <div class="bg-amber-50 rounded-xl p-5 border border-amber-200 mb-6">
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-semibold text-gray-900 mb-1">{{ __('opac.pages.panduan_akademik.fine_late') }}</p>
                    <p class="text-xs text-gray-600">{{ __('opac.pages.panduan_akademik.fine_late_amount') }}</p>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900 mb-1">{{ __('opac.pages.panduan_akademik.fine_lost') }}</p>
                    <p class="text-xs text-gray-600">{{ __('opac.pages.panduan_akademik.fine_lost_amount') }}</p>
                </div>
            </div>
        </div>

        <!-- Contact -->
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-xl p-5 text-white flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <h4 class="font-bold">{{ __('opac.pages.panduan_akademik.question_title') }}</h4>
                <p class="text-purple-200 text-sm">{{ __('opac.pages.panduan_akademik.question_desc') }}</p>
            </div>
            <a href="mailto:library@unida.gontor.ac.id" class="px-4 py-2 bg-white text-purple-600 rounded-lg text-sm font-medium hover:bg-purple-50 transition">
                <i class="fas fa-envelope mr-1"></i> {{ __('opac.pages.panduan_akademik.email_us') }}
            </a>
        </div>
    </section>
</x-opac.layout>
