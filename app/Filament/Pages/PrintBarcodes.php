<?php

namespace App\Filament\Pages;

use App\Models\Item;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class PrintBarcodes extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-qr-code';
    protected static ?string $navigationGroup = 'Katalog';
    protected static ?string $navigationLabel = 'Cetak Barcode';
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.pages.print-barcodes';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Item::query()
                    ->with(['book', 'location'])
                    ->when(!auth('web')->user()->isSuperAdmin(), fn ($q) => $q->where('branch_id', auth('web')->user()->branch_id))
                    ->when(auth('web')->user()->isSuperAdmin() && session('current_branch_id'), fn ($q) => $q->where('branch_id', session('current_branch_id')))
            )
            ->columns([
                Tables\Columns\TextColumn::make('barcode')
                    ->label('Barcode')
                    ->searchable()
                    ->fontFamily('mono'),
                Tables\Columns\TextColumn::make('book.title')
                    ->label('Judul')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('call_number')
                    ->label('No. Panggil'),
                Tables\Columns\TextColumn::make('location.name')
                    ->label('Lokasi'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('location_id')
                    ->label('Lokasi')
                    ->relationship('location', 'name'),
            ])
            ->actions([
                Tables\Actions\Action::make('print')
                    ->label('Cetak')
                    ->icon('heroicon-o-printer')
                    ->url(fn ($record) => route('print.barcode', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('printSelected')
                    ->label('Cetak Barcode')
                    ->icon('heroicon-o-printer')
                    ->action(function ($records) {
                        $ids = $records->pluck('id')->join(',');
                        return redirect()->away(route('print.barcodes', ['ids' => $ids]));
                    }),
                Tables\Actions\BulkAction::make('printLabels')
                    ->label('Cetak Label')
                    ->icon('heroicon-o-tag')
                    ->action(function ($records) {
                        $ids = $records->pluck('id')->join(',');
                        return redirect()->away(route('print.labels', ['ids' => $ids]));
                    }),
            ]);
    }
}
