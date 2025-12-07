<x-filament-panels::page>
    @if($activeMember)
        {{-- Member Info Section --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center justify-between">
                    <span>Transaksi Aktif</span>
                    <x-filament::button wire:click="endTransaction" color="danger" size="sm">
                        Selesai Transaksi
                    </x-filament::button>
                </div>
            </x-slot>

            <div class="flex flex-col md:flex-row gap-6">
                {{-- Photo --}}
                <div class="flex-shrink-0">
                    @if($activeMember->photo)
                        <img src="{{ asset('storage/' . $activeMember->photo) }}" class="w-28 h-36 object-cover rounded-lg border">
                    @else
                        <div class="w-28 h-36 rounded-lg bg-primary-100 dark:bg-primary-900 flex items-center justify-center text-primary-600 text-4xl font-bold">
                            {{ strtoupper(substr($activeMember->name, 0, 1)) }}
                        </div>
                    @endif
                </div>

                {{-- Info Table --}}
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-4">
                        <h3 class="text-xl font-bold">{{ $activeMember->name }}</h3>
                        <x-filament::badge color="primary">{{ $activeMember->memberType->name ?? 'Member' }}</x-filament::badge>
                        @if($activeMember->isExpired())
                            <x-filament::badge color="danger">Kadaluarsa</x-filament::badge>
                        @endif
                    </div>

                    <table class="text-sm">
                        <tr>
                            <td class="py-1 pr-4 text-gray-500 w-32">No. Anggota</td>
                            <td class="py-1 font-mono font-medium">{{ $activeMember->member_id }}</td>
                            <td class="py-1 px-6 text-gray-500 w-32">Email</td>
                            <td class="py-1 font-medium">{{ $activeMember->email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 pr-4 text-gray-500">Telepon</td>
                            <td class="py-1 font-medium">{{ $activeMember->phone ?? '-' }}</td>
                            <td class="py-1 px-6 text-gray-500">Alamat</td>
                            <td class="py-1 font-medium">{{ Str::limit($activeMember->address, 30) ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 pr-4 text-gray-500">Terdaftar</td>
                            <td class="py-1 font-medium">{{ $activeMember->register_date?->format('d M Y') }}</td>
                            <td class="py-1 px-6 text-gray-500">Berlaku s/d</td>
                            <td class="py-1 font-medium {{ $activeMember->isExpired() ? 'text-danger-600' : '' }}">
                                {{ $activeMember->expire_date?->format('d M Y') }}
                            </td>
                        </tr>
                    </table>
                </div>

                {{-- Stats --}}
                <div class="flex md:flex-col gap-3">
                    <div class="flex-1 text-center px-6 py-3 bg-primary-50 dark:bg-primary-900/20 rounded-lg">
                        <div class="text-3xl font-bold text-primary-600">{{ count($activeLoans) }}</div>
                        <div class="text-xs text-gray-500">Dipinjam</div>
                    </div>
                    <div class="flex-1 text-center px-6 py-3 bg-success-50 dark:bg-success-900/20 rounded-lg">
                        <div class="text-3xl font-bold text-success-600">{{ max(0, ($activeMember->memberType->loan_limit ?? 3) - count($activeLoans)) }}</div>
                        <div class="text-xs text-gray-500">Sisa Kuota</div>
                    </div>
                </div>
            </div>

            @if($activeMember->isExpired())
                <x-filament::section class="mt-4" compact>
                    <p class="text-sm text-danger-600">⚠️ Keanggotaan sudah kadaluarsa. Peminjaman tidak diizinkan.</p>
                </x-filament::section>
            @endif
        </x-filament::section>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            {{-- Loan Input --}}
            <x-filament::section>
                <x-slot name="heading">Pinjam Buku</x-slot>
                
                <div class="space-y-3">
                    <x-filament::input.wrapper label="Barcode Item">
                        <x-filament::input
                            wire:model="itemBarcode"
                            wire:keydown.enter="loanItem"
                            placeholder="Scan barcode..."
                            :disabled="$activeMember->isExpired()"
                        />
                    </x-filament::input.wrapper>
                    
                    <x-filament::button wire:click="loanItem" class="w-full" :disabled="$activeMember->isExpired()">
                        Pinjam
                    </x-filament::button>
                </div>
            </x-filament::section>

            {{-- Loans Table --}}
            <div class="lg:col-span-2">
                <x-filament::section>
                    <x-slot name="heading">Daftar Pinjaman ({{ count($activeLoans) }})</x-slot>

                    @if(count($activeLoans) > 0)
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-4 py-2 text-left">Judul</th>
                                    <th class="px-4 py-2 text-center w-20">Pinjam</th>
                                    <th class="px-4 py-2 text-center w-20">Tempo</th>
                                    <th class="px-4 py-2 text-center w-24">Status</th>
                                    <th class="px-4 py-2 text-center w-40">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($activeLoans as $loan)
                                    @php
                                        $isOverdue = $loan->due_date < now();
                                        $daysLeft = (int) now()->diffInDays($loan->due_date, false);
                                    @endphp
                                    <tr class="{{ $isOverdue ? 'bg-danger-50 dark:bg-danger-900/10' : '' }}">
                                        <td class="px-4 py-2">
                                            <div class="font-medium">{{ Str::limit($loan->item->book->title ?? '-', 40) }}</div>
                                            <div class="text-xs text-gray-500 font-mono">{{ $loan->item->barcode }}</div>
                                        </td>
                                        <td class="px-4 py-2 text-center text-xs">{{ $loan->loan_date->format('d/m/Y') }}</td>
                                        <td class="px-4 py-2 text-center text-xs">{{ $loan->due_date->format('d/m/Y') }}</td>
                                        <td class="px-4 py-2 text-center">
                                            @if($isOverdue)
                                                <x-filament::badge color="danger">{{ abs($daysLeft) }} hari terlambat</x-filament::badge>
                                            @else
                                                <x-filament::badge color="success">{{ $daysLeft }} hari lagi</x-filament::badge>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <div class="flex justify-center gap-1">
                                                <x-filament::button size="xs" wire:click="returnItem({{ $loan->id }})">
                                                    Kembali
                                                </x-filament::button>
                                                @if(!$isOverdue && $loan->extend_count < 2)
                                                    <x-filament::button size="xs" color="gray" wire:click="extendLoan({{ $loan->id }})">
                                                        +7
                                                    </x-filament::button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <p>Tidak ada pinjaman aktif</p>
                        </div>
                    @endif
                </x-filament::section>
            </div>
        </div>

    @else
        {{-- Start Transaction --}}
        <x-filament::section>
            <x-slot name="heading">Mulai Transaksi</x-slot>
            <x-slot name="description">Scan kartu anggota atau masukkan nomor anggota untuk memulai transaksi peminjaman/pengembalian</x-slot>

            <div class="max-w-md">
                <div class="space-y-4">
                    <x-filament::input.wrapper label="No. Anggota">
                        <x-filament::input
                            wire:model="memberBarcode"
                            wire:keydown.enter="startTransaction"
                            placeholder="Scan atau ketik nomor anggota..."
                            autofocus
                        />
                    </x-filament::input.wrapper>
                    
                    <x-filament::button wire:click="startTransaction">
                        Mulai Transaksi
                    </x-filament::button>
                </div>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
