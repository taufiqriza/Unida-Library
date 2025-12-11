{{-- Quick Actions Popup Component --}}
@php
$quickActions = [
    ['icon' => 'fa-qrcode', 'label' => 'Scan Pinjam', 'route' => '#', 'gradient' => 'from-blue-500 to-indigo-600', 'color' => 'blue'],
    ['icon' => 'fa-rotate-left', 'label' => 'Scan Kembali', 'route' => '#', 'gradient' => 'from-emerald-500 to-teal-600', 'color' => 'emerald'],
    ['icon' => 'fa-user-plus', 'label' => 'Member Baru', 'route' => '#', 'gradient' => 'from-purple-500 to-pink-600', 'color' => 'purple'],
    ['icon' => 'fa-search', 'label' => 'Cari Buku', 'route' => '#', 'gradient' => 'from-amber-400 to-orange-500', 'color' => 'amber'],
    ['icon' => 'fa-id-card', 'label' => 'Cetak Kartu', 'route' => '#', 'gradient' => 'from-cyan-500 to-blue-600', 'color' => 'cyan'],
    ['icon' => 'fa-receipt', 'label' => 'Bayar Denda', 'route' => '#', 'gradient' => 'from-rose-500 to-red-600', 'color' => 'rose'],
];
@endphp

<div class="relative" x-data="{ open: false }">
    {{-- Trigger Button --}}
    <button @click="open = !open"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-amber-400 to-orange-500 text-white font-semibold text-sm shadow hover:shadow-lg hover:-translate-y-0.5 transition-all">
        <i class="fas fa-bolt"></i>
        <span>Aksi Cepat</span>
        <i class="fas fa-chevron-down text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
    </button>

    {{-- Popup --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
         @click.away="open = false"
         class="absolute right-0 top-full mt-2 w-80 bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden z-50"
         x-cloak>
        
        {{-- Header --}}
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2 text-white">
                <i class="fas fa-bolt"></i>
                <span class="font-semibold text-sm">Aksi Cepat</span>
            </div>
            <button @click="open = false" class="w-6 h-6 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center text-white transition">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>

        {{-- Actions Grid --}}
        <div class="p-3 grid grid-cols-3 gap-2">
            @foreach($quickActions as $action)
            <a href="{{ $action['route'] }}" 
               @click="open = false"
               class="group flex flex-col items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-{{ $action['color'] }}-50 border border-transparent hover:border-{{ $action['color'] }}-200 transition-all">
                <div class="w-10 h-10 bg-gradient-to-br {{ $action['gradient'] }} rounded-xl flex items-center justify-center text-white shadow-sm group-hover:scale-110 transition-transform">
                    <i class="fas {{ $action['icon'] }}"></i>
                </div>
                <span class="text-[10px] font-semibold text-gray-600 text-center leading-tight">{{ $action['label'] }}</span>
            </a>
            @endforeach
        </div>

        {{-- Footer --}}
        <div class="px-4 py-2 bg-gray-50 border-t border-gray-100">
            <p class="text-[10px] text-gray-400 text-center">Tekan <kbd class="px-1 py-0.5 bg-gray-200 rounded text-[9px]">Esc</kbd> untuk menutup</p>
        </div>
    </div>
</div>
