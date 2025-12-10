<?php

namespace App\Filament\Resources\PlagiarismCheckResource\Pages;

use App\Filament\Resources\PlagiarismCheckResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlagiarismChecks extends ListRecords
{
    protected static string $resource = PlagiarismCheckResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
