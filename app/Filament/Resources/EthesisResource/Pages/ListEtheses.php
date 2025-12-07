<?php

namespace App\Filament\Resources\EthesisResource\Pages;

use App\Filament\Resources\EthesisResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEtheses extends ListRecords
{
    protected static string $resource = EthesisResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
