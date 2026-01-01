<div>
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('staff.biblio.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 gap-1.5">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                    </svg>
                    <span>Kembali</span>
                </a>
                <div>
                    <h1 class="text-xl font-bold text-gray-900">{{ $book ? 'Edit Bibliografi' : 'Tambah Bibliografi Baru' }}</h1>
                </div>
            </div>
            
            <button type="button" wire:click="save" class="inline-flex items-center justify-center rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 gap-1.5">
                <svg wire:loading.remove wire:target="save" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                </svg>
                <svg wire:loading wire:target="save" class="w-5 h-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span wire:loading.remove wire:target="save">Simpan</span>
                <span wire:loading wire:target="save">Menyimpan...</span>
            </button>
        </div>

        {{-- Filament Form --}}
        <div class="fi-form-component">
            <form wire:submit="save">
                {{ $this->form }}
            </form>
        </div>
    </div>

    {{-- Modal rendered via Alpine's x-teleport to body --}}
    <template x-teleport="body">
        <div class="filament-modal-portal" style="position: relative; z-index: 9999;">
            <x-filament-actions::modals />
        </div>
    </template>
</div>
