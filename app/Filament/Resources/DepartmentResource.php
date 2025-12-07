<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DepartmentResource\Pages;
use App\Models\Department;
use App\Models\Faculty;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'E-Library';
    protected static ?string $navigationLabel = 'Program Studi';
    protected static ?string $modelLabel = 'Program Studi';
    protected static ?int $navigationSort = 11;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('faculty_id')->label('Fakultas')->options(Faculty::pluck('name', 'id'))->required()->searchable(),
            TextInput::make('name')->label('Nama Program Studi')->required()->maxLength(255),
            TextInput::make('code')->label('Kode')->maxLength(20),
            Select::make('degree')->label('Jenjang')->options(['S1' => 'S1', 'S2' => 'S2', 'S3' => 'S3', 'D3' => 'D3', 'D4' => 'D4'])->default('S1'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('faculty.name')->label('Fakultas')->sortable(),
                Tables\Columns\TextColumn::make('code')->label('Kode')->searchable(),
                Tables\Columns\TextColumn::make('name')->label('Program Studi')->searchable(),
                Tables\Columns\TextColumn::make('degree')->label('Jenjang')->badge(),
                Tables\Columns\TextColumn::make('etheses_count')->counts('etheses')->label('Thesis'),
            ])
            ->filters([Tables\Filters\SelectFilter::make('faculty_id')->label('Fakultas')->options(Faculty::pluck('name', 'id'))])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageDepartments::route('/')];
    }
}
