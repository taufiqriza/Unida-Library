<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanResource\Pages;
use App\Models\Loan;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LoanResource extends Resource
{
    protected static ?string $model = Loan::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Sirkulasi';
    protected static ?string $navigationLabel = 'Riwayat Peminjaman';
    protected static ?string $modelLabel = 'Peminjaman';
    protected static ?int $navigationSort = 3;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Cabang')
                    ->badge()
                    ->visible(fn () => auth('admin')->user()?->isSuperAdmin() && !session('current_branch_id')),
                Tables\Columns\TextColumn::make('member.name')
                    ->label('Peminjam')
                    ->searchable()
                    ->description(fn ($record) => $record->member->member_id ?? ''),
                Tables\Columns\TextColumn::make('item.barcode')
                    ->label('Barcode')
                    ->searchable()
                    ->fontFamily('mono')
                    ->size('xs'),
                Tables\Columns\TextColumn::make('item.book.title')
                    ->label('Judul')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('loan_date')
                    ->label('Tgl Pinjam')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($record) => !$record->is_returned && $record->due_date < now() ? 'danger' : null),
                Tables\Columns\TextColumn::make('return_date')
                    ->label('Tgl Kembali')
                    ->date('d/m/Y')
                    ->placeholder('-'),
                Tables\Columns\IconColumn::make('is_returned')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning'),
                Tables\Columns\TextColumn::make('extend_count')
                    ->label('Perpanjangan')
                    ->badge()
                    ->color('gray'),
            ])
            ->defaultSort('loan_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('is_returned')
                    ->label('Status')
                    ->options([
                        '0' => 'Sedang Dipinjam',
                        '1' => 'Sudah Dikembalikan',
                    ]),
                Tables\Filters\Filter::make('overdue')
                    ->label('Terlambat')
                    ->query(fn ($query) => $query->where('is_returned', false)->where('due_date', '<', now())),
            ])
            ->actions([
                Tables\Actions\Action::make('return')
                    ->label('Kembalikan')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => !$record->is_returned)
                    ->action(function ($record) {
                        $record->update([
                            'return_date' => now(),
                            'is_returned' => true,
                        ]);

                        if ($record->due_date < now()) {
                            $daysOverdue = now()->diffInDays($record->due_date);
                            $finePerDay = $record->member->memberType->fine_per_day ?? 500;
                            $fineAmount = $daysOverdue * $finePerDay;

                            if ($fineAmount > 0) {
                                \App\Models\Fine::create([
                                    'loan_id' => $record->id,
                                    'member_id' => $record->member_id,
                                    'amount' => $fineAmount,
                                    'description' => "Keterlambatan {$daysOverdue} hari",
                                ]);
                            }
                        }
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLoans::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_returned', false)->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
