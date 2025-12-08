<?php

namespace App\Filament\Pages;

use App\Models\Member;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class PrintMemberCards extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-printer';
    protected static ?string $navigationGroup = 'Keanggotaan';
    protected static ?string $navigationLabel = 'Cetak Kartu';
    protected static ?int $navigationSort = 4;
    protected static string $view = 'filament.pages.print-member-cards';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Member::query()
                    ->where('is_active', true)
                    ->when(!auth('web')->user()->isSuperAdmin(), fn ($q) => $q->where('branch_id', auth('web')->user()->branch_id))
                    ->when(auth('web')->user()->isSuperAdmin() && session('current_branch_id'), fn ($q) => $q->where('branch_id', session('current_branch_id')))
            )
            ->columns([
                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Cabang')
                    ->badge()
                    ->visible(fn () => auth('web')->user()?->isSuperAdmin() && !session('current_branch_id')),
                Tables\Columns\ImageColumn::make('photo')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name)),
                Tables\Columns\TextColumn::make('member_id')
                    ->label('No. Anggota')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('memberType.name')
                    ->label('Tipe')
                    ->badge(),
                Tables\Columns\TextColumn::make('expire_date')
                    ->label('Berlaku s/d')
                    ->date('d M Y'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('member_type_id')
                    ->label('Tipe')
                    ->relationship('memberType', 'name'),
            ])
            ->actions([
                Tables\Actions\Action::make('print')
                    ->label('Cetak')
                    ->icon('heroicon-o-printer')
                    ->url(fn ($record) => route('member.card', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('printSelected')
                    ->label('Cetak Terpilih')
                    ->icon('heroicon-o-printer')
                    ->action(function ($records) {
                        $ids = $records->pluck('id')->join(',');
                        return redirect()->away(route('member.cards', ['ids' => $ids]));
                    })
                    ->deselectRecordsAfterCompletion(),
            ]);
    }
}
