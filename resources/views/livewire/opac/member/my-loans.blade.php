<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 py-6">
        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Pinjaman & Denda</h1>
            <p class="text-gray-500 text-sm">Kelola pinjaman, reservasi, dan pembayaran denda</p>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
            <div class="bg-white rounded-xl p-4 border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-book-reader text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $this->activeLoans->count() }}</p>
                        <p class="text-xs text-gray-500">Dipinjam</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-amber-600"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $this->reservations->count() }}</p>
                        <p class="text-xs text-gray-500">Reservasi</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exclamation-circle text-red-600"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $this->activeLoans->where('due_date', '<', now())->count() }}</p>
                        <p class="text-xs text-gray-500">Terlambat</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-rose-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-coins text-rose-600"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($this->totalFine) }}</p>
                        <p class="text-xs text-gray-500">Total Denda</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <div class="flex border-b border-gray-100">
                <button wire:click="$set('activeTab', 'loans')" class="flex-1 px-4 py-3 text-sm font-medium {{ $activeTab === 'loans' ? 'text-primary-600 border-b-2 border-primary-600 bg-primary-50/50' : 'text-gray-500 hover:text-gray-700' }}">
                    <i class="fas fa-book-reader mr-2"></i>Pinjaman Aktif
                </button>
                <button wire:click="$set('activeTab', 'reservations')" class="flex-1 px-4 py-3 text-sm font-medium {{ $activeTab === 'reservations' ? 'text-primary-600 border-b-2 border-primary-600 bg-primary-50/50' : 'text-gray-500 hover:text-gray-700' }}">
                    <i class="fas fa-bookmark mr-2"></i>Reservasi
                </button>
                <button wire:click="$set('activeTab', 'fines')" class="flex-1 px-4 py-3 text-sm font-medium {{ $activeTab === 'fines' ? 'text-primary-600 border-b-2 border-primary-600 bg-primary-50/50' : 'text-gray-500 hover:text-gray-700' }}">
                    <i class="fas fa-coins mr-2"></i>Denda
                    @if($this->fines->count() > 0)
                    <span class="ml-1 px-1.5 py-0.5 bg-red-500 text-white text-xs rounded-full">{{ $this->fines->count() }}</span>
                    @endif
                </button>
                <button wire:click="$set('activeTab', 'history')" class="flex-1 px-4 py-3 text-sm font-medium {{ $activeTab === 'history' ? 'text-primary-600 border-b-2 border-primary-600 bg-primary-50/50' : 'text-gray-500 hover:text-gray-700' }}">
                    <i class="fas fa-history mr-2"></i>Riwayat
                </button>
            </div>

            <div class="p-4">
                {{-- Active Loans Tab --}}
                @if($activeTab === 'loans')
                <div class="space-y-3">
                    @forelse($this->activeLoans as $loan)
                    @php
                        $isOverdue = $loan->due_date < now();
                        $daysLeft = now()->diffInDays($loan->due_date, false);
                        $canRenew = !$isOverdue && $loan->renewal_count < ($loan->max_renewals ?? 2);
                    @endphp
                    <div class="flex items-center gap-4 p-4 rounded-xl border {{ $isOverdue ? 'border-red-200 bg-red-50' : ($daysLeft <= 3 ? 'border-amber-200 bg-amber-50' : 'border-gray-100 bg-gray-50') }}">
                        <div class="w-12 h-16 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0 overflow-hidden">
                            @if($loan->item?->book?->image)
                            <img src="{{ asset('storage/' . $loan->item->book->image) }}" class="w-full h-full object-cover">
                            @else
                            <i class="fas fa-book text-gray-400"></i>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-900 truncate">{{ $loan->item?->book?->title }}</h3>
                            <p class="text-xs text-gray-500">{{ $loan->item?->book?->author }}</p>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="text-xs {{ $isOverdue ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                    <i class="far fa-calendar mr-1"></i>
                                    {{ $isOverdue ? 'Terlambat ' . abs($daysLeft) . ' hari' : 'Jatuh tempo: ' . $loan->due_date->format('d M Y') }}
                                </span>
                                @if($loan->renewal_count > 0)
                                <span class="text-xs text-blue-600">
                                    <i class="fas fa-redo mr-1"></i>Diperpanjang {{ $loan->renewal_count }}x
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            @if($canRenew)
                            <button wire:click="renewLoan({{ $loan->id }})" wire:loading.attr="disabled" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition">
                                <span wire:loading.remove wire:target="renewLoan({{ $loan->id }})">Perpanjang</span>
                                <span wire:loading wire:target="renewLoan({{ $loan->id }})"><i class="fas fa-spinner fa-spin"></i></span>
                            </button>
                            @else
                            <span class="px-3 py-1.5 bg-gray-200 text-gray-500 text-xs font-medium rounded-lg">
                                {{ $isOverdue ? 'Terlambat' : 'Maks perpanjangan' }}
                            </span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-12 text-gray-400">
                        <i class="fas fa-book-open text-4xl mb-3"></i>
                        <p>Tidak ada pinjaman aktif</p>
                    </div>
                    @endforelse
                </div>
                @endif

                {{-- Reservations Tab --}}
                @if($activeTab === 'reservations')
                <div class="space-y-3">
                    @forelse($this->reservations as $reservation)
                    <div class="flex items-center gap-4 p-4 rounded-xl border {{ $reservation->isReady() ? 'border-green-200 bg-green-50' : 'border-gray-100 bg-gray-50' }}">
                        <div class="w-12 h-16 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0 overflow-hidden">
                            @if($reservation->book?->image)
                            <img src="{{ asset('storage/' . $reservation->book->image) }}" class="w-full h-full object-cover">
                            @else
                            <i class="fas fa-book text-gray-400"></i>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-900 truncate">{{ $reservation->book?->title }}</h3>
                            <p class="text-xs text-gray-500">{{ $reservation->book?->author }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                @if($reservation->isReady())
                                <span class="px-2 py-0.5 bg-green-500 text-white text-xs font-semibold rounded-full">Siap Diambil</span>
                                <span class="text-xs text-gray-500">Batas: {{ $reservation->pickup_deadline->format('d M Y H:i') }}</span>
                                @else
                                <span class="px-2 py-0.5 bg-amber-500 text-white text-xs font-semibold rounded-full">Antrian #{{ $reservation->queue_position }}</span>
                                @endif
                            </div>
                        </div>
                        <button wire:click="cancelReservation({{ $reservation->id }})" wire:confirm="Batalkan reservasi ini?" class="px-3 py-1.5 bg-gray-200 hover:bg-red-100 hover:text-red-600 text-gray-600 text-sm font-medium rounded-lg transition">
                            Batalkan
                        </button>
                    </div>
                    @empty
                    <div class="text-center py-12 text-gray-400">
                        <i class="fas fa-bookmark text-4xl mb-3"></i>
                        <p>Tidak ada reservasi aktif</p>
                        <a href="{{ route('opac.search') }}" class="inline-block mt-3 text-primary-600 hover:underline text-sm">Cari buku untuk direservasi</a>
                    </div>
                    @endforelse
                </div>
                @endif

                {{-- Fines Tab --}}
                @if($activeTab === 'fines')
                <div>
                    @if($this->fines->count() > 0)
                    <div class="flex items-center justify-between mb-4">
                        <button wire:click="selectAllFines" class="text-sm text-primary-600 hover:underline">Pilih Semua</button>
                        @if(count($selectedFines) > 0)
                        <button wire:click="openPaymentModal" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg">
                            Bayar Rp {{ number_format($this->selectedTotal) }}
                        </button>
                        @endif
                    </div>
                    <div class="space-y-2">
                        @foreach($this->fines as $fine)
                        <label class="flex items-center gap-4 p-4 rounded-xl border border-gray-100 bg-gray-50 cursor-pointer hover:bg-gray-100 transition">
                            <input type="checkbox" wire:click="toggleFine({{ $fine->id }})" {{ in_array($fine->id, $selectedFines) ? 'checked' : '' }} class="w-5 h-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-medium text-gray-900 truncate">{{ $fine->loan?->item?->book?->title ?? 'Denda' }}</h3>
                                <p class="text-xs text-gray-500">{{ $fine->description ?? 'Keterlambatan pengembalian' }}</p>
                            </div>
                            <span class="font-bold text-red-600">Rp {{ number_format($fine->amount) }}</span>
                        </label>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-12 text-gray-400">
                        <i class="fas fa-check-circle text-4xl mb-3 text-green-400"></i>
                        <p>Tidak ada denda</p>
                    </div>
                    @endif
                </div>
                @endif

                {{-- History Tab --}}
                @if($activeTab === 'history')
                <div class="space-y-2">
                    @forelse($this->loanHistory as $loan)
                    <div class="flex items-center gap-4 p-3 rounded-xl bg-gray-50">
                        <div class="w-10 h-14 bg-gray-200 rounded flex items-center justify-center flex-shrink-0 overflow-hidden">
                            @if($loan->item?->book?->image)
                            <img src="{{ asset('storage/' . $loan->item->book->image) }}" class="w-full h-full object-cover">
                            @else
                            <i class="fas fa-book text-gray-400 text-xs"></i>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-medium text-gray-900 text-sm truncate">{{ $loan->item?->book?->title }}</h3>
                            <p class="text-xs text-gray-500">Dikembalikan: {{ $loan->return_date?->format('d M Y') }}</p>
                        </div>
                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded">Selesai</span>
                    </div>
                    @empty
                    <div class="text-center py-12 text-gray-400">
                        <i class="fas fa-history text-4xl mb-3"></i>
                        <p>Belum ada riwayat pinjaman</p>
                    </div>
                    @endforelse
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Payment Modal --}}
    @if($showPaymentModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" x-data x-on:keydown.escape="$wire.set('showPaymentModal', false)">
        <div class="bg-white rounded-2xl w-full max-w-md p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Pembayaran Denda</h3>
            <div class="mb-4 p-4 bg-gray-50 rounded-xl">
                <p class="text-sm text-gray-500">Total Pembayaran</p>
                <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($this->selectedTotal) }}</p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran</label>
                <div class="space-y-2">
                    <label class="flex items-center gap-3 p-3 border rounded-xl cursor-pointer {{ $paymentMethod === 'cash' ? 'border-primary-500 bg-primary-50' : 'border-gray-200' }}">
                        <input type="radio" wire:model="paymentMethod" value="cash" class="text-primary-600">
                        <i class="fas fa-money-bill-wave text-green-600"></i>
                        <span class="font-medium">Bayar di Perpustakaan</span>
                    </label>
                    <label class="flex items-center gap-3 p-3 border rounded-xl cursor-pointer {{ $paymentMethod === 'midtrans' ? 'border-primary-500 bg-primary-50' : 'border-gray-200' }}">
                        <input type="radio" wire:model="paymentMethod" value="midtrans" class="text-primary-600">
                        <i class="fas fa-credit-card text-blue-600"></i>
                        <span class="font-medium">Bayar Online</span>
                    </label>
                </div>
            </div>
            <div class="flex gap-3">
                <button wire:click="$set('showPaymentModal', false)" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-300 transition">Batal</button>
                <button wire:click="processPayment" class="flex-1 px-4 py-2 bg-primary-600 text-white font-medium rounded-xl hover:bg-primary-700 transition">
                    <span wire:loading.remove wire:target="processPayment">Bayar Sekarang</span>
                    <span wire:loading wire:target="processPayment"><i class="fas fa-spinner fa-spin"></i></span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
