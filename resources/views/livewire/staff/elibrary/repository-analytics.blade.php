<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-emerald-500/30">
                <i class="fas fa-search-plus text-2xl"></i>
            </div>
            <div>
                <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Repository Analytics</h1>
                <p class="text-sm text-gray-500">Monitor indexing & discoverability status</p>
            </div>
        </div>
        <div class="flex gap-2">
            <button wire:click="testGoogleScholar" wire:loading.attr="disabled" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 flex items-center gap-2 disabled:opacity-50">
                <i class="fas fa-vial" wire:loading.class="fa-spin" wire:target="testGoogleScholar"></i> 
                <span wire:loading.remove wire:target="testGoogleScholar">Test Crawler Access</span>
                <span wire:loading wire:target="testGoogleScholar">Testing...</span>
            </button>
            <button wire:click="generateSitemap" wire:loading.attr="disabled" class="px-4 py-2 bg-emerald-600 text-white rounded-xl text-sm font-medium hover:bg-emerald-700 flex items-center gap-2 disabled:opacity-50">
                <i class="fas fa-sitemap" wire:loading.class="fa-spin" wire:target="generateSitemap"></i>
                <span wire:loading.remove wire:target="generateSitemap">Generate Sitemap</span>
                <span wire:loading wire:target="generateSitemap">Generating...</span>
            </button>
        </div>
    </div>

    {{-- Indexing Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-xs font-medium">Total Public</span>
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-globe text-blue-500 text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-black text-gray-900">{{ $indexingStats['total_public'] ?? 0 }}</p>
        </div>
        
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-xs font-medium">With Metadata</span>
                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tags text-emerald-500 text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-black text-gray-900">{{ $indexingStats['with_metadata'] ?? 0 }}</p>
            <p class="text-xs text-emerald-600 mt-1">{{ $indexingStats['metadata_completeness'] ?? 0 }}% complete</p>
        </div>
        
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-xs font-medium">With Fulltext</span>
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-pdf text-purple-500 text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-black text-gray-900">{{ $indexingStats['with_fulltext'] ?? 0 }}</p>
            <p class="text-xs text-purple-600 mt-1">{{ $indexingStats['fulltext_availability'] ?? 0 }}% available</p>
            @if(($indexingStats['with_fulltext'] ?? 0) == 0)
            <p class="text-xs text-amber-600 mt-1">⚠ No PDF files found</p>
            @endif
        </div>
        
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-xs font-medium">OAI Records</span>
                <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exchange-alt text-orange-500 text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-black text-gray-900">{{ $oaiStats['total_records'] ?? 0 }}</p>
            <p class="text-xs text-orange-600 mt-1">{{ $oaiStats['harvest_count'] ?? 0 }} harvests</p>
        </div>
        
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-xs font-medium">Endpoint Status</span>
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-{{ $oaiStats['endpoint_active'] ? 'check-circle' : 'times-circle' }} text-{{ $oaiStats['endpoint_active'] ? 'green' : 'red' }}-500 text-sm"></i>
                </div>
            </div>
            <p class="text-sm font-bold text-{{ $oaiStats['endpoint_active'] ? 'green' : 'red' }}-600">
                {{ $oaiStats['endpoint_active'] ? 'Active' : 'Inactive' }}
            </p>
            <p class="text-xs text-gray-500 mt-1">OAI-PMH</p>
        </div>
    </div>

    {{-- Indexing Guidelines --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
            <i class="fas fa-lightbulb text-yellow-500"></i> Indexing Guidelines
        </h3>
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-semibold text-gray-800 mb-3">Google Scholar Requirements</h4>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check text-green-500 text-xs mt-1"></i>
                        <span>Title in large font on first page</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check text-green-500 text-xs mt-1"></i>
                        <span>Authors listed below title</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check text-green-500 text-xs mt-1"></i>
                        <span>Bibliography section at end</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check text-green-500 text-xs mt-1"></i>
                        <span>PDF with searchable text</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check text-green-500 text-xs mt-1"></i>
                        <span>Abstract visible without login</span>
                    </li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-gray-800 mb-3">Metadata Standards</h4>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-start gap-2">
                        <i class="fas fa-tag text-blue-500 text-xs mt-1"></i>
                        <span>Dublin Core metadata</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-tag text-blue-500 text-xs mt-1"></i>
                        <span>Google Scholar meta tags</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-tag text-blue-500 text-xs mt-1"></i>
                        <span>OAI-PMH compliance</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-tag text-blue-500 text-xs mt-1"></i>
                        <span>JSON-LD structured data</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-tag text-blue-500 text-xs mt-1"></i>
                        <span>XML sitemap generation</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
            <i class="fas fa-clock text-blue-500"></i> Recently Updated
        </h3>
        <div class="space-y-3">
            @forelse($recentIndexed as $item)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                <div class="flex-1">
                    <h4 class="font-medium text-gray-900 text-sm">{{ Str::limit($item['title'], 60) }}</h4>
                    <p class="text-xs text-gray-500">{{ $item['author'] }} • {{ $item['updated_at']->diffForHumans() }}</p>
                </div>
                <div class="flex items-center gap-2">
                    @if($item['has_metadata'])
                        <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-xs rounded-full">Metadata ✓</span>
                    @else
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full">Metadata ⚠</span>
                    @endif
                    
                    @if($item['has_fulltext'])
                        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">Fulltext ✓</span>
                    @else
                        <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">No Fulltext</span>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-inbox text-3xl mb-2"></i>
                <p>No recent updates</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
            <i class="fas fa-tools text-purple-500"></i> Quick Actions
        </h3>
        <div class="grid md:grid-cols-3 gap-4">
            <a href="/sitemap-ethesis.xml" target="_blank" class="flex items-center gap-3 p-4 bg-emerald-50 rounded-xl hover:bg-emerald-100 transition">
                <div class="w-10 h-10 bg-emerald-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-sitemap text-white"></i>
                </div>
                <div>
                    <p class="font-semibold text-gray-900">View Sitemap</p>
                    <p class="text-xs text-emerald-600">XML sitemap for crawlers</p>
                </div>
            </a>
            
            <a href="/oai-pmh?verb=Identify" target="_blank" class="flex items-center gap-3 p-4 bg-blue-50 rounded-xl hover:bg-blue-100 transition">
                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exchange-alt text-white"></i>
                </div>
                <div>
                    <p class="font-semibold text-gray-900">OAI-PMH Endpoint</p>
                    <p class="text-xs text-blue-600">Metadata harvesting</p>
                </div>
            </a>
            
            <button wire:click="processFullText" wire:loading.attr="disabled" class="flex items-center gap-3 p-4 bg-purple-50 rounded-xl hover:bg-purple-100 transition disabled:opacity-50">
                <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-text text-white" wire:loading.class="fa-spin" wire:target="processFullText"></i>
                </div>
                <div>
                    <p class="font-semibold text-gray-900">
                        <span wire:loading.remove wire:target="processFullText">Process Full-Text</span>
                        <span wire:loading wire:target="processFullText">Processing...</span>
                    </p>
                    <p class="text-xs text-purple-600">Extract PDF content for search</p>
                </div>
            </button>
        </div>
    </div>
</div>
