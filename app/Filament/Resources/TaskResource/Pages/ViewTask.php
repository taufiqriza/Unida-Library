<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use App\Models\TaskStatus;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewTask extends ViewRecord
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('change_status')
                ->label('Ubah Status')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->form([
                    \Filament\Forms\Components\Select::make('status_id')
                        ->label('Status')
                        ->options(fn () => TaskStatus::query()
                            ->when($this->record->project_id, fn ($q) => $q->where('project_id', $this->record->project_id))
                            ->when(!$this->record->project_id, fn ($q) => $q->whereNull('project_id'))
                            ->orderBy('order')
                            ->pluck('name', 'id'))
                        ->default($this->record->status_id)
                        ->required(),
                ])
                ->action(fn (array $data) => $this->record->update(['status_id' => $data['status_id']])),
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make()
                ->schema([
                    Infolists\Components\Grid::make(4)->schema([
                        Infolists\Components\TextEntry::make('id')
                            ->label('ID')
                            ->formatStateUsing(fn ($state) => "#{$state}"),
                        Infolists\Components\TextEntry::make('type')
                            ->label('Tipe')
                            ->badge()
                            ->formatStateUsing(fn ($state) => match ($state) {
                                'bug' => '🐛 Bug',
                                'feature' => '✨ Feature',
                                'improvement' => '📈 Improvement',
                                default => '📋 Task',
                            }),
                        Infolists\Components\TextEntry::make('status.name')
                            ->label('Status')
                            ->badge()
                            ->color(fn ($record) => $record->status?->color ?? 'gray'),
                        Infolists\Components\TextEntry::make('priority')
                            ->label('Prioritas')
                            ->badge()
                            ->color(fn ($state) => match ($state) {
                                'urgent' => 'danger',
                                'high' => 'warning',
                                'medium' => 'info',
                                'low' => 'success',
                                default => 'gray',
                            }),
                    ]),
                    Infolists\Components\TextEntry::make('title')
                        ->label('Judul')
                        ->weight('bold')
                        ->size('lg')
                        ->columnSpanFull(),
                    Infolists\Components\TextEntry::make('description')
                        ->label('Deskripsi')
                        ->html()
                        ->columnSpanFull(),
                ]),

            Infolists\Components\Section::make('Detail')
                ->schema([
                    Infolists\Components\Grid::make(4)->schema([
                        Infolists\Components\TextEntry::make('project.name')->label('Proyek'),
                        Infolists\Components\TextEntry::make('division.name')->label('Divisi'),
                        Infolists\Components\TextEntry::make('assignee.name')
                            ->label('Assignee')
                            ->default('Unassigned'),
                        Infolists\Components\TextEntry::make('reporter.name')->label('Reporter'),
                    ]),
                    Infolists\Components\Grid::make(4)->schema([
                        Infolists\Components\TextEntry::make('start_date')
                            ->label('Mulai')
                            ->date('d M Y'),
                        Infolists\Components\TextEntry::make('due_date')
                            ->label('Deadline')
                            ->date('d M Y')
                            ->color(fn ($record) => $record->isOverdue() ? 'danger' : null),
                        Infolists\Components\TextEntry::make('estimated_hours')
                            ->label('Estimasi')
                            ->suffix(' jam'),
                        Infolists\Components\TextEntry::make('actual_hours')
                            ->label('Aktual')
                            ->suffix(' jam'),
                    ]),
                    Infolists\Components\TextEntry::make('tags')
                        ->label('Tags')
                        ->badge()
                        ->separator(',')
                        ->columnSpanFull(),
                ])
                ->columns(4),

            Infolists\Components\Section::make('Subtasks')
                ->schema([
                    Infolists\Components\RepeatableEntry::make('subtasks')
                        ->label('')
                        ->schema([
                            Infolists\Components\TextEntry::make('title'),
                            Infolists\Components\TextEntry::make('status.name')->badge(),
                            Infolists\Components\TextEntry::make('assignee.name'),
                        ])
                        ->columns(3),
                ])
                ->visible(fn ($record) => $record->subtasks->count() > 0),
        ]);
    }
}
