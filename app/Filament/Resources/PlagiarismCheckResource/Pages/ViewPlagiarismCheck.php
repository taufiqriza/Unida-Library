<?php

namespace App\Filament\Resources\PlagiarismCheckResource\Pages;

use App\Filament\Resources\PlagiarismCheckResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPlagiarismCheck extends ViewRecord
{
    protected static string $resource = PlagiarismCheckResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('download_certificate')
                ->label('Download Sertifikat')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn () => $this->record->hasCertificate() 
                    ? route('opac.member.plagiarism.certificate.download', $this->record) 
                    : null)
                ->openUrlInNewTab()
                ->visible(fn () => $this->record->hasCertificate()),
        ];
    }
}
