<div class="space-y-5">
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-gradient-to-br from-rose-500 via-pink-500 to-fuchsia-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-rose-500/25">
                <i class="fas fa-newspaper text-xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Berita & Pengumuman</h1>
                <p class="text-sm text-gray-500">Kelola berita cabang Anda</p>
            </div>
        </div>

        <a href="{{ route('staff.news.create') }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-rose-500 to-pink-600 hover:from-rose-600 hover:to-pink-700 text-white font-medium rounded-xl shadow-lg shadow-rose-500/25 transition text-sm">
            <i class="fas fa-plus"></i>
            <span>Tulis Berita</span>
        </a>
    </div>

    {{-- Stats Cards (My Branch Only) --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-gradient-to-r from-rose-500 to-pink-600 rounded-xl p-4 text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-file-alt text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold">{{ $stats['total'] }}</p>
                    <p class="text-xs text-rose-100">Berita Saya</p>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl p-4 text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check-circle text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold">{{ $stats['published'] }}</p>
                    <p class="text-xs text-emerald-100">Published</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-edit text-white text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['draft'] }}</p>
                    <p class="text-xs text-gray-500">Draft</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-eye text-white text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['views']) }}</p>
                    <p class="text-xs text-gray-500">Total Views</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Search & Filter --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari judul berita..."
                       class="w-full pl-9 pr-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 rounded-lg text-sm">
            </div>
            <select wire:model.live="branch_id" class="px-4 py-2.5 bg-gray-50 border-transparent focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 rounded-lg text-sm">
                <option value="">Semua Cabang</option>
                @foreach($branches as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
            <select wire:model.live="status" class="px-4 py-2.5 bg-gray-50 border-transparent focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 rounded-lg text-sm">
                <option value="">Semua Status</option>
                <option value="draft">Draft</option>
                <option value="published">Published</option>
                <option value="archived">Archived</option>
            </select>
        </div>
    </div>

    {{-- News List --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        @if($newsList->count() > 0)
        <div class="divide-y divide-gray-50">
            @foreach($newsList as $item)
            @php $isOwn = $item->branch_id === $userBranchId; @endphp
            <div class="p-4 hover:bg-gray-50/50 transition {{ !$isOwn ? 'opacity-75' : '' }}">
                <div class="flex gap-4">
                    {{-- Image --}}
                    <div class="w-24 h-24 sm:w-32 sm:h-24 bg-gray-100 rounded-xl overflow-hidden flex-shrink-0">
                        @if($item->image_url)
                        <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                        @else
                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                            <i class="fas fa-image text-2xl"></i>
                        </div>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-900 truncate">{{ $item->title }}</h3>
                                <div class="flex flex-wrap items-center gap-2 mt-1">
                                    {{-- Branch Badge --}}
                                    <span class="px-2 py-0.5 text-xs rounded-full font-medium {{ $isOwn ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                                        <i class="fas fa-building mr-1"></i>{{ $item->branch?->name ?? 'Unknown' }}
                                    </span>
                                    @if($item->category)
                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded-full">{{ $item->category->name }}</span>
                                    @endif
                                    <span class="px-2 py-0.5 text-xs rounded-full font-medium
                                        @if($item->status === 'published') bg-emerald-100 text-emerald-700
                                        @elseif($item->status === 'draft') bg-amber-100 text-amber-700
                                        @else bg-gray-100 text-gray-600 @endif">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                    @if($item->is_featured)
                                    <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-xs rounded-full"><i class="fas fa-star mr-1"></i>Featured</span>
                                    @endif
                                </div>
                            </div>
                            {{-- Actions - Only for own branch --}}
                            @if($isOwn)
                            <div class="flex items-center gap-1">
                                @if($item->status === 'draft')
                                <button wire:click="publish({{ $item->id }})" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition" title="Publish">
                                    <i class="fas fa-check-circle"></i>
                                </button>
                                @endif
                                <a href="{{ route('staff.news.edit', $item->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button wire:click="confirmDelete({{ $item->id }})" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            @else
                            <span class="text-xs text-gray-400 italic">Cabang lain</span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ $item->excerpt ?: Str::limit(strip_tags($item->content), 120) }}</p>
                        <div class="flex items-center gap-4 mt-2 text-xs text-gray-400">
                            <span><i class="fas fa-user mr-1"></i>{{ $item->author?->name ?? 'Unknown' }}</span>
                            <span><i class="fas fa-eye mr-1"></i>{{ number_format($item->views) }} views</span>
                            <span><i class="fas fa-clock mr-1"></i>{{ $item->published_at?->format('d M Y H:i') ?? $item->created_at->format('d M Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if($newsList->hasPages())
        <div class="px-4 py-3 border-t border-gray-100 bg-gray-50">
            {{ $newsList->links() }}
        </div>
        @endif
        @else
        <div class="p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-newspaper text-gray-300 text-2xl"></i>
            </div>
            <p class="text-gray-500 font-medium">Belum ada berita</p>
            <p class="text-sm text-gray-400 mt-1">Mulai tulis berita pertama Anda</p>
            <a href="{{ route('staff.news.create') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-rose-500 hover:bg-rose-600 text-white text-sm font-medium rounded-lg transition">
                <i class="fas fa-plus"></i> Tulis Berita
            </a>
        </div>
        @endif
    </div>

    {{-- Delete Confirmation Modal --}}
    @if($deleteConfirmId)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="cancelDelete"></div>
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
                <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-trash text-red-500 text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Hapus Berita?</h3>
                <p class="text-gray-500 text-sm mb-6">Berita yang dihapus tidak dapat dikembalikan.</p>
                <div class="flex gap-3">
                    <button wire:click="cancelDelete" class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition">Batal</button>
                    <button wire:click="delete" class="flex-1 px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white font-medium rounded-xl transition">Hapus</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('notify', (data) => {
                alert((data[0].type === 'success' ? '✓ ' : '') + data[0].message);
            });
        });
    </script>
</div>
