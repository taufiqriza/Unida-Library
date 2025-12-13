@if($data->count() > 0)
<div class="divide-y divide-gray-50">
    @foreach($data as $item)
    <div class="p-4 hover:bg-gray-50/50 transition">
        <div class="flex items-start gap-4">
            {{-- Cover --}}
            <div class="w-14 h-20 bg-gradient-to-br from-violet-100 to-purple-100 rounded-xl flex items-center justify-center flex-shrink-0 overflow-hidden shadow-sm">
                @if($item->cover_image)
                <img src="{{ asset('storage/'.$item->cover_image) }}" class="w-full h-full object-cover">
                @else
                <i class="fas fa-book text-violet-400 text-xl"></i>
                @endif
            </div>

            {{-- Content --}}
            <div class="flex-1 min-w-0">
                <h3 class="font-semibold text-gray-900 line-clamp-1">{{ $item->title }}</h3>
                
                {{-- Author/SOR --}}
                <p class="text-sm text-gray-600 mt-1">
                    <i class="fas fa-user-pen text-gray-400 mr-1"></i>{{ $item->sor ?? 'Unknown Author' }}
                </p>

                {{-- Meta Info --}}
                <div class="flex flex-wrap items-center gap-2 mt-2 text-xs">
                    {{-- Year --}}
                    <span class="px-2 py-0.5 bg-violet-100 text-violet-700 rounded-full font-medium">
                        {{ $item->publish_year ?? '-' }}
                    </span>
                    
                    {{-- Status --}}
                    <span class="px-2 py-0.5 rounded-full font-medium {{ $item->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                        <i class="fas {{ $item->is_active ? 'fa-check-circle' : 'fa-circle-xmark' }} mr-1"></i>
                        {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>

                    {{-- Stats --}}
                    <span class="text-gray-400">
                        <i class="fas fa-eye mr-1"></i>{{ number_format($item->view_count ?? 0) }}
                    </span>
                    <span class="text-gray-400">
                        <i class="fas fa-download mr-1"></i>{{ number_format($item->download_count ?? 0) }}
                    </span>
                </div>

                {{-- Branch & User Info --}}
                <div class="flex items-center gap-3 mt-2 text-xs text-gray-400">
                    @if($item->branch)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 text-blue-600 rounded-md">
                        <i class="fas fa-building"></i>{{ $item->branch->name }}
                    </span>
                    @endif
                    @if($item->user)
                    <span>
                        <i class="fas fa-user-circle mr-1"></i>{{ $item->user->name }}
                    </span>
                    @endif
                    <span class="text-gray-300">•</span>
                    <span>{{ $item->created_at->format('d M Y') }}</span>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-1 flex-shrink-0">
                @if($item->file_path)
                <a href="{{ asset('storage/'.$item->file_path) }}" target="_blank" class="p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition" title="Lihat File">
                    <i class="fas fa-file-pdf"></i>
                </a>
                @endif
                <a href="{{ route('staff.elibrary.ebook.edit', $item->id) }}" class="p-2 text-gray-400 hover:text-violet-600 hover:bg-violet-50 rounded-lg transition" title="Edit">
                    <i class="fas fa-pen-to-square"></i>
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="p-12 text-center">
    <div class="w-16 h-16 bg-violet-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
        <i class="fas fa-book-open text-violet-300 text-2xl"></i>
    </div>
    <p class="text-gray-500">Belum ada e-book</p>
    <a href="{{ route('staff.elibrary.ebook.create') }}" class="inline-flex items-center gap-2 mt-3 px-4 py-2 bg-violet-100 text-violet-700 rounded-lg text-sm font-medium hover:bg-violet-200 transition">
        <i class="fas fa-plus"></i> Tambah E-Book
    </a>
</div>
@endif
