<?php

namespace App\Filament\Pages;

use App\Models\Loan;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class OverdueLoans extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';
    protected static ?string $navigationGroup = 'Sirkulasi';
    protected static ?string $navigationLabel = 'Keterlambatan';
    protected static ?int $navigationSort = 4;
    protected static string $view = 'filament.pages.overdue-loans';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Loan::query()
                    ->with(['member.memberType', 'item.book'])
                    ->where('is_returned', false)
                    ->where('due_date', '<', now())
                    ->when(!auth('web')->user()->isSuperAdmin(), fn ($q) => $q->where('branch_id', auth('web')->user()->branch_id))
                    ->when(auth('web')->user()->isSuperAdmin() && session('current_branch_id'), fn ($q) => $q->where('branch_id', session('current_branch_id')))
            )
            ->columns([
                Tables\Columns\TextColumn::make('member.name')
                    ->label('Peminjam')
                    ->searchable()
                    ->description(fn ($record) => $record->member->member_id ?? ''),
                Tables\Columns\TextColumn::make('member.phone')
                    ->label('Telepon')
                    ->copyable(),
                Tables\Columns\TextColumn::make('item.book.title')
                    ->label('Judul')
                    ->limit(30),
                Tables\Columns\TextColumn::make('item.barcode')
                    ->label('Barcode')
                    ->fontFamily('mono'),
                Tables\Columns\TextColumn::make('loan_date')
                    ->label('Tgl Pinjam')
                    ->date('d/m/Y'),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d/m/Y')
                    ->color('danger'),
                Tables\Columns\TextColumn::make('days_overdue')
                    ->label('Terlambat')
                    ->state(fn ($record) => now()->diffInDays($record->due_date) . ' hari')
                    ->badge()
                    ->color('danger'),
                Tables\Columns\TextColumn::make('estimated_fine')
                    ->label('Est. Denda')
                    ->state(function ($record) {
                        $days = now()->diffInDays($record->due_date);
                        $fine = $days * ($record->member->memberType->fine_per_day ?? 500);
                        return 'Rp ' . number_format($fine, 0, ',', '.');
                    })
                    ->color('danger'),
            ])
            ->defaultSort('due_date', 'asc')
            ->actions([
                Tables\Actions\Action::make('return')
                    ->label('Kembalikan')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalDescription(fn ($record) => "Kembalikan buku dengan denda keterlambatan?")
                    ->action(function ($record) {
                        $daysOverdue = now()->diffInDays($record->due_date);
                        $finePerDay = $record->member->memberType->fine_per_day ?? 500;
                        $fineAmount = $daysOverdue * $finePerDay;

                        $record->update([
                            'return_date' => now(),
                            'is_returned' => true,
                        ]);

                        if ($fineAmount > 0) {
                            \App\Models\Fine::create([
                                'loan_id' => $record->id,
                                'member_id' => $record->member_id,
                                'amount' => $fineAmount,
                                'description' => "Keterlambatan {$daysOverdue} hari",
                            ]);
                        }
                    }),
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        $query = Loan::where('is_returned', false)->where('due_date', '<', now());
        
        if (!auth('web')->user()?->isSuperAdmin()) {
            $query->where('branch_id', auth('web')->user()->branch_id);
        } elseif (session('current_branch_id')) {
            $query->where('branch_id', session('current_branch_id'));
        }
        
        $count = $query->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }
}
