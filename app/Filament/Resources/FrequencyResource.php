<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FrequencyResource\Pages;
use App\Models\Frequency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FrequencyResource extends Resource
{
    protected static ?string $model = Frequency::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Frekuensi Terbitan';
    protected static ?int $navigationSort = 14;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nama')
                ->required()
                ->maxLength(100),
            Forms\Components\TextInput::make('time_increment')
                ->label('Interval')
                ->numeric(),
            Forms\Components\Select::make('time_unit')
                ->label('Satuan Waktu')
                ->options([
                    'day' => 'Hari',
                    'week' => 'Minggu',
                    'month' => 'Bulan',
                    'year' => 'Tahun',
                ]),
            Forms\Components\TextInput::make('language_prefix')
                ->label('Prefix Bahasa')
                ->maxLength(5),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama')->searchable(),
                Tables\Columns\TextColumn::make('time_increment')->label('Interval'),
                Tables\Columns\TextColumn::make('time_unit')->label('Satuan'),
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
            'index' => Pages\ManageFrequencies::route('/'),
        ];
    }
}
