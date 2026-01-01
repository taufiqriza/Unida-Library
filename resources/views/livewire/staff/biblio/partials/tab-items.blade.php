{{-- Tab Eksemplar --}}
@php
$config = ['gradient' => 'from-emerald-500 to-teal-600', 'bg' => 'emerald', 'icon' => 'fa-barcode'];
@endphp

@if($items->count() > 0)
<div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gradient-to-r {{ $config['gradient'] }} text-white">
            <tr>
                <th class="px-4 py-3 text-left w-12"><input type="checkbox" wire:model.live="selectAll" class="rounded border-white/30 text-white focus:ring-white/50 bg-white/20"></th>
                <th class="px-4 py-3 text-left font-medium">Barcode</th>
                <th class="px-4 py-3 text-left font-medium">Judul Buku</th>
                <th class="px-4 py-3 text-left font-medium w-44">No. Panggil</th>
                <th class="px-4 py-3 text-left font-medium w-28">Lokasi</th>
                <th class="px-4 py-3 text-left font-medium w-28">Status</th>
                <th class="px-4 py-3 text-center font-medium w-20">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @foreach($items as $item)
            <tr class="hover:bg-emerald-50/30 transition {{ in_array($item->id, $selectedItems) ? 'bg-emerald-50' : '' }}">
                <td class="px-4 py-3"><input type="checkbox" wire:model.live="selectedItems" value="{{ $item->id }}" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"></td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-gradient-to-br {{ $config['gradient'] }} rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                            <i class="fas fa-barcode text-white text-xs"></i>
                        </div>
                        <span class="font-mono text-sm font-medium">{{ $item->barcode }}</span>
                    </div>
                </td>
                <td class="px-4 py-3">
                    <p class="font-medium text-gray-900 truncate max-w-xs">{{ $item->book?->title ?? '-' }}</p>
                    <p class="text-xs text-gray-500">{{ $item->inventory_code }}</p>
                </td>
                <td class="px-4 py-3"><span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">{{ $item->call_number ?: $item->book?->call_number ?: '-' }}</span></td>
                <td class="px-4 py-3"><span class="text-xs text-gray-600">{{ $item->location?->name ?? '-' }}</span></td>
                <td class="px-4 py-3">
                    @if($item->itemStatus)
                    <span class="px-2.5 py-1 text-xs rounded-full font-medium {{ $item->itemStatus->no_loan ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">{{ $item->itemStatus->name }}</span>
                    @else
                    <span class="px-2.5 py-1 text-xs rounded-full bg-gray-100 text-gray-600">-</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-center">
                    <a href="{{ route('print.barcode', $item->id) }}" target="_blank" class="p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition inline-block" title="Cetak Barcode"><i class="fas fa-print"></i></a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="p-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
    <div class="text-sm text-gray-500">
        @if(count($selectedItems) > 0)
        <span class="inline-flex items-center px-3 py-1 bg-emerald-100 text-emerald-700 font-medium rounded-full">
            <i class="fas fa-check-circle mr-1.5"></i>{{ count($selectedItems) }} item dipilih
        </span>
        @endif
    </div>
    {{ $items->links() }}
</div>
@else
<div class="flex flex-col items-center justify-center py-16 text-center px-4">
    <div class="w-20 h-20 bg-gradient-to-br {{ $config['gradient'] }} rounded-2xl flex items-center justify-center mb-4 shadow-lg">
        <i class="fas {{ $config['icon'] }} text-3xl text-white"></i>
    </div>
    <h3 class="text-lg font-bold text-gray-900 mb-1">Belum Ada Eksemplar</h3>
    <p class="text-gray-500 text-sm">{{ $search ? 'Tidak ditemukan eksemplar yang sesuai.' : 'Tambahkan eksemplar melalui halaman edit buku.' }}</p>
</div>
@endif
