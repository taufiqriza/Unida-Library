<?php

namespace App\Livewire\Staff\Circulation;

use App\Models\Item;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Fine;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class CirculationTransaction extends Component
{
    use WithPagination;
    
    public string $tab = 'transaction';
    public ?string $memberBarcode = '';
    public ?string $itemBarcode = '';
    public ?Member $activeMember = null;
    public $activeLoans = [];
    
    // Stats
    public $todayLoans = 0;
    public $todayReturns = 0;
    public $overdueCount = 0;
    public $activeLoansCount = 0;
    
    // History filters
    public string $searchHistory = '';
    public int $filterDays = 7;
    
    // Branch filter
    public $selectedBranchId = '';

    protected $queryString = ['tab'];

    public function mount(): void
    {
        $this->selectedBranchId = auth()->user()->branch_id ?? '';
        $this->loadFromSession();
        $this->loadStats();
    }
    
    public function updatedSelectedBranchId(): void
    {
        $this->loadStats();
    }
    
    public function getBranchIdProperty()
    {
        return $this->selectedBranchId ?: null;
    }
    
    public function getIsSuperAdminProperty(): bool
    {
        return auth()->user()->role === 'super_admin' || auth()->user()->branch_id === null;
    }
    
    public function getBranchesProperty()
    {
        return \App\Models\Branch::orderBy('name')->get();
    }

    protected function loadFromSession(): void
    {
        if (session('staff_circulation_member_id')) {
            $this->activeMember = Member::with('memberType')->find(session('staff_circulation_member_id'));
            $this->loadActiveLoans();
        }
    }

    protected function loadStats(): void
    {
        $branchId = $this->branchId;
        $startOfMonth = now()->startOfMonth();
        
        $query = Loan::query();
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        
        $this->todayLoans = (clone $query)->where('loan_date', '>=', $startOfMonth)->count();
        $this->todayReturns = (clone $query)->whereNotNull('return_date')->where('return_date', '>=', $startOfMonth)->count();
        $this->overdueCount = (clone $query)->where('is_returned', false)->where('due_date', '<', now())->count();
        $this->activeLoansCount = (clone $query)->where('is_returned', false)->count();
    }

    protected function loadActiveLoans(): void
    {
        if ($this->activeMember) {
            $this->activeLoans = Loan::with(['item.book', 'item.location'])
                ->where('member_id', $this->activeMember->id)
                ->where('is_returned', false)
                ->orderBy('due_date')
                ->get();
        }
    }

    // Search suggestions
    public $memberSuggestions = [];
    public $itemSuggestions = [];

    public function updatedMemberBarcode($value): void
    {
        $value = trim($value);
        if (strlen($value) >= 2) {
            $branchId = auth()->user()->branch_id;
            $this->memberSuggestions = Member::with('memberType')
                ->where('is_active', true)
                ->where(function($q) use ($branchId) {
                    if ($branchId) $q->where('branch_id', $branchId);
                })
                ->where(function($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%")
                      ->orWhere('member_id', 'like', "%{$value}%")
                      ->orWhere('email', 'like', "%{$value}%");
                })
                ->limit(5)
                ->get();
        } else {
            $this->memberSuggestions = [];
        }
    }

    public function selectMember($memberId): void
    {
        $member = Member::with('memberType')->find($memberId);
        if ($member) {
            $this->activeMember = $member;
            session(['staff_circulation_member_id' => $member->id]);
            $this->loadActiveLoans();
            $this->memberBarcode = '';
            $this->memberSuggestions = [];
            
            if ($member->isExpired()) {
                $this->alert('warning', 'Keanggotaan kadaluarsa', $member->name);
            } else {
                $this->alert('success', 'Transaksi dimulai', $member->name);
            }
        }
    }

    public function startTransaction(): void
    {
        $this->memberBarcode = trim($this->memberBarcode);
        
        if (empty($this->memberBarcode)) {
            $this->alert('error', 'Masukkan nama atau nomor anggota');
            return;
        }

        $branchId = auth()->user()->branch_id;
        $member = Member::with('memberType')
            ->where(function($q) use ($branchId) {
                if ($branchId) $q->where('branch_id', $branchId);
            })
            ->where(function($q) {
                $q->where('member_id', $this->memberBarcode)
                  ->orWhere('id', $this->memberBarcode);
            })
            ->first();

        if (!$member) {
            $this->alert('error', 'Anggota tidak ditemukan', $this->memberBarcode);
            return;
        }

        if (!$member->is_active) {
            $this->alert('error', 'Keanggotaan tidak aktif', $member->name);
            return;
        }

        $this->activeMember = $member;
        session(['staff_circulation_member_id' => $member->id]);
        $this->loadActiveLoans();
        $this->memberBarcode = '';
        $this->memberSuggestions = [];

        if ($member->isExpired()) {
            $this->alert('warning', 'Keanggotaan kadaluarsa', $member->name);
        } else {
            $this->alert('success', 'Transaksi dimulai', $member->name);
        }
    }

    public function updatedItemBarcode($value): void
    {
        $value = trim($value);
        if (strlen($value) >= 2) {
            $branchId = auth()->user()->branch_id;
            $query = Item::withoutGlobalScopes()
                ->with('book', 'location')
                ->whereDoesntHave('loans', fn($q) => $q->where('is_returned', false))
                ->where(function($q) use ($value) {
                    $q->where('barcode', 'like', "%{$value}%")
                      ->orWhereHas('book', fn($b) => $b->where('title', 'like', "%{$value}%"));
                });
            
            if ($branchId) {
                $query->where(function($q) use ($branchId) {
                    $q->where('branch_id', $branchId)
                      ->orWhereHas('location', fn($l) => $l->where('branch_id', $branchId));
                });
            }
            
            $this->itemSuggestions = $query->limit(8)->get();
        } else {
            $this->itemSuggestions = [];
        }
    }

    public function selectItem($itemId): void
    {
        $this->itemBarcode = Item::find($itemId)?->barcode ?? '';
        $this->itemSuggestions = [];
        $this->loanItem();
    }

    protected function alert($type, $title, $message = ''): void
    {
        $this->dispatch('circulation-alert', type: $type, title: $title, message: $message);
    }

    public function loanItem(): void
    {
        if (!$this->activeMember) {
            $this->alert('error', 'Pilih anggota terlebih dahulu');
            return;
        }

        if ($this->activeMember->isExpired()) {
            $this->alert('error', 'Keanggotaan sudah kadaluarsa', 'Peminjaman tidak diizinkan');
            return;
        }

        $this->itemBarcode = trim($this->itemBarcode);
        $this->itemSuggestions = [];
        
        if (empty($this->itemBarcode)) {
            $this->alert('error', 'Masukkan barcode atau judul buku');
            return;
        }

        $currentBranchId = auth()->user()->branch_id;
        
        // Cari item, prioritaskan dari branch sendiri jika ada duplikat barcode
        $item = Item::withoutGlobalScopes()
            ->with('book', 'location.branch', 'branch')
            ->where('barcode', $this->itemBarcode)
            ->when($currentBranchId, function ($q) use ($currentBranchId) {
                $q->orderByRaw("CASE WHEN branch_id = ? THEN 0 ELSE 1 END", [$currentBranchId]);
            })
            ->first();

        if (!$item) {
            $this->alert('error', 'Item tidak ditemukan', "Barcode: {$this->itemBarcode}");
            return;
        }

        $itemBranchId = $item->location?->branch_id ?? $item->branch_id;
        if ($currentBranchId && $itemBranchId !== $currentBranchId) {
            $itemBranch = $item->location?->branch?->name ?? $item->branch?->name ?? "ID: {$itemBranchId}";
            $this->alert('error', 'Item milik cabang lain', "Item: {$itemBranch}");
            return;
        }

        if (!$item->isAvailable()) {
            $this->alert('error', 'Item tidak tersedia', 'Sedang dipinjam atau tidak aktif');
            return;
        }

        $loanLimit = $this->activeMember->memberType->loan_limit ?? 3;
        $currentLoans = count($this->activeLoans);

        if ($currentLoans >= $loanLimit) {
            $this->alert('warning', 'Batas pinjam tercapai', "Maksimal {$loanLimit} buku");
            return;
        }

        $alreadyBorrowed = Loan::where('member_id', $this->activeMember->id)
            ->where('item_id', $item->id)
            ->where('is_returned', false)
            ->exists();

        if ($alreadyBorrowed) {
            $this->alert('warning', 'Sudah dipinjam', 'Item ini sudah dipinjam anggota');
            return;
        }

        $loanPeriod = $this->activeMember->memberType->loan_period ?? 7;

        // Check if there's a reservation for this book
        $reservation = \App\Models\Reservation::where('book_id', $item->book_id)
            ->where('member_id', $this->activeMember->id)
            ->where('status', 'ready')
            ->first();
        
        if ($reservation) {
            $reservation->update(['status' => 'completed']);
        }

        Loan::create([
            'branch_id' => $item->branch_id,
            'member_id' => $this->activeMember->id,
            'item_id' => $item->id,
            'loan_date' => now(),
            'due_date' => now()->addDays($loanPeriod),
        ]);
        
        $item->update(['status' => 'borrowed']);

        $this->loadActiveLoans();
        $this->loadStats();
        $this->itemBarcode = '';

        $this->alert('success', 'Berhasil dipinjam!', Str::limit($item->book->title, 40));
    }

    public function returnItem(int $loanId): void
    {
        $loan = Loan::with('item.book')->find($loanId);

        if (!$loan) {
            $this->alert('error', 'Data tidak ditemukan');
            return;
        }

        $loan->update([
            'return_date' => now(),
            'is_returned' => true,
        ]);
        
        // Update item status
        $loan->item->update(['status' => 'available']);

        $bookTitle = Str::limit($loan->item->book->title ?? 'Buku', 35);

        if ($loan->due_date < now()) {
            $daysOverdue = now()->diffInDays($loan->due_date);
            $finePerDay = $this->activeMember->memberType->fine_per_day ?? 500;
            $fineAmount = $daysOverdue * $finePerDay;

            if ($fineAmount > 0) {
                Fine::create([
                    'loan_id' => $loan->id,
                    'member_id' => $loan->member_id,
                    'amount' => $fineAmount,
                    'description' => "Keterlambatan {$daysOverdue} hari",
                ]);
                $this->alert('warning', 'Dikembalikan dengan denda', "Rp " . number_format($fineAmount, 0, ',', '.') . " ({$daysOverdue} hari)");
            }
        } else {
            $this->alert('success', 'Berhasil dikembalikan!', $bookTitle);
        }
        
        // Process reservation queue and notify staff
        $nextReservation = \App\Models\Reservation::where('book_id', $loan->item->book_id)
            ->where('status', 'pending')
            ->orderBy('queue_position')
            ->with('member')
            ->first();
            
        if ($nextReservation) {
            app(\App\Services\Circulation\ReservationService::class)->processReturn($loan->item->book);
            $this->dispatch('showReservationAlert', [
                'memberName' => $nextReservation->member->name,
                'memberId' => $nextReservation->member->member_id,
                'bookTitle' => $bookTitle,
            ]);
        }

        $this->loadActiveLoans();
        $this->loadStats();
    }

    public function extendLoan(int $loanId): void
    {
        $loan = Loan::with('item.book')->find($loanId);

        if (!$loan) return;

        if ($loan->extend_count >= 2) {
            $this->alert('error', 'Batas perpanjangan tercapai', 'Maksimal 2x perpanjangan');
            return;
        }

        if ($loan->due_date < now()) {
            $this->alert('error', 'Tidak bisa perpanjang', 'Buku sudah terlambat');
            return;
        }

        $loanPeriod = $this->activeMember->memberType->loan_period ?? 7;

        $loan->update([
            'due_date' => $loan->due_date->addDays($loanPeriod),
            'extend_count' => $loan->extend_count + 1,
        ]);

        $this->loadActiveLoans();
        $this->alert('success', 'Perpanjangan berhasil!', "+{$loanPeriod} hari • " . Str::limit($loan->item->book->title ?? '', 25));
    }

    public bool $showReceipt = false;
    public $receiptData = null;

    public function endTransaction(): void
    {
        if ($this->activeMember && count($this->activeLoans) > 0) {
            $this->receiptData = [
                'member' => $this->activeMember,
                'loans' => $this->activeLoans,
                'date' => now(),
                'staff' => auth()->user()->name,
                'branch' => auth()->user()->branch->name ?? 'Perpustakaan',
            ];
            $this->showReceipt = true;
        } else {
            $this->closeTransaction();
        }
    }

    public function closeTransaction(): void
    {
        session()->forget('staff_circulation_member_id');
        $this->activeMember = null;
        $this->activeLoans = [];
        $this->memberBarcode = '';
        $this->itemBarcode = '';
        $this->showReceipt = false;
        $this->receiptData = null;
        $this->alert('info', 'Transaksi selesai', 'Siap untuk transaksi berikutnya');
    }

    public function quickReturn(int $loanId): void
    {
        $loan = Loan::with(['item.book', 'member.memberType'])->find($loanId);
        if (!$loan) return;

        $loan->update(['return_date' => now(), 'is_returned' => true]);
        $bookTitle = Str::limit($loan->item->book->title ?? 'Buku', 30);

        if ($loan->due_date < now()) {
            $daysOverdue = now()->diffInDays($loan->due_date);
            $finePerDay = $loan->member->memberType->fine_per_day ?? 500;
            $fineAmount = $daysOverdue * $finePerDay;
            if ($fineAmount > 0) {
                Fine::create([
                    'loan_id' => $loan->id,
                    'member_id' => $loan->member_id,
                    'amount' => $fineAmount,
                    'description' => "Keterlambatan {$daysOverdue} hari",
                ]);
                $this->alert('warning', 'Dikembalikan dengan denda', "Rp " . number_format($fineAmount, 0, ',', '.'));
            }
        } else {
            $this->alert('success', 'Berhasil dikembalikan!', $bookTitle);
        }

        $this->loadStats();
    }

    public function render()
    {
        $branchId = $this->branchId;
        
        $historyLoans = null;
        $overdueLoans = null;
        
        if ($this->tab === 'history') {
            $query = Loan::with(['member', 'item.book'])
                ->where('loan_date', '>=', now()->subDays($this->filterDays))
                ->when($this->searchHistory, fn($q) => $q->whereHas('member', fn($m) => $m->where('name', 'like', "%{$this->searchHistory}%")->orWhere('member_id', 'like', "%{$this->searchHistory}%")))
                ->orderByDesc('loan_date');
            
            if ($branchId) {
                $query->where('branch_id', $branchId);
            }
            
            $historyLoans = $query->paginate(15);
        }
        
        if ($this->tab === 'overdue') {
            $query = Loan::with(['member', 'item.book'])
                ->where('is_returned', false)
                ->where('due_date', '<', now())
                ->orderBy('due_date');
            
            if ($branchId) {
                $query->where('branch_id', $branchId);
            }
            
            $overdueLoans = $query->get();
        }

        return view('livewire.staff.circulation.transaction', [
            'historyLoans' => $historyLoans,
            'overdueLoans' => $overdueLoans,
        ])->extends('staff.layouts.app')->section('content');
    }
}
