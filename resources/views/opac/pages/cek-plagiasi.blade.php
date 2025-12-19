<x-opac.layout :title="__('opac.additional_pages.cek_plagiasi.title')">
    <x-opac.page-header 
        :title="__('opac.additional_pages.cek_plagiasi.title')" 
        :subtitle="__('opac.additional_pages.cek_plagiasi.subtitle')"
        :breadcrumbs="[['label' => __('opac.menu.guide')], ['label' => __('opac.additional_pages.cek_plagiasi.title')]]"
    />

    <section class="max-w-4xl mx-auto px-4 py-6 lg:py-10">
        <!-- Intro -->
        <div class="bg-gradient-to-br from-teal-50 to-emerald-50 rounded-2xl p-5 lg:p-6 border border-teal-100 mb-8">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-shield-alt text-teal-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 mb-1">{{ __('opac.additional_pages.cek_plagiasi.service_title') }}</h3>
                    <p class="text-gray-700 text-sm leading-relaxed">
                        {!! __('opac.additional_pages.cek_plagiasi.service_desc') !!}
                    </p>
                </div>
            </div>
        </div>

        <!-- Important Notice -->
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-8">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-amber-600 text-sm"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-amber-800 text-sm">{{ __('opac.additional_pages.cek_plagiasi.important') }}</h4>
                    <ul class="text-amber-700 text-xs mt-1 space-y-1">
                        <li>• {!! __('opac.additional_pages.cek_plagiasi.note_1') !!}</li>
                        <li>• {!! __('opac.additional_pages.cek_plagiasi.note_2') !!}</li>
                        <li>• {!! __('opac.additional_pages.cek_plagiasi.note_3') !!}</li>
                        <li>• {{ __('opac.additional_pages.cek_plagiasi.note_4') }}</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Alur Pengajuan - 4 Steps -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg flex items-center gap-2">
            <i class="fas fa-route text-teal-500"></i> {{ __('opac.additional_pages.cek_plagiasi.flow_title') }}
        </h3>
        
        <div class="relative mb-8">
            <!-- Timeline Line -->
            <div class="absolute left-5 top-8 bottom-8 w-0.5 bg-gradient-to-b from-teal-500 to-teal-300 hidden sm:block"></div>
            
            <div class="space-y-4">
                <!-- Step 1 -->
                <div class="flex gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100 relative">
                    <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-teal-700 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-lg shadow-teal-200 z-10">
                        1
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-900">{{ __('opac.additional_pages.cek_plagiasi.step_1_title') }}</h4>
                        <p class="text-sm text-gray-600 mt-1">{{ __('opac.additional_pages.cek_plagiasi.step_1_desc') }}</p>
                        <ul class="text-xs text-gray-500 mt-2 space-y-1">
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500"></i> {{ __('opac.additional_pages.cek_plagiasi.step_1_1') }}</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500"></i> {{ __('opac.additional_pages.cek_plagiasi.step_1_2') }}</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500"></i> {{ __('opac.additional_pages.cek_plagiasi.step_1_3') }}</li>
                        </ul>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="flex gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100 relative">
                    <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-teal-700 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-lg shadow-teal-200 z-10">
                        2
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-900">{{ __('opac.additional_pages.cek_plagiasi.step_2_title') }}</h4>
                        <p class="text-sm text-gray-600 mt-1">{{ __('opac.additional_pages.cek_plagiasi.step_2_desc') }}</p>
                        <div class="grid sm:grid-cols-2 gap-3 mt-3">
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-xs font-semibold text-gray-700 flex items-center gap-2">
                                    <i class="fas fa-file-pdf text-red-500"></i> {{ __('opac.additional_pages.cek_plagiasi.format_pdf') }}
                                </p>
                                <p class="text-[10px] text-gray-500 mt-1">{{ __('opac.additional_pages.cek_plagiasi.format_pdf_desc') }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-xs font-semibold text-gray-700 flex items-center gap-2">
                                    <i class="fas fa-file-word text-blue-500"></i> {{ __('opac.additional_pages.cek_plagiasi.format_docx') }}
                                </p>
                                <p class="text-[10px] text-gray-500 mt-1">{{ __('opac.additional_pages.cek_plagiasi.format_docx_desc') }}</p>
                            </div>
                        </div>
                        <div class="mt-3 p-2 bg-blue-50 rounded-lg border border-blue-200">
                            <p class="text-[10px] text-blue-700"><i class="fas fa-info-circle mr-1"></i> {!! __('opac.additional_pages.cek_plagiasi.max_size') !!}</p>
                        </div>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="flex gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100 relative">
                    <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-teal-700 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-lg shadow-teal-200 z-10">
                        3
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-900">{{ __('opac.additional_pages.cek_plagiasi.step_3_title') }}</h4>
                        <p class="text-sm text-gray-600 mt-1">{{ __('opac.additional_pages.cek_plagiasi.step_3_desc') }}</p>
                        <ul class="text-xs text-gray-500 mt-2 space-y-1">
                            <li class="flex items-center gap-2"><i class="fas fa-spinner text-teal-500"></i> {{ __('opac.additional_pages.cek_plagiasi.step_3_1') }}</li>
                            <li class="flex items-center gap-2"><i class="fas fa-search text-teal-500"></i> {{ __('opac.additional_pages.cek_plagiasi.step_3_2') }}</li>
                            <li class="flex items-center gap-2"><i class="fas fa-clock text-teal-500"></i> {{ __('opac.additional_pages.cek_plagiasi.step_3_3') }}</li>
                        </ul>
                        <div class="mt-3 p-2 bg-teal-50 rounded-lg border border-teal-200">
                            <p class="text-[10px] text-teal-700"><i class="fas fa-info-circle mr-1"></i> {{ __('opac.additional_pages.cek_plagiasi.auto_save') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="flex gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100 relative">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-lg shadow-emerald-200 z-10">
                        4
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-900">{{ __('opac.additional_pages.cek_plagiasi.step_4_title') }}</h4>
                        <p class="text-sm text-gray-600 mt-1">{{ __('opac.additional_pages.cek_plagiasi.step_4_desc') }}</p>
                        <ul class="text-xs text-gray-500 mt-2 space-y-1">
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500"></i> {{ __('opac.additional_pages.cek_plagiasi.step_4_1') }}</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500"></i> {{ __('opac.additional_pages.cek_plagiasi.step_4_2') }}</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500"></i> {{ __('opac.additional_pages.cek_plagiasi.step_4_3') }}</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500"></i> {{ __('opac.additional_pages.cek_plagiasi.step_4_4') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Similarity Score Explanation -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg flex items-center gap-2">
            <i class="fas fa-chart-pie text-teal-500"></i> {{ __('opac.additional_pages.cek_plagiasi.result_title') }}
        </h3>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 mb-8">
            <div class="grid sm:grid-cols-3 gap-3">
                <div class="flex items-center gap-3 p-3 bg-emerald-50 rounded-lg">
                    <span class="w-4 h-4 bg-emerald-500 rounded-full"></span>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">0% - 15%</p>
                        <p class="text-[10px] text-emerald-600 font-medium">{{ __('opac.additional_pages.cek_plagiasi.pass') }}</p>
                        <p class="text-[10px] text-gray-500">{{ __('opac.additional_pages.cek_plagiasi.pass_desc') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-amber-50 rounded-lg">
                    <span class="w-4 h-4 bg-amber-500 rounded-full"></span>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">15% - 25%</p>
                        <p class="text-[10px] text-amber-600 font-medium">{{ __('opac.additional_pages.cek_plagiasi.warning') }}</p>
                        <p class="text-[10px] text-gray-500">{{ __('opac.additional_pages.cek_plagiasi.warning_desc') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-red-50 rounded-lg">
                    <span class="w-4 h-4 bg-red-500 rounded-full"></span>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">> 25%</p>
                        <p class="text-[10px] text-red-600 font-medium">{{ __('opac.additional_pages.cek_plagiasi.fail') }}</p>
                        <p class="text-[10px] text-gray-500">{{ __('opac.additional_pages.cek_plagiasi.fail_desc') }}</p>
                    </div>
                </div>
            </div>
            <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                <p class="text-xs text-gray-600"><i class="fas fa-info-circle text-gray-400 mr-1"></i> {{ __('opac.additional_pages.cek_plagiasi.result_note') }}</p>
            </div>
        </div>

        <!-- FAQ -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg flex items-center gap-2">
            <i class="fas fa-question-circle text-teal-500"></i> {{ __('opac.additional_pages.cek_plagiasi.faq_title') }}
        </h3>
        <div class="space-y-3 mb-8">
            <details class="bg-white rounded-xl border border-gray-100 overflow-hidden group">
                <summary class="p-4 cursor-pointer font-medium text-gray-900 flex items-center justify-between hover:bg-gray-50">
                    <span class="text-sm">{{ __('opac.additional_pages.cek_plagiasi.faq_1_q') }}</span>
                    <i class="fas fa-chevron-down text-gray-400 group-open:rotate-180 transition"></i>
                </summary>
                <div class="px-4 pb-4 text-sm text-gray-600">
                    {{ __('opac.additional_pages.cek_plagiasi.faq_1_a') }}
                </div>
            </details>
            <details class="bg-white rounded-xl border border-gray-100 overflow-hidden group">
                <summary class="p-4 cursor-pointer font-medium text-gray-900 flex items-center justify-between hover:bg-gray-50">
                    <span class="text-sm">{{ __('opac.additional_pages.cek_plagiasi.faq_2_q') }}</span>
                    <i class="fas fa-chevron-down text-gray-400 group-open:rotate-180 transition"></i>
                </summary>
                <div class="px-4 pb-4 text-sm text-gray-600">
                    {{ __('opac.additional_pages.cek_plagiasi.faq_2_a') }}
                </div>
            </details>
            <details class="bg-white rounded-xl border border-gray-100 overflow-hidden group">
                <summary class="p-4 cursor-pointer font-medium text-gray-900 flex items-center justify-between hover:bg-gray-50">
                    <span class="text-sm">{{ __('opac.additional_pages.cek_plagiasi.faq_3_q') }}</span>
                    <i class="fas fa-chevron-down text-gray-400 group-open:rotate-180 transition"></i>
                </summary>
                <div class="px-4 pb-4 text-sm text-gray-600">
                    {{ __('opac.additional_pages.cek_plagiasi.faq_3_a') }}
                </div>
            </details>
            <details class="bg-white rounded-xl border border-gray-100 overflow-hidden group">
                <summary class="p-4 cursor-pointer font-medium text-gray-900 flex items-center justify-between hover:bg-gray-50">
                    <span class="text-sm">{{ __('opac.additional_pages.cek_plagiasi.faq_4_q') }}</span>
                    <i class="fas fa-chevron-down text-gray-400 group-open:rotate-180 transition"></i>
                </summary>
                <div class="px-4 pb-4 text-sm text-gray-600">
                    {{ __('opac.additional_pages.cek_plagiasi.faq_4_a') }}
                </div>
            </details>
        </div>

        <!-- CTA -->
        <div class="grid sm:grid-cols-2 gap-4">
            @auth('member')
            <a href="{{ route('opac.member.plagiarism.index') }}" class="bg-gradient-to-r from-teal-600 to-emerald-600 rounded-xl p-5 text-white text-center hover:shadow-lg hover:shadow-teal-200 transition group">
                <i class="fas fa-shield-alt text-2xl mb-2 group-hover:scale-110 transition"></i>
                <h4 class="font-bold">{{ __('opac.additional_pages.cek_plagiasi.start_check') }}</h4>
                <p class="text-teal-100 text-xs">{{ __('opac.additional_pages.cek_plagiasi.open_dashboard') }}</p>
            </a>
            @else
            <a href="{{ route('login') }}" class="bg-gradient-to-r from-teal-600 to-emerald-600 rounded-xl p-5 text-white text-center hover:shadow-lg hover:shadow-teal-200 transition group">
                <i class="fas fa-sign-in-alt text-2xl mb-2 group-hover:scale-110 transition"></i>
                <h4 class="font-bold">{{ __('opac.additional_pages.cek_plagiasi.login_first') }}</h4>
                <p class="text-teal-100 text-xs">{{ __('opac.additional_pages.cek_plagiasi.login_to_check') }}</p>
            </a>
            @endauth
            <a href="https://wa.me/6285183053934?text=Halo, saya butuh bantuan untuk cek plagiasi" target="_blank" class="bg-white rounded-xl p-5 text-center border border-gray-200 hover:border-emerald-300 hover:shadow-lg transition group">
                <i class="fab fa-whatsapp text-2xl text-emerald-600 mb-2 group-hover:scale-110 transition"></i>
                <h4 class="font-bold text-gray-900">{{ __('opac.additional_pages.cek_plagiasi.need_help') }}</h4>
                <p class="text-gray-500 text-xs">{{ __('opac.additional_pages.cek_plagiasi.chat_librarian') }}</p>
            </a>
        </div>
    </section>
</x-opac.layout>
