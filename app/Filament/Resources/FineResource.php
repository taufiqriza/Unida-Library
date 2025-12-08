<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FineResource\Pages;
use App\Models\Fine;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FineResource extends Resource
{
    protected static ?string $model = Fine::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Sirkulasi';
    protected static ?string $navigationLabel = 'Denda';
    protected static ?string $modelLabel = 'Denda';
    protected static ?int $navigationSort = 5;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('paid_amount')
                ->label('Jumlah Bayar')
                ->numeric()
                ->prefix('Rp')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Fine::query()
                    ->with(['member', 'loan.item.book'])
                    ->when(!auth('web')->user()?->isSuperAdmin(), fn ($q) => 
                        $q->whereHas('loan', fn ($q2) => $q2->where('branch_id', auth('web')->user()?->branch_id))
                    )
            )
            ->columns([
                Tables\Columns\TextColumn::make('member.name')
                    ->label('Anggota')
                    ->searchable()
                    ->description(fn ($record) => $record->member->member_id ?? ''),
                Tables\Columns\TextColumn::make('loan.item.book.title')
                    ->label('Judul Buku')
                    ->limit(30),
                Tables\Columns\TextColumn::make('description')
                    ->label('Keterangan'),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Total Denda')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('paid_amount')
                    ->label('Dibayar')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('remaining')
                    ->label('Sisa')
                    ->state(fn ($record) => $record->amount - $record->paid_amount)
                    ->money('IDR')
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success'),
                Tables\Columns\IconColumn::make('is_paid')
                    ->label('Lunas')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_paid')
                    ->label('Status')
                    ->trueLabel('Lunas')
                    ->falseLabel('Belum Lunas'),
            ])
            ->actions([
                Tables\Actions\Action::make('pay')
                    ->label('Bayar')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->visible(fn ($record) => !$record->is_paid)
                    ->form([
                        Forms\Components\TextInput::make('pay_amount')
                            ->label('Jumlah Bayar')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(fn ($record) => $record->amount - $record->paid_amount)
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $newPaid = $record->paid_amount + $data['pay_amount'];
                        $record->update([
                            'paid_amount' => $newPaid,
                            'is_paid' => $newPaid >= $record->amount,
                        ]);
                    }),
                Tables\Actions\Action::make('payFull')
                    ->label('Lunas')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => !$record->is_paid)
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update([
                        'paid_amount' => $record->amount,
                        'is_paid' => true,
                    ])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFines::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Fine::where('is_paid', false)->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
