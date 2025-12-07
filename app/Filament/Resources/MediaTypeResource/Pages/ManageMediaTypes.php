<?php

namespace App\Filament\Resources\MediaTypeResource\Pages;

use App\Filament\Resources\MediaTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageMediaTypes extends ManageRecords
{
    protected static string $resource = MediaTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
