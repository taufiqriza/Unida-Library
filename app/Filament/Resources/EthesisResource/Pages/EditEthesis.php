<?php

namespace App\Filament\Resources\EthesisResource\Pages;

use App\Filament\Resources\EthesisResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEthesis extends EditRecord
{
    protected static string $resource = EthesisResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
