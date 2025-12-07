<?php

namespace App\Filament\Resources\FrequencyResource\Pages;

use App\Filament\Resources\FrequencyResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageFrequencies extends ManageRecords
{
    protected static string $resource = FrequencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
