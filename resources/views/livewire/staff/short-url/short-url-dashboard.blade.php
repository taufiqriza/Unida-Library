<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-500/30">
                <i class="fas fa-link text-2xl"></i>
            </div>
            <div>
                <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Short URL Dashboard</h1>
                <p class="text-sm text-gray-500">Kelola dan pantau semua short URL</p>
            </div>
        </div>
        <div class="flex gap-2">
            <button onclick="openCreateModal()" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 flex items-center gap-2">
                <i class="fas fa-plus"></i> Buat Short URL
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-xs font-medium">Total URLs</span>
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-link text-blue-500 text-sm"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ \App\Models\ShortUrl::count() }}</div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-xs font-medium">Total Clicks</span>
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-mouse-pointer text-green-500 text-sm"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ number_format(\App\Models\ShortUrl::sum('clicks')) }}</div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-xs font-medium">Hari Ini</span>
                <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-day text-yellow-500 text-sm"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ \App\Models\ShortUrl::whereDate('created_at', today())->count() }}</div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-xs font-medium">Users Aktif</span>
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-purple-500 text-sm"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ \App\Models\ShortUrl::distinct('user_id')->count('user_id') }}</div>
        </div>
    </div>

    {{-- Search & Filters --}}
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="flex-1">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input 
                        type="text" 
                        wire:model.live="search"
                        placeholder="Cari berdasarkan judul, URL, atau kode..."
                        class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th wire:click="sortBy('code')" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            <div class="flex items-center gap-1">
                                Kode
                                @if($sortBy === 'code')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-blue-500"></i>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            URL & Judul
                        </th>
                        <th wire:click="sortBy('clicks')" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            <div class="flex items-center gap-1">
                                Clicks
                                @if($sortBy === 'clicks')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-blue-500"></i>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Pembuat
                        </th>
                        <th wire:click="sortBy('created_at')" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            <div class="flex items-center gap-1">
                                Dibuat
                                @if($sortBy === 'created_at')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-blue-500"></i>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($shortUrls as $shortUrl)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <code class="px-3 py-1 bg-gray-100 rounded-lg text-sm font-mono">{{ $shortUrl->code }}</code>
                            </td>
                            <td class="px-6 py-4">
                                <div class="max-w-xs">
                                    @if($shortUrl->title)
                                        <div class="text-sm font-medium text-gray-900 mb-1">{{ $shortUrl->title }}</div>
                                    @endif
                                    <div class="text-sm text-gray-500 truncate">{{ $shortUrl->original_url }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <i class="fas fa-mouse-pointer text-gray-400 mr-2"></i>
                                    <span class="text-sm font-medium text-gray-900">{{ number_format($shortUrl->clicks) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-gray-500 text-xs"></i>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $shortUrl->user->name ?? 'System' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $shortUrl->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($shortUrl->isActive())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <a href="{{ url('/s/' . $shortUrl->code) }}" target="_blank" 
                                       class="text-blue-600 hover:text-blue-900 p-2 hover:bg-blue-50 rounded-lg transition">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    <button onclick="copyToClipboard('{{ url('/s/' . $shortUrl->code) }}')"
                                            class="text-gray-600 hover:text-gray-900 p-2 hover:bg-gray-50 rounded-lg transition">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-link text-gray-300 text-4xl mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada Short URL</h3>
                                    <p class="text-gray-500">Short URL yang dibuat akan muncul di sini</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($shortUrls->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $shortUrls->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Create Modal --}}
<div id="createModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/70">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg" x-data="shortUrlGenerator()">
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-link text-blue-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Buat Short URL</h3>
                    <p class="text-sm text-gray-500">Generate link pendek profesional</p>
                </div>
            </div>
            <button onclick="closeCreateModal()" class="p-2 hover:bg-gray-100 rounded-lg transition">
                <i class="fas fa-times text-gray-500"></i>
            </button>
        </div>

        <div class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">URL Asli</label>
                <input 
                    type="url" 
                    x-model="originalUrl"
                    placeholder="https://example.com/very-long-url"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    required
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Judul (Opsional)</label>
                <input 
                    type="text" 
                    x-model="title"
                    placeholder="Judul untuk link ini"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kode Kustom (Opsional)</label>
                <div class="flex">
                    <span class="inline-flex items-center px-3 text-sm text-gray-500 bg-gray-50 border border-r-0 border-gray-300 rounded-l-lg">
                        library.unida.gontor.ac.id/s/
                    </span>
                    <input 
                        type="text" 
                        x-model="customCode"
                        placeholder="kode-unik"
                        class="flex-1 px-4 py-3 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>
            </div>

            <div x-show="generatedUrl" class="bg-green-50 border border-green-200 rounded-lg p-4">
                <p class="text-sm font-medium text-green-800 mb-2">
                    <i class="fas fa-check-circle mr-2"></i>Short URL berhasil dibuat!
                </p>
                <div class="flex items-center gap-2">
                    <code class="text-sm bg-white px-3 py-1 rounded border text-green-700 flex-1" x-text="generatedUrl"></code>
                    <button @click="copyUrl()" class="p-2 hover:bg-green-100 rounded transition">
                        <i class="fas fa-copy text-green-600"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 bg-gray-50 rounded-b-xl">
            <button 
                onclick="closeCreateModal()"
                class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition"
            >
                <span x-text="generatedUrl ? 'Tutup' : 'Batal'"></span>
            </button>
            <button 
                x-show="!generatedUrl"
                @click="generate()"
                :disabled="loading || !originalUrl"
                class="px-6 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-lg transition flex items-center gap-2"
            >
                <i class="fas fa-magic" x-show="!loading"></i>
                <i class="fas fa-spinner fa-spin" x-show="loading"></i>
                <span x-text="loading ? 'Membuat...' : 'Generate'"></span>
            </button>
        </div>
    </div>
</div>

<script>
function openCreateModal() {
    document.getElementById('createModal').classList.remove('hidden');
    document.getElementById('createModal').classList.add('flex');
}

function closeCreateModal() {
    document.getElementById('createModal').classList.add('hidden');
    document.getElementById('createModal').classList.remove('flex');
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
        toast.innerHTML = '<i class="fas fa-check mr-2"></i>URL berhasil disalin!';
        document.body.appendChild(toast);
        
        setTimeout(() => {
            if (document.body.contains(toast)) {
                document.body.removeChild(toast);
            }
        }, 3000);
    });
}

function shortUrlGenerator() {
    return {
        originalUrl: '',
        title: '',
        customCode: '',
        generatedUrl: '',
        loading: false,

        async generate() {
            if (!this.originalUrl) return;
            
            this.loading = true;
            
            try {
                const response = await fetch('/api/short-url', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        url: this.originalUrl,
                        title: this.title,
                        custom_code: this.customCode
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    this.generatedUrl = data.data.short_url;
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                }
            } catch (error) {
                console.error('Error:', error);
            } finally {
                this.loading = false;
            }
        },

        copyUrl() {
            navigator.clipboard.writeText(this.generatedUrl);
        }
    }
}
</script>
