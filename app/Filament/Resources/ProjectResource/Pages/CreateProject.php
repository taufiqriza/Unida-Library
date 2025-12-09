<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Models\TaskStatus;
use Filament\Resources\Pages\CreateRecord;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    protected function afterCreate(): void
    {
        // Create default statuses for new project
        foreach (TaskStatus::getDefaultStatuses() as $status) {
            $this->record->statuses()->create($status);
        }
    }
}
