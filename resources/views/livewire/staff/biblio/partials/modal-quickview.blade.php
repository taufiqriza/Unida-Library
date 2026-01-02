{{-- Quick View Modal --}}
<template x-teleport="body">
    <div x-data="{ show: @entangle('quickViewId').live }" x-show="show" x-cloak style="position: fixed; inset: 0; z-index: 99999;" @keydown.escape.window="$wire.closeQuickView()">
        <div x-show="show" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="position: fixed; inset: 0; background: rgba(0,0,0,0.6);" @click="$wire.closeQuickView()"></div>
        <div style="position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; padding: 1rem; pointer-events: none;">
            <div x-show="show" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden" style="pointer-events: auto;">
                @if($this->quickViewBook)
                @php $book = $this->quickViewBook; @endphp
                <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-blue-50 to-white">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2"><i class="fas fa-book text-blue-500"></i> Detail Bibliografi</h3>
                    <button @click="$wire.closeQuickView()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
                </div>
                <div class="p-5 overflow-y-auto" style="max-height: calc(90vh - 140px);">
                    <div class="flex gap-5">
                        <div class="w-32 flex-shrink-0">
                            <div class="aspect-[2/3] bg-gray-100 rounded-xl overflow-hidden shadow-sm">
                                @if($book->image)<img src="{{ asset('storage/' . $book->image) }}" class="w-full h-full object-cover" alt="">
                                @else<div class="w-full h-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center"><i class="fas fa-book-open text-white/80 text-2xl"></i></div>@endif
                            </div>
                            <div class="mt-2 text-center"><span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-lg"><i class="fas fa-copy mr-1"></i> {{ $book->items->count() }} eks</span></div>
                        </div>
                        <div class="flex-1 space-y-3">
                            <div>
                                <h4 class="text-lg font-bold text-gray-900 leading-tight">{{ $book->title }}</h4>
                                @if($book->authors->count())<p class="text-sm text-gray-600 mt-1"><i class="fas fa-user text-gray-400 mr-1"></i>{{ $book->authors->pluck('name')->implode(', ') }}</p>@endif
                            </div>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div class="flex items-center gap-2"><span class="text-gray-400 w-20">ISBN</span><span class="font-medium text-gray-900">{{ $book->isbn ?: '-' }}</span></div>
                                <div class="flex items-center gap-2"><span class="text-gray-400 w-20">Tahun</span><span class="font-medium text-gray-900">{{ $book->publish_year ?: '-' }}</span></div>
                                <div class="flex items-center gap-2"><span class="text-gray-400 w-20">Penerbit</span><span class="font-medium text-gray-900">{{ $book->publisher?->name ?: '-' }}</span></div>
                                <div class="flex items-center gap-2"><span class="text-gray-400 w-20">Tempat</span><span class="font-medium text-gray-900">{{ $book->place?->name ?: '-' }}</span></div>
                                <div class="flex items-center gap-2"><span class="text-gray-400 w-20">Klasifikasi</span><span class="font-mono font-medium text-purple-600">{{ $book->classification ?: '-' }}</span></div>
                                <div class="flex items-center gap-2"><span class="text-gray-400 w-20">No. Panggil</span><span class="font-mono font-medium text-blue-600">{{ $book->call_number ?: '-' }}</span></div>
                            </div>
                            @if($book->subjects->count())<div class="flex flex-wrap gap-1.5 pt-2">@foreach($book->subjects as $subject)<span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-xs rounded-lg">{{ $subject->name }}</span>@endforeach</div>@endif
                            @if($book->abstract)<div class="pt-2 border-t border-gray-100"><p class="text-xs text-gray-500 mb-1">Abstrak</p><p class="text-sm text-gray-700 line-clamp-3">{{ $book->abstract }}</p></div>@endif
                        </div>
                    </div>
                </div>
                <div class="p-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
                    <a href="{{ route('staff.biblio.show', $book->id) }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium"><i class="fas fa-external-link-alt mr-1"></i> Lihat Detail Lengkap</a>
                    <a href="{{ route('staff.biblio.edit', $book->id) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition"><i class="fas fa-edit mr-1"></i> Edit</a>
                </div>
                @else
                <div class="p-8 text-center"><i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i></div>
                @endif
            </div>
        </div>
    </div>
</template>
