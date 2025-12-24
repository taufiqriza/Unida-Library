<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EbookResource\Pages;
use App\Models\Ebook;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EbookResource extends Resource
{
    protected static ?string $model = Ebook::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'E-Library';
    protected static ?string $navigationLabel = 'E-Book';
    protected static ?string $modelLabel = 'E-Book';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Ebook')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('Informasi Utama')
                        ->icon('heroicon-o-information-circle')
                        ->schema([
                            Forms\Components\TextInput::make('title')
                                ->label('Judul')
                                ->required()
                                ->maxLength(500)
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('sor')
                                ->label('Pernyataan Tanggung Jawab')
                                ->maxLength(200)
                                ->columnSpanFull(),
                            Forms\Components\Select::make('authors')
                                ->label('Penulis')
                                ->relationship('authors', 'name')
                                ->multiple()
                                ->searchable()
                                ->preload()
                                ->createOptionForm([
                                    Forms\Components\TextInput::make('name')->required(),
                                    Forms\Components\Select::make('type')
                                        ->options(['personal' => 'Personal', 'organizational' => 'Organizational'])
                                        ->default('personal'),
                                ]),
                            Forms\Components\Select::make('subjects')
                                ->label('Subjek')
                                ->relationship('subjects', 'name')
                                ->multiple()
                                ->searchable()
                                ->preload(),
                            Forms\Components\Select::make('publisher_id')
                                ->label('Penerbit')
                                ->relationship('publisher', 'name')
                                ->searchable()
                                ->preload()
                                ->createOptionForm([
                                    Forms\Components\TextInput::make('name')->required(),
                                ]),
                            Forms\Components\TextInput::make('publish_year')
                                ->label('Tahun Terbit')
                                ->maxLength(4),
                            Forms\Components\TextInput::make('isbn')
                                ->label('ISBN')
                                ->maxLength(20),
                            Forms\Components\TextInput::make('edition')
                                ->label('Edisi'),
                        ])->columns(2),

                    Forms\Components\Tabs\Tab::make('File & Format')
                        ->icon('heroicon-o-document-arrow-up')
                        ->schema([
                            Forms\Components\Radio::make('file_source')
                                ->label('Sumber File')
                                ->options([
                                    'local' => 'Upload Lokal',
                                    'google_drive' => 'Google Drive',
                                ])
                                ->default('local')
                                ->inline()
                                ->live()
                                ->columnSpanFull(),

                            // Local Upload
                            Forms\Components\FileUpload::make('file_path')
                                ->label('File E-Book')
                                ->directory('ebooks')
                                ->acceptedFileTypes(['application/pdf', 'application/epub+zip'])
                                ->maxSize(102400)
                                ->columnSpanFull()
                                ->visible(fn (Get $get) => $get('file_source') === 'local'),

                            // Google Drive
                            Forms\Components\TextInput::make('google_drive_url')
                                ->label('Google Drive Link')
                                ->placeholder('https://drive.google.com/file/d/FILE_ID/view?usp=sharing')
                                ->helperText('Paste link share dari Google Drive. Pastikan file sudah di-share "Anyone with the link".')
                                ->columnSpanFull()
                                ->visible(fn (Get $get) => $get('file_source') === 'google_drive')
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                    if ($state) {
                                        $fileId = Ebook::extractGoogleDriveId($state);
                                        $set('google_drive_id', $fileId);
                                    }
                                }),

                            Forms\Components\TextInput::make('google_drive_id')
                                ->label('Google Drive File ID')
                                ->disabled()
                                ->dehydrated()
                                ->helperText('Otomatis diisi dari link di atas')
                                ->visible(fn (Get $get) => $get('file_source') === 'google_drive'),

                            Forms\Components\FileUpload::make('cover_image')
                                ->label('Cover')
                                ->image()
                                ->directory('ebook-covers')
                                ->imageResizeMode('cover')
                                ->imageCropAspectRatio('2:3'),
                            Forms\Components\Select::make('file_format')
                                ->label('Format')
                                ->options([
                                    'PDF' => 'PDF',
                                    'EPUB' => 'EPUB',
                                    'MOBI' => 'MOBI',
                                ]),
                            Forms\Components\TextInput::make('pages')
                                ->label('Jumlah Halaman'),
                            Forms\Components\TextInput::make('file_size')
                                ->label('Ukuran File'),
                            Forms\Components\Select::make('language')
                                ->label('Bahasa')
                                ->options([
                                    'id' => 'Indonesia',
                                    'en' => 'English',
                                    'ar' => 'Arabic',
                                ])
                                ->default('id'),
                        ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Klasifikasi')
                        ->icon('heroicon-o-tag')
                        ->schema([
                            Forms\Components\TextInput::make('classification')
                                ->label('No. Klasifikasi'),
                            Forms\Components\TextInput::make('call_number')
                                ->label('No. Panggil'),
                            Forms\Components\Select::make('media_type_id')
                                ->label('GMD')
                                ->relationship('mediaType', 'name'),
                            Forms\Components\Select::make('content_type_id')
                                ->label('Content Type')
                                ->relationship('contentType', 'name'),
                        ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Abstrak')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Forms\Components\Textarea::make('abstract')
                                ->label('Abstrak/Ringkasan')
                                ->rows(6)
                                ->columnSpanFull(),
                        ]),

                    Forms\Components\Tabs\Tab::make('Pengaturan')
                        ->icon('heroicon-o-cog-6-tooth')
                        ->schema([
                            Forms\Components\Select::make('access_type')
                                ->label('Tipe Akses')
                                ->options([
                                    'public' => 'Publik (Semua orang)',
                                    'member' => 'Member (Anggota saja)',
                                    'restricted' => 'Terbatas (Perlu izin)',
                                ])
                                ->default('member'),
                            Forms\Components\Toggle::make('is_downloadable')
                                ->label('Boleh Download')
                                ->default(false),
                            Forms\Components\Toggle::make('is_active')
                                ->label('Aktif')
                                ->default(true),
                            Forms\Components\Toggle::make('opac_hide')
                                ->label('Sembunyikan dari OPAC'),
                        ])->columns(2),
                ])
                ->columnSpanFull()
                ->persistTabInQueryString(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')
                    ->label('Cover')
                    ->height(60)
                    ->defaultImageUrl(fn () => 'https://placehold.co/40x60?text=No+Cover'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->description(fn ($record) => $record->authors->pluck('name')->implode(', ')),
                Tables\Columns\TextColumn::make('file_source')
                    ->label('Sumber')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'google_drive' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => $state === 'google_drive' ? 'G-Drive' : 'Lokal'),
                Tables\Columns\TextColumn::make('file_format')
                    ->label('Format')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'PDF' => 'danger',
                        'EPUB' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('publish_year')
                    ->label('Tahun')
                    ->sortable(),
                Tables\Columns\TextColumn::make('access_type')
                    ->label('Akses')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'public' => 'success',
                        'member' => 'primary',
                        'restricted' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('branch.name')->label('Perpustakaan')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('file_source')
                    ->label('Sumber File')
                    ->options(['local' => 'Lokal', 'google_drive' => 'Google Drive']),
                Tables\Filters\SelectFilter::make('file_format')
                    ->label('Format')
                    ->options(['PDF' => 'PDF', 'EPUB' => 'EPUB']),
                Tables\Filters\SelectFilter::make('access_type')
                    ->label('Akses')
                    ->options([
                        'public' => 'Publik',
                        'member' => 'Member',
                        'restricted' => 'Terbatas',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('preview')
                    ->label('Preview')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => $record->viewer_url)
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => $record->viewer_url),
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => $record->download_url)
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => $record->download_url),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListEbooks::route('/'),
            'create' => Pages\CreateEbook::route('/create'),
            'edit' => Pages\EditEbook::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count() ?: null;
    }
}
