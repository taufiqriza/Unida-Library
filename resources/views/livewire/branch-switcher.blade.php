<div class="flex items-center gap-2">
    <x-filament::dropdown>
        <x-slot name="trigger">
            <x-filament::button color="gray" size="sm" icon="heroicon-o-building-library">
                {{ $currentBranch?->name ?? 'Semua Cabang' }}
            </x-filament::button>
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
