<?php

namespace App\Filament\Resources\ThesisSubmissionResource\Pages;

use App\Filament\Resources\ThesisSubmissionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditThesisSubmission extends EditRecord
{
    protected static string $resource = ThesisSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
