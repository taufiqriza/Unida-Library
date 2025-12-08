<?php

namespace App\Filament\Resources\ThesisSubmissionResource\Pages;

use App\Filament\Resources\ThesisSubmissionResource;
use App\Models\ThesisSubmission;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListThesisSubmissions extends ListRecords
{
    protected static string $resource = ThesisSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua')
                ->badge(ThesisSubmission::count()),
            'pending' => Tab::make('Menunggu Review')
                ->badge(ThesisSubmission::whereIn('status', ['submitted', 'under_review'])->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereIn('status', ['submitted', 'under_review'])),
            'revision' => Tab::make('Perlu Revisi')
                ->badge(ThesisSubmission::where('status', 'revision_required')->count())
                ->badgeColor('orange')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'revision_required')),
            'approved' => Tab::make('Disetujui')
                ->badge(ThesisSubmission::where('status', 'approved')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'approved')),
            'published' => Tab::make('Dipublikasikan')
                ->badge(ThesisSubmission::where('status', 'published')->count())
                ->badgeColor('primary')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'published')),
            'rejected' => Tab::make('Ditolak')
                ->badge(ThesisSubmission::where('status', 'rejected')->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'rejected')),
        ];
    }
}
