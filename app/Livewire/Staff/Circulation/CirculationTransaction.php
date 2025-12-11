<?php

namespace App\Livewire\Staff\Circulation;

use App\Models\Item;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Fine;
use Livewire\Component;

class CirculationTransaction extends Component
{
    public ?string $memberBarcode = '';
    public ?string $itemBarcode = '';
    public ?Member $activeMember = null;
    public $activeLoans = [];
    
    // Stats
    public $todayLoans = 0;
    public $todayReturns = 0;
    public $overdueCount = 0;

    public function mount(): void
    {
        $this->loadFromSession();
        $this->loadTodayStats();
    }

    protected function loadFromSession(): void
    {
        if (session('staff_circulation_member_id')) {
            $this->activeMember = Member::with('memberType')->find(session('staff_circulation_member_id'));
            $this->loadActiveLoans();
        }
    }

    protected function loadTodayStats(): void
    {
        $branchId = auth()->user()->branch_id;
        
        $this->todayLoans = Loan::where('branch_id', $branchId)
            ->whereDate('loan_date', today())
            ->count();
            
        $this->todayReturns = Loan::where('branch_id', $branchId)
            ->whereDate('return_date', today())
            ->count();
            
        $this->overdueCount = Loan::where('branch_id', $branchId)
            ->where('is_returned', false)
            ->where('due_date', '<', now())
            ->count();
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

    public function startTransaction(): void
    {
        $this->memberBarcode = trim($this->memberBarcode);
        
        if (empty($this->memberBarcode)) {
            session()->flash('error', 'Masukkan nomor anggota');
            return;
        }

        $member = Member::with('memberType')
            ->where('member_id', $this->memberBarcode)
            ->orWhere('id', $this->memberBarcode)
            ->first();

        if (!$member) {
            session()->flash('error', 'Anggota tidak ditemukan');
            return;
        }

        if (!$member->is_active) {
            session()->flash('error', 'Keanggotaan tidak aktif');
            return;
        }

        $this->activeMember = $member;
        session(['staff_circulation_member_id' => $member->id]);
        $this->loadActiveLoans();
        $this->memberBarcode = '';

        if ($member->isExpired()) {
            session()->flash('warning', 'Perhatian: Keanggotaan sudah kadaluarsa');
        } else {
            session()->flash('success', "Transaksi dimulai: {$member->name}");
        }
    }

    public function loanItem(): void
    {
        if (!$this->activeMember) {
            session()->flash('error', 'Pilih anggota terlebih dahulu');
            return;
        }

        if ($this->activeMember->isExpired()) {
            session()->flash('error', 'Keanggotaan sudah kadaluarsa. Peminjaman tidak diizinkan.');
            return;
        }

        $this->itemBarcode = trim($this->itemBarcode);
        
        if (empty($this->itemBarcode)) {
            session()->flash('error', 'Masukkan barcode item');
            return;
        }

        $item = Item::withoutGlobalScopes()->with('book')->where('barcode', $this->itemBarcode)->first();

        if (!$item) {
            session()->flash('error', 'Item tidak ditemukan');
            return;
        }

        // Branch validation
        $currentBranchId = auth()->user()->branch_id;
        if ($currentBranchId && $item->branch_id !== $currentBranchId) {
            session()->flash('error', 'Item milik cabang lain. Tidak dapat dipinjam.');
            return;
        }

        if (!$item->isAvailable()) {
            session()->flash('error', 'Item tidak tersedia untuk dipinjam');
            return;
        }

        // Check loan limit
        $loanLimit = $this->activeMember->memberType->loan_limit ?? 3;
        $currentLoans = count($this->activeLoans);

        if ($currentLoans >= $loanLimit) {
            session()->flash('error', "Batas pinjam tercapai ({$loanLimit} buku)");
            return;
        }

        // Check if already borrowed
        $alreadyBorrowed = Loan::where('member_id', $this->activeMember->id)
            ->where('item_id', $item->id)
            ->where('is_returned', false)
            ->exists();

        if ($alreadyBorrowed) {
            session()->flash('warning', 'Item sudah dipinjam oleh anggota ini');
            return;
        }

        $loanPeriod = $this->activeMember->memberType->loan_period ?? 7;

        Loan::create([
            'branch_id' => $item->branch_id,
            'member_id' => $this->activeMember->id,
            'item_id' => $item->id,
            'loan_date' => now(),
            'due_date' => now()->addDays($loanPeriod),
        ]);

        $this->loadActiveLoans();
        $this->loadTodayStats();
        $this->itemBarcode = '';

        session()->flash('success', "Berhasil meminjam: {$item->book->title}");
    }

    public function returnItem(int $loanId): void
    {
        $loan = Loan::with('item.book')->find($loanId);

        if (!$loan) {
            session()->flash('error', 'Data peminjaman tidak ditemukan');
            return;
        }

        $loan->update([
            'return_date' => now(),
            'is_returned' => true,
        ]);

        $bookTitle = $loan->item->book->title ?? 'Buku';

        // Calculate fine if overdue
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

                session()->flash('warning', "Dikembalikan dengan denda Rp " . number_format($fineAmount, 0, ',', '.'));
            }
        } else {
            session()->flash('success', "Berhasil dikembalikan: {$bookTitle}");
        }

        $this->loadActiveLoans();
        $this->loadTodayStats();
    }

    public function extendLoan(int $loanId): void
    {
        $loan = Loan::find($loanId);

        if (!$loan) return;

        if ($loan->extend_count >= 2) {
            session()->flash('error', 'Batas perpanjangan tercapai (max 2x)');
            return;
        }

        if ($loan->due_date < now()) {
            session()->flash('error', 'Tidak bisa perpanjang, sudah terlambat');
            return;
        }

        $loanPeriod = $this->activeMember->memberType->loan_period ?? 7;

        $loan->update([
            'due_date' => $loan->due_date->addDays($loanPeriod),
            'extend_count' => $loan->extend_count + 1,
        ]);

        $this->loadActiveLoans();
        session()->flash('success', 'Perpanjangan berhasil (+' . $loanPeriod . ' hari)');
    }

    public function endTransaction(): void
    {
        session()->forget('staff_circulation_member_id');
        $this->activeMember = null;
        $this->activeLoans = [];
        $this->memberBarcode = '';
        $this->itemBarcode = '';

        session()->flash('success', 'Transaksi selesai');
    }

    public function render()
    {
        return view('livewire.staff.circulation.transaction')
            ->extends('staff.layouts.app')
            ->section('content');
    }
}
