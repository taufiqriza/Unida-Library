<x-opac.layout :title="__('opac.additional_pages.unggah_tugas_akhir.title')">
    <x-opac.page-header 
        :title="__('opac.additional_pages.unggah_tugas_akhir.title')" 
        :subtitle="__('opac.additional_pages.unggah_tugas_akhir.subtitle')"
        :breadcrumbs="[['label' => __('opac.menu.guide')], ['label' => __('opac.additional_pages.unggah_tugas_akhir.title')]]"
    />

    <section class="max-w-4xl mx-auto px-4 py-6 lg:py-10">
        <!-- Intro -->
        <div class="bg-gradient-to-br from-primary-50 to-indigo-50 rounded-2xl p-5 lg:p-6 border border-primary-100 mb-8">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-graduation-cap text-primary-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 mb-1">{{ __('opac.additional_pages.unggah_tugas_akhir.mandatory_title') }}</h3>
                    <p class="text-gray-700 text-sm leading-relaxed">
                        {!! __('opac.additional_pages.unggah_tugas_akhir.mandatory_desc') !!}
                    </p>
                </div>
            </div>
        </div>

        <!-- Alur Pengajuan - 5 Steps -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg flex items-center gap-2">
            <i class="fas fa-route text-primary-500"></i> {{ __('opac.additional_pages.unggah_tugas_akhir.flow_title') }}
        </h3>
        
        <div class="relative mb-8">
            <!-- Timeline Line -->
            <div class="absolute left-5 top-8 bottom-8 w-0.5 bg-gradient-to-b from-primary-500 to-primary-300 hidden sm:block"></div>
            
            <div class="space-y-4">
                <!-- Step 1 -->
                <div class="flex gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100 relative">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-700 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-lg shadow-primary-200 z-10">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-900">{{ __('opac.additional_pages.unggah_tugas_akhir.step_1_title') }}</h4>
                        <p class="text-sm text-gray-600 mt-1">{{ __('opac.additional_pages.unggah_tugas_akhir.step_1_desc') }}</p>
                        <ul class="text-xs text-gray-500 mt-2 space-y-1">
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500"></i> {{ __('opac.additional_pages.unggah_tugas_akhir.step_1_1') }}</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500"></i> {{ __('opac.additional_pages.unggah_tugas_akhir.step_1_2') }}</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500"></i> {{ __('opac.additional_pages.unggah_tugas_akhir.step_1_3') }}</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500"></i> {{ __('opac.additional_pages.unggah_tugas_akhir.step_1_4') }}</li>
                        </ul>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="flex gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100 relative">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-700 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-lg shadow-primary-200 z-10">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-900">{{ __('opac.additional_pages.unggah_tugas_akhir.step_2_title') }}</h4>
                        <p class="text-sm text-gray-600 mt-1">{{ __('opac.additional_pages.unggah_tugas_akhir.step_2_desc') }}</p>
                        <ul class="text-xs text-gray-500 mt-2 space-y-1">
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500"></i> {{ __('opac.additional_pages.unggah_tugas_akhir.step_2_1') }}</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500"></i> {{ __('opac.additional_pages.unggah_tugas_akhir.step_2_2') }}</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500"></i> {{ __('opac.additional_pages.unggah_tugas_akhir.step_2_3') }}</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500"></i> {{ __('opac.additional_pages.unggah_tugas_akhir.step_2_4') }}</li>
                        </ul>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="flex gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100 relative">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-700 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-lg shadow-primary-200 z-10">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-900">{{ __('opac.additional_pages.unggah_tugas_akhir.step_3_title') }}</h4>
                        <p class="text-sm text-gray-600 mt-1">{{ __('opac.additional_pages.unggah_tugas_akhir.step_3_desc') }}</p>
                        <ul class="text-xs text-gray-500 mt-2 space-y-1">
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500"></i> {{ __('opac.additional_pages.unggah_tugas_akhir.step_3_1') }}</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500"></i> {{ __('opac.additional_pages.unggah_tugas_akhir.step_3_2') }}</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500"></i> {{ __('opac.additional_pages.unggah_tugas_akhir.step_3_3') }}</li>
                        </ul>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="flex gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100 relative">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-700 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-lg shadow-primary-200 z-10">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-900">{{ __('opac.additional_pages.unggah_tugas_akhir.step_4_title') }}</h4>
                        <p class="text-sm text-gray-600 mt-1">{{ __('opac.additional_pages.unggah_tugas_akhir.step_4_desc') }}</p>
                        <div class="grid sm:grid-cols-2 gap-3 mt-3">
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-xs font-semibold text-gray-700 flex items-center gap-2">
                                    <i class="fas fa-image text-blue-500"></i> {{ __('opac.additional_pages.unggah_tugas_akhir.cover') }}
                                </p>
                                <p class="text-[10px] text-gray-500 mt-1">{{ __('opac.additional_pages.unggah_tugas_akhir.cover_format') }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-xs font-semibold text-gray-700 flex items-center gap-2">
                                    <i class="fas fa-file-signature text-green-500"></i> {{ __('opac.additional_pages.unggah_tugas_akhir.approval') }}
                                </p>
                                <p class="text-[10px] text-gray-500 mt-1">{{ __('opac.additional_pages.unggah_tugas_akhir.approval_format') }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-xs font-semibold text-gray-700 flex items-center gap-2">
                                    <i class="fas fa-file-alt text-purple-500"></i> {{ __('opac.additional_pages.unggah_tugas_akhir.preview') }}
                                </p>
                                <p class="text-[10px] text-gray-500 mt-1">{{ __('opac.additional_pages.unggah_tugas_akhir.preview_format') }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-xs font-semibold text-gray-700 flex items-center gap-2">
                                    <i class="fas fa-book text-orange-500"></i> {{ __('opac.additional_pages.unggah_tugas_akhir.fulltext') }}
                                </p>
                                <p class="text-[10px] text-gray-500 mt-1">{{ __('opac.additional_pages.unggah_tugas_akhir.fulltext_format') }}</p>
                            </div>
                        </div>
                        <div class="mt-3 p-2 bg-amber-50 rounded-lg border border-amber-200">
                            <p class="text-[10px] text-amber-700"><i class="fas fa-info-circle mr-1"></i> {!! __('opac.additional_pages.unggah_tugas_akhir.access_note') !!}</p>
                        </div>
                    </div>
                </div>

                <!-- Step 5 -->
                <div class="flex gap-4 bg-white rounded-xl p-4 shadow-sm border border-gray-100 relative">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-lg shadow-emerald-200 z-10">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-900">{{ __('opac.additional_pages.unggah_tugas_akhir.step_5_title') }}</h4>
                        <p class="text-sm text-gray-600 mt-1">{{ __('opac.additional_pages.unggah_tugas_akhir.step_5_desc') }}</p>
                        <ul class="text-xs text-gray-500 mt-2 space-y-1">
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500"></i> {{ __('opac.additional_pages.unggah_tugas_akhir.step_5_1') }}</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500"></i> {{ __('opac.additional_pages.unggah_tugas_akhir.step_5_2') }}</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500"></i> {{ __('opac.additional_pages.unggah_tugas_akhir.step_5_3') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Pengajuan -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg flex items-center gap-2">
            <i class="fas fa-tasks text-primary-500"></i> {{ __('opac.additional_pages.unggah_tugas_akhir.status_title') }}
        </h3>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 mb-8">
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                    <span class="w-3 h-3 bg-gray-400 rounded-full"></span>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ __('opac.additional_pages.unggah_tugas_akhir.draft') }}</p>
                        <p class="text-[10px] text-gray-500">{{ __('opac.additional_pages.unggah_tugas_akhir.draft_desc') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg">
                    <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ __('opac.additional_pages.unggah_tugas_akhir.submitted') }}</p>
                        <p class="text-[10px] text-gray-500">{{ __('opac.additional_pages.unggah_tugas_akhir.submitted_desc') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-orange-50 rounded-lg">
                    <span class="w-3 h-3 bg-orange-500 rounded-full"></span>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ __('opac.additional_pages.unggah_tugas_akhir.revision') }}</p>
                        <p class="text-[10px] text-gray-500">{{ __('opac.additional_pages.unggah_tugas_akhir.revision_desc') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-emerald-50 rounded-lg">
                    <span class="w-3 h-3 bg-emerald-500 rounded-full"></span>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ __('opac.additional_pages.unggah_tugas_akhir.approved') }}</p>
                        <p class="text-[10px] text-gray-500">{{ __('opac.additional_pages.unggah_tugas_akhir.approved_desc') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-primary-50 rounded-lg">
                    <span class="w-3 h-3 bg-primary-500 rounded-full"></span>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ __('opac.additional_pages.unggah_tugas_akhir.published') }}</p>
                        <p class="text-[10px] text-gray-500">{{ __('opac.additional_pages.unggah_tugas_akhir.published_desc') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-red-50 rounded-lg">
                    <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ __('opac.additional_pages.unggah_tugas_akhir.rejected') }}</p>
                        <p class="text-[10px] text-gray-500">{{ __('opac.additional_pages.unggah_tugas_akhir.rejected_desc') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tips -->
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 mb-8">
            <h4 class="font-bold text-amber-800 mb-3 flex items-center gap-2">
                <i class="fas fa-lightbulb text-amber-500"></i> {{ __('opac.additional_pages.unggah_tugas_akhir.tips_title') }}
            </h4>
            <ul class="text-sm text-amber-700 space-y-2">
                <li class="flex items-start gap-2">
                    <i class="fas fa-check-circle text-amber-500 mt-0.5"></i>
                    <span>{{ __('opac.additional_pages.unggah_tugas_akhir.tip_1') }}</span>
                </li>
                <li class="flex items-start gap-2">
                    <i class="fas fa-check-circle text-amber-500 mt-0.5"></i>
                    <span>{{ __('opac.additional_pages.unggah_tugas_akhir.tip_2') }}</span>
                </li>
                <li class="flex items-start gap-2">
                    <i class="fas fa-check-circle text-amber-500 mt-0.5"></i>
                    <span>{{ __('opac.additional_pages.unggah_tugas_akhir.tip_3') }}</span>
                </li>
                <li class="flex items-start gap-2">
                    <i class="fas fa-check-circle text-amber-500 mt-0.5"></i>
                    <span>{{ __('opac.additional_pages.unggah_tugas_akhir.tip_4') }}</span>
                </li>
            </ul>
        </div>

        <!-- CTA -->
        <div class="grid sm:grid-cols-2 gap-4">
            @auth('member')
            <a href="{{ route('opac.member.submit-thesis') }}" class="bg-gradient-to-r from-primary-600 to-indigo-600 rounded-xl p-5 text-white text-center hover:shadow-lg hover:shadow-primary-200 transition group">
                <i class="fas fa-upload text-2xl mb-2 group-hover:scale-110 transition"></i>
                <h4 class="font-bold">{{ __('opac.additional_pages.unggah_tugas_akhir.start_upload') }}</h4>
                <p class="text-primary-100 text-xs">{{ __('opac.additional_pages.unggah_tugas_akhir.start_upload_desc') }}</p>
            </a>
            @else
            <a href="{{ route('login') }}" class="bg-gradient-to-r from-primary-600 to-indigo-600 rounded-xl p-5 text-white text-center hover:shadow-lg hover:shadow-primary-200 transition group">
                <i class="fas fa-sign-in-alt text-2xl mb-2 group-hover:scale-110 transition"></i>
                <h4 class="font-bold">{{ __('opac.additional_pages.unggah_tugas_akhir.login_first') }}</h4>
                <p class="text-primary-100 text-xs">{{ __('opac.additional_pages.unggah_tugas_akhir.login_to_upload') }}</p>
            </a>
            @endauth
            <a href="https://wa.me/6285183053934?text=Halo, saya butuh bantuan untuk upload tugas akhir" target="_blank" class="bg-white rounded-xl p-5 text-center border border-gray-200 hover:border-emerald-300 hover:shadow-lg transition group">
                <i class="fab fa-whatsapp text-2xl text-emerald-600 mb-2 group-hover:scale-110 transition"></i>
                <h4 class="font-bold text-gray-900">{{ __('opac.additional_pages.unggah_tugas_akhir.need_help') }}</h4>
                <p class="text-gray-500 text-xs">{{ __('opac.additional_pages.unggah_tugas_akhir.chat_librarian') }}</p>
            </a>
        </div>
    </section>
</x-opac.layout>
