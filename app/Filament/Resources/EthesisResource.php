<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EthesisResource\Pages;
use App\Models\Department;
use App\Models\Ethesis;
use App\Models\Faculty;
use App\Models\Subject;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EthesisResource extends Resource
{
    protected static ?string $model = Ethesis::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'E-Library';
    protected static ?string $navigationLabel = 'E-Thesis';
    protected static ?string $modelLabel = 'E-Thesis';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make('Tabs')->tabs([
                Tabs\Tab::make('Informasi Utama')->icon('heroicon-o-document-text')->schema([
                    Select::make('type')->label('Jenis')->options([
                        'skripsi' => 'Skripsi (S1)',
                        'tesis' => 'Tesis (S2)',
                        'disertasi' => 'Disertasi (S3)',
                    ])->required()->default('skripsi'),
                    TextInput::make('title')->label('Judul')->required()->maxLength(500)->columnSpanFull(),
                    TextInput::make('title_en')->label('Judul (English)')->maxLength(500)->columnSpanFull(),
                    Textarea::make('abstract')->label('Abstrak')->required()->rows(5)->columnSpanFull(),
                    Textarea::make('abstract_en')->label('Abstract (English)')->rows(5)->columnSpanFull(),
                    TextInput::make('keywords')->label('Kata Kunci')->placeholder('pisahkan dengan koma')->columnSpanFull(),
                ]),
                Tabs\Tab::make('Penulis & Pembimbing')->icon('heroicon-o-users')->schema([
                    TextInput::make('author')->label('Nama Penulis')->required()->maxLength(255),
                    TextInput::make('nim')->label('NIM')->maxLength(50),
                    TextInput::make('advisor1')->label('Pembimbing 1')->required()->maxLength(255),
                    TextInput::make('advisor2')->label('Pembimbing 2')->maxLength(255),
                    TextInput::make('examiner1')->label('Penguji 1')->maxLength(255),
                    TextInput::make('examiner2')->label('Penguji 2')->maxLength(255),
                    TextInput::make('examiner3')->label('Penguji 3')->maxLength(255),
                ]),
                Tabs\Tab::make('Institusi')->icon('heroicon-o-building-library')->schema([
                    Select::make('faculty_id')->label('Fakultas')
                        ->options(Faculty::pluck('name', 'id'))
                        ->searchable()->live()->afterStateUpdated(fn($set) => $set('department_id', null)),
                    Select::make('department_id')->label('Program Studi')
                        ->options(fn(Get $get) => Department::where('faculty_id', $get('faculty_id'))->pluck('name', 'id'))
                        ->required()->searchable(),
                    TextInput::make('year')->label('Tahun')->numeric()->required()->default(date('Y'))->minValue(1900)->maxValue(2100),
                    DatePicker::make('defense_date')->label('Tanggal Sidang'),
                    Select::make('subjects')->label('Subjek')->relationship('subjects', 'name')->multiple()->searchable()->preload(),
                ]),
                Tabs\Tab::make('File & Akses')->icon('heroicon-o-document-arrow-up')->schema([
                    FileUpload::make('file_path')->label('File PDF')->directory('ethesis')->acceptedFileTypes(['application/pdf'])->maxSize(50000),
                    FileUpload::make('cover_path')->label('Cover')->directory('ethesis/covers')->image()->maxSize(2048),
                    TextInput::make('url')->label('URL Eksternal')->url(),
                    Toggle::make('is_public')->label('Tampil di OPAC')->default(true),
                    Toggle::make('is_fulltext_public')->label('Full-text Publik')->default(false)->helperText('Izinkan download PDF oleh publik'),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Judul')->limit(50)->searchable()->sortable(),
                Tables\Columns\TextColumn::make('author')->label('Penulis')->searchable(),
                Tables\Columns\TextColumn::make('department.name')->label('Prodi')->sortable(),
                Tables\Columns\TextColumn::make('year')->label('Tahun')->sortable(),
                Tables\Columns\TextColumn::make('type')->label('Jenis')->badge()
                    ->color(fn(string $state) => match($state) { 'skripsi' => 'info', 'tesis' => 'warning', 'disertasi' => 'success', default => 'gray' }),
                Tables\Columns\TextColumn::make('branch.name')->label('Perpustakaan')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_public')->label('Publik')->boolean(),
                Tables\Columns\TextColumn::make('views')->label('Views')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')->label('Jenis')->options(['skripsi' => 'Skripsi', 'tesis' => 'Tesis', 'disertasi' => 'Disertasi']),
                Tables\Filters\SelectFilter::make('department_id')->label('Prodi')->options(Department::pluck('name', 'id'))->searchable(),
                Tables\Filters\SelectFilter::make('year')->label('Tahun')->options(fn() => Ethesis::distinct()->pluck('year', 'year')->toArray()),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEtheses::route('/'),
            'create' => Pages\CreateEthesis::route('/create'),
            'edit' => Pages\EditEthesis::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count() ?: null;
    }
}
