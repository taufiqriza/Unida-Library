<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberTypeResource\Pages;
use App\Models\MemberType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MemberTypeResource extends Resource
{
    protected static ?string $model = MemberType::class;
    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationGroup = 'Keanggotaan';
    protected static ?string $navigationLabel = 'Tipe Anggota';
    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return auth('web')->user()?->isAdmin() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nama Tipe')
                ->required()
                ->maxLength(100),
            Forms\Components\TextInput::make('loan_limit')
                ->label('Batas Pinjam')
                ->numeric()
                ->default(3)
                ->suffix('buku'),
            Forms\Components\TextInput::make('loan_period')
                ->label('Lama Pinjam')
                ->numeric()
                ->default(7)
                ->suffix('hari'),
            Forms\Components\TextInput::make('fine_per_day')
                ->label('Denda per Hari')
                ->numeric()
                ->prefix('Rp')
                ->default(500),
            Forms\Components\TextInput::make('membership_period')
                ->label('Masa Keanggotaan')
                ->numeric()
                ->default(365)
                ->suffix('hari'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Tipe')->searchable(),
                Tables\Columns\TextColumn::make('loan_limit')->label('Batas Pinjam')->suffix(' buku'),
                Tables\Columns\TextColumn::make('loan_period')->label('Lama Pinjam')->suffix(' hari'),
                Tables\Columns\TextColumn::make('fine_per_day')->label('Denda/Hari')->money('IDR'),
                Tables\Columns\TextColumn::make('membership_period')->label('Masa Aktif')->suffix(' hari'),
                Tables\Columns\TextColumn::make('members_count')->label('Anggota')->counts('members'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageMemberTypes::route('/'),
        ];
    }
}
