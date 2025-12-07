<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ItemStatusResource\Pages;
use App\Models\ItemStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ItemStatusResource extends Resource
{
    protected static ?string $model = ItemStatus::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Status Eksemplar';
    protected static ?int $navigationSort = 12;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nama Status')
                ->required()
                ->maxLength(100),
            Forms\Components\TextInput::make('code')
                ->label('Kode')
                ->maxLength(10),
            Forms\Components\Textarea::make('rules')
                ->label('Aturan')
                ->rows(3),
            Forms\Components\Toggle::make('no_loan')
                ->label('Tidak Dapat Dipinjam'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->label('Kode')->searchable(),
                Tables\Columns\TextColumn::make('name')->label('Nama')->searchable(),
                Tables\Columns\IconColumn::make('no_loan')->label('Tidak Dipinjam')->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageItemStatuses::route('/'),
        ];
    }
}
