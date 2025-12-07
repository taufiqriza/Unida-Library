<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ItemResource\Pages;
use App\Models\Item;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Katalog';
    protected static ?string $modelLabel = 'Eksemplar';
    protected static ?string $pluralModelLabel = 'Eksemplar';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'barcode';

    public static function getGloballySearchableAttributes(): array
    {
        return ['barcode', 'inventory_code', 'book.title'];
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'Judul' => $record->book?->title ?? '-',
            'Status' => $record->status ?? '-',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('ItemDetails')
                ->tabs([
                    // Tab 1: Data Eksemplar
                    Forms\Components\Tabs\Tab::make('Data Eksemplar')
                        ->icon('heroicon-o-document-duplicate')
                        ->schema([
                            Forms\Components\Select::make('book_id')
                                ->label('Judul Buku')
                                ->relationship('book', 'title')
                                ->required()
                                ->searchable()
                                ->preload(),
                            Forms\Components\Select::make('branch_id')
                                ->label('Cabang')
                                ->relationship('branch', 'name')
                                ->required()
                                ->searchable()
                                ->default(1),
                            Forms\Components\TextInput::make('barcode')
                                ->label('Kode Eksemplar')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(30),
                            Forms\Components\TextInput::make('call_number')
                                ->label('No. Panggil')
                                ->maxLength(50),
                            Forms\Components\TextInput::make('inventory_code')
                                ->label('No. Inventaris')
                                ->maxLength(50),
                            Forms\Components\TextInput::make('site')
                                ->label('Lokasi Rak')
                                ->maxLength(50),
                        ])->columns(3),

                    // Tab 2: Tipe & Lokasi
                    Forms\Components\Tabs\Tab::make('Tipe & Lokasi')
                        ->icon('heroicon-o-map-pin')
                        ->schema([
                            Forms\Components\Select::make('collection_type_id')
                                ->label('Tipe Koleksi')
                                ->relationship('collectionType', 'name')
                                ->searchable()
                                ->createOptionForm([
                                    Forms\Components\TextInput::make('name')->required(),
                                ]),
                            Forms\Components\Select::make('location_id')
                                ->label('Lokasi')
                                ->relationship('location', 'name')
                                ->searchable()
                                ->createOptionForm([
                                    Forms\Components\TextInput::make('code')->required()->maxLength(20),
                                    Forms\Components\TextInput::make('name')->required(),
                                    Forms\Components\Select::make('branch_id')->relationship('branch', 'name')->required(),
                                ]),
                            Forms\Components\Select::make('item_status_id')
                                ->label('Status')
                                ->relationship('itemStatus', 'name')
                                ->searchable(),
                        ])->columns(3),

                    // Tab 3: Pengadaan
                    Forms\Components\Tabs\Tab::make('Pengadaan')
                        ->icon('heroicon-o-shopping-cart')
                        ->schema([
                            Forms\Components\DatePicker::make('received_date')
                                ->label('Tanggal Terima'),
                            Forms\Components\Select::make('source')
                                ->label('Sumber')
                                ->options([
                                    1 => 'Pembelian',
                                    2 => 'Hibah/Hadiah',
                                    3 => 'Tukar Menukar',
                                ]),
                            Forms\Components\Select::make('supplier_id')
                                ->label('Supplier')
                                ->relationship('supplier', 'name')
                                ->searchable()
                                ->createOptionForm([
                                    Forms\Components\TextInput::make('name')->required(),
                                    Forms\Components\TextInput::make('address'),
                                    Forms\Components\TextInput::make('phone'),
                                ]),
                            Forms\Components\TextInput::make('order_no')
                                ->label('No. Order')
                                ->maxLength(20),
                            Forms\Components\DatePicker::make('order_date')
                                ->label('Tanggal Order'),
                            Forms\Components\TextInput::make('price')
                                ->label('Harga')
                                ->numeric()
                                ->prefix('Rp'),
                        ])->columns(3),
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
                Tables\Columns\TextColumn::make('barcode')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('book.title')
                    ->label('Judul')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->book?->title),
                Tables\Columns\TextColumn::make('call_number')
                    ->label('No. Panggil')
                    ->searchable(),
                Tables\Columns\TextColumn::make('collectionType.name')
                    ->label('Tipe')
                    ->badge(),
                Tables\Columns\TextColumn::make('location.name')
                    ->label('Lokasi'),
                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Cabang')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('itemStatus.name')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'Tersedia' => 'success',
                        'Dipinjam' => 'warning',
                        'Hilang' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('branch_id')
                    ->label('Cabang')
                    ->relationship('branch', 'name'),
                Tables\Filters\SelectFilter::make('collection_type_id')
                    ->label('Tipe Koleksi')
                    ->relationship('collectionType', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('print_barcode')
                    ->label('Cetak')
                    ->icon('heroicon-o-printer')
                    ->url(fn ($record) => route('print.barcode', $record))
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListItems::route('/'),
            'create' => Pages\CreateItem::route('/create'),
            'edit' => Pages\EditItem::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count() ?: null;
    }
}
