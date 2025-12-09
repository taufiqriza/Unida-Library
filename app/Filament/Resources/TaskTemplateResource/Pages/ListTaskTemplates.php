<?php

namespace App\Filament\Resources\TaskTemplateResource\Pages;

use App\Filament\Resources\TaskTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTaskTemplates extends ListRecords
{
    protected static string $resource = TaskTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
