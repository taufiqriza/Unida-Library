@if($data->count() > 0)
<div class="divide-y divide-gray-50">
    @foreach($data as $item)
    <div class="p-4 hover:bg-gray-50/50 transition">
        <div class="flex items-start gap-4">
            {{-- Icon --}}
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-gradient-to-br from-blue-100 to-cyan-100">
                <i class="fas fa-graduation-cap text-blue-500 text-lg"></i>
            </div>

            {{-- Content --}}
            <div class="flex-1 min-w-0">
                <h3 class="font-semibold text-gray-900 line-clamp-1">{{ $item->title }}</h3>
                
                {{-- Author Info --}}
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-sm font-medium text-gray-700">{{ $item->author }}</span>
                    <span class="text-gray-300">•</span>
                    <span class="text-sm text-gray-500 font-mono">{{ $item->nim }}</span>
                </div>

                {{-- Meta Info --}}
                <div class="flex flex-wrap items-center gap-2 mt-2 text-xs">
                    {{-- Type Badge --}}
                    <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full font-medium">
                        {{ $item->getTypeLabel() }}
                    </span>
                    
                    {{-- Year --}}
                    <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full">
                        {{ $item->year }}
                    </span>

                    {{-- Department --}}
                    @if($item->department)
                    <span class="text-gray-400">
                        <i class="fas fa-graduation-cap mr-1"></i>{{ $item->department->name }}
                    </span>
                    @endif

                    {{-- Views --}}
                    <span class="text-gray-400">
                        <i class="fas fa-eye mr-1"></i>{{ number_format($item->views ?? 0) }}
                    </span>

                    {{-- Visibility --}}
                    <span class="px-2 py-0.5 rounded-full {{ $item->is_public ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                        <i class="fas {{ $item->is_public ? 'fa-globe' : 'fa-lock' }} mr-1"></i>
                        {{ $item->is_public ? 'Publik' : 'Terbatas' }}
                    </span>
                </div>

                {{-- Branch & User Info --}}
                <div class="flex items-center gap-3 mt-2 text-xs text-gray-400">
                    @if($item->branch)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-cyan-50 text-cyan-600 rounded-md">
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
                <a href="{{ route('staff.elibrary.ethesis.edit', $item->id) }}" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                    <i class="fas fa-pen-to-square"></i>
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="p-12 text-center">
    <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
        <i class="fas fa-graduation-cap text-blue-300 text-2xl"></i>
    </div>
    <p class="text-gray-500">Belum ada e-thesis</p>
    <a href="{{ route('staff.elibrary.ethesis.create') }}" class="inline-flex items-center gap-2 mt-3 px-4 py-2 bg-blue-100 text-blue-700 rounded-lg text-sm font-medium hover:bg-blue-200 transition">
        <i class="fas fa-plus"></i> Tambah E-Thesis
    </a>
</div>
@endif
