<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MediaTypeResource\Pages;
use App\Models\MediaType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MediaTypeResource extends Resource
{
    protected static ?string $model = MediaType::class;
    protected static ?string $navigationIcon = 'heroicon-o-film';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $modelLabel = 'GMD';
    protected static ?string $pluralModelLabel = 'GMD';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nama GMD')
                ->required()
                ->maxLength(100),
            Forms\Components\TextInput::make('code')
                ->label('Kode')
                ->maxLength(10),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('books_count')
                    ->label('Jumlah Buku')
                    ->counts('books')
                    ->badge()
                    ->color('success'),
            ])
            ->defaultSort('name')
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
            'index' => Pages\ManageMediaTypes::route('/'),
        ];
    }
}
