<?php

namespace App\Filament\Resources\TaskResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';
    protected static ?string $title = 'Activity Log';
    protected static ?string $icon = 'heroicon-o-clock';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('action')
                    ->label('Aksi')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'created' => 'success',
                        'status_changed' => 'info',
                        'assigned' => 'warning',
                        'commented' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'created' => 'Dibuat',
                        'status_changed' => 'Status Diubah',
                        'assigned' => 'Ditugaskan',
                        'commented' => 'Komentar',
                        default => ucfirst($state),
                    }),
                Tables\Columns\TextColumn::make('description')
                    ->label('Detail')
                    ->getStateUsing(function ($record) {
                        if ($record->field && $record->old_value !== null) {
                            return "{$record->old_value} → {$record->new_value}";
                        }
                        return $record->new_value;
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->since(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50]);
    }

    public function isReadOnly(): bool
    {
        return true;
    }
}
