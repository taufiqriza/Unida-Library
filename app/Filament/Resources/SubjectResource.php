<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubjectResource\Pages;
use App\Models\Subject;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $modelLabel = 'Subjek';
    protected static ?string $pluralModelLabel = 'Subjek';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nama Subjek')
                ->required()
                ->maxLength(255),
            Forms\Components\Select::make('type')
                ->label('Tipe')
                ->options([
                    'topic' => 'Topik (t)',
                    'geographic' => 'Geografis (g)',
                    'name' => 'Nama (n)',
                    'temporal' => 'Temporal (tm)',
                    'genre' => 'Genre (gr)',
                    'occupation' => 'Pekerjaan (oc)',
                ])
                ->default('topic'),
            Forms\Components\TextInput::make('classification')
                ->label('Klasifikasi')
                ->maxLength(40),
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
                    ->label('Subjek')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge(),
                Tables\Columns\TextColumn::make('classification')
                    ->label('Klasifikasi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('books_count')
                    ->label('Jumlah Buku')
                    ->counts('books')
                    ->badge()
                    ->color('success'),
            ])
            ->defaultSort('name')
            ->filters([])
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
            'index' => Pages\ManageSubjects::route('/'),
        ];
    }
}
