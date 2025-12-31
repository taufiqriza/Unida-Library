<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlagiarismCheckResource\Pages;
use App\Models\PlagiarismCheck;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PlagiarismCheckResource extends Resource
{
    protected static ?string $model = PlagiarismCheck::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup = 'E-Library';
    protected static ?string $navigationLabel = 'Cek Plagiasi';
    protected static ?string $modelLabel = 'Cek Plagiasi';
    protected static ?string $pluralModelLabel = 'Cek Plagiasi';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dokumen')
                    ->schema([
                        Forms\Components\TextInput::make('document_title')
                            ->label('Judul Dokumen')
                            ->disabled(),
                        Forms\Components\TextInput::make('original_filename')
                            ->label('Nama File')
                            ->disabled(),
                        Forms\Components\TextInput::make('file_type')
                            ->label('Tipe File')
                            ->disabled(),
                        Forms\Components\TextInput::make('file_size_formatted')
                            ->label('Ukuran')
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('Informasi Member')
                    ->schema([
                        Forms\Components\TextInput::make('member.name')
                            ->label('Nama')
                            ->disabled(),
                        Forms\Components\TextInput::make('member.member_id')
                            ->label('NIM/ID')
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('Hasil')
                    ->schema([
                        Forms\Components\TextInput::make('status')
                            ->label('Status')
                            ->disabled(),
                        Forms\Components\TextInput::make('similarity_score')
                            ->label('Similarity Score (%)')
                            ->disabled(),
                        Forms\Components\TextInput::make('provider')
                            ->label('Provider')
                            ->disabled(),
                        Forms\Components\TextInput::make('certificate_number')
                            ->label('No. Sertifikat')
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('member'))
            ->columns([
                Tables\Columns\TextColumn::make('member_name')
                    ->label('Nama')
                    ->getStateUsing(fn ($record) => $record->member?->name)
                    ->searchable(query: fn (Builder $query, string $search) => $query->whereHas('member', fn ($q) => $q->where('name', 'like', "%{$search}%"))),
                Tables\Columns\TextColumn::make('member_nim')
                    ->label('NIM/ID')
                    ->getStateUsing(fn ($record) => $record->member?->member_id)
                    ->searchable(query: fn (Builder $query, string $search) => $query->whereHas('member', fn ($q) => $q->where('member_id', 'like', "%{$search}%"))),
                Tables\Columns\TextColumn::make('document_title')
                    ->label('Judul')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->document_title),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'secondary' => 'pending',
                        'warning' => 'processing',
                        'success' => 'completed',
                        'danger' => 'failed',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'pending' => 'Menunggu',
                        'processing' => 'Proses',
                        'completed' => 'Selesai',
                        'failed' => 'Gagal',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('similarity_score')
                    ->label('Similarity')
                    ->suffix('%')
                    ->color(fn ($record) => match($record->similarity_level) {
                        'low' => 'success',
                        'moderate' => 'warning',
                        'high', 'critical' => 'danger',
                        default => 'secondary',
                    })
                    ->weight('bold'),
                Tables\Columns\IconColumn::make('certificate_number')
                    ->label('Sertifikat')
                    ->boolean()
                    ->trueIcon('heroicon-o-document-check')
                    ->falseIcon('heroicon-o-document')
                    ->getStateUsing(fn ($record) => !empty($record->certificate_number)),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Menunggu',
                        'processing' => 'Sedang Proses',
                        'completed' => 'Selesai',
                        'failed' => 'Gagal',
                    ]),
                Tables\Filters\Filter::make('has_certificate')
                    ->label('Sudah Ada Sertifikat')
                    ->query(fn (Builder $query) => $query->whereNotNull('certificate_number')),
                Tables\Filters\Filter::make('high_similarity')
                    ->label('Similarity Tinggi (>25%)')
                    ->query(fn (Builder $query) => $query->where('similarity_score', '>', 25)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('download_certificate')
                    ->label('Sertifikat')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => $record->hasCertificate() 
                        ? route('opac.member.plagiarism.certificate.download', $record) 
                        : null)
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => $record->hasCertificate()),
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
            'index' => Pages\ListPlagiarismChecks::route('/'),
            'view' => Pages\ViewPlagiarismCheck::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }
}
