<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookResource\Pages;
use App\Models\Book;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Katalog';
    protected static ?string $modelLabel = 'Bibliografi';
    protected static ?string $pluralModelLabel = 'Bibliografi';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'title';

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'isbn', 'call_number'];
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'ISBN' => $record->isbn ?? '-',
            'Call Number' => $record->call_number ?? '-',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Bibliography')
                ->tabs([
                    // Tab 1: Informasi Utama (Primary Info - SLiMS style)
                    Forms\Components\Tabs\Tab::make('Informasi Utama')
                        ->icon('heroicon-o-information-circle')
                        ->schema([
                            Forms\Components\Select::make('branch_id')
                                ->label('Lokasi')
                                ->relationship('branch', 'name')
                                ->required()
                                ->searchable()
                                ->preload()
                                ->default(1),
                            Forms\Components\Select::make('media_type_id')
                                ->label('GMD')
                                ->relationship('mediaType', 'name')
                                ->searchable()
                                ->preload()
                                ->helperText('General Material Designation')
                                ->createOptionForm([
                                    Forms\Components\TextInput::make('name')->required(),
                                    Forms\Components\TextInput::make('code')->maxLength(10),
                                ]),
                            Forms\Components\Select::make('content_type_id')
                                ->label('Content Type')
                                ->relationship('contentType', 'name')
                                ->searchable()
                                ->helperText('RDA Content Type'),
                            Forms\Components\Select::make('carrier_type_id')
                                ->label('Carrier Type')
                                ->relationship('carrierType', 'name')
                                ->searchable()
                                ->helperText('RDA Carrier Type'),
                            Forms\Components\TextInput::make('title')
                                ->label('Judul')
                                ->required()
                                ->maxLength(500)
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('sor')
                                ->label('Pernyataan Tanggung Jawab')
                                ->helperText('Statement of Responsibility - pengarang seperti tertulis di buku')
                                ->maxLength(200)
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('edition')
                                ->label('Edisi'),
                            Forms\Components\TextInput::make('spec_detail_info')
                                ->label('Info Detail Khusus')
                                ->helperText('Untuk terbitan berseri: Vol, No'),
                        ])->columns(4),

                    // Tab 2: Penulis & Subjek (Authors & Subjects)
                    Forms\Components\Tabs\Tab::make('Penulis & Subjek')
                        ->icon('heroicon-o-users')
                        ->schema([
                            Forms\Components\Select::make('authors')
                                ->label('Penulis')
                                ->relationship('authors', 'name')
                                ->multiple()
                                ->searchable()
                                ->preload()
                                ->helperText('Tambahkan penulis utama dan tambahan')
                                ->createOptionForm([
                                    Forms\Components\TextInput::make('name')->label('Nama Penulis')->required(),
                                    Forms\Components\Select::make('type')
                                        ->label('Tipe Kepengarangan')
                                        ->options([
                                            'personal' => 'Personal Name (p)',
                                            'organizational' => 'Organizational Body (o)',
                                            'conference' => 'Conference (c)',
                                        ])
                                        ->default('personal'),
                                    Forms\Components\TextInput::make('authority_file')
                                        ->label('Authority File'),
                                ]),
                            Forms\Components\Select::make('subjects')
                                ->label('Subjek/Topik')
                                ->relationship('subjects', 'name')
                                ->multiple()
                                ->searchable()
                                ->preload()
                                ->helperText('Subjek atau topik buku')
                                ->createOptionForm([
                                    Forms\Components\TextInput::make('name')->label('Subjek')->required(),
                                    Forms\Components\Select::make('type')
                                        ->label('Tipe Subjek')
                                        ->options([
                                            'topic' => 'Topik (t)',
                                            'geographic' => 'Geografis (g)',
                                            'name' => 'Nama (n)',
                                            'temporal' => 'Temporal (tm)',
                                            'genre' => 'Genre (gr)',
                                        ])
                                        ->default('topic'),
                                    Forms\Components\TextInput::make('classification')
                                        ->label('Klasifikasi'),
                                ]),
                        ])->columns(2),

                    // Tab 3: Penerbitan (Publishing Info)
                    Forms\Components\Tabs\Tab::make('Penerbitan')
                        ->icon('heroicon-o-building-office')
                        ->schema([
                            Forms\Components\Select::make('publisher_id')
                                ->label('Penerbit')
                                ->relationship('publisher', 'name')
                                ->searchable()
                                ->preload()
                                ->createOptionForm([
                                    Forms\Components\TextInput::make('name')->label('Nama Penerbit')->required(),
                                    Forms\Components\TextInput::make('city')->label('Kota'),
                                ]),
                            Forms\Components\Select::make('place_id')
                                ->label('Tempat Terbit')
                                ->relationship('place', 'name')
                                ->searchable()
                                ->createOptionForm([
                                    Forms\Components\TextInput::make('name')->required(),
                                ]),
                            Forms\Components\TextInput::make('publish_year')
                                ->label('Tahun Terbit')
                                ->maxLength(4)
                                ->numeric(),
                            Forms\Components\TextInput::make('collation')
                                ->label('Kolasi')
                                ->helperText('Contoh: xii, 350 hlm. : ilus. ; 21 cm')
                                ->maxLength(100),
                            Forms\Components\TextInput::make('isbn')
                                ->label('ISBN/ISSN')
                                ->maxLength(20),
                            Forms\Components\Select::make('language')
                                ->label('Bahasa')
                                ->options([
                                    'id' => 'Indonesia',
                                    'en' => 'English',
                                    'ar' => 'Arabic',
                                    'zh' => 'Chinese',
                                    'ja' => 'Japanese',
                                    'de' => 'German',
                                    'fr' => 'French',
                                    'nl' => 'Dutch',
                                    'ms' => 'Malay',
                                ])
                                ->default('id')
                                ->searchable(),
                        ])->columns(3),

                    // Tab 4: Klasifikasi (Classification)
                    Forms\Components\Tabs\Tab::make('Klasifikasi')
                        ->icon('heroicon-o-tag')
                        ->schema([
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\TextInput::make('classification')
                                        ->label('No. Klasifikasi')
                                        ->maxLength(40)
                                        ->hint('Nomor DDC/UDC')
                                        ->hintIcon('heroicon-o-information-circle')
                                        ->suffixAction(
                                            Forms\Components\Actions\Action::make('searchDdc')
                                                ->label('Cari DDC')
                                                ->icon('heroicon-o-book-open')
                                                ->color('primary')
                                                ->modalHeading('')
                                                ->modalWidth('4xl')
                                                ->modalSubmitAction(false)
                                                ->modalCancelAction(false)
                                                ->modalContent(fn () => view('filament.components.ddc-lookup-modal'))
                                        ),
                                    Forms\Components\TextInput::make('call_number')
                                        ->label('No. Panggil')
                                        ->hint('Call number untuk rak')
                                        ->hintIcon('heroicon-o-information-circle')
                                        ->maxLength(50),
                                ]),
                            Forms\Components\TextInput::make('series_title')
                                ->label('Judul Seri')
                                ->maxLength(200),
                            Forms\Components\Select::make('frequency_id')
                                ->label('Frekuensi')
                                ->relationship('frequency', 'name')
                                ->helperText('Untuk terbitan berseri'),
                        ])->columns(2),

                    // Tab 5: Abstrak & Catatan
                    Forms\Components\Tabs\Tab::make('Abstrak & Catatan')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Forms\Components\Textarea::make('abstract')
                                ->label('Abstrak/Ringkasan')
                                ->rows(5)
                                ->columnSpanFull(),
                            Forms\Components\Textarea::make('notes')
                                ->label('Catatan')
                                ->rows(3)
                                ->columnSpanFull(),
                        ]),

                    // Tab 6: Gambar & File
                    Forms\Components\Tabs\Tab::make('Gambar & File')
                        ->icon('heroicon-o-photo')
                        ->schema([
                            Forms\Components\FileUpload::make('image')
                                ->label('Gambar Sampul')
                                ->image()
                                ->directory('covers')
                                ->imageResizeMode('cover')
                                ->imageCropAspectRatio('2:3')
                                ->imageResizeTargetWidth('300')
                                ->imageResizeTargetHeight('450')
                                ->helperText('Upload gambar sampul buku'),
                        ]),

                    // Tab 7: Pengaturan OPAC
                    Forms\Components\Tabs\Tab::make('Pengaturan')
                        ->icon('heroicon-o-cog-6-tooth')
                        ->schema([
                            Forms\Components\Toggle::make('is_opac_visible')
                                ->label('Tampilkan di OPAC')
                                ->default(true)
                                ->helperText('Tampilkan bibliografi ini di OPAC publik'),
                            Forms\Components\Toggle::make('opac_hide')
                                ->label('Sembunyikan dari OPAC')
                                ->helperText('Sembunyikan sementara dari OPAC'),
                            Forms\Components\Toggle::make('promoted')
                                ->label('Promosikan')
                                ->helperText('Tampilkan di halaman utama OPAC'),
                            Forms\Components\TextInput::make('labels')
                                ->label('Label')
                                ->helperText('Label tambahan, pisahkan dengan koma'),
                        ])->columns(4),
                ])
                ->columnSpanFull()
                ->persistTabInQueryString(),

            Forms\Components\Hidden::make('user_id')->default(auth()->id()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl(url('/images/no-cover.png')),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->title)
                    ->description(fn ($record) => $record->sor),
                Tables\Columns\TextColumn::make('items_count')
                    ->label('Eks')
                    ->counts('items')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('subjects.name')
                    ->label('Subjek')
                    ->badge()
                    ->color('warning')
                    ->limitList(2)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('isbn')
                    ->label('ISBN')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('call_number')
                    ->label('No. Panggil')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('publisher.name')
                    ->label('Penerbit')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('publish_year')
                    ->label('Tahun')
                    ->sortable(),
                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Lokasi')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Input')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_opac_visible')
                    ->label('OPAC')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('branch_id')
                    ->label('Lokasi')
                    ->relationship('branch', 'name'),
                Tables\Filters\SelectFilter::make('media_type_id')
                    ->label('GMD')
                    ->relationship('mediaType', 'name'),
                Tables\Filters\SelectFilter::make('language')
                    ->label('Bahasa')
                    ->options([
                        'id' => 'Indonesia',
                        'en' => 'English',
                        'ar' => 'Arabic',
                    ]),
                Tables\Filters\TernaryFilter::make('is_opac_visible')
                    ->label('Tampil OPAC'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('print_label')
                    ->label('Label')
                    ->icon('heroicon-o-printer')
                    ->url(fn ($record) => route('print.labels', ['ids' => $record->items->pluck('id')->toArray()]))
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => $record->items->count() > 0),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count() ?: null;
    }
}
