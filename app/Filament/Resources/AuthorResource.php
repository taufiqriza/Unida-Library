<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuthorResource\Pages;
use App\Models\Author;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AuthorResource extends Resource
{
    protected static ?string $model = Author::class;
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $modelLabel = 'Penulis';
    protected static ?string $pluralModelLabel = 'Penulis';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nama Penulis')
                ->required()
                ->maxLength(255),
            Forms\Components\Select::make('type')
                ->label('Tipe Kepengarangan')
                ->options([
                    'personal' => 'Personal Name (p)',
                    'organizational' => 'Organizational Body (o)',
                    'conference' => 'Conference (c)',
                ])
                ->default('personal'),
            Forms\Components\TextInput::make('authority_file')
                ->label('Authority File')
                ->maxLength(20),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'personal' => 'Personal',
                        'organizational' => 'Organisasi',
                        'conference' => 'Konferensi',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('books_count')
                    ->label('Jumlah Buku')
                    ->counts('books')
                    ->badge()
                    ->color('success'),
            ])
            ->defaultSort('name')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'personal' => 'Personal',
                        'organizational' => 'Organisasi',
                        'conference' => 'Konferensi',
                    ]),
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
            'index' => Pages\ManageAuthors::route('/'),
        ];
    }
}
