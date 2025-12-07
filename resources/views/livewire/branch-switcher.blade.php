<div class="flex items-center">
    <style>
        @media (min-width: 768px) {
            .branch-text { display: inline !important; }
        }
    </style>
    
    <x-filament::dropdown placement="bottom-end">
        <x-slot name="trigger">
            <button type="button" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 transition">
                <x-heroicon-o-building-library class="w-4 h-4" />
                <span style="display: none;" class="branch-text">{{ $currentBranch?->name ?? 'Semua Cabang' }}</span>
            </button>
        </x-slot>

        <x-filament::dropdown.list>
            <x-filament::dropdown.list.item 
                wire:click="switchBranch(null)"
                icon="heroicon-o-globe-alt"
                :color="!$currentBranchId ? 'primary' : 'gray'"
            >
                Semua Cabang
            </x-filament::dropdown.list.item>

            @foreach($branches as $branch)
                <x-filament::dropdown.list.item 
                    wire:click="switchBranch({{ $branch->id }})"
                    icon="heroicon-o-building-library"
                    :color="$currentBranchId === $branch->id ? 'primary' : 'gray'"
                >
                    {{ $branch->name }}
                </x-filament::dropdown.list.item>
            @endforeach
        </x-filament::dropdown.list>
    </x-filament::dropdown>
</div>
