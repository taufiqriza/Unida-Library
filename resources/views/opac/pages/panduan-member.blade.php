<x-opac.layout :title="__('opac.pages.panduan_member.title')">
    <x-opac.page-header 
        :title="__('opac.pages.panduan_member.title')" 
        :subtitle="__('opac.pages.panduan_member.subtitle')"
        :breadcrumbs="[['label' => __('opac.pages.panduan_member.breadcrumb')], ['label' => __('opac.pages.panduan_member.title')]]"
    />

    <section class="max-w-4xl mx-auto px-4 py-6 lg:py-10">
        <!-- Intro -->
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-5 lg:p-6 border border-blue-100 mb-8">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-id-card text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 mb-1">{{ __('opac.pages.panduan_member.account_title') }}</h3>
                    <p class="text-gray-700 text-sm leading-relaxed">
                        {{ __('opac.pages.panduan_member.account_desc') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Cara Login -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg flex items-center gap-2">
            <i class="fas fa-sign-in-alt text-blue-500"></i> {{ __('opac.pages.panduan_member.login_title') }}
        </h3>

        <div class="space-y-4 mb-8">
            <!-- Google Login -->
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" viewBox="0 0 24 24"><path fill="#fff" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#fff" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/></svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-900">{{ __('opac.pages.panduan_member.google_login_title') }}</h4>
                        <p class="text-sm text-gray-600 mt-1">{{ __('opac.pages.panduan_member.google_login_desc') }}</p>
                        <ol class="text-xs text-gray-500 mt-2 space-y-1.5 list-decimal list-inside">
                            <li>{{ __('opac.pages.panduan_member.google_step1') }}</li>
                            <li>{{ __('opac.pages.panduan_member.google_step2') }}</li>
                            <li>{{ __('opac.pages.panduan_member.google_step3') }}</li>
                            <li>{{ __('opac.pages.panduan_member.google_step4') }}</li>
                        </ol>
                        <div class="mt-3 p-2 bg-emerald-50 rounded-lg border border-emerald-100">
                            <p class="text-xs text-emerald-700"><i class="fas fa-lightbulb mr-1"></i> {{ __('opac.pages.panduan_member.google_tip') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Manual Login -->
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-keyboard text-gray-600"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-900">{{ __('opac.pages.panduan_member.manual_login_title') }}</h4>
                        <p class="text-sm text-gray-600 mt-1">{{ __('opac.pages.panduan_member.manual_login_desc') }}</p>
                        <ol class="text-xs text-gray-500 mt-2 space-y-1.5 list-decimal list-inside">
                            <li>{{ __('opac.pages.panduan_member.manual_step1') }}</li>
                            <li>{{ __('opac.pages.panduan_member.manual_step2') }}</li>
                            <li>{{ __('opac.pages.panduan_member.manual_step3') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fitur Dashboard -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg flex items-center gap-2">
            <i class="fas fa-th-large text-purple-500"></i> {{ __('opac.pages.panduan_member.features_title') }}
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-8">
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-book-reader text-blue-600 text-sm"></i>
                    </div>
                    <h4 class="font-bold text-gray-900 text-sm">{{ __('opac.pages.panduan_member.active_loans') }}</h4>
                </div>
                <p class="text-xs text-gray-600">{{ __('opac.pages.panduan_member.active_loans_desc') }}</p>
            </div>

            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 bg-violet-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-upload text-violet-600 text-sm"></i>
                    </div>
                    <h4 class="font-bold text-gray-900 text-sm">{{ __('opac.pages.panduan_member.upload_thesis') }}</h4>
                </div>
                <p class="text-xs text-gray-600">{{ __('opac.pages.panduan_member.upload_thesis_desc') }}</p>
            </div>

            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 bg-teal-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shield-alt text-teal-600 text-sm"></i>
                    </div>
                    <h4 class="font-bold text-gray-900 text-sm">{{ __('opac.pages.panduan_member.plagiarism_check') }}</h4>
                </div>
                <p class="text-xs text-gray-600">{{ __('opac.pages.panduan_member.plagiarism_check_desc') }}</p>
            </div>

            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-certificate text-emerald-600 text-sm"></i>
                    </div>
                    <h4 class="font-bold text-gray-900 text-sm">{{ __('opac.pages.panduan_member.clearance_letter') }}</h4>
                </div>
                <p class="text-xs text-gray-600">{{ __('opac.pages.panduan_member.clearance_letter_desc') }}</p>
            </div>
        </div>

        <!-- FAQ -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg flex items-center gap-2">
            <i class="fas fa-question-circle text-amber-500"></i> {{ __('opac.pages.panduan_member.faq_title') }}
        </h3>

        <div class="space-y-3 mb-8">
            <details class="bg-white rounded-xl shadow-sm border border-gray-100 group">
                <summary class="p-4 cursor-pointer flex items-center justify-between font-medium text-gray-900 text-sm">
                    {{ __('opac.pages.panduan_member.faq1_q') }}
                    <i class="fas fa-chevron-down text-gray-400 group-open:rotate-180 transition-transform"></i>
                </summary>
                <div class="px-4 pb-4 text-xs text-gray-600">
                    {{ __('opac.pages.panduan_member.faq1_a') }}
                </div>
            </details>

            <details class="bg-white rounded-xl shadow-sm border border-gray-100 group">
                <summary class="p-4 cursor-pointer flex items-center justify-between font-medium text-gray-900 text-sm">
                    {{ __('opac.pages.panduan_member.faq2_q') }}
                    <i class="fas fa-chevron-down text-gray-400 group-open:rotate-180 transition-transform"></i>
                </summary>
                <div class="px-4 pb-4 text-xs text-gray-600">
                    <strong>{{ __('opac.pages.panduan_member.faq2_a_student') }}</strong><br>
                    <strong>{{ __('opac.pages.panduan_member.faq2_a_public') }}</strong>
                </div>
            </details>

            <details class="bg-white rounded-xl shadow-sm border border-gray-100 group">
                <summary class="p-4 cursor-pointer flex items-center justify-between font-medium text-gray-900 text-sm">
                    {{ __('opac.pages.panduan_member.faq3_q') }}
                    <i class="fas fa-chevron-down text-gray-400 group-open:rotate-180 transition-transform"></i>
                </summary>
                <div class="px-4 pb-4 text-xs text-gray-600">
                    {{ __('opac.pages.panduan_member.faq3_a') }}
                </div>
            </details>
        </div>

        <!-- CTA -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-6 text-white text-center">
            <h3 class="font-bold text-lg mb-2">{{ __('opac.pages.panduan_member.cta_title') }}</h3>
            <p class="text-blue-200 text-sm mb-4">{{ __('opac.pages.panduan_member.cta_desc') }}</p>
            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white text-blue-600 font-semibold rounded-xl hover:bg-blue-50 transition">
                <i class="fas fa-sign-in-alt"></i> {{ __('opac.pages.panduan_member.login_now') }}
            </a>
        </div>
    </section>
</x-opac.layout>
