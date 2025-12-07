<x-filament-panels::page>
    @php
        $stats = $this->getStats();
        $byMediaType = $this->getByMediaType();
        $byCollectionType = $this->getByCollectionType();
        $byClassification = $this->getByClassification();
        $byLanguage = $this->getByLanguage();
        $byPublisher = $this->getByPublisher();
        $byYear = $this->getByYear();
        $bySubject = $this->getBySubject();
        $byAuthor = $this->getByAuthor();
    @endphp

    <style>
        .gradient-card {
            border-radius: 20px;
            padding: 1.5rem;
            color: white;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 10px 40px -10px rgba(0,0,0,0.3);
        }
        .gradient-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 50px -10px rgba(0,0,0,0.4);
        }
        .gradient-card::before {
            content: '';
            position: absolute;
            top: -20px;
            right: -20px;
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
        }
        .gradient-blue { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .gradient-green { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
        .gradient-orange { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .gradient-purple { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .gradient-pink { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
        .gradient-teal { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        .gradient-indigo { background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%); }
        .gradient-rose { background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%); }
        
        .category-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        .dark .category-card {
            background: rgb(31 41 55);
        }
        .category-card:hover {
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        }
        .category-header {
            padding: 1rem 1.25rem;
            color: white;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .category-header svg {
            width: 1.25rem;
            height: 1.25rem;
        }
        .header-blue { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .header-green { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
        .header-orange { background: linear-gradient(135deg, #f5af19 0%, #f12711 100%); }
        .header-cyan { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .header-purple { background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%); }
        .header-pink { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
        .header-teal { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        .header-indigo { background: linear-gradient(135deg, #5f72bd 0%, #9b23ea 100%); }
        
        .category-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.875rem 1.25rem;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .dark .category-item {
            border-bottom-color: rgba(255,255,255,0.05);
        }
        .category-item:hover {
            background: linear-gradient(90deg, rgba(99,102,241,0.1) 0%, transparent 100%);
            padding-left: 1.5rem;
        }
        .category-item:last-child {
            border-bottom: none;
        }
        .item-name {
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
        }
        .dark .item-name {
            color: #e5e7eb;
        }
        .item-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .badge-blue { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .badge-green { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; }
        .badge-orange { background: linear-gradient(135deg, #f5af19 0%, #f12711 100%); color: white; }
        .badge-cyan { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; }
        .badge-purple { background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%); color: #4c1d95; }
        .badge-pink { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: #831843; }
        .badge-teal { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: #134e4a; }
        .badge-indigo { background: linear-gradient(135deg, #5f72bd 0%, #9b23ea 100%); color: white; }
        
        .stat-icon {
            width: 3.5rem;
            height: 3.5rem;
            background: rgba(255,255,255,0.2);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .stat-icon svg {
            width: 1.75rem;
            height: 1.75rem;
        }
        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1;
        }
        .stat-label {
            font-size: 0.875rem;
            opacity: 0.9;
            margin-top: 0.25rem;
        }
    </style>

    {{-- Hero Section --}}
    <div class="gradient-card gradient-blue mb-6">
        <div class="flex items-center gap-5">
            <div class="stat-icon">
                <x-heroicon-o-squares-2x2 class="text-white" />
            </div>
            <div>
                <h1 class="text-2xl font-bold">Klasifikasi & Kategori Koleksi</h1>
                <p class="opacity-90 text-sm mt-1">Analisis koleksi perpustakaan berdasarkan berbagai kategori</p>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="flex flex-wrap lg:flex-nowrap gap-6 mb-10">
        <div class="gradient-card gradient-blue flex-1 min-w-[200px]">
            <div class="flex items-center gap-4">
                <div class="stat-icon"><x-heroicon-o-book-open class="text-white" /></div>
                <div>
                    <div class="stat-value">{{ number_format($stats['total_books']) }}</div>
                    <div class="stat-label">Total Judul</div>
                </div>
            </div>
        </div>
        <div class="gradient-card gradient-green flex-1 min-w-[200px]">
            <div class="flex items-center gap-4">
                <div class="stat-icon"><x-heroicon-o-archive-box class="text-white" /></div>
                <div>
                    <div class="stat-value">{{ number_format($stats['total_items']) }}</div>
                    <div class="stat-label">Total Eksemplar</div>
                </div>
            </div>
        </div>
        <div class="gradient-card gradient-orange flex-1 min-w-[200px]">
            <div class="flex items-center gap-4">
                <div class="stat-icon"><x-heroicon-o-cube class="text-white" /></div>
                <div>
                    <div class="stat-value">{{ number_format($stats['total_media_types']) }}</div>
                    <div class="stat-label">Jenis Bahan</div>
                </div>
            </div>
        </div>
        <div class="gradient-card gradient-purple flex-1 min-w-[200px]">
            <div class="flex items-center gap-4">
                <div class="stat-icon"><x-heroicon-o-folder class="text-white" /></div>
                <div>
                    <div class="stat-value">{{ number_format($stats['total_collection_types']) }}</div>
                    <div class="stat-label">Jenis Koleksi</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Category Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        {{-- By Media Type --}}
        <div class="category-card">
            <div class="category-header header-blue">
                <x-heroicon-o-cube />
                <span>Jenis Bahan</span>
            </div>
            <div class="max-h-72 overflow-y-auto">
                @forelse($byMediaType as $item)
                    <div class="category-item" wire:click="openFilter('media_type', '{{ $item['id'] }}', '{{ $item['name'] }}')">
                        <span class="item-name">{{ $item['name'] }}</span>
                        <span class="item-badge badge-blue">{{ number_format($item['count']) }}</span>
                    </div>
                @empty
                    <div class="p-4 text-center text-gray-400 text-sm">Tidak ada data</div>
                @endforelse
            </div>
        </div>

        {{-- By Collection Type --}}
        <div class="category-card">
            <div class="category-header header-green">
                <x-heroicon-o-folder-open />
                <span>Jenis Koleksi</span>
            </div>
            <div class="max-h-72 overflow-y-auto">
                @forelse($byCollectionType as $item)
                    <div class="category-item" wire:click="openFilter('collection_type', '{{ $item['id'] }}', '{{ $item['name'] }}')">
                        <span class="item-name">{{ $item['name'] }}</span>
                        <div class="flex gap-1">
                            <span class="item-badge badge-green">{{ $item['books'] }}</span>
                            <span class="item-badge" style="background:#e5e7eb;color:#374151;">{{ $item['count'] }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-gray-400 text-sm">Tidak ada data</div>
                @endforelse
            </div>
        </div>

        {{-- By Classification --}}
        <div class="category-card">
            <div class="category-header header-orange">
                <x-heroicon-o-hashtag />
                <span>Klasifikasi</span>
            </div>
            <div class="max-h-72 overflow-y-auto">
                @forelse($byClassification as $item)
                    <div class="category-item" wire:click="openFilter('classification', '{{ $item['classification'] }}', '{{ $item['classification'] }}')">
                        <span class="item-name"><span class="text-gray-400">#</span>{{ $item['classification'] }}</span>
                        <span class="item-badge badge-orange">{{ number_format($item['count']) }}</span>
                    </div>
                @empty
                    <div class="p-4 text-center text-gray-400 text-sm">Tidak ada data</div>
                @endforelse
            </div>
        </div>

        {{-- By Language --}}
        <div class="category-card">
            <div class="category-header header-cyan">
                <x-heroicon-o-language />
                <span>Bahasa</span>
            </div>
            <div class="max-h-72 overflow-y-auto">
                @forelse($byLanguage as $item)
                    <div class="category-item" wire:click="openFilter('language', '{{ $item['language'] }}', '{{ $item['language'] }}')">
                        <span class="item-name">{{ $item['language'] }}</span>
                        <span class="item-badge badge-cyan">{{ number_format($item['count']) }}</span>
                    </div>
                @empty
                    <div class="p-4 text-center text-gray-400 text-sm">Tidak ada data</div>
                @endforelse
            </div>
        </div>

        {{-- By Publisher --}}
        <div class="category-card">
            <div class="category-header header-purple">
                <x-heroicon-o-building-office />
                <span>Penerbit</span>
            </div>
            <div class="max-h-72 overflow-y-auto">
                @forelse($byPublisher as $item)
                    <div class="category-item" wire:click="openFilter('publisher', '{{ $item['id'] }}', '{{ $item['name'] }}')">
                        <span class="item-name truncate">{{ $item['name'] }}</span>
                        <span class="item-badge badge-purple">{{ number_format($item['count']) }}</span>
                    </div>
                @empty
                    <div class="p-4 text-center text-gray-400 text-sm">Tidak ada data</div>
                @endforelse
            </div>
        </div>

        {{-- By Year --}}
        <div class="category-card">
            <div class="category-header header-pink">
                <x-heroicon-o-calendar />
                <span>Tahun Terbit</span>
            </div>
            <div class="max-h-72 overflow-y-auto">
                @forelse($byYear as $item)
                    <div class="category-item" wire:click="openFilter('year', '{{ $item['publish_year'] }}', 'Tahun {{ $item['publish_year'] }}')">
                        <span class="item-name">{{ $item['publish_year'] }}</span>
                        <span class="item-badge badge-pink">{{ number_format($item['count']) }}</span>
                    </div>
                @empty
                    <div class="p-4 text-center text-gray-400 text-sm">Tidak ada data</div>
                @endforelse
            </div>
        </div>

        {{-- By Subject --}}
        <div class="category-card">
            <div class="category-header header-teal">
                <x-heroicon-o-tag />
                <span>Subjek</span>
            </div>
            <div class="max-h-72 overflow-y-auto">
                @forelse($bySubject as $item)
                    <div class="category-item" wire:click="openFilter('subject', '{{ $item['id'] }}', '{{ $item['name'] }}')">
                        <span class="item-name truncate">{{ $item['name'] }}</span>
                        <span class="item-badge badge-teal">{{ number_format($item['count']) }}</span>
                    </div>
                @empty
                    <div class="p-4 text-center text-gray-400 text-sm">Tidak ada data</div>
                @endforelse
            </div>
        </div>

        {{-- By Author --}}
        <div class="category-card">
            <div class="category-header header-indigo">
                <x-heroicon-o-user />
                <span>Pengarang</span>
            </div>
            <div class="max-h-72 overflow-y-auto">
                @forelse($byAuthor as $item)
                    <div class="category-item" wire:click="openFilter('author', '{{ $item['id'] }}', '{{ $item['name'] }}')">
                        <span class="item-name truncate">{{ $item['name'] }}</span>
                        <span class="item-badge badge-indigo">{{ number_format($item['count']) }}</span>
                    </div>
                @empty
                    <div class="p-4 text-center text-gray-400 text-sm">Tidak ada data</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Modal for filtered books --}}
    @if($filterType)
    <div class="fixed inset-0 z-50" style="background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl w-full max-w-4xl shadow-2xl overflow-hidden">
                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ $filterLabel }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Daftar koleksi perpustakaan</p>
                    </div>
                    <button wire:click="closeFilter" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </button>
                </div>

                {{-- Search --}}
                <div class="px-6 py-4 bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
                    <div class="relative">
                        <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                        <input type="text" wire:model.live.debounce.300ms="search" 
                            placeholder="Cari judul, pengarang, atau ISBN..."
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>

                {{-- Book List --}}
                <div class="overflow-auto" style="max-height: 60vh;">
                    @php $filteredBooks = $this->getFilteredBooks(); @endphp
                    @if(count($filteredBooks) > 0)
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-900 sticky top-0">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Judul</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Pengarang</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Klasifikasi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tahun</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Eks</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($filteredBooks as $book)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ Str::limit($book['title'], 40) }}</div>
                                    @if($book['isbn'])
                                    <div class="text-xs text-gray-500">{{ $book['isbn'] }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                    {{ Str::limit(collect($book['authors'] ?? [])->pluck('name')->implode(', '), 25) ?: '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($book['classification'])
                                    <span class="inline-block px-2 py-1 text-xs font-medium rounded" style="background: rgba(99,102,241,0.1); color: rgb(79,70,229);">{{ $book['classification'] }}</span>
                                    @else
                                    <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $book['publish_year'] ?: '-' }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-block px-2 py-1 text-xs font-medium rounded" style="background: rgba(16,185,129,0.1); color: rgb(5,150,105);">{{ count($book['items'] ?? []) }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('filament.admin.resources.books.edit', $book['id']) }}" 
                                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg transition" style="color: rgb(79,70,229);">
                                        <x-heroicon-m-eye class="w-4 h-4 mr-1" />
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="text-center py-12">
                        <x-heroicon-o-inbox class="w-12 h-12 mx-auto text-gray-300" />
                        <p class="mt-2 text-sm text-gray-500">Tidak ada buku ditemukan</p>
                    </div>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                    <span class="text-sm text-gray-500">{{ count($filteredBooks) }} buku</span>
                    <button wire:click="closeFilter" class="px-4 py-2 text-sm font-medium border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</x-filament-panels::page>
