<?php

namespace App\Filament\Resources\CarrierTypeResource\Pages;

use App\Filament\Resources\CarrierTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCarrierTypes extends ManageRecords
{
    protected static string $resource = CarrierTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
