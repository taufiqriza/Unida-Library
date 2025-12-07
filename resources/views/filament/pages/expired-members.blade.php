<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            Anggota Kadaluarsa
        </x-slot>
        <x-slot name="description">
            Daftar anggota yang masa keanggotaannya sudah berakhir dan perlu diperpanjang.
        </x-slot>
        
        {{ $this->table }}
    </x-filament::section>
</x-filament-panels::page>
