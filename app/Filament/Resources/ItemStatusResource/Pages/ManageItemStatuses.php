<?php

namespace App\Filament\Resources\ItemStatusResource\Pages;

use App\Filament\Resources\ItemStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageItemStatuses extends ManageRecords
{
    protected static string $resource = ItemStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
