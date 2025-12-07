<?php

namespace App\Filament\Pages;

use App\Models\Fine;
use App\Models\Item;
use App\Models\Loan;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class QuickReturn extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-arrow-uturn-left';
    protected static ?string $navigationGroup = 'Sirkulasi';
    protected static ?string $navigationLabel = 'Pengembalian Cepat';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.quick-return';

    public string $itemBarcode = '';
    public array $returnedItems = [];

    public function returnItem(): void
    {
        if (empty($this->itemBarcode)) return;

        $item = Item::where('barcode', $this->itemBarcode)->first();

        if (!$item) {
            Notification::make()->title('Item tidak ditemukan')->danger()->send();
            $this->itemBarcode = '';
            return;
        }

        $loan = Loan::with(['member', 'item.book'])
            ->where('item_id', $item->id)
            ->where('is_returned', false)
            ->first();

        if (!$loan) {
            Notification::make()->title('Item tidak sedang dipinjam')->warning()->send();
            $this->itemBarcode = '';
            return;
        }

        $loan->update([
            'return_date' => now(),
            'is_returned' => true,
        ]);

        $fineAmount = 0;
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
            }
        }

        array_unshift($this->returnedItems, [
            'barcode' => $item->barcode,
            'title' => $loan->item->book->title ?? '-',
            'member' => $loan->member->name,
            'member_id' => $loan->member->member_id,
            'loan_date' => $loan->loan_date->format('d/m/Y'),
            'due_date' => $loan->due_date->format('d/m/Y'),
            'return_date' => now()->format('d/m/Y H:i'),
            'overdue' => $loan->due_date < now(),
            'fine' => $fineAmount,
        ]);

        // Keep only last 20 items
        $this->returnedItems = array_slice($this->returnedItems, 0, 20);

        $message = $fineAmount > 0 
            ? "Dikembalikan dengan denda Rp " . number_format($fineAmount, 0, ',', '.')
            : "Pengembalian berhasil";

        Notification::make()
            ->title($message)
            ->body($loan->item->book->title ?? '')
            ->color($fineAmount > 0 ? 'warning' : 'success')
            ->send();

        $this->itemBarcode = '';
    }

    public function clearHistory(): void
    {
        $this->returnedItems = [];
    }
}
