@if($data->count() > 0)
<div class="divide-y divide-gray-50">
    @foreach($data as $item)
    <div class="p-4 hover:bg-gray-50/50 transition">
        <div class="flex items-start gap-4">
            {{-- Icon --}}
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0
                @if($item->status === 'submitted') bg-amber-100 text-amber-600
                @elseif($item->status === 'approved') bg-emerald-100 text-emerald-600
                @elseif($item->status === 'rejected') bg-red-100 text-red-600
                @elseif($item->status === 'published') bg-blue-100 text-blue-600
                @elseif($item->status === 'revision_required') bg-orange-100 text-orange-600
                @else bg-gray-100 text-gray-500 @endif">
                <i class="fas 
                    @if($item->status === 'submitted') fa-clock
                    @elseif($item->status === 'approved') fa-check-circle
                    @elseif($item->status === 'rejected') fa-times-circle
                    @elseif($item->status === 'published') fa-globe
                    @elseif($item->status === 'revision_required') fa-edit
                    @else fa-file-alt @endif text-lg"></i>
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

                {{-- Academic Info --}}
                <div class="flex flex-wrap items-center gap-2 mt-2 text-xs">
                    {{-- Type Badge --}}
                    <span class="px-2 py-0.5 bg-violet-100 text-violet-700 rounded-full font-medium">
                        {{ ucfirst($item->type) }}
                    </span>
                    
                    {{-- Status Badge --}}
                    <span class="px-2 py-0.5 rounded-full font-medium
                        @if($item->status === 'submitted') bg-amber-100 text-amber-700
                        @elseif($item->status === 'approved') bg-emerald-100 text-emerald-700
                        @elseif($item->status === 'rejected') bg-red-100 text-red-700
                        @elseif($item->status === 'published') bg-blue-100 text-blue-700
                        @elseif($item->status === 'revision_required') bg-orange-100 text-orange-700
                        @else bg-gray-100 text-gray-600 @endif">
                        {{ $item->status_label }}
                    </span>
                    
                    {{-- Clearance Badge --}}
                    @if($item->clearanceLetter && $item->clearanceLetter->status === 'approved')
                    <span class="px-2 py-0.5 bg-teal-100 text-teal-700 rounded-full font-medium flex items-center gap-1">
                        <i class="fas fa-file-certificate text-[10px]"></i> Bebas Pustaka
                    </span>
                    @endif

                    {{-- Faculty & Department --}}
                    @if($item->department)
                    <span class="text-gray-400">
                        <i class="fas fa-university mr-1"></i>{{ $item->department->faculty?->name ?? 'Fakultas' }}
                    </span>
                    <span class="text-gray-400">
                        <i class="fas fa-graduation-cap mr-1"></i>{{ $item->department->name }}
                    </span>
                    @endif

                    {{-- Year --}}
                    <span class="text-gray-400">
                        <i class="fas fa-calendar mr-1"></i>{{ $item->year }}
                    </span>
                </div>

                {{-- Member Info --}}
                @if($item->member)
                <div class="flex items-center gap-2 mt-2 text-xs text-gray-400">
                    <i class="fas fa-user"></i>
                    <span>Diajukan oleh: {{ $item->member->name }} ({{ $item->member->member_id }})</span>
                    <span class="text-gray-300">•</span>
                    <span>{{ $item->created_at->format('d M Y H:i') }}</span>
                </div>
                @endif
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-1 flex-shrink-0">
                <button wire:click="viewDetail({{ $item->id }}, 'submission')" class="p-2 text-gray-400 hover:text-violet-600 hover:bg-violet-50 rounded-lg transition" title="Lihat Detail">
                    <i class="fas fa-eye"></i>
                </button>
                @if($isMainBranch && $item->status === 'submitted')
                <button wire:click="viewDetail({{ $item->id }}, 'submission')" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition" title="Review">
                    <i class="fas fa-clipboard-check"></i>
                </button>
                @endif
                @if($isMainBranch && $item->status === 'approved')
                <button wire:click="quickPublish({{ $item->id }})" wire:confirm="Publikasikan '{{ Str::limit($item->title, 50) }}' ke E-Thesis?" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Publikasikan">
                    <i class="fas fa-globe"></i>
                </button>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="p-12 text-center">
    <div class="w-16 h-16 bg-amber-100 rounded-2xl flex items-center justify-center mx-auto mb-4"><i class="fas fa-upload text-amber-300 text-2xl"></i></div>
    <p class="text-gray-500">Belum ada unggah mandiri</p>
</div>
@endif
