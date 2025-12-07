<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockOpnameResource\Pages;
use App\Models\Item;
use App\Models\StockOpname;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class StockOpnameResource extends Resource
{
    protected static ?string $model = StockOpname::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Sirkulasi';
    protected static ?string $modelLabel = 'Stock Opname';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Stock Opname')->schema([
                Forms\Components\TextInput::make('code')
                    ->label('Kode')
                    ->default(fn () => (new StockOpname)->generateCode())
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\TextInput::make('name')
                    ->label('Nama Kegiatan')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('start_date')
                    ->label('Tanggal Mulai')
                    ->required()
                    ->default(now()),
                Forms\Components\DatePicker::make('end_date')
                    ->label('Tanggal Selesai'),
                Forms\Components\Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'in_progress' => 'Sedang Berjalan',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                    ])
                    ->default('draft')
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->rows(3),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->label('Kode')->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('name')->label('Nama')->searchable()->limit(25),
                Tables\Columns\TextColumn::make('start_date')->label('Mulai')->date('d M Y'),
                Tables\Columns\TextColumn::make('status')->label('Status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'draft' => 'gray',
                        'in_progress' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'draft' => 'Draft',
                        'in_progress' => 'Berjalan',
                        'completed' => 'Selesai',
                        'cancelled' => 'Batal',
                    }),
                Tables\Columns\TextColumn::make('total_items')->label('Total')->alignCenter(),
                Tables\Columns\TextColumn::make('found_items')->label('✓')->alignCenter()->color('success'),
                Tables\Columns\TextColumn::make('missing_items')->label('✗')->alignCenter()->color('danger'),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                // Start action
                Tables\Actions\Action::make('start')
                    ->label('Mulai')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->visible(fn (StockOpname $record) => $record->status === 'draft')
                    ->requiresConfirmation()
                    ->modalIcon('heroicon-o-play')
                    ->modalHeading('Mulai Stock Opname')
                    ->modalDescription(fn (StockOpname $record) => 'Memuat semua eksemplar dari cabang ke daftar pengecekan.')
                    ->action(function (StockOpname $record) {
                        // Clear existing items first
                        $record->items()->delete();
                        
                        // Load all items from branch
                        $items = Item::withoutGlobalScopes()->where('branch_id', $record->branch_id)->get();
                        
                        foreach ($items as $item) {
                            $record->items()->create([
                                'item_id' => $item->id,
                                'status' => 'pending',
                            ]);
                        }
                        
                        $record->update([
                            'status' => 'in_progress',
                            'total_items' => $items->count(),
                            'found_items' => 0,
                            'missing_items' => 0,
                            'damaged_items' => 0,
                        ]);
                        
                        Notification::make()
                            ->title('Stock Opname Dimulai')
                            ->body($items->count() . ' eksemplar dimuat ke daftar')
                            ->success()
                            ->send();
                    }),

                // Scan action with modal
                Tables\Actions\Action::make('scan')
                    ->label('Scan')
                    ->icon('heroicon-o-qr-code')
                    ->color('primary')
                    ->visible(fn (StockOpname $record) => $record->status === 'in_progress')
                    ->modalHeading(fn (StockOpname $record) => '📦 ' . $record->name)
                    ->modalWidth('xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->before(function (StockOpname $record) {
                        // Auto-load items if empty
                        if ($record->items()->count() === 0) {
                            $items = Item::withoutGlobalScopes()->where('branch_id', $record->branch_id)->get();
                            foreach ($items as $item) {
                                $record->items()->create(['item_id' => $item->id, 'status' => 'pending']);
                            }
                            $record->update(['total_items' => $items->count()]);
                        }
                    })
                    ->form(fn (StockOpname $record) => [
                        Forms\Components\View::make('filament.components.stock-opname-scanner')
                            ->viewData(['record' => $record->fresh()]),
                    ]),

                // Complete action
                Tables\Actions\Action::make('complete')
                    ->label('Selesai')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (StockOpname $record) => $record->status === 'in_progress')
                    ->requiresConfirmation()
                    ->modalIcon('heroicon-o-exclamation-triangle')
                    ->modalIconColor('warning')
                    ->modalHeading('Selesaikan Stock Opname?')
                    ->modalDescription(fn (StockOpname $record) => 
                        ($record->total_items - $record->found_items - $record->missing_items) . ' item yang belum di-scan akan ditandai sebagai HILANG.')
                    ->action(function (StockOpname $record) {
                        $missing = $record->items()->where('status', 'pending')->count();
                        $record->items()->where('status', 'pending')->update([
                            'status' => 'missing',
                            'checked_by' => auth()->id(),
                            'checked_at' => now(),
                        ]);
                        $record->updateCounts();
                        $record->update(['status' => 'completed', 'end_date' => now()]);
                        Notification::make()
                            ->title('Stock opname selesai!')
                            ->body($missing . ' item ditandai hilang')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\EditAction::make()->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockOpnames::route('/'),
            'create' => Pages\CreateStockOpname::route('/create'),
            'edit' => Pages\EditStockOpname::route('/{record}/edit'),
        ];
    }
}
