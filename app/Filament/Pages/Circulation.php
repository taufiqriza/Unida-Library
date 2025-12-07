<?php

namespace App\Filament\Pages;

use App\Models\Item;
use App\Models\Loan;
use App\Models\Member;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Livewire\Attributes\On;

class Circulation extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';
    protected static ?string $navigationGroup = 'Sirkulasi';
    protected static ?string $navigationLabel = 'Transaksi';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.circulation';

    public ?string $memberBarcode = '';
    public ?string $itemBarcode = '';
    public ?Member $activeMember = null;
    public $activeLoans = [];

    public function mount(): void
    {
        $this->loadFromSession();
    }

    protected function loadFromSession(): void
    {
        if (session('circulation_member_id')) {
            $this->activeMember = Member::with('memberType')->find(session('circulation_member_id'));
            $this->loadActiveLoans();
        }
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
        $member = Member::where('member_id', $this->memberBarcode)
            ->orWhere('id', $this->memberBarcode)
            ->first();

        if (!$member) {
            Notification::make()->title('Anggota tidak ditemukan')->danger()->send();
            return;
        }

        if (!$member->is_active) {
            Notification::make()->title('Keanggotaan tidak aktif')->danger()->send();
            return;
        }

        if ($member->isExpired()) {
            Notification::make()->title('Keanggotaan sudah kadaluarsa')->warning()->send();
        }

        $this->activeMember = $member;
        session(['circulation_member_id' => $member->id]);
        $this->loadActiveLoans();
        $this->memberBarcode = '';

        Notification::make()->title("Transaksi dimulai: {$member->name}")->success()->send();
    }

    public function loanItem(): void
    {
        if (!$this->activeMember) {
            Notification::make()->title('Pilih anggota terlebih dahulu')->danger()->send();
            return;
        }

        $item = Item::with('book')->where('barcode', $this->itemBarcode)->first();

        if (!$item) {
            Notification::make()->title('Item tidak ditemukan')->danger()->send();
            return;
        }

        if (!$item->isAvailable()) {
            Notification::make()->title('Item tidak tersedia untuk dipinjam')->danger()->send();
            return;
        }

        // Check loan limit
        $loanLimit = $this->activeMember->memberType->loan_limit ?? 3;
        $currentLoans = $this->activeLoans->count();

        if ($currentLoans >= $loanLimit) {
            Notification::make()->title("Batas pinjam tercapai ({$loanLimit} buku)")->danger()->send();
            return;
        }

        // Check if already borrowed
        $alreadyBorrowed = Loan::where('member_id', $this->activeMember->id)
            ->where('item_id', $item->id)
            ->where('is_returned', false)
            ->exists();

        if ($alreadyBorrowed) {
            Notification::make()->title('Item sudah dipinjam oleh anggota ini')->warning()->send();
            return;
        }

        $loanPeriod = $this->activeMember->memberType->loan_period ?? 7;

        Loan::create([
            'branch_id' => auth()->user()->getCurrentBranchId() ?? $item->branch_id,
            'member_id' => $this->activeMember->id,
            'item_id' => $item->id,
            'loan_date' => now(),
            'due_date' => now()->addDays($loanPeriod),
        ]);

        $this->loadActiveLoans();
        $this->itemBarcode = '';

        Notification::make()
            ->title('Peminjaman berhasil')
            ->body("{$item->book->title}")
            ->success()
            ->send();
    }

    public function returnItem(int $loanId): void
    {
        $loan = Loan::find($loanId);

        if (!$loan) {
            Notification::make()->title('Data peminjaman tidak ditemukan')->danger()->send();
            return;
        }

        $loan->update([
            'return_date' => now(),
            'is_returned' => true,
        ]);

        // Calculate fine if overdue
        if ($loan->due_date < now()) {
            $daysOverdue = now()->diffInDays($loan->due_date);
            $finePerDay = $this->activeMember->memberType->fine_per_day ?? 500;
            $fineAmount = $daysOverdue * $finePerDay;

            if ($fineAmount > 0) {
                \App\Models\Fine::create([
                    'loan_id' => $loan->id,
                    'member_id' => $loan->member_id,
                    'amount' => $fineAmount,
                    'description' => "Keterlambatan {$daysOverdue} hari",
                ]);

                Notification::make()
                    ->title('Pengembalian dengan denda')
                    ->body("Denda: Rp " . number_format($fineAmount, 0, ',', '.'))
                    ->warning()
                    ->send();
            }
        } else {
            Notification::make()->title('Pengembalian berhasil')->success()->send();
        }

        $this->loadActiveLoans();
    }

    public function extendLoan(int $loanId): void
    {
        $loan = Loan::find($loanId);

        if (!$loan) return;

        if ($loan->extend_count >= 2) {
            Notification::make()->title('Batas perpanjangan tercapai (max 2x)')->danger()->send();
            return;
        }

        if ($loan->due_date < now()) {
            Notification::make()->title('Tidak bisa perpanjang, sudah terlambat')->danger()->send();
            return;
        }

        $loanPeriod = $this->activeMember->memberType->loan_period ?? 7;

        $loan->update([
            'due_date' => $loan->due_date->addDays($loanPeriod),
            'extend_count' => $loan->extend_count + 1,
        ]);

        $this->loadActiveLoans();
        Notification::make()->title('Perpanjangan berhasil')->success()->send();
    }

    public function endTransaction(): void
    {
        session()->forget('circulation_member_id');
        $this->activeMember = null;
        $this->activeLoans = [];
        $this->memberBarcode = '';
        $this->itemBarcode = '';

        Notification::make()->title('Transaksi selesai')->success()->send();
    }

    public static function getNavigationBadge(): ?string
    {
        return session('circulation_member_id') ? '●' : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
