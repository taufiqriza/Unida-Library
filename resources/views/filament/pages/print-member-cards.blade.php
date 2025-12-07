<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            Cetak Kartu Anggota
        </x-slot>
        <x-slot name="description">
            Pilih anggota yang akan dicetak kartunya, lalu klik "Cetak Terpilih".
        </x-slot>
        
        {{ $this->table }}
    </x-filament::section>
</x-filament-panels::page>
