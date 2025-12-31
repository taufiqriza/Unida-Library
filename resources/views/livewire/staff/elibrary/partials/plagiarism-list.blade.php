@if($data->count() > 0)
{{-- DEBUG --}}
@foreach($data as $item)
<div class="p-2 bg-yellow-100 text-xs">DEBUG: ID={{ $item->id }} member_id={{ $item->member_id }} loaded={{ $item->relationLoaded('member') ? 'Y' : 'N' }} name={{ $item->member?->name ?? 'NULL' }}</div>
@endforeach
{{-- END DEBUG --}}
<div class="divide-y divide-gray-50">
    @foreach($data as $item)
    <div class="p-4 hover:bg-gray-50/50 transition">
        <div class="flex items-start gap-4">
            {{-- Similarity Score Circle --}}
            <div class="w-14 h-14 rounded-xl flex flex-col items-center justify-center flex-shrink-0
                @if($item->status === 'completed')
                    @if($item->similarity_score <= 15) bg-emerald-100
                    @elseif($item->similarity_score <= 25) bg-amber-100
                    @else bg-red-100 @endif
                @elseif($item->status === 'processing') bg-blue-100
                @elseif($item->status === 'failed') bg-red-100
                @else bg-gray-100 @endif">
                @if($item->status === 'completed' && $item->similarity_score !== null)
                <span class="text-lg font-bold
                    @if($item->similarity_score <= 15) text-emerald-600
                    @elseif($item->similarity_score <= 25) text-amber-600
                    @else text-red-600 @endif">{{ round($item->similarity_score) }}%</span>
                <span class="text-[9px] text-gray-500">similarity</span>
                @elseif($item->status === 'processing')
                <i class="fas fa-spinner fa-spin text-blue-500"></i>
                @elseif($item->status === 'failed')
                <i class="fas fa-times text-red-500"></i>
                @else
                <i class="fas fa-clock text-gray-400"></i>
                @endif
            </div>

            {{-- Content --}}
            <div class="flex-1 min-w-0">
                <h3 class="font-semibold text-gray-900 line-clamp-1">{{ $item->document_title }}</h3>
                
                {{-- Member Info --}}
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-sm font-medium text-gray-700">{{ $item->member?->name ?? '-' }}</span>
                    <span class="text-gray-300">•</span>
                    <span class="text-sm text-gray-500 font-mono">{{ $item->member?->member_id ?? '-' }}</span>
                </div>

                {{-- Status & Info --}}
                <div class="flex flex-wrap items-center gap-2 mt-2 text-xs">
                    {{-- Status Badge --}}
                    <span class="px-2 py-0.5 rounded-full font-medium
                        @if($item->status === 'completed') bg-emerald-100 text-emerald-700
                        @elseif($item->status === 'processing') bg-blue-100 text-blue-700
                        @elseif($item->status === 'failed') bg-red-100 text-red-700
                        @else bg-gray-100 text-gray-600 @endif">
                        <i class="fas 
                            @if($item->status === 'completed') fa-check
                            @elseif($item->status === 'processing') fa-spinner fa-spin
                            @elseif($item->status === 'failed') fa-times
                            @else fa-clock @endif mr-1"></i>
                        {{ $item->status_label }}
                    </span>

                    {{-- Similarity Level --}}
                    @if($item->status === 'completed' && $item->similarity_score !== null)
                    <span class="px-2 py-0.5 rounded-full font-medium
                        @if($item->similarity_score <= 15) bg-emerald-100 text-emerald-700
                        @elseif($item->similarity_score <= 25) bg-amber-100 text-amber-700
                        @else bg-red-100 text-red-700 @endif">
                        {{ $item->similarity_label }}
                    </span>
                    @endif

                    {{-- Provider --}}
                    <span class="text-gray-400">
                        <i class="fas fa-server mr-1"></i>{{ $item->provider_label }}
                    </span>

                    {{-- External Badge --}}
                    @if($item->check_type === 'external')
                    <span class="px-2 py-0.5 rounded-full font-medium bg-violet-100 text-violet-700">
                        <i class="fas fa-upload mr-1"></i>Eksternal
                    </span>
                    @endif

                    {{-- File Info --}}
                    <span class="text-gray-400">
                        <i class="fas fa-file mr-1"></i>{{ $item->file_size_formatted }}
                    </span>

                    {{-- Date --}}
                    <span class="text-gray-400">
                        <i class="fas fa-calendar mr-1"></i>{{ $item->created_at->format('d M Y H:i') }}
                    </span>
                </div>

                {{-- Processing Time --}}
                @if($item->processing_time)
                <div class="text-xs text-gray-400 mt-1">
                    <i class="fas fa-stopwatch mr-1"></i>Waktu proses: {{ $item->processing_time }}
                </div>
                @endif
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-1 flex-shrink-0">
                <button wire:click="viewDetail({{ $item->id }}, 'plagiarism')" class="p-2 text-gray-400 hover:text-violet-600 hover:bg-violet-50 rounded-lg transition" title="Lihat Detail">
                    <i class="fas fa-eye"></i>
                </button>
                @if($item->hasCertificate())
                <a href="{{ route('plagiarism.certificate.download', $item) }}" target="_blank" class="p-2 text-violet-600 hover:bg-violet-50 rounded-lg transition" title="Download Sertifikat">
                    <i class="fas fa-certificate"></i>
                </a>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="p-12 text-center">
    <div class="w-16 h-16 bg-rose-100 rounded-2xl flex items-center justify-center mx-auto mb-4"><i class="fas fa-shield-halved text-rose-300 text-2xl"></i></div>
    <p class="text-gray-500">Belum ada cek plagiasi</p>
</div>
@endif
