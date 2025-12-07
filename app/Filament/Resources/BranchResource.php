<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BranchResource\Pages;
use App\Models\Branch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationGroup = 'Perpustakaan';
    protected static ?string $navigationLabel = 'Cabang';
    protected static ?string $modelLabel = 'Cabang';
    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Cabang')
                ->schema([
                    Forms\Components\TextInput::make('code')
                        ->label('Kode Cabang')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(20),
                    Forms\Components\TextInput::make('name')
                        ->label('Nama Cabang')
                        ->required()
                        ->maxLength(100),
                    Forms\Components\Textarea::make('address')
                        ->label('Alamat')
                        ->rows(2)
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('city')
                        ->label('Kota'),
                    Forms\Components\TextInput::make('phone')
                        ->label('Telepon')
                        ->tel(),
                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email(),
                    Forms\Components\Toggle::make('is_main')
                        ->label('Cabang Utama'),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Cabang')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('Kota'),
                Tables\Columns\IconColumn::make('is_main')
                    ->label('Utama')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('books_count')
                    ->label('Koleksi')
                    ->counts('books')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('items_count')
                    ->label('Eksemplar')
                    ->counts('items')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('members_count')
                    ->label('Anggota')
                    ->counts('members')
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('loans_count')
                    ->label('Pinjaman')
                    ->counts('loans')
                    ->badge()
                    ->color('danger'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->isSuperAdmin()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->isSuperAdmin()),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Informasi Cabang')
                ->schema([
                    Infolists\Components\TextEntry::make('code')->label('Kode'),
                    Infolists\Components\TextEntry::make('name')->label('Nama'),
                    Infolists\Components\TextEntry::make('address')->label('Alamat')->columnSpanFull(),
                    Infolists\Components\TextEntry::make('city')->label('Kota'),
                    Infolists\Components\TextEntry::make('phone')->label('Telepon'),
                    Infolists\Components\TextEntry::make('email')->label('Email'),
                    Infolists\Components\IconEntry::make('is_main')->label('Cabang Utama')->boolean(),
                    Infolists\Components\IconEntry::make('is_active')->label('Aktif')->boolean(),
                ])->columns(2),
            Infolists\Components\Section::make('Statistik')
                ->schema([
                    Infolists\Components\TextEntry::make('books_count')
                        ->label('Total Koleksi')
                        ->state(fn ($record) => $record->books()->count() . ' judul'),
                    Infolists\Components\TextEntry::make('items_count')
                        ->label('Total Eksemplar')
                        ->state(fn ($record) => $record->items()->count() . ' item'),
                    Infolists\Components\TextEntry::make('members_count')
                        ->label('Total Anggota')
                        ->state(fn ($record) => $record->members()->count() . ' orang'),
                    Infolists\Components\TextEntry::make('active_loans')
                        ->label('Pinjaman Aktif')
                        ->state(fn ($record) => $record->loans()->where('is_returned', false)->count() . ' pinjaman'),
                ])->columns(4),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBranches::route('/'),
            'create' => Pages\CreateBranch::route('/create'),
            'view' => Pages\ViewBranch::route('/{record}'),
            'edit' => Pages\EditBranch::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count() ?: null;
    }
}
