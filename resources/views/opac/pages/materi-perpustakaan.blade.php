<x-opac.layout :title="__('opac.additional_pages.materi_perpustakaan.title')">
    <x-opac.page-header 
        :title="__('opac.additional_pages.materi_perpustakaan.title')" 
        :subtitle="__('opac.additional_pages.materi_perpustakaan.subtitle')"
        :breadcrumbs="[['label' => 'Guide'], ['label' => __('opac.additional_pages.materi_perpustakaan.title')]]"
    />

    <section class="max-w-4xl mx-auto px-4 py-6 lg:py-10">
        <!-- Intro -->
        <div class="bg-gradient-to-br from-pink-50 to-rose-50 rounded-2xl p-5 lg:p-6 border border-pink-100 mb-6">
            <p class="text-gray-700 text-sm lg:text-base leading-relaxed">
                {!! __('opac.additional_pages.materi_perpustakaan.intro') !!}
            </p>
        </div>

        <!-- Materials -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg">{{ __('opac.additional_pages.materi_perpustakaan.available_materials') }}</h3>
        <div class="space-y-3 mb-8">
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:border-pink-200 hover:shadow-md transition">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-file-pdf text-red-600 text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900 text-sm">{{ __('opac.additional_pages.materi_perpustakaan.mat_1_title') }}</h4>
                        <p class="text-xs text-gray-500 mb-2">{{ __('opac.additional_pages.materi_perpustakaan.mat_1_desc') }}</p>
                        <div class="flex items-center gap-3">
                            <span class="text-[10px] text-gray-400"><i class="fas fa-file mr-1"></i> PDF</span>
                            <span class="text-[10px] text-gray-400"><i class="fas fa-download mr-1"></i> 2.5 MB</span>
                        </div>
                    </div>
                    <a href="#" class="px-3 py-1.5 bg-pink-100 text-pink-600 rounded-lg text-xs font-medium hover:bg-pink-200 transition">
                        <i class="fas fa-download"></i>
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:border-pink-200 hover:shadow-md transition">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-file-powerpoint text-orange-600 text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900 text-sm">{{ __('opac.additional_pages.materi_perpustakaan.mat_2_title') }}</h4>
                        <p class="text-xs text-gray-500 mb-2">{{ __('opac.additional_pages.materi_perpustakaan.mat_2_desc') }}</p>
                        <div class="flex items-center gap-3">
                            <span class="text-[10px] text-gray-400"><i class="fas fa-file mr-1"></i> PPTX</span>
                            <span class="text-[10px] text-gray-400"><i class="fas fa-download mr-1"></i> 5.1 MB</span>
                        </div>
                    </div>
                    <a href="#" class="px-3 py-1.5 bg-pink-100 text-pink-600 rounded-lg text-xs font-medium hover:bg-pink-200 transition">
                        <i class="fas fa-download"></i>
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:border-pink-200 hover:shadow-md transition">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-file-pdf text-blue-600 text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900 text-sm">{{ __('opac.additional_pages.materi_perpustakaan.mat_3_title') }}</h4>
                        <p class="text-xs text-gray-500 mb-2">{{ __('opac.additional_pages.materi_perpustakaan.mat_3_desc') }}</p>
                        <div class="flex items-center gap-3">
                            <span class="text-[10px] text-gray-400"><i class="fas fa-file mr-1"></i> PDF</span>
                            <span class="text-[10px] text-gray-400"><i class="fas fa-download mr-1"></i> 3.2 MB</span>
                        </div>
                    </div>
                    <a href="#" class="px-3 py-1.5 bg-pink-100 text-pink-600 rounded-lg text-xs font-medium hover:bg-pink-200 transition">
                        <i class="fas fa-download"></i>
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:border-pink-200 hover:shadow-md transition">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-file-pdf text-emerald-600 text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900 text-sm">{{ __('opac.additional_pages.materi_perpustakaan.mat_4_title') }}</h4>
                        <p class="text-xs text-gray-500 mb-2">{{ __('opac.additional_pages.materi_perpustakaan.mat_4_desc') }}</p>
                        <div class="flex items-center gap-3">
                            <span class="text-[10px] text-gray-400"><i class="fas fa-file mr-1"></i> PDF</span>
                            <span class="text-[10px] text-gray-400"><i class="fas fa-download mr-1"></i> 1.8 MB</span>
                        </div>
                    </div>
                    <a href="#" class="px-3 py-1.5 bg-pink-100 text-pink-600 rounded-lg text-xs font-medium hover:bg-pink-200 transition">
                        <i class="fas fa-download"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Request -->
        <div class="bg-gradient-to-r from-pink-500 to-rose-500 rounded-xl p-5 text-white text-center">
            <h4 class="font-bold mb-2">{{ __('opac.additional_pages.materi_perpustakaan.need_special') }}</h4>
            <p class="text-pink-100 text-sm mb-4">{{ __('opac.additional_pages.materi_perpustakaan.need_special_desc') }}</p>
            <a href="mailto:library@unida.gontor.ac.id?subject=Permintaan Materi Perpustakaan" class="inline-flex items-center gap-2 px-4 py-2 bg-white text-pink-600 rounded-lg text-sm font-medium hover:bg-pink-50 transition">
                <i class="fas fa-envelope"></i> {{ __('opac.additional_pages.materi_perpustakaan.submit_request') }}
            </a>
        </div>
    </section>
</x-opac.layout>
