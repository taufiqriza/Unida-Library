{{-- Print Barcode Modal --}}
<template x-teleport="body">
    <div x-data="{ show: @entangle('showPrintModal').live }" x-show="show" x-cloak style="position: fixed; inset: 0; z-index: 99999;" @keydown.escape.window="$wire.closePrintModal()">
        <div x-show="show" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="position: fixed; inset: 0; background: rgba(0,0,0,0.6);" @click="$wire.closePrintModal()"></div>
        <div style="position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; padding: 1rem; pointer-events: none;">
            <div x-show="show" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden" style="pointer-events: auto;">
                <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-white">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center"><i class="fas fa-print text-emerald-600 text-xl"></i></div>
                        <div><h3 class="text-lg font-bold text-gray-900">Cetak Barcode</h3><p class="text-sm text-gray-500">{{ count($selectedItems) }} label akan dicetak</p></div>
                    </div>
                </div>
                <div class="p-5 space-y-4">
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <h4 class="text-sm font-semibold text-blue-800 flex items-center gap-2 mb-2"><i class="fas fa-info-circle"></i> Informasi Cetak</h4>
                        <ul class="text-xs text-blue-700 space-y-1">
                            <li><i class="fas fa-check mr-1.5 text-blue-500"></i> Ukuran label: <strong>9 × 3 cm</strong></li>
                            <li><i class="fas fa-check mr-1.5 text-blue-500"></i> Kertas: <strong>A4</strong></li>
                            <li><i class="fas fa-check mr-1.5 text-blue-500"></i> Hasil: <strong>~18 label</strong> per halaman</li>
                        </ul>
                    </div>
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                        <h4 class="text-sm font-semibold text-amber-800 flex items-center gap-2 mb-2"><i class="fas fa-lightbulb"></i> Tips</h4>
                        <ul class="text-xs text-amber-700 space-y-1">
                            <li>• Gunakan kertas stiker untuk hasil terbaik</li>
                            <li>• Atur printer ke "Actual Size" / "100%"</li>
                        </ul>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-2">Item yang akan dicetak:</p>
                        <div class="max-h-32 overflow-y-auto bg-gray-50 rounded-xl p-3 space-y-1.5">
                            @foreach($this->selectedItemsData->take(5) as $item)
                            <div class="flex items-center gap-2 text-xs">
                                <span class="font-mono bg-white px-2 py-0.5 rounded border">{{ $item->barcode }}</span>
                                <span class="text-gray-600 truncate">{{ Str::limit($item->book?->title, 40) }}</span>
                            </div>
                            @endforeach
                            @if(count($selectedItems) > 5)<p class="text-xs text-gray-400 italic">... dan {{ count($selectedItems) - 5 }} lainnya</p>@endif
                        </div>
                    </div>
                </div>
                <div class="p-5 border-t border-gray-100 bg-gray-50 flex items-center justify-end gap-3">
                    <button wire:click="closePrintModal" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition"><i class="fas fa-times mr-1"></i> Batal</button>
                    <button wire:click="confirmPrint" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl transition"><i class="fas fa-print mr-1"></i> Cetak</button>
                </div>
            </div>
        </div>
    </div>
</template>
