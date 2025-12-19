<x-opac.layout :title="__('opac.pages.research_tools.title')">
    <x-opac.page-header 
        :title="__('opac.pages.research_tools.title')" 
        :subtitle="__('opac.pages.research_tools.subtitle')"
        :breadcrumbs="[['label' => __('opac.pages.research_tools.breadcrumb')], ['label' => __('opac.pages.research_tools.title')]]"
    />

    <section class="max-w-5xl mx-auto px-4 py-6 lg:py-10">
        <!-- Intro -->
        <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl p-5 lg:p-6 border border-amber-100 mb-6">
            <p class="text-gray-700 text-sm lg:text-base leading-relaxed">
                {{ __('opac.pages.research_tools.intro') }}
            </p>
        </div>

        <!-- Reference Management -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg flex items-center gap-2">
            <i class="fas fa-bookmark text-blue-500"></i> {{ __('opac.pages.research_tools.reference_mgmt') }}
        </h3>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
            <a href="https://www.mendeley.com" target="_blank" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:border-red-200 hover:shadow-md transition group">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-book-bookmark text-red-600"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm group-hover:text-red-600">Mendeley</h4>
                        <span class="text-[10px] text-gray-400">mendeley.com</span>
                    </div>
                </div>
                <p class="text-xs text-gray-500">{{ __('opac.pages.research_tools.mendeley_desc') }}</p>
            </a>

            <a href="https://www.zotero.org" target="_blank" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:border-red-200 hover:shadow-md transition group">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-folder-open text-red-600"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm group-hover:text-red-600">Zotero</h4>
                        <span class="text-[10px] text-gray-400">zotero.org</span>
                    </div>
                </div>
                <p class="text-xs text-gray-500">{{ __('opac.pages.research_tools.zotero_desc') }}</p>
            </a>

            <a href="https://endnote.com" target="_blank" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:border-blue-200 hover:shadow-md transition group">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-alt text-blue-600"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm group-hover:text-blue-600">EndNote</h4>
                        <span class="text-[10px] text-gray-400">endnote.com</span>
                    </div>
                </div>
                <p class="text-xs text-gray-500">{{ __('opac.pages.research_tools.endnote_desc') }}</p>
            </a>
        </div>

        <!-- Plagiarism Check -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg flex items-center gap-2">
            <i class="fas fa-shield-alt text-emerald-500"></i> {{ __('opac.pages.research_tools.plagiarism_check') }}
        </h3>
        <div class="grid sm:grid-cols-2 gap-4 mb-8">
            <a href="https://www.turnitin.com" target="_blank" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:border-emerald-200 hover:shadow-md transition group">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-double text-emerald-600"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm group-hover:text-emerald-600">Turnitin</h4>
                        <span class="text-[10px] text-gray-400">turnitin.com</span>
                    </div>
                </div>
                <p class="text-xs text-gray-500">{{ __('opac.pages.research_tools.turnitin_desc') }}</p>
                <span class="inline-block mt-2 px-2 py-0.5 bg-amber-100 text-amber-700 text-[10px] font-medium rounded">{{ __('opac.pages.research_tools.campus_access') }}</span>
            </a>

            <a href="https://www.grammarly.com" target="_blank" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:border-green-200 hover:shadow-md transition group">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-spell-check text-green-600"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm group-hover:text-green-600">Grammarly</h4>
                        <span class="text-[10px] text-gray-400">grammarly.com</span>
                    </div>
                </div>
                <p class="text-xs text-gray-500">{{ __('opac.pages.research_tools.grammarly_desc') }}</p>
                <span class="inline-block mt-2 px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[10px] font-medium rounded">{{ __('opac.pages.research_tools.free_version') }}</span>
            </a>
        </div>

        <!-- Academic Search -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg flex items-center gap-2">
            <i class="fas fa-search text-purple-500"></i> {{ __('opac.pages.research_tools.academic_search') }}
        </h3>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
            <a href="https://scholar.google.com" target="_blank" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:border-blue-200 hover:shadow-md transition group">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fab fa-google text-blue-600"></i>
                    </div>
                    <h4 class="font-bold text-gray-900 text-sm group-hover:text-blue-600">Google Scholar</h4>
                </div>
                <p class="text-xs text-gray-500">{{ __('opac.pages.research_tools.google_scholar_desc') }}</p>
            </a>

            <a href="https://www.semanticscholar.org" target="_blank" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:border-indigo-200 hover:shadow-md transition group">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-brain text-indigo-600"></i>
                    </div>
                    <h4 class="font-bold text-gray-900 text-sm group-hover:text-indigo-600">Semantic Scholar</h4>
                </div>
                <p class="text-xs text-gray-500">{{ __('opac.pages.research_tools.semantic_scholar_desc') }}</p>
            </a>

            <a href="https://www.connectedpapers.com" target="_blank" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:border-purple-200 hover:shadow-md transition group">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-project-diagram text-purple-600"></i>
                    </div>
                    <h4 class="font-bold text-gray-900 text-sm group-hover:text-purple-600">Connected Papers</h4>
                </div>
                <p class="text-xs text-gray-500">{{ __('opac.pages.research_tools.connected_papers_desc') }}</p>
            </a>
        </div>

        <!-- Writing Tools -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg flex items-center gap-2">
            <i class="fas fa-pen text-orange-500"></i> {{ __('opac.pages.research_tools.writing_tools') }}
        </h3>
        <div class="grid sm:grid-cols-2 gap-4">
            <a href="https://www.overleaf.com" target="_blank" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:border-green-200 hover:shadow-md transition group">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-leaf text-green-600"></i>
                    </div>
                    <h4 class="font-bold text-gray-900 text-sm group-hover:text-green-600">Overleaf</h4>
                </div>
                <p class="text-xs text-gray-500">{{ __('opac.pages.research_tools.overleaf_desc') }}</p>
            </a>

            <a href="https://www.notion.so" target="_blank" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:border-gray-300 hover:shadow-md transition group">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-sticky-note text-gray-600"></i>
                    </div>
                    <h4 class="font-bold text-gray-900 text-sm group-hover:text-gray-600">Notion</h4>
                </div>
                <p class="text-xs text-gray-500">{{ __('opac.pages.research_tools.notion_desc') }}</p>
            </a>
        </div>
    </section>
</x-opac.layout>
