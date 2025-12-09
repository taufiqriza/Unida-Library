<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberResource\Pages;
use App\Models\Member;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?string $navigationGroup = 'Keanggotaan';
    protected static ?string $navigationLabel = 'Anggota';
    protected static ?string $modelLabel = 'Anggota';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'member_id', 'email', 'phone'];
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'ID' => $record->member_id ?? '-',
            'Email' => $record->email ?? '-',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Member')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('Data Pribadi')
                        ->icon('heroicon-o-user')
                        ->schema([
                            Forms\Components\TextInput::make('member_id')
                                ->label('No. Anggota')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(30)
                                ->default(fn () => 'M' . date('Y') . str_pad(Member::count() + 1, 4, '0', STR_PAD_LEFT)),
                            Forms\Components\TextInput::make('name')
                                ->label('Nama Lengkap')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\Select::make('gender')
                                ->label('Jenis Kelamin')
                                ->options([
                                    'M' => 'Laki-laki',
                                    'F' => 'Perempuan',
                                ]),
                            Forms\Components\DatePicker::make('birth_date')
                                ->label('Tanggal Lahir'),
                            Forms\Components\TextInput::make('identity_number')
                                ->label('No. Identitas (KTP/NIM)')
                                ->maxLength(30),
                            Forms\Components\FileUpload::make('photo')
                                ->label('Foto')
                                ->image()
                                ->directory('members')
                                ->imageResizeMode('cover')
                                ->imageCropAspectRatio('3:4')
                                ->imageResizeTargetWidth('300')
                                ->imageResizeTargetHeight('400'),
                        ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Kontak & Alamat')
                        ->icon('heroicon-o-map-pin')
                        ->schema([
                            Forms\Components\Textarea::make('address')
                                ->label('Alamat')
                                ->rows(2)
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('city')
                                ->label('Kota'),
                            Forms\Components\TextInput::make('phone')
                                ->label('No. Telepon')
                                ->tel()
                                ->maxLength(30),
                            Forms\Components\TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->maxLength(255),
                        ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Keanggotaan')
                        ->icon('heroicon-o-identification')
                        ->schema([
                            Forms\Components\Select::make('member_type_id')
                                ->label('Tipe Anggota')
                                ->relationship('memberType', 'name')
                                ->required()
                                ->preload()
                                ->live()
                                ->afterStateUpdated(function ($state, Forms\Set $set) {
                                    if ($state) {
                                        $memberType = \App\Models\MemberType::find($state);
                                        if ($memberType) {
                                            $set('expire_date', now()->addDays($memberType->membership_period)->format('Y-m-d'));
                                        }
                                    }
                                }),
                            Forms\Components\DatePicker::make('register_date')
                                ->label('Tanggal Daftar')
                                ->required()
                                ->default(now()),
                            Forms\Components\DatePicker::make('expire_date')
                                ->label('Tanggal Kadaluarsa')
                                ->required()
                                ->default(now()->addYear()),
                            Forms\Components\Toggle::make('is_active')
                                ->label('Aktif')
                                ->default(true),
                            Forms\Components\Textarea::make('notes')
                                ->label('Catatan')
                                ->rows(2)
                                ->columnSpanFull(),
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
                Tables\Columns\ImageColumn::make('photo')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&background=random'),
                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Cabang')
                    ->badge()
                    ->color('gray')
                    ->visible(fn () => auth('web')->user()?->isSuperAdmin() && !session('current_branch_id')),
                Tables\Columns\TextColumn::make('member_id')
                    ->label('No. Anggota')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->socialAccounts()->where('provider', 'google')->exists() ? '🌐 OAuth Google' : null),
                Tables\Columns\TextColumn::make('memberType.name')
                    ->label('Tipe')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('register_date')
                    ->label('Terdaftar')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('expire_date')
                    ->label('Kadaluarsa')
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn ($record) => $record->expire_date < now() ? 'danger' : 'success'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('loans_count')
                    ->label('Pinjaman')
                    ->counts(['loans' => fn ($query) => $query->where('is_returned', false)])
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'gray'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('branch_id')
                    ->label('Cabang')
                    ->relationship('branch', 'name')
                    ->visible(fn () => auth('web')->user()?->isSuperAdmin() && !session('current_branch_id')),
                Tables\Filters\SelectFilter::make('member_type_id')
                    ->label('Tipe Anggota')
                    ->relationship('memberType', 'name'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
                Tables\Filters\Filter::make('expired')
                    ->label('Kadaluarsa')
                    ->query(fn ($query) => $query->where('expire_date', '<', now())),
            ])
            ->actions([
                Tables\Actions\Action::make('extend')
                    ->label('Perpanjang')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Perpanjang Keanggotaan')
                    ->modalDescription(fn ($record) => "Perpanjang keanggotaan {$record->name}?")
                    ->action(function ($record) {
                        $period = $record->memberType->membership_period ?? 365;
                        $record->update([
                            'register_date' => now(),
                            'expire_date' => now()->addDays($period),
                        ]);
                    })
                    ->visible(fn ($record) => $record->expire_date < now()),
                Tables\Actions\Action::make('printCard')
                    ->label('Cetak Kartu')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn ($record) => route('member.card', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('printCards')
                        ->label('Cetak Kartu')
                        ->icon('heroicon-o-printer')
                        ->action(function ($records) {
                            $ids = $records->pluck('id')->join(',');
                            return redirect()->route('member.cards', ['ids' => $ids]);
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('extend')
                        ->label('Perpanjang')
                        ->icon('heroicon-o-arrow-path')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $period = $record->memberType->membership_period ?? 365;
                                $record->update([
                                    'register_date' => now(),
                                    'expire_date' => now()->addDays($period),
                                ]);
                            }
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'view' => Pages\ViewMember::route('/{record}'),
            'edit' => Pages\EditMember::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count() ?: null;
    }
}
