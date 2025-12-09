<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use App\Models\Task;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua')
                ->badge(Task::count()),
            'my_tasks' => Tab::make('Task Saya')
                ->badge(Task::where('assigned_to', auth()->id())->count())
                ->modifyQueryUsing(fn ($query) => $query->where('assigned_to', auth()->id())),
            'overdue' => Tab::make('Overdue')
                ->badge(Task::overdue()->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn ($query) => $query->overdue()),
            'due_soon' => Tab::make('Due Soon')
                ->badge(Task::dueSoon(3)->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn ($query) => $query->dueSoon(3)),
        ];
    }
}
