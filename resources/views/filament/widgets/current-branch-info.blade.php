<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center gap-4">
            <x-heroicon-o-building-library class="w-10 h-10 text-primary-500" />
            <div>
                @if($this->getBranch())
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                        {{ $this->getBranch()->name }}
                    </h2>
                    <p class="text-sm text-gray-500">
                        {{ $this->getBranch()->address ?? $this->getBranch()->city ?? 'Cabang Perpustakaan' }}
                    </p>
                @else
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                        Semua Cabang
                    </h2>
                    <p class="text-sm text-gray-500">
                        Menampilkan data dari seluruh cabang perpustakaan
                    </p>
                @endif
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
