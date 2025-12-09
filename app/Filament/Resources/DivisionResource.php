<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DivisionResource\Pages;
use App\Filament\Resources\DivisionResource\RelationManagers;
use App\Models\Division;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DivisionResource extends Resource
{
    protected static ?string $model = Division::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'Task Management';
    protected static ?string $navigationLabel = 'Divisi';
    protected static ?string $modelLabel = 'Divisi';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Divisi')
                ->icon('heroicon-o-building-office-2')
                ->schema([
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Divisi')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('code')
                            ->label('Kode')
                            ->maxLength(20)
                            ->placeholder('AUTO'),
                        Forms\Components\ColorPicker::make('color')
                            ->label('Warna')
                            ->default('#6366f1'),
                    ]),
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\Select::make('head_id')
                            ->label('Kepala Divisi')
                            ->relationship('head', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('branch_id')
                            ->label('Cabang')
                            ->relationship('branch', 'name')
                            ->default(1)
                            ->required(),
                    ]),
                    Forms\Components\Textarea::make('description')
                        ->label('Deskripsi')
                        ->rows(2)
                        ->columnSpanFull(),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ColorColumn::make('color')
                    ->label(''),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Divisi')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('head.name')
                    ->label('Kepala')
                    ->placeholder('Belum ditentukan'),
                Tables\Columns\TextColumn::make('users_count')
                    ->label('Anggota')
                    ->counts('users')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('projects_count')
                    ->label('Proyek')
                    ->counts('projects')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('tasks_count')
                    ->label('Tasks')
                    ->counts('tasks')
                    ->badge()
                    ->color('warning'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make()
                ->schema([
                    Infolists\Components\Grid::make(4)->schema([
                        Infolists\Components\ColorEntry::make('color')->label('Warna'),
                        Infolists\Components\TextEntry::make('name')->label('Nama')->weight('bold'),
                        Infolists\Components\TextEntry::make('code')->label('Kode')->badge(),
                        Infolists\Components\IconEntry::make('is_active')->label('Aktif')->boolean(),
                    ]),
                    Infolists\Components\TextEntry::make('description')->label('Deskripsi')->columnSpanFull(),
                    Infolists\Components\Grid::make(3)->schema([
                        Infolists\Components\TextEntry::make('head.name')->label('Kepala Divisi'),
                        Infolists\Components\TextEntry::make('users_count')->label('Jumlah Anggota')->state(fn ($record) => $record->users->count()),
                        Infolists\Components\TextEntry::make('projects_count')->label('Jumlah Proyek')->state(fn ($record) => $record->projects->count()),
                    ]),
                ]),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\UsersRelationManager::class,
            RelationManagers\ProjectsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDivisions::route('/'),
            'create' => Pages\CreateDivision::route('/create'),
            'view' => Pages\ViewDivision::route('/{record}'),
            'edit' => Pages\EditDivision::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count() ?: null;
    }
}
