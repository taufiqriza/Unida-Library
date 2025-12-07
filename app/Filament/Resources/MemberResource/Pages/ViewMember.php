<?php

namespace App\Filament\Resources\MemberResource\Pages;

use App\Filament\Resources\MemberResource;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewMember extends ViewRecord
{
    protected static string $resource = MemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('printCard')
                ->label('Cetak Kartu')
                ->icon('heroicon-o-printer')
                ->url(fn () => route('member.card', $this->record))
                ->openUrlInNewTab(),
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Data Anggota')
                ->schema([
                    Infolists\Components\ImageEntry::make('photo')
                        ->label('Foto')
                        ->circular()
                        ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&size=200'),
                    Infolists\Components\TextEntry::make('member_id')->label('No. Anggota'),
                    Infolists\Components\TextEntry::make('name')->label('Nama'),
                    Infolists\Components\TextEntry::make('gender')
                        ->label('Jenis Kelamin')
                        ->formatStateUsing(fn ($state) => $state === 'M' ? 'Laki-laki' : 'Perempuan'),
                    Infolists\Components\TextEntry::make('birth_date')->label('Tanggal Lahir')->date('d F Y'),
                    Infolists\Components\TextEntry::make('identity_number')->label('No. Identitas'),
                ])->columns(3),
            Infolists\Components\Section::make('Kontak')
                ->schema([
                    Infolists\Components\TextEntry::make('address')->label('Alamat'),
                    Infolists\Components\TextEntry::make('city')->label('Kota'),
                    Infolists\Components\TextEntry::make('phone')->label('Telepon'),
                    Infolists\Components\TextEntry::make('email')->label('Email'),
                ])->columns(2),
            Infolists\Components\Section::make('Keanggotaan')
                ->schema([
                    Infolists\Components\TextEntry::make('memberType.name')->label('Tipe')->badge(),
                    Infolists\Components\TextEntry::make('register_date')->label('Terdaftar')->date('d F Y'),
                    Infolists\Components\TextEntry::make('expire_date')
                        ->label('Kadaluarsa')
                        ->date('d F Y')
                        ->color(fn ($record) => $record->expire_date < now() ? 'danger' : 'success'),
                    Infolists\Components\IconEntry::make('is_active')->label('Status')->boolean(),
                ])->columns(4),
            Infolists\Components\Section::make('Statistik Peminjaman')
                ->schema([
                    Infolists\Components\TextEntry::make('active_loans')
                        ->label('Sedang Dipinjam')
                        ->state(fn ($record) => $record->loans()->where('is_returned', false)->count()),
                    Infolists\Components\TextEntry::make('total_loans')
                        ->label('Total Peminjaman')
                        ->state(fn ($record) => $record->loans()->count()),
                    Infolists\Components\TextEntry::make('total_fines')
                        ->label('Total Denda')
                        ->state(fn ($record) => 'Rp ' . number_format($record->fines()->sum('amount'), 0, ',', '.')),
                ])->columns(3),
        ]);
    }
}
