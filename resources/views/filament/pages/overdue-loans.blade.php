<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2 text-danger-600">
                <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
                Daftar Keterlambatan
            </div>
        </x-slot>
        <x-slot name="description">
            Peminjaman yang sudah melewati tanggal jatuh tempo dan perlu segera dikembalikan.
        </x-slot>
        
        {{ $this->table }}
    </x-filament::section>
</x-filament-panels::page>
