<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JournalSourceResource\Pages;
use App\Models\JournalSource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class JournalSourceResource extends Resource
{
    protected static ?string $model = JournalSource::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Sumber Jurnal';
    protected static ?string $modelLabel = 'Sumber Jurnal';
    protected static ?string $pluralModelLabel = 'Sumber Jurnal';
    protected static ?string $navigationGroup = 'Perpustakaan';
    protected static ?int $navigationSort = 15;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Jurnal')
                    ->description('Data dasar jurnal')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Kode')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->helperText('Kode unik jurnal, contoh: tsaqafah'),
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Jurnal')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('sinta_rank')
                            ->label('Peringkat SINTA')
                            ->options([
                                1 => 'SINTA 1',
                                2 => 'SINTA 2',
                                3 => 'SINTA 3',
                                4 => 'SINTA 4',
                                5 => 'SINTA 5',
                                6 => 'SINTA 6',
                            ])
                            ->placeholder('Pilih SINTA...'),
                        Forms\Components\TextInput::make('issn')
                            ->label('ISSN')
                            ->maxLength(20)
                            ->placeholder('1234-5678'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->helperText('Nonaktifkan untuk menghentikan sinkronisasi'),
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(3),

                Forms\Components\Section::make('URL & Feed')
                    ->description('Konfigurasi sinkronisasi')
                    ->schema([
                        Forms\Components\TextInput::make('base_url')
                            ->label('URL Jurnal (OJS)')
                            ->required()
                            ->url()
                            ->maxLength(500)
                            ->placeholder('https://ejournal.unida.gontor.ac.id/index.php/tsaqafah')
                            ->helperText('URL halaman utama jurnal')
                            ->columnSpanFull(),
                        Forms\Components\Select::make('feed_type')
                            ->label('Tipe Feed')
                            ->options([
                                'atom' => 'Atom Feed',
                                'rss' => 'RSS Feed',
                                'oai' => 'OAI-PMH',
                            ])
                            ->default('atom')
                            ->required(),
                        Forms\Components\TextInput::make('feed_url')
                            ->label('URL Feed/OAI')
                            ->required()
                            ->url()
                            ->maxLength(500)
                            ->placeholder('https://ejournal.unida.gontor.ac.id/index.php/tsaqafah/gateway/plugin/WebFeedGatewayPlugin/atom')
                            ->helperText('URL untuk sinkronisasi artikel')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('cover_url')
                            ->label('URL Cover')
                            ->url()
                            ->maxLength(500)
                            ->placeholder('https://ejournal.unida.gontor.ac.id/home_page/1.jpg')
                            ->helperText('URL gambar cover jurnal')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Statistik')
                    ->description('Data sinkronisasi (read-only)')
                    ->schema([
                        Forms\Components\TextInput::make('article_count')
                            ->label('Jumlah Artikel')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\DateTimePicker::make('last_synced_at')
                            ->label('Terakhir Sync')
                            ->disabled()
                            ->dehydrated(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_url')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn () => 'https://ui-avatars.com/api/?name=J&background=6366f1&color=fff')
                    ->width(40)
                    ->height(40),
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Jurnal')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->name),
                Tables\Columns\TextColumn::make('sinta_rank')
                    ->label('SINTA')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        1, 2 => 'success',
                        3, 4 => 'warning',
                        5, 6 => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => $state ? "S{$state}" : '-'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('article_count')
                    ->label('Artikel')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('last_synced_at')
                    ->label('Terakhir Sync')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('Belum pernah'),
                Tables\Columns\TextColumn::make('feed_type')
                    ->label('Feed')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'atom' => 'success',
                        'rss' => 'warning',
                        'oai' => 'info',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('name')
            ->filters([
                Tables\Filters\SelectFilter::make('sinta_rank')
                    ->label('SINTA')
                    ->options([
                        1 => 'SINTA 1',
                        2 => 'SINTA 2',
                        3 => 'SINTA 3',
                        4 => 'SINTA 4',
                        5 => 'SINTA 5',
                        6 => 'SINTA 6',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->actions([
                Tables\Actions\Action::make('sync')
                    ->label('Sync')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading(fn ($record) => "Sync: {$record->name}")
                    ->modalDescription('Sync artikel dari jurnal ini sekarang?')
                    ->action(function ($record) {
                        try {
                            $service = app(\App\Services\OjsSyncService::class);
                            $result = $service->syncSource($record);
                            
                            if ($result['success'] ?? false) {
                                Notification::make()
                                    ->title('Sync Berhasil')
                                    ->body("Created: {$result['created']}, Updated: {$result['updated']}")
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Sync Gagal')
                                    ->body($result['error'] ?? 'Unknown error')
                                    ->danger()
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Tables\Actions\Action::make('visit')
                    ->label('Kunjungi')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn ($record) => $record->base_url)
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Aktifkan')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_active' => true]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Nonaktifkan')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('syncAll')
                        ->label('Sync Semua')
                        ->icon('heroicon-o-arrow-path')
                        ->color('info')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $service = app(\App\Services\OjsSyncService::class);
                            $total = ['created' => 0, 'updated' => 0];
                            
                            foreach ($records as $record) {
                                if (!$record->is_active) continue;
                                
                                try {
                                    $result = $service->syncSource($record);
                                    if ($result['success'] ?? false) {
                                        $total['created'] += $result['created'];
                                        $total['updated'] += $result['updated'];
                                    }
                                } catch (\Exception $e) {
                                    continue;
                                }
                            }
                            
                            Notification::make()
                                ->title('Bulk Sync Selesai')
                                ->body("Created: {$total['created']}, Updated: {$total['updated']}")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('syncAllJournals')
                    ->label('Sync Semua Jurnal')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Sync Semua Jurnal Aktif')
                    ->modalDescription('Proses ini akan sync semua jurnal yang aktif. Lanjutkan?')
                    ->action(function () {
                        \Artisan::call('journals:sync');
                        Notification::make()
                            ->title('Sync dimulai')
                            ->body('Cek log untuk progress')
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageJournalSources::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
