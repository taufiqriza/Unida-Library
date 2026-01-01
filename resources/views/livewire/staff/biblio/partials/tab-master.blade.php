{{-- Tab Master Data (Authors, Publishers, Subjects, Locations, Places, GMD) --}}
@php
$tabConfig = [
    'authors' => ['icon' => 'fa-user-edit', 'label' => 'Penulis', 'gradient' => 'from-purple-500 to-violet-600', 'bg' => 'purple', 'countLabel' => 'buku'],
    'publishers' => ['icon' => 'fa-building', 'label' => 'Penerbit', 'gradient' => 'from-indigo-500 to-blue-600', 'bg' => 'indigo', 'countLabel' => 'buku'],
    'subjects' => ['icon' => 'fa-tags', 'label' => 'Subjek', 'gradient' => 'from-amber-500 to-orange-600', 'bg' => 'amber', 'countLabel' => 'buku'],
    'locations' => ['icon' => 'fa-map-marker-alt', 'label' => 'Lokasi Rak', 'gradient' => 'from-rose-500 to-pink-600', 'bg' => 'rose', 'countLabel' => 'item'],
    'places' => ['icon' => 'fa-city', 'label' => 'Kota Terbit', 'gradient' => 'from-cyan-500 to-teal-600', 'bg' => 'cyan', 'countLabel' => 'buku'],
    'gmd' => ['icon' => 'fa-compact-disc', 'label' => 'Jenis Media', 'gradient' => 'from-violet-500 to-purple-600', 'bg' => 'violet', 'countLabel' => 'buku'],
];
$config = $tabConfig[$activeTab] ?? $tabConfig['authors'];
@endphp

@if($masterData->count() > 0)
<div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gradient-to-r {{ $config['gradient'] }} text-white">
            <tr>
                <th class="px-4 py-3 text-left font-medium w-12">#</th>
                <th class="px-4 py-3 text-left font-medium">Nama {{ $config['label'] }}</th>
                <th class="px-4 py-3 text-center font-medium w-32">Jumlah</th>
                <th class="px-4 py-3 text-center font-medium w-28">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @foreach($masterData as $index => $item)
            <tr class="hover:bg-{{ $config['bg'] }}-50/30 transition">
                <td class="px-4 py-3 text-gray-400 text-sm">{{ $masterData->firstItem() + $index }}</td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-gradient-to-br {{ $config['gradient'] }} rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                            <i class="fas {{ $config['icon'] }} text-white text-sm"></i>
                        </div>
                        <span class="font-medium text-gray-900">{{ $item->name }}</span>
                    </div>
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="inline-flex items-center px-2.5 py-1 bg-{{ $config['bg'] }}-100 text-{{ $config['bg'] }}-700 text-xs font-bold rounded-full">
                        {{ number_format($activeTab === 'locations' ? $item->items_count : $item->books_count) }} {{ $config['countLabel'] }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center justify-center gap-1">
                        <button wire:click="openModal('edit', {{ $item->id }})" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button wire:click="confirmDelete({{ $item->id }}, '{{ $activeTab === 'gmd' ? 'gmd' : rtrim($activeTab, 's') }}')" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="p-4 border-t border-gray-100 bg-gray-50">{{ $masterData->links() }}</div>
@else
<div class="flex flex-col items-center justify-center py-16 text-center px-4">
    <div class="w-20 h-20 bg-gradient-to-br {{ $config['gradient'] }} rounded-2xl flex items-center justify-center mb-4 shadow-lg">
        <i class="fas {{ $config['icon'] }} text-3xl text-white"></i>
    </div>
    <h3 class="text-lg font-bold text-gray-900 mb-1">Belum Ada Data {{ $config['label'] }}</h3>
    <p class="text-gray-500 text-sm mb-4">{{ $search ? 'Tidak ditemukan data yang sesuai.' : 'Mulai dengan menambahkan data pertama.' }}</p>
    @if(!$search)
    <button wire:click="openModal('create')" class="px-5 py-2.5 bg-gradient-to-r {{ $config['gradient'] }} text-white text-sm font-medium rounded-xl hover:shadow-lg transition shadow-md">
        <i class="fas fa-plus mr-2"></i>Tambah {{ $config['label'] }}
    </button>
    @endif
</div>
@endif
