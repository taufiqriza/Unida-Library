<?php

namespace App\Filament\Pages;

use App\Models\Member;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ExpiredMembers extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'Keanggotaan';
    protected static ?string $navigationLabel = 'Kadaluarsa';
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.pages.expired-members';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Member::query()
                    ->where('expire_date', '<', now())
                    ->when(!auth('admin')->user()->isSuperAdmin(), fn ($q) => $q->where('branch_id', auth('admin')->user()->branch_id))
                    ->when(auth('admin')->user()->isSuperAdmin() && session('current_branch_id'), fn ($q) => $q->where('branch_id', session('current_branch_id')))
            )
            ->columns([
                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Cabang')
                    ->badge()
                    ->visible(fn () => auth('admin')->user()?->isSuperAdmin() && !session('current_branch_id')),
                Tables\Columns\TextColumn::make('member_id')
                    ->label('No. Anggota')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('memberType.name')
                    ->label('Tipe')
                    ->badge(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telepon'),
                Tables\Columns\TextColumn::make('expire_date')
                    ->label('Kadaluarsa')
                    ->date('d M Y')
                    ->color('danger'),
                Tables\Columns\TextColumn::make('days_expired')
                    ->label('Hari')
                    ->state(fn ($record) => now()->diffInDays($record->expire_date) . ' hari')
                    ->color('danger'),
            ])
            ->defaultSort('expire_date', 'asc')
            ->actions([
                Tables\Actions\Action::make('extend')
                    ->label('Perpanjang')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $period = $record->memberType->membership_period ?? 365;
                        $record->update([
                            'register_date' => now(),
                            'expire_date' => now()->addDays($period),
                        ]);
                    }),
                Tables\Actions\Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil')
                    ->url(fn ($record) => route('filament.admin.resources.members.edit', $record)),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('extendAll')
                    ->label('Perpanjang Semua')
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
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        $user = auth('admin')->user();
        if (!$user) return null;
        
        $query = Member::where('expire_date', '<', now());
        
        if (!$user->isSuperAdmin()) {
            $query->where('branch_id', $user->branch_id);
        } elseif (session('current_branch_id')) {
            $query->where('branch_id', session('current_branch_id'));
        }
        
        $count = $query->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }
}
