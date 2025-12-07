<?php

namespace App\Filament\Resources\CollectionTypeResource\Pages;

use App\Filament\Resources\CollectionTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCollectionTypes extends ManageRecords
{
    protected static string $resource = CollectionTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
