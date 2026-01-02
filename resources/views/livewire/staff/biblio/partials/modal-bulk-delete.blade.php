{{-- Bulk Delete Confirmation Modal --}}
<template x-teleport="body">
    <div x-data="{ show: @entangle('showBulkDeleteModal').live }" x-show="show" x-cloak style="position: fixed; inset: 0; z-index: 99999;">
        <div x-show="show" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="position: fixed; inset: 0; background: rgba(0,0,0,0.6);" @click="$wire.set('showBulkDeleteModal', false)"></div>
        <div style="position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; padding: 1rem; pointer-events: none;">
            <div x-show="show" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center" style="pointer-events: auto;">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-trash-alt text-red-500 text-2xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Hapus {{ count($selectedItems) }} Buku?</h3>
                <p class="text-gray-500 text-sm mb-6">
                    Semua buku yang dipilih beserta eksemplarnya akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.
                </p>
                <div class="flex gap-3">
                    <button wire:click="$set('showBulkDeleteModal', false)" class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button wire:click="bulkDeleteBooks" class="flex-1 px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white font-medium rounded-xl transition">
                        <i class="fas fa-trash mr-1"></i> Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
