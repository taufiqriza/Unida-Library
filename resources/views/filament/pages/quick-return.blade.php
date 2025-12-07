<x-filament-panels::page>
    @php
        $todayReturns = \App\Models\Loan::whereDate('return_date', today())->where('is_returned', true)->count();
        $todayLoans = \App\Models\Loan::whereDate('loan_date', today())->count();
        $todayFines = \App\Models\Fine::whereDate('created_at', today())->sum('amount');
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Scanner --}}
        <div class="space-y-4">
            <x-filament::section>
                <x-slot name="heading">Scan Barcode</x-slot>
                <x-slot name="description">Scan barcode buku untuk pengembalian</x-slot>

                <div class="space-y-3">
                    <x-filament::input.wrapper label="Barcode Item">
                        <x-filament::input
                            wire:model="itemBarcode"
                            wire:keydown.enter="returnItem"
                            placeholder="Scan barcode..."
                            autofocus
                        />
                    </x-filament::input.wrapper>
                    
                    <x-filament::button wire:click="returnItem" class="w-full">
                        Kembalikan
                    </x-filament::button>
                </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">Statistik Hari Ini</x-slot>
                
                <div class="grid grid-cols-3 gap-2 text-center">
                    <div class="p-3 bg-success-50 dark:bg-success-900/20 rounded-lg">
                        <div class="text-xl font-bold text-success-600">{{ $todayReturns }}</div>
                        <div class="text-xs text-gray-500">Kembali</div>
                    </div>
                    <div class="p-3 bg-primary-50 dark:bg-primary-900/20 rounded-lg">
                        <div class="text-xl font-bold text-primary-600">{{ $todayLoans }}</div>
                        <div class="text-xs text-gray-500">Pinjam</div>
                    </div>
                    <div class="p-3 bg-warning-50 dark:bg-warning-900/20 rounded-lg">
                        <div class="text-xl font-bold text-warning-600">{{ number_format($todayFines/1000, 0) }}K</div>
                        <div class="text-xs text-gray-500">Denda</div>
                    </div>
                </div>
            </x-filament::section>
        </div>

        {{-- Right: History --}}
        <div class="lg:col-span-2">
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center justify-between">
                        <span>Riwayat Pengembalian ({{ count($returnedItems) }})</span>
                        @if(count($returnedItems) > 0)
                            <x-filament::button size="xs" color="gray" wire:click="clearHistory">
                                Hapus
                            </x-filament::button>
                        @endif
                    </div>
                </x-slot>

                @if(count($returnedItems) > 0)
                    <div class="space-y-2">
                        @foreach($returnedItems as $item)
                            <div class="p-3 rounded-lg border {{ $item['overdue'] ? 'bg-danger-50 border-danger-200 dark:bg-danger-900/10' : 'bg-success-50 border-success-200 dark:bg-success-900/10' }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="font-medium">{{ Str::limit($item['title'], 50) }}</div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            <span class="font-mono">{{ $item['barcode'] }}</span> • 
                                            {{ $item['member'] }} • 
                                            Tempo: {{ $item['due_date'] }}
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        @if($item['fine'] > 0)
                                            <div class="font-bold text-danger-600">Rp {{ number_format($item['fine'], 0, ',', '.') }}</div>
                                            <div class="text-xs text-danger-500">Denda</div>
                                        @else
                                            <div class="font-bold text-success-600">✓</div>
                                            <div class="text-xs text-success-500">OK</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <p>Belum ada pengembalian</p>
                    </div>
                @endif
            </x-filament::section>
        </div>
    </div>
</x-filament-panels::page>
