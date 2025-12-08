<x-opac.layout title="Research Tools">
    <x-opac.page-header 
        title="Research Tools" 
        subtitle="Alat bantu penelitian dan penulisan ilmiah"
        :breadcrumbs="[['label' => 'Discover'], ['label' => 'Research Tools']]"
    />

    <section class="max-w-5xl mx-auto px-4 py-6 lg:py-10">
        <!-- Intro -->
        <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl p-5 lg:p-6 border border-amber-100 mb-6">
            <p class="text-gray-700 text-sm lg:text-base leading-relaxed">
                Kumpulan <span class="text-amber-600 font-semibold">tools dan resources</span> untuk membantu penelitian dan penulisan karya ilmiah Anda. Dari manajemen referensi hingga pengecekan plagiarisme.
            </p>
        </div>

        <!-- Reference Management -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg flex items-center gap-2">
            <i class="fas fa-bookmark text-blue-500"></i> Manajemen Referensi
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
                <p class="text-xs text-gray-500">Reference manager dan academic social network</p>
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
                <p class="text-xs text-gray-500">Free, open-source reference management</p>
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
                <p class="text-xs text-gray-500">Professional reference management software</p>
            </a>
        </div>

        <!-- Plagiarism Check -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg flex items-center gap-2">
            <i class="fas fa-shield-alt text-emerald-500"></i> Pengecekan Plagiarisme
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
                <p class="text-xs text-gray-500">Industry-standard plagiarism detection</p>
                <span class="inline-block mt-2 px-2 py-0.5 bg-amber-100 text-amber-700 text-[10px] font-medium rounded">Akses via Kampus</span>
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
                <p class="text-xs text-gray-500">Writing assistant & plagiarism checker</p>
                <span class="inline-block mt-2 px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[10px] font-medium rounded">Free Version Available</span>
            </a>
        </div>

        <!-- Academic Search -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg flex items-center gap-2">
            <i class="fas fa-search text-purple-500"></i> Pencarian Akademik
        </h3>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
            <a href="https://scholar.google.com" target="_blank" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:border-blue-200 hover:shadow-md transition group">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fab fa-google text-blue-600"></i>
                    </div>
                    <h4 class="font-bold text-gray-900 text-sm group-hover:text-blue-600">Google Scholar</h4>
                </div>
                <p class="text-xs text-gray-500">Search scholarly literature</p>
            </a>

            <a href="https://www.semanticscholar.org" target="_blank" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:border-indigo-200 hover:shadow-md transition group">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-brain text-indigo-600"></i>
                    </div>
                    <h4 class="font-bold text-gray-900 text-sm group-hover:text-indigo-600">Semantic Scholar</h4>
                </div>
                <p class="text-xs text-gray-500">AI-powered research tool</p>
            </a>

            <a href="https://www.connectedpapers.com" target="_blank" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:border-purple-200 hover:shadow-md transition group">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-project-diagram text-purple-600"></i>
                    </div>
                    <h4 class="font-bold text-gray-900 text-sm group-hover:text-purple-600">Connected Papers</h4>
                </div>
                <p class="text-xs text-gray-500">Visual paper exploration</p>
            </a>
        </div>

        <!-- Writing Tools -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg flex items-center gap-2">
            <i class="fas fa-pen text-orange-500"></i> Tools Penulisan
        </h3>
        <div class="grid sm:grid-cols-2 gap-4">
            <a href="https://www.overleaf.com" target="_blank" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:border-green-200 hover:shadow-md transition group">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-leaf text-green-600"></i>
                    </div>
                    <h4 class="font-bold text-gray-900 text-sm group-hover:text-green-600">Overleaf</h4>
                </div>
                <p class="text-xs text-gray-500">Online LaTeX editor untuk penulisan ilmiah</p>
            </a>

            <a href="https://www.notion.so" target="_blank" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:border-gray-300 hover:shadow-md transition group">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-sticky-note text-gray-600"></i>
                    </div>
                    <h4 class="font-bold text-gray-900 text-sm group-hover:text-gray-600">Notion</h4>
                </div>
                <p class="text-xs text-gray-500">All-in-one workspace untuk catatan penelitian</p>
            </a>
        </div>
    </section>
</x-opac.layout>
