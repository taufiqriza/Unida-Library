<?php

namespace App\Filament\Resources\EbookResource\Pages;

use App\Filament\Resources\EbookResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEbook extends EditRecord
{
    protected static string $resource = EbookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
