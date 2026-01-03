@section('title', 'Sirkulasi')

<div class="space-y-4" x-data="circulationAlerts()" @circulation-alert.window="showAlert($event.detail)" @show-reservation-alert.window="showReservationModal($event.detail)">
    {{-- Reservation Alert Modal --}}
    <template x-teleport="body">
        <div x-show="reservationModal.show" x-cloak class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" x-transition>
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6" @click.outside="reservationModal.show = false">
                <div class="text-center">
                    <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-bookmark text-amber-600 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Ada Reservasi!</h3>
                    <p class="text-gray-600 mb-4">Buku ini direservasi oleh member:</p>
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-4">
                        <p class="font-bold text-gray-900" x-text="reservationModal.memberName"></p>
                        <p class="text-sm text-gray-600" x-text="reservationModal.memberId"></p>
                        <p class="text-xs text-amber-700 mt-2"><i class="fas fa-book mr-1"></i><span x-text="reservationModal.bookTitle"></span></p>
                    </div>
                    <p class="text-sm text-gray-500 mb-4">Member akan diberitahu via email. Buku ditahan 48 jam.</p>
                    <button @click="reservationModal.show = false" class="w-full py-3 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-xl transition">
                        Mengerti
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- Custom Toast Alerts --}}
    <template x-teleport="body">
        <div class="fixed top-6 right-6 z-[99999] space-y-3 pointer-events-none">
            <template x-for="alert in alerts" :key="alert.id">
                <div x-show="alert.show"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-x-8 scale-95"
                     x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-x-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-x-8 scale-95"
                     class="pointer-events-auto flex items-center gap-3 px-5 py-4 rounded-2xl shadow-2xl backdrop-blur-sm border min-w-[320px] max-w-md"
                     :class="{
                         'bg-emerald-500/95 border-emerald-400/50 text-white': alert.type === 'success',
                         'bg-red-500/95 border-red-400/50 text-white': alert.type === 'error',
                         'bg-amber-500/95 border-amber-400/50 text-white': alert.type === 'warning',
                         'bg-blue-500/95 border-blue-400/50 text-white': alert.type === 'info'
                     }">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 bg-white/20">
                        <i class="fas text-lg"
                           :class="{
                               'fa-check': alert.type === 'success',
                               'fa-times': alert.type === 'error',
                               'fa-exclamation': alert.type === 'warning',
                               'fa-info': alert.type === 'info'
                           }"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm" x-text="alert.title"></p>
                        <p class="text-xs opacity-90" x-text="alert.message" x-show="alert.message"></p>
                    </div>
                    <button @click="dismissAlert(alert.id)" class="w-8 h-8 rounded-lg hover:bg-white/20 flex items-center justify-center transition">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
            </template>
        </div>
    </template>

    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-gradient-to-br from-blue-500 via-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-blue-500/30">
                <i class="fas fa-exchange-alt text-2xl"></i>
            </div>
            <div>
                <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Sirkulasi</h1>
                <p class="text-sm text-gray-500">Peminjaman & Pengembalian Buku</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if($this->isSuperAdmin)
            <select wire:model.live="selectedBranchId" class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm">
                <option value="">🌐 Semua Cabang</option>
                @foreach($this->branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                @endforeach
            </select>
            @endif
            <div class="text-right hidden md:block">
                <p class="text-2xl font-bold text-gray-900">{{ now()->format('H:i') }}</p>
                <p class="text-xs text-gray-500">{{ now()->locale('id')->isoFormat('dddd, D MMM Y') }}</p>
            </div>
        </div>
    </div>

    {{-- Receipt Modal - Teleported --}}
    @if($showReceipt && $receiptData)
    <template x-teleport="body">
        <div class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" 
             x-data="{ 
                printData: {
                    member: '{{ addslashes($receiptData['member']->name) }}',
                    memberId: '{{ $receiptData['member']->member_id }}',
                    date: '{{ $receiptData['date']->format('d/m/Y H:i') }}',
                    staff: '{{ addslashes($receiptData['staff']) }}',
                    fine: '{{ number_format($receiptData['member']->memberType->fine_per_day ?? 500, 0, ',', '.') }}',
                    loans: [
                        @foreach($receiptData['loans'] as $loan)
                        { title: '{{ addslashes(Str::limit($loan->item->book->title ?? '-', 40)) }}', barcode: '{{ $loan->item->barcode }}', due: '{{ $loan->due_date->format('d/m/Y') }}' },
                        @endforeach
                    ]
                }
             }">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
                {{-- Receipt Header --}}
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-5 text-center text-white">
                    <div class="w-14 h-14 bg-white rounded-xl flex items-center justify-center mx-auto mb-2 shadow-lg">
                        <img src="{{ asset('storage/logo-portal.png') }}" alt="Logo" class="w-10 h-10 object-contain">
                    </div>
                    <h2 class="text-lg font-bold">UNIDA LIBRARY</h2>
                    <p class="text-blue-100 text-xs">Universitas Darussalam Gontor</p>
                </div>
                
                {{-- Receipt Body --}}
                <div class="p-5 text-sm">
                    <div class="text-center mb-3 pb-3 border-b-2 border-dashed border-gray-200">
                        <p class="text-[10px] text-gray-400 uppercase tracking-wider">Bukti Peminjaman</p>
                        <p class="font-mono text-gray-600">{{ $receiptData['date']->format('d/m/Y H:i') }}</p>
                    </div>
                    
                    <div class="mb-3 pb-3 border-b border-dashed border-gray-200 space-y-1">
                        <div class="flex justify-between"><span class="text-gray-500">Nama</span><span class="font-semibold text-right">{{ $receiptData['member']->name }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">No. Anggota</span><span class="font-mono">{{ $receiptData['member']->member_id }}</span></div>
                    </div>
                    
                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-2">Daftar Pinjaman ({{ count($receiptData['loans']) }})</p>
                    <div class="space-y-1.5 mb-3 pb-3 border-b border-dashed border-gray-200 max-h-40 overflow-y-auto">
                        @foreach($receiptData['loans'] as $i => $loan)
                        <div class="flex gap-2 text-xs">
                            <span class="text-gray-400 w-4">{{ $i + 1 }}.</span>
                            <div class="flex-1">
                                <p class="text-gray-900 truncate">{{ $loan->item->book->title ?? '-' }}</p>
                                <p class="text-gray-400 font-mono">{{ $loan->item->barcode }} • Kembali: {{ $loan->due_date->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="text-center text-[10px] text-gray-400 space-y-0.5">
                        <p>Petugas: {{ $receiptData['staff'] }}</p>
                        <p>Denda keterlambatan: Rp {{ number_format($receiptData['member']->memberType->fine_per_day ?? 500, 0, ',', '.') }}/hari</p>
                        <p class="pt-2 font-medium text-gray-500">Terima kasih • Kembalikan tepat waktu</p>
                    </div>
                </div>
                
                {{-- Actions --}}
                <div class="px-5 pb-5 flex gap-2">
                    <button @click="
                        let items = printData.loans.map((l, i) => '<tr><td style=\'padding:3px 0;color:#666;width:16px;vertical-align:top;font-size:9px\'>'+(i+1)+'.</td><td style=\'padding:3px 0\'><div style=\'font-weight:500;font-size:9px\'>'+l.title+'</div><div style=\'color:#888;font-size:8px;font-family:monospace\'>'+l.barcode+' • '+l.due+'</div></td></tr>').join('');
                        let html = `<!DOCTYPE html><html><head><title>Bukti Peminjaman</title>
<style>
@page{size:72mm auto;margin:0}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:Arial,sans-serif;font-size:9px;width:72mm;-webkit-print-color-adjust:exact;print-color-adjust:exact}
.screen{padding:15px;background:#f5f5f5}
.ctrl{background:#2563eb;border-radius:6px;padding:8px 12px;margin-bottom:10px;color:#fff;display:flex;align-items:center;justify-content:space-between}
.btn{background:#fff;color:#2563eb;border:none;padding:5px 12px;border-radius:4px;font-weight:600;cursor:pointer;font-size:11px}
.card{background:#fff;border-radius:6px;overflow:hidden;width:72mm;margin:0 auto;box-shadow:0 2px 8px rgba(0,0,0,.1)}
.hdr{background:#2563eb !important;color:#fff !important;padding:10px;text-align:center}
.hdr h2{font-size:11px;margin-bottom:1px}
.hdr p{font-size:8px;opacity:.9}
.bdy{padding:8px}
.sec{border-bottom:1px dashed #ccc;padding-bottom:5px;margin-bottom:5px}
.row{display:flex;justify-content:space-between;margin:2px 0;font-size:9px}
.lbl{color:#666}
.val{font-weight:600;text-align:right}
.tit{font-size:7px;color:#888;text-transform:uppercase;margin-bottom:3px;font-weight:600}
.ftr{text-align:center;color:#888;font-size:7px;padding-top:5px;border-top:1px dashed #ccc;margin-top:5px}
table{width:100%;border-collapse:collapse}
@media print{
.screen{padding:0;background:#fff}
.ctrl{display:none}
.card{box-shadow:none;border-radius:0}
}
</style></head>
<body><div class='screen'>
<div class='ctrl'><b style='font-size:12px'>🖨️ Cetak Receipt</b><button class='btn' onclick='window.print()'>Print</button></div>
<div class='card'>
<div class='hdr'><h2>UNIDA LIBRARY</h2><p>Universitas Darussalam Gontor</p></div>
<div class='bdy'>
<div class='sec' style='text-align:center'><div style='font-size:7px;color:#888;text-transform:uppercase'>Bukti Peminjaman</div><div style='font-family:monospace;font-size:9px'>${printData.date}</div></div>
<div class='sec'><div class='row'><span class='lbl'>Nama</span><span class='val'>${printData.member}</span></div><div class='row'><span class='lbl'>No. Anggota</span><span class='val' style='font-family:monospace'>${printData.memberId}</span></div></div>
<div class='tit'>DAFTAR PINJAMAN (${printData.loans.length})</div>
<table>${items}</table>
<div class='ftr'><div>Petugas: ${printData.staff}</div><div>Denda: Rp ${printData.fine}/hari</div><div style='margin-top:3px;font-weight:600;color:#444'>Terima kasih</div></div>
</div></div>
</div></body></html>`;
                        let w = window.open('', '_blank');
                        w.document.write(html);
                        w.document.close();
                    " class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition">
                        <i class="fas fa-print mr-2"></i>Cetak
                    </button>
                    <button wire:click="closeTransaction" class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition">
                        <i class="fas fa-check mr-2"></i>Selesai
                    </button>
                </div>
            </div>
        </div>
    </template>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-500/25">
                    <i class="fas fa-arrow-up-from-bracket text-sm"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $todayLoans }}</p>
                    <p class="text-xs text-gray-500">Pinjam Bulan Ini</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-emerald-500/25">
                    <i class="fas fa-arrow-down-to-bracket text-sm"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $todayReturns }}</p>
                    <p class="text-xs text-gray-500">Kembali Bulan Ini</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-amber-500/25">
                    <i class="fas fa-hourglass-half text-sm"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $activeLoansCount ?? 0 }}</p>
                    <p class="text-xs text-gray-500">Sedang Dipinjam</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-rose-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-red-500/25">
                    <i class="fas fa-clock text-sm"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $overdueCount }}</p>
                    <p class="text-xs text-gray-500">Terlambat</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        {{-- Tabs --}}
        <div class="flex border-b border-gray-100 bg-gray-50/50 p-1.5 gap-1">
            <button wire:click="$set('tab', 'transaction')" 
                    class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all {{ $tab === 'transaction' ? 'bg-gradient-to-r from-blue-500 to-blue-700 text-white shadow-lg shadow-blue-500/25' : 'text-gray-600 hover:bg-white hover:shadow-sm' }}">
                <i class="fas fa-exchange-alt"></i>
                <span>Transaksi</span>
            </button>
            <button wire:click="$set('tab', 'history')" 
                    class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all {{ $tab === 'history' ? 'bg-gradient-to-r from-cyan-500 to-blue-600 text-white shadow-lg shadow-cyan-500/25' : 'text-gray-600 hover:bg-white hover:shadow-sm' }}">
                <i class="fas fa-history"></i>
                <span>Riwayat</span>
            </button>
            <button wire:click="$set('tab', 'overdue')" 
                    class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all {{ $tab === 'overdue' ? 'bg-gradient-to-r from-red-500 to-rose-600 text-white shadow-lg shadow-red-500/25' : 'text-gray-600 hover:bg-white hover:shadow-sm' }}">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Terlambat</span>
                @if($overdueCount > 0)
                <span class="px-1.5 py-0.5 text-[10px] rounded-full {{ $tab === 'overdue' ? 'bg-white/20' : 'bg-red-100 text-red-600' }}">{{ $overdueCount }}</span>
                @endif
            </button>
        </div>

        {{-- Tab Content --}}
        <div class="p-5">
            @if($tab === 'transaction')
                @if($activeMember)
                    {{-- Active Transaction --}}
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                        {{-- Member Info --}}
                        <div class="bg-gradient-to-br from-slate-50 to-gray-100 rounded-xl p-4 border border-gray-200">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="font-bold text-gray-900 text-sm">Anggota Aktif</h3>
                            </div>
                            <div class="flex gap-3">
                                @if($activeMember->photo)
                                    <img src="{{ asset('storage/' . $activeMember->photo) }}" class="w-16 h-20 object-cover rounded-lg border-2 border-white shadow">
                                @else
                                    <div class="w-16 h-20 bg-gradient-to-br from-blue-500 to-blue-700 rounded-lg flex items-center justify-center text-white text-2xl font-bold shadow">
                                        {{ strtoupper(substr($activeMember->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-bold text-gray-900 truncate text-sm">{{ $activeMember->name }}</h4>
                                    <p class="text-xs text-gray-500 font-mono">{{ $activeMember->member_id }}</p>
                                    <div class="flex items-center gap-1 mt-1">
                                        <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full {{ $activeMember->isExpired() ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">
                                            {{ $activeMember->isExpired() ? 'Kadaluarsa' : 'Aktif' }}
                                        </span>
                                        <span class="px-2 py-0.5 bg-gray-200 text-gray-600 text-[10px] font-medium rounded-full">{{ $activeMember->memberType->name ?? '-' }}</span>
                                    </div>
                                    <div class="flex gap-2 mt-2">
                                        <div class="flex-1 text-center py-1.5 bg-white rounded-lg border">
                                            <p class="text-base font-bold text-blue-600">{{ count($activeLoans) }}</p>
                                            <p class="text-[9px] text-gray-500">Dipinjam</p>
                                        </div>
                                        <div class="flex-1 text-center py-1.5 bg-white rounded-lg border">
                                            <p class="text-base font-bold text-emerald-600">{{ max(0, ($activeMember->memberType->loan_limit ?? 3) - count($activeLoans)) }}</p>
                                            <p class="text-[9px] text-gray-500">Kuota</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Finish Button --}}
                            <button wire:click="endTransaction" class="w-full mt-3 py-3 bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white font-bold rounded-xl shadow-lg shadow-red-500/25 transition flex items-center justify-center gap-2">
                                <i class="fas fa-check-circle"></i>
                                <span>Selesaikan Transaksi</span>
                            </button>
                        </div>

                        {{-- Scan & Loans --}}
                        <div class="lg:col-span-2 space-y-4">
                            {{-- Scan Input --}}
                            @unless($activeMember->isExpired())
                            <div class="flex gap-2" x-data="{ itemRect: null }">
                                <div class="relative flex-1">
                                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                                    <input x-ref="itemInput"
                                           wire:model.live.debounce.300ms="itemBarcode" 
                                           wire:keydown.enter="loanItem" 
                                           @focus="itemRect = $refs.itemInput.getBoundingClientRect()"
                                           @input="itemRect = $refs.itemInput.getBoundingClientRect()"
                                           type="text" 
                                           placeholder="Cari judul / barcode / scan..." autofocus
                                           class="w-full pl-11 pr-4 py-3 bg-gray-50 border-2 border-gray-200 focus:border-blue-500 focus:bg-white rounded-xl font-mono transition">
                                </div>
                                <button wire:click="loanItem" class="px-5 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/25 transition">
                                    <i class="fas fa-plus mr-2"></i>Pinjam
                                </button>
                                {{-- Item Suggestions - Teleported --}}
                                @if(count($itemSuggestions) > 0)
                                <template x-teleport="body">
                                    <div class="fixed z-[9999] bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden max-h-72 overflow-y-auto"
                                         :style="itemRect ? 'left: ' + itemRect.left + 'px; top: ' + (itemRect.bottom + 4) + 'px; width: ' + itemRect.width + 'px;' : ''">
                                        @foreach($itemSuggestions as $item)
                                        <button wire:click="selectItem({{ $item->id }})" type="button"
                                                class="w-full px-4 py-3 flex items-center gap-3 hover:bg-blue-50 transition text-left border-b border-gray-100 last:border-0">
                                            <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center text-white">
                                                <i class="fas fa-book text-sm"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="font-medium text-gray-900 truncate text-sm">{{ $item->book->title ?? '-' }}</p>
                                                <p class="text-xs text-gray-500 font-mono">{{ $item->barcode }}</p>
                                            </div>
                                        </button>
                                        @endforeach
                                    </div>
                                </template>
                                @endif
                            </div>
                            @else
                            <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-600">
                                <i class="fas fa-exclamation-triangle mr-2"></i>Keanggotaan kadaluarsa. Peminjaman tidak diizinkan.
                            </div>
                            @endunless

                            {{-- Active Loans --}}
                            <div class="bg-gray-50 rounded-xl border overflow-hidden">
                                <div class="px-4 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-sm font-semibold flex items-center justify-between">
                                    <span><i class="fas fa-list mr-2"></i>Pinjaman Aktif</span>
                                    <span class="px-2 py-0.5 bg-white/20 rounded-full text-xs">{{ count($activeLoans) }}</span>
                                </div>
                                @if(count($activeLoans) > 0)
                                <div class="divide-y divide-gray-200 max-h-64 overflow-y-auto">
                                    @foreach($activeLoans as $loan)
                                    @php $isOverdue = $loan->due_date < now(); $daysLeft = (int) now()->diffInDays($loan->due_date, false); @endphp
                                    <div class="p-3 flex items-center gap-3 {{ $isOverdue ? 'bg-red-50' : 'bg-white' }}">
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-gray-900 text-sm truncate">{{ Str::limit($loan->item->book->title ?? '-', 35) }}</p>
                                            <p class="text-xs text-gray-500 font-mono">{{ $loan->item->barcode }}</p>
                                        </div>
                                        <div class="text-right">
                                            @if($isOverdue)
                                            <span class="text-xs font-bold text-red-600">{{ abs($daysLeft) }}h terlambat</span>
                                            @else
                                            <span class="text-xs text-gray-500">{{ $daysLeft }}h lagi</span>
                                            @endif
                                        </div>
                                        <div class="flex gap-1">
                                            <button wire:click="returnItem({{ $loan->id }})" class="px-2.5 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-medium rounded-lg">Kembali</button>
                                            @if(!$isOverdue && $loan->extend_count < 2)
                                            <button wire:click="extendLoan({{ $loan->id }})" class="px-2 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs rounded-lg" title="Perpanjang 7 hari">+7</button>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <div class="p-8 text-center text-gray-400">
                                    <i class="fas fa-inbox text-2xl mb-2"></i>
                                    <p class="text-sm">Tidak ada pinjaman aktif</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Start Transaction --}}
                    <div class="max-w-lg mx-auto text-center py-8" x-data="{ inputRect: null }" x-init="inputRect = $refs.memberInput?.getBoundingClientRect()">
                        <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl flex items-center justify-center text-white mx-auto mb-4 shadow-xl shadow-blue-500/30">
                            <i class="fas fa-id-card text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Mulai Transaksi</h3>
                        <p class="text-gray-500 text-sm mb-6">Cari nama, NIM, atau scan kartu anggota</p>
                        <div class="flex gap-2 max-w-md mx-auto">
                            <div class="relative flex-1">
                                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                                <input x-ref="memberInput" 
                                       wire:model.live.debounce.300ms="memberBarcode" 
                                       wire:keydown.enter="startTransaction" 
                                       @focus="inputRect = $refs.memberInput.getBoundingClientRect()"
                                       @input="inputRect = $refs.memberInput.getBoundingClientRect()"
                                       type="text" 
                                       placeholder="Nama / NIM / Scan..." autofocus
                                       class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border-2 border-gray-200 focus:border-blue-500 focus:bg-white rounded-xl text-lg transition">
                            </div>
                            <button wire:click="startTransaction" class="px-6 py-3.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/25 transition">
                                <i class="fas fa-play mr-2"></i>Mulai
                            </button>
                        </div>
                        {{-- Member Suggestions - Teleported --}}
                        @if(count($memberSuggestions) > 0)
                        <template x-teleport="body">
                            <div class="fixed z-[9999] bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden max-h-72 overflow-y-auto"
                                 :style="inputRect ? 'left: ' + inputRect.left + 'px; top: ' + (inputRect.bottom + 4) + 'px; width: ' + inputRect.width + 'px;' : ''">
                                @foreach($memberSuggestions as $member)
                                <button wire:click="selectMember({{ $member->id }})" type="button"
                                        class="w-full px-4 py-3 flex items-center gap-3 hover:bg-blue-50 transition text-left border-b border-gray-100 last:border-0">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-700 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                                        {{ strtoupper(substr($member->name, 0, 1)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-gray-900 truncate">{{ $member->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $member->member_id }} • {{ $member->memberType->name ?? '-' }}</p>
                                    </div>
                                    @if($member->isExpired())
                                    <span class="px-2 py-0.5 bg-red-100 text-red-600 text-[10px] font-semibold rounded-full">Expired</span>
                                    @endif
                                </button>
                                @endforeach
                            </div>
                        </template>
                        @endif
                    </div>
                @endif

            @elseif($tab === 'history')
                {{-- History Tab --}}
                <div class="space-y-4">
                    <div class="flex gap-3">
                        <div class="relative flex-1">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input wire:model.live.debounce.300ms="searchHistory" type="text" placeholder="Cari riwayat..." 
                                   class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400">
                        </div>
                        <select wire:model.live="filterDays" class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm">
                            <option value="7">7 Hari</option>
                            <option value="30">30 Hari</option>
                            <option value="90">90 Hari</option>
                        </select>
                    </div>
                    <div class="overflow-hidden rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead class="bg-gradient-to-r from-blue-500 to-cyan-600 text-white">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold">Anggota</th>
                                    <th class="px-4 py-3 text-left font-semibold">Buku</th>
                                    <th class="px-4 py-3 text-center font-semibold">Pinjam</th>
                                    <th class="px-4 py-3 text-center font-semibold">Kembali</th>
                                    <th class="px-4 py-3 text-center font-semibold">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($historyLoans ?? [] as $loan)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-gray-900">{{ $loan->member->name ?? '-' }}</p>
                                        <p class="text-xs text-gray-500">{{ $loan->member->member_id ?? '-' }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="text-gray-900">{{ Str::limit($loan->item->book->title ?? '-', 30) }}</p>
                                        <p class="text-xs text-gray-500 font-mono">{{ $loan->item->barcode ?? '-' }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-center text-gray-600">{{ $loan->loan_date->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3 text-center text-gray-600">{{ $loan->return_date?->format('d/m/Y') ?? '-' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @if($loan->return_date)
                                        <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-full">Dikembalikan</span>
                                        @else
                                        <span class="px-2 py-1 bg-amber-100 text-amber-700 text-xs font-semibold rounded-full">Dipinjam</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Tidak ada riwayat</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if(isset($historyLoans) && $historyLoans->hasPages())
                    <div class="mt-4">{{ $historyLoans->links() }}</div>
                    @endif
                </div>

            @elseif($tab === 'overdue')
                {{-- Overdue Tab --}}
                <div class="overflow-hidden rounded-xl border border-gray-200">
                    <table class="w-full text-sm">
                        <thead class="bg-gradient-to-r from-red-500 to-rose-600 text-white">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold">Anggota</th>
                                <th class="px-4 py-3 text-left font-semibold">Buku</th>
                                <th class="px-4 py-3 text-center font-semibold">Jatuh Tempo</th>
                                <th class="px-4 py-3 text-center font-semibold">Terlambat</th>
                                <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($overdueLoans ?? [] as $loan)
                            @php $daysOverdue = (int) $loan->due_date->diffInDays(now()); @endphp
                            <tr class="hover:bg-red-50">
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-900">{{ $loan->member->name ?? '-' }}</p>
                                    <p class="text-xs text-gray-500">{{ $loan->member->phone ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-gray-900">{{ Str::limit($loan->item->book->title ?? '-', 30) }}</p>
                                    <p class="text-xs text-gray-500 font-mono">{{ $loan->item->barcode ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600">{{ $loan->due_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">{{ $daysOverdue }} hari</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button wire:click="quickReturn({{ $loan->id }})" class="px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-medium rounded-lg">
                                        Kembalikan
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400"><i class="fas fa-check-circle text-emerald-400 text-2xl mb-2"></i><p>Tidak ada pinjaman terlambat</p></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function circulationAlerts() {
    return {
        alerts: [],
        alertId: 0,
        reservationModal: { show: false, memberName: '', memberId: '', bookTitle: '' },
        
        showAlert(detail) {
            const id = ++this.alertId;
            const alert = {
                id,
                type: detail.type || 'info',
                title: detail.title || '',
                message: detail.message || '',
                show: false
            };
            this.alerts.push(alert);
            
            setTimeout(() => {
                const a = this.alerts.find(x => x.id === id);
                if (a) a.show = true;
            }, 50);
            
            setTimeout(() => this.dismissAlert(id), detail.duration || 4000);
        },
        
        dismissAlert(id) {
            const alert = this.alerts.find(x => x.id === id);
            if (alert) {
                alert.show = false;
                setTimeout(() => {
                    this.alerts = this.alerts.filter(x => x.id !== id);
                }, 300);
            }
        },
        
        showReservationModal(detail) {
            this.reservationModal = {
                show: true,
                memberName: detail[0]?.memberName || '',
                memberId: detail[0]?.memberId || '',
                bookTitle: detail[0]?.bookTitle || ''
            };
        }
    }
}
</script>
@endpush
