<div class="flex items-center gap-2">
    <style>
        @media (min-width: 768px) {
            .qa-text { display: inline !important; }
        }
    </style>
    
    {{-- Sirkulasi Button --}}
    <a href="{{ route('filament.admin.pages.circulation') }}" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 transition">
        <x-heroicon-o-arrow-path class="w-4 h-4" />
        <span style="display: none;" class="qa-text">Sirkulasi</span>
    </a>

    {{-- Quick Actions Dropdown --}}
    <x-filament::dropdown placement="bottom-end">
        <x-slot name="trigger">
            <button type="button" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 transition">
                <x-heroicon-o-bolt class="w-4 h-4" />
                <span style="display: none;" class="qa-text">Aksi</span>
            </button>
        </x-slot>

        <x-filament::dropdown.list>
            <x-filament::dropdown.list.item href="{{ route('filament.admin.pages.quick-return') }}" icon="heroicon-o-arrow-uturn-left" tag="a">
                Pengembalian Cepat
            </x-filament::dropdown.list.item>
            <x-filament::dropdown.list.item href="{{ route('filament.admin.resources.members.create') }}" icon="heroicon-o-user-plus" tag="a">
                Anggota Baru
            </x-filament::dropdown.list.item>
            <x-filament::dropdown.list.item href="{{ route('filament.admin.resources.books.create') }}" icon="heroicon-o-plus-circle" tag="a">
                Katalog Baru
            </x-filament::dropdown.list.item>
            <x-filament::dropdown.list.item href="{{ route('filament.admin.resources.items.create') }}" icon="heroicon-o-archive-box-arrow-down" tag="a">
                Eksemplar Baru
            </x-filament::dropdown.list.item>
            <x-filament::dropdown.list.item href="{{ route('filament.admin.pages.print-barcodes') }}" icon="heroicon-o-qr-code" tag="a">
                Cetak Barcode
            </x-filament::dropdown.list.item>
        </x-filament::dropdown.list>
    </x-filament::dropdown>
</div>
